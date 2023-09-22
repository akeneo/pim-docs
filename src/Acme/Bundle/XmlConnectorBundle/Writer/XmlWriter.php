<?php

namespace Acme\Bundle\XmlConnectorBundle\Writer;

use Akeneo\Tool\Component\Batch\Item\FlushableInterface;
use Akeneo\Tool\Component\Batch\Item\InitializableInterface;
use Akeneo\Tool\Component\Batch\Item\ItemWriterInterface;
use Akeneo\Tool\Component\Batch\Step\StepExecutionAwareInterface;
use Akeneo\Tool\Component\Connector\Writer\File\AbstractFileWriter;
use Akeneo\Tool\Component\Connector\Writer\File\ArchivableWriterInterface;

class XmlWriter extends AbstractFileWriter implements
    ItemWriterInterface,
    InitializableInterface,
    FlushableInterface,
    ArchivableWriterInterface,
    StepExecutionAwareInterface
{
    /** @var array */
    protected $writtenFiles = [];

    /** @var \XMLWriter **/
    protected $xml;

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        if (null === $this->xml) {
            $jobParameters = $this->stepExecution->getJobParameters();
            $filePath = $jobParameters->get('storage')['file_path'];

            $this->xml = new \XMLWriter();
            $this->xml->openURI($filePath);
            $this->xml->startDocument('1.0', 'UTF-8');
            $this->xml->setIndent(4);
            $this->xml->startElement('products');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getWrittenFiles()
    {
        return $this->writtenFiles;
    }

    /**
     * {@inheritdoc}
     */
    public function write(array $items)
    {
        $exportDirectory = dirname($this->getPath());
        if (!is_dir($exportDirectory)) {
            $this->localFs->mkdir($exportDirectory);
        }

        foreach ($items as $item) {
            $this->xml->startElement('product');
            foreach ($item as $property => $value) {
                $this->xml->writeAttribute($property, $value);
            }
            $this->xml->endElement();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->xml->endElement();
        $this->xml->endDocument();
        $this->xml->flush();
        $jobParameters = $this->stepExecution->getJobParameters();

        $this->writtenFiles = [$this->stepExecution->getJobParameters()->get('storage')['file_path']];
    }
}
