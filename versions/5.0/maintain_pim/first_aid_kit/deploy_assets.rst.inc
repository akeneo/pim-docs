.. code-block:: bash

    cd /path/to/your/pim/
    rm -rf ./public/bundles/* ./public/css/* ./public/js/*
    php bin/console pim:install:assets
    php bin/console assets:install --symlink web
    yarn run webpack
