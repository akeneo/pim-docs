#!/bin/bash -e

usage(){
    echo "Usage: $0 [--no-asset-check] [--deploy --host <host> --port <port> --username <username>]"
    echo "  --deploy              Deploy to the server"
    echo "  --no-asset-check      Do not check asset changes"
    echo "  --host <host>         Host used for deployment"
    echo "  --port <port>         Port used for deployment"
    echo "  --username <username> Username used for deployment"
}

DEPLOY=false
ASSET_CHECK=true
HOST=docs-staging.akeneo.com
PORT=22
USERNAME=pim-docs

while true; do
  case "$1" in
    -d | --deploy ) echo "Enable deployment..."; DEPLOY=true; shift ;;
    -h | --host ) echo "Set host to $2..."; HOST=$2; shift 2 ;;
    -p | --port ) echo "Set port to $2..."; PORT=$2; shift 2 ;;
    -u | --username ) echo "Set username to $2..."; USERNAME=$2; shift 2 ;;
    -n | --no-asset-check ) ASSET_CHECK=false; shift ;;
    -- ) shift; break ;;
    * ) break ;;
  esac
done

if [ "$DEPLOY" == true ]; then
    echo "Trying connection to $USERNAME@$HOST:$PORT..."
    mkdir ~/.ssh/
    touch ~/.ssh/known_hosts
    ssh-keyscan -H -p $PORT $HOST >> ~/.ssh/known_hosts
    ssh -p $PORT $USERNAME@$HOST exit || exit 0
    echo "Connection OK"
fi

if [ "$ASSET_CHECK" != false ]; then
    wget https://github.com/akeneo/pim-community-dev/archive/1.7.zip -P /tmp/

    md5original=`md5sum /home/akeneo/pim-docs/1.7.zip | cut -f 1 -d " "`
    md5current=`md5sum /tmp/1.7.zip | cut -f 1 -d " "`

    if [ "$md5original" = "$md5current" ]
    then
        echo "Akeneo PIM does not change."
        rm /tmp/1.7.zip
    else
        echo "Rebuild Akeneo PIM assets..."
        rm -rf /home/akeneo/pim-docs/1.7.zip
        mv /tmp/1.7.zip /home/akeneo/pim-docs/1.7.zip
        rm -rf /home/akeneo/pim-docs/pim-community-dev-1.7
        unzip /home/akeneo/pim-docs/1.7.zip -d /home/akeneo/pim-docs/
        cd /home/akeneo/pim-docs/pim-community-dev-1.7/ && php -d memory_limit=3G ../composer.phar install --no-dev --no-suggest
        service mysql start && \
        cd /home/akeneo/pim-docs/pim-community-dev-1.7/ && php app/console pim:installer:assets --env=prod
    fi
fi

rm -rf /home/akeneo/pim-docs/data/pim-docs-build/web
rm -rf /home/akeneo/pim-docs/data/pim-docs-build/vendor
sed -i -e "s/^version =.*/version = '1.7'/" /home/akeneo/pim-docs/data/conf.py
sphinx-build -b html /home/akeneo/pim-docs/data /home/akeneo/pim-docs/data/pim-docs-build
cp -r /home/akeneo/pim-docs/pim-community-dev-1.7/web /home/akeneo/pim-docs/data/pim-docs-build/
cp -r /home/akeneo/pim-docs/pim-community-dev-1.7/vendor /home/akeneo/pim-docs/data/pim-docs-build/
cp -r /home/akeneo/pim-docs/data/styleguide /home/akeneo/pim-docs/data/pim-docs-build/

if [ "$DEPLOY" == true ]; then
    rsync -e "ssh -p $PORT" -avz /home/akeneo/pim-docs/data/pim-docs-build/* $USERNAME@$HOST:/var/www/1.7
fi
