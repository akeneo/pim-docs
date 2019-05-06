Enrich Records with a new Reference Entity Attribute type
=========================================================

.. note::

   Reference Entities feature is only available for the **Enterprise Edition**.

This cookbook will present how to enrich Records with a custom Reference Entity attribute type we just created `in a previous step`_.

.. _in a previous step: ./create_new_attribute_type.html

Enrich Records with the Attribute
---------------------------------

In the previous tutorial, we've created a custom simple metric attribute.
In this tutorial, we will be able to enrich this attribute directly in the records of the reference entity, for example here, the Surface attribute:

.. image:: ../_images/reference_entities/enrich_record_simple_metric_attribute.png
  :alt: Enrich a Simple Metric value on a record

1) Your new Record value
^^^^^^^^^^^^^^^^^^^^^^^^

To enrich a record, we will create a new Record Value for the brand new Attribute type.
For example, we already have the ``TextData`` class for attribute type "Text".

Let's create our own ``SimpleMetricData`` that will handle the current data of the Record:

.. code-block:: php

    <?php

    namespace Acme\CustomBundle\Record;

    use Akeneo\ReferenceEntity\Domain\Model\Record\Value\ValueDataInterface;
    use Webmozart\Assert\Assert;

    class SimpleMetricData implements ValueDataInterface
    {
        /** @var string */
        private $metricValue;

        private function __construct(string $metricValue)
        {
            $this->metricValue = $metricValue;
        }

        /**
         * @return string
         */
        public function normalize()
        {
            return $this->metricValue;
        }

        public static function createFromNormalize($normalizedData): ValueDataInterface
        {
            Assert::string($normalizedData, 'Normalized data should be a string');

            return new self($normalizedData);
        }

        public static function fromString(string $metricValue)
        {
            return new self($metricValue);
        }
    }


2) Set a value for the new attribute
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Let's start by creating a command to represent the intent of updating the value:

.. code-block:: php

    <?php

    namespace Acme\CustomBundle\Record;

    use Acme\CustomBundle\Attribute\SimpleMetricAttribute;
    use Akeneo\ReferenceEntity\Application\Record\EditRecord\CommandFactory\AbstractEditValueCommand;

    class EditSimpleMetricValueCommand extends AbstractEditValueCommand
    {
        /** @var string */
        public $newMetricValue;

        public function __construct(SimpleMetricAttribute $attribute, ?string $channel, ?string $locale, string $newMetricValue)
        {
            parent::__construct($attribute, $channel, $locale);

            $this->newMetricValue = $newMetricValue;
        }
    }


And its factory to build the command:

.. code-block:: php

    <?php

    namespace Acme\CustomBundle\Record;

    use Acme\CustomBundle\Attribute\SimpleMetricAttribute;
    use Akeneo\ReferenceEntity\Application\Record\EditRecord\CommandFactory\AbstractEditValueCommand;
    use Akeneo\ReferenceEntity\Application\Record\EditRecord\CommandFactory\EditValueCommandFactoryInterface;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AbstractAttribute;

    class EditSimpleMetricValueCommandFactory implements EditValueCommandFactoryInterface
    {
        public function supports(AbstractAttribute $attribute, array $normalizedValue): bool
        {
             return
                 $attribute instanceof SimpleMetricAttribute &&
                '' !== $normalizedValue['data'] &&
                is_string($normalizedValue['data']);
        }

        public function create(AbstractAttribute $attribute, array $normalizedValue): AbstractEditValueCommand
        {
            $command = new EditSimpleMetricValueCommand(
                $attribute,
                $normalizedValue['channel'],
                $normalizedValue['locale'],
                $normalizedValue['data']
            );

            return $command;
        }
    }

Don't forget to register your factory to be recognized by our registry:

.. code-block:: yaml

    acme.application.factory.record.edit_simple_metric_value_command_factory:
        class: Acme\CustomBundle\Record\EditSimpleMetricValueCommandFactory
        tags:
            - { name: akeneo_referenceentity.edit_record_value_command_factory }

Now that we have our command, we need a specific value updater that will be able to understand this command to update a simple metric value:

