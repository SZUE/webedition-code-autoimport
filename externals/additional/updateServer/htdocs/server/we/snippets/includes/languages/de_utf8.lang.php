<?php

//
// ---> installer
//

// installer common
$lang["installer"]["errorAtStep"] = "Fehler beim Schritt: ";

// installApplication::getPrepareApplicationInstallationResponse()
$lang["installer"]["tableNotDrop"] = "Bestehende webEdition-Tabellen konnten nicht gelöscht werden. Bitte prüfen Sie, ob der Datenbankbenutzer über die benötigten Rechte verfügt.";

// installApplication::getApplicationFilesResponse()
// installerDownload::getGetInstallerFilesResponse()
$lang["installer"]["downloadFilesTotal"] = "Diese Installation benötigt %s Dateien";
$lang["installer"]["downloadFilesFiles"] = "Dateien";

// installApplication::getApplicationFilesResponse()
$lang["installer"]["downloadFilesQueries"] = "Datenbankanfragen";
$lang["installer"]["downloadFilesPatches"] = "Patches";

// installApplication::getUpdateApplicationDatabaseResponse()
$lang["installer"]["updateDatabaseNotice"] = "Hinweis beim Schritt: Datenbank anlegen";
$lang["installer"]["tableExists"] = "Tabelle existiert bereits";
$lang["installer"]["tableReCreated"] = "Tabelle wurde neu gespeichert";
$lang["installer"]["tableChanged"] = "Tabelle wurde aktualisiert";
$lang["installer"]["entryAlreadyExists"] = "Einträge sind schon vorhanden";
$lang["installer"]["errorExecutingQuery"] = "Einige Datenbankanfragen konnten nicht durchgeführt werden.";

// installApplication::getCopyApplicationFilesResponse()
// installerDownload::getCopyFilesResponse()
$lang["installer"]["amountFilesCopied"] = "Dateien %s bis %s von %s wurden installiert.";

// installApplication::getCopyApplicationFilesResponse()
// installApplication::getWriteApplicationConfigurationResponse()
// installerDownload::getCopyFilesResponse()
$lang["installer"]["errorMoveFile"] = "Konnte Datei nicht installieren, bitte überprüfen Sie ob PHP (Apache) Schreibrecht auf das webEdition Verzeichnis (inkl. Dateien) hat.";


// installer::_getDownloadFilesResponse()
$lang["installer"]["amountFilesDownloaded"] = "Dateien %s bis %s wurden heruntergeladen.";

// installerDownload::getPrepareInstallerFilesResponse()
$lang["installer"]["amountFilesPrepared"] = "Dateien %s bis %s wurden vorbereitet.";

// installApplication::getUpdateApplicationDatabaseResponse()
$lang["installer"]["amountDatabaseQueries"] = "Datenbankqueries %s bis %s wurden ausgeführt.";


// installer::getErrorMessage()
$lang["installer"]["errorMessage"] = "Fehlermeldung";
$lang["installer"]["errorIn"] = "in";
$lang["installer"]["errorLine"] = "Zeile";

//
// ---> downloadSnippet
//

$lang["downloadSnippet"]["noImportTypeSet"] = "Fehler: Es wurde kein Import Typ angegeben.";

// installer::getProceedNextCommandResponsePart()
$lang["downloadSnippet"]["determineFiles"] = "Benötigte Daten ermitteln";
$lang["downloadSnippet"]["downloadFiles"] = "Daten herunterladen";

// installer::getErrorMessage()
$lang["downloadSnippet"]["determineFilesError"] = $lang["installer"]["errorAtStep"] . $lang["downloadSnippet"]["determineFiles"];
$lang["downloadSnippet"]["downloadFilesError"] = $lang["installer"]["errorAtStep"] . $lang["downloadSnippet"]["downloadFiles"];

?>