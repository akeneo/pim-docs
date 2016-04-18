Setting-up the Hybrid Storage MySQL/MongoDB
===========================================

Prerequisites
*************

If you are here, it means you already sort out that you need to enable the Hybrid Storage Mysql/MongoDB. If not, please read the :doc:`/developer_guide/installation/system_requirements/system_requirements` section. It also means that you have installed MongoDB alongside with its PHP driver.

Installing and enabling MongoDB support in Akeneo
*************************************************

* Install the required dependency

.. code-block:: bash
    :linenos:

    $ cd /path/to/installation/pim-community-standard
    $ php ../composer.phar --prefer-dist require doctrine/mongodb-odm-bundle 3.0.1

* In ``app/AppKernel.php``, uncomment the following line (this will enable ``DoctrineMongoDBBundle`` and will load and enable the MongoDB configuration):

.. code-block:: bash
    :linenos:

    $ gedit app/AppKernel.php
    new Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle(),

* Set MongoDB server configuration at the end of the configuration file

.. code-block:: bash
    :linenos:

    $ gedit app/config/pim_parameters.yml

    pim_catalog_product_storage_driver: doctrine/mongodb-odm

    mongodb_server: 'mongodb://localhost:27017'
    mongodb_database: your_mongo_database
