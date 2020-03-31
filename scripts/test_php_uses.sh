#!/bin/bash

set -e

SCRIPT_DIR=$(dirname $0)

EE_STD_DIR=$SCRIPT_DIR/../ee_dev

if [ ! -d ee_dev ]; then
    git clone --branch 4.0 --depth 1 git@github.com:akeneo/pim-enterprise-standard.git $EE_STD_DIR
fi

find . \( -iname "*.rst" -or -iname "*.php" \) -not -path "./ee_dev/*" | xargs grep -ne '^ *use.*;$' | grep -v "use Acme" | sed -e 's/ *use */#/' | tr -d ";" > $EE_STD_DIR/php_uses.list
cp $SCRIPT_DIR/check_uses_existence.php $EE_STD_DIR/

cd $EE_STD_DIR

docker run -u www-data --rm \
    -v $(pwd):/srv/pim -v ~/.composer:/var/www/.composer -v ~/.ssh:/var/www/.ssh -w /srv/pim \
    akeneo/pim-php-dev:4.0 php -d memory_limit=4G /usr/local/bin/composer install

docker run -u www-data --rm \
    -v $(pwd):/srv/pim -w /srv/pim \
    akeneo/pim-php-dev:4.0 php check_uses_existence.php php_uses.list
