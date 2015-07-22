How to Validate Products
========================

Instantiate the validator
-------------------------

The validator is a service, you can fetch it from the container

.. code-block:: php

    $validator = $this->getContainer()->get('pim_catalog.validator.product');

The validator that we use for products is quite special, for other objects, we use the service `validator`.

The service `pim_catalog.validator.product` receives Constraints from yaml files (standard Symfony) and also from attributes configuration, to do so we use our own `DelegatingClassMetadataFactory`.

Validate the product
--------------------

Then you can validate your product, all business validation are applied here.

.. code-block:: php

    $validator->validate($product);

It returns here a list of violation errors, a 'Symfony\Component\Validator\ConstraintViolationList'.