.. code-block:: php

    <?php

    namespace Acme\CustomBundle\Record;

    use Acme\CustomBundle\Record\EditSimpleMetricValueCommand;
    use Acme\CustomBundle\Record\SimpleMetricData;
    use Akeneo\ReferenceEntity\Application\Record\EditRecord\CommandFactory\AbstractEditValueCommand;
    use Akeneo\ReferenceEntity\Application\Record\EditRecord\ValueUpdater\ValueUpdaterInterface;
    use Akeneo\ReferenceEntity\Domain\Model\ChannelIdentifier;
    use Akeneo\ReferenceEntity\Domain\Model\LocaleIdentifier;
    use Akeneo\ReferenceEntity\Domain\Model\Record\Record;
    use Akeneo\ReferenceEntity\Domain\Model\Record\Value\ChannelReference;
    use Akeneo\ReferenceEntity\Domain\Model\Record\Value\LocaleReference;
    use Akeneo\ReferenceEntity\Domain\Model\Record\Value\Value;

    class SimpleMetricUpdater implements ValueUpdaterInterface
    {
        public function supports(AbstractEditValueCommand $command): bool
        {
            return $command instanceof EditSimpleMetricValueCommand;
        }

        public function __invoke(Record $record, AbstractEditValueCommand $command): void
        {
            if (!$this->supports($command)) {
                throw new \RuntimeException('Impossible to update the value of the record with the given command.');
            }

            $attribute = $command->attribute->getIdentifier();
            $channelReference = (null !== $command->channel) ?
                ChannelReference::fromChannelIdentifier(ChannelIdentifier::fromCode($command->channel)) :
                ChannelReference::noReference();
            $localeReference = (null !== $command->locale) ?
                LocaleReference::fromLocaleIdentifier(LocaleIdentifier::fromCode($command->locale)) :
                LocaleReference::noReference();

            $metricValue = SimpleMetricData::createFromNormalize($command->metricValue);

            $value = Value::create($attribute, $channelReference, $localeReference, $metricValue);
            $record->setValue($value);
        }
    }

We need to register this updater to be known by our registry:

.. code-block:: yaml

    acme.application.edit_record.record_value_updater.simple_metric_updater:
            class: Acme\CustomBundle\Record\SimpleMetricUpdater
            tags:
                - { name: akeneo_referenceentity.record_value_updater }


3) Retrieve our record value
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Now that we can enrich our record with this new type of value, we need to create a dedicated hydrator, to hydrate our new record value from the DB:

.. code-block:: php

    <?php

    namespace Acme\CustomBundle\Record;

    use Acme\CustomBundle\Attribute\SimpleMetricAttribute;
    use Acme\CustomBundle\Record\SimpleMetricData;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AbstractAttribute;
    use Akeneo\ReferenceEntity\Domain\Model\Record\Value\ValueDataInterface;
    use Akeneo\ReferenceEntity\Infrastructure\Persistence\Sql\Record\Hydrator\DataHydratorInterface;

    class SimpleMetricDataHydrator implements DataHydratorInterface
    {
        public function supports(AbstractAttribute $attribute): bool
        {
            return $attribute instanceof SimpleMetricAttribute;
        }

        public function hydrate($normalizedData): ValueDataInterface
        {
            return SimpleMetricData::createFromNormalize($normalizedData);
        }
    }

And register it for the registry:

.. code-block:: yaml

    acme.infrastructure.persistence.record.hydrator.simple_metric_data:
        class: Acme\CustomBundle\Record\SimpleMetricDataHydrator
        tags:
            - { name: akeneo_referenceentity.data_hydrator }

Frontend Part of The New Record Value
-------------------------------------

To be able to enrich your records with this new attribute, we need to add some code in the frontend part.

To do so, you can put all needed code in one single file but you can (and are encouraged) to split it into multiple
files if needed.

To keep this example simple, we will create everything in this file :

``src/Acme/CustomBundle/Resources/public/reference-entity/record/simple-metric.tsx``

.. note::

    If you create a new Record Value, Akeneo will need three things to manage it in the frontend:

    - A **model**: a representation of your Record Value, its properties and overall behaviour
    - A **view**: as a React component to be able to render a user interface in the Record Form and dispatch events to the application
    - A **cell**: as a React component to be able to render a cell in the Record Grid

