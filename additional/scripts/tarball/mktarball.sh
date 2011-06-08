#!/bin/bash
VERSION=`php -r "include ('../../../webEdition/we/include/we_version.php'); echo str_replace('.','',WE_VERSION);"`
CURDIR=`pwd`
TMPFOLDER=`pwd`"/src"
DIST=`pwd`"/dist"
ADDITIONAL=`pwd`"/../.."
WEBASE=${ADDITIONAL}"/.."
TAR_FILE="${DIST}/webEdition_${VERSION}.tar"
TAR="tar --exclude=.svn -f ${TAR_FILE} "

echo "Creating tar-ball for WE ${VERSION}"
rm -rf ${TMPFOLDER}
mkdir -p ${TMPFOLDER}
rm -rf ${DIST}
mkdir -p ${DIST}

echo "temporary: adding iso-files"
mkdir -p ${TMPFOLDER}/webEdition/we/include/we_language/
cp -R ${ADDITIONAL}/lang_iso/* ${TMPFOLDER}/webEdition/we/include/we_language/

# Create sql-queries
echo "Creating sqldumps"
php dump_sql.php ${ADDITIONAL}/sqldumps/ ${TMPFOLDER}/
cd ${TMPFOLDER}
${TAR} -c *

# copy setup
echo "Adding tar-ball-setup"
cd ${ADDITIONAL}/setup
${TAR} -r *

echo "adding webEdition files"
cd ${WEBASE}
${TAR} -r webEdition


#remove temporary files
rm -rf ${TMPFOLDER}

echo "compressing tar"
gzip -9 ${TAR_FILE}
