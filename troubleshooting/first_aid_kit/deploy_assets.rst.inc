.. code-block:: txt

    cd /path/to/your/pim/
    rm -rf ./web/bundles/* ./web/css/* ./web/js/*
    php app/console pim:install:assets --env=prod
    php app/console assets:install --symlink web
