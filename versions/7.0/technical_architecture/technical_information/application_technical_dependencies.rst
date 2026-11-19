Application Technical Dependencies
==================================

Akeneo PIM application relies on several web technologies to work properly. We list here all the various technologies used by the application to deliver the best user experience.

Backend technologies
--------------------

**Symfony and PHP**

The application relies on Symfony framework. This web framework allows Akeneo PIM application to be built on a solid structure with a wonderful ecosystem of bundles and libraries.
This means that Akeneo PIM  installation process will use composer to install or update all these dependencies easily.

+---------+-----------------------------------------------------+------------------+
| PHP     | 8.1 (Apache Fast CGI with FPM, no CGI, nor mod_php) | Required         |
+---------+-----------------------------------------------------+------------------+
| Symfony | 5.4                                                 | Shipped with PIM |
+---------+-----------------------------------------------------+------------------+

**Main Symfony bundles**

+------------------------+--------------------------------------------------------------------------------+
| Oro Platform           | Open source Business Application Platform                                      |
+------------------------+--------------------------------------------------------------------------------+
| Doctrine               | ORM (and/or ODM) to abstract interactions with databases                       |
+------------------------+--------------------------------------------------------------------------------+
| League Flysystem       | Filesystem abstraction layer                                                   |
+------------------------+--------------------------------------------------------------------------------+
| FOS OAuth Server       | Server side OAuth2 Bundle for Symfony2                                         |
+------------------------+--------------------------------------------------------------------------------+
| FOS REST               | Manages REST interactions                                                      |
+------------------------+--------------------------------------------------------------------------------+
| Monolog                | Sends your logs to files, sockets, inboxes, databases and various web services |
+------------------------+--------------------------------------------------------------------------------+

Testing libraries
-----------------

To ensure the best possible quality of our product and avoid any regressions we created unit, integration and functional tests using various libraries such as:

+---------+-------------------------------------------------------------------------+
| PHPSpec | Intuitive unit testing with mocking (for unit tests and classes design) |
+---------+-------------------------------------------------------------------------+
| Behat   | Behavior Driven Development framework (for functional tests)            |
+---------+-------------------------------------------------------------------------+
| PHPUnit | Basic but powerful unit testing library (for integration tests)         |
+---------+-------------------------------------------------------------------------+

Frontend technologies
---------------------

**Javascript dependencies**

+-----------------+-----------------------------------------------------------------+
| Backbone.js     | Data binding framework                                          |
+-----------------+-----------------------------------------------------------------+
| React.js        | A javascript library for building user interfaces               |
+-----------------+-----------------------------------------------------------------+
| Webpack         | Dependency loader                                               |
+-----------------+-----------------------------------------------------------------+
| Underscore.js   | Useful toolkits library                                         |
+-----------------+-----------------------------------------------------------------+
| Bootstrap (2.3) | Frontend framework                                              |
+-----------------+-----------------------------------------------------------------+
| jQuery Frontend | Library used mostly for DOM interaction                         |
+-----------------+-----------------------------------------------------------------+
| jQuery UI       | Frontend library to improve user experience with better widgets |
+-----------------+-----------------------------------------------------------------+
| Various widgets | Select2 (v3), etc.                                              |
+-----------------+-----------------------------------------------------------------+

**Styling dependencies**

As mentioned in the Symfony bundles dependencies above, we use LESS stylesheets to provide developers with a smarter way to manage styles than bare CSS stylesheets.

+-----------+-----------------------------------+
| Bootstrap | Twitter Bootstrap CSS stylesheets |
+-----------+-----------------------------------+
| jQuery UI | jQuery UI CSS stylesheets         |
+-----------+-----------------------------------+
