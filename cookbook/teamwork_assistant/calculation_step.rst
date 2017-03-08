Add A Calculation Step
======================

Before saving a project a job is launched in background. This job is composed by calculation steps. Each calculation step
is used to execute an action between the project and a product. For example, we extract data from the product to add
information in project. For custom development you may need to add your own calculation step.

.. _project creation: ../../reference/teamwork_assistant/project_creation.html

.. note::

    To get more information about project creation and calculation steps go to this `project creation`_.

To add a new step you just need to create a new Step Class that implements
``PimEnterprise\Component\ActivityManager\Job\ProjectCalculation\CalculationStep\CalculationStepInterface`` interface.

.. code-block:: php

    <?php

    namespace AcmeBundle\ProjectCalculation\CalculationStep;

    use Pim\Component\Catalog\Model\ProductInterface;
    use Akeneo\TeamworkAssistant\Component\Model\ProjectInterface;

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

Then you need to declare as a service with the tag ``teamwork_assistant.calculation_step``:

.. code-block:: yaml

    teamwork_assistant.calculation_step.custom_step:
        class: 'AcmeBundle\ProjectCalculation\CalculationStep\MyCustomStep'
        public: false
        tags:
            - { name: teamwork_assistant.calculation_step }

Your custom calculation step will be run before the project saving.
