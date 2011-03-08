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
				'beta' => 'Pre-Release Versions', // TRANSLATE
				'register' => 'Registratie',
				'update' => 'Bijwerken',
				'upgrade' => 'webEdition 4',
				'modules' => 'Modules', // TRANSLATE
				'updatelog' => 'Log', // TRANSLATE
				'languages' => 'Talen',
				'connect' => 'Test verbinding',
				'nextVersion' => 'Volgende versie',
		),
		'register' => array(
				'headline' => 'registreer webEdition',
				'description' => 'registreer webEdition en ontdek de complete functionaliteit.',
		),
		'update' => array(
				'headline' => 'Zoek naar nieuwe versie',
				'actualVersion' => 'Huidige versie',
				'lastUpdate' => 'Laatste installatie',
				'neverUpdated' => '-', // TRANSLATE
				'lookForUpdate' => 'Zoek naar update',
		),
		'upgrade' => array(
				'headline' => 'Bijwerken naar webEdition 5',
				'actualVersion' => 'Huidige versie',
				'lookForUpdate' => 'Installeer webEdition 5',
		),
		'modules' => array(
				'headline' => 'Installatie van modules',
				'installedModules' => 'Geïnstalleerde modules',
				'noModulesInstalled' => 'Er zijn geen modules geïnstalleerd',
				'showModules' => 'Selecteer modules',
		),
		'languages' => array(
				'headline' => 'Installatie van talen',
				'installedLngs' => 'De volgende talen zijn geïnstalleerd',
				'showLanguages' => 'Installeer meer talen',
				'deleteSelectedLanguages' => 'Verwijder geselecteerde talen',
				'systemLanguage' => 'system taal',
				'usedLanguage' => 'gebruikte taal',
				'languagesDeleted' => 'De volgende talen zijn verwijderd',
				'languagesNotDeleted' => 'De volgende talen konden niet verwijderd worden',
		),
		'connect' => array(
				'headline' => 'Controleer verbinding naar de update server',
				'description' => 'Indien er problemen onstaan tijdens de update, kunt u hier testen of het mogelijk is om een verbinding te openen met de webEdition update server.',
				'connectionSuccess' => 'De verbinding met de update server is gelegd.',
				'connectionSuccessError' => 'De verbinding met de update server is gelegd, maar er is een fout opgetreden op de server.<br />',
				'connectionError' => 'Het is op dit moment niet mogelijk om te verbinden met de update server.',
				'connectionErrorJs' => 'Het is niet mogelijk een verbinding te maken met de update server.',
				'connectionInfo' => "Verbindings informatie",
				'availableConnectionTypes' => "Beschikbare verbindings types",
				'connectionType' => "Gebruikte connectie type",
				'proxyHost' => "Proxy host", // TRANSLATE
				'proxyPort' => "Proxy poort",
				'hostName' => "Hostnaam",
				'addressResolution' => "Adres resolutie",
				'updateServer' => "Update server", // TRANSLATE
				'ipResolutionTest' => "IP resolutie test",
				'dnsResolutionTest' => "DNS resolutie test",
				'succeeded' => "voltooid",
				'failed' => "mislukt",
				'ipAddresses' => "IP adres(sen)",
		),
		'state' => array(
				'headline' => 'Bericht van update server',
				'descriptionTrue' => 'De update server heeft het verzoek afgerond.',
				'descriptionError' => 'De update server kon het verzoek niet afronden. De volgende fout is opgetreden.',
		),
		'updatelog' => array(
				'headline' => 'Update Log', // TRANSLATE
				'logIsEmpty' => 'De update log is leeg',
				'date' => 'Datum / tijd',
				'action' => 'Actie',
				'version' => 'Versie',
				'entriesTotal' => 'Invoer totaal',
				'page' => 'pagina',
				'noEntriesMatchFilter' => 'Er zijn geen invoeren gevonden die passen bij het geselecteerde filter.',
				'legendMessages' => 'Berichten',
				'legendNotices' => 'Meldingen',
				'legendErrors' => 'Fouten',
				'confirmDelete' => 'Do you really want to delete all selected entries?',
		),
		'beta' => array(
				'headline' => 'Shall Pre-Release Versions be included in the search?', // TRANSLATE
				'lookForUpdate' => 'search for Pre-Release Versions', // TRANSLATE
				'warning' => '<b>Pre-Release versions,<br/> such as <u>nightly Builds, Alpha-, Beta- und RC-Versions</u>,<br/> should never be used in produktion sites!</b><br/><br/>They are provided for testing purposes only and aim at easing <br/>the search for severe erros before an official version is published.', // TRANSLATE
		),
);
