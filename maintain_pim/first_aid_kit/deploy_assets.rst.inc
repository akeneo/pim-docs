.. code-block:: bash

    cd /path/to/your/pim/
    rm -rf ./web/bundles/* ./web/css/* ./web/js/*
    php bin/console pim:install:assets --env=prod
    php bin/console assets:install --symlink web
