Create a new Reference Entity Attribute type
============================================

.. note::

   Reference Entities feature is only available for the **Enterprise Edition**.

This cookbook will present you how to create your own Attribute Type for Reference Entities.
Currently, there are 6 types of Attribute for Reference Entities:

- Text
- Image
- Record (*Link Record to another Record*)
- Record Collection (*Link Record to several Records*)
- Option
- Option Collection

Requirements
------------

During this chapter, we assume that you already created a new bundle to add your custom Reference Entity Attribute. Let's assume its namespace is ``Acme\\CustomBundle``.

Create the Attribute
--------------------

For this tutorial, we will create a **custom boolean attribute** type for reference entity.
For the reference entities, we followed the Hexagonal architecture.

We'll see we need several classes in multiple "layers" (Domain, Application & Infrastructure).
- First we'll create Domain classes (the new custom Attribute itself and its Value Object)
- Then Application classes (only commands (from CQRS principle (https://martinfowler.com/bliki/CQRS.html))
- And to finish Infrastructure classes (an Hydrator to hydrate our Attribute coming from SQL)


1) Domain Layer
^^^^^^^^^^^^^^^

Let's start with our new custom Attribute. It must extends the ``\Akeneo\ReferenceEntity\Domain\Model\Attribute\AbstractAttribute``.

.. code-block:: php

    <?php
    namespace Acme\CustomBundle\Domain\Model\Attribute;

    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AbstractAttribute;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeCode;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeIdentifier;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeIsRequired;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeOrder;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeValuePerChannel;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeValuePerLocale;
    use Akeneo\ReferenceEntity\Domain\Model\LabelCollection;
    use Akeneo\ReferenceEntity\Domain\Model\ReferenceEntity\ReferenceEntityIdentifier;

    class BooleanAttribute extends AbstractAttribute
    {
        /** @var AttributeDefaultValue */
        private $defaultValue;

        protected function __construct(
            AttributeIdentifier $identifier,
            ReferenceEntityIdentifier $referenceEntityIdentifier,
            AttributeCode $code,
            LabelCollection $labelCollection,
            AttributeOrder $order,
            AttributeIsRequired $isRequired,
            AttributeValuePerChannel $valuePerChannel,
            AttributeValuePerLocale $valuePerLocale,
            AttributeDefaultValue $defaultValue
        ) {
            parent::__construct(
                $identifier,
                $referenceEntityIdentifier,
                $code,
                $labelCollection,
                $order,
                $isRequired,
                $valuePerChannel,
                $valuePerLocale
            );

            $this->defaultValue = $defaultValue;
        }

        public static function createBoolean(
            AttributeIdentifier $identifier,
            ReferenceEntityIdentifier $referenceEntityIdentifier,
            AttributeCode $code,
            LabelCollection $labelCollection,
            AttributeOrder $order,
            AttributeIsRequired $isRequired,
            AttributeValuePerChannel $valuePerChannel,
            AttributeValuePerLocale $valuePerLocale,
            AttributeDefaultValue $defaultValue
        ) {
            return new self(
                $identifier,
                $referenceEntityIdentifier,
                $code,
                $labelCollection,
                $order,
                $isRequired,
                $valuePerChannel,
                $valuePerLocale,
                $defaultValue
            );
        }

        protected function getType(): string
        {
            return 'boolean';
        }
    }


Now we need to create its value object for the property "DefaultValue":

.. code-block:: php

    <?php
    namespace Acme\CustomBundle\Domain\Model\Attribute;

    class AttributeDefaultValue
    {
        /** @var bool */
        private $defaultValue;

        private function __construct(bool $defaultValue)
        {
            $this->defaultValue = $defaultValue;
        }

        public static function fromBoolean(bool $defaultBooleanValue): self
        {
            return new self($defaultBooleanValue);
        }

        public function normalize(): bool
        {
            return $this->defaultValue;
        }
    }

2) Application Layer
^^^^^^^^^^^^^^^^^^^^

Now that we have our Attribute class, we need to create classes to handle its creation and edition.

We'll need first to add the "Creation command", it needs to extend ``\Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute\AbstractCreateAttributeCommand``.

.. code-block:: php

    <?php
    namespace Acme\CustomBundle\Application\Attribute\CreateAttribute;

    class CreateBooleanAttributeCommand extends AbstractCreateAttributeCommand
    {
        /** @var bool */
        public $defaultValue; // Example of parameter for your creation command

        public function __construct(
            string $referenceEntityIdentifier,
            string $code,
            array $labels,
            bool $isRequired,
            bool $valuePerChannel,
            bool $valuePerLocale,
            bool $defaultValue
        ) {
            parent::__construct(
                $referenceEntityIdentifier,
                $code,
                $labels,
                $isRequired,
                $valuePerChannel,
                $valuePerLocale
            );

            $this->defaultValue = $defaultValue;
        }
    }

For the edition of this attribute, we'll need to create a command to edit the property of our attribute (default value):

.. code-block:: php

    <?php
    namespace Acme\CustomBundle\Application\Attribute\EditAttribute\CommandFactory;

    use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\AbstractEditAttributeCommand;

    class EditDefaultValueCommand extends AbstractEditAttributeCommand
    {
        /** @var boolean */
        public $defaultValue;

        public function __construct(string $identifier, bool $defaultValue)
        {
            parent::__construct($identifier);

            $this->defaultValue = $defaultValue;
        }
    }

The entry points that will receive the instruction to edit the attribute will need to "build" this command thanks to a factory.
It needs to implement ``Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\EditAttributeCommandFactoryInterface``

.. code-block:: php

    <?php
    namespace Acme\CustomBundle\Application\Attribute\EditAttribute\CommandFactory;

    class EditDefaultValueCommandFactory implements EditAttributeCommandFactoryInterface
    {
        public function supports(array $normalizedCommand): bool
        {
            return array_key_exists('default_value', $normalizedCommand)
                && array_key_exists('identifier', $normalizedCommand);
        }

        public function create(array $normalizedCommand): AbstractEditAttributeCommand
        {
            if (!$this->supports($normalizedCommand)) {
                throw new \RuntimeException('Impossible to create an edit default value property command.');
            }

            $command = new EditDefaultValueCommand(
                $normalizedCommand['identifier'],
                $normalizedCommand['default_value']
            );

            return $command;
        }
    }

This factory needs to be a service with a specific tag:

.. code-block:: yaml

    # src/Acme/CustomBundle/Resources/config/services.yml

    services:
        akeneo_referenceentity.application.factory.edit_default_value_command_factory:
            class: Acme\CustomBundle\Application\Attribute\EditAttribute\CommandFactory\EditDefaultValueCommandFactory
            tags:
                - { name: akeneo_referenceentity.create_attribute_command_factory }

3) Infrastructure Layer
^^^^^^^^^^^^^^^^^^^^^^^

