PHP7 and HHVM Compatibility?
============================

PHP7
----

We continued our effort regarding Akeneo PIM PHP7 support.

We're happy to announce that PHP7 is now usable in experimental mode for both CLI and Web, for both ORM and MongoDB storages.

Experimental means that we manage to install and use the PIM but due to missing tests in our functional matrix we can't commit to officially supporting it (for now).

This modification introduces a new constraint, the minimum version of PHP for Akeneo PIM is now PHP 5.6 (due to our dependencies, we had to choose between <= PHP 5.6 or >= PHP 5.6).

Akeneo PIM is **still not production ready with PHP7 but we're definitely getting closer.**

HHVM
----

Will not be supported.
