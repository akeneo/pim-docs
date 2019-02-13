Create a new Reference Entity Attribute type
============================================

.. note::

   Reference Entities feature is only available for the **Enterprise Edition**.

This cookbook will present how to create your own Attribute Type for Reference Entities.
Currently, there are 6 types of Attribute for Reference Entities:

- Image
- Text
- Reference entity single link
- Reference entity multiple links
- Single Option
- Multiple Options

Requirements
------------

During this chapter, we assume that you already created a new bundle to add your custom Reference Entity Attribute. Let's assume its namespace is ``Acme\\CustomBundle``.

Create the Attribute
--------------------

For this tutorial, we will create a **custom boolean attribute** type for reference entity.
In addition to its common properties (code, label, value per locale...), we will add a custom property, which will be its "default value" for records.

.. note::

   **A small word on our architecture:**

   For the reference entities, we followed the Hexagonal architecture. So we split our classes in 3 different layers: Domain, Application & Infrastructure.

   - First we'll create **Domain** classes (the new custom ``BooleanAttribute`` itself and its ``Value Object``)
   - Then **Application** classes (only Commands from CQRS principle (https://martinfowler.com/bliki/CQRS.html) and regular updaters)
   - And to finish **Infrastructure** classes (an Hydrator to hydrate our ``BooleanAttribute`` coming from SQL)

   It's not mandatory to respect this architecture in your custom project, but for the sake of this example, we'll respect it.


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
        private $defaultValue; // This is our custom property for this attribute.

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

        public function setDefaultValue(AttributeDefaultValue $defaultValue): void
        {
            $this->defaultValue = $defaultValue;
        }

        /**
         * {@inheritdoc}
         */
        protected function getType(): string
        {
            return 'boolean';
        }
    }


Now that we have our custom attribute class, we need to create its Value Object class for the property "DefaultValue":

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

First, let's create a factory to create our brand new ``BooleanAttribute``:

.. code-block:: php

    <?php
    namespace Acme\CustomBundle\Application\Attribute\CreateAttribute\AttributeFactory;

    use Acme\CustomBundle\Application\Attribute\CreateAttribute\CreateBooleanAttributeCommand;
    use Acme\CustomBundle\Domain\Model\Attribute\AttributeDefaultValue;
    use Acme\CustomBundle\Domain\Model\Attribute\BooleanAttribute;
    use Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute\AbstractCreateAttributeCommand;
    use Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute\AttributeFactory\AttributeFactoryInterface;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AbstractAttribute;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeCode;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeIdentifier;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeIsRequired;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeOrder;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeValuePerChannel;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeValuePerLocale;
    use Akeneo\ReferenceEntity\Domain\Model\LabelCollection;
    use Akeneo\ReferenceEntity\Domain\Model\ReferenceEntity\ReferenceEntityIdentifier;

    class BooleanAttributeFactory implements AttributeFactoryInterface
    {
        public function supports(AbstractCreateAttributeCommand $command): bool
        {
            return $command instanceof CreateBooleanAttributeCommand;
        }

        public function create(
            AbstractCreateAttributeCommand $command,
            AttributeIdentifier $identifier,
            AttributeOrder $order
        ): AbstractAttribute {
            if (!$this->supports($command)) {
                throw new \RuntimeException(
                    sprintf(
                        'Expected command of type "%s", "%s" given',
                        CreateBooleanAttributeCommand::class,
                        get_class($command)
                    )
                );
            }

            return BooleanAttribute::createBoolean(
                $identifier,
                ReferenceEntityIdentifier::fromString($command->referenceEntityIdentifier),
                AttributeCode::fromString($command->code),
                LabelCollection::fromArray($command->labels),
                $order,
                AttributeIsRequired::fromBoolean($command->isRequired),
                AttributeValuePerChannel::fromBoolean($command->valuePerChannel),
                AttributeValuePerLocale::fromBoolean($command->valuePerLocale),
                AttributeDefaultValue::fromBoolean($command->defaultValue)
            );
        }
    }


The Domain classes were quite simple objects. Now we need to add some logic
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

To build this creation command, we need a factory:

.. code-block:: php

    <?php

    namespace Acme\CustomBundle\Application\Attribute\CreateAttribute\CommandFactory;

    use Acme\CustomBundle\Application\Attribute\CreateAttribute\CreateBooleanAttributeCommand;
    use Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute\AbstractCreateAttributeCommand;
    use Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute\CommandFactory\AbstractCreateAttributeCommandFactory;

    class CreateBooleanAttributeCommandFactory extends AbstractCreateAttributeCommandFactory
    {
        public function supports(array $normalizedCommand): bool
        {
            return isset($normalizedCommand['type']) && 'boolean' === $normalizedCommand['type'];
        }

        public function create(array $normalizedCommand): AbstractCreateAttributeCommand
        {
            $this->checkCommonProperties($normalizedCommand);

            $command = new CreateBooleanAttributeCommand(
                $normalizedCommand['reference_entity_identifier'],
                $normalizedCommand['code'],
                $normalizedCommand['labels'] ?? [],
                $normalizedCommand['is_required'] ?? false,
                $normalizedCommand['value_per_channel'],
                $normalizedCommand['value_per_locale'],
                $normalizedCommand['default_value'] ?? false
            );

            return $command;
        }
    }

And we also need to register it with a specific tag:

.. code-block:: yaml

    acme.application.factory.create_boolean_attribute_command_factory:
        class: Acme\CustomBundle\Application\Attribute\CreateAttribute\CommandFactory\CreateBooleanAttributeCommandFactory
        tags:
            - { name: akeneo_referenceentity.create_attribute_command_factory }


And its declaration:

.. code-block:: yaml

    acme.application.factory.boolean_attribute_factory:
        class: Acme\CustomBundle\Application\Attribute\CreateAttribute\AttributeFactory\BooleanAttributeFactory
        tags:
            - { name: akeneo_referenceentity.attribute_factory }

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
        acme.application.factory.edit_default_value_command_factory:
            class: Acme\CustomBundle\Application\Attribute\EditAttribute\CommandFactory\EditDefaultValueCommandFactory
            tags:
                - { name: akeneo_referenceentity.edit_attribute_command_factory, priority: 120 }

Now that we have our command, we need a dedicated updater to handle the change on the actual attribute:

.. code-block:: php

    <?php

    namespace Acme\CustomBundle\Application\Attribute\EditAttribute\AttributeUpdater;

    use Acme\CustomBundle\Application\Attribute\EditAttribute\CommandFactory\EditDefaultValueCommand;
    use Acme\CustomBundle\Domain\Model\Attribute\AttributeDefaultValue;
    use Acme\CustomBundle\Domain\Model\Attribute\BooleanAttribute;
    use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\AttributeUpdater\AttributeUpdaterInterface;
    use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\AbstractEditAttributeCommand;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AbstractAttribute;

    class DefaultValueUpdater implements AttributeUpdaterInterface
    {
        public function supports(AbstractAttribute $attribute, AbstractEditAttributeCommand $command): bool
        {
            return $command instanceof EditDefaultValueCommand && $attribute instanceof BooleanAttribute;
        }

        public function __invoke(AbstractAttribute $attribute, AbstractEditAttributeCommand $command): AbstractAttribute
        {
            if (!$command instanceof EditDefaultValueCommand) {
                throw new \RuntimeException(
                    sprintf(
                        'Expected command of type "%s", "%s" given',
                        EditDefaultValueCommand::class,
                        get_class($command)
                    )
                );
            }

            $attribute->setDefaultValue(AttributeDefaultValue::fromBoolean($command->defaultValue));

            return $attribute;
        }
    }

This updater needs to be registered to be retrieved by a registry:

.. code-block:: yaml

    acme.application.edit_attribute.attribute_updater.default_value:
        class: Acme\CustomBundle\Application\Attribute\EditAttribute\AttributeUpdater\DefaultValueUpdater
        tags:
            - { name: akeneo_referenceentity.attribute_updater, priority: 120 }


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

And to register it:

.. code-block:: yaml

    acme.infrastructure.persistence.hydrator.attribute.text_attribute_hydrator:
        class: Acme\CustomBundle\Infrastructure\Persistence\Sql\Attribute\Hydrator\BooleanAttributeHydrator
        arguments:
            - '@database_connection'
        tags:
            - { name: akeneo_referenceentity.attribute_hydrator }

.. note::

   Note that if you want to validate the ``EditDefaultValueCommand``, you simply have to create a regular Symfony validator.


Frontend Part of The New Attribute Type
---------------------------------------

To be able to create your brand new Boolean attribute on a Reference Entity, we need to add some code in the frontend part.

To do so, you can put all needed code in one single file but you can (and are encouraged) to split it into multiple
files if needed.

To keep this example simple, we will create everything in this file :

``src/Acme/CustomBundle/Resources/public/reference-entity/attribute/boolean.tsx``

If you create a new attribute type, Akeneo will need three things to manage it in the frontend:
 - A model: a representation of your attribute, those properties and overall behaviour
 - A reducer: to be able to know how to modify those custom properties and react to the user intentions (see https://redux.js.org/)
 - A view: as a React component to be able to render a user interface and dispatch events to the application

1) Model
^^^^^^^^

The model of your custom attribute will contain the common properties of an attribute (code, labels, scope, etc) but also those custom properties
and behaviours. To interface it with the rest of the PIM, your attribute needs to implement the Attribute interface and provide a denormalizer.

This is the purpose of this section: provide a denormalizer capable of creating your custom attribute implementing Attribute interface.

.. code-block:: javascript

    /**
     * ## Import section
     *
     * This is where sits your dependencies to external modules using the standard import method (see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/import)
     * The paths are absolute and the root is the web/bundles folder (at the root of your PIM project)
     */
    import Identifier, {createIdentifier} from 'akeneoreferenceentity/domain/model/attribute/identifier';
    import ReferenceEntityIdentifier, {
      createIdentifier as createReferenceEntityIdentifier,
    } from 'akeneoreferenceentity/domain/model/reference-entity/identifier';
    import LabelCollection, {createLabelCollection} from 'akeneoreferenceentity/domain/model/label-collection';
    import AttributeCode, {createCode} from 'akeneoreferenceentity/domain/model/attribute/code';
    import {
      NormalizedAttribute,
      Attribute,
      ConcreteAttribute,
    } from 'akeneoreferenceentity/domain/model/attribute/attribute';

    /**
     * This type is an aggregate of all the custom properties. Here we only have one so it could seems useless but
     * here is an example with multiple properties:
     *
     *     export type TextAdditionalProperty = MaxLength | IsTextarea | IsRichTextEditor | ValidationRule | RegularExpression;
     *
     * In the example above, a additional property of a text attribute could be a Max length, is textarea, is rich text editor, ...
     */
    export type BooleanAdditionalProperty = DefaultValue;

    /**
     * Same for the non normalized form
     */
    export type NormalizedBooleanAdditionalProperty = NormalizedDefaultValue;

    /**
     * This interface will represent your normalized attribute (usually coming from the backend but also used in the reducer)
     */
    export interface NormalizedBooleanAttribute extends NormalizedAttribute {
      type: 'boolean';
      default_value: NormalizedDefaultValue;
    }

    /**
     * Here we define the interface for our concrete class (our model) extending the base attribute interface
     */
    export interface BooleanAttribute extends Attribute {
      defaultValue: DefaultValue;
      normalize(): NormalizedBooleanAttribute;
    }

    /**
     * Here we are starting to implement our custom attribute class.
     * Note that most of the code is due to the custom property (defaultValue). If you don't need to add a
     * custom property to your attribute, the code can be stripped to it's minimal
     */
    export class ConcreteBooleanAttribute extends ConcreteAttribute implements BooleanAttribute {
      /**
       * Here, our constructor is private to be sure that our model will be created through a named constructor
       */
      private constructor(
        identifier: Identifier,
        referenceEntityIdentifier: ReferenceEntityIdentifier,
        code: AttributeCode,
        labelCollection: LabelCollection,
        valuePerLocale: boolean,
        valuePerChannel: boolean,
        order: number,
        is_required: boolean,
        readonly defaultValue: DefaultValue
      ) {
        super(
          identifier,
          referenceEntityIdentifier,
          code,
          labelCollection,
          'boolean',
          valuePerLocale,
          valuePerChannel,
          order,
          is_required
        );

        /**
         * Always ensure that your object is well formed from it's constructor to avoid crash of the application
         */
        if (!(defaultValue instanceof DefaultValue)) {
          throw new Error('Attribute expect a DefaultValue as defaultValue');
        }

        /**
         * This will ensure that your model is not modified after it's creation (see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/freeze)
         */
        Object.freeze(this);
      }

      /**
       * Here, we denormalize our attribute
       */
      public static createFromNormalized(normalizedBooleanAttribute: NormalizedBooleanAttribute) {
        return new ConcreteBooleanAttribute(
          createIdentifier(normalizedBooleanAttribute.identifier),
          createReferenceEntityIdentifier(normalizedBooleanAttribute.reference_entity_identifier),
          createCode(normalizedBooleanAttribute.code),
          createLabelCollection(normalizedBooleanAttribute.labels),
          normalizedBooleanAttribute.value_per_locale,
          normalizedBooleanAttribute.value_per_channel,
          normalizedBooleanAttribute.order,
          normalizedBooleanAttribute.is_required,
          new DefaultValue(normalizedBooleanAttribute.default_value)
        );
      }

      /**
       * The only method to implement here: the normalize method. Here you need to provide a serializable object (see https://developer.mozilla.org/en-US/docs/Glossary/Serialization)
       */
      public normalize(): NormalizedBooleanAttribute {
        return {
          ...super.normalize(),
          type: 'boolean',
          default_value: this.defaultValue.normalize()
        };
      }
    }

    /**
     * This part is not mandatory but we advise you to create value object to represent your custom properties (see https://en.wikipedia.org/wiki/Value_object)
     */
    type NormalizedDefaultValue = boolean;
    class DefaultValue {
      public constructor(readonly defaultValue: boolean) {}

      public normalize() {
        return this.defaultValue;
      }

      public stringValue(): string {
        return this.defaultValue ? '1' : '0';
      }
    }

    /**
     * The only required part of the file: exporting a denormalize method returning a custom attribute implementing Attribute interface
     */
    export const denormalize = ConcreteBooleanAttribute.createFromNormalized;

2) Reducer
^^^^^^^^^^

