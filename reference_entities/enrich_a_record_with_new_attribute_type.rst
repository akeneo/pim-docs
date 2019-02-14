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

In the previous tutorial, we've created a custom boolean attribute.
In this tutorial, we will be able to enrich this attribute directly in the records of the reference entity.

1) Domain Layer
^^^^^^^^^^^^^^^

To enrich an record, we will create a new Record Value for the brand new Attribute type.
For example, we already have the ``TextData`` class for attribute type "Text".

Let's create our own ``BooleanData`` that will handle the current data of the Record:

.. code-block:: php

    <?php
    namespace Acme\CustomBundle\Domain\Model\Record\Value;

    use Akeneo\ReferenceEntity\Domain\Model\Record\Value\ValueDataInterface;
    use Webmozart\Assert\Assert;

    class BooleanData implements ValueDataInterface
    {
        /** @var bool */
        private $boolean;

        private function __construct(bool $boolean)
        {
            $this->boolean = $boolean;
        }

        /**
         * @return boolean
         */
        public function normalize()
        {
            return $this->boolean;
        }

        public static function createFromNormalize($normalizedData): ValueDataInterface
        {
            Assert::boolean($normalizedData, 'Normalized data should be a boolean');

            return new self($normalizedData);
        }
    }


That's all for the Domain Layer.

2) Application Layer
^^^^^^^^^^^^^^^^^^^^

Regarding the Application Layer, we will create a command first:

.. code-block:: php

    <?php

    namespace Acme\CustomBundle\Application\Record\EditRecord\CommandFactory;

    use Acme\CustomBundle\Domain\Model\Attribute\BooleanAttribute;
    use Akeneo\ReferenceEntity\Application\Record\EditRecord\CommandFactory\AbstractEditValueCommand;

    class EditBooleanValueCommand extends AbstractEditValueCommand
    {
        /** @var bool */
        public $boolean;

        public function __construct(BooleanAttribute $attribute, ?string $channel, ?string $locale, bool $boolean)
        {
            parent::__construct($attribute, $channel, $locale);

            $this->boolean = $boolean;
        }
    }

Then its factory:

