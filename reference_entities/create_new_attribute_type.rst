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

For this tutorial, we will create a **custom simple metric attribute** type for reference entity.
In addition to its common properties (code, label, value per locale...), we will add a custom property, which will be its "unit".

.. note::

   **A small word on our architecture:**

   For the reference entities, we followed the Hexagonal architecture. So we split our classes in 3 different layers: Domain, Application & Infrastructure.

   - First we'll create **Domain** classes (the new custom ``SimpleMetricAttribute`` itself and its ``Value Object``)
   - Then **Application** classes (only Commands from CQRS principle (https://martinfowler.com/bliki/CQRS.html) and regular updaters)
   - And to finish **Infrastructure** classes (an Hydrator to hydrate our ``SimpleMetricAttribute`` coming from SQL)

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

    class SimpleMetricAttribute extends AbstractAttribute
    {
        /** @var AttributeMetricUnit */
        private $unit;

        protected function __construct(
            AttributeIdentifier $identifier,
            ReferenceEntityIdentifier $referenceEntityIdentifier,
            AttributeCode $code,
            LabelCollection $labelCollection,
            AttributeOrder $order,
            AttributeIsRequired $isRequired,
            AttributeValuePerChannel $valuePerChannel,
            AttributeValuePerLocale $valuePerLocale,
            AttributeMetricUnit $unit
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

            $this->unit = $unit;
        }

        public static function createSimpleMetric(
            AttributeIdentifier $identifier,
            ReferenceEntityIdentifier $referenceEntityIdentifier,
            AttributeCode $code,
            LabelCollection $labelCollection,
            AttributeOrder $order,
            AttributeIsRequired $isRequired,
            AttributeValuePerChannel $valuePerChannel,
            AttributeValuePerLocale $valuePerLocale,
            AttributeMetricUnit $unit
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
                $unit
            );
        }

        public function setUnit(AttributeMetricUnit $unit): void
        {
            $this->unit = $unit;
        }

        /**
         * {@inheritdoc}
         */
        protected function getType(): string
        {
            return 'simple_metric';
        }

        public function normalize(): array
        {
            return array_merge(
                parent::normalize(),
                [
                    'unit' => $this->unit->normalize(),
                ]
            );
        }
    }



Now that we have our custom attribute class, we need to create its Value Object class for the property "MetricUnit":

.. code-block:: php

    <?php
    namespace Acme\CustomBundle\Domain\Model\Attribute;

    class AttributeMetricUnit
    {
        /** @var string */
        private $metricUnit;

        private function __construct(string $metricUnit)
        {
            $this->metricUnit = $metricUnit;
        }

        public static function fromString(string $metricUnit): self
        {
            return new self($metricUnit);
        }

        public function normalize(): string
        {
            return $this->metricUnit;
        }
    }

2) Application Layer
^^^^^^^^^^^^^^^^^^^^

The Domain classes were quite simple objects. Now we need to add some logic
Now that we have our Attribute class, we need to create classes to handle its creation and edition.

We'll need first to add the "Creation command", it needs to extend ``\Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute\AbstractCreateAttributeCommand``.

.. code-block:: php

    <?php
    namespace Acme\CustomBundle\Application\Attribute\CreateAttribute;

    use Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute\AbstractCreateAttributeCommand;

    class CreateSimpleMetricAttributeCommand extends AbstractCreateAttributeCommand
    {
        /** @var string */
        public $unit;

        public function __construct(
            string $referenceEntityIdentifier,
            string $code,
            array $labels,
            bool $isRequired,
            bool $valuePerChannel,
            bool $valuePerLocale,
            string $unit
        ) {
            parent::__construct(
                $referenceEntityIdentifier,
                $code,
                $labels,
                $isRequired,
                $valuePerChannel,
                $valuePerLocale
            );

            $this->unit = $unit;
        }
    }

To build this creation command, we need a factory:

