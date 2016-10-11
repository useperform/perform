#!/bin/bash

set -e

npm install --no-progress --prune
gulp build
