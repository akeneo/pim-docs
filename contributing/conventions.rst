Code Conventions
================

When contributing code to Akeneo PIM, you must follow its code conventions (very close to Symfony Code conventions).

This is part of our Developer Xperience effort, write these conventions allow us to share the path we want to follow to continuoulsy improve our codebase.

There are still inconsistencies in the codebase that we'll fix in coming releases.

We'll continue to enhance the Best Practises section to give clear hints on:

* how to contribute code on Akeneo PIM
* how to customize Akeneo PIM in projects
* how to create your own third party extension

Bundle Organization
-------------------

Classic folders are:

* Controller
* Form
* Model
* Repository
* Exception
* Doctrine

Controllers
-----------

Controllers are implemented in the /Controller directory.

We define them as services, these classes don't extend the BaseController and are not ContainerAware.

Entities or Documents
---------------------

Entities or Documents (depending on used Storage) are defined under the /Model directory.

An Interface is created for each model and, from outside, we rely on the interface.

Repositories
------------

Repository Interfaces are defined under /Repository directory

The implementations of Repositories (Doctrine ORM or MongoDBODM) are located in:

* /Doctrine/ORM/Repository or
* /Doctrine/MongoDBODM/Repository

Except Doctrine/Common, we should never use a Doctrine classes in a classe not located in /Doctrine.

Services Definition
-------------------

We defined the services in several files depending of the service purpose (form type, manager, etc.).

In each of these, a service is named with the following convention:

.. code-block:: yaml

  <pim_bundle>.<directory>.<entity>

For example, the product saver service is ``pim_catalog.saver.product``.

You can have several directories separated by dot like ``pim_catalog.form.type.product``.

Parameters are defined in the same file, the convention used is the same as the one for services, with a ``.class`` suffix

The parameter for the product saver service class is ``pim_catalog.saver.product.class``

Method Names
------------

When an object has a "main" many relation with related "things"
(objects, parameters, ...), the method names are normalized:

  * ``get()``
  * ``set()``
  * ``has()``
  * ``all()``
  * ``replace()``
  * ``remove()``
  * ``clear()``
  * ``isEmpty()``
  * ``add()``
  * ``register()``
  * ``count()``
  * ``keys()``

The usage of these methods are only allowed when it is clear that there
is a main relation:

* a ``CookieJar`` has many ``Cookie`` objects;

* a Service ``Container`` has many services and many parameters (as services
  is the main relation, the naming convention is used for this relation);

* a Console ``Input`` has many arguments and many options. There is no "main"
  relation, and so the naming convention does not apply.

For many relations where the convention does not apply, the following methods
must be used instead (where ``XXX`` is the name of the related thing):

+----------------+-------------------+
| Main Relation  | Other Relations   |
+================+===================+
| ``get()``      | ``getXXX()``      |
+----------------+-------------------+
| ``set()``      | ``setXXX()``      |
+----------------+-------------------+
| n/a            | ``replaceXXX()``  |
+----------------+-------------------+
| ``has()``      | ``hasXXX()``      |
+----------------+-------------------+
| ``all()``      | ``getXXXs()``     |
+----------------+-------------------+
| ``replace()``  | ``setXXXs()``     |
+----------------+-------------------+
| ``remove()``   | ``removeXXX()``   |
+----------------+-------------------+
| ``clear()``    | ``clearXXX()``    |
+----------------+-------------------+
| ``isEmpty()``  | ``isEmptyXXX()``  |
+----------------+-------------------+
| ``add()``      | ``addXXX()``      |
+----------------+-------------------+
| ``register()`` | ``registerXXX()`` |
+----------------+-------------------+
| ``count()``    | ``countXXX()``    |
+----------------+-------------------+
| ``keys()``     | n/a               |
+----------------+-------------------+

.. note::

    While "setXXX" and "replaceXXX" are very similar, there is one notable
    difference: "setXXX" may replace, or add new elements to the relation.
    "replaceXXX", on the other hand, cannot add new elements. If an unrecognized
    key is passed to "replaceXXX" it must throw an exception.

