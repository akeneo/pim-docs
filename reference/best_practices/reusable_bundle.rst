Create a reusable bundle
========================

.. warning::

    Here is a very early version of this section, we'll keep enriching it in coming weeks.

You are an integrator and you want to share reusable parts of your project with the awesome Akeneo PIM community?
Let's read our pieces of advice to write a great and documented bundle!


What is the purpose of a reusable bundle?

* **Compatibility**: to fit Akeneo PIM features
* **Maintainability**: to make your code flexible and testable (and tested)
* **Extensibility**: to allow integrators to use your bundle to fit with their specific needs
* **Scalability**: to make your application work with high volume of data and document its behavior in such cases


Compatibility
-------------

Your bundle won't be reusable if you forget some cases. That is no more an issue if you document compatibilities of your bundle.

Akeneo PIM storage stack comes with MySQL and hybrid MySQL/MongoDB (:doc:`/reference/technical_information/choose_database`).
You can refer to the internal API to be sure you develop your bundle the right way.
Follow these cookbooks for more details: ":doc:`/cookbook/catalog/product/index`" and ":doc:`/cookbook/catalog/common/index`".

There are two existing distributions: `Community`_ (CE) and `Enterprise`_ (EE) which comes with several extra features dedicated to advanced needs.

Many attribute types are managed (14 in CE and one more in EE). Make sure you do not forget any use case for an attribute type.

Try your bundle with other catalogs. Don't forget use cases with many locales, many channels and various catalog volumes.
You can use our `data generator bundle`_ to generate a catalog with random data.

.. _Community: https://github.com/akeneo/pim-community-standard
.. _Enterprise: https://www.akeneo.com/enterprise-edition/
.. _data generator bundle: https://github.com/akeneo-labs/DataGeneratorBundle


Maintainability
---------------

Keep in mind the five `SOLID principles`_ of Object Oriented Programming and especially the `Single Responsibility principle`_.

You can use `PMD`_ to help make your code simple, flexible and easily testable.


In Akeneo PIM, we use `PhpSpec`_ as unit testing tool and `Behat`_ for behavior-oriented tests.

Use a Continuous Integration (CI) tool to deploy your application, test your code and improve its quality.
On our open-source projects, we use free CI tools `Travis`_ for unit tests and `Scrutinizer`_ for static analysis.
You can find some configuration examples on our `Akeneo-Labs`_ projects (`scrutinizer.yml`_ and `travis.yml`_).

.. _Single Responsibility principle: https://en.wikipedia.org/wiki/Single_responsibility_principle
.. _SOLID principles: https://en.wikipedia.org/wiki/SOLID_(object-oriented_design)
.. _PMD: https://phpmd.org/
.. _PhpSpec: http://phpspec.readthedocs.org/
.. _Behat: http://docs.behat.org/
.. _Travis: https://travis-ci.org/
.. _Scrutinizer: https://scrutinizer-ci.com/
.. _Akeneo-Labs: https://github.com/akeneo-labs
.. _scrutinizer.yml: https://github.com/akeneo-labs/CustomEntityBundle/blob/master/.scrutinizer.yml
.. _travis.yml: https://github.com/akeneo-labs/CustomEntityBundle/blob/master/.travis.yml


Another important point about maintainability is to rely on interfaces instead of concrete classes.
It will facilitate your future migrations of Akeneo PIM.

Always prefer `composition over inheritance`_.
Indeed, class constructors can change from a minor version to another, while an interface will never change.

To give integrators a clean way to rely on your code, you can also create your own interfaces.

.. _composition over inheritance: https://en.wikipedia.org/wiki/Composition_over_inheritance


Extensibility
-------------

Follow Akeneo PIM dependencies
""""""""""""""""""""""""""""""
You **MUST** follow Akeneo PIM dependencies at all costs. Don't change version of existing ones.
Akeneo PIM has been tested with these dependencies. Changing them could impact the whole application performance.
You may add new dependencies needed for your customisations.


