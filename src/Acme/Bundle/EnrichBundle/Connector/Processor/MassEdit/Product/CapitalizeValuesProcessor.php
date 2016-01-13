<?php

namespace Acme\Bundle\EnrichBundle\Connector\Processor\MassEdit\Product;

use Akeneo\Component\StorageUtils\Updater\PropertySetterInterface;
use Pim\Bundle\CatalogBundle\Exception\InvalidArgumentException;
use Pim\Bundle\CatalogBundle\Model\ProductInterface;
use Pim\Bundle\EnrichBundle\Connector\Processor\AbstractProcessor;
use Pim\Component\Connector\Repository\JobConfigurationRepositoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CapitalizeValuesProcessor extends AbstractProcessor
{
    /** @var PropertySetterInterface */
    protected $propertySetter;

    /** @var ValidatorInterface */
    protected $validator;

    /**
     * @param JobConfigurationRepositoryInterface $jobConfigRepository
     * @param PropertySetterInterface             $propertySetter
     * @param ValidatorInterface                  $validator
     */
    public function __construct(
        JobConfigurationRepositoryInterface $jobConfigRepository,
        PropertySetterInterface $propertySetter,
        ValidatorInterface $validator
    ) {
        parent::__construct($jobConfigRepository);

        $this->propertySetter = $propertySetter;
        $this->validator      = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function process($product)
    {
        /** @var ProductInterface $product */

        // This is where you put your custom logic. Here we work on a
        // $product the Reader gave us.

        // This is the configuration we receive from our Operation
        $configuration = $this->getJobConfiguration();

        if (!array_key_exists('actions', $configuration)) {
            throw new InvalidArgumentException('Missing configuration for \'actions\'.');
        }

        $actions = $configuration['actions'];

        // Retrieve custom config from the action
        $field = $actions['field'];
        $options = $actions['options'];

        // Capitalize the attribute value of the product
        $originalValue    = $product->getValue($field)->getData();
        $capitalizedValue = strtoupper($originalValue);

        // Use the property setter to update the product
        $newData = ['field' => $field, 'value' => $capitalizedValue, 'options' => $options];
        $this->setData($product, [$newData]);

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
     * Validate the product and raise a warning if not
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
            $this->propertySetter->setData($product, $action['field'], $action['value'], $action['options']);
        }

        return $this;
    }
}