.. code-block:: php

    <?php

    namespace Acme\CustomBundle\Application\Record\EditRecord\CommandFactory;

    use Acme\CustomBundle\Domain\Model\Attribute\BooleanAttribute;
    use Akeneo\ReferenceEntity\Application\Record\EditRecord\CommandFactory\AbstractEditValueCommand;
    use Akeneo\ReferenceEntity\Application\Record\EditRecord\CommandFactory\EditValueCommandFactoryInterface;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AbstractAttribute;

    class EditBooleanValueCommandFactory implements EditValueCommandFactoryInterface
    {
        public function supports(AbstractAttribute $attribute, array $normalizedValue): bool
        {
             return
                 $attribute instanceof BooleanAttribute &&
                '' !== $normalizedValue['data'] &&
                is_bool($normalizedValue['data']);
        }

        public function create(AbstractAttribute $attribute, array $normalizedValue): AbstractEditValueCommand
        {
            $command = new EditBooleanValueCommand(
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

    acme.application.factory.record.edit_boolean_value_command_factory:
        class: Acme\CustomBundle\Application\Record\EditRecord\CommandFactory\EditBooleanValueCommandFactory
        tags:
        - { name: akeneo_referenceentity.edit_record_value_command_factory }

Now that we have our command, we need a specific value updater that will be able to understand this command to update a boolean value:

.. code-block:: php

    <?php

    namespace Acme\CustomBundle\Application\Record\EditRecord\ValueUpdater;

    use Acme\CustomBundle\Application\Record\EditRecord\CommandFactory\EditBooleanValueCommand;
    use Acme\CustomBundle\Domain\Model\Record\Value\BooleanData;
    use Akeneo\ReferenceEntity\Application\Record\EditRecord\CommandFactory\AbstractEditValueCommand;
    use Akeneo\ReferenceEntity\Application\Record\EditRecord\ValueUpdater\ValueUpdaterInterface;
    use Akeneo\ReferenceEntity\Domain\Model\ChannelIdentifier;
    use Akeneo\ReferenceEntity\Domain\Model\LocaleIdentifier;
    use Akeneo\ReferenceEntity\Domain\Model\Record\Record;
    use Akeneo\ReferenceEntity\Domain\Model\Record\Value\ChannelReference;
    use Akeneo\ReferenceEntity\Domain\Model\Record\Value\LocaleReference;
    use Akeneo\ReferenceEntity\Domain\Model\Record\Value\Value;

    class BooleanUpdater implements ValueUpdaterInterface
    {
        public function supports(AbstractEditValueCommand $command): bool
        {
            return $command instanceof EditBooleanValueCommand;
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

            $boolean = BooleanData::createFromNormalize($command->boolean);

            $value = Value::create($attribute, $channelReference, $localeReference, $boolean);
            $record->setValue($value);
        }
    }

Of course, we need to register this updater to be recognized by our registry:

.. code-block:: yaml

    acme.application.edit_record.record_value_updater.boolean_updater:
        class: Acme\CustomBundle\Application\Record\EditRecord\ValueUpdater\BooleanUpdater
        tags:
        - { name: akeneo_referenceentity.record_value_updater }


3) Infrastructure Layer
^^^^^^^^^^^^^^^^^^^^^^^

Now that we can enrich our record with this new type of value, we need to create a dedicated hydrator, to hydrate our new record value from the DB:

.. code-block:: php

    <?php

    namespace Acme\CustomBundle\Infrastructure\Persistence\Sql\Record\Hydrator;

    use Acme\CustomBundle\Domain\Model\Attribute\BooleanAttribute;
    use Acme\CustomBundle\Domain\Model\Record\Value\BooleanData;
    use Akeneo\ReferenceEntity\Domain\Model\Attribute\AbstractAttribute;
    use Akeneo\ReferenceEntity\Domain\Model\Record\Value\ValueDataInterface;
    use Akeneo\ReferenceEntity\Infrastructure\Persistence\Sql\Record\Hydrator\DataHydratorInterface;

    class BooleanDataHydrator implements DataHydratorInterface
    {
        public function supports(AbstractAttribute $attribute): bool
        {
            return $attribute instanceof BooleanAttribute;
        }

        public function hydrate($normalizedData): ValueDataInterface
        {
            return BooleanData::createFromNormalize($normalizedData);
        }
    }

And register it for the registry:

.. code-block:: yaml

    acme.infrastructure.persistence.record.hydrator.text_data:
        class: Acme\CustomBundle\Infrastructure\Persistence\Sql\Record\Hydrator\BooleanDataHydrator
        tags:
        - { name: akeneo_referenceentity.data_hydrator }




.. note::

   Note that if you want to validate the ``EditBooleanValueCommand``, you simply have to create a regular Symfony validator.

Frontend Part of The New Record Value
-------------------------------------

To be able to create your brand new Boolean Record Value, we need to add some code in the frontend part.

To do so, you can put all needed code in one single file but you can (and are encouraged) to split it into multiple
files if needed.

To keep this example simple, we will create everything in this file :

``src/Acme/CustomBundle/Resources/public/reference-entity/record/boolean.tsx``

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
    export type NormalizedBooleanData = boolean | null;
    class BooleanData extends ValueData {
      private constructor(private booleanData: boolean) {
        super();

        if ('boolean' !== typeof booleanData) {
          throw new InvalidTypeError('BooleanData expects a boolean as parameter to be created');
        }

        Object.freeze(this);
      }

      public static createFromNormalized(booleanData: NormalizedBooleanData): BooleanData {
        return new BooleanData(null === booleanData ? false : booleanData);
      }

      public isEmpty(): boolean {
        return false;
      }

      public equals(data: ValueData): boolean {
        return data instanceof BooleanData && this.booleanData === data.booleanData;
      }

      public stringValue(): string {
        return (this.booleanData) ? 'true' : 'false';
      }

      public normalize(): boolean {
        return this.booleanData;
      }
    }

    /**
     * The only required part of the file: exporting a denormalize method returning a boolean Record Value.
     */
    export const denormalize = BooleanData.createFromNormalized;

2) View
^^^^^^^

Now that we have our custom Record Value model , it's to create the React component to be able to render a user interface in the Record Form and dispatch events to the application (https://reactjs.org/docs/react-component.html).

.. code-block:: javascript

    import * as React from 'react';
    import Value from 'akeneoreferenceentity/domain/model/record/value';
    import {ConcreteBooleanAttribute} from 'custom/reference-entity/attribute/boolean.tsx';
    import Key from "akeneoreferenceentity/tools/key";

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
      if (!(value.data instanceof BooleanData && value.attribute instanceof ConcreteBooleanAttribute)) {
        return null;
      }

      const onValueChange = (boolean: boolean) => {
        const newData = denormalize(boolean);
        if (newData.equals(value.data)) {
          return;
        }

        const newValue = value.setData(newData);

        onChange(newValue);
      };

      // We need to have single quotes around the React.Fragment tag for displaying well the JSX in the documentation but you have to remove it in your code.
      return (
        '<React.Fragment>
          <label
            className={`AknSwitch ${!canEditData ? 'AknSwitch--disabled' : ''}`}
            tabIndex={!canEditData ? -1 : 0}
            role="checkbox"
            aria-checked={value.data.normalize()}
            onKeyPress={event => {
              if ([' '].includes(event.key) && !canEditData) onValueChange(!value.data.normalize());
              if (Key.Enter === event.key) onSubmit();
            }}
          >
            <input
              id={"pim_reference_entity.record.edit.input.default_value"}
              type="checkbox"
              className="AknSwitch-input"
              checked={value.data.normalize()}
              onChange={() => {
                if (canEditData) onValueChange(!value.data.normalize());
              }}
            />
            <span className="AknSwitch-slider" />
          </label>
        </React.Fragment>'
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
    const memo = (React as any).memo;

    /**
     * Here we define the React Component as a function with the following props :
     *    - the custom Record Value
     *
     * It returns the JSX View to display the cell of your custom Record Value in the grid.
     */
    const BooleanCellView: CellView = memo(({value}: {value: NormalizedValue}) => {
      const booleanData = denormalize(value.data);

      // We need to have single quotes around the div tag for displaying well the JSX in the documentation but you have to remove it in your code.
      return (
        '<div className="AknGrid-bodyCellContainer" title={booleanData.stringValue()}>
          {booleanData.stringValue()}
        </div>'
      );
    });

    /**
     * The only required part of the file: exporting the custom Record Value cell.
     */
    export const cell = BooleanCellView;

4) Register our custom Record Value
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

To be able to have everything working, we need to register our custom Record Value in the ``src/Acme/CustomBundle/Resources/config/requirejs.yml`` :

.. code-block:: yaml

    config:
        config:
            akeneoreferenceentity/application/configuration/value:
                boolean:
                    denormalize: '@custom/reference-entity/record/boolean.tsx'
                    view: '@custom/reference-entity/record/boolean.tsx'
                    cell: '@custom/reference-entity/record/boolean.tsx'

