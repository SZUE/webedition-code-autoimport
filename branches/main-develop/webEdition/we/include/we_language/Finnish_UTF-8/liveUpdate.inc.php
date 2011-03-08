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
				'register' => 'Rekisteröinti',
				'update' => 'Päivitä',
				'upgrade' => 'Päivitä webEdition 5',
				'modules' => 'Moduulit',
				'updatelog' => 'Loki',
				'languages' => 'Kielet',
				'connect' => 'Testaa yhteyttä',
				'nextVersion' => 'Seuraava versio',
		),
		'register' => array(
				'headline' => 'rekisteröi webEdition',
				'description' => 'rekisteröi webEdition ja löydä sen täysi toiminnallisuus.',
		),
		'update' => array(
				'headline' => 'Etsi uutta versiota',
				'actualVersion' => 'Nykyinen versio',
				'lastUpdate' => 'Edellinen asennus',
				'neverUpdated' => '-',
				'lookForUpdate' => 'Etsi päivitystä',
		),
		'upgrade' => array(
				'headline' => 'Päivitä versioon webEdition 5',
				'actualVersion' => 'Nykyinen versio',
				'lookForUpdate' => 'Asenna webEdition 5',
		),
		'modules' => array(
				'headline' => 'Moduulien asennus',
				'installedModules' => 'Asennetut moduulit',
				'noModulesInstalled' => 'Moduuleja ei ole vielä asennettu',
				'showModules' => 'Valitse moduulit',
		),
		'languages' => array(
				'headline' => 'Kielien asennus',
				'installedLngs' => 'Seuraavat kielet on asennettu',
				'showLanguages' => 'Asenna lisää kieliä',
				'deleteSelectedLanguages' => 'Poista valitut kielet',
				'systemLanguage' => 'järkestelmän kieli',
				'usedLanguage' => 'käytössä oleva kieli',
				'languagesDeleted' => 'Seuraavat kielet poistettiin',
				'languagesNotDeleted' => 'Seuraavia kieliä ei voitu poistaa',
		),
		'connect' => array(
				'headline' => 'Tarkista yhteys päivityspalvelimeen',
				'description' => 'Jos päivityksessä on ongelmia, voit täällä testata onko yhteyden luonti päivityspalvelimeen mahdollista.',
				'connectionSuccess' => 'Yhteys päivityspalvelimeen saatiin muodostettua.',
				'connectionSuccessError' => 'Yhteys päivityspalvelimeen saatiin luotua mutta päivityspalvelimella tapahtui virhe.<br />',
				'connectionError' => 'Tällä hetkellä yhteyttä päivityspalvelimeen ei saada muodostettua',
				'connectionErrorJs' => 'Yhteyden muodostaminen päivityspalvelimeen ei ole mahdollista',
				'connectionInfo' => "Yhteystiedot",
				'availableConnectionTypes' => "Saatavilla olevat yhteystyypit",
				'connectionType' => "Käytetty yhteystyyppi",
				'proxyHost' => "Proxy isäntä",
				'proxyPort' => "Proxy portti",
				'hostName' => "Isäntänimi",
				'addressResolution' => "Osoitteen selvitys",
				'updateServer' => "Päivitä palvelin",
				'ipResolutionTest' => "IP-osoitteen selvitystesti",
				'dnsResolutionTest' => "DNS selvitystesti",
				'succeeded' => "Onnistui",
				'failed' => "Epäonnistui",
				'ipAddresses' => "IP osoite(es)",
		),
		'state' => array(
				'headline' => 'Viesti päivityspalvelimelta',
				'descriptionTrue' => 'Päivityspalvelin on suorittanut tämän pyynnön.',
				'descriptionError' => 'Päivityspalvelin ei voinut suorittaa tätä pyyntöä. Seuraava ongelma ilmeni.',
		),
		'updatelog' => array(
				'headline' => 'Päivitysloki',
				'logIsEmpty' => 'Päivitysloki on tyhjä',
				'date' => 'Pvm / aika',
				'action' => 'Toiminto',
				'version' => 'Versio',
				'entriesTotal' => 'Merkintöjä yhteensä',
				'page' => 'sivu',
				'noEntriesMatchFilter' => 'Suodatuksen ehtoihin sopivia merkintöjä ei ole.',
				'legendMessages' => 'Viesti',
				'legendNotices' => 'Huomautukset',
				'legendErrors' => 'Virheet',
				'confirmDelete' => 'Haluatko varmasti poistaa valitut kohteet?',
		),
		'beta' => array(
				'headline' => 'Shall Pre-Release Versions be included in the search?', // TRANSLATE
				'lookForUpdate' => 'search for Pre-Release Versions', // TRANSLATE
				'warning' => '<b>Pre-Release versions,<br/> such as <u>nightly Builds, Alpha-, Beta- und RC-Versions</u>,<br/> should never be used in produktion sites!</b><br/><br/>They are provided for testing purposes only and aim at easing <br/>the search for severe erros before an official version is published.', // TRANSLATE
		),
);
