#!/bin/sh
#DIR=`pwd`/../../..
#DIR=`readlink -f ${DIR}`
DIR=$1

find ${DIR} \( -path ${DIR}/webEdition/lib/Zend -o -path ${DIR}/webEdition/lib/phpMailer \) -prune -o -name \*.php -exec svn propset svn:keywords "Date Author Revision" {} \;> log.txt
find ${DIR} -name \*.js -exec svn propset svn:keywords "Date Author Revision" {} \;>> log.txt
find ${DIR} -name \*.php -exec svn propset svn:eol-style native {} \;>> log.txt
find ${DIR} -name \*.html -exec svn propset svn:eol-style native {} \;>> log.txt
find ${DIR} -name \*.js -exec svn propset svn:eol-style native {} \;>> log.txt
find ${DIR} -name \*.css -exec svn propset svn:eol-style native {} \;>> log.txt
find ${DIR} -name \*.java -exec svn propset svn:eol-style native {} \;>> log.txt
find ${DIR} -name \*.xml -exec svn propset svn:eol-style native {} \;>> log.txt
find ${DIR} -name \*.svg -exec svn propset svn:eol-style native {} \;>> log.txt
find ${DIR} -name .htaccess -exec svn propset svn:eol-style native {} \;>> log.txt
find ${DIR} -name .sh -exec svn propset svn:eol-style LF svn:executable {} \;>> log.txt
find ${DIR} -name \*.txt -exec svn propset svn:eol-style native {} \;>> log.txt
find ${DIR} -name \*.png -exec svn propset svn:mime-type image/png {} \;>> log.txt
find ${DIR} -name \*.gif -exec svn propset svn:mime-type image/gif {} \;>> log.txt
find ${DIR} -name \*.jpg -exec svn propset svn:mime-type image/jpeg {} \;>> log.txt
