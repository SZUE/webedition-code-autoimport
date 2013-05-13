<?php

//
// ---> installer
//

// installer common
$lang["installer"]["errorAtStep"] = "Error during step: ";

// installApplication::getPrepareApplicationInstallationResponse()
$lang["installer"]["tableNotDrop"] = "Existing webEdition-Tables could not be deleted. Please check, if your database user has the privileges to drop tables.";

// installApplication::getApplicationFilesResponse()
// installerDownload::getGetInstallerFilesResponse()
$lang["installer"]["downloadFilesTotal"] = "This installation requires %s files";
$lang["installer"]["downloadFilesFiles"] = "files";

// installApplication::getApplicationFilesResponse()
$lang["installer"]["downloadFilesQueries"] = "Database queries";
$lang["installer"]["downloadFilesPatches"] = "Patches";

// installApplication::getUpdateApplicationDatabaseResponse()
$lang["installer"]["updateDatabaseNotice"] = "Notice during Step: Create database";
$lang["installer"]["tableExists"] = "Table already exists";
$lang["installer"]["tableReCreated"] = "Table was recreated";
$lang["installer"]["tableChanged"] = "Table updated";
$lang["installer"]["entryAlreadyExists"] = "Entries already exist";
$lang["installer"]["errorExecutingQuery"] = "Some queries failed.";

// installApplication::getCopyApplicationFilesResponse()
// installerDownload::getCopyFilesResponse()
$lang["installer"]["amountFilesCopied"] = "Files %s to %s installed";

// installApplication::getCopyApplicationFilesResponse()
// installApplication::getWriteApplicationConfigurationResponse()
// installerDownload::getCopyFilesResponse()
$lang["installer"]["errorMoveFile"] = "Could not install file, please verify if PHP (Apache) has write permission in the webEdition directory (and files).";


// installer::_getDownloadFilesResponse()
$lang["installer"]["amountFilesDownloaded"] = "%s of %s Files downloaded.";

// installerDownload::getPrepareInstallerFilesResponse()
$lang["installer"]["amountFilesPrepared"] = "%s of %s Files prepared.";

// installApplication::getUpdateApplicationDatabaseResponse()
$lang["installer"]["amountDatabaseQueries"] = "%s of %s Queries executed.";


// installer::getErrorMessage()
$lang["installer"]["errorMessage"] = "Error message";
$lang["installer"]["errorIn"] = "at";
$lang["installer"]["errorLine"] = "line";


//
// ---> installApplication
//

// installer::getProceedNextCommandResponsePart()
$lang["installApplication"]["prepareApplicationInstallation"] = "Prepare application installation";
$lang["installApplication"]["determineApplicationFiles"] = "Determine application files";
$lang["installApplication"]["downloadApplicationFiles"] = "Download application files";
$lang["installApplication"]["updateApplicationDatabase"] = "Setup database";
$lang["installApplication"]["prepareApplicationFiles"] = "Prepare application files";
$lang["installApplication"]["copyApplicationFiles"] = "Copy application files";
$lang["installApplication"]["writeApplicationConfiguration"] = "Configure webEdition";


// installer::getErrorMessage()
$lang["installApplication"]["prepareApplicationInstallationError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["prepareApplicationInstallation"];
$lang["installApplication"]["determineApplicationFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["determineApplicationFiles"];
$lang["installApplication"]["downloadApplicationFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["downloadApplicationFiles"];
$lang["installApplication"]["updateApplicationDatabaseError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["updateApplicationDatabase"];
$lang["installApplication"]["prepareApplicationFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["prepareApplicationFiles"];
$lang["installApplication"]["copyApplicationFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["copyApplicationFiles"];
$lang["installApplication"]["writeApplicationConfigurationError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["writeApplicationConfiguration"];

// installApplication::getWriteApplicationConfigurationResponse()
$lang["installApplication"]["dbNotInsertUser"] = "Could not create user";
$lang["installApplication"]["dbNotInsertPrefs"] = "Could not save preferences.";
$lang["installApplication"]["finished"] = "Installation finished";

$lang["installApplication"]["module_must_be_reinstalled"] = "This module was already installed at this domain. You have to reinstall this module.";
$lang["installApplication"]["rss_feed_url"] = "http://www.living-e.de/en/press-center/press-releases/rss2.xml";


//
// ---> installerDownload
//

// installer::getProceedNextCommandResponsePart()
$lang["installerDownload"]["determineInstallerFiles"] = "Determine installer files";
$lang["installerDownload"]["downloadInstallerFiles"] = "Download installer files";
$lang["installerDownload"]["prepareInstallerFiles"] = "Prepare installer files";
$lang["installerDownload"]["copyInstallerFiles"] = "Copy installer files";

// installer::getErrorMessage()
$lang["installerDownload"]["determineInstallerFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installerDownload"]["determineInstallerFiles"];
$lang["installerDownload"]["downloadInstallerFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installerDownload"]["downloadInstallerFiles"];
$lang["installerDownload"]["prepareInstallerFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installerDownload"]["prepareInstallerFiles"];
$lang["installerDownload"]["copyInstallerFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installerDownload"]["copyInstallerFiles"];


$lang["installer"]['nightly-build'] = 'nightly build';
$lang["installer"]['alpha'] = 'Alpha';
$lang["installer"]['beta'] = 'Beta';
$lang["installer"]['rc'] = 'RC';
$lang["installer"]['release'] = 'official release';


?>