#!/bin/bash

BASEDIR=/kunden/343047_10825/
HOME=/kunden/343047_10825/sites/webedition.org/nightlybuilder/

SVN_DIR=${BASEDIR}build/svn/
UPDATE_VERSIONS_DIR=${BASEDIR}sites/webedition.org/update/htdocs/files/we/

GET_PARAM_PHP=${HOME}we_builder_bridge.php

BRANCH=0
TYPE=0
NORMALIZED_TYPE=0
HOTFIX_SN=0

while getopts b:t:v: opts; do
	case ${opts} in
		b) BRANCH=${OPTARG} ;;
		t) TYPE=${OPTARG} ;;
		v) HOTFIX_SN=${OPTARG} ;;
	esac
done

# CHECK IF WE HAVE CONFIG FOR branch:release-type OR type=hotfix:version
NORMALIZED_TYPE=$(php56 -q ${GET_PARAM_PHP} -b ${BRANCH} -t ${TYPE} -v ${HOTFIX_SN} -p targetNormalizedType)
IS_HOTFIX=$(php56 -q ${GET_PARAM_PHP} -b ${BRANCH} -t ${TYPE} -v ${HOTFIX_SN} -p isHotfix)

if [ $(php56 -q ${GET_PARAM_PHP} -b ${BRANCH} -t ${TYPE} -v ${HOTFIX_SN} -p isValidBranch) -eq 0 ] && [ ${IS_HOTFIX} -ne 1 ] ; then
	echo -e "\nThe branch you have entered (\""${BRANCH}"\") is not valid.\nBye...\n"; exit;
fi

if [ $(php56 -q ${GET_PARAM_PHP} -b ${BRANCH} -t ${TYPE} -v ${HOTFIX_SN} -p isValidType) -eq 0 ] ; then
	echo -e "\nThere is no release-type \""${TYPE}"\" defined for \""${BRANCH}"\".\nBye...\n"; exit;
fi

if [ ${IS_HOTFIX} -eq 1 ] ; then
	HOTFIX_SN_OK=$(php56 -q ${GET_PARAM_PHP} -b ${BRANCH} -t ${TYPE} -v ${HOTFIX_SN} -p isValidHotfixSN)
	if [ ${HOTFIX_SN_OK} -eq 0 ] ; then
		echo -e "\nThe version number you have entered for making a hotfix (\""${HOTFIX_SN}"\") is not valid.\nBye...\n"; exit;
	fi
fi


# GET CONFIGURATION AND ASK WHETER TO GO ON OR NOT
QUESTION=1
while true; do
	case ${QUESTION} in
		1)
			SHOW_CONFIG=0
			# get complete configuration as string and write
			echo -e "\nConfiguration found for the parameters you entered:"
			CONF_CONFIGSTRING=$(php56 -q ${GET_PARAM_PHP} -b ${BRANCH} -t ${TYPE} -v ${HOTFIX_SN} -p configString)
			IFS='%'
			echo -e ${CONF_CONFIGSTRING}"\n"
			unset IFS

			# get some parameters we need again later
			CONF_VERSION=$(php56 -q ${GET_PARAM_PHP} -b ${BRANCH} -t ${TYPE} -v ${HOTFIX_SN} -p targetVersion)
			CONF_DELETEVERSIONS=$(php56 -q ${GET_PARAM_PHP} -b ${BRANCH} -t ${TYPE} -v ${HOTFIX_SN} -p builderVersionsToDelete)
			CONF_CREATETAG=$(php56 -q ${GET_PARAM_PHP} -b ${BRANCH} -t ${TYPE} -v ${HOTFIX_SN} -p builderCreateTag)

			read -p "Do you wish to build webEdition using this configuration? [y|n] " yn
			case $yn in
				[Yy]* ) echo -e "\nLet's go...\n"; break;;
				[Nn]* ) QUESTION=2;;
				* ) echo "Please answer yes [Yy] or no [Nn].";;
			esac;;
		2)
			read -p "Correct the configuration? [y|n]" yn
			case $yn in
				[Yy]* ) QUESTION=3;;
				[Nn]* ) echo -e "\nSo we exit... See you later!"; exit;;
				* ) echo "Please answer yes [Yy] or no [Nn].";;
					esac;;
		3)
			read -p "Please press [c] to continue when you are finished, or abort. [c|a] " ca
			case $ca in
				[Cc]* ) QUESTION=1;;
				[Aa]* ) echo -e "\nSo we exit... See you later!"; exit;;
				* ) echo "Please answer continue [c] or abort [a].";;
			esac;;
	esac
