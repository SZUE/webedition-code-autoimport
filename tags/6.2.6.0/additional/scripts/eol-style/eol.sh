#!/bin/sh
DIR=`pwd`/../../..
find ${DIR} -name \*.php -exec svn propset svn:eol-style native {} \;
find ${DIR} -name \*.html -exec svn propset svn:eol-style native {} \;
find ${DIR} -name \*.js -exec svn propset svn:eol-style native {} \;
find ${DIR} -name \*.java -exec svn propset svn:eol-style native {} \;
find ${DIR} -name \*.xml -exec svn propset svn:eol-style native {} \;