1) Model
^^^^^^^^

The model of your custom Record Value will contain it's properties and behaviours.
To interface it with the rest of the PIM, your Record Value needs to extend the ValueData and provide a denormalizer.

This is the purpose of this section: provide a denormalizer capable of creating your custom Record Value extending the ValueData.

.. code-block:: javascript

    /**
     * ## Import section
     *
     * This is where your dependencies are to external modules using the standard import method (see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/import)
     * The paths are absolute and the root is the web/bundles folder (at the root of your PIM project)
     */
    import ValueData from 'akeneoreferenceentity/domain/model/record/data';

    class InvalidTypeError extends Error {}

    /**
     * Here we are implementing our custom Record Value model.
     */
    export type NormalizedSimpleMetricData = string | null;
    class SimpleMetricData extends ValueData {
      private constructor(private simpleMetricData: string) {
        super();

        if ('string' !== typeof simpleMetricData) {
          throw new InvalidTypeError('SimpleMetricData expects a string as parameter to be created');
        }

        Object.freeze(this);
      }

      /**
       * Here, we denormalize our record value
       */
      public static createFromNormalized(simpleMetricData: NormalizedSimpleMetricData): SimpleMetricData {
        return new SimpleMetricData(null === simpleMetricData ? '' : simpleMetricData);
      }

      /**
       * Check the emptiness
       */
      public isEmpty(): boolean {
        return '' === this.simpleMetricData;
      }

      /**
      * Check if the value is equal to the simple metric data
      */
      public equals(data: ValueData): boolean {
        return data instanceof SimpleMetricData && this.simpleMetricData === data.simpleMetricData;
      }

      /**
       * The only method to implement here: the normalize method. Here you need to provide a serializable object (see https://developer.mozilla.org/en-US/docs/Glossary/Serialization)
       */
      public normalize(): string {
        return this.simpleMetricData;
      }
    }

    /**
     * The only required part of the file: exporting a denormalize method returning a simple metric Record Value.
     */
    export const denormalize = SimpleMetricData.createFromNormalized;

2) View
^^^^^^^

Now that we have our custom Record Value model we can now create the React component to be able to render a user interface in the Record Form and dispatch events to the application (https://reactjs.org/docs/react-component.html).

.. code-block:: javascript

    import * as React from 'react';
    import Value from 'akeneoreferenceentity/domain/model/record/value';
    import {ConcreteSimpleMetricAttribute} from 'custom/reference-entity/attribute/simple_metric.tsx';
    import Key from 'akeneoreferenceentity/tools/key';

    /**
     * Here we define the React Component as a function with the following props :
     *    - the custom Record Value
     *    - the callback function to update the Record Value
     *    - the callback for the submit
     *    - the right to edit the Record Value
     *
     * It returns the JSX View to display the field of the custom Record Value.
     */
    const View = ({
      value,
      onChange,
      onSubmit,
      canEditData,
    }: {
      value: Value;
      onChange: (value: Value) => void;
      onSubmit: () => void;
      canEditData: boolean;
    }) => {
      if (!(value.data instanceof SimpleMetricData && value.attribute instanceof ConcreteSimpleMetricAttribute)) {
        return null;
      }

      const onValueChange = (text: string) => {
        const newData = denormalize(text);
        if (newData.equals(value.data)) {
          return;
        }

        const newValue = value.setData(newData);

        onChange(newValue);
      };

      return (
        <React.Fragment>
          <input
            id={`pim_reference_entity.record.enrich.${value.attribute.getCode().stringValue()}`}
            autoComplete="off"
            className={`AknTextField AknTextField--narrow AknTextField--light
              ${value.attribute.valuePerLocale ? 'AknTextField--localizable' : ''}
              ${!canEditData ? 'AknTextField--disabled' : ''}`}
            value={value.data.normalize()}
            onChange={(event: React.ChangeEvent<HTMLInputElement>) => {
              onValueChange(event.currentTarget.value);
            }}
            onKeyDown={(event: React.KeyboardEvent<HTMLInputElement>) => {
              if (Key.Enter === event.key) onSubmit();
            }}
            disabled={!canEditData}
            readOnly={!canEditData}
          />
          <span>{value.attribute.unit.normalize()}</span>
        </React.Fragment>
      );
    };

    /**
     * The only required part of the file: exporting the custom Record Value view.
     */
    export const view = View;

3) Cell
^^^^^^^

