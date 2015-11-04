Application Technical Dependencies
==================================

Akeneo PIM application relies on several web technologies to work properly. We list here all the various technologies used by the application to deliver the best user experience.

Backend technologies
--------------------

**Symfony and PHP**

The application relies on Symfony framework. This web framework allows Akeneo PIM application to be built on a solid structure with a wonderful ecosystem of bundles and libraries.
This means that Akeneo PIM  installation process will use composer to install or update all these dependencies easily.

+---------+-------------------------------------------------------+------------------+
| PHP     | ≥ 5.4.4 (Apache mod_php, no CGI, no Fast CGI nor FPM) | Required         |
+---------+-------------------------------------------------------+------------------+
| Symfony | 2.7                                                   | Shipped with PIM |
+---------+-------------------------------------------------------+------------------+

**Main Symfony bundles**

+------------------------+-------------------------------------------------------------+
| Oro Platform           | Open source Business Application Platform                   |
+------------------------+-------------------------------------------------------------+
| Doctrine               | ORM (and/or ODM) to abstract interactions with databases    |
+------------------------+-------------------------------------------------------------+
| KNP Menu               | Manages menus and navigation more easily                    |
+------------------------+-------------------------------------------------------------+
| KNP Paginator          | Manages data pagination                                     |
+------------------------+-------------------------------------------------------------+
| KNP Gaufrette          | Filesystem abstraction layer                                |
+------------------------+-------------------------------------------------------------+
| Liip Imagine           | Manages image manipulation and caching                      |
+------------------------+-------------------------------------------------------------+
| APY JSFV               | Form validation provided on the frontend side               |
+------------------------+-------------------------------------------------------------+
| JMS Serializer         | Manages data serialization and deserialization              |
+------------------------+-------------------------------------------------------------+
| FOS REST               | Manages REST interactions                                   |
+------------------------+-------------------------------------------------------------+
| Genemu Form            | Adds more complex form widgets                              |
+------------------------+-------------------------------------------------------------+
| A2lix Translation Form | Allows better translations management in forms              |
+------------------------+-------------------------------------------------------------+
| FOS JS Routing         | Allows to expose application routing on the JavaScript side |
+------------------------+-------------------------------------------------------------+
| Leafo LessPHP          | Converts LESS stylesheets to CSS stylesheets                |
+------------------------+-------------------------------------------------------------+

Testing libraries
-----------------

To ensure the best possible quality of our product and avoid any regressions we created unit and functional tests using various libraries such as:

+---------+-----------------------------------------+
| PHPSpec | Intuitive unit testing with mocking     |
+---------+-----------------------------------------+
| Behat   | Functional testing                      |
+---------+-----------------------------------------+
| PHPUnit | Basic but powerful unit testing library |
+---------+-----------------------------------------+

Frontend technologies
---------------------

**Javascript dependencies**

+-----------------+-----------------------------------------------------------------+
| Backbone.js     | Data binding framework                                          |
+-----------------+-----------------------------------------------------------------+
| Require.js      | Dependency loader                                               |
+-----------------+-----------------------------------------------------------------+
| Underscore.js   | Useful toolkits library                                         |
+-----------------+-----------------------------------------------------------------+
| Bootstrap (2.3) | Frontend framework                                              |
+-----------------+-----------------------------------------------------------------+
| jQuery Frontend | library used mostly for DOM interaction                         |
+-----------------+-----------------------------------------------------------------+
| jQuery UI       | Frontend library to improve user experience with better widgets |
+-----------------+-----------------------------------------------------------------+
| Various widgets | TinyMCE (v4), Select2 (v3), etc.                                |
+-----------------+-----------------------------------------------------------------+

**Styling dependencies**

As mentioned in the Symfony bundles dependencies above, we use LESS stylesheets to provide developers with a smarter way to manage styles than bare CSS stylesheets.

+-----------+-----------------------------------+
| Bootstrap | Twitter Bootstrap CSS stylesheets |
+-----------+-----------------------------------+
| jQuery UI | jQuery UI CSS stylesheets         |
+-----------+-----------------------------------+


