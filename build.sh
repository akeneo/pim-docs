#!/bin/bash -e

usage(){
    echo "Usage: $0 [--uid <uid>] [--gid <gid] [ [--no-asset-check] [--deploy --host <host> --port <port> --username <username>]"
    echo "  --deploy              Deploy to the server"
    echo "  --no-asset-check      Do not check asset changes"
    echo "  --host <host>         Host used for deployment"
    echo "  --port <port>         Port used for deployment"
    echo "  --username <username> Username used for deployment"
    echo "  --uid <user_id>       User id for documentation generation"
    echo "  --gid <group_id>      Group id for documentation generation"
}

DEPLOY=false
ASSET_CHECK=true
HOST=docs-staging.akeneo.com
PORT=22
USERNAME=pim-docs
CUSTOM_UID=`id -u`
CUSTOM_GID=`id -g`

while true; do
  case "$1" in
    -d | --deploy ) echo "Enable deployment..."; DEPLOY=true; shift ;;
    -h | --host ) echo "Set host to $2..."; HOST=$2; shift 2 ;;
    -p | --port ) echo "Set port to $2..."; PORT=$2; shift 2 ;;
    -u | --username ) echo "Set username to $2..."; USERNAME=$2; shift 2 ;;
    -n | --no-asset-check ) ASSET_CHECK=false; shift ;;
    --uid ) CUSTOM_UID=$2; shift 2 ;;
    --gid ) CUSTOM_GID=$2; shift 2 ;;
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
    wget https://github.com/akeneo/pim-community-dev/archive/2.0.zip -P /tmp/

    md5original=`md5sum /home/akeneo/pim-docs/2.0.zip | cut -f 1 -d " "`
    md5current=`md5sum /tmp/2.0.zip | cut -f 1 -d " "`

    if [ "$md5original" = "$md5current" ]
    then
        echo "Akeneo PIM does not change."
        rm /tmp/2.0.zip
    else
        echo "Rebuild Akeneo PIM assets..."
        rm -rf /home/akeneo/pim-docs/2.0.zip
        mv /tmp/2.0.zip /home/akeneo/pim-docs/2.0.zip
        rm -rf /home/akeneo/pim-docs/pim-community-dev-2.0
        unzip /home/akeneo/pim-docs/2.0.zip -d /home/akeneo/pim-docs/
        cd /home/akeneo/pim-docs/pim-community-dev-2.0/ && php -d memory_limit=3G ../composer.phar install --no-dev --no-suggest
        service mysql start && \
        cd /home/akeneo/pim-docs/pim-community-dev-2.0/ && php app/console pim:installer:assets --env=prod
    fi
fi

rm -rf /home/akeneo/pim-docs/data/pim-docs-build/web
rm -rf /home/akeneo/pim-docs/data/pim-docs-build/vendor
sed -i -e "s/^version =.*/version = '2.0'/" /home/akeneo/pim-docs/data/conf.py
sphinx-build -b html /home/akeneo/pim-docs/data /home/akeneo/pim-docs/data/pim-docs-build
cp -r /home/akeneo/pim-docs/pim-community-dev-2.0/web /home/akeneo/pim-docs/data/pim-docs-build/
cp -r /home/akeneo/pim-docs/pim-community-dev-2.0/vendor /home/akeneo/pim-docs/data/pim-docs-build/
cp -r /home/akeneo/pim-docs/data/design_pim/styleguide /home/akeneo/pim-docs/data/pim-docs-build/design_pim/
find /home/akeneo/pim-docs/data/pim-docs-build/ -exec chown $CUSTOM_UID:$CUSTOM_GID {} \;

if [ "$DEPLOY" == true ]; then
    rsync -e "ssh -p $PORT" -avz /home/akeneo/pim-docs/data/pim-docs-build/* $USERNAME@$HOST:/var/www/2.0
fi
