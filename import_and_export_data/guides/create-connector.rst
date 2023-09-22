How to create a new Connector
=============================

Sometimes native connectors won't cover all your needs, you will need to write your own.

A connector is an ensemble of jobs able to import and export data using a specific format. Each job is composed of several steps.

.. note::

    For more details about these concepts, see import/export :doc:`/import_and_export_data/index`.

Let's say we want to create a connector that can export CSV data (like the native one), but at the end of each export we want to notify another application.
We also want the notification to contain the path to the directory of the exported file.

.. note::

    Here we use a very simple case to have overview of the connectors, for more complex cases (like adding support for new file formats) you can refer to the next chapters.

Configure a job
---------------

Jobs and steps are actually Symfony services. The first thing we need is to declare a new service for our product export job:

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/Resources/config/jobs.yml
    :language: yaml
    :linenos:
    :lines: 1-9,11-

.. warning::

    Make sure that the file containing your declaration is correctly loaded by your bundle extension. For more info please see the `Symfony documentation`_.

.. _Symfony documentation: https://symfony.com/doc/5.4/bundles/extension.html#using-the-load-method

As you can see there is almost no difference with the native CSV export job.
The only new info here is the name (first parameter) and the connector name (the ``connector`` property of the tag).

How can we add our notification behaviour to this job? The simplest way is to write a new step that will be executed after the export step.

.. _connector-add-a-new-step:

Add a new step
--------------

A step class needs two things: extend ``Akeneo\Tool\Component\Batch\Step\AbstractStep`` and implement a ``doExecute()`` method. This method will contain your custom behavior:

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/Step/NotifyStep.php
   :language: php
   :linenos:

We can now declare the step as a service:

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/Resources/config/steps.yml
   :language: yaml
   :linenos:

And add it to the job we previously declared:

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/Resources/config/jobs.yml
    :language: yaml
    :linenos:

.. tip::

    Thanks to Symfony's dependency injection, it's quite easy to reuse a step for several jobs.
    For example, our notification step can be added to any export job just by putting it in the job service declaration.

Configure a job instance
------------------------

A job can be seen as a template, it cannot be executed on its own: it needs parameters.
For example our new job needs ``file_path`` and ``urlToNotify`` parameters to work properly (plus the ones needed by the native export step).

Each set of parameters for a given job is called a **job instance**.
A job instance can be executed, modified or deleted using the UI or the ``akeneo:batch:*`` Symfony commands.

A job also needs a way to get default values for parameters and a way to validate this parameters.

Let's write it! For convenience reasons we can use the same class for both roles, it must then implement both ``Akeneo\Tool\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface`` and ``Akeneo\Tool\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface``.

We want also to keep the default values and validation constraints needed by the native export step. The easiest way to do that is to use the decoration pattern:

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/JobParameters/ProductCsvExportNotify.php
    :language: php
    :linenos:

.. tip::

    If the job doesn't need any particular parameters, it's possible to use directly the classes ``Akeneo\Tool\Component\Batch\Job\JobParameters\EmptyDefaultValuesProvider`` and ``Akeneo\Tool\Component\Batch\Job\JobParameters\EmptyConstraintCollectionProvider``.

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/Resources/config/job_parameters.yml
    :language: yaml
    :linenos:

Your job instances parameters can now be populated by default and validated.

Create a job instance via the command
-------------------------------------

We can create an instance with the following command:

.. code-block:: bash

    php bin/console cache:clear
    # akeneo:batch:create-job <connector> <job> <type> <code> <config> [<label>]
    php bin/console akeneo:batch:create-job 'Acme CSV Notify Connector' csv_product_export_notify export my_app_product_export '{"urlToNotify": "http://my-app.com/product-export-done"}'


You can also list the existing job instances with the following command:

.. code-block:: bash

    php bin/console akeneo:batch:list-jobs

Execute our new job instance
----------------------------

You can run the job with the following command:

.. code-block:: bash

    php bin/console akeneo:batch:job my_app_product_export

    [2017-04-18 18:43:55] batch.DEBUG: Job execution starting: startTime=, endTime=, updatedTime=, status=2, exitStatus=[UNKNOWN] , exitDescription=[], job=[my_app_product_export] [] []
    [2017-04-18 18:43:55] batch.INFO: Step execution starting: id=0, name=[export], status=[2], exitCode=[EXECUTING], exitDescription=[] [] []
    [2017-04-18 18:43:55] batch.DEBUG: Step execution success: id= 42 [] []
    [2017-04-18 18:43:55] batch.DEBUG: Step execution complete: id=42, name=[export], status=[1], exitCode=[EXECUTING], exitDescription=[] [] []
    [2017-04-18 18:43:55] batch.INFO: Step execution starting: id=0, name=[notify], status=[2], exitCode=[EXECUTING], exitDescription=[] [] []
    [2017-04-18 18:43:55] batch.DEBUG: Step execution success: id= 43 [] []
    [2017-04-18 18:43:55] batch.DEBUG: Step execution complete: id=43, name=[notify], status=[1], exitCode=[EXECUTING], exitDescription=[] [] []
    [2017-04-18 18:43:55] batch.DEBUG: Upgrading JobExecution status: startTime=2017-04-18T16:43:55+00:00, endTime=, updatedTime=, status=3, exitStatus=[UNKNOWN] , exitDescription=[], job=[my_app_product_export] [] []
    Export my_app_product_export has been successfully executed.

The ``--config`` option can be used to override the job instance parameters at runtime, for instance, to change the file path:

.. code-block:: bash

    php bin/console akeneo:batch:publish-job-to-queue csv_product_import -c '{"storage":{"type":"local","file_path":"/test.csv"}}'

.. warning::

    In production, use this command instead:

    .. code-block:: bash

        php bin/console akeneo:batch:publish-job-to-queue my_app_product_export --env=prod

    One daemon or several daemon processes have to be started to execute the jobs.
    Please follow the documentation :doc:`/install_pim/manual/daemon_queue` if it's not the case.

Configure the UI for our new job
--------------------------------

At this point the job instance is usable in command line, but it cannot be configured via the UI.

Since our job is based on the native Product CSV export job, we can copy and paste the native configuration files, then customize it.

We need to provide a form name to the frontend to be able to render it properly. If your connector doesn't require extra fields, you can use the basic forms shipped with Akeneo.
There are actually two forms for each job: one for edit mode and one for view mode. This way we can tune very finely what is displayed for each mode.

For our form we'll need to copy:

- ``vendor/akeneo/pim-community-dev/src/Akeneo/Platform/Bundle/UIBundle/Resources/config/form_extensions/job_instance/csv_product_export_edit.yml`` to ``src/Acme/Bundle/NotifyConnectorBundle/Resources/config/form_extensions/job_instance/csv_product_export_notify_edit.yml``
- ``vendor/akeneo/pim-community-dev/src/Akeneo/Platform/Bundle/UIBundle/Resources/config/form_extensions/job_instance/csv_product_export_show.yml`` to ``src/Acme/Bundle/NotifyConnectorBundle/Resources/config/form_extensions/job_instance/csv_product_export_notify_show.yml``

Now replace all occurrence of ``csv-product-export`` in these files by, let's say, ``csv-product-export-notify``.
Indeed, each key in form configuration files must be unique across the whole application.

.. note::

    We are aware that this is not an ideal solution and we're working on a more satisfactory way to handle relations between forms.
    If you have any idea feel free to propose it or even write a contribution!


Now we need to declare a provider to link your job to the right form root:

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/Resources/config/providers.yml
    :language: yaml
    :linenos:

.. tip::

    Of course, if your job doesn't require any extra fields you don't need to use a specific form configuration.
    Just specify the root of the native form in your provider (that would be ``pim-job-instance-csv-product-export`` in our case).

Add a new field to the job instance form
----------------------------------------

For now we have the same form for our job than the native one.
We still need to add a field to be able to configure the target URL.

To do that, we need to register a new view in our form, representing the new field:

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/Resources/config/form_extensions/csv_product_export_notify_edit.yml
    :language: yaml
    :linenos:
    :lines: 267-277

.. literalinclude:: ../../src/Acme/Bundle/NotifyConnectorBundle/Resources/config/form_extensions/csv_product_export_notify_show.yml
    :language: yaml
    :linenos:
    :lines: 249-258

Job form fields need special properties defined under the ``config`` key:

- **fieldCode**: The path to the data inside the form model. It's usually ``configuration.myParam``, with ``myParam`` being the key you use in the default values provider, constraint collection provider, and in your custom steps.
- **readOnly**: Is this field in read only mode?
- **label**: The translation key for the field label.
- **tooltip**: The translation key for the help tooltip.

.. note::

    Here we used the very simple text field for our needs (``pim/job/common/edit/field/text`` module).
    You can also use other fields natively available in the PIM or, if you have more specific needs, create your own field.

Now we can create and edit job instances via the UI using the menu "Spread > Export profiles" then "Create export profile" button.

Add a tab to the job edit form
------------------------------

Let's say that we would like to add a custom tab to our job edit form in order to manage field mappings.

First, we need to create a Form extension in our bundle:

