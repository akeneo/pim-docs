How to apply a EE patch
=======================

Once a new patch is deployed, all you have to do in your project directory is :

.. code-block:: bash
    :linenos:

     $ cd /path/to/you/project/directory
     $ composer update

Then don't forget to clean and rebuild you cache

 .. code-block:: bash
    :linenos:

     $ rm app/cache/* -rf
     $ php app/console cache:clear --env=prod --no-debug
