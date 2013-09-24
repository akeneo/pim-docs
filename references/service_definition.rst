Service definition convention
=============================

Service definition
------------------

We defined services in several files depending of the service purpose (form type, manager, etc.).

In each of these, a service is named with the following convention :

.. code-block:: yaml

  <pim_bundle>.<directory>.<entity>

For example, the product manager service is pim_catalog.manager.product

You can have several directories separated by dot like "pim_catalog.form.type.product"

Parameters definition
---------------------

Parameters are defined in a specific file named parameters.yml and all class used must defined in this one to favorize overriding

The convention used is the same than for services with .class suffix

By the way, product manager service class parameter is "pim_catalog.manager.product_class"
