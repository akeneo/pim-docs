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

Keep in mind the `Single Responsibility`_ from the five `SOLID principles`_ of Object Oriented Programming.
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


Another important point about maintainability is to try to rely on interfaces not on concrete classes.
An interface is a contract you will inject in a method. It will ease you the migration on future Akeneo PIM versions.

Don't forget you can use composition instead of inheritance.
Decorate a class will allow you to benefit of inheritance avoiding migration problems.
Indeed, class constructors can changed from a version to another. On the other side, an interface do not change.

To give to integrator a clean way to rely on your code, you can alse create interfaces.


Extensibility
-------------

Respect Akeneo PIM dependencies
"""""""""""""""""""""""""""""""
You **MUST** follow Akeneo PIM dependencies at tall cost. Don't try to change version of existing ones.
You may add new dependencies needed for your customisations.


Use registries
""""""""""""""
Registries are also a good extension point when you have to manage with many cases (different attribute types, entities, etc.).
It consists in creating a registry class and then tag services which will be add to this registry.

.. note::
Learn more about `registry`_ pattern.

You can also take example on the localizers' cookbook: :doc:`/cookbook/localization/how_to_use_the_localizers`

.. _registry: http://martinfowler.com/eaaCatalog/registry.html


Plug on events (datagrid, savers, remover, etc.)
""""""""""""""""""""""""""""""""""""""""""""""""
It is the best extensibility point you have.
Unfortunately, it is not always possible as Akeneo PIM can't know where you want to plug your code.
If you can not, it does not matter, you can decorate or extend your class and add the extension point you need.
By the way, feel free to contribute opening an issue to discuss with our team on our `github repository`_
or let us know your need on our `forum`_. It is important for us to improve ourselves and our application.

.. _github repository: https://github.com/akeneo/pim-community-dev
.. _forum: https://www.akeneo.com/fr/forums/


Avoid to override Akeneo PIM classes/services
"""""""""""""""""""""""""""""""""""""""""""""
The easiest way to add a new behavior on your services is to override it.. But wait! That's not a good thing!
First, each override leads to potential compatibility problems.
Let's imagine that you override the `pim_catalog.saver.product` because you want to add a specific action when you save a product.
Now, a final customer wants to use your bundle and another one which override the same service (Tough luck!).
The last bundle defined in the `app/AppKernel.php` will defined the service used..
And in both cases, one of the actions will not be applied.

The second effect of overriding a parameter or a service is that it will have a side effect on the whole application.
If you need to impact transversely Akeneo PIM and you can't plug on events, that's a way to do but keep in mind
you can use create your own service with your own class.


Models and Repositories
"""""""""""""""""""""""
You should avoid to extends the models (entities and documents) and repositories.
The problem is the same than when you override classes/services.

There is unfortunately no perfect solution for these cases.

Don't extend an entity adding it properties.
Two possibilities here:
* There is not a lot of changes on the entity you overrode. Document what is missing and let's the integrators do it.
* Do a `oneToOne unidirectional association`_. Here is an example with the `Category` entity. I want to add the description on it.
A way to do that avoiding to extend is to create a new entity named `MyExtendedCategory` with an id, a description and a relation to the `Category` entity.
The bad side of this solution is you won't have access to the description from the Category object (the reverse is possible).
It's sometimes frustrating to access it by a non-natural way.


For your own model classes, create your class and its interface.
Then you can rely on your interface and use the `Akeneo target resolver`_ which is based on the `Doctrine target entity resolver`_.


About repositories, you can create and inject your own service. Doctrine does not allow to have many repositories.
Don't tint it as a doctrine one, just inject the `ObjectManager` (Entity or Document) and the class you want to rely on.
Then you will have access to the query builder as in a doctrine repository and you can prepare the query you want.

.. _oneToOne unidirectional association: http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-unidirectional
.. _Akeneo target resolver: https://github.com/akeneo/pim-community-dev/blob/1.5/src/Pim/Bundle/CatalogBundle/DependencyInjection/Compiler/ResolveDoctrineTargetModelPass.php
.. _Doctrine target entity resolver: http://symfony.com/doc/2.7/cookbook/doctrine/resolve_target_entity.html


Scalability
-----------

know your limits and document them

Check the memory usage
xdebug + blackfire
Use php-meminfo

https://github.com/akeneo/catalogs

Never do findAll
http://docs.akeneo.com/latest/reference/scalability_guide/representative_catalogs.html
