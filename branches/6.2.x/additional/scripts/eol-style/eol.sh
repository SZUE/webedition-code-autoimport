#!/bin/sh
DIR=`pwd`/../../..
DIR=`readlink -f ${DIR}`

find ${DIR} -name \*.php -exec svn propset svn:eol-style native {} \;|tee -a log.txt
find ${DIR} -name \*.html -exec svn propset svn:eol-style native {} \;|tee -a log.txt
find ${DIR} -name \*.js -exec svn propset svn:eol-style native {} \;|tee -a log.txt
find ${DIR} -name \*.css -exec svn propset svn:eol-style native {} \;|tee -a log.txt
find ${DIR} -name \*.java -exec svn propset svn:eol-style native {} \;|tee -a log.txt
find ${DIR} -name \*.xml -exec svn propset svn:eol-style native {} \;|tee -a log.txt
find ${DIR} -name .htaccess -exec svn propset svn:eol-style native {} \;|tee -a log.txt
find ${DIR} -name \*.txt -exec svn propset svn:eol-style native {} \;|tee -a log.txt