.. code-block:: javascript
    :linenos:

    'use strict';
    /*
     * /src/Acme/Bundle/EnrichBundle/Resources/public/js/job/product/edit/mapping.js
     */
    define(['pim/form'],
        function (BaseForm) {
            return BaseForm.extend({
                configure: function () {
                    this.trigger('tab:register', {
                        code: this.code,
                        isVisible: this.isVisible.bind(this),
                        label: 'Mapping'
                    });

                    return BaseForm.prototype.configure.apply(this, arguments);
                },
                render: function () {
                    this.$el.html('Hello world');

                    return this;
                },
                isVisible: function () {
                    return true;
                }
            });
        }
    );

For now this is a dummy extension, but this is a good start!

Let's register this file in the ``requirejs`` configuration

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/requirejs.yml

    config:
        paths:
            pim/job/product/edit/mapping: acmeenrich/js/job/product/edit/mapping

Now that our file is registered in ``requirejs`` configuration, we can add this extension to the product edit form:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/EnrichBundle/Resources/config/form_extensions/job_instance/csv_product_export_edit.yml

    extensions:
        pim-job-instance-csv-product-export-notify-edit-mapping:                 # The form extension code (can be whatever you want)
            module: pim/job/product/edit/mapping                                 # The requirejs module we just created
            parent: pim-job-instance-csv-product-export-notify-edit-tabs         # The parent extension in the form where we want to be registered
            aclResourceId: pim_importexport_export_profile_mapping_edit          # The user will need this ACL for this extension to be registered
            targetZone: container
            position: 140                                                        # The extension position
            config:
                tabTitle: acme_enrich.form.job_instance.tab.mapping.title
                tabCode: pim-job-instance-mapping


    # /src/Acme/Bundle/EnrichBundle/Resources/config/form_extensions/job_instance/csv_product_export_show.yml

    extensions:
        pim-job-instance-csv-product-export-notify-show-mapping:                 # The form extension code (can be whatever you want)
            module: pim/job/product/show/mapping                                 # The requirejs module we just created
            parent: pim-job-instance-csv-product-export-notify-show-tabs         # The parent extension in the form where we want to be registered
            aclResourceId: pim_importexport_export_profile_mapping_show          # The user will need this ACL for this extension to be registered
            targetZone: container
            position: 140                                                        # The extension position
            config:
                tabTitle: acme_enrich.form.job_instance.tab.mapping.title
                tabCode: pim-job-instance-mapping

To see your changes in the new tab in the job edit form you need to run:

.. code-block:: bash

    bin/console cache:clear
    bin/console --env=prod pim:installer:assets --symlink --clean
    yarn run webpack

If you don't see your changes, make sure you have run (``bin/console assets:install --symlink web``).

Make a job stoppable
--------------------

In Akeneo 5.0 we added the possibility to declare a Job as stoppable. But as some task may be not stoppable (important workflows that always need to finish, job depending on other steps, etc) we decided to make this new feature "opt in". In this documentation, we will explain you how to make your job stoppable.

.. note::

    If your job is based on the ``Akeneo\Tool\Component\Batch\Job\Job`` class, you have to adapt your job service definition by setting the last property of the constructor to "true" (as shown in the example below).

.. note::

    If your job is composed of an ``Akeneo\Tool\Component\Batch\Step\ItemStep`` instance, you should not have to do anything regarding the steps of your jobs as the stopping mechanism is already built in the ItemStep class.

To make this action available from the UI, you need to make sure of a few things:

1. You need to make sure your custom job implements ``Akeneo\Tool\Component\Batch\Job\StoppableJobInterface`` and set the last property of the constructor to "true". See the following job service definition example:

.. code-block:: yaml
    :linenos:

    pimee_catalog_rule.job.xlsx_product_import_with_rules:
        class: 'Akeneo\Tool\Component\Batch\Job\Job'
        arguments:
            - '%pimee_catalog_rule.job_name.xlsx_product_import_with_rules%'
            - '@event_dispatcher'
            - '@akeneo_batch.job_repository'
            -
                - '@pim_connector.step.charset_validator'
                - '@pimee_catalog_rule.step.xlsx_product.import'
                - '@pimee_catalog_rule.step.xlsx_product.import_associations'
                - '@pimee_catalog_rule.step.xlsx_product.execute_rules'
            - true # <-- This property should be true if the job is stoppable
        tags:
            - { name: akeneo_batch.job, connector: '%pim_connector.connector_name.xlsx%', type: '%pim_connector.job.import_type%' }

