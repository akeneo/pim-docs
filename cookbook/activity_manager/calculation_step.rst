Add a calculation step
----------------------

To add a new step you just need to create a new Step Class that implements a CalculationStepInterface

.. code-block:: php
    <?php

    namespace AcmeBundle\ProjectCalculation\CalculationStep;

    use Pim\Component\Catalog\Model\ProductInterface;
    use Akeneo\ActivityManager\Component\Model\ProjectInterface;

    class MyCustomStep implements CalculationStepInterface
    {
        /**
         * {@inheritdoc}
         */
        public function execute(ProductInterface $product, ProjectInterface $project)
        {
            // Implement the custom code here.
        }
    }

Then you need to declare as a service with the tag `activity_manager.calculation_step`

.. code-block:: yaml

    activity_manager.calculation_step.user_group:
        class: 'AcmeBundle\ProjectCalculation\CalculationStep\MyCustomStep'
        public: false
        tags:
            - { name: activity_manager.calculation_step }

.. note::

    To get more information about how to add custom steps follow this link.