Now that we have our custom Attribute and commands to create/edit it, we'll need to have a way to Hydrate it from the DB for example:

.. code-block:: php

    <?php
    namespace Acme\CustomBundle\Infrastructure\Persistence\Sql\Attribute\Hydrator;

    use Acme\CustomBundle\Domain\Model\Attribute\AttributeDefaultValue;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AbstractAttribute;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeCode;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeIdentifier;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeIsRequired;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeOrder;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeValuePerChannel;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeValuePerLocale;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\TextAttribute;
    use Akeneo\ReferenceEntity\Domain\Model\LabelCollection;
    use Akeneo\ReferenceEntity\Domain\Model\ReferenceEntity\ReferenceEntityIdentifier;
    use Akeneo\ReferenceEntity\Infrastructure\Persistence\Sql\Attribute\Hydrator\AbstractAttributeHydrator;
    use Doctrine\DBAL\Platforms\AbstractPlatform;
    use Doctrine\DBAL\Types\Type;

    class BooleanAttributeHydrator extends AbstractAttributeHydrator
    {
        protected function getExpectedProperties(): array
        {
            return [
                'identifier',
                'reference_entity_identifier',
                'code',
                'labels',
                'attribute_order',
                'is_required',
                'value_per_locale',
                'value_per_channel',
                'attribute_type',
                // ↑ these are common properties for each reference entity attributes
                'default_value'
            ];
        }

        protected function convertAdditionalProperties(AbstractPlatform $platform, array $row): array
        {
            $row['default_value'] = Type::getType(Type::BOOLEAN)->convertToPhpValue(
                $row['additional_properties']['default_value'], $platform
            );

            return $row;
        }

        protected function hydrateAttribute(array $row): AbstractAttribute
        {
            $defaultValue = AttributeDefaultValue::fromBoolean($row['default_value']);

            return TextAttribute::createText(
                AttributeIdentifier::fromString($row['identifier']),
                ReferenceEntityIdentifier::fromString($row['reference_entity_identifier']),
                AttributeCode::fromString($row['code']),
                LabelCollection::fromArray($row['labels']),
                AttributeOrder::fromInteger($row['attribute_order']),
                AttributeIsRequired::fromBoolean($row['is_required']),
                AttributeValuePerChannel::fromBoolean($row['value_per_channel']),
                AttributeValuePerLocale::fromBoolean($row['value_per_locale']),
                $defaultValue
            );
        }

        public function supports(array $result): bool
        {
            return isset($result['attribute_type']) && 'boolean' === $result['attribute_type'];
        }
    }

.. note::

   Note that if you want to validate the ``EditDefaultValueCommand``, you simply have to create a regular Symfony validator.


Frontend Part of The New Attribute Type
---------------------------------------

To be able to create your brand new Boolean attribute on a Reference Entity, we need to add some code in the frontend part.
To do so, you can put all needed code in one single file:

``src/Acme/CustomBundle/Resources/public/reference-entity/attribute/template.tsx``

https://github.com/akeneo/pim-enterprise-dev/pull/5673/files#diff-9f58f66bb7130d11a4234cbcb39917bd

``src/Acme/CustomBundle/Resources/config/requirejs.yml``

.. code-block:: yaml

    config:
        config:
            akeneoreferenceentity/application/configuration/attribute:
                boolean:
                    icon: bundles/pimui/images/attribute/icon-boolean.svg
                    denormalize: '@acmecustom/reference-entity/attribute/boolean.tsx'
                    reducer: '@acmecustom/reference-entity/attribute/boolean.tsx'
                    view: '@acmecustom/reference-entity/attribute/boolean.tsx'


Enrich Records with your new Attribute
--------------------------------------

- Domain Record (Data of the Value)
- Application Record (Edit)
- Infra Record (Validation, Hydrator)
