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

First let's see how to add the action. You need to override `PimEnterprise\Bundle\CatalogRuleBundle\Engine\ProductRuleApplier` service that will contain the logic:

.. code-block:: php

    #/src/Acme/Bundle/CustomBundle/Engine/ProductRuleApplier/ProductsUpdater.php
    <?php

    namespace Acme\Bundle\CustomBundle\Engine\ProductRuleApplier;

    use Acme\Bundle\CustomBundle\Model\ProductPatternActionInterface;
    use Akeneo\Bundle\RuleEngineBundle\Model\RuleInterface;
    use Doctrine\Common\Util\ClassUtils;
    use Pim\Bundle\CatalogBundle\Model\ProductInterface;
    use PimEnterprise\Bundle\CatalogRuleBundle\Engine\ProductRuleApplier\ProductsUpdater as BaseProductsUpdater;
    use PimEnterprise\Bundle\CatalogRuleBundle\Model\ProductCopyValueActionInterface;
    use PimEnterprise\Bundle\CatalogRuleBundle\Model\ProductSetValueActionInterface;

    class ProductsUpdater extends BaseProductsUpdater
    {
        /**
         * @param ProductInterface[] $products
         * @param RuleInterface      $rule
         */
        public function updateFromRule(array $products, RuleInterface $rule)
        {
            $actions = $rule->getActions();
            foreach ($actions as $action) {
                if ($action instanceof ProductSetValueActionInterface) {
                    $this->applySetAction($products, $action);
                } elseif ($action instanceof ProductCopyValueActionInterface) {
                    $this->applyCopyAction($products, $action);
                } elseif ($action instanceof ProductPatternActionInterface) {
                    $this->applyPatternAction($products, $action);
                } else {
                    throw new \LogicException(
                        sprintf('The action "%s" is not supported yet.', ClassUtils::getClass($action))
                    );
                }
            }
        }

        /**
         * Applies a pattern action on a subject set.
         *
         * @param ProductInterface[]            $products
         * @param ProductPatternActionInterface $action
         *
         * @return ProductRuleApplier
         */
        public function applyPatternAction(array $products = [], ProductPatternActionInterface $action)
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
    }

Then we need to create the object that will handle the data.

.. code-block:: php

    #/src/Acme/Bundle/CustomBundle/Model/ProductPatternAction.php
    <?php

    namespace Acme\Bundle\CustomBundle\Model;

    use Akeneo\Bundle\RuleEngineBundle\Model\ActionInterface;
    use PimEnterprise\Component\CatalogRule\Model\ProductAddActionInterface;

    class ProductPatternAction implements ProductPatternActionInterface
    {
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
         * {@inheritdoc}
         */
        public function getImpactedFields()
        {
            return [$this->getField()];
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
         *
         * @return ProductAddActionInterface
         */
        public function setAttributes(array $attributes = [])
        {
            $this->attributes = $attributes;
        }

        /**
         * @param string $pattern
         *
         * @return ProductAddActionInterface
         */
        public function setPattern($pattern)
        {
            $this->pattern = $pattern;
        }
    }

    #/src/Acme/Bundle/CustomBundle/Model/ProductPatternActionInterface.php
    <?php

    namespace Acme\Bundle\CustomBundle\Model;

    use Akeneo\Bundle\RuleEngineBundle\Model\ActionInterface;
    use PimEnterprise\Bundle\CatalogRuleBundle\Model\FieldImpactActionInterface;

    interface ProductPatternActionInterface extends ActionInterface, FieldImpactActionInterface
    {
        const ACTION_TYPE = 'pattern';

        /**
         * @return string
         */
        public function getField();

        /**
         * @param string $field
         *
         * @return ProductPatternActionInterface
         */
        public function setField($field);

        /**
         * @return array
         */
        public function getOptions();

        /**
         * @param array $options
         *
         * @return ProductPatternActionInterface
         */
        public function setOptions(array $options = []);

        /**
         * @return array
         */
        public function getAttributes();

        /**
         * @param array $attributes
         *
         * @return ProductPatternActionInterface
         */
        public function setAttributes(array $attributes = []);

        /**
         * @return string
         */
        public function getPattern();

        /**
         * @param string $pattern
         *
         * @return ProductPatternActionInterface
         */
        public function setPattern($pattern);
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

    use Pim\Bundle\CatalogBundle\Repository\AttributeRepositoryInterface;
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
        public $message = 'There are no attributes with such code : "%attribute%"';

        /**
         * {@inheritdoc}
         */
        public function validatedBy()
        {
            return 'pimee_constraint_attributes_validator';
        }
    }

.. code-block:: yml

    #/src/Acme/Bundle/CustomBundle/Resources/config/validation/ProductPatternAction.yml
    Acme\Bundle\CustomBundle\Model\ProductPatternAction:
        properties:
            attributes:
                - Type:
                    type: array
                - NotBlank:
                    message: The key "attributes" is missing or empty.
                - \Acme\Bundle\CustomBundle\Validator\Constraints\ExistingAttributes: ~
            pattern:
               - Type:
                    type: string
               - NotBlank: ~
               - Length:
                   max: 255

Don't forget to add these classes in you service definition and to tag them with the proper tag

.. code-block:: yml

    #/src/Acme/Bundle/CustomBundle/Resources/config/services.yml
    parameters:
        pimee_catalog_rule.applier.product.updater.class: Acme\Bundle\CustomBundle\Engine\ProductRuleApplier\ProductsUpdater

    services:
        acme.action_applier.pattern:
            class: Acme\Bundle\CustomBundle\ActionApplier\PatternActionApplier
            arguments:
                - '@pim_catalog.updater.product_property_setter'
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

        # you need to override this service to register your denormalizer
        pimee_catalog_rule.denormalizer.product_rule.chained:
            class: %pimee_catalog_rule.denormalizer.product_rule.chained.class%
            calls:
                - [addDenormalizer, ['@pimee_catalog_rule.denormalizer.product_rule.condition']]
                - [addDenormalizer, ['@pimee_catalog_rule.denormalizer.product_rule.set_value_action']]
                - [addDenormalizer, ['@pimee_catalog_rule.denormalizer.product_rule.copy_value_action']]
                - [addDenormalizer, ['@pimee_catalog_rule.denormalizer.product_rule.content']]
                - [addDenormalizer, ['@pimee_catalog_rule.denormalizer.product_rule']]
                - [addDenormalizer, ['@acme.denormalizer.product_rule.pattern_action']]

        # you need to override this service to register your action
        pimee_catalog_rule.denormalizer.product_rule.content:
            class: %pimee_catalog_rule.denormalizer.product_rule.content.class%
            arguments:
                - %akeneo_rule_engine.model.rule.class%
                - %pimee_catalog_rule.model.product_condition.class%
                - copy_value: %pimee_catalog_rule.model.copy_value_action.class%
                  set_value: %pimee_catalog_rule.model.set_value_action.class%
                  pattern: Acme\Bundle\CustomBundle\Model\ProductPatterAction

Here is an example of how you could write a rule.

.. code-block:: txt

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
