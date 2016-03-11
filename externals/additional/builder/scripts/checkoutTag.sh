#!/bin/bash
# get all command arguments

if [ $1 == "" ]
then
	exit 0
fi

SN=$1
SNSTRING=${SN:0:1}"."${SN:1:1}"."${SN:2:1}"."${SN:3:1}

cd /kunden/343047_10825/build/svn/trunk

#checkout
TAGDIR="/kunden/343047_10825/build/svn/tags/"${SNSTRING}"/"
REPO="^/tags/"${SNSTRING}"/webEdition/"

if [ -d ${TAGDIR} ]
then
	rm -rf ${TAGDIR}
fi

mkdir ${TAGDIR}
svn checkout ${REPO} ${TAGDIR}