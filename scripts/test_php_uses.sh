#!/bin/bash

set -e

SCRIPT_DIR=$(dirname $0)

EE_STD_DIR=$SCRIPT_DIR/../ee_dev

if [ ! -d $EE_STD_DIR ]; then
    mkdir $EE_STD_DIR
    cp $SCRIPT_DIR/test_php_uses_composer.json $EE_STD_DIR/composer.json
fi

# Extract "use" statement from php and rst files
# Acme\CustomBundle and Acme\RangeBundle are excluded because created in the examples
find . \( -iname "*.rst" -or -iname "*.php" \) -not -path "*/ee_dev/*" -not -path "*/.composer/*" | \
    xargs grep -ne '^ *use.*\\.*;$' | \
    sed -e 's/ *use */#/' | \
    grep -v 'CustomBundle' | \
    grep -v 'RangeBundle' | \
    cut -d " " -f 1 | \
    tr -d ";" > $EE_STD_DIR/php_uses.list

cp $SCRIPT_DIR/check_uses_existence.php $EE_STD_DIR/

cd $EE_STD_DIR

php -d memory_limit=4G /usr/local/bin/composer.phar install
php check_uses_existence.php php_uses.list
