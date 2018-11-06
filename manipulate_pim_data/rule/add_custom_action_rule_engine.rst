How to add a custom action in the rule engine
=============================================

Quick Overview
--------------

**This cookbook is about a feature only provided in the Enterprise Edition.**

This cookbook assumes that you already created a new bundle to add your custom rule. Let's assume its namespace is `Acme\\CustomBundle`.

Create a custom action
----------------------

In this cookbook we are going to see how to add a custom action in the rule engine.
For this example, the goal of this rule is to concatenate attributes name, price and total megapixels into the description field.

First let's see how to create the action. You need to create an ActionApplier object that will contain the logic:

.. code-block:: php

    #/src/Acme/Bundle/CustomBundle/ActionApplier/PatternActionApplier.php
    <?php

    namespace Acme\Bundle\CustomBundle\ActionApplier;

    use Akeneo\Bundle\Tool\RuleEngineBundle\Model\ActionInterface;
    use Acme\Bundle\CustomBundle\Model\ProductPatternAction;
    use Akeneo\Tool\Component\RuleEngine\ActionApplier\ActionApplierInterface;
    use Akeneo\Tool\Component\StorageUtils\Updater\PropertySetterInterface;

    class PatternActionApplier implements ActionApplierInterface
    {
        /** @var PropertySetterInterface */
        protected $propertySetter;

        /**
         * @param PropertySetterInterface $propertySetter
         */
        public function __construct(PropertySetterInterface $propertySetter)
        {
            $this->propertySetter = $propertySetter;
        }

        /**
         * {@inheritdoc}
         */
        public function applyAction(ActionInterface $action, array $products = [])
        {
            $attributes = $action->getAttributes();
            $pattern    = $action->getPattern();

            foreach ($products as $product) {
                $result = $pattern;

                foreach ($attributes as $attributeCode) {
                    $value = $product->getValue($attributeCode);

                    $content = null === $value ? '' : (string) $value;
                    $result = str_replace('%%' . $attributeCode . '%%', $content, $result);
                }

                $this->propertySetter->setData(
                    $product,
                    $action->getField(),
                    $result,
                    $action->getOptions()
                );
            }
        }

        /**
         * {@inheritdoc}
         */
        public function supports(ActionInterface $action)
        {
            return $action instanceof ProductPatternAction;
        }
    }

Then we need to create the object that will handle the data.

.. tip::

    Implementing the FieldImpactActionInterface will allow the attribute to be flagged as smart in the UI.

.. code-block:: php

    #/src/Acme/Bundle/CustomBundle/Model/ProductPatternAction.php
    <?php

    namespace Acme\Bundle\CustomBundle\Model;

    use Akeneo\Bundle\Tool\RuleEngineBundle\Model\ActionInterface;
    use Akeneo\Pim\Automation\RuleEngine\Component\Model\FieldImpactActionInterface;

    class ProductPatternAction implements ActionInterface, FieldImpactActionInterface
    {
        const ACTION_TYPE = 'pattern';

        /** @var string */
        protected $field;

        /** @var array */
        protected $attributes = [];

        /** @var string */
        protected $pattern;

        /** @var array */
        protected $options = [];

        /**
         * {@inheritdoc}
         */
        public function getField()
        {
            return $this->field;
        }

        /**
         * {@inheritdoc}
         */
        public function setField($field)
        {
            $this->field = $field;
        }

        /**
         * {@inheritdoc}
         */
        public function getOptions()
        {
            return $this->options;
        }

        /**
         * {@inheritdoc}
         */
        public function setOptions(array $options = [])
        {
            $this->options = $options;
        }

        /**
         * @return array
         */
        public function getAttributes()
        {
            return $this->attributes;
        }

        /**
         * @return array
         */
        public function getPattern()
        {
            return $this->pattern;
        }

        /**
         * @param array $attributes
         */
        public function setAttributes(array $attributes = [])
        {
            $this->attributes = $attributes;
        }

        /**
         * @param string $pattern
         */
        public function setPattern($pattern)
        {
            $this->pattern = $pattern;
        }

        /**
         * {@inheritdoc}
         */
        public function getImpactedFields()
        {
            return [$this->getField()];
        }
    }

