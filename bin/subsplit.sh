#!/bin/sh

set -eu

SERVER=$PERFORM_GIT_SERVER

if ! git remote | grep -q $2
then
    git remote add $2 $SERVER/$2.git
fi

splitsh-lite --prefix src/$1 --target heads/$2 --origin origin/master
git push $2 heads/$2:refs/heads/master
