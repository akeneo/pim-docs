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

    acme.application.edit_record.record_value_updater.text_updater:
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


Enrich Records with your new Attribute
--------------------------------------

- Domain Record (Data of the Value)
- Application Record (Edit)
- Infra Record (Validation, Hydrator)
