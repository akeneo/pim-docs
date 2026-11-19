Akeneo or Pim, Bundle or Component?
-----------------------------------

Since version 1.3, Akeneo PIM introduced components, that contain business logic or technical logic not coupled with Symfony Framework, although Bundles contain the glue to assemble components together.

We use different namespaces, Akeneo for the technical parts not coupled with the PIM, Pim for the Community Edition parts and Enterprise suffix for the Enterprise Edition parts.

We organise the code as above in any new development and try to update legagy code as much as we can in each minor version to make the stack more and more understandable.

.. image:: /technical_architecture/component-or-bundle.png
    :width: 400pt
