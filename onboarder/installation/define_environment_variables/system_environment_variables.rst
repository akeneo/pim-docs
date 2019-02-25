Using environment variables
===========================

Here are some examples to manage environment variables, the way to define environment variables may vary depending on your server installation and your deployment/update strategy.

**CLI**

Define the environment variables for CLI. Ensure these variables are always set using the export command :

.. code-block:: bash

    export ONBOARDER_TOPIC_NAME_FOR_PUBLICATION=a_topic_name

**WEB**

Define the environment variables in your vhost using the SetEnv directive :

.. code-block:: apache

    <VirtualHost *>
        SetEnv ONBOARDER_TOPIC_NAME_FOR_PUBLICATION a_topic_name
    </VirtualHost>