.. code-block:: php

    <?php

    namespace Acme\CustomBundle\Application\Attribute\CreateAttribute\CommandFactory;

    use Acme\CustomBundle\Application\Attribute\CreateAttribute\CreateSimpleMetricAttributeCommand;
    use Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute\AbstractCreateAttributeCommand;
    use Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute\CommandFactory\AbstractCreateAttributeCommandFactory;

    class CreateSimpleMetricAttributeCommandFactory extends AbstractCreateAttributeCommandFactory
    {
        public function supports(array $normalizedCommand): bool
        {
            return isset($normalizedCommand['type']) && 'simple_metric' === $normalizedCommand['type'];
        }

        public function create(array $normalizedCommand): AbstractCreateAttributeCommand
        {
            $this->checkCommonProperties($normalizedCommand);

            $command = new CreateSimpleMetricAttributeCommand(
                $normalizedCommand['reference_entity_identifier'],
                $normalizedCommand['code'],
                $normalizedCommand['labels'] ?? [],
                $normalizedCommand['is_required'] ?? false,
                $normalizedCommand['value_per_channel'],
                $normalizedCommand['value_per_locale'],
                $normalizedCommand['unit'] ?? ''
            );

            return $command;
        }
    }

And we also need to register it with a specific tag:

.. code-block:: yaml

    acme.application.factory.create_simple_metric_attribute_command_factory:
        class: Acme\CustomBundle\Application\Attribute\CreateAttribute\CommandFactory\CreateSimpleMetricAttributeCommandFactory
        tags:
            - { name: akeneo_referenceentity.create_attribute_command_factory }

Now that we have our command created, we need a factory to create our brand new ``SimpleMetricAttribute``:

.. code-block:: php

    <?php
    namespace Acme\CustomBundle\Application\Attribute\CreateAttribute\AttributeFactory;

    use Acme\CustomBundle\Application\Attribute\CreateAttribute\CreateSimpleMetricAttributeCommand;
    use Acme\CustomBundle\Domain\Model\Attribute\AttributeMetricUnit;
    use Acme\CustomBundle\Domain\Model\Attribute\SimpleMetricAttribute;
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

    class SimpleMetricAttributeFactory implements AttributeFactoryInterface
    {
        public function supports(AbstractCreateAttributeCommand $command): bool
        {
            return $command instanceof CreateSimpleMetricAttributeCommand;
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
                        CreateSimpleMetricAttributeCommand::class,
                        get_class($command)
                    )
                );
            }

            return SimpleMetricAttribute::createSimpleMetric(
                $identifier,
                ReferenceEntityIdentifier::fromString($command->referenceEntityIdentifier),
                AttributeCode::fromString($command->code),
                LabelCollection::fromArray($command->labels),
                $order,
                AttributeIsRequired::fromBoolean($command->isRequired),
                AttributeValuePerChannel::fromBoolean($command->valuePerChannel),
                AttributeValuePerLocale::fromBoolean($command->valuePerLocale),
                AttributeMetricUnit::fromString($command->unit)
            );
        }
    }

And its declaration:

.. code-block:: yaml

    acme.application.factory.simple_metric_attribute_factory:
        class: Acme\CustomBundle\Application\Attribute\CreateAttribute\AttributeFactory\SimpleMetricAttributeFactory
        tags:
            - { name: akeneo_referenceentity.attribute_factory }

For the edition of this attribute, we'll need to create a command to edit the property of our attribute (MetricUnit):

.. code-block:: php

    <?php
    namespace Acme\CustomBundle\Application\Attribute\EditAttribute\CommandFactory;

    use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\AbstractEditAttributeCommand;

    class EditMetricUnitCommand extends AbstractEditAttributeCommand
    {
        /** @var string */
        public $metricUnit;

        public function __construct(string $identifier, string $metricUnit)
        {
            parent::__construct($identifier);

            $this->metricUnit = $metricUnit;
        }
    }

The entry points that will receive the instruction to edit the attribute will need to "build" this command thanks to a factory.
It needs to implement ``Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\EditAttributeCommandFactoryInterface``

.. code-block:: php

    <?php
    namespace Acme\CustomBundle\Application\Attribute\EditAttribute\CommandFactory;

    use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\AbstractEditAttributeCommand;
    use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\EditAttributeCommandFactoryInterface;

    class EditMetricUnitCommandFactory implements EditAttributeCommandFactoryInterface
    {
        public function supports(array $normalizedCommand): bool
        {
            return array_key_exists('unit', $normalizedCommand)
                && array_key_exists('identifier', $normalizedCommand);
        }

        public function create(array $normalizedCommand): AbstractEditAttributeCommand
        {
            if (!$this->supports($normalizedCommand)) {
                throw new \RuntimeException('Impossible to create an edit unit property command.');
            }

            $command = new EditMetricUnitCommand(
                $normalizedCommand['identifier'],
                $normalizedCommand['unit']
            );

            return $command;
        }
    }

