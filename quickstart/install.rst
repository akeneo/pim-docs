Install 
=======

Clone repository
----------------

I want install my own PIM instance to customize it :

.. code-block:: bash

    $ git clone git@github.com:akeneo/pim-ce.git

I want install my own PIM with demo data : 

.. code-block:: bash

    $ git clone git@github.com:akeneo/pim-ce-demo.git

I want install the PIM to contribute :

.. code-block:: bash

    $ git clone git@github.com:akeneo/pim-dev.git

Check your PHP configuration
----------------------------

.. code-block:: bash

    $ php app/check.php

Install dependencies
--------------------

First, install composer :

.. code-block:: bash

    $ curl -sS https://getcomposer.org/installer | php

Then use it to install the dependencies, the prompt will ask you for configuration parameters :

.. code-block:: bash

    $ php composer.phar install

Check rights
------------

TODO


Setup database
--------------

The following script helps to create database, schema and load demo data if you're using the demo bundle :

.. code-block:: bash

    $ ./init-db.sh

Deploy assets
-------------

The following script helps to properly deploy assets  :

.. code-block:: bash

    $ ./assets.sh

Virtual host
------------

TODO

Production mode
---------------------------

TODO