2. If your job uses tasklets (by implementing ``TaskletInterface``) to execute your business logic. You can inject the service `"akeneo_batch.job.job_stopper"` directly in your custom tasklets and use the function `JobStopper::isStopping` to know if user asked to stop the job. If this function return true, you should use the function `JobStopper::stop` and exit your Tasklet.

The following example shows a simple tasklet able to stop when a user stops the job from the UI.

.. code-block:: php
    :linenos:

    <?php
    declare(strict_types=1);

    namespace Acme\Bundle\StoppableJobBundle;

    class TrackableTasklet implements TaskletInterface
    {
        private const BATCH_SIZE = 100;

        protected ?StepExecution $stepExecution = null;
        protected FindItemsToProcess $findItemsToProcess;
        protected JobStopper $jobStopper;

        public function __construct(
            FindItemsToProcess $findItemsToProcess,
            JobStopper $jobStopper
        ) {
            $this->jobStopper = $jobStopper;
            $this->findItemsToProcess = $findItemsToProcess;
        }

        public function setStepExecution(StepExecution $stepExecution): void
        {
            $this->stepExecution = $stepExecution;
        }

        public function execute(): void
        {
            $itemsToProcess = $this->FindItemsToProcess->find();
            foreach ($itemsToProcess as $i => $itemToProcess) {
                $itemToProcess->doSomeWork();

                // Check every 100 items if the process should be stopped
                if ($i % self::BATCH_SIZE === 0
                    && $this->jobStopper->isStopping($this->stepExecution)
                ) {
                    $this->jobStopper->stop($this->stepExecution);

                    return;
                }
            }
        }
    }

Track the progress of a job
---------------------------

In Akeneo 5.0 we added the possibility to track the progress of job. But as some task may be not trackable we decided to make this new feature "opt in". In this documentation, we will explain you how to expose the progress of your job.

.. note::

    If your job is composed of an ``Akeneo\Tool\Component\Batch\Step\ItemStep`` instance, as well as a reader implementing ``Akeneo\Tool\Component\Batch\Item\TrackableItemReaderInterface`` the tracking of your job should already be available in the UI.


If your job uses a custom reader, make sure it implements ``Akeneo\Tool\Component\Batch\Item\TrackableItemReaderInterface`` and exposes the total of number of items that will be processed during the execution of the step.

If your job uses a custom tasklet, we need to make sure of a few additional things:

- your tasklet should implement the ``Akeneo\Tool\Component\Batch\Item\TrackableTaskletInterface`` interface
- at the very beginning of the execution of the tasklet, you need to provide the step execution with the total items your tasklet will process through the ``Akeneo\Tool\Component\Batch\Model\StepExecution::setTotalItems`` function.
- during the process of the tasklet, you need to provide the step execution with the progression the tasklet by incrementing a counter through the ``Akeneo\Tool\Component\Batch\Model\StepExecution::incrementProcessedItems`` function.

The following example shows a simple tasklet updating it's progress using the step execution.

.. code-block:: php
    :linenos:

    <?php
    declare(strict_types=1);

    namespace Acme\Bundle\StoppableJobBundle;

    class TrackableTasklet implements TaskletInterface, TrackableTaskletInterface
    {
        protected ?StepExecution $stepExecution = null;
        protected FindItemsToProcess $findItemsToProcess;
        protected JobRepositoryInterface $jobRepository;

        public function __construct(
            FindItemsToProcess $findItemsToProcess,
            JobRepositoryInterface $jobRepository
        ) {
            $this->findItemsToProcess = $findItemsToProcess;
            $this->jobRepository = $jobRepository;
        }

        public function setStepExecution(StepExecution $stepExecution): void
        {
            $this->stepExecution = $stepExecution;
        }

        public function execute(): void
        {
            // First, let's calculate the total items to process
            $itemsToProcess = $this->findItemsToProcess->find();
            $this->stepExecution->setTotalItems($itemsToProcess->count());

            // then, start to process entities
            // and update the step execution with the progress
            foreach ($itemsToProcess as $itemToProcess) {
                $itemToProcess->doSomeWork();
                $this->stepExecution->incrementProcessedItems();
                $this->jobRepository->updateStepExecution($this->stepExecution);
            }
        }

        public function isTrackable(): bool
        {
            return true;
        }
    }


.. warning::

    Make sure to only call `JobStopper::isStopped` not too often as it will do a MySQL query. A good way of doing it could be to do it only every 100 loop tour.

.. note::

    You can refer to the ``Akeneo\Pim\Enrichment\Component\Product\Job\DeleteProductsAndProductModelsTasklet`` to see how the tracking is implemented and how the step execution is kept up to date with the progression.
