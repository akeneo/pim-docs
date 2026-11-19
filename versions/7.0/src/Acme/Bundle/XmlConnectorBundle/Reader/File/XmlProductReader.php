<?php

namespace Acme\Bundle\XmlConnectorBundle\Reader\File;

use Akeneo\Tool\Component\Batch\Item\FileInvalidItem;
use Akeneo\Tool\Component\Batch\Item\FlushableInterface;
use Akeneo\Tool\Component\Batch\Item\InvalidItemException;
use Akeneo\Tool\Component\Batch\Item\ItemReaderInterface;
use Akeneo\Tool\Component\Batch\Model\StepExecution;
use Akeneo\Tool\Component\Batch\Step\StepExecutionAwareInterface;
use Akeneo\Tool\Component\Connector\ArrayConverter\ArrayConverterInterface;
use Akeneo\Tool\Component\Connector\Exception\DataArrayConversionException;
use Akeneo\Tool\Component\Connector\Exception\InvalidItemFromViolationsException;

class XmlProductReader implements
    ItemReaderInterface,
    StepExecutionAwareInterface,
    FlushableInterface
{
    /** @var array */
    protected $xml;

    /** @var StepExecution */
    protected $stepExecution;

    /** @var ArrayConverterInterface */
    protected $converter;

    /**
     * @param ArrayConverterInterface $converter
     */
    public function __construct(ArrayConverterInterface $converter)
    {
        $this->converter = $converter;
    }

    public function read()
    {
        if (null === $this->xml) {
            $jobParameters = $this->stepExecution->getJobParameters();
            $filePath = $jobParameters->get('storage')['file_path'];

            // for example purpose, we should use XML Parser to read line per line
            $this->xml = simplexml_load_file($filePath, 'SimpleXMLIterator');
            $this->xml->rewind();
        }

        if ($data = $this->xml->current()) {
            $item = [];
            foreach ($data->attributes() as $attributeName => $attributeValue) {
                $item[$attributeName] = (string) $attributeValue;
            }
            $this->xml->next();

            if (null !== $this->stepExecution) {
                $this->stepExecution->incrementSummaryInfo('item_position');
            }

            try {
                $item = $this->converter->convert($item);
            } catch (DataArrayConversionException $e) {
                $this->skipItemFromConversionException($this->xml->current(), $e);
            }

            return $item;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->xml = null;
    }

    /**
     * {@inheritdoc}
     */
    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }

    /**
     * @param array                        $item
     * @param DataArrayConversionException $exception
     *
     * @throws InvalidItemException
     * @throws InvalidItemFromViolationsException
     */
    protected function skipItemFromConversionException(array $item, DataArrayConversionException $exception)
    {
        if (null !== $this->stepExecution) {
            $this->stepExecution->incrementSummaryInfo('skip');
        }

        if (null !== $exception->getViolations()) {
            throw new InvalidItemFromViolationsException(
                $exception->getViolations(),
                new FileInvalidItem($item, $this->stepExecution->getSummaryInfo('item_position')),
                [],
                0,
                $exception
            );
        }

        $invalidItem = new FileInvalidItem(
            $item,
            $this->stepExecution->getSummaryInfo('item_position')
        );

        throw new InvalidItemException($exception->getMessage(), $invalidItem, [], 0, $exception);
    }
}
