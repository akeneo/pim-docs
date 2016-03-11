Reusable Bundles for Akeneo PIM community
=========================================

You are an integrator and you want to share your project with the awesome Akeneo PIM community?
Let's read our pieces of advice to make a reusable and documented bundle!


What do you want when you create a reusable bundle?

* **Compatibility**: to fit Akeneo PIM features
* **Maintainability**: to make your code flexible and testable (and tested)
* **Extensibility**: to allow integrators to use your bundle to fit with their specific needs
* **Scalability**: to know your application limits and document them


Compatibility
-------------

Your bundle won't be reusable for some customers if you forget some cases. That is no more an issue if you document compatibilities of your bundle.

Akeneo PIM storage stack comes with MySQL and hybrid MySQL/MongoDB (:doc:`/reference/technical_information/choose_database`).
You can refer to the internal API to be sure you develop your bundle in the right way.
Follow these cookbooks for more details: ":doc:`/cookbook/catalog/product/index`" and ":doc:`/cookbook/catalog/common/index`".

There are two existing distributions CE/EE. The main project is `Akeneo PIM Community Edition`_ (CE) but
we also have an `Enterprise Edition`_ (EE) which comes with several of extra features dedicated to advanced needs.

Many attribute types are managed (14 in CE and one more in EE). Make sure you do not forget any use case for an attribute type.

Try your bundle with other catalogs. Don't forget use cases with many locales, many channels and various catalog volumes.
You can use our `data generator bundle`_ to generate a catalog with random data.

.. _Akeneo PIM Community Edition: https://github.com/akeneo/pim-community-standard
.. _Enterprise Edition: https://www.akeneo.com/enterprise-edition/
.. _data generator bundle: https://github.com/akeneo-labs/DataGeneratorBundle


Maintainability
---------------

First, keep in mind the `Single Responsibility`_ from the five `SOLID principles`_ of Object Oriented Programming.
It seems simple but we often forget to explode your code in multiple classes to make it more readable and testable.

You can use `PMD`_ to help make your code simple, flexible and easily testable.


In Akeneo PIM, we use `PhpSpec`_ as unit testing tool and `Behat`_ for behavior-oriented tests.

Use a Continuous Integration (CI) tool to deploy your application, test your code and improve its quality.
On our open-source projects, we use free CI tools `Travis`_ for unit tests and `Scrutinizer`_ for static analysis.
You can find some examples on our `Akeneo-Labs`_ projects (`scrutinizer.yml`_ and `travis.yml`_).

.. _Single Responsibility: https://en.wikipedia.org/wiki/Single_responsibility_principle
.. _SOLID principles: https://en.wikipedia.org/wiki/SOLID_(object-oriented_design)
.. _PMD: https://phpmd.org/
.. _PhpSpec: http://phpspec.readthedocs.org/
.. _Behat: http://docs.behat.org/
.. _Travis: https://travis-ci.org/
.. _Scrutinizer: https://scrutinizer-ci.com/
.. _Akeneo-Labs: https://github.com/akeneo-labs
.. _scrutinizer.yml: https://github.com/akeneo-labs/CustomEntityBundle/blob/master/.scrutinizer.yml
.. _travis.yml: https://github.com/akeneo-labs/CustomEntityBundle/blob/master/.travis.yml


Extensibility
-------------

**Respect Akeneo PIM dependencies**
You **MUST** follow Akeneo PIM dependencies at tall cost. Don't try to change version of existing ones.
You may add new dependencies needed for your customisations.

**Rely on interfaces not concrete classes**
An interface is a contract you will inject in a method. It will ease you the migration on future Akeneo PIM versions.
And create some for yours classes to give to integrator a clean way to rely on your code.

**Plug on events (datagrid, savers, remover, etc.)**
It is the best extensibility point you have.
Unfortunately, it is not always possible as Akeneo PIM can't know where you want to plug your code.
If you can not, it does not matter, you can decorate or extend your class and add the extension point you need.
By the way, feel free to contribute opening an issue to discuss with our team on our `github repository`_
or let us know your need on our `forum`_. It is important for us to improve ourselves and our application.

.. _github repository: https://github.com/akeneo/pim-community-dev
.. _forum: https://www.akeneo.com/fr/forums/

**Use registries**
Registries are also a good extension point when you have to manage with many cases (different attribute types, entities, etc.).
It consists in creating a registry class and then tag services which will be add to this registry.

.. note::
    Learn more about `registry`_ pattern.

You can also take example on the localizers: :doc:`/cookbook/localization/how_to_use_the_localizers`

.. _registry: http://martinfowler.com/eaaCatalog/registry.html


**Models and Repositories**
TargetResolvers
No hardcoded classes
    -> don't add properties to entities (extend model)

    -> Don't extend an entity but document how to use it

    -> Don't override repositories but create and inject your own service


- Avoid to override Akeneo PIM classes/services

    -> don't override class parameter or service

    --> create your own service (with your own class if you have to add specific code) for your use case.
    It won't impact the whole Akeneo PIM application


    --> Use composition instead of extension. Decorate your service






Scalability
-----------

know your limits and document them

Check the memory usage
xdebug + blackfire
Use php-meminfo

https://github.com/akeneo/catalogs

Never do findAll
http://docs.akeneo.com/latest/reference/scalability_guide/representative_catalogs.html
