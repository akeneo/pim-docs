How to customize any frontend part of the application
-----------------------------------------------------

Introduction
===================

Akeneo PIM uses RequireJS and Backbone to manage modules and views in our application. On top of those two layers, we added our own layer: the form extension system. In this documentation, we will discover the basics of those three layers to be able to understand Akeneo PIM stack and customize it.

.. note::

    Since the 1.8 version, we migrated to webpack to manage our front dependencies. To ease the migration we decided to keep the historicals ``requirejs.yml`` files to give the community enough time to migrate their configuration.

Create our first RequireJS module
=================================

RequireJS modules are the equivalent of php classes. They provide a way to declare dependencies to a module (like use statements) and expose a new module.

Simple business logic
+++++++++++++++++++++

As an example, we will create a module that will display in the console the current UI locale

To begin here is the business logic of our component:

.. code-block:: javascript
    :linenos:

    {
        logLocale: function () {
            console.log(UserContext.get('uiLocale'));
        }
    }


Our newly created object has one method which logs the current catalog locale using the UserContext module.
Now that we have our business logic implemented, we need to tell the system that our module has a dependency on ``pim/user-context`` and that we are exposing this object.

The real thing
++++++++++++++

Here is how to do it using RequireJS:

.. code-block:: javascript
    :linenos:

    // src/Acme/Bundle/CustomBundle/Resources/public/js/locale_logger.js
    define(
        ['pim/user-context'],
        function (UserContext) {
            return {
                logLocale: function () {
                    console.log(UserContext.get('uiLocale'));
                }
            };
        }
    );


The ``define`` method takes two arguments: the first one is an array of the dependencies we want to use (``pim/user-context`` in this case) and a callback that will be called every time our module is requested. This callback will receive as argument the list of dependencies we asked for. Note that you can name the arguments of the callback as you want.

Register it
+++++++++++

Now that we have this small module, we would like to use it somewhere else. To do so, we need to register it in the RequireJS configuration file.

.. code-block:: yaml

    # src/Acme/Bundle/CustomBundle/Resources/config/requirejs.yml
    config:
        paths:
            my_locale_logger: acmecustom/js/locale_logger


We just declared a new module named ``my_locale_logger`` located in ``web/bundles/acmecustom/js/locale_logger.js``.
Note that we don't need to specify the file extension and it is located in the public web folder (moved during the ``bin/console assets:install`` command execution). Also, ``my_locale_logger`` name is an arbitrary name and you can replace it with whatever you want.

Make it available
+++++++++++++++++

To make your module available you need to clear your cache:

.. code-block:: bash

    rm -rf ./var/cache/*


dump the assets in the web folder

.. code-block:: bash

    bin/console assets:install web


You can also decide to symlink those assets instead of copying them:

.. code-block:: bash

    bin/console assets:install --symlink web


After that the last step is to build the bundle.js file

.. code-block:: bash

    yarn run webpack


This command will compile and minify all the pim files into web files and dump them in the public folder.

You can also use the watch command which will recompile this file each time you modify a registered module.

.. code-block:: bash

    yarn run webpack-watch


Summary
+++++++

We just created our first module and made it available for other parts of the application. You can now use it by requirering ``my_locale_logger``. We cannot use it know but we will see that in a minute.

Create our first Backbone view
==============================

Basic view
++++++++++

Now that we know how to create a RequireJS module, let's create our first Backbone view.

.. code-block:: javascript
    :linenos:

    // src/Acme/Bundle/CustomBundle/Resources/public/js/new_view.js
    define(
        ['backbone'],
        function (Backbone) {
            return Backbone.View.extend({

            });
        }
    );


This is the simplest view we can create using Backbone. It will result in an empty div if we add it to the DOM.

The real stuff
++++++++++++++

Let's add some more interesting stuff to it:

.. code-block:: javascript
    :linenos:

    // src/Acme/Bundle/CustomBundle/Resources/public/js/new_view.js
    define(
        ['backbone'],
        function (Backbone) {
            return Backbone.View.extend({
                events: {
                    'click': 'clicked'
                },

                render: function () {
                    this.$el.html('<div>Hello world</div>')
                },

                clicked: function (event) {
                    console.log(event);
                }
            });
        }
    );


In this code we do three things:

- We override the ``render`` method to add custom render logic. In this example, we add a div inside our view with a simple text inside it.
- We declare an event listener to listen to the click events on our view to call the `clicked` method
- The clicked method will log the DOM click event each time a click is triggered on our view.

You can find more information about Backbone views on the `dedicated documentation <http://backbonejs.org/#View>`_


Register it
+++++++++++

Now that we have a view, we need to register it in the ``requirejs.yml`` file

.. code-block:: yaml

    # src/Acme/Bundle/CustomBundle/Resources/config/requirejs.yml
    config:
        paths:
            my_locale_logger: acmecustom/js/locale_logger
            my_custom_view: acmecustom/js/new_view


After cache clearing, asset dump and webpack build, you should be able to use your newly created view.

Create our first form extension
===============================

Now that we can create a RequireJS module and a Backbone view, we would like to actually customize the PIM to add our own business logic on top of it.

A little bit of history
+++++++++++++++++++++++

As we are an open source company, our product can be used and customized for a lot of different reasons and by a lot of different people (integrators, clients, contributors and technological partners). With this in mind, it was impossible to base our architecture on overrides to customize it.

We decided to create a tree-based architecture where each form of the application would be a tree of extensions. If you want to change a part of a page or add something to it, you need to create a RequireJS module and register it in the tree of form extensions.

The form extension
++++++++++++++++++

To continue on our example we can use our previously created Backbone view to transform it into a form extension.

.. code-block:: javascript
    :linenos:

    // src/Acme/Bundle/CustomBundle/Resources/public/js/new_view.js
    define(
        ['pim/form'],
        function (BaseForm) {
            return BaseForm.extend({
                events: {
                    'click': 'clicked'
                },

                render: function () {
                    this.$el.html('<div>Hello world</div>')
                },

                clicked: function (event) {
                    console.log(event);
                }
            });
        }
    );


As you can see, we haven't changed much: we now extend the BaseForm instead of the Backbone.View. As BaseForm extends itself the ``Backbone.View``, everything works as before and you can use all Backbone features.

Register it
+++++++++++

Now that we created our form extension we need to register it:

.. code-block:: yaml

    # src/Acme/Bundle/CustomBundle/Resources/config/form_extensions.yml
    extensions:
        my_form_extension:
            module: my_custom_view             # Your RequireJS module name
            parent: pim-product-edit-form-meta # The parent of your extension (the meta section of the product edit form in this case)


A fiew words about this small configuration:

- the key ``my_form_extension`` should be a unique key to represent your form extension.
- you can declare multiple form extensions with the same RequireJS module.
- to be registered, your configuration file have to be named ``form_extensions.yml`` or put in a ``form_extensions`` folder in your bundle ``Resources/config`` folder.
- You can override any form extension by using the same unique key (the order of the override is defined by your ``AppKernel.php`` registration order).

This configuration is the minimal example. Here are the other parameters that you can use:

.. code-block:: yaml

    # src/Acme/Bundle/CustomBundle/Resources/config/form_extensions.yml
    extensions:
        my_form_extension:
            module: my_custom_view
            parent: pim-product-edit-form-meta
            targetZone: header
            position: 90
            aclResourceId: pim_catalog_product_edit
            config:
                here: you_can_put
                whatever: you_want


What does it mean?
++++++++++++++++++

First you must specify a key (``my_form_extension``). As stated above it must be unique in your application.

Then, each view has the following properties:

- **module**: It's the view module that will be rendered. The value is the key declared in the ``requirejs.yml`` file for this module.
- **parent**: As forms are trees, each view must declare its parent (except the root obviously). The value is the key of the parent, that's why keys must be unique.
- **targetZone**: Views can have different zones. When a child wants to register itself in a parent, it can choose in which zone to be appended. Zones are defined by a ``data-drop-zone`` attribute in the DOM.
- **position**: When several views are registered in the same parent for the same zone, they are ordered following their positions, in ascending order.
- **aclResourceId**: If the current user doesn't have this ACL granted, the view (and all its children) won't be included in the final tree.
- **config**: A free key to pass parameters to your view explained below

The last key of our module (``config``) is used to pass the configuration you want to the RequireJS module. You can get it in the initialize method of your module:

.. code-block:: javascript
    :linenos:

    /**
     * {@inheritdoc}
     */
    initialize: function (meta) {
        this.meta = meta.config;

        BaseForm.prototype.initialize.apply(this, arguments);
    }


As you can see, we receive the entire configuration via the constructor of our view.

Now that our extension is registered, we need to clear the Symfony cache and we are good to go. You should see your extension in the meta section of the product edit form now.

Some extra features
===================

Useful methods
++++++++++++++

Here are a list of methods that you can override or call in your extension that should make your life easier.

Managing the model
******************

Each form has an internal model representing the current object we are modifying. Here is the way to access it and modify it

.. code-block:: javascript
    :linenos:

    BaseForm.extend({
        render: function () {
            // You can access it from any method, this is just an example

            const model = this.getFormData();

            model.hello = 'world';

            this.setData(model);
        }
    });


As you can see, we can get the model by calling ``this.getFormData()`` from any extension and update the model with ``this.setData()``. Note that calling setData will trigger the event ``pim_enrich:form:entity:(pre|post)_update`` on the root view. You can pass the option ``silent`` to true to avoid triggering it (``this.setData(data, {silent: true})``).


.. note::

    Those two methods will in fact call ``this.getRoot().setData()`` and ``this.getRoot().getFormData()`` so anywhere in the tree you can get and set the common model.

Configure your extension
************************

Sometimes, you want to perform actions before the first render (fetch information, do heavy computation, etc). The configure method is perfect for this need.

.. code-block:: javascript
    :linenos:

    BaseForm.extend({
        configure: function () {
            return $.when(function () {
                return $.get('my_url').then(function (elements) {
                    this.elements = elements;
                }.bind(this));
            }, BaseForm.prototype.configure.apply(this, arguments));
        }
    });


As you can see, the configure method should return a promise. We do that because we want this method to be blocking before the first rendering of the view. We also need to call the parent configure method to configure basic behaviour.

Know your ancestors
*******************

As an extension, you can access your parent by calling ``this.getParent()`` and ``this.getRoot()`` to get the root extension of the form.