Now that we have our attribute model in the frontend, we need to define our Reducer to know how to modify those custom properties and react to the user intentions.

.. code-block:: javascript

    /**
    * Our custom attribute reducer needs to receive as input the normalized custom attribute, the code of the additional property and the value of the additional property.
    * It returns the normalized custom attribute with the values of the additional properties updated.
    */
    const booleanAttributeReducer = (
      normalizedAttribute: NormalizedBooleanAttribute,
      propertyCode: string,
      propertyValue: NormalizedBooleanAdditionalProperty
    ): NormalizedBooleanAttribute => {
      switch (propertyCode) {
        case 'default_value':
          const default_value = propertyValue as NormalizedDefaultValue;
          return {...normalizedAttribute, default_value};

        default:
          break;
      }

      return normalizedAttribute;
    };

    /**
     * The only required part of the file: exporting the custom attribute reducer.
     */
    export const reducer = booleanAttributeReducer;

3) View
^^^^^^^

The last part we need to do, it's to create the React component to be able to render a user interface and dispatch events to the application (https://reactjs.org/docs/react-component.html).

.. code-block:: javascript

    import * as React from 'react';
    import __ from 'akeneoreferenceentity/tools/translator';
    import {getErrorsView} from 'akeneoreferenceentity/application/component/app/validation-error';
    import ValidationError from "akeneoreferenceentity/domain/model/validation-error";
    import Key from "akeneoreferenceentity/tools/key";

    /**
    * Here we define the React Component as a function with the following props :
    *    - the custom attribute
    *    - the callback function to update the additional property
    *    - the callback for the submit
    *    - the validation errors
    *    - the attribute rights
    *
    * It returns the JSX View to display the additional properties of your custom attribute.
    */
    const BooleanAttributeView = ({
       attribute,
       onAdditionalPropertyUpdated,
       onSubmit,
       errors,
       rights,
     }: {
      attribute: BooleanAttribute;
      onAdditionalPropertyUpdated: (property: string, value: BooleanAdditionalProperty) => void;
      onSubmit: () => void;,
      errors: ValidationError[];
      rights: {
        attribute: {
          create: boolean;
          edit: boolean;
          delete: boolean;
        };
      }
    }) => {
      const value = attribute.defaultValue.normalize();

      // We need to have single quotes around the React.Fragment tag for displaying well the JSX in the documentation but you have to remove it in your code.
      return (
        '<React.Fragment>
          <div className="AknFieldContainer AknFieldContainer--packed" data-code="defaultValue">
            <div className="AknFieldContainer-header">
              <label className="AknFieldContainer-label" htmlFor="pim_reference_entity.attribute.edit.input.default_value">
                <div
                  className={`AknCheckbox AknCheckbox--inline ${value ? 'AknCheckbox--checked' : ''} ${
                    !rights.attribute.edit ? 'AknCheckbox--disabled' : ''
                    }`}
                  data-checked={value ? 'true' : 'false'}
                  tabIndex={!rights.attribute.edit ? -1 : 0}
                  id="pim_reference_entity.attribute.edit.input.default_value"
                  role="checkbox"
                  aria-checked={value ? 'true' : 'false'}
                  onKeyPress={(event: React.KeyboardEvent<HTMLSpanElement>) => {
                    if ([' '].includes(event.key) && rights.attribute.edit) {
                      onAdditionalPropertyUpdated('default_value', new DefaultValue(!value));
                    }
                    if (Key.Enter === event.key) onSubmit();
                    event.preventDefault();
                  }}
                  onClick={() => {
                    if (rights.attribute.edit) onAdditionalPropertyUpdated('default_value', new DefaultValue(!value));
                  }}
                >
                  <svg width={16} height={16}>
                    <path
                      className=""
                      fill="none"
                      stroke="#FFFFFF"
                      strokeWidth={1}
                      strokeLinejoin="round"
                      strokeMiterlimit={10}
                      d="M1.7 8l4.1 4 8-8"
                    />
                  </svg>
                </div>
                {__('acme_custom.attribute.edit.input.default_value')}
              </label>
            </div>
            {getErrorsView(errors, 'defaultValue')}
          </div>
        </React.Fragment>'
      );
    };

    /**
     * The only required part of the file: exporting the custom attribute view.
     */
    export const view = BooleanAttributeView;

4) Register our custom attribute
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

To be able to have everything working, we need to register our custom attribute in the ``src/Acme/CustomBundle/Resources/config/requirejs.yml`` :

.. code-block:: yaml

    config:
        config:
            akeneoreferenceentity/application/configuration/attribute:
                boolean:
                    icon: bundles/pimui/images/attribute/icon-boolean.svg
                    denormalize: '@acmecustom/reference-entity/attribute/boolean.tsx'
                    reducer: '@acmecustom/reference-entity/attribute/boolean.tsx'
                    view: '@acmecustom/reference-entity/attribute/boolean.tsx'
