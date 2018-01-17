#!/bin/bash -e

usage(){
    echo "Usage: $0 [--uid <uid>] [--gid <gid] [ [--no-asset-check] [--deploy --host <host> --port <port> --username <username>]"
    echo "  --deploy              Deploy to the server"
    echo "  --host <host>         Host used for deployment"
    echo "  --port <port>         Port used for deployment"
    echo "  --username <username> Username used for deployment"
    echo "  --uid <user_id>       User id for documentation generation"
    echo "  --gid <group_id>      Group id for documentation generation"
}

DEPLOY=false
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

sed -i -e "s/^version =.*/version = '1.3'/" /home/akeneo/pim-docs/data/conf.py
sphinx-build -b html /home/akeneo/pim-docs/data /home/akeneo/pim-docs/data/pim-docs-build
find /home/akeneo/pim-docs/data/pim-docs-build/ -exec chown $CUSTOM_UID:$CUSTOM_GID {} \;

if [ "$DEPLOY" == true ]; then
    rsync -e "ssh -p $PORT" -avz /home/akeneo/pim-docs/data/pim-docs-build/* $USERNAME@$HOST:/var/www/1.3
fi