done

# CREATE TAG
echo -en "Create tag "${CONF_VERSION}": "
if [ ${CONF_CREATETAG} -eq 1 ]
then
	RESPONSE=$(${HOME}"scripts/makeTag.sh" "${CONF_VERSION}")
	if [ ${RESPONSE} -eq 1 ]
	then
		echo "        done"
	else
		echo "        failed"
	fi
else
	echo -e "        [no tag for this configuration]"
fi

# BUILD VERSION
echo -en "Build version "${CONF_VERSION}": "
RESPONSE=0
RESPONSE="$(php56 -q we_builder.php -b ${BRANCH} -t ${TYPE})"

if [ "${RESPONSE}" = "11" ]
then
	echo -e "     done"
	SUCCESS=1

else
	SUCCESS=0
	echo "     failed"
	echo -en "\nBuilder response: "
	echo -e ${RESPONSE}"\n"
	exit
fi

# PROMPT: BUILD TARBAL?
echo -en "Build tarball "${CONF_VERSION}": "
if [ "${NORMALIZED_TYPE}" == "release" -a "${BRANCH}" == "trunk" ]
then
	cd ${HOME}
	RESPONSE=0
	RESPONSE=$(scripts/makeReleaseTarball.sh ${CONF_VERSION})

	if [ ${RESPONSE} -eq 1 ]
	then
		echo -e "     done\n"
	else
		echo -e "     failed\n"
	fi
else 
	echo -e "     [no tarball for this configuration]"
fi
cd ${HOME}

# (CHECKOUT TAG)
if [ ${CONF_CREATETAG} -eq 1 ]
then 
	while true; do
		read -p "Do you want to check out tag (to /build/svn/tags/...)? [y|n]" yn
		case $yn in
			[Yy]* ) CHECKOUT=1; break;;
			[Nn]* ) CHECKOUT=0; break;;
			* ) echo "Please answer yes [Yy] or no [Nn].";;
		esac
	done
	if [ ${CHECKOUT} == 1 ]
	then
		echo "implement checkout..."
	fi
fi

# PROMPT: BUILD NIGHTLY?
ASK_FOR_NIGHTLY=0
BUILD_NIGHTLY=0
if [ ${NORMALIZED_TYPE} == "release" ]
then
	IS_NIGHTLY_CONF=$(php56 -q ${GET_PARAM_PHP} -b ${BRANCH} -t nightly -v ${HOTFIX_SN} -p isValidType)
	if [ ${IS_NIGHTLY_CONF} -eq 0 ]
	then
		ASK_FOR_NIGHTLY=0;
	else
		ASK_FOR_NIGHTLY=1;
	fi
fi

