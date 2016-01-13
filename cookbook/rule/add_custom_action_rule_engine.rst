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

-First let's see how to create the action. You need to create an ActionApplier object that will contain the logic:		

.. code-block:: php

    #/src/Acme/Bundle/CustomBundle/ActionApplier/PatternActionApplier.php
    <?php

    namespace Acme\Bundle\CustomBundle\ActionApplier;

    use Acme\Bundle\CustomBundle\Model\ProductPatternAction;
    use Akeneo\Component\RuleEngine\ActionApplier\ActionApplierInterface;
    use Akeneo\Component\StorageUtils\Updater\PropertySetterInterface;

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

.. code-block:: php

    #/src/Acme/Bundle/CustomBundle/Model/ProductPatternAction.php
    <?php

    namespace Acme\Bundle\CustomBundle\Model;

    use Akeneo\Bundle\RuleEngineBundle\Model\ActionInterface;
    use PimEnterprise\Component\CatalogRule\Model\ProductAddActionInterface;

    class ProductPatternAction implements ActionInterface
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
        constraints:
            - \PimEnterprise\Bundle\CatalogRuleBundle\Validator\Constraints\ProductRule\PropertyAction: ~
        properties:
            field:
               - Type:
                    type: string
               - NotBlank: ~
               - Length:
                   max: 255
               - \PimEnterprise\Bundle\CatalogRuleBundle\Validator\Constraints\ExistingSetField: ~
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

Here is an example on how you could write a rule.

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
