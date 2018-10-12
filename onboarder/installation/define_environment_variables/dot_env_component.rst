Using the DotEnv file
=====================

The `Dotenv Symfony component <https://symfony.com/doc/3.4/components/dotenv.html>`_ is a dependency already available into the pim as it's a standard symfony framework component.

Copy the ``doc/onboarder.env`` file to your project root directory and modify the values according to your project.

Add the following code to **each** PHP entry point (``bin/console``, ``web/app.php``, etc.) after the ``autoload.php`` require statement.

.. code-block:: php

    <?php

    use Symfony\Component\Dotenv\Dotenv;
    //...
    
    require __DIR__ . '/../vendor/autoload.php';
    //...

    $dotEnv = new Dotenv();
    $dotEnv->load(__DIR__ . '/../.env');

