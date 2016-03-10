Common Best Practices
=====================

The Akeneo PIM standard project comes with a default configuration to help you during the development.


What about versioning?
----------------------

The `.gitignore` file defines what is NOT to be versioned.
You have to follow this file if you want to use another versioning system.
Here are some explanations of the most important points:

* Do **NOT** modify the code in the vendors
The next time you will update them with composer, your modifications would be lost.

* Do **NOT** version vendors
* Do **NOT** fork your vendors
You would not benefit of dependencies patches otherwise.


What about your dependencies?
-----------------------------

* Try to rely on stable dependencies (tags)

If you're using Enterprise Edition:
* You MUST update Community Edition AND Enterprise Edition.
