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
		'filename_empty' => "Er is geen naam ingevoerd voor dit document!",
		'we_filename_notValid' => "Ongeldige bestandsnaam\\nGeldige karakters zijn alfa-numeriek, boven- en onderkast, eveneens als de underscore, koppelteken en punt (a-z, A-Z, 0-9, _, -, .)",
		'we_filename_notAllowed' => "De bestandsnaam die u heeft ingevoerd is niet toegestaan!",
		'response_save_noperms_to_create_folders' => "Het document kon niet bewaard worden omdat u niet de juiste rechten heeft om mappen aan te maken (%s)!",
);
$l_weEditor = array(
		'doubble_field_alert' => "Het veld '%s' bestaat al! Een veldnaam moet uniek zijn!",
		'variantNameInvalid' => "De naam van een artikel variant mag niet leeg zijn!",
		'folder_save_nok_parent_same' => "De gekozen hoofd directory bevind zich in de eigenlijke directory! Kies a.u.b. een andere directory en probeer het opnieuw!",
		'pfolder_notsave' => "De directory kan niet bewaard worden in de gekozen directory!",
		'required_field_alert' => "Het veld '%s' is vereist en moet ingevuld worden!",
		'category' => array(
				'response_save_ok' => "De categorie '%s' is succesvol bewaard!",
				'response_save_notok' => "Fout tijdens het bewaren van categorie '%s'!",
				'response_path_exists' => "De categorie '%s' kon niet bewaard worden omdat een andere categorie zich op dezelfde plek bevind!",
				'we_filename_notValid' => "Ongeldige naam!\\n\", \\' / < > en \\\\ zijn niet toegestaan!",
				'filename_empty' => "De bestandsnaam mag niet leeg zijn.",
				'name_komma' => "Ongeldige naam! Een komma is niet toegestaan!",
		),
		'text/webedition' => array_merge($l__tmp, array(
				'response_save_ok' => "De webEdition pagina '%s' is succesvol bewaard!",
				'response_publish_ok' => "De webEdition pagina '%s' is succesvol gepubliceerd!",
				'response_publish_notok' => "Fout tijdens het publiceren van webEdition pagina '%s'!",
				'response_unpublish_ok' => "De webEdition pagina '%s' is succesvol gedepubliceerd!",
				'response_unpublish_notok' => "Fout tijdens het depubliceren van webEdition pagina '%s'!",
				'response_not_published' => "De webEdition pagina '%s' is niet gepubliceerd!",
				'response_save_notok' => "Fout tijdens het bewaren van webEdition pagina '%s'!",
				'response_path_exists' => "De webEdition pagina '%s' kon niet bewaard worden omdat een ander document of directory zich op dezelfde plek bevind!",
				'autoschedule' => "De webEdition pagina zal automatisch gepubliceerd worden op %s.",
		)),
		'text/html' => array_merge($l__tmp, array(
				'response_save_ok' => "De HTML pagina '%s' is succesvol bewaard!",
				'response_publish_ok' => "De HTML pagina '%s' is succesvol gepubliceerd!",
				'response_publish_notok' => "Fout tijdens het publiceren van HTML pagina '%s'!",
				'response_unpublish_ok' => "De HTML pagina '%s' is succesvol gedepubliceerd!",
				'response_unpublish_notok' => "Fout tijdens het depubliceren van HTML pagina '%s'!",
				'response_not_published' => "De HTML pagina '%s' is niet gepubliceerd!",
				'response_save_notok' => "Fout tijdens het bewaren van HTML pagina '%s'!",
				'response_path_exists' => "De HTML pagina '%s' kon niet bewaard worden omdat een ander document of directory zich op dezelfde plek bevind!",
				'autoschedule' => "The HTML page will be published automatically on %s.",
		)),
		'text/weTmpl' => array_merge($l__tmp, array(
				'response_save_ok' => "Het sjabloon '%s' is succesvol bewaard!",
				'response_publish_ok' => "Het sjabloon'%s' is succesvol gepubliceerd!",
				'response_unpublish_ok' => "Het sjabloon '%s' is succesvol gedepubliceerd!",
				'response_save_notok' => "Fout tijdens het bewaren van sjabloon '%s'!",
				'response_path_exists' => "Het sjabloon '%s' kon niet bewaard worden omdat een ander document of directory zich op dezelfde plek bevind!",
				'no_template_save' => "Templates " . "can " . "not " . "saved " . "in the " . "de" . "mo" . " of" . " webEdition.",
		)),
		'text/css' => array_merge($l__tmp, array(
				'response_save_ok' => "De stylesheet '%s' is succesvol bewaard!",
				'response_publish_ok' => "De stylesheet '%s' is succesvol gepubliceerd!",
				'response_unpublish_ok' => "De stylesheet '%s' is succesvol gedepubliceerd!",
				'response_save_notok' => "Fout tijdens het bewaren van stylesheet '%s'!",
				'response_path_exists' => "De stylesheet '%s' kon niet bewaard worden omdat een ander document of directory zich op dezelfde plek bevind!",
		)),
		'text/js' => array_merge($l__tmp, array(
				'response_save_ok' => "The JavaScript '%s' has been successfully saved!",
				'response_publish_ok' => "Het JavaScript'%s' is succesvol gepubliceerd!",
				'response_unpublish_ok' => "Het JavaScript '%s' is succesvol gedepubliceerd!",
				'response_save_notok' => "Fout tijdens het bewaren van JavaScript '%s'!",
				'response_path_exists' => "Het JavaScript '%s' kon niet bewaard worden omdat een ander document of directory zich op dezelfde plek bevind!",
		)),
		'text/plain' => array_merge($l__tmp, array(
				'response_save_ok' => "The text file '%s' has been successfully saved!",
				'response_publish_ok' => "Het tekst bestand '%s' is succesvol gepubliceerd!",
				'response_unpublish_ok' => "Het tekst bestand '%s' is succesvol gedepubliceerd!",
				'response_save_notok' => "Fout tijdens het bewaren van tekst bestand '%s'!",
				'response_path_exists' => "Het tekst bestand '%s' kon niet bewaard worden omdat een ander document of directory zich op dezelfde plek bevind!",
		)),
		'text/htaccess' => array_merge($l__tmp, array(
				'response_save_ok' => "The file '%s' has been successfully saved!", //TRANSLATE
				'response_publish_ok' => "The file '%s' has been successfully published!", //TRANSLATE
				'response_unpublish_ok' => "The file '%s' has been successfully unpublished!", //TRANSLATE
				'response_save_notok' => "Error while saving the file '%s'!", //TRANSLATE
				'response_path_exists' => "The file '%s' could not be saved because another document or directory is positioned at the same location!", //TRANSLATE
		)),
		'text/xml' => array_merge($l__tmp, array(
				'response_save_ok' => "The XML file '%s' has been successfully saved!",
				'response_publish_ok' => "Het XML bestand '%s' is succesvol gepubliceerd!",
				'response_unpublish_ok' => "Het XML bestand '%s' is succesvol gedepubliceerd!",
				'response_save_notok' => "Fout tijdens opslaan van XML bestand '%s'!",
				'response_path_exists' => "Het XML bestand '%s' kon niet bewaard worden omdat een ander document of directory zich op dezelfde locatie bevind!",
		)),
		'folder' => array(
				'response_save_ok' => "The directory '%s' has been successfully saved!",
				'response_publish_ok' => "De directory '%s' is succesvol gepubliceerd!",
				'response_unpublish_ok' => "De directory '%s' is succesvol gedepubliceerd!",
				'response_save_notok' => "Fout tijdens het bewaren van directory '%s'!",
				'response_path_exists' => "De directory '%s' kon niet bewaard worden omdat een ander document of directory zich op dezelfde plek bevind!",
				'filename_empty' => "Er is geen naam ingevoerd voor deze directory!",
				'we_filename_notValid' => "Ongeldige map naam\\nGeldige karakters zijn alfa-numeriek, boven- en onderkast, eveneens als de underscore, koppelteken en punt (a-z, A-Z, 0-9, _, -, .)",
				'we_filename_notAllowed' => "De ingevoerde directory naam is niet toegestaan!",
				'response_save_noperms_to_create_folders' => "De directory kon niet bewaard worden omdat u niet de juisten rechten heeft om mappen aan te maken (%s)!",
		),
		'image/*' => array_merge($l__tmp, array(
				'response_save_ok' => "De afbeelding '%s' is succesvol bewaard!",
				'response_publish_ok' => "De afbeelding '%s' is succesvol gepubliceerd!",
				'response_unpublish_ok' => "De afbeelding '%s' is succesvol gedepubliceerd!",
				'response_save_notok' => "Fout tijdens het bewaren van afbeelding '%s'!",
				'response_path_exists' => "De afbeelding '%s' kon niet bewaard worden omdat een ander document of directory zich op dezelfde plek bevind!",
		)),
		'application/*' => array_merge($l__tmp, array(
				'response_save_ok' => "The document '%s' has been successfully saved!",
				'response_publish_ok' => "Het document '%s' is succesvol gepubliceerd!",
				'response_unpublish_ok' => "Het document '%s' is succesvol gedepubliceerd!",
				'response_save_notok' => "Fout tijdens het bewaren van document '%s'!",
				'response_path_exists' => "Het document '%s' kon niet bewaard worden omdat een ander document of directory zich op dezelfde plek bevind!",
				'we_description_missing' => "Please enter a desription in the 'Desription' field!",
				'response_save_wrongExtension' => "Fout tijdens het bewaren van '%s' \\nDe bestands extensie '%s' is niet geldig voor andere bestanden!\\nMaak hier a.u.b. een HTML pagina voor aan!",
		)),
		'application/x-shockwave-flash' => array_merge($l__tmp, array(
				'response_save_ok' => "De Flash film '%s' is succesvol bewaard!",
				'response_publish_ok' => "De Flash film '%s' is succesvol gepubliceerd!",
				'response_unpublish_ok' => "De Flash film '%s' is succesvol gedepubliceerd!",
				'response_save_notok' => "Fout tijdens het bewaren van Flash film '%s'!",
				'response_path_exists' => "De Flash film '%s' kon niet bewaard worden omdat een ander document of directory zich op dezelfde plek bevind!",
		)),
		'video/quicktime' => array_merge($l__tmp, array(
				'response_save_ok' => "The Quicktime movie '%s' has been successfully saved!",
				'response_publish_ok' => "De Quicktime film '%s' is succesvol gepubliceerd!",
				'response_unpublish_ok' => "De Quicktime film '%s' is succesvol gedepubliceerd!",
				'response_save_notok' => "Fout tijdens het bewaren van Quicktime film '%s'!",
				'response_path_exists' => "De Quicktime film '%s' kon niet bewaard worden omdat een ander document of directory zich op dezelfde plek bevind!",
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