This factory needs to be a service with a specific tag:

.. code-block:: yaml

    # src/Acme/CustomBundle/Resources/config/services.yml

    services:
         acme.application.factory.edit_metric_unit_command_factory:
            class: Acme\CustomBundle\Application\Attribute\EditAttribute\CommandFactory\EditMetricUnitCommandFactory
            tags:
                - { name: akeneo_referenceentity.edit_attribute_command_factory, priority: 120 }

Now that we have our command, we need a dedicated updater to handle the change on the actual attribute:

.. code-block:: php

    <?php

    namespace Acme\CustomBundle\Application\Attribute\EditAttribute\AttributeUpdater;

    use Acme\CustomBundle\Application\Attribute\EditAttribute\CommandFactory\EditMetricUnitCommand;
    use Acme\CustomBundle\Domain\Model\Attribute\AttributeMetricUnit;
    use Acme\CustomBundle\Domain\Model\Attribute\SimpleMetricAttribute;
    use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\AttributeUpdater\AttributeUpdaterInterface;
    use Akeneo\ReferenceEntity\Application\Attribute\EditAttribute\CommandFactory\AbstractEditAttributeCommand;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AbstractAttribute;

    class MetricUnitUpdater implements AttributeUpdaterInterface
    {
        public function supports(AbstractAttribute $attribute, AbstractEditAttributeCommand $command): bool
        {
            return $command instanceof EditMetricUnitCommand && $attribute instanceof SimpleMetricAttribute;
        }

        public function __invoke(AbstractAttribute $attribute, AbstractEditAttributeCommand $command): AbstractAttribute
        {
            if (!$command instanceof EditMetricUnitCommand) {
                throw new \RuntimeException(
                    sprintf(
                        'Expected command of type "%s", "%s" given',
                        EditMetricUnitCommand::class,
                        get_class($command)
                    )
                );
            }

            $attribute->setUnit(AttributeMetricUnit::fromString($command->metricUnit));

            return $attribute;
        }
    }

This updater needs to be registered to be retrieved by a registry:

.. code-block:: yaml

    # src/Acme/CustomBundle/Resources/config/services.yml

    services:
        acme.application.edit_attribute.attribute_updater.metric_unit:
            class: Acme\CustomBundle\Application\Attribute\EditAttribute\AttributeUpdater\MetricUnitUpdater
            tags:
                - { name: akeneo_referenceentity.attribute_updater, priority: 120 }


3) Infrastructure Layer
^^^^^^^^^^^^^^^^^^^^^^^

Now that we have our custom Attribute and commands to create/edit it, we'll need to have a way to Hydrate it from the DB for example:

.. code-block:: php

    <?php
    namespace Acme\CustomBundle\Infrastructure\Persistence\Sql\Attribute\Hydrator;

    use Acme\CustomBundle\Domain\Model\Attribute\AttributeMetricUnit;
    use Acme\CustomBundle\Domain\Model\Attribute\SimpleMetricAttribute;
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

    class SimpleMetricAttributeHydrator extends AbstractAttributeHydrator
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
                'unit'
            ];
        }

        protected function convertAdditionalProperties(AbstractPlatform $platform, array $row): array
        {
            $row['unit'] = Type::getType(Type::STRING)->convertToPhpValue(
                $row['additional_properties']['unit'], $platform
            );

            return $row;
        }

        protected function hydrateAttribute(array $row): AbstractAttribute
        {
            $metricUnit = AttributeMetricUnit::fromString($row['unit']);

            return SimpleMetricAttribute::createSimpleMetric(
                AttributeIdentifier::fromString($row['identifier']),
                ReferenceEntityIdentifier::fromString($row['reference_entity_identifier']),
                AttributeCode::fromString($row['code']),
                LabelCollection::fromArray($row['labels']),
                AttributeOrder::fromInteger($row['attribute_order']),
                AttributeIsRequired::fromBoolean($row['is_required']),
                AttributeValuePerChannel::fromBoolean($row['value_per_channel']),
                AttributeValuePerLocale::fromBoolean($row['value_per_locale']),
                $metricUnit
            );
        }

        public function supports(array $result): bool
        {
            return isset($result['attribute_type']) && 'simple_metric' === $result['attribute_type'];
        }
    }


