How To Log Memory Usage In Calculation Steps
============================================

We created the
``Akeneo\Pim\WorkOrganization\TeamWorkAssistant\Component\Job\ProjectCalculation\CalculationStep\LoggableStep`` for debug purposes.
It allows to log the memory usage for each loop of the
``Akeneo\Pim\WorkOrganization\TeamWorkAssistant\Component\Job\ProjectCalculation\ProjectCalculationTasklet``.
It helped us to hunt memory leaks.
Don't hesitate to use it to check your custom :ref:`calculation step <calculation-steps>`.

.. code-block:: php

    <?php // src/PimEnterprise/Component/TeamworkAssistant/Job/ProjectCalculation/CalculationStep/LoggableStep.php

    namespace Akeneo\Pim\WorkOrganization\TeamWorkAssistant\Component\Job\ProjectCalculation\CalculationStep;

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

You need to declare the ``LoggableStep`` as a service with the tag ``teamwork_assistant.calculation_step``:

.. code-block:: yaml

    teamwork_assistant.calculation_step.loggable_step:
        class: 'Akeneo\Pim\WorkOrganization\TeamWorkAssistant\Component\Job\ProjectCalculation\CalculationStep\LoggableStep'
        arguments:
            - '/your/custom/path/memory_leak_hunter.csv'
        public: false
        tags:
            - { name: teamwork_assistant.calculation_step }

Now your custom step will be executed and you can find memory usage trace in ``/your/custom/path/memory_leak_hunter.csv``.
