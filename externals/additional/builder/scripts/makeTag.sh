#!/bin/bash

if [ $1 == "" ]
then
	echo 0
	exit
fi

SN=$1
SNSTRING=${SN:0:1}"."${SN:1:1}"."${SN:2:1}"."${SN:3:1}

cd /kunden/343047_10825/build/svn/trunk
#svn copy ^/trunk ^/tags/${SNSTRING} -m "tag create: "${SNSTRING}
cd /kunden/343047_10825/sites/webedition.org/nightlybuilder
echo 1;