if [ ${ASK_FOR_NIGHTLY} == 1 ]
then
	QUESTION=1
	while true; do
		case ${QUESTION} in
			1)
				SHOW_CONFIG=0
				NIGHTLY="nightly"
				NIGHTLY_VERSION=$(php56 -q ${GET_PARAM_PHP} -b ${BRANCH} -t ${NIGHTLY} -v ${HOTFIX_SN} -p targetVersion)
				echo -e "\nThere is a configuration for a new nightly:\n"
				NIGHTLY_CONFIGSTRING=$(php56 -q ${GET_PARAM_PHP} -b ${BRANCH} -t ${NIGHTLY} -v ${HOTFIX_SN} -p configString)
				IFS='%'
				echo -e ${NIGHTLY_CONFIGSTRING}"\n"
				unset IFS
				read -p "Do you wish to build a new nightly using this configuration? [y|n] " yn
				case $yn in
					[Yy]* ) BUILD_NIGHTLY=1; echo -e "\nLet's start building the nightly version\n"; break;;
					[Nn]* ) QUESTION=2;;
					* ) echo "Please answer yes [Yy] or no [Nn].";;
				esac;;
			2)
				read -p "Correct the configuration of the new nightly? [y|n] " yn
				case $yn in
					[Yy]* ) QUESTION=3;;
					[Nn]* ) echo -e "\nOk, no new nightly at the moment..."; break;;
					* ) echo "Please answer yes [Yy] or no [Nn].";;
				esac;;
			3)
				read -p "Please press [c] to continue when you are finished, or abort. [c|a] " ca
				case $ca in
					[Cc]* ) QUESTION=1;;
					[Aa]* ) echo -e "\nOk, no new nightly at the moment..."; break;;
					* ) echo "Please answer continue [c] or abort [a].";;
				esac;;
		esac
	done
fi

if [ ${BUILD_NIGHTLY} == 1 ]
then
	echo -en "Build new nightly"${NIGHTLY_VERSION}": "
	RESPONSE=0
	RESPONSE="$(php56 -q we_builder.php -b ${BRANCH} -t ${NIGHTLY})"

	if [ "${RESPONSE}" = "11" ]
	then
		echo -e "     done"

		# cp we_version to svn and commit: move to separate script
		echo -en "cp we_version and commit:"
		NIGHTLY_BRANCH_DIR=$(php56 -q ${GET_PARAM_PHP} -b ${BRANCH} -t ${NIGHTLY} -v ${HOTFIX_SN} -p targetBranchDir)
		SVN_BRANCH_DIR=${SVN_DIR}${NIGHTLY_BRANCH_DIR}/
		WE_VERSION_FROM=${UPDATE_VERSIONS_DIR}version${NIGHTLY_VERSION}/files/none/webEdition/we/include/we_version.php

		if [[ -f ${WE_VERSION_FROM} ]] ; then
			cd ${SVN_BRANCH_DIR}
			cp -u ${WE_VERSION_FROM} ${SVN_BRANCH_DIR}webEdition/we/include/we_version.php
			FAILED=$?
			svn -q ci -m "cli builder: we_version for new nightly" webEdition/we/include/we_version.php
			FAILED=${FAILED}$?
			if [ "${FAILED}" = "00" ]; then
				echo "   done"
			else
				echo "   failed"
			fi
		else 
			echo "   failed"
		fi
	else
		echo "     failed\n"
		echo -e "\nBuilder response:\n"
		echo -e ${RESPONSE}"\n"
	fi
fi

# commit we_version from build


# PROMPT: DELETE OBSOLETE VERSIONS?
if [ "${CONF_DELETEVERSIONS}" == "0" ]
then
	DELETE=0
	echo -en "There are no versions to delete."
else 
	echo -e "\nThere are versions to delete: "${CONF_DELETEVERSIONS}"\n"

	IFS=','
	VERSIONS=($CONF_DELETEVERSIONS)
	IFS='%'
	for v in "${VERSIONS[@]}"; do
		while true; do
			read -p "Delete ${v}? [y|n]" yn
			case $yn in
				[Yy]* ) DELETE=1; break;;
				[Nn]* ) DELETE=0; break;;
				* ) echo "Please answer yes [Yy] or no [Nn].";;
			esac
		done

		if [ ${DELETE} == 1 ]
		then
			RESPONSE=0
			RESPONSE=$(php56 -q ${HOME}scripts/deleteVersions.php -v "${v}")

			if [ ${RESPONSE} -eq 1 ]
			then
				echo -e "...done\n"
				rm -rf ${BASEDIR}"sites/webedition.org/update/htdocs/files/we/version"${v}
			else
				echo -e "...failed"
				echo -e "Response:"
				echo -e ${RESPONSE}
			fi
		fi
	done
fi

# BYE
echo -e "\nWe are all done. See you next time!\n"
exit
