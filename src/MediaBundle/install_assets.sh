#!/bin/bash

set -e

if which yarn > /dev/null
then
    yarn install
else
    npm install
fi

npm run build
