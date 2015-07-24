How to Validate Non-Product Objects
===================================

To validate an object, we use a classic Symfony Validator with Constraints defined in yaml files.

Use the Validator
-----------------

The validator is a service, you can fetch it from the container

.. code-block:: php

    $validator = $this->getContainer()->get('validator');

When you can validate an attribute, a family or another object, all business validations are applied.

.. code-block:: php

    $violations = $validator->validate($family);
    $violations = $validator->validate($attribute);
    // ...

It returns here a list of violation errors, a 'Symfony\Component\Validator\ConstraintViolationList'.

.. warning::

    The validation of a product is a bit different, you should use its own validator, the service alias is  `pim_catalog.validator.product`.