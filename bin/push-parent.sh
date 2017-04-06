#!/bin/sh

# push the parent package to the packaging git server.

set -eu

SERVER=$PERFORM_GIT_SERVER

if ! git remote | grep -q pkg
then
    git remote add pkg $SERVER/perform-bundles.git
fi

git push pkg master:refs/heads/master
