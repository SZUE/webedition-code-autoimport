#!/bin/sh
#DIR=`pwd`/../../..
#DIR=`readlink -f ${DIR}`
DIR=$1
LOG=`pwd`/log.txt

find ${DIR} \( -path ${DIR}/webEdition/lib/Zend -o -path ${DIR}/webEdition/lib/phpMailer \) -prune -o -name \*.php -exec svn propset svn:keywords "Date Author Revision" {} \;> ${LOG}
find ${DIR} -name \*.js -exec svn propset svn:keywords "Date Author Revision" {} \;>> ${LOG}
find ${DIR} -name \*.php -exec svn propset svn:eol-style native {} \;>> ${LOG}
find ${DIR} -name \*.html -exec svn propset svn:eol-style native {} \;>> ${LOG}
find ${DIR} -name \*.js -exec svn propset svn:eol-style native {} \;>> ${LOG}
find ${DIR} -name \*.css -exec svn propset svn:eol-style native {} \;>> ${LOG}
find ${DIR} -name \*.java -exec svn propset svn:eol-style native {} \;>> ${LOG}
find ${DIR} -name \*.xml -exec svn propset svn:eol-style native {} \;>> ${LOG}
find ${DIR} -name \*.svg -exec svn propset svn:eol-style native {} \;>> ${LOG}
find ${DIR} -name .htaccess -exec svn propset svn:eol-style native {} \;>> ${LOG}
find ${DIR} -name .sh -exec svn propset svn:eol-style LF svn:executable {} \;>> ${LOG}
find ${DIR} -name \*.txt -exec svn propset svn:eol-style native {} \;>> ${LOG}
find ${DIR} -name \*.png -exec svn propset svn:mime-type image/png {} \;>> ${LOG}
find ${DIR} -name \*.gif -exec svn propset svn:mime-type image/gif {} \;>> ${LOG}
find ${DIR} -name \*.jpg -exec svn propset svn:mime-type image/jpeg {} \;>> ${LOG}
