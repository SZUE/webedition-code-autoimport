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
 * Language file: metadata.inc.php
 * Provides language strings.
 * Language: English
 */
/* * ***************************************************************************
 * DOCUMENT TAB
 * *************************************************************************** */
$l_metadata = array(
		'filesize' => "Bestandsgrootte",
		'supported_types' => "Meta gegevens formaten",
		'none' => "geen",
		'filetype' => "Bestandstype",
		/*		 * ***************************************************************************
		 * METADATA FIELD MAPPING
		 * *************************************************************************** */

		'headline' => "Meta gegevens velden",
		'tagname' => "Veld naam",
		'type' => "Type", // TRANSLATE
		'dummy' => "dummy", // TRANSLATE

		'save' => "Bezig met bewaren van meta gegevens velden, een ogenblik geduld ...",
		'save_wait' => "Instellingen bewaren",
		'saved' => "De meta gegevens velden zijn succesvol bewaard.",
		'saved_successfully' => "Meta gegevens velden bewaard",
		'properties' => "Eigenschappen",
		'fields_hint' => "Defineer extra velden voor meta gegevens. Toegevoegde gegevens(Exit, IPTC) aan het originele bestand, kunnen automatisch inbegrepen worden tijdens het importeren. Voeg één of meerdere velden toe die geïmporteerd moeten worden in het invoer veld &quot;importeer vanuit&quot; in het formaat &quot;[type]/[fieldname]&quot;. Bijvoorbeeld: &quot;exif/copyright,iptc/copyright&quot;. Er kunnen meerdere ingevoerd worden, gescheiden door een komma. Tijdens het importeren worden alle gespecificeerde velden doorzocht.",
		'import_from' => "Importeer uit",
		'fields' => "Velden",
		'add' => "voeg toe",
		/*		 * ***************************************************************************
		 * UPLOAD
		 * *************************************************************************** */

		'import_metadata_at_upload' => "Importeer metagegevens uit bestand",
		/*		 * ***************************************************************************
		 * ERROR MESSAGES
		 * *************************************************************************** */

		'error_meta_field_empty_msg' => "De veldnaam op regel %s1 mag niet leeg zijn!",
		'meta_field_wrong_chars_messsage' => "De veldnaam '%s1' is niet geldig! Geldige karakters zijn alfa-numeriek, hoofd- en kleine letters (a-z, A-Z, 0-9) en underscore.",
		'meta_field_wrong_name_messsage' => "De veldnaam '%s1' is niet geldig! Deze naam wordt intern gebruikt in webEdition! De volgende namen zijn niet geldig en kunnen niet gebruikt worden: %s2",
		'file_size_0' => 'The file size is 0 byte, please upload a document to the server before saving', // TRANSLATE

		/*		 * ***************************************************************************
		 * INFO TAB
		 * *************************************************************************** */

		'info_exif_data' => "Exif gegevens",
		'info_iptc_data' => "IPTC gegevens",
		'no_exif_data' => "Geen Exif gegevens beschikbaar",
		'no_iptc_data' => "Geen IPTC gegevens available",
		'no_exif_installed' => "De PHP Exif extensie is niet geïnstalleerd!",
		'no_metadata_supported' => "webEdition ondersteunt geen metagegevens formaten voor dit type document.",
);