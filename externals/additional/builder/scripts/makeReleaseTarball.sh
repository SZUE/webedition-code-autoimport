#!/bin/bash

echo 0
exit
# disabled

BASEDIR="/kunden/343047_10825/"
BUILDDIR=$BASEDIR"build/tmp/"

if [ $1 == "" ]
then
	exit 0
fi

SN=$1

FROM="trunk"
if [ $2 == "hotfixes" ]
then
	FROM="hotfixes"
	# activate as soon as tag-repo is implemented
	exit 0
fi
echo $FROM

VERSIONDIR = $BASEDIR"sites/webedition.org/"

# check if svn checkout exists
SVNDIR=$BASEDIR"build/svn/"$FROM"/"

if [ -d $SVNDIR ];
then
	#echo "SVN dir '$SVNDIR' exists."
else
	echo "ERROR: SVN dir '$SVNDIR' does not exist! Aborting."
	exit 0
fi

svn update $SVNDIR
TARGET=${BASEDIR}"sites/webedition.org/download/releases/"
FILENAME="webEdition_"$SN".tgz"
TARGETFILE=${TARGET}${FILENAME}
if [ -f $TARGETFILE ];
then
	rm -rf $TARGETFILE
fi

# remove old files from tmp:
BUILDTRUNK=$BUILDDIR"trunk/"
if [ -d $BUILDTRUNK ];
then
	echo "removing old files from '$BUILDDIR'"
	rm -rf $BUILDTRUNK
fi

# copy svn checkout files:
echo "copying files from '$SVNDIR'webEdition checkout to '$BUILDTRUNK'"
mkdir $BUILDTRUNK
cp -R $SVNDIR"webEdition" $BUILDTRUNK
cp $SVNDIR"additional/setup/"* $BUILDTRUNK

# remove all .svn subdirectories and .DS_Store files
cd $BUILDTRUNK
rm -rf `find . -type d -name .svn`
rm -rf `find . -type d -name .DS_Store`
cd ../../

# create a version file containing the current build date:
echo "creating BUILD file"
SVNREV=`svnversion ${SVNDIR}`
echo -e "Date: `date '+%Y/%m/%d %H:%M'`" >> $BUILDTRUNK"BUILD.txt"
echo -e "Revision: "$SVNREV >> $BUILDTRUNK"BUILD.txt"

# get we_version from release
WEVERSION=$BASEDIR"sites/webedition.org/update/htdocs/files/we/version"$SN"/files/none/webEdition/we/include/we_version.php"
rm -rf $BUILDTRUNK"webEdition/we/include/we_version.php"
cp $WEVERSION $BUILDTRUNK"webEdition/we/include/"

# create tarball
echo "creating tarball"
cd $BUILDTRUNK
tar cfz ${TARGET}${FILENAME} *

echo "done. The installation archive is created in \""$TARGET"\""

# remove old files from tmp:
if [ -d $BUILDTRUNK ];
then
	echo "removing all from tmp"
	rm -rf $BUILDTRUNK
fi

# create checksums:
cd $TARGET
ln -sf $FILENAME webEdition_latest
sha1sum *.tgz > "SHA1SUMS"
sha256sum *.tgz > "SHA256SUMS"
md5sum *.tgz > "MD5SUMS"

echo 1


