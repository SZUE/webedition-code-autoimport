<?php

/**
 * webEdition CMS
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
$l_weEditor["doubble_field_alert"] = "Kenttä '%s' on jo olemassa! Kentän nimi on oltava yksilöllinen!";
$l_weEditor["variantNameInvalid"] = "Artikkelimuuttujan nimi ei voi olla tyhjä!";

$l_weEditor["folder_save_nok_parent_same"] = "Valittu juurihakemisto on samanniminen kuin nykyinen hakemisto! Valitse toinen hakemisto ja yritä uudelleen!";
$l_weEditor["pfolder_notsave"] = "Hakemistoa ei voida tallentaa valittuun hakemistoon!";
$l_weEditor["required_field_alert"] = "Kenttä '%s' on pakollinen!";

$l_weEditor["category"]["response_save_ok"] = "Kategoriaa '%s' on tallennettu!";
$l_weEditor["category"]["response_save_notok"] = "Virhe tallennettaessa kategoriaa '%s'!";
$l_weEditor["category"]["response_path_exists"] = "Kategoriaa '%s' ei voitu tallentaa koska toinen kategoria sijaitsee samassa kohteessa!";
$l_weEditor["category"]["we_filename_notValid"] = "Virheellinen nimi!\\nMerkit \", \\' < > ja / eivät ole sallittuja!";
$l_weEditor["category"]["filename_empty"]       = "Tiedoston nimi ei voi olla tyhjä.";
$l_weEditor["category"]["name_komma"] = "Virheellinen nimi! Pilkku ei ole sallittu!";

$l_weEditor["text/webedition"]["response_save_ok"] = "webEdition -sivu '%s' on tallennettu!";
$l_weEditor["text/webedition"]["response_publish_ok"] = "webEdition -sivu '%s' on julkaistu!";
$l_weEditor["text/webedition"]["response_publish_notok"] = "Virhe julkaistaessa webEdition -sivua '%s'!";
$l_weEditor["text/webedition"]["response_unpublish_ok"] = "webEdition -sivu '%s' on poistettu julkaisusta!";
$l_weEditor["text/webedition"]["response_unpublish_notok"] = "Virhe poistettaessa julkaisusta webEdition -sivua '%s'!";
$l_weEditor["text/webedition"]["response_not_published"] = "webEdition -sivu '%s' ei ole julkaistu!";
$l_weEditor["text/webedition"]["response_save_notok"] = "Virhe tallennettaessa webEdition -sivua '%s'!";
$l_weEditor["text/webedition"]["response_path_exists"] = "webEdition sivua '%s' ei voitu tallentaa, koska toinen dokumentti tai hakemisto sijaisee samassa kohteessa!";
$l_weEditor["text/webedition"]["filename_empty"] = "Dokumentille ei ole annettu nimeä!";
$l_weEditor["text/webedition"]["we_filename_notValid"] = "Virheellinen tiedoston nimi!\\nSallitut merkit ovat alfa-numeerisia, isot ja pienet kirjaimet, alaviiva, tavuviiva ja piste (a-z, A-Z, 0-9, _, -, .)";
$l_weEditor["text/webedition"]["we_filename_notAllowed"] = "Annettu tiedoston nimi ei ole sallittu!";
$l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"] = "Dokumenttia ei voitu tallentaa koska sinulla ei ole riittäviä oikeuksia luoda kansioita (%s)!";
$l_weEditor["text/webedition"]["autoschedule"] = "webEdition -sivu julkaistaan automaattisesti %s.";

$l_weEditor["text/html"]["response_save_ok"] = "HTML -sivu '%s' on tallennettu!";
$l_weEditor["text/html"]["response_publish_ok"] = "HTML -sivu '%s' on julkaistu!";
$l_weEditor["text/html"]["response_publish_notok"] = "Virhe julkaistaessa HTML -sivua '%s'!";
$l_weEditor["text/html"]["response_unpublish_ok"] = "HTML -sivu '%s' on poistettu julkaisusta!";
$l_weEditor["text/html"]["response_unpublish_notok"] = "Virhe poistettaessa julkaisusta HTML -sivua '%s'!";
$l_weEditor["text/html"]["response_not_published"] = "HTML -sivua '%s' ei ole julkaistu!";
$l_weEditor["text/html"]["response_save_notok"] = "Virhe tallennettaessa HTML -sivua '%s'!";
$l_weEditor["text/html"]["response_path_exists"] = "HTML -sivua '%s' ei voitu tallentaa koska toinen dokumentti tai hakemisto sijaitsee samassa kohteessa!";
$l_weEditor["text/html"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/html"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/html"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/html"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["text/html"]["autoschedule"] = "The HTML page will be published automatically on %s.";

$l_weEditor["text/weTmpl"]["response_save_ok"] = "Sivupohja '%s' on tallennettu!";
$l_weEditor["text/weTmpl"]["response_publish_ok"] = "Sivupohja '%s' on julkaistu!";
$l_weEditor["text/weTmpl"]["response_unpublish_ok"] = "Sivupohja '%s' on poistettu julkaisusta!";
$l_weEditor["text/weTmpl"]["response_save_notok"] = "Virhe tallennettaessa sivupohjaa '%s'!";
$l_weEditor["text/weTmpl"]["response_path_exists"] = "Sivupohjaa '%s' ei voitu tallentaa koska toinen sivupohja tai hakemisto sijaitsee samassa kohteessa!";
$l_weEditor["text/weTmpl"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/weTmpl"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/weTmpl"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/weTmpl"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["text/weTmpl"]["no_template_save"] = "Templates " . "can " . "not " . "saved " . "in the " . "de" . "mo" . " of" . " webEdition.";

$l_weEditor["text/css"]["response_save_ok"] = "Tyylitiedosto '%s' on tallennettu!";
$l_weEditor["text/css"]["response_publish_ok"] = "Tyylitiedosto '%s' on julkaistu!";
$l_weEditor["text/css"]["response_unpublish_ok"] = "Tyylitiedosto '%s' on poistettu julkaisusta!";
$l_weEditor["text/css"]["response_save_notok"] = "Virhe tallennettaessa tyylitiedostoa '%s'!";
$l_weEditor["text/css"]["response_path_exists"] = "Tyylitiedostoa '%s' ei voitu tallentaa, koska samanniminen tiedosto tai hakemisto sijaitsee samassa kohteessa!";
$l_weEditor["text/css"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/css"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/css"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/css"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/js"]["response_save_ok"] = "JavaScript '%s' tallennettu!";
$l_weEditor["text/js"]["response_publish_ok"] = "JavaScript -tiedosto '%s' on julkaistu!";
$l_weEditor["text/js"]["response_unpublish_ok"] = "JavaScript -tiedosto '%s' on poistettu julkaisusta!";
$l_weEditor["text/js"]["response_save_notok"] = "Virhe tallennettaessa JavaScript -tiedostoa '%s'!";
$l_weEditor["text/js"]["response_path_exists"] = "JavaScript -tiedostoa '%s' ei voitu tallenntaa, koska samanniminen tiedosto tai hakemisto sijaitsee kohteessa!";
$l_weEditor["text/js"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/js"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/js"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/js"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/plain"]["response_save_ok"] = "Tekstitiedosto '%s' tallennettu!";
$l_weEditor["text/plain"]["response_publish_ok"] = "Tekstitiedosto '%s' on julkaistu!";
$l_weEditor["text/plain"]["response_unpublish_ok"] = "Tekstitiedosto '%s' on poistettu julkaisusta!";
$l_weEditor["text/plain"]["response_save_notok"] = "Virhe tallennettaessa tekstitiedostoa '%s'!";
$l_weEditor["text/plain"]["response_path_exists"] = "Tekstitiedostoa '%s' ei voitu tallentaa, koska samanniminen tiedosto tai hakemisto sijaitsee kohteessa!";
$l_weEditor["text/plain"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/plain"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/plain"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/plain"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/htaccess"]["response_save_ok"] = "The file '%s' has been successfully saved!"; //TRANSLATE
$l_weEditor["text/htaccess"]["response_publish_ok"] = "The file '%s' has been successfully published!"; //TRANSLATE
$l_weEditor["text/htaccess"]["response_unpublish_ok"] = "The file '%s' has been successfully unpublished!"; //TRANSLATE
$l_weEditor["text/htaccess"]["response_save_notok"] = "Error while saving the file '%s'!"; //TRANSLATE
$l_weEditor["text/htaccess"]["response_path_exists"] = "The file '%s' could not be saved because another document or directory is positioned at the same location!"; //TRANSLATE
$l_weEditor["text/htaccess"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/htaccess"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/htaccess"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/htaccess"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/xml"]["response_save_ok"] = "XML -tiedosto '%s' onnistuneesti tallennettu!";
$l_weEditor["text/xml"]["response_publish_ok"] = "XML -tiedosto '%s' onnistuneesti julkaistu!";
$l_weEditor["text/xml"]["response_unpublish_ok"] = "XML -tiedosto '%s' onnistuneesti poistettu julkaisusta!";
$l_weEditor["text/xml"]["response_save_notok"] = "Virhe tallentaessa XML -tiedostoa '%s'!";
$l_weEditor["text/xml"]["response_path_exists"] = "XML -tiedostoa '%s' ei voitu tallentaa koska toinen dokumentti tai hakemisto sijaitsee samassa paikassa!";
$l_weEditor["text/xml"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/xml"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/xml"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/xml"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["folder"]["response_save_ok"] = "Hakemisto '%s' on tallennettu!";
$l_weEditor["folder"]["response_publish_ok"] = "Hakemisto '%s' on julkaistu!";
$l_weEditor["folder"]["response_unpublish_ok"] = "Hakemisto '%s' on poistettu julkaisusta!";
$l_weEditor["folder"]["response_save_notok"] = "Virhe hakemiston '%s' tallennuksessa!";
$l_weEditor["folder"]["response_path_exists"] = "Hakemistoa '%s' ei voitu tallentaa koska samanniminen tiedosto tai hakemisto sijaitsee kohteessa!";
$l_weEditor["folder"]["filename_empty"] = "Hakemistolle ei ole annettu nimeä!";
$l_weEditor["folder"]["we_filename_notValid"] = "Virheellinen hakemiston nimi\\nSallitut merkit ovat alfa-numeerisia, isot ja pienet kirjaimet, alaviiva, tavuviiva ja piste (a-z, A-Z, 0-9, _, -, .)";
$l_weEditor["folder"]["we_filename_notAllowed"] = "Hakemiston nimi ei ole sallittu!";
$l_weEditor["folder"]["response_save_noperms_to_create_folders"] = "Hakemistoa ei voitu tallentaa, koska sinulla ei ole tarvittavia oikeuksia luoda kasioita (%s)!";

$l_weEditor["image/*"]["response_save_ok"] = "Kuva '%s' on tallennettu!";
$l_weEditor["image/*"]["response_publish_ok"] = "Kuva '%s' on julkaistu!";
$l_weEditor["image/*"]["response_unpublish_ok"] = "Kuva '%s' on poistettu julkaisusta!";
$l_weEditor["image/*"]["response_save_notok"] = "Virhe tallennettaessa kuvaa '%s'!";
$l_weEditor["image/*"]["response_path_exists"] = "Kuvaa '%s' ei voitu tallentaa, koska samanniminen tiedosto tai hakemisto sijaitsee kohteessa!";
$l_weEditor["image/*"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["image/*"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["image/*"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["image/*"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["application/*"]["response_save_ok"] = "Dokumentti '%s' tallennettu!";
$l_weEditor["application/*"]["response_publish_ok"] = "Dokumentti '%s' on julkaistu!";
$l_weEditor["application/*"]["response_unpublish_ok"] = "Dokumentti '%s' on poistettu julkaisusta!";
$l_weEditor["application/*"]["response_save_notok"] = "Virhe tallennettaessa dokumenttia '%s'!";
$l_weEditor["application/*"]["response_path_exists"] = "Dokumenttia '%s' ei voitu tallentaa, koska samanniminen tiedosto tai hakemisto sijaitsee kohteessa!";
$l_weEditor["application/*"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["application/*"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["application/*"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["application/*"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["application/*"]["we_description_missing"] = "Please enter a desription in the 'Desription' field!";
$l_weEditor["application/*"]["response_save_wrongExtension"] =  "Tallennettaessa tiedostoa '%s' tapahtui virhe! \\nMuut tiedostot -tyyppisen tiedoston pääte ei voi olla '%s'!\\nOle hyvä ja luo HTML-sivu tähän tarkoitukseen!";

$l_weEditor["application/x-shockwave-flash"]["response_save_ok"] = "Flash -tiedosto '%s' on tallennettu!";
$l_weEditor["application/x-shockwave-flash"]["response_publish_ok"] = "Flash -tiedosto '%s' on julkaistu!";
$l_weEditor["application/x-shockwave-flash"]["response_unpublish_ok"] = "Flash -tiedosto '%s' on poistettu julkaisusta!";
$l_weEditor["application/x-shockwave-flash"]["response_save_notok"] = "Virhe tallennettaessa Flash -tiedostoa '%s'!";
$l_weEditor["application/x-shockwave-flash"]["response_path_exists"] = "Flash -tiedostoa '%s' ei voitu tallentaa, koska samanniminen tiedosto tai hakemisto sijaitsee kohteessa!";
$l_weEditor["application/x-shockwave-flash"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["application/x-shockwave-flash"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["application/x-shockwave-flash"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["application/x-shockwave-flash"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["video/quicktime"]["response_save_ok"] = "Quicktime -tiedosto '%s' on tallennettu!";
$l_weEditor["video/quicktime"]["response_publish_ok"] = "Quicktime -tiedosto '%s' on julkaistu!";
$l_weEditor["video/quicktime"]["response_unpublish_ok"] = "Quicktime -tiedosto '%s' on poistettu julkaisusta!";
$l_weEditor["video/quicktime"]["response_save_notok"] = "Virhe tallennettaessa Quicktime -tiedostoa '%s'!";
$l_weEditor["video/quicktime"]["response_path_exists"] = "Quicktime -tiedostoa '%s' ei voitu tallentaa koska samanniminen tiedosto tai hakemisto sijaitsee kohteessa!";
$l_weEditor["video/quicktime"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["video/quicktime"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["video/quicktime"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["video/quicktime"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

/*****************************************************************************
 * PLEASE DON'T TOUCH THE NEXT LINES
 * UNLESS YOU KNOW EXACTLY WHAT YOU ARE DOING!
 *****************************************************************************/

$_language_directory = dirname(__FILE__)."/modules";
$_directory = dir($_language_directory);

while (false !== ($entry = $_directory->read())) {
	if (strstr($entry, '_we_editor')) {
		include($_language_directory."/".$entry);
	}
}
