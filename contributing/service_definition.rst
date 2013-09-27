Service Definition Convention
=============================

Service Definition
------------------

We defined the services in several files depending of the service purpose (form type, manager, etc.).

In each of these, a service is named with the following convention :

.. code-block:: yaml

  <pim_bundle>.<directory>.<entity>

For example, the product manager service is `pim_catalog.manager.product`.

You can have several directories separated by dot like `pim_catalog.form.type.product`.

Parameters Definition
---------------------

Parameters are defined in a specific file of the bundle, named `parameters.yml`. All used classes must defined in this 
file.

The convention used is the same as the one for services, with a `.class` suffix

The parameter for the product manager service class is `pim_catalog.manager.product.class`
