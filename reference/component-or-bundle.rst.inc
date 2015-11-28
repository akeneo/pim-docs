Akeneo or Pim, Bundle or Component?
-----------------------------------

Since the 1.3, Akeneo PIM introduced components, they contain business logic or technical logic not coupled with Symfony Framework or Doctrine ORM / MongoDBODM, although Bundles contain the glue to assemble components together.

We use different namespaces, Akeneo for the technical parts not coupled with the PIM, Pim for the Community Edition parts and Enterprise postfix for the Enterprise Edition parts.

.. image:: /reference/component-or-bundle.png