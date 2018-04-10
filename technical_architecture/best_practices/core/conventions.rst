Code Conventions
================

When contributing code to Akeneo PIM, you must follow its code conventions (very close to Symfony Code conventions).

This is part of our Developer eXperience effort, writing these conventions allows us to share the path we want to follow to continuously improve our codebase.

There are still inconsistencies in the codebase that we're fixing in the upcoming minor releases.

We'll keep on enhancing this section.

.. include:: /technical_architecture/component-or-bundle.rst.inc

Bundle Organization
-------------------

Classic folders are:

* Controller
* Datagrid
* DependencyInjection
* Doctrine
* Form
* EventSubscriber
* Resources

Controllers
-----------

Controllers are implemented in the /Controller directory. We created also a /Rest directory inside the /Controller directory to have all the REST Controller in the same directory.

We define them as services, these classes don't extend the BaseController and are not ContainerAware.

Entities
--------

Entities are defined in Component under the /Model directory.

An Interface is created for each model and, from outside, we rely only on the interface.

Repositories
------------

Repository Interfaces are defined in Component under /Repository directory

The implementations of Repositories (Doctrine ORM) are located in Bundle in:

* /Doctrine/ORM/Repository

Except classes from Doctrine/Common, we should never use Doctrine classes in a class which is not located in /Doctrine.

SaverInterface & RemoverInterface
---------------------------------

We never use ObjectManager persist/remove/flush except in SaverInterface and RemoverInterface.

It allows use to decouple the business code of component from the ORM code.

Services Definition
-------------------

We define the services in several files depending on the service purpose (form type, etc.).

In each of these, a service is named with the following convention:

.. code-block:: yaml

  <pim_bundle>.<directory>.<entity>

For example, the product saver service is ``pim_catalog.saver.product``.

You can have several directories separated by a dot like ``pim_catalog.form.type.product``.

Parameters are defined in the same file, the convention used is the same as the one for services, with a ``.class`` suffix

The parameter for the product saver service class is ``pim_catalog.saver.product.class``