Use registries
""""""""""""""
Registries are also a good extension point when you have to deal with many cases (different attribute types, entities, etc.).
They are used to gather related classes (i.e: filters, sorters).
Simply declare it as a service and tag it (http://symfony.com/doc/2.7/components/dependency_injection/tags.html)

.. note::
    Learn more about `registry`_ pattern.

You can also take example on the localizers' cookbook: :doc:`/cookbook/localization/how_to_use_the_localizers`

.. _registry: http://martinfowler.com/eaaCatalog/registry.html


Plug on events (datagrid, savers, remover, etc.)
""""""""""""""""""""""""""""""""""""""""""""""""
It is the best extensibility point you have.
Unfortunately, it is not always possible as Akeneo PIM cannot cover all cases where you would plug your code.
In this case, you can still decorate or extend the desired class and add the extension point you need.

By the way, feel free to contribute by opening an issue to discuss with our team on our `github repository`_
or let us know your needs on our `forum`_. Your feedbacks are very valuable for us so we can improve our application.

.. _github repository: https://github.com/akeneo/pim-community-dev
.. _forum: https://www.akeneo.com/fr/forums/


Avoid to override Akeneo PIM classes/services
"""""""""""""""""""""""""""""""""""""""""""""
The easiest way to add a new behavior on your services is to override it. But wait! That's not a good thing!
First, each override leads to potential compatibility problems.
Let's assume that you overrode the `pim_catalog.saver.product` service and that a final customer wants to use your bundle and another one.
If both of them are overriding this service, the last bundle listed in the `app/AppKernel.php` will determine the one that will be used.

Another consequence is that it will have an impact on the whole application. That being said, it might be what you want to do.


Models
""""""
You should avoid to extend models (entities and documents) in order to add properties.
You would face the same problem than when you override classes/services.
There are unfortunately no great solutions for these cases.

Two options:

- There is not a lot of changes on the entity you overrode. Document what is missing and let future integrators of your bundle handle this.
- Set up a `oneToOne unidirectional association`_. Here is an example with the `Category` entity where we want to add a description field.
  You could also create a brand new one entity named `MyCategoryDescription` with an id, a description and a relation to the `Category` entity.
  As a side effect, you won't have access to the description from the Category object (opposite is possible).

For your own model classes, create your class and its interface.
Then you can rely on your interface and use the `Akeneo target resolver`_ which is based on the `Doctrine target entity resolver`_.

.. _oneToOne unidirectional association: http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-unidirectional
.. _Akeneo target resolver: https://github.com/akeneo/pim-community-dev/blob/1.5/src/Pim/Bundle/CatalogBundle/DependencyInjection/Compiler/ResolveDoctrineTargetModelPass.php
.. _Doctrine target entity resolver: http://symfony.com/doc/2.7/cookbook/doctrine/resolve_target_entity.html


Repositories
""""""""""""
Doctrine does not allow more than one repository per entity. For this reason, you can't declare them as one.
Nevertheless, you can create a service, inject the ObjectManager in it and the class you want to rely on.
Then you will have access to the query builder as in a doctrine repository and you can prepare the query you want.


Scalability
-----------

Do you know the limitations of your application?
It does not matter if you can't handle millions of products but you have to document what are the limitation of your application.

Keep in mind that some users will use your bundle with a data volume you never thought could be possible (true story!).

You can test your bundle with our `representative catalogs`_ we provide: https://github.com/akeneo/catalogs

Remember never to use the `findAll()` method from a repository as you don't know how many entities will be retrieved.

On batch processes, don't forget to detach your objects from the Doctrine unitOfWork and check the memory usage.
You can use `blackfire`_ and `php-meminfo`_ to help you tracking memory leaks.

.. _representative catalogs: http://docs.akeneo.com/latest/reference/scalability_guide/representative_catalogs.html
.. _blackfire: https://blackfire.io/docs/introduction
.. _php-meminfo: https://github.com/BitOne/php-meminfo