And to register it:

.. code-block:: yaml

    # src/Acme/CustomBundle/Resources/config/services.yml

    services:
        acme.infrastructure.persistence.hydrator.attribute.simple_metric_attribute_hydrator:
            class: Acme\CustomBundle\Infrastructure\Persistence\Sql\Attribute\Hydrator\SimpleMetricAttributeHydrator
            arguments:
                - '@database_connection'
            tags:
                - { name: akeneo_referenceentity.attribute_hydrator }

.. note::

   Note that if you want to validate the ``EditDefaultValueCommand``, you simply have to create a regular Symfony validator.


Frontend Part of The New Attribute Type
---------------------------------------

To be able to create your brand new Simple Metric attribute on a Reference Entity, we need to add some code in the frontend part.

To do so, you can put all needed code in one single file but you can (and are encouraged) to split it into multiple
files if needed.

To keep this example simple, we will create everything in this file :

``src/Acme/CustomBundle/Resources/public/reference-entity/attribute/simple_metric.tsx``

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
    export type SimpleMetricAdditionalProperty = MetricUnit;

    /**
     * Same for the non normalized form
     */
    export type NormalizedSimpleMetricAdditionalProperty = NormalizedMetricUnit;

    /**
     * This interface will represent your normalized attribute (usually coming from the backend but also used in the reducer)
     */
    export interface NormalizedSimpleMetricAttribute extends NormalizedAttribute {
      type: 'simple_metric';
      unit: NormalizedMetricUnit;
    }

    /**
     * Here we define the interface for our concrete class (our model) extending the base attribute interface
     */
    export interface SimpleMetricAttribute extends Attribute {
      unit: MetricUnit;
      normalize(): NormalizedSimpleMetricAttribute;
    }

    /**
     * Here we are starting to implement our custom attribute class.
     * Note that most of the code is due to the custom property (defaultValue). If you don't need to add a
     * custom property to your attribute, the code can be stripped to it's minimal
     */
    export class ConcreteSimpleMetricAttribute extends ConcreteAttribute implements SimpleMetricAttribute {
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
        readonly unit: MetricUnit
      ) {
        super(
          identifier,
          referenceEntityIdentifier,
          code,
          labelCollection,
          'simple_metric',
          valuePerLocale,
          valuePerChannel,
          order,
          is_required
        );

        /**
         * Always ensure that your object is well formed from it's constructor to avoid crash of the application
         */
        if (!(unit instanceof MetricUnit)) {
          throw new Error('Attribute expect a MetricUnit as unit');
        }

        /**
         * This will ensure that your model is not modified after it's creation (see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/freeze)
         */
        Object.freeze(this);
      }

      /**
       * Here, we denormalize our attribute
       */
      public static createFromNormalized(normalizedSimpleMetricAttribute: NormalizedSimpleMetricAttribute) {
        return new ConcreteSimpleMetricAttribute(
          createIdentifier(normalizedSimpleMetricAttribute.identifier),
          createReferenceEntityIdentifier(normalizedSimpleMetricAttribute.reference_entity_identifier),
          createCode(normalizedSimpleMetricAttribute.code),
          createLabelCollection(normalizedSimpleMetricAttribute.labels),
          normalizedSimpleMetricAttribute.value_per_locale,
          normalizedSimpleMetricAttribute.value_per_channel,
          normalizedSimpleMetricAttribute.order,
          normalizedSimpleMetricAttribute.is_required,
          new MetricUnit(normalizedSimpleMetricAttribute.unit)
        );
      }

      /**
       * The only method to implement here: the normalize method. Here you need to provide a serializable object (see https://developer.mozilla.org/en-US/docs/Glossary/Serialization)
       */
      public normalize(): NormalizedSimpleMetricAttribute {
        return {
          ...super.normalize(),
          type: 'simple_metric',
          unit: this.unit.normalize()
        };
      }
    }

    /**
     * This part is not mandatory but we advise you to create value object to represent your custom properties (see https://en.wikipedia.org/wiki/Value_object)
     */
    type NormalizedMetricUnit = string;
    class MetricUnit {
      public constructor(readonly unit: string) {}

      public normalize() {
        return this.unit;
      }
    }


    /**
     * The only required part of the file: exporting a denormalize method returning a custom attribute implementing Attribute interface
     */
    export const denormalize = ConcreteSimpleMetricAttribute.createFromNormalized;

