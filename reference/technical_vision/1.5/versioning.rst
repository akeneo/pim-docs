Versioning Bundle & Component [WIP]
===================================

Versioning system has been introduced in a really early version of the PIM and has never been re-worked.

Up to the version 1.3, the saving of an object was done through direct calls to Doctrine persist/flush anywhere in the application.

The versioning system relies on Doctrine events to detect if an object has been changed to write a new version if this object is versionable.

Since the 1.3, we've introduced the SaverInterface and BulkSaverInterface and make all our business code rely on it.

It allows to decouple business code from Doctrine ORM persistence and make the object saving more explicit from business code point of view.

Our future plan for the versioning is to rely on these save calls to create new versions (and avoid to guess if any object has been updated).

This guessing part is very greedy and we expect to enhance the performances by making it more straightforward.

To prepare this shiny future, we've introduced a new Akeneo component which contains only pure business logic related to the versioning.

The versioning process itself will be re-worked in a future version. To make this future change painless, you can ensure to always rely on the SaverInterface, the BulkSaverInterface and the Versioning component models.

As usual, we provide upgrade commands (cf last chapter) to easily update projects migrating from 1.4 to 1.5.

TODO:
 - re-work the versioning system in the new component
 - rely on business save & saveAll and not anymore on doctrine events
 - depreciate the legacy system
