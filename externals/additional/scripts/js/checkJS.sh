#!/bin/sh
DIR=$1


echo "check js errors"
cd ${DIR}
jshint .

echo "check duplicate code"

jsinspect .

