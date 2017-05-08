<?php
$GLOBALS['lang'] = array('installer' => array(
		'alpha' => 'Alpha',
		'amountDatabaseQueries' => '%s von %s Datenbankqueries wurden ausgef&uuml;hrt.',
		'amountFilesCopied' => '%s von %s Dateien wurden installiert.',
		'amountFilesDownloaded' => '%s von %s Dateien wurden heruntergeladen.',
		'amountFilesPrepared' => '%s von %s Dateien wurden vorbereitet.',
		'beta' => 'Beta',
		'downloadFilesFiles' => 'Dateien',
		'downloadFilesPatches' => 'Patches',
		'downloadFilesQueries' => 'Datenbankanfragen',
		'downloadFilesTotal' => 'Diese Installation ben&ouml;tigt %s Dateien',
		'entryAlreadyExists' => 'Eintr&auml;ge sind schon vorhanden',
		'errorAtStep' => 'Fehler beim Schritt: ',
		'errorExecutingQuery' => 'Einige Datenbankanfragen konnten nicht durchgef&uuml;hrt werden.',
		'errorIn' => 'in',
		'errorLine' => 'Zeile',
		'errorMessage' => 'Fehlermeldung',
		'errorMoveFile' => 'Konnte Datei nicht installieren, bitte &uuml;berpr&uuml;fen Sie ob PHP (Apache) Schreibrecht auf das webEdition Verzeichnis (inkl. Dateien) hat.',
		'nightly' => 'nightly Build',
		'nightly-build' => 'nightly Build',
		'rc' => 'RC',
		'release' => 'offizieller Release',
		'retired' => '=&gt; zur&uuml;ckgezogener Release!',
		'tableChanged' => 'Tabelle wurde aktualisiert',
		'tableExists' => 'Tabelle existiert bereits',
		'tableNotDrop' => 'Bestehende webEdition-Tabellen konnten nicht gel&ouml;scht werden. Bitte pr&uuml;fen Sie, ob der Datenbankbenutzer &uuml;ber die ben&ouml;tigten Rechte verf&uuml;gt.',
		'tableReCreated' => 'Tabelle wurde neu gespeichert',
		'updateDatabaseNotice' => 'Hinweis beim Schritt: Datenbank anlegen',
	),
	'installApplication' => array(
		'copyApplicationFiles' => 'Daten installieren',
		'dbNotInsertPrefs' => 'Konnte Einstellungen nicht abspeichern.',
		'dbNotInsertUser' => 'Konnte Benutzer nicht anlegen',
		'determineApplicationFiles' => 'Ben&ouml;tigte Daten ermitteln',
		'downloadApplicationFiles' => 'Daten herunterladen',
		'finished' => 'Installation abgeschlossen',
		'module_must_be_reinstalled' => 'Dieses Modul war auf dieser Domain bereits installiert und muss daher wieder installiert werden.',
		'prepareApplicationFiles' => 'Daten vorbereiten',
		'prepareApplicationInstallation' => 'Installation vorbereiten',
		'rss_feed_url' => 'https://www.webedition.org/de/rss/webedition.xml',
		'updateApplicationDatabase' => 'Datenbank einrichten',
		'writeApplicationConfiguration' => 'webEdition konfigurieren',
	),
	'installerDownload' => array(
		'determineInstallerFiles' => 'Ben&ouml;tigte Daten ermitteln',
		'downloadInstallerFiles' => 'Daten herunterladen',
		'prepareInstallerFiles' => 'Daten vorbereiten',
		'copyInstallerFiles' => 'Daten installieren',
	),
);

// installer::getErrorMessage()
$lang["installApplication"]["prepareApplicationInstallationError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["prepareApplicationInstallation"];
$lang["installApplication"]["determineApplicationFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["determineApplicationFiles"];
$lang["installApplication"]["downloadApplicationFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["downloadApplicationFiles"];
$lang["installApplication"]["updateApplicationDatabaseError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["updateApplicationDatabase"];
$lang["installApplication"]["prepareApplicationFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["prepareApplicationFiles"];
$lang["installApplication"]["copyApplicationFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["copyApplicationFiles"];
$lang["installApplication"]["writeApplicationConfigurationError"] = $lang["installer"]["errorAtStep"] . $lang["installApplication"]["writeApplicationConfiguration"];
// installer::getErrorMessage()
$lang["installerDownload"]["determineInstallerFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installerDownload"]["determineInstallerFiles"];
$lang["installerDownload"]["downloadInstallerFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installerDownload"]["downloadInstallerFiles"];
$lang["installerDownload"]["prepareInstallerFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installerDownload"]["prepareInstallerFiles"];
$lang["installerDownload"]["copyInstallerFilesError"] = $lang["installer"]["errorAtStep"] . $lang["installerDownload"]["copyInstallerFiles"];

