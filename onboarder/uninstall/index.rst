How to uninstall the Onboarder bundle
=====================================

Clean related data
------------------

There are several related data created by Onboarder:

- Supplier & supplier reference attributes
- The Onboarder channel
- Product values

You need to run this command to clean them:

.. code-block:: bash

    bin/console akeneo:onboarder:deactivate
    bin/console akeneo:onboarder:clean-bundle-data

.. warning::

    Onboarder ACL is stored in a yml file and not in the database. So the `Akeneo Onboarder` permission will still be displayed after running this command.