2) Reducer
^^^^^^^^^^

Now that we have our attribute model in the frontend, we need to define our Reducer to know how to modify those custom properties and react to the user intentions.

.. code-block:: javascript

    /**
     * Our custom attribute reducer needs to receive as input the normalized custom attribute, the code of the additional property and the value of the additional property.
     * It returns the normalized custom attribute with the values of the additional properties updated.
     */
    const simpleMetricAttributeReducer = (
      normalizedAttribute: NormalizedSimpleMetricAttribute,
      propertyCode: string,
      propertyValue: NormalizedSimpleMetricAdditionalProperty
    ): NormalizedSimpleMetricAttribute => {
      switch (propertyCode) {
        case 'unit':
          const unit = propertyValue as NormalizedMetricUnit;
          return {...normalizedAttribute, unit};

        default:
          break;
      }

      return normalizedAttribute;
    };

    /**
     * The only required part of the file: exporting the custom attribute reducer.
     */
    export const reducer = simpleMetricAttributeReducer;

3) View
^^^^^^^

The last part we need to do, it's to create the React component to be able to render a user interface and dispatch events to the application (https://reactjs.org/docs/react-component.html).

.. code-block:: javascript

    import * as React from 'react';
    import __ from 'akeneoreferenceentity/tools/translator';
    import {getErrorsView} from 'akeneoreferenceentity/application/component/app/validation-error';
    import ValidationError from 'akeneoreferenceentity/domain/model/validation-error';
    import Key from 'akeneoreferenceentity/tools/key';

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
    const SimpleMetricAttributeView = ({
       attribute,
       onAdditionalPropertyUpdated,
       onSubmit,
       errors,
       rights,
     }: {
      attribute: SimpleMetricAttribute;
      onAdditionalPropertyUpdated: (property: string, value: SimpleMetricAdditionalProperty) => void;
      onSubmit: () => void;
      errors: ValidationError[];
      rights: {
        attribute: {
          create: boolean;
          edit: boolean;
          delete: boolean;
        };
      }
    }) => {
      const inputTextClassName = `AknTextField AknTextField--light ${
        !rights.attribute.edit ? 'AknTextField--disabled' : ''
        }`;

      return (
        <React.Fragment>
          <div className="AknFieldContainer" data-code="unit">
            <div className="AknFieldContainer-header AknFieldContainer-header--light">
              <label className="AknFieldContainer-label" htmlFor="pim_reference_entity.attribute.edit.input.unit">
                {__('pim_reference_entity.attribute.edit.input.unit')}
              </label>
            </div>
            <div className="AknFieldContainer-inputContainer">
              <input
                type="text"
                autoComplete="off"
                className={inputTextClassName}
                id="pim_reference_entity.attribute.edit.input.unit"
                name="unit"
                readOnly={!rights.attribute.edit}
                value={attribute.unit.normalize()}
                onKeyPress={(event: React.KeyboardEvent<HTMLInputElement>) => {
                  if (Key.Enter === event.key) onSubmit();
                }}
                onChange={(event: React.FormEvent<HTMLInputElement>) => {
                  onAdditionalPropertyUpdated('unit', new MetricUnit(event.currentTarget.value));
                }}
              />
            </div>
            {getErrorsView(errors, 'unit')}
          </div>
        </React.Fragment>
      );
    };

    /**
     * The only required part of the file: exporting the custom attribute view.
     */
    export const view = SimpleMetricAttributeView;


4) Register our custom attribute
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

To be able to have everything working, we need to register our custom attribute in the ``src/Acme/CustomBundle/Resources/config/requirejs.yml`` :

.. code-block:: yaml

    config:
        config:
            akeneoreferenceentity/application/configuration/attribute:
                simple_metric:
                    icon: bundles/pimui/images/attribute/icon-metric.svg
                    denormalize: '@custom/reference-entity/attribute/simple_metric.tsx'
                    reducer: '@custom/reference-entity/attribute/simple_metric.tsx'
                    view: '@custom/reference-entity/attribute/simple_metric.tsx'