We also need to create a denormalizer that will return our previous object that handles the data. It will convert the array into an object (needed for the import).

.. code-block:: php

    #/src/Acme/Bundle/CustomBundle/Denormalizer/ProductRule/PatternActionDenormalizer.php
    <?php

    namespace Acme\Bundle\CustomBundle\Denormalizer\ProductRule;

    use Acme\Bundle\CustomBundle\Model\ProductPatternAction;
    use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

    class PatternActionDenormalizer extends GetSetMethodNormalizer
    {
        /**
         * {@inheritdoc}
         */
        public function denormalize($data, $class, $format = null, array $context = [])
        {
            return parent::denormalize($data, 'Acme\Bundle\CustomBundle\Model\ProductPatternAction');
        }

        /**
         * {@inheritdoc}
         */
        public function supportsDenormalization($data, $type, $format = null)
        {
            return isset($data['type']) && ProductPatternAction::ACTION_TYPE === $data['type'];
        }
    }

For our example we need to create an `ExistingAttributeValidator` that will check if the attributes provided in the rule file exist. It will raise a violation and skip this item if not.

.. code-block:: php

    #/src/Acme/Bundle/CustomBundle/Validator/Constraints/ExistingAttributesValidator.php
    <?php

    namespace Acme\Bundle\CustomBundle\Validator\Constraints;

    use Akeneo\Pim\Structure\Component\Repository\AttributeRepositoryInterface;
    use Symfony\Component\Validator\Constraint;
    use Symfony\Component\Validator\ConstraintValidator;

    class ExistingAttributesValidator extends ConstraintValidator
    {
        /** @var AttributeRepositoryInterface */
        protected $attributeRepository;

        /**
         * @param AttributeRepositoryInterface $attributeRepository
         */
        public function __construct(AttributeRepositoryInterface $attributeRepository)
        {
            $this->attributeRepository = $attributeRepository;
        }

        /**
         * {@inheritdoc}
         */
        public function validate($attributes, Constraint $constraint)
        {
            foreach ($attributes as $attribute) {
                if (null === $this->attributeRepository->findOneByIdentifier($attribute)) {
                    $this->context->buildViolation($constraint->message, ['%attribute%' => $attribute])->addViolation();
                }
            }
        }
    }

Here is the constraint message and its associated validation file:

.. code-block:: php

    #/src/Acme/Bundle/CustomBundle/Validator/Constraints/ExistingAttributes.php
    <?php

    namespace Acme\Bundle\CustomBundle\Validator\Constraints;

    use Symfony\Component\Validator\Constraint;

    class ExistingAttributes extends Constraint
    {
        /** @var string */
        public $message = 'There are no attributes with such code: "%attribute%"';

        /**
         * {@inheritdoc}
         */
        public function validatedBy()
        {
            return 'pimee_constraint_attributes_validator';
        }
    }

.. code-block:: yaml

    #/src/Acme/Bundle/CustomBundle/Resources/config/validation/ProductPatternAction.yml
    Acme\Bundle\CustomBundle\Model\ProductPatternAction:
        constraints:
            - Akeneo\Pim\Automation\RuleEngine\Bundle\Validator\Constraint\PropertyAction: ~
        properties:
            field:
               - Type:
                    type: string
               - NotBlank: ~
               - Length:
                   max: 255
               - Akeneo\Pim\Automation\RuleEngine\Bundle\Validator\Constraint\ExistingSetField: ~
            attributes:
                - Type:
                    type: array
                - NotBlank:
                    message: The key "attributes" is missing or empty.
                - Acme\Bundle\CustomBundle\Validator\Constraints\ExistingAttributes: ~
            pattern:
               - Type:
                    type: string
               - NotBlank: ~
               - Length:
                   max: 255

Don't forget to add these classes in your service definition and to tag them with the proper tag

