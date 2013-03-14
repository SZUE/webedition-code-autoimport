<?php

//
// ---> installer
//

// installer common
$lang["installer"]["errorAtStep"] = "Fehler beim Schritt: ";

// installApplication::getPrepareApplicationInstallationResponse()
$lang["installer"]["tableNotDrop"] = "Bestehende webEdition-Tabellen konnten nicht gel&ouml;scht werden. Bitte pr&uuml;fen Sie, ob der Datenbankbenutzer &uuml;ber die ben&ouml;tigten Rechte verf&uuml;gt.";

// installApplication::getApplicationFilesResponse()
// installerDownload::getGetInstallerFilesResponse()
$lang["installer"]["downloadFilesTotal"] = "Diese Installation ben&ouml;tigt %s Dateien";
$lang["installer"]["downloadFilesFiles"] = "Dateien";

// installApplication::getApplicationFilesResponse()
$lang["installer"]["downloadFilesQueries"] = "Datenbankanfragen";
$lang["installer"]["downloadFilesPatches"] = "Patches";

// installApplication::getUpdateApplicationDatabaseResponse()
$lang["installer"]["updateDatabaseNotice"] = "Hinweis beim Schritt: Datenbank anlegen";
$lang["installer"]["tableExists"] = "Tabelle existiert bereits";
$lang["installer"]["tableReCreated"] = "Tabelle wurde neu gespeichert";
$lang["installer"]["tableChanged"] = "Tabelle wurde aktualisiert";
$lang["installer"]["entryAlreadyExists"] = "Eintr&auml;ge sind schon vorhanden";
$lang["installer"]["errorExecutingQuery"] = "Einige Datenbankanfragen konnten nicht durchgef&uuml;hrt werden.";

// installApplication::getCopyApplicationFilesResponse()
// installerDownload::getCopyFilesResponse()
$lang["installer"]["amountFilesCopied"] = "%s von %s Dateien wurden installiert.";

// installApplication::getCopyApplicationFilesResponse()
// installApplication::getWriteApplicationConfigurationResponse()
// installerDownload::getCopyFilesResponse()
$lang["installer"]["errorMoveFile"] = "Konnte Datei nicht installieren, bitte &uuml;berpr&uuml;fen Sie ob PHP (Apache) Schreibrecht auf das webEdition Verzeichnis (inkl. Dateien) hat.";


// installer::_getDownloadFilesResponse()
$lang["installer"]["amountFilesDownloaded"] = "%s von %s Dateien wurden heruntergeladen.";

// installerDownload::getPrepareInstallerFilesResponse()
$lang["installer"]["amountFilesPrepared"] = "%s von %s Dateien wurden vorbereitet.";

// installApplication::getUpdateApplicationDatabaseResponse()
$lang["installer"]["amountDatabaseQueries"] = "%s von %s Datenbankqueries wurden ausgef&uuml;hrt.";


// installer::getErrorMessage()
$lang["installer"]["errorMessage"] = "Fehlermeldung";
$lang["installer"]["errorIn"] = "in";
$lang["installer"]["errorLine"] = "Zeile";

$lang["installer"]['nightly-build'] = 'nightly Build';
$lang["installer"]['alpha'] = 'Alpha';
$lang["installer"]['beta'] = 'Beta';
$lang["installer"]['rc'] = 'RC';
$lang["installer"]['release'] = 'offizieller Release';


//
// ---> installApplication
//

// installer::getProceedNextCommandResponsePart()
$lang["installApplication"]["prepareApplicationInstallation"] = "Installation vorbereiten";
$lang["installApplication"]["determineApplicationFiles"] = "Ben&ouml;tigte Daten ermitteln";
$lang["installApplication"]["downloadApplicationFiles"] = "Daten herunterladen";
$lang["installApplication"]["updateApplicationDatabase"] = "Datenbank einrichten";
$lang["installApplication"]["prepareApplicationFiles"] = "Daten vorbereiten";
$lang["installApplication"]["copyApplicationFiles"] = "Daten installieren";
$lang["installApplication"]["writeApplicationConfiguration"] = "webEdition konfigurieren";

// installer::getErrorMessage()
$lang["installApplication"]["prepareApplicationInstallationError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["prepareApplicationInstallation"];
$lang["installApplication"]["determineApplicationFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["determineApplicationFiles"];
$lang["installApplication"]["downloadApplicationFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["downloadApplicationFiles"];
$lang["installApplication"]["updateApplicationDatabaseError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["updateApplicationDatabase"];
$lang["installApplication"]["prepareApplicationFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["prepareApplicationFiles"];
$lang["installApplication"]["copyApplicationFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["copyApplicationFiles"];
$lang["installApplication"]["writeApplicationConfigurationError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["writeApplicationConfiguration"];

// installApplication::getWriteApplicationConfigurationResponse()
$lang["installApplication"]["dbNotInsertUser"] = "Konnte Benutzer nicht anlegen";
$lang["installApplication"]["dbNotInsertPrefs"] = "Konnte Einstellungen nicht abspeichern.";
$lang["installApplication"]["finished"] = "Installation abgeschlossen";

$lang["installApplication"]["module_must_be_reinstalled"] = "Dieses Modul war auf dieser Domain bereits installiert und muss daher wieder installiert werden.";
$lang["installApplication"]["rss_feed_url"] = "http://www.living-e.de/de/pressezentrum/pr-mitteilungen/rss2.xml";


//
// ---> installerDownload
//

// installer::getProceedNextCommandResponsePart()
$lang["installerDownload"]["determineInstallerFiles"] = "Ben&ouml;tigte Daten ermitteln";
$lang["installerDownload"]["downloadInstallerFiles"] = "Daten herunterladen";
$lang["installerDownload"]["prepareInstallerFiles"] = "Daten vorbereiten";
$lang["installerDownload"]["copyInstallerFiles"] = "Daten installieren";

// installer::getErrorMessage()
$lang["installerDownload"]["determineInstallerFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installerDownload"]["determineInstallerFiles"];
$lang["installerDownload"]["downloadInstallerFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installerDownload"]["downloadInstallerFiles"];
$lang["installerDownload"]["prepareInstallerFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installerDownload"]["prepareInstallerFiles"];
$lang["installerDownload"]["copyInstallerFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installerDownload"]["copyInstallerFiles"];

?>