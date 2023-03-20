First aid kit
=============

Here is our first aid kit if you encounter a bug on your PIM project.

Step 1: are you sure that it's a bug?
-------------------------------------

Sometimes, it's quite clear that the behaviour you experience is a bug. But it is not always the case. Some behaviours can occur because you missed something (for instance a permissions misconfiguration), and the PIM just works as designed.

If you have doubts, please have a look at the following resources. If not, go to the next step.

* `The PIM Help Center <https://help.akeneo.com/en_US/v3/index.html>`_
* `The PIM online API documentation <https://api.akeneo.com/>`_

.. tip::

    Help Center can be accessed at any moment from the **(?) Help** icon on the bottom left corner of the PIM.

Step 2: is your computer ready to use the PIM?
----------------------------------------------

There are some client side requirements to use the PIM. Please check that everything is OK by looking at :doc:`/technical_architecture/technical_information/client_side_compatibilities`.

Step 3: are the system requirements still OK?
---------------------------------------------

Maybe something has been changed in your server. You can check that by running the following command:

.. code-block:: bash

    cd /path/to/your/pim/
    php bin/console pim:installer:check-requirements

.. note::

    All our system requirements can also be found in this documentation:
    :doc:`/install_pim/manual/system_requirements/system_requirements`.

In addition, check that:

* The Xdebug PHP extension is well deactivated.
* The PIM commands are well run in prod mode instead of dev mode (See: `Symfony documentation: Selecting the Environment for Console Commands <https://symfony.com/doc/5.4/configuration.html#selecting-the-active-environment>`_)
* Your crontab scheduled PIM commands are not launched too frequently

Step 4: what about your infrastructure?
---------------------------------------

Depending on your infrastructure, you may need to check some points:

* Make sure you are not experiencing latency issues on your local network.
* Make sure you don't have an issue with one of your network equipments. For instance with a proxy server.


Step 5: is your PIM up-to-date?
-------------------------------

Make sure that you applied the latest patches available for your PIM version. You can check your current PIM version at the bottom of the PIM login or dashboard pages.

.. tip::

    The :doc:`/migrate_pim/index` documentation explains how to update your PIM to the most recent minor version.

Step 6: are your additional bundles up-to-date?
-----------------------------------------------

As for the PIM it's important to check that the additional bundles you use are up-to-date.

If you need assistance, please refer to each bundle's documentation.

Step 7: are your assets properly deployed?
------------------------------------------

.. tip::

    This step is recommended if you encounter user interface issues like javascript errors or display problems.

From a technical point of view, assets are all the javascript, css and media files used by the PIM itself, by its dependencies, and eventually by your custom developments.

To make sure everything is OK, you can run the following commands:

.. include:: deploy_assets.rst.inc

One last thing, clear your browser's cache:

.. include:: clear_browser_cache.rst.inc

Step 8: did you clear the cache?
--------------------------------

Clear the PIM cache (also known as "Symfony cache") by running the following commands:

.. code-block:: bash

    cd /path/to/your/pim/
    php bin/console cache:clear --env=prod --no-warmup


You could use the command `partners_clear_cache` if you are on a Akeneo Cloud Offer. You can find more commands in our `_System Administration & Services Management page <https://docs.akeneo.com/4.0/cloud_edition/flexibility_mode/docs/system_administration.html>`__.

Step 9: did you consider the volume of your catalog?
----------------------------------------------------

.. tip::

    This step is recommended if you encounter performances issues.

Each catalog is unique. How many channels, locales, attributes, families, categories, products and users do you have? These values may had increased drastically since you began using your PIM.

Most of this information can be found on the PIM under System > System information. Since 2.3 version, you have a dedicated page available under Activity menu, called Catalog Volume Monitoring, see our `_Help Center article <https://help.akeneo.com/v3-discover-akeneo-concepts/24-v3-monitor-your-catalog-size#a-dedicated-screen>`__ for more information about this page and how to read it.

You can also read this Medium article `_Because your product catalog typology matters <https://medium.com/akeneo-labs/because-your-product-catalog-typology-matters-e1a9af4c33e0>`__ to see the impacts catalog modeling can have on PIM performances.

Step 10: did you customize your PIM?
------------------------------------

.. warning::

    If the previous steps failed to solve the bug, try this one.

Disable all custom developments by commenting them in the "AppKernel.php" file ``/path/to/your/pim/app/AppKernel.php``.

.. code-block:: php

    // your app bundles should be registered here
    // new YourCompagny\Bundle\AppBundle\YourCompagnyCustomBundle(),

And then, re-apply `Step 7: are your assets properly deployed?`_ and `Step 8: did you clear the cache?`_

Does the bug persist?
---------------------

Sorry to hear that. It seems it's :doc:`/maintain_pim/bug_qualification/index` time.
