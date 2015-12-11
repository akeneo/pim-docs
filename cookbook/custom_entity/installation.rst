Install the Custom Entity Bundle
================================

Add the custom entity bundle into your project dependencies by running:

.. code-block:: bash

   composer require akeneo/custom-entity-bundle 1.5.x-dev@dev

Then you have to update your **app/AppKernel.php** file :

.. code-block:: php

  <?php
  // app/AppKernel.php

  protected function getAdditionalBundles()
  {
      return [
          // ...
          new Pim\Bundle\CustomEntityBundle\PimCustomEntityBundle(),
      ];
  }
  // ...
  ?>

And your **routing.yml** file by putting this content at **the very end** of the file:

.. code-block:: yaml

  # app/config/routing.yml

  pim_customentity:
      resource: "@PimCustomEntityBundle/Resources/config/routing.yml"
      prefix: /enrich

Everything's now set!
