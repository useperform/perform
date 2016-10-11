#!/bin/bash

set -e

npm install --no-progress
bower install
grunt sass
