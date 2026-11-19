Create a project
================

.. warning::

    Here is a very early version of this section, we'll keep enriching it in the upcoming weeks.

Setup the project
-----------------

The Akeneo PIM standard project comes with a default configuration to help you during the development.

Please install it by following the :doc:`/install_pim/index` process.

This distribution contains an empty src folder, your custom code will reside here.

What about versioning?
----------------------

You should use a version control system (VCS) for your project, for instance, our team uses Git with Github.

The `.gitignore` file located in the root folder of your project defines what is NOT to be versioned.

If you use another VCS, you should follow the guideline provided by this file to know what to version or not.

Here are some explanations of the most important points:

* Do **NOT** modify the code in the vendors

The next time you update them with composer, your modifications would be lost.

* Do **NOT** version vendors
* Do **NOT** fork your vendors

You would not benefit from dependencies patches otherwise.

What about your dependencies?
-----------------------------

In your composer.json, you should try to rely as much as possible on stable dependencies (tags) when installing extra dependencies.

Once installed don't hesitate to version your composer.lock. This practice ensures that all of your coworkers (or servers) will use the exact same version of all dependencies.
