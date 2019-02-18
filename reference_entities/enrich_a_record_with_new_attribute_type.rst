Enrich Records with a new Reference Entity Attribute type
=========================================================

.. note::

   Reference Entities feature is only available for the **Enterprise Edition**.

This cookbook will present how to enrich Records with a custom Reference Entity Attribute Type we just created in this step (create_new_attribute_type.rst).


Requirements
------------

Please be sure to follow the creation steps before following this guide.


Enrich Records With the Attribute
---------------------------------

In the previous tutorial, we've created a custom simple metric attribute.
In this tutorial, we will be able to enrich this attribute directly in the records of the reference entity.

1) Domain Layer
^^^^^^^^^^^^^^^

To enrich an record, we will create a new Record Value for the brand new Attribute type.
For example, we already have the ``TextData`` class for attribute type "Text".

Let's create our own ``SimpleMetricData`` that will handle the current data of the Record:

.. code-block:: php

    <?php
    namespace Acme\CustomBundle\Domain\Model\Record\Value;

    use Akeneo\ReferenceEntity\Domain\Model\Record\Value\ValueDataInterface;
    use Webmozart\Assert\Assert;

    class SimpleMetricData implements ValueDataInterface
    {
        /** @var string */
        private $metricValue;

        private function __construct(string $metricValue)
        {
            Assert::numeric($metricValue, 'The metric value should be a numeric string value');

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


That's all for the Domain Layer.

2) Application Layer
^^^^^^^^^^^^^^^^^^^^

Regarding the Application Layer, we will create a command first:

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

And its factory to build the command:

.. code-block:: php

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

Don't forget to register your factory to be recognized by our registry:

.. code-block:: yaml

    acme.application.factory.edit_metric_unit_command_factory:
        class: Acme\CustomBundle\Application\Attribute\EditAttribute\CommandFactory\EditMetricUnitCommandFactory
        tags:
            - { name: akeneo_referenceentity.edit_attribute_command_factory, priority: 120 }

Now that we have our command, we need a specific value updater that will be able to understand this command to update a simple metric value:

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

Of course, we need to register this updater to be recognized by our registry:

.. code-block:: yaml

    acme.application.edit_attribute.attribute_updater.metric_unit:
        class: Acme\CustomBundle\Application\Attribute\EditAttribute\AttributeUpdater\MetricUnitUpdater
        tags:
            - { name: akeneo_referenceentity.attribute_updater, priority: 120 }


3) Infrastructure Layer
^^^^^^^^^^^^^^^^^^^^^^^

Now that we can enrich our record with this new type of value, we need to create a dedicated hydrator, to hydrate our new record value from the DB:

.. code-block:: php

    <?php

    namespace Acme\CustomBundle\Infrastructure\Persistence\Sql\Record\Hydrator;

    use Acme\CustomBundle\Domain\Model\Attribute\SimpleMetricAttribute;
    use Acme\CustomBundle\Domain\Model\Record\Value\SimpleMetricData;
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
        class: Acme\CustomBundle\Infrastructure\Persistence\Sql\Record\Hydrator\SimpleMetricDataHydrator
        tags:
            - { name: akeneo_referenceentity.data_hydrator }

.. note::

   Note that if you want to validate the ``EditSimpleMetricValueCommand``, you simply have to create a regular Symfony validator.

Frontend Part of The New Record Value
-------------------------------------

To be able to create your brand new Simple Metric Record Value, we need to add some code in the frontend part.

To do so, you can put all needed code in one single file but you can (and are encouraged) to split it into multiple
files if needed.

To keep this example simple, we will create everything in this file :

``src/Acme/CustomBundle/Resources/public/reference-entity/record/simple-metric.tsx``

If you create a new Record Value, Akeneo will need three things to manage it in the frontend:
 - A model: a representation of your Record Value, those properties and overall behaviour
 - A view: as a React component to be able to render a user interface in the Record Form and dispatch events to the application
 - A cell: as a React component to be able to render a cell in the Record Grid

1) Model
^^^^^^^^

The model of your custom Record Value will contain those properties and behaviours.
To interface it with the rest of the PIM, your Record Value needs to extend the ValueData and provide a denormalizer.

This is the purpose of this section: provide a denormalizer capable of creating your custom Record Value extending the ValueData.

.. code-block:: javascript

    /**
     * ## Import section
     *
     * This is where sits your dependencies to external modules using the standard import method (see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/import)
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

      public static createFromNormalized(simpleMetricData: NormalizedSimpleMetricData): SimpleMetricData {
        return new SimpleMetricData(null === simpleMetricData ? '' : simpleMetricData);
      }

      public isEmpty(): boolean {
        return false;
      }

      public equals(data: ValueData): boolean {
        return data instanceof SimpleMetricData && this.simpleMetricData === data.simpleMetricData;
      }

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

Now that we have our custom Record Value model , it's to create the React component to be able to render a user interface in the Record Form and dispatch events to the application (https://reactjs.org/docs/react-component.html).

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

The last part we need to do, it's to create the React component to be able to render a cell in the Record Grid.

.. code-block:: javascript

    import {NormalizedValue} from 'akeneoreferenceentity/domain/model/record/value';
    import {CellView} from 'akeneoreferenceentity/application/configuration/value';
    import {denormalize as denormalizeAttribute} from "custom/reference-entity/attribute/simple_metric";
    import {NormalizedSimpleMetricAttribute} from "../attribute/simple_metric";
    import {Column} from "akeneoreferenceentity/application/reducer/grid";

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
                    denormalize: '@custom/reference-entity/record/simple_metric.tsx'
                    view: '@custom/reference-entity/record/simple_metric.tsx'
                    cell: '@custom/reference-entity/record/simple_metric.tsx'