The last part we need to do is to create the React component to be able to render a cell in the Record Grid.

.. code-block:: javascript

    import {NormalizedValue} from 'akeneoreferenceentity/domain/model/record/value';
    import {CellView} from 'akeneoreferenceentity/application/configuration/value';
    import {denormalize as denormalizeAttribute} from 'custom/reference-entity/attribute/simple_metric';
    import {NormalizedSimpleMetricAttribute} from 'custom/reference-entity/attribute/simple_metric';
    import {Column} from 'akeneoreferenceentity/application/reducer/grid';

    const memo = (React as any).memo;

    /**
     * Here we define the React Component as a function with the following props :
     *    - the custom Record Value
     *
     * It returns the JSX View to display the cell of your custom Record Value in the grid.
     */
    const SimpleMetricCellView: CellView = memo(({column, value}: {column: Column, value: NormalizedValue}) => {
      const simpleMetricData = denormalize(value.data);
      const simpleMetricAttribute = denormalizeAttribute(column.attribute as NormalizedSimpleMetricAttribute);

      return (
        <div className="AknGrid-bodyCellContainer" title={simpleMetricData.normalize()}>
          {simpleMetricData.normalize()}
          <span>{simpleMetricAttribute.unit.normalize()}</span>
        </div>
      );
    });

    /**
     * The only required part of the file: exporting the custom Record Value cell.
     */
    export const cell = SimpleMetricCellView;

4) Register our custom Record Value
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

To be able to have everything working, we need to register our custom Record Value in the ``src/Acme/CustomBundle/Resources/config/requirejs.yml`` :

.. code-block:: yaml

    config:
        config:
            akeneoreferenceentity/application/configuration/value:
                simple_metric:
                    denormalize: '@custom/reference-entity/record/simple-metric.tsx'
                    view: '@custom/reference-entity/record/simple-metric.tsx'
                    cell: '@custom/reference-entity/record/simple-metric.tsx'

API Part of The New Record Value
--------------------------------

1) Json schema validator of a simple metric value when creating or updating a record

^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
In order to validate the format of the value when creating or editing a record, we have to create a Json Schema validator for the simple metric value.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace Acme\CustomBundle\Record\JsonSchema;

    use Acme\CustomBundle\Attribute\SimpleMetricAttribute;
    use Akeneo\ReferenceEntity\Infrastructure\Connector\Api\Record\JsonSchema\RecordValueValidatorInterface;
    use JsonSchema\Validator;

    class SimpleMetricTypeValidator implements RecordValueValidatorInterface
    {
        /**
         * {@inheritdoc}
         */
        public function validate(array $normalizedRecord): array
        {
            $record = Validator::arrayToObjectRecursive($normalizedRecord);
            $validator = new Validator();
            $validator->validate($record, $this->getJsonSchema());

            return $validator->getErrors();
        }

        public function forAttributeType(): string
        {
            return SimpleMetricAttribute::class;
        }

        private function getJsonSchema(): array
        {
            return [
                'type' => 'object',
                'properties' => [
                    'values' => [
                        'type' => 'object',
                        'patternProperties' => [
                            '.+' => [
                                'type'  => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'locale' => [
                                            'type' => ['string', 'null'],
                                        ],
                                        'channel' => [
                                            'type' => ['string', 'null'],
                                        ],
                                        'data' => [
                                            'type' => ['string', 'null'],
                                        ],
                                    ],
                                    'required' => ['locale', 'channel', 'data'],
                                    'additionalProperties' => false,
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }
    }


And to register it:

.. code-block:: yaml

    # src/Acme/CustomBundle/Resources/config/services.yml

    services:
        acme.infrastructure.connector.api.record_value_simple_metric_type_validator:
            class: Acme\CustomBundle\Record\JsonSchema\SimpleMetricTypeValidator
            tags:
                - { name: akeneo_referenceentity.connector.api.record_value_validator }
