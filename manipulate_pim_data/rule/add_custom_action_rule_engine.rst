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

    use Akeneo\Pim\Automation\RuleEngine\Component\Exception\NonApplicableActionException;
    use Akeneo\Tool\Bundle\RuleEngineBundle\Model\ActionInterface;
    use Acme\Bundle\CustomBundle\Model\ProductPatternAction;
    use Akeneo\Tool\Component\RuleEngine\ActionApplier\ActionApplierInterface;
    use Akeneo\Tool\Component\StorageUtils\Updater\PropertySetterInterface;

    class PatternActionApplier implements ActionApplierInterface
    {
        protected PropertySetterInterface $propertySetter;

        public function __construct(PropertySetterInterface $propertySetter)
        {
            $this->propertySetter = $propertySetter;
        }

        /**
         * {@inheritdoc}
         */
        public function applyAction(ActionInterface $action, array $products = []): array
        {
            $attributes = $action->getAttributes();
            $pattern    = $action->getPattern();

            foreach ($products as $index => $product) {
                $result = $pattern;

                foreach ($attributes as $attributeCode) {
                    $value = $product->getValue($attributeCode);

                    $content = null === $value ? '' : (string) $value;
                    $result = str_replace('%%' . $attributeCode . '%%', $content, $result);
                }

                try {
                    $this->propertySetter->setData(
                        $product,
                        $action->getField(),
                        $result,
                        $action->getOptions()
                    );
                } catch (NonApplicableActionException $e) {
                    unset($products[$index]);
                }
            }

            return $products;
        }

        /**
         * {@inheritdoc}
         */
        public function supports(ActionInterface $action): bool
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

    use Akeneo\Tool\Bundle\RuleEngineBundle\Model\ActionInterface;
    use Akeneo\Pim\Automation\RuleEngine\Component\Model\FieldImpactActionInterface;

    class ProductPatternAction implements ActionInterface, FieldImpactActionInterface
    {
        const ACTION_TYPE = 'pattern';

        protected string $field;
        protected array $attributes = [];
        protected string $pattern;
        protected array $options = [];

        /**
         * {@inheritDoc}
         */
        public function getType(): string
        {
            return self::ACTION_TYPE;
        }

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
        public function getOptions(): array
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

        public function getAttributes(): array
        {
            return $this->attributes;
        }

        public function getPattern(): string
        {
            return $this->pattern;
        }

        public function setAttributes(array $attributes = []): void
        {
            $this->attributes = $attributes;
        }

        public function setPattern(string $pattern)
        {
            $this->pattern = $pattern;
        }

        /**
         * {@inheritdoc}
         */
        public function getImpactedFields(): array
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
            return parent::denormalize($data, ProductPatternAction::class);
        }

        /**
         * {@inheritdoc}
         */
        public function supportsDenormalization($data, $type, $format = null): bool
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
        protected AttributeRepositoryInterface $attributeRepository;

        public function __construct(AttributeRepositoryInterface $attributeRepository)
        {
            $this->attributeRepository = $attributeRepository;
        }

        /**
         * {@inheritdoc}
         */
        public function validate($attributes, Constraint $constraint): void
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
        public string $message = 'There are no attributes with such code: "%attribute%"';

        /**
         * {@inheritdoc}
         */
        public function validatedBy(): string
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
                    message: The "attributes" key is missing or empty.
                - Acme\Bundle\CustomBundle\Validator\Constraints\ExistingAttributes: ~
            pattern:
               - Type:
                    type: string
               - NotBlank: ~
               - Length:
                   max: 255

Don't forget to add these classes in your service definition and to tag them with the proper tag.
Also, do not forget to load your `services.yml` in your dependency injection, either in a bundle extension or in the config directory.

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


Here is an example on how you could write a rule.

.. code-block:: yaml

    rules:
        test_pattern:
            priority: 0
            enabled: true
            conditions:
                -
                    field: family
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
