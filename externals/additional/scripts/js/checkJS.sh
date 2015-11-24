#!/bin/sh
DIR=$1


echo "check js errors"
jshint ${DIR}

echo "check duplicate code"

jsinspect ${DIR}

