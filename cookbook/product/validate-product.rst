How to Validate Products
========================

Instantiate the validator
-------------------------

The validator is a service, you can fetch it from the container

.. code-block:: php

    $validator = $this->getContainer()->get('pim_validator');

Validate the product
--------------------

.. code-block:: php

    $validator->validate($product);

The validator returns the list of violation errors (`Symfony\Component\Validator\ConstraintViolationList`).
