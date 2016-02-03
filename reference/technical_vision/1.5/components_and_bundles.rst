Components & Bundles
====================

Since the 1.3, Akeneo PIM introduced several components, they contain pure PIM business logic (Pim namespace) or technical logic (Akeneo namespace).

The code located in components is not coupled to Symfony Framework or Doctrine ORM/MongoDBODM.

The bundles contain specific Doctrine implementations and the Symfony glue to assemble components together.

At the end, this decoupling is very powerful, it allows to all our business code to rely only on interfaces and allow to change specific implementation easily.

For instance, in previous versions, by introducing SaverInterface and BulkSaverInterface, we've decoupled our business code from Doctrine by avoiding the direct use of persist/flush everywhere in the code.

With this move, we were able to replace the bulk saving of a products by a more efficient one without breaking anything in the business code.

Our very new features are done in this way but what to do with our legacy code?

We were really hesitating about this topic, because re-organizing has an impact on projects migration and not re-organizing makes the technical stack even harder to understand.

We've decided to assume the re-organization of our legacy code by providing a clear way to migrate from minor version to the upcoming one.

At the end, most of these changes consist in moving classes from bundles to move them into components and can be fixed by search & replace (cf last chapter).

These changes will continue to improve Developer eXperience by bringing a more understandable technical stack and by simplifying future evolutions and maintenance.
