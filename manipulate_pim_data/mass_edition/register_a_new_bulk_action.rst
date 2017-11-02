How to register a new bulk action
=================================

In this cookbook, you will learn how to create a new bulk action. We will create a bulk action for products to add a comment for a set of products.
Following this cookbook, you will be able to create any bulk action for any entity of the PIM.

To create a new bulk action on a product, you need to

- create a new processor to process a product,
- create a job with this processor,
- create the page for the new bulk action.

Create the processor
--------------------

A processor inherits from ``Pim\Bundle\EnrichBundle\Connector\Processor\AbstractProcessor``.
Any processor should have a ``process($item)`` method to process an entity. Here, the ``process`` method will add a comment for a product.

.. note::

    Each bulk action has a set of ``actions`` to do. Here, we register in the ``actions`` array a unique action containing the comment and the username.

.. code-block:: php

    // src/Acme/Bundle/AppBundle/Connector/Processor/MassEdit/Product/AddCommentProcessor.php
    <?php
    'use strict';

    namespace Acme\Bundle\AppBundle\Connector\Processor\MassEdit\Product;

    use Akeneo\Component\StorageUtils\Saver\SaverInterface;
    use Pim\Bundle\CommentBundle\Builder\CommentBuilder;
    use Pim\Bundle\CommentBundle\Model\CommentInterface;
    use Pim\Bundle\EnrichBundle\Connector\Processor\AbstractProcessor;
    use Pim\Bundle\UserBundle\Repository\UserRepositoryInterface;
    use Symfony\Component\Validator\Validator\ValidatorInterface;

    class AddCommentProcessor extends AbstractProcessor
    {
        protected $commentBuilder;
        protected $commentSaver;
        protected $userRepository;

        public function __construct(
            CommentBuilder $commentBuilder,
            SaverInterface $commentSaver,
            UserRepositoryInterface $userRepository
        ) {
            $this->commentBuilder = $commentBuilder;
            $this->commentSaver = $commentSaver;
            $this->userRepository = $userRepository;
        }

        public function process($product): void
        {
            $actions = $this->getConfiguredActions();

            $comment = $this->commentBuilder->buildComment(
                $product,
                $this->userRepository->findOneByIdentifier($actions[0]['username'])
            )->setBody($actions[0]['value']);
            $this->commentSaver->save($comment);

            return $product;
        }
    }


Then, declare a service in a processor configuration file:

.. code-block:: yaml

    # src/Acme/Bundle/AppBundle/Resources/config/processors.yml
    parameters:
        acme.connector.processor.mass_edit.product.add_comment.class: Acme\Bundle\AppBundle\Connector\Processor\MassEdit\Product\AddCommentProcessor

    services:
        acme.connector.processor.mass_edit.product.add_comment:
            class: '%acme.connector.processor.mass_edit.product.add_comment.class%'
            arguments:
                - '@pim_comment.builder.comment'
                - '@pim_comment.saver.comment'
                - '@pim_user.repository.user'

Don't forget to load this new configuration file in your dependency injection:

.. code-block:: php

    // src/Acme/Bundle/AppBundle/DependencyInjection/AcmeAppExtension.php
    <?php

    namespace Acme\Bundle\AppBundle\DependencyInjection;

    use Symfony\Component\Config\FileLocator;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\DependencyInjection\Loader;
    use Symfony\Component\HttpKernel\DependencyInjection\Extension;

    class AcmeAppExtension extends Extension
    {
        public function load(array $configs, ContainerBuilder $container)
        {
            $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('processors.yml');
        }
    }

Create the job
--------------

The job will be run in background to process the entities with the processor defined above.
The job that we'll create has one single step, and this step is the default step.
The default step is composed of a reader (reading the products from the database, already exists), a processor and a writer (writing products to the database, already exists).
As there is no need to redefine any class for this job, we simply add configuration files:

.. code-block:: yaml

    # src/Acme/Bundle/AppBundle/Resources/config/steps.yml
    services:
        acme.step.add_comment.mass_edit:
            class: '%pim_connector.step.item_step.class%'
            arguments:
                - 'perform'
                - '@event_dispatcher'
                - '@akeneo_batch.job_repository'
                - '@pim_enrich.reader.database.product_and_product_model'
                - '@acme.connector.processor.mass_edit.product.add_comment'
                - '@pim_connector.writer.database.product'

.. note::

    Each job needs to define a default value provider and a constraint collection provider.
    In this very simple case, we will use the default product mass edit ones, so we added it without any additional parameter.

.. code-block:: yaml

    # src/Acme/Bundle/AppBundle/Resources/config/jobs.yml
    services:
        acme.job.add_comment:
            class: '%pim_connector.job.simple_job.class%'
            arguments:
                - 'add_comment'
                - '@event_dispatcher'
                - '@akeneo_batch.job_repository'
                - [ '@acme.step.add_comment.mass_edit' ]
            tags:
                -
                    name: akeneo_batch.job
                    connector: '%pim_enrich.connector_name.mass_edit%'
                    type: '%pim_enrich.job.mass_edit_type%'

        acme.job.default_values_provider.add_comment:
            class: '%pim_enrich.connector.job.job_parameters.default_values_provider.product_mass_edit.class%'
            arguments:
                - [ 'add_comment' ]
            tags:
                - { name: akeneo_batch.job.job_parameters.default_values_provider }

        acme.job.constraint_collection_provider.add_comment:
            class: '%pim_enrich.connector.job.job_parameters.constraint_collection_provider.product_mass_edit.class%'
            arguments:
                - [ 'add_comment' ]
            tags:
                - { name: akeneo_batch.job.job_parameters.constraint_collection_provider }

Just like we did above, load these files to the dependency injection:

.. code-block:: php

    // src/Acme/Bundle/AppBundle/DependencyInjection/AcmeAppExtension.php
    [...]
    $loader->load('jobs.yml');
    $loader->load('steps.yml');
    [...]

Finally, add this new job instance to the database to be able to run it:

.. code-block:: bash

    bin/console akeneo:batch:create-job internal add_comment mass_edit add_comment '{}' 'Add comment' --env=prod

Create the UI
-------------

The UI of this mass action is simple: we just have to create a textarea field.
When a user updates this textarea, the data will be put in the form data.
The new module is composed of a template and a form extension.

.. code-block:: html

    <!-- src/Acme/Bundle/AppBundle/Resources/public/templates/add-comment.html -->
    <div class="AknFieldContainer">
        <div class="AknFieldContainer-header">
            <label class="AknFieldContainer-label">Comment</label>
        </div>
        <div class="AknFieldContainer-inputContainer">
            <textarea class="AknTextareaField comment-field" <% if (readOnly) { %> disabled="disabled"<% } %>><%- value %></textarea>
        </div>
    </div>

.. code-block:: javascript

    // src/Acme/Bundle/AppBundle/Resources/public/js/add-comment.js
    'use strict';
    define(['underscore', 'pim/mass-edit-form/product/operation', 'acme/template/add-comment', 'pim/user-context'],
        function (_, BaseOperation, template, UserContext) {
            return BaseOperation.extend({
                template: _.template(template),
                events: {
                    'change .comment-field': 'updateModel'
                },

                render: function () {
                    this.$el.html(this.template({
                        value: this.getValue(),
                        readOnly: this.readOnly
                    }));
                    return this;
                },

                updateModel: function (event) {
                    this.setValue(event.target.value);
                },

                setValue: function (comment) {
                    let data = this.getFormData();
                    data.actions = [{
                        field: 'comment',
                        value: comment,
                        username: UserContext.get('username')
                    }];
                    this.setData(data);
                },

                getValue: function () {
                    const action = _.findWhere(this.getFormData().actions, { field: 'comment' });
                    return action ? action.value : null;
                }
            });
        }
    );

Then, register these new modules and add a new bulk action to the current list of bulk actions:

.. code-block:: yaml

    # src/Acme/Bundle/AppBundle/Resources/config/requirejs.yml
    config:
        paths:
            acme/add-comment: acmeapp/js/add-comment
            acme/template/add-comment: acmeapp/templates/add-comment.html

.. code-block:: yaml

    # src/Acme/Bundle/AppBundle/Resources/config/form_extensions/mass_edit/product.yml
    extensions:
        acme-mass-product-edit-configure-add-comment:
            module: acme/add-comment
            parent: pim-mass-product-edit
            position: 500
            config:
                title: pim_enrich.mass_edit.product.title
                label: 'Add comment'
                labelCount: "{1}Add comment to <span class=\"AknFullPage-title--highlight\">1 product</span>|]1, Inf[Add comment to <span class=\"AknFullPage-title--highlight\">{{ itemsCount }} products</span>"
                description: 'Add a comment for a set of products'
                code: add_comment
                jobInstanceCode: add_comment
                icon: icon-template

Recompute the assets
--------------------

Finally, you have to reinstall your assets:

.. code-block:: bash

    rm -rf var/cache/
    bin/console pim:install:assets
    bin/console assets:install --symlink
    npm run webpack

That's it! If you select several products then click "Bulk actions", your will be able to use your new feature.
