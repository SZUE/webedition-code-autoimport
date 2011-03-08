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
/**
 * Language file: we_editor.inc.php
 * Provides language strings.
 * Language: English
 */
$l__tmp = array(
		'filename_empty' => "Dokumentille ei ole annettu nimeä!",
		'we_filename_notValid' => "Virheellinen tiedoston nimi!\\nSallitut merkit ovat alfa-numeerisia, isot ja pienet kirjaimet, alaviiva, tavuviiva ja piste (a-z, A-Z, 0-9, _, -, .)",
		'we_filename_notAllowed' => "Annettu tiedoston nimi ei ole sallittu!",
		'response_save_noperms_to_create_folders' => "Dokumenttia ei voitu tallentaa koska sinulla ei ole riittäviä oikeuksia luoda kansioita (%s)!",
);
$l_weEditor = array(
		'doubble_field_alert' => "Kenttä '%s' on jo olemassa! Kentän nimi on oltava yksilöllinen!",
		'variantNameInvalid' => "Artikkelimuuttujan nimi ei voi olla tyhjä!",
		'folder_save_nok_parent_same' => "Valittu juurihakemisto on samanniminen kuin nykyinen hakemisto! Valitse toinen hakemisto ja yritä uudelleen!",
		'pfolder_notsave' => "Hakemistoa ei voida tallentaa valittuun hakemistoon!",
		'required_field_alert' => "Kenttä '%s' on pakollinen!",
		'category' => array(
				'response_save_ok' => "Kategoriaa '%s' on tallennettu!",
				'response_save_notok' => "Virhe tallennettaessa kategoriaa '%s'!",
				'response_path_exists' => "Kategoriaa '%s' ei voitu tallentaa koska toinen kategoria sijaitsee samassa kohteessa!",
				'we_filename_notValid' => "Virheellinen nimi!\\nMerkit \", \\' < > ja / eivät ole sallittuja!",
				'filename_empty' => "Tiedoston nimi ei voi olla tyhjä.",
				'name_komma' => "Virheellinen nimi! Pilkku ei ole sallittu!",
		),
		'text/webedition' => array_merge($l__tmp, array(
				'response_save_ok' => "webEdition -sivu '%s' on tallennettu!",
				'response_publish_ok' => "webEdition -sivu '%s' on julkaistu!",
				'response_publish_notok' => "Virhe julkaistaessa webEdition -sivua '%s'!",
				'response_unpublish_ok' => "webEdition -sivu '%s' on poistettu julkaisusta!",
				'response_unpublish_notok' => "Virhe poistettaessa julkaisusta webEdition -sivua '%s'!",
				'response_not_published' => "webEdition -sivu '%s' ei ole julkaistu!",
				'response_save_notok' => "Virhe tallennettaessa webEdition -sivua '%s'!",
				'response_path_exists' => "webEdition sivua '%s' ei voitu tallentaa, koska toinen dokumentti tai hakemisto sijaisee samassa kohteessa!",
				'autoschedule' => "webEdition -sivu julkaistaan automaattisesti %s.",
		)),
		'text/html' => array_merge($l__tmp, array(
				'response_save_ok' => "HTML -sivu '%s' on tallennettu!",
				'response_publish_ok' => "HTML -sivu '%s' on julkaistu!",
				'response_publish_notok' => "Virhe julkaistaessa HTML -sivua '%s'!",
				'response_unpublish_ok' => "HTML -sivu '%s' on poistettu julkaisusta!",
				'response_unpublish_notok' => "Virhe poistettaessa julkaisusta HTML -sivua '%s'!",
				'response_not_published' => "HTML -sivua '%s' ei ole julkaistu!",
				'response_save_notok' => "Virhe tallennettaessa HTML -sivua '%s'!",
				'response_path_exists' => "HTML -sivua '%s' ei voitu tallentaa koska toinen dokumentti tai hakemisto sijaitsee samassa kohteessa!",
				'autoschedule' => "The HTML page will be published automatically on %s.",
		)),
		'text/weTmpl' => array_merge($l__tmp, array(
				'response_save_ok' => "Sivupohja '%s' on tallennettu!",
				'response_publish_ok' => "Sivupohja '%s' on julkaistu!",
				'response_unpublish_ok' => "Sivupohja '%s' on poistettu julkaisusta!",
				'response_save_notok' => "Virhe tallennettaessa sivupohjaa '%s'!",
				'response_path_exists' => "Sivupohjaa '%s' ei voitu tallentaa koska toinen sivupohja tai hakemisto sijaitsee samassa kohteessa!",
				'no_template_save' => "Templates " . "can " . "not " . "saved " . "in the " . "de" . "mo" . " of" . " webEdition.",
		)),
		'text/css' => array_merge($l__tmp, array(
				'response_save_ok' => "Tyylitiedosto '%s' on tallennettu!",
				'response_publish_ok' => "Tyylitiedosto '%s' on julkaistu!",
				'response_unpublish_ok' => "Tyylitiedosto '%s' on poistettu julkaisusta!",
				'response_save_notok' => "Virhe tallennettaessa tyylitiedostoa '%s'!",
				'response_path_exists' => "Tyylitiedostoa '%s' ei voitu tallentaa, koska samanniminen tiedosto tai hakemisto sijaitsee samassa kohteessa!",
		)),
		'text/js' => array_merge($l__tmp, array(
				'response_save_ok' => "JavaScript '%s' tallennettu!",
				'response_publish_ok' => "JavaScript -tiedosto '%s' on julkaistu!",
				'response_unpublish_ok' => "JavaScript -tiedosto '%s' on poistettu julkaisusta!",
				'response_save_notok' => "Virhe tallennettaessa JavaScript -tiedostoa '%s'!",
				'response_path_exists' => "JavaScript -tiedostoa '%s' ei voitu tallenntaa, koska samanniminen tiedosto tai hakemisto sijaitsee kohteessa!",
		)),
		'text/plain' => array_merge($l__tmp, array(
				'response_save_ok' => "Tekstitiedosto '%s' tallennettu!",
				'response_publish_ok' => "Tekstitiedosto '%s' on julkaistu!",
				'response_unpublish_ok' => "Tekstitiedosto '%s' on poistettu julkaisusta!",
				'response_save_notok' => "Virhe tallennettaessa tekstitiedostoa '%s'!",
				'response_path_exists' => "Tekstitiedostoa '%s' ei voitu tallentaa, koska samanniminen tiedosto tai hakemisto sijaitsee kohteessa!",
		)),
		'text/htaccess' => array_merge($l__tmp, array(
				'response_save_ok' => "The file '%s' has been successfully saved!", //TRANSLATE
				'response_publish_ok' => "The file '%s' has been successfully published!", //TRANSLATE
				'response_unpublish_ok' => "The file '%s' has been successfully unpublished!", //TRANSLATE
				'response_save_notok' => "Error while saving the file '%s'!", //TRANSLATE
				'response_path_exists' => "The file '%s' could not be saved because another document or directory is positioned at the same location!", //TRANSLATE
		)),
		'text/xml' => array_merge($l__tmp, array(
				'response_save_ok' => "XML -tiedosto '%s' onnistuneesti tallennettu!",
				'response_publish_ok' => "XML -tiedosto '%s' onnistuneesti julkaistu!",
				'response_unpublish_ok' => "XML -tiedosto '%s' onnistuneesti poistettu julkaisusta!",
				'response_save_notok' => "Virhe tallentaessa XML -tiedostoa '%s'!",
				'response_path_exists' => "XML -tiedostoa '%s' ei voitu tallentaa koska toinen dokumentti tai hakemisto sijaitsee samassa paikassa!",
		)),
		'folder' => array(
				'response_save_ok' => "Hakemisto '%s' on tallennettu!",
				'response_publish_ok' => "Hakemisto '%s' on julkaistu!",
				'response_unpublish_ok' => "Hakemisto '%s' on poistettu julkaisusta!",
				'response_save_notok' => "Virhe hakemiston '%s' tallennuksessa!",
				'response_path_exists' => "Hakemistoa '%s' ei voitu tallentaa koska samanniminen tiedosto tai hakemisto sijaitsee kohteessa!",
				'filename_empty' => "Hakemistolle ei ole annettu nimeä!",
				'we_filename_notValid' => "Virheellinen hakemiston nimi\\nSallitut merkit ovat alfa-numeerisia, isot ja pienet kirjaimet, alaviiva, tavuviiva ja piste (a-z, A-Z, 0-9, _, -, .)",
				'we_filename_notAllowed' => "Hakemiston nimi ei ole sallittu!",
				'response_save_noperms_to_create_folders' => "Hakemistoa ei voitu tallentaa, koska sinulla ei ole tarvittavia oikeuksia luoda kasioita (%s)!",
		),
		'image/*' => array_merge($l__tmp, array(
				'response_save_ok' => "Kuva '%s' on tallennettu!",
				'response_publish_ok' => "Kuva '%s' on julkaistu!",
				'response_unpublish_ok' => "Kuva '%s' on poistettu julkaisusta!",
				'response_save_notok' => "Virhe tallennettaessa kuvaa '%s'!",
				'response_path_exists' => "Kuvaa '%s' ei voitu tallentaa, koska samanniminen tiedosto tai hakemisto sijaitsee kohteessa!",
		)),
		'application/*' => array_merge($l__tmp, array(
				'response_save_ok' => "Dokumentti '%s' tallennettu!",
				'response_publish_ok' => "Dokumentti '%s' on julkaistu!",
				'response_unpublish_ok' => "Dokumentti '%s' on poistettu julkaisusta!",
				'response_save_notok' => "Virhe tallennettaessa dokumenttia '%s'!",
				'response_path_exists' => "Dokumenttia '%s' ei voitu tallentaa, koska samanniminen tiedosto tai hakemisto sijaitsee kohteessa!",
				'we_description_missing' => "Please enter a desription in the 'Desription' field!",
				'response_save_wrongExtension' => "Tallennettaessa tiedostoa '%s' tapahtui virhe! \\nMuut tiedostot -tyyppisen tiedoston pääte ei voi olla '%s'!\\nOle hyvä ja luo HTML-sivu tähän tarkoitukseen!",
		)),
		'application/x-shockwave-flash' => array_merge($l__tmp, array(
				'response_save_ok' => "Flash -tiedosto '%s' on tallennettu!",
				'response_publish_ok' => "Flash -tiedosto '%s' on julkaistu!",
				'response_unpublish_ok' => "Flash -tiedosto '%s' on poistettu julkaisusta!",
				'response_save_notok' => "Virhe tallennettaessa Flash -tiedostoa '%s'!",
				'response_path_exists' => "Flash -tiedostoa '%s' ei voitu tallentaa, koska samanniminen tiedosto tai hakemisto sijaitsee kohteessa!",
		)),
		'video/quicktime' => array_merge($l__tmp, array(
				'response_save_ok' => "Quicktime -tiedosto '%s' on tallennettu!",
				'response_publish_ok' => "Quicktime -tiedosto '%s' on julkaistu!",
				'response_unpublish_ok' => "Quicktime -tiedosto '%s' on poistettu julkaisusta!",
				'response_save_notok' => "Virhe tallennettaessa Quicktime -tiedostoa '%s'!",
				'response_path_exists' => "Quicktime -tiedostoa '%s' ei voitu tallentaa koska samanniminen tiedosto tai hakemisto sijaitsee kohteessa!",
		)),
);


/* * ***************************************************************************
 * PLEASE DON'T TOUCH THE NEXT LINES
 * UNLESS YOU KNOW EXACTLY WHAT YOU ARE DOING!
 * *************************************************************************** */

$_language_directory = dirname(__FILE__) . "/modules";
$_directory = dir($_language_directory);

while (false !== ($entry = $_directory->read())) {
	if (strstr($entry, '_we_editor')) {
		include($_language_directory . "/" . $entry);
	}
}
