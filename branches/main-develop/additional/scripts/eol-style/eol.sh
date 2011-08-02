#!/bin/sh
DIR=`pwd`/../../..
DIR=`readlink -f ${DIR}`

find ${DIR} \( -path ${DIR}/webEdition/lib/Zend -o -path ${DIR}/webEdition/lib/phpMailer \) -prune -o -name \*.php -exec svn propset svn:keywords "Date Author Revision" {} \;
find ${DIR} -name \*.php -exec svn:eol-style native {} \;
find ${DIR} -name \*.html -exec svn propset svn:eol-style native {} \;
find ${DIR} -name \*.js -exec svn propset svn:eol-style native {} \;
find ${DIR} -name \*.css -exec svn propset svn:eol-style native {} \;
find ${DIR} -name \*.java -exec svn propset svn:eol-style native {} \;
find ${DIR} -name \*.xml -exec svn propset svn:eol-style native {} \;
