Prerequisites
=============

All the Prerequisites listed below have to be fulfilled before the installation step.

+------------------------------------------------+--------------------------------------------------------------------------------------------------------+
| Context                                        | Prerequisites                                                                                          |
+================================================+========================================================================================================+
| :ref:`PIM <onboarder-prereq-pim>`              | Your Akeneo PIM is an enterprise edition                                                               |
+                                                +--------------------------------------------------------------------------------------------------------+
|                                                | Your Akeneo PIM version is above 2.3.9                                                                 |
+------------------------------------------------+--------------------------------------------------------------------------------------------------------+
| :ref:`SSO <onboarder-prereq-pim>`              | Your Akeneo PIM instance is accessible through public internet network                                 |
+                                                +--------------------------------------------------------------------------------------------------------+
|                                                | You've communicated the public url to the akeneo cloud team                                            |
+------------------------------------------------+--------------------------------------------------------------------------------------------------------+
| :ref:`Synchronization <onboarder-prereq-sync>` | You've received from the akeneo cloud team the onboarder parameters package                            |
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

The onboarder feature is only compatible with enterprise edition on a 2.3.9 version

.. _onboarder-prereq-sso:

SSO
---

| The Single Sign On process is the only authentication mechanism that is available for the onboarder in order to create on the fly the user on the retailer onboarder application and to ease the user experience between the Akeneo PIM and the retailer onboarder.
| In order to implement the SAMLv2 protocol the Akeneo PIM and the retailer onboarder must be able to communicate through http calls.

The parameters package provided by the akeneo cloud team must contain:

* A pair of public and private key needed for signing the communication during the authentication process (pimmaster.crt and pimmaster.pem)

.. _onboarder-prereq-sync:

Synchronization
---------------

Parameters package
^^^^^^^^^^^^^^^^^^

The parameters package provided by the akeneo cloud team must contain:

* A `serviceAccount.json` file that contains authentication values to be able to use the Google PubSub and Google Cloud Storage services
* Several environments variables values that you will configure in the install process in order to communicate with Google Cloud PubSub instance  
    * `ONBOARDER_TOPIC_NAME_FOR_PUBLICATION` -> the topic where the Akeneo Pim will publish synchronization messages
    * `ONBOARDER_TOPIC_NAME_FOR_CONSUMPTION` -> the topic where the Akeneo Pim will subscribe
    * `ONBOARDER_QUEUE_NAME` -> the queue where the Akeneo Pim will consume synchronization messages
* `ONBOARDER_RETAILER_URL` -> url of the onboarder retailer, needed for SSO implementation
* `ONBOARDER_CLOUD_STORAGE_BUCKET_NAME` -> name of the bucket where the assets will be synchronized

Worker
^^^^^^
| The synchronization of the catalog structure and the catalog data is handle in a asynchronous way using the Google Cloud PubSub service.
| The messages that are part of the synchronization process are queued. 
| The queue is consumed by a command line process called `worker`.
| 
| The worker have to be always launched as it poll the queue waiting for new messages to handle.
| The akeneo/pim-onboarder extension comes with a `supervisor <http://supervisord.org/>` configuration example to manage this worker to be always running but it's up to you to manage this another way.

