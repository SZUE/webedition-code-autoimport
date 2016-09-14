#!/bin/sh
#DIR=`pwd`/../../..
#DIR=`readlink -f ${DIR}`
DIR=$1
LOG=/tmp/log.txt

find ${DIR} \( -path ${DIR}/webEdition/lib/phpMailer \) -prune -o -name \*.php -exec svn propset svn:keywords "Date Author Revision" {} \;> ${LOG}
find ${DIR} \( -name *.js -o -name *.css -o -name *.scss \) -exec svn propset svn:keywords "Date Author Revision" {} \;>> ${LOG}
find ${DIR} \( -name \*.php -o -name \*.html -o -name \*.js -o -name \*.css -o -name \*.scss -o -name \*.java -o -name \*.xml -o -name \*.svg \) -exec svn propset svn:eol-style native {} \;>> ${LOG}
find ${DIR} -name .htaccess -exec svn propset svn:eol-style native {} \;>> ${LOG}
find ${DIR} -name \*.sh -exec svn propset svn:eol-style LF {} \;>> ${LOG}
find ${DIR} -name \*.sh -exec svn propset svn:executable ON {} \;>> ${LOG}
find ${DIR} -name \*.txt -exec svn propset svn:eol-style native {} \;>> ${LOG}
find ${DIR} -name \*.png -exec svn propset svn:mime-type image/png {} \;>> ${LOG}
find ${DIR} -name \*.gif -exec svn propset svn:mime-type image/gif {} \;>> ${LOG}
find ${DIR} -name \*.jpg -exec svn propset svn:mime-type image/jpeg {} \;>> ${LOG}
