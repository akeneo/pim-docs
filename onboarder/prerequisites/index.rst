Prerequisites
=============

All the Prerequisites listed below have to be fulfilled before the installation step.

+------------------------------------------------+--------------------------------------------------------------------------------------------------------+
| Context                                        | Prerequisites                                                                                          |
+================================================+========================================================================================================+
| :ref:`PIM <onboarder-prereq-pim>`              | Your Akeneo PIM is an enterprise edition                                                               |
+                                                +--------------------------------------------------------------------------------------------------------+
|                                                | Your Akeneo PIM version is >= 2.3.9 and <3.0                                                           |
+------------------------------------------------+--------------------------------------------------------------------------------------------------------+
| :ref:`Synchronization <onboarder-prereq-sync>` | You've received from the Akeneo team the Onboarder parameters package                                  |
+                                                +--------------------------------------------------------------------------------------------------------+
|                                                | You are able to launch a daemonized command in your server (a process that needs to be always started) |
+                                                +--------------------------------------------------------------------------------------------------------+
|                                                | The HTTP traffic must be allowed on port 443 for the google pubsub api pubsub.googleapis.com           |
+                                                +--------------------------------------------------------------------------------------------------------+
|                                                | The HTTP traffic must be allowed on port 443 for the google storage api www.googleapis.com             |
+------------------------------------------------+--------------------------------------------------------------------------------------------------------+

.. _onboarder-prereq-pim:

PIM
---

The Onboarder is compatible with enterprise edition from >= 2.3.9 to <3.0

.. _onboarder-prereq-sync:

Synchronization
---------------

Parameters package
^^^^^^^^^^^^^^^^^^

The parameters package provided by the Akeneo team must contain:

* A ``serviceAccount.json`` file that contains authentication values to be able to use the Google PubSub and Google Cloud Storage services.
* Several environments variables values that you will configure in the install process in order to communicate with Google Cloud PubSub instance
*
    * ``ONBOARDER_TOPIC_NAME_FOR_PUBLICATION_TO_MIDDLEWARE``: the topic where the Akeneo PIM will publish synchronization messages to retailer onboarder
    * ``ONBOARDER_TOPIC_NAME_FOR_PUBLICATION_TO_ONBOARDER``: the topic where the Akeneo PIM will publish synchronization messages to supplier onboarder
    * ``ONBOARDER_TOPIC_NAME_FOR_CONSUMPTION``: the topic where the Akeneo PIM will subscribe
    * ``ONBOARDER_QUEUE_NAME``: the queue where the Akeneo PIM will consume synchronization messages
* ``ONBOARDER_CLOUD_STORAGE_BUCKET_NAME``: name of the bucket where the assets will be synchronized
