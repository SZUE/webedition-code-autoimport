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
		'filesize' => "File size", // TRANSLATE
		'supported_types' => "Meta data formats", // TRANSLATE
		'none' => "none", // TRANSLATE
		'filetype' => "File type", // TRANSLATE

		/*		 * ***************************************************************************
		 * METADATA FIELD MAPPING
		 * *************************************************************************** */

		'headline' => "Meta data fields", // TRANSLATE
		'tagname' => "Field name", // TRANSLATE
		'type' => "Type", // TRANSLATE
		'dummy' => "dummy", // TRANSLATE

		'save' => "Saving meta data fields, one moment ...", // TRANSLATE
		'save_wait' => "Saving settings", // TRANSLATE

		'saved' => "Meta data fields have been saved successfully.", // TRANSLATE
		'saved_successfully' => "Meta data fields saved", // TRANSLATE

		'properties' => "Properties", // TRANSLATE

		'fields_hint' => "Define additional fields for meta data. Attached data (Exit, IPTC) to the original file, may be migrated automatically during the import. Add one or more fields that are to be imported in the entry field &quot;import from&quot; in the format &quot;[type]/[fieldname]&quot;. Example: &quot;exif/copyright,iptc/copyright&quot;. Multiple fields may be entered separated by comma. The import will search all specified fields up to the first field filled with data.", // TRANSLATE
		'import_from' => "Import from", // TRANSLATE
		'fields' => "Fields", // TRANSLATE
		'add' => "add", // TRANSLATE

		/*		 * ***************************************************************************
		 * UPLOAD
		 * *************************************************************************** */

		'import_metadata_at_upload' => "Import meta data from file", // TRANSLATE

		/*		 * ***************************************************************************
		 * ERROR MESSAGES
		 * *************************************************************************** */

		'error_meta_field_empty_msg' => "The fieldname at line %s1 can not be empty!", // TRANSLATE
		'meta_field_wrong_chars_messsage' => "The fieldname '%s1' is not valid! Valid characters are alpha-numeric, capital and small (a-z, A-Z, 0-9) and underscore.", // TRANSLATE
		'meta_field_wrong_name_messsage' => "The fieldname '%s1' is not valid! It is used internaly from webEdition! Following names are invalid and can not be used: %s2", // TRANSLATE
		'file_size_0' => 'The file size is 0 byte, please upload a document to the server before saving', // TRANSLATE

		/*		 * ***************************************************************************
		 * INFO TAB
		 * *************************************************************************** */

		'info_exif_data' => "Exif data", // TRANSLATE
		'info_iptc_data' => "IPTC data", // TRANSLATE
		'no_exif_data' => "No Exif data available", // TRANSLATE
		'no_iptc_data' => "No IPTC data available", // TRANSLATE
		'no_exif_installed' => "The PHP Exif extension is not installed!", // TRANSLATE
		'no_metadata_supported' => "webEdition does not support metadata formats for this kind of document.", // TRANSLATE
);