.. code-block:: yaml

    #/src/Acme/Bundle/CustomBundle/Resources/config/services.yml
    services:
        acme.action_applier.pattern:
            class: Acme\Bundle\CustomBundle\ActionApplier\PatternActionApplier
            arguments:
                - '@pim_catalog.updater.property_setter'
            tags:
                - { name: akeneo_rule_engine.action_applier, priority: 100 }

        acme.denormalizer.product_rule.pattern_action:
            class: Acme\Bundle\CustomBundle\Denormalizer\ProductRule\PatternActionDenormalizer
            tags:
                - { name: 'pimee_catalog_rule.denormalizer.product_rule' }

        acme.validator.existing_attributes:
            class: Acme\Bundle\CustomBundle\Validator\Constraints\ExistingAttributesValidator
            arguments:
                - '@pim_catalog.repository.attribute'
            tags:
                - { name: validator.constraint_validator, alias: pimee_constraint_attributes_validator }

You have to override the action column from the rule view to use the pattern type.

.. code-block:: jinja

    {# src/Acme/Bundle/CustomBundle/Resources/views/Rules/_actions.html.twig #}
    {% for action in value.actions %}
        <p class="AknRule">
            {% if action.type in ["copy", "copy_value"] %}
                {% set parameters = {
                '%from_field%': action.from_field|append_locale_and_scope_context(action.from_locale|default, action.from_scope|default)|highlight,
                '%to_field%': action.to_field|append_locale_and_scope_context(action.to_locale|default, action.to_scope|default)|highlight
                } %}
            {% elseif action.type in ["add", "remove"] %}
                {% set parameters = {
                '%field%': action.field|append_locale_and_scope_context(action.options.locale|default, action.options.scope|default)|highlight,
                '%value%': action.items|present_rule_action_value(action.field)|highlight,
                } %}
            {% elseif action.type == 'pattern' %}
                {% set parameters = {
                '%field%': action.field|append_locale_and_scope_context(action.options.locale|default, action.options.scope|default)|highlight,
                '%attributes%': action.attributes|join(',')|highlight
                } %}
            {% else %}
                {% set parameters = {
                '%field%': action.field|append_locale_and_scope_context(action.locale|default, action.scope|default)|highlight,
                '%value%': action.value|present_rule_action_value(action.field)|highlight
                } %}
            {% endif %}

            {{ ('pimee_catalog_rule.actions.type.' ~ action.type) |trans(parameters)|raw }}
        </p>
    {% endfor %}

You also need to override the rule file for the datagrid with your template.

.. code-block:: yaml

    #src/Acme/Bundle/CustomBundle/Resources/config/datagrid/rule.yml
    datagrid:
        rule-grid:
            source:
                acl_resource: pimee_catalog_rule_rule_view_permissions
                repository_method: createDatagridQueryBuilder
                type: pim_datasource_rule
                entity: '%akeneo_rule_engine.model.rule_definition.class%'
            columns:
                code:
                    label: pimee_catalog_rule.datagrid.rule-grid.column.code
                conditions:
                    label: pimee_catalog_rule.datagrid.rule-grid.column.conditions
                    type: twig
                    template: PimEnterpriseCatalogRuleBundle:Rule:_conditions.html.twig
                    frontend_type: html
                    data_name: content
                actions:
                    label: pimee_catalog_rule.datagrid.rule-grid.column.actions
                    type: twig
                    template: AcmeCustomBundle:Rules:_actions.html.twig
                    frontend_type: html
                    data_name: content
                impactedSubjectCount:
                    label: pimee_catalog_rule.datagrid.rule-grid.column.impacted_product_count.label
                    type: twig
                    template: PimEnterpriseCatalogRuleBundle:Rule:_impacted_product_count.html.twig
                    frontend_type: html
            properties:
                id: ~
                execute_link:
                    type: url
                    route: pimee_catalog_rule_rule_execute
                    params:
                        - code
                delete_link:
                    type: url
                    route: pimee_catalog_rule_rule_delete
                    params:
                        - id
            actions:
                execute:
                    launcherOptions:
                        className: AknIconButton AknIconButton--small AknIconButton--play
                    type: ajax
                    label: pimee_catalog_rule.datagrid.rule-grid.actions.execute
                    link: execute_link
                    acl_resource: pimee_catalog_rule_rule_execute_permissions
                    confirmation: true
                    messages:
                        confirm_title: pimee_catalog_rule.datagrid.rule-grid.actions.execute.confirm_title
                        confirm_content: pimee_catalog_rule.datagrid.rule-grid.actions.execute.confirm_content
                        confirm_ok: pimee_catalog_rule.datagrid.rule-grid.actions.execute.confirm_ok
                delete:
                    launcherOptions:
                        className: AknIconButton AknIconButton--small AknIconButton--trash
                    type: delete
                    label: pimee_catalog_rule.datagrid.rule-grid.actions.delete
                    link: delete_link
                    acl_resource:  pimee_catalog_rule_rule_delete_permissions
            filters:
                columns:
                    code:
                        type: string
                        data_name:   r.code
            sorters:
                columns:
                    code:
                        data_name: r.code
                    impactedSubjectCount:
                        data_name: r.impactedSubjectCount
                default:
                    code: '%oro_datagrid.extension.orm_sorter.class%::DIRECTION_ASC'
            mass_actions_groups:
                bulk_actions:
                    label: pim_datagrid.mass_action_group.bulk_actions.label
            mass_actions:
                impacted_product_count:
                    type: ajax
                    acl_resource: pimee_catalog_rule_rule_impacted_product_count_permissions
                    handler: rule_impacted_product_count
                    label: pimee_catalog_rule.datagrid.rule-grid.mass_edit_action.impacted_product_count
                    route: pimee_catalog_rule_rule_mass_impacted_product_count
                    messages:
                        confirm_title: pimee_catalog_rule.datagrid.rule-grid.mass_edit_action.confirm_title
                        confirm_content: pimee_catalog_rule.datagrid.rule-grid.mass_edit_action.confirm_content
                        confirm_ok: pimee_catalog_rule.datagrid.rule-grid.mass_edit_action.confirm_ok
                    launcherOptions:
                        group: bulk_actions
                execute:
                    type: ajax
                    acl_resource: pimee_catalog_rule_rule_execute_permissions
                    label: pimee_catalog_rule.datagrid.rule-grid.mass_edit_action.execute
                    handler: mass_execute_rule
                    messages:
                        confirm_title: pimee_catalog_rule.datagrid.rule-grid.mass_action.execute.confirm_title
                        confirm_content: pimee_catalog_rule.datagrid.rule-grid.mass_action.execute.confirm_content
                        confirm_ok: pimee_catalog_rule.datagrid.rule-grid.mass_action.execute.confirm_ok
                        success: pimee_catalog_rule.datagrid.rule-grid.mass_action.execute.success
                        error: pimee_catalog_rule.datagrid.rule-grid.mass_action.execute.error
                        empty_selection: pimee_catalog_rule.datagrid.rule-grid.mass_action.execute.empty_selection
                    launcherOptions:
                        group: bulk_actions
                delete:
                    type: delete
                    entity_name: rule
                    acl_resource: pimee_catalog_rule_rule_delete_permissions
                    handler: mass_delete_rule
                    label: pimee_catalog_rule.datagrid.rule-grid.mass_edit_action.delete
                    messages:
                        confirm_title: pimee_catalog_rule.datagrid.rule-grid.mass_action.delete.confirm_title
                        confirm_content: pimee_catalog_rule.datagrid.rule-grid.mass_action.delete.confirm_content
                        confirm_ok: pimee_catalog_rule.datagrid.rule-grid.mass_action.delete.confirm_ok
                        success: pimee_catalog_rule.datagrid.rule-grid.mass_action.delete.success
                        error: pimee_catalog_rule.datagrid.rule-grid.mass_action.delete.error
                        empty_selection: pimee_catalog_rule.datagrid.rule-grid.mass_action.delete.empty_selection
                    launcherOptions:
                        group: bulk_actions

Then, add the translations.

.. code-block:: yaml

    #src/Acme/Bundle/CustomBundle/Resources/translations/messages.en.yml
    pimee_catalog_rule:
        actions:
            type:
                "pattern": Then attributes (%attributes%) from pattern are replaced by specific values into %field%

Here is an example on how you could write a rule.

.. code-block:: yaml

    rules:
        test_pattern:
            priority: 0
            conditions:
                -
                    field: family.code
                    operator: IN
                    value:
                        - camcorders
            actions:
                -
                    type: pattern
                    field: description
                    attributes:
                        - name
                        - price
                        - total_megapixels
                    pattern: '%%name%% -- %%price%% -- %%total_megapixels%%'
                    options:
                        scope: ecommerce
                        locale: en_US
