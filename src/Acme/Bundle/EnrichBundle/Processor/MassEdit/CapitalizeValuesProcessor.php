<?php

namespace Acme\Bundle\EnrichBundle\Processor\MassEdit;

use Pim\Bundle\CatalogBundle\Exception\InvalidArgumentException;
use Pim\Bundle\CatalogBundle\Model\ProductInterface;
use Pim\Bundle\CatalogBundle\Updater\ProductUpdaterInterface;
use Pim\Bundle\EnrichBundle\Entity\Repository\MassEditRepositoryInterface;
use Pim\Bundle\EnrichBundle\Processor\MassEdit\AbstractMassEditProcessor;
use Symfony\Component\Validator\ValidatorInterface;

class CapitalizeValuesProcessor extends AbstractMassEditProcessor
{
    /** @var ProductUpdaterInterface */
    protected $productUpdater;

    /** @var ValidatorInterface */
    protected $validator;

    /**
     * @param ProductUpdaterInterface      $productUpdater
     * @param ValidatorInterface           $validator
     * @param MassEditRepositoryInterface  $massEditRepository
     */
    public function __construct(
        ProductUpdaterInterface $productUpdater,
        ValidatorInterface $validator,
        MassEditRepositoryInterface $massEditRepository
    ) {
        parent::__construct($massEditRepository);
        $this->productUpdater = $productUpdater;
        $this->validator      = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function process($product)
    {
        // This is where you put your custom logic. Here we work on a
        // $product the Reader gave us.

        // This is the configuration we receive from our Operation
        $configuration = $this->getJobConfiguration();

        if (!array_key_exists('actions', $configuration)) {
            throw new InvalidArgumentException('Missing configuration for \'actions\'.');
        }

        $actions = $configuration['actions'];

        // Retrieve custom config from the action
        $field   = $actions[0]['field'];
        $options = $actions[0]['options'];

        // Capitalize the attribute value of the product
        $originalValue = $product->getValue($field, $options['locale'], $options['scope'])->getData();
        $capitalizedValue = strtoupper($originalValue);

        // Use the updater to update the product
        $this->setData(
            $product,
            [
                [
                    'field'   => $field,
                    'options' => $options,
                    'value'   => $capitalizedValue
                ]
            ]
        );

        // Validate the product
        if (null === $product || (null !== $product && !$this->isProductValid($product))) {
            $this->stepExecution->incrementSummaryInfo('skipped_products');

            return null; // By returning null, the product won't be saved by the Writer
        }

        // Used on the Reporting Screen to have a summary on the Mass Edit execution
        $this->stepExecution->incrementSummaryInfo('mass_edited');

        return $product; // Send the product to the Writer to be saved
    }

    /**
     * Validate the product
     *
     * @param ProductInterface $product
     *
     * @return bool
     */
    protected function isProductValid(ProductInterface $product)
    {
        $violations = $this->validator->validate($product);
        $this->addWarningMessage($violations, $product);

        return 0 === $violations->count();
    }

    /**
     * Set data from $actions to the given $product
     *
     * @param ProductInterface $product
     * @param array            $actions
     *
     * @return CapitalizeValuesProcessor
     */
    protected function setData(ProductInterface $product, array $actions)
    {
        foreach ($actions as $action) {
            $this->productUpdater->setData($product, $action['field'], $action['value'], $action['options']);
        }

        return $this;
    }
}
