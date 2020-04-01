#!/bin/bash

set -e

SCRIPT_DIR=$(dirname $0)

EE_STD_DIR=$SCRIPT_DIR/../ee_dev

if [ ! -d $EE_STD_DIR ]; then
    mkdir $EE_STD_DIR
    cp $SCRIPT_DIR/test_php_uses_composer.json $EE_STD_DIR/composer.json
fi

find . \( -iname "*.rst" -or -iname "*.php" \) -not -path "./ee_dev/*" | xargs grep -ne '^ *use.*;$' | grep -v "use Acme" | sed -e 's/ *use */#/' | tr -d ";" > $EE_STD_DIR/php_uses.list
cp $SCRIPT_DIR/check_uses_existence.php $EE_STD_DIR/

cd $EE_STD_DIR

docker run -u $UID --rm \
    -v $(pwd):/srv/pim -v ~/.composer:/tmp/.composer -v ~/.ssh:/var/www/.ssh -w /srv/pim \
    -e COMPOSER_HOME=/tmp/.composer \
    akeneo/pim-php-dev:4.0 php -d memory_limit=4G /usr/local/bin/composer install

docker run -u $UID --rm \
    -v $(pwd):/srv/pim -w /srv/pim \
    akeneo/pim-php-dev:4.0 php check_uses_existence.php php_uses.list
