<?php

/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_language
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
$l_liveUpdate = array(
		'tabs' => array(
				'beta' => 'Pre-Release Versionen',
				'register' => 'Registrieren',
				'update' => 'Update',
				'upgrade' => 'Update webEdition 5',
				'modules' => 'Modulinstallation',
				'updatelog' => 'Log',
				'languages' => 'Sprachen',
				'connect' => 'Verbindung testen',
				'nextVersion' => 'Nächste Version',
		),
		'register' => array(
				'headline' => 'webEdition registrieren',
				'description' => 'Registrieren Sie webEdition und nutzen sie den vollen Funktionsumfang.',
		),
		'update' => array(
				'headline' => 'Suche nach neuer Version',
				'actualVersion' => 'Installierte Version',
				'lastUpdate' => 'Letzte Installation',
				'neverUpdated' => '-',
				'lookForUpdate' => 'Nach neuer Version suchen',
		),
		'upgrade' => array(
				'headline' => 'Update zu webEdition 5',
				'actualVersion' => 'Installierte Version',
				'lookForUpdate' => 'webEdition 5 installieren',
		),
		'modules' => array(
				'headline' => 'Modulinstallation',
				'installedModules' => 'Installierte Module',
				'noModulesInstalled' => 'Es sind noch keine Module installiert',
				'showModules' => 'Module auswählen',
		),
		'languages' => array(
				'headline' => 'Sprachinstallation',
				'installedLngs' => 'Folgende Sprachen sind auf Ihrem System installiert',
				'showLanguages' => 'Weitere Sprachen installieren',
				'deleteSelectedLanguages' => 'Ausgewählte Sprachen löschen',
				'systemLanguage' => 'Systemsprache',
				'usedLanguage' => 'verwendete Sprache',
				'languagesDeleted' => 'Folgende Sprachen wurden erfolgreich gelöscht',
				'languagesNotDeleted' => 'Folgende Sprachen konnten nicht gelöscht werden',
		),
		'connect' => array(
				'headline' => 'Verbindung zum Server überprüfen',
				'description' => 'Falls Sie Probleme haben, sich mit unserem Update-Server zu verbinden, können Sie hier testen, ob es überhaupt möglich ist, eine Verbindung zu unserem Update-Server aufzubauen.',
				'connectionSuccess' => 'Die Verbindung zum Update-Server konnte aufgebaut werden.',
				'connectionSuccessError' => 'Die Verbindung zum Update-Server konnte zwar aufgebaut werden, allerdings antwortet der Server mit einer Fehlermeldung.<br /><br/>Die Fehlermeldung lautet:',
				'connectionError' => 'Es konnte keine Verbindung zum Update-Server aufgebaut werden.',
				'connectionErrorJs' => 'Momentan kann keine Verbindung zum Update-Server aufgebaut werden.',
				'connectionInfo' => "Verbindungsinformationen",
				'availableConnectionTypes' => "Verfügbare Verbindungstypen",
				'connectionType' => "Verwendeter Verbindungstyp",
				'proxyHost' => "Proxy-Adresse",
				'proxyPort' => "Proxy-Port",
				'hostName' => "Hostname",
				'addressResolution' => "Adressauflösung",
				'updateServer' => "Updateserver",
				'ipResolutionTest' => "IP-Aufl&ouml;sungstest",
				'dnsResolutionTest' => "DNS-Aufl&ouml;sungstest",
				'succeeded' => "erfolgreich",
				'failed' => "fehlgeschlagen",
				'ipAddresses' => "IP-Adresse(n)",
		),
		'state' => array(
				'headline' => 'Meldung vom Update-Server',
				'descriptionTrue' => 'Der Update-Server hat diese Anfrage erfolgreich ausgeführt.',
				'descriptionError' => 'Der Update-Server kann diese Anfrage nicht ausführen, folgender Fehler ist aufgetreten.',
		),
		'updatelog' => array(
				'headline' => 'Installations Log',
				'logIsEmpty' => 'Das Installations Log ist leer',
				'date' => 'Datum / Uhrzeit',
				'action' => 'Aktion',
				'version' => 'Version',
				'entriesTotal' => 'Einträge insgesamt',
				'page' => 'Seite',
				'noEntriesMatchFilter' => 'Mit diesen Einstellungen konnten keine Einträge gefunden werden.',
				'legendMessages' => 'Meldungen',
				'legendNotices' => 'Hinweise',
				'legendErrors' => 'Fehler',
				'confirmDelete' => 'Wenn sie fortfahren, werden alle ausgewählten Einträge gelöscht',
		),
		'beta' => array(
				'headline' => 'Sollen auch Pre-Release Versionen mit in die Suche einbezogen werden?',
				'lookForUpdate' => 'nach Pre-Release Versionen suchen',
				'warning' => '<b>Pre-Release Versionen, <br/>also <u>nightly Builds, Alpha-, Beta- und RC-Versionen</u>,<br/> sollten niemals in Produktions-Sites eingesetzt werden!</b><br/><br/>Sie werden nur zu Testzwecken zur Verfügung gestellt und sollen es erleichtern,<br/> vor der offiziellen Veröffentlichung einer Version Fehler zu finden und zu beheben.',
		),
);
