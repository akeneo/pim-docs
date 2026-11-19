Prerequisites
=============

All the Prerequisites listed below have to be fulfilled before the installation step.

+------------------------------------------------+--------------------------------------------------------------------------------------------------------+
| Context                                        | Prerequisites                                                                                          |
+================================================+========================================================================================================+
| :ref:`PIM <onboarder-prereq-pim>`              | Your Akeneo PIM is an enterprise edition                                                               |
+                                                +--------------------------------------------------------------------------------------------------------+
|                                                | Your Akeneo PIM version is >= 7.0                                                                      |
+------------------------------------------------+--------------------------------------------------------------------------------------------------------+
| :ref:`Synchronization <onboarder-prereq-sync>` | The Onboarder parameters are available on your Partner Portal project page                             |
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

The Onboarder is compatible with the enterprise edition of the PIM 7.0 version. If you have a PIM 6.0, please see the 
Onboarder documentation `v6.0` (you can change the version with the dropdown in the top level menu).

.. _onboarder-prereq-sync:

Synchronization
---------------

Parameters package
^^^^^^^^^^^^^^^^^^

The parameters provided on the Partner Portal must contain:

* A downloadable ``serviceAccount.json`` file that contains authentication values to be able to use the Google PubSub and Google Cloud Storage services.
* Several environments variables values that you will configure in the install process in order to communicate with Google Cloud PubSub instance
*
    * ``FLAG_ONBOARDER_ENABLED``: the feature flag that allows the activation of the Retailer Onboarder
    * ``ONBOARDER_TOPIC_NAME_FOR_PUBLICATION_TO_MIDDLEWARE``: the topic into wich Akeneo PIM will publish synchronization messages to middleware
    * ``ONBOARDER_TOPIC_NAME_FOR_PUBLICATION_TO_ONBOARDER``: the topic into wich Akeneo PIM will publish synchronization messages to supplier onboarder
    * ``ONBOARDER_TOPIC_NAME_FOR_CONSUMPTION``: the topic Akeneo PIM will subscribe to
    * ``ONBOARDER_QUEUE_NAME``: the queue Akeneo PIM will consume synchronization messages from
    * ``ONBOARDER_CLOUD_STORAGE_BUCKET_NAME``: the name of the bucket where the assets will be synchronized
