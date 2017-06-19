#!/bin/sh

set -eu

case "$1" in
    7.1) BASE_IMAGE="php:7.1"
         PERFORM_IMAGE="perform:7.1"
         ;;
    5.6) BASE_IMAGE="php:5.6"
         PERFORM_IMAGE="perform:5.6"
         ;;
    *) echo "Unknown base php image $1"
       exit 1
       ;;
esac

if docker images --format '{{.Repository}}:{{.Tag}}' | grep -q $PERFORM_IMAGE
then
    echo "Found existing docker image $PERFORM_IMAGE"
else
    echo "Building new docker image $PERFORM_IMAGE"
    echo 'FROM '$BASE_IMAGE > Dockerfile
    cat etc/Dockerfile.dist >> Dockerfile
	docker build . -t $PERFORM_IMAGE
fi

docker run -ti --rm -v `pwd`:/opt/perform $PERFORM_IMAGE composer install
