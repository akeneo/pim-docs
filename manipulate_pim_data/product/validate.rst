How to Validate Products
========================

Instantiate the validator
-------------------------

The validator is a service, you can fetch it from the container

.. code-block:: php

    $validator = $this->getContainer()->get('pim_catalog.validator.product');

The validator that we use for products is quite special, for other objects we use the Symfony classic `validator`.

The service `pim_catalog.validator.product` receives Constraints from yaml files (standard Symfony) and also from attributes configuration, to do so we use our own `DelegatingClassMetadataFactory`.

Validate the product
--------------------

Now you can validate your product, all business validations are applied here.

.. code-block:: php

    $violations = $validator->validate($product);

In return we get a list of violation errors, a 'Symfony\Component\Validator\ConstraintViolationList'.
