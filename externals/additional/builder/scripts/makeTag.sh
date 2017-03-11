#!/bin/bash

if [ $1 == "" ]
then
	echo 0
	exit
fi

HOME=/www/343047_10825
SN=$1
SNSTRING=${SN:0:1}"."${SN:1:1}"."${SN:2:1}"."${SN:3:1}

cd /kunden/343047_10825/build/svn/trunk
svn -q copy svn+ssh://we-org@svn.code.sf.net/p/webedition/code/trunk ^/tags/${SNSTRING} -m "builder create tag: "${SNSTRING}
#svn copy ^/trunk ^/tags/${SNSTRING} -m "tag create: "${SNSTRING}
cd /kunden/343047_10825/sites/webedition.org/nightlybuilder
echo 1;
