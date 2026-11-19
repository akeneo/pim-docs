PHP7 and HHVM Compatibility?
============================

We continue to do several compatibility tests for PHP7 and HHVM.

We expect very important performance improvement with the support of PHP7, especialy for large backend processes, such as a product import for instance.

Our dependencies are becoming more and more compliant (still questions around php mongodb driver) and our Continuous Integration is configured to run our unit tests suites with PHP 5.4, 5.5, 5.6, 7.0 and HHVM cf https://travis-ci.org/akeneo/pim-community-dev

Akeneo PIM is **still not production ready with PHP7 and HHVM but we're definitely getting closer.**
