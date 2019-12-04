#!/bin/bash -e

usage(){
    echo "Usage: $0 <version> [--uid <uid>] [--gid <gid] [ [--deploy --host <host> --port <port> --username <username>]"
    echo "  version               The version to build (e.g. 1.7 or master)"
    echo "  --deploy              Deploy to the server"
    echo "  --host <host>         Host used for deployment"
    echo "  --port <port>         Port used for deployment"
    echo "  --username <username> Username used for deployment"
}

DEPLOY=false
HOST=docs-staging.akeneo.com
PORT=GIMMETHEREALPORT
USERNAME=GIMMETHEREALUSER
VERSION=$1; shift; echo "Building version $VERSION..."

while true; do
  case "$1" in
    -d | --deploy ) echo "Enable deployment..."; DEPLOY=true; shift ;;
    -h | --host ) echo "Set host to $2..."; HOST=$2; shift 2 ;;
    -p | --port ) echo "Set port to $2..."; PORT=$2; shift 2 ;;
    -u | --username ) echo "Set username to $2..."; USERNAME=$2; shift 2 ;;
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

rm -rf /home/akeneo/pim-docs/data/pim-docs-build/web
sed -i -e "s/^version =.*/version = '${VERSION}'/" /home/akeneo/pim-docs/data/conf.py

sphinx-build -b html /home/akeneo/pim-docs/data /home/akeneo/pim-docs/data/pim-docs-build
cp -L -r /home/akeneo/pim-docs/pim-community-dev-${VERSION}/public /home/akeneo/pim-docs/data/pim-docs-build/
cp -r /home/akeneo/pim-docs/data/design_pim/styleguide /home/akeneo/pim-docs/data/pim-docs-build/design_pim/
if [ "$DEPLOY" == true ]; then
    rsync -e "ssh -p $PORT" -avz /home/akeneo/pim-docs/data/pim-docs-build/* $USERNAME@$HOST:/var/www/${VERSION}
fi
