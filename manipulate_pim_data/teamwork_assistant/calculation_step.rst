Add A Calculation Step
======================

To add a new step you just need to create a new Step Class that implements
`Akeneo\\Pim\\WorkOrganization\\TeamworkAssistant\\Component\\Job\\ProjectCalculation\\CalculationStep\\CalculationStepInterface` interface.

.. code-block:: php

    <?php

    namespace AcmeBundle\ProjectCalculation\CalculationStep;

    use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
    use Akeneo\Pim\WorkOrganization\TeamworkAssistant\Component\Model\ProjectInterface;

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

For example, we created the
`Akeneo\\Pim\\WorkOrganization\\TeamworkAssistant\\Component\\Job\\ProjectCalculation\\CalculationStep\\LoggableStep` for debug purposes.
It allows logging the memory usage for each loop of the
`Akeneo\\Pim\\WorkOrganization\\TeamworkAssistant\\Component\\Job\\ProjectCalculation\\ProjectCalculationTasklet`. It helped us to hunt
memory leaks. Don't hesitate to use it to check your custom calculation step.

.. code-block:: php

    <?php // src/Akeneo/Pim/WorkOrganization/TeamworkAssistant/Component/Job/ProjectCalculation/CalculationStep/LoggableStep.php

    namespace Akeneo\Pim\WorkOrganization\TeamworkAssistant\Component\Job\ProjectCalculation\CalculationStep;

    use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
    use Akeneo\Pim\WorkOrganization\TeamworkAssistant\Component\Model\ProjectInterface;

    /**
     * Log the memory usage. Use it to debug.
     *
     * @author Arnaud Langlade <arnaud.langlade@akeneo.com>
     */
    class LoggableStep implements CalculationStepInterface
    {
        /** @var string */
        protected $fileLog;

        /**
         * @param string $fileLog
         */
        public function __construct($fileLog)
        {
            $this->fileLog = $fileLog;
        }

        /**
         * {@inheritdoc}
         */
        public function execute(ProductInterface $product, ProjectInterface $project)
        {
            $newRow = [$project->getCode(), $product->getId(), memory_get_usage()/1024/1024];
            $handle = fopen($this->fileLog, 'a+');
            fputcsv($handle, $newRow);
            fclose($handle);
        }
    }

Then you need to declare as a service with the tag `activity_manager.calculation_step`:

.. code-block:: yaml

    activity_manager.calculation_step.custom_step:
        class: 'AcmeBundle\ProjectCalculation\CalculationStep\MyCustomStep'
        public: false
        tags:
            - { name: activity_manager.calculation_step }

Or for the `LoggableStep`:

.. code-block:: yaml

    activity_manager.calculation_step.loggable_step:
        class: 'Akeneo\Pim\WorkOrganization\TeamworkAssistant\Component\Job\ProjectCalculation\CalculationStep\LoggableStep'
        arguments:
            - '/your/custom/path/memory_leak_hunter.csv'
        public: false
        tags:
            - { name: activity_manager.calculation_step }

Now your custom step will be executed and you can find memory usage trace in `/your/custom/path/memory_leak_hunter.csv`.
