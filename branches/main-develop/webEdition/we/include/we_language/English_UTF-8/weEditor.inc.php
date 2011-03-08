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
		'filename_empty' => "No name has been entered for this document!",
		'we_filename_notValid' => "Invalid file name\\nValid characters are alpha-numeric, upper and lower case, as well as underscore, hyphen and dot (a-z, A-Z, 0-9, _, -, .)",
		'we_filename_notAllowed' => "The file name you have entered is not allowed!",
		'response_save_noperms_to_create_folders' => "The document could not be saved because you do not have the neccessary rights to create folders (%s)!",
);
$l_weEditor = array(
		'doubble_field_alert' => "The field '%s' already exists! A field name must be unique!",
		'variantNameInvalid' => "The name of an article variant can not be empty!",
		'folder_save_nok_parent_same' => "The chosen parent directory is within the actual directory! Please choose another directory and try again!",
		'pfolder_notsave' => "The directory cannot be saved in the chosen directory!",
		'required_field_alert' => "The field '%s' is required and must be filled!",
		'category' => array(
				'response_save_ok' => "The category '%s' has been successfully saved!",
				'response_save_notok' => "Error while saving category '%s'!",
				'response_path_exists' => "The category '%s' could not be saved because another category is positioned at the same location!",
				'we_filename_notValid' => 'Invalid name!\n", \' / < > and \\\\ are not allowed!',
				'filename_empty' => "The file name cannot be empty.",
				'name_komma' => "Invalid name! A comma is not allowed!",
		),
		'text/webedition' => array_merge($l__tmp, array(
				'response_save_ok' => "The webEdition page '%s' has been successfully saved!",
				'response_publish_ok' => "The webEdition page '%s' has been successfully published!",
				'response_publish_notok' => "Error while publishing webEdition page '%s'!",
				'response_unpublish_ok' => "The webEdition page '%s' has been successfully unpublished!",
				'response_unpublish_notok' => "Error while unpublishing webEdition page '%s'!",
				'response_not_published' => "The webEdition page '%s' is not published!",
				'response_save_notok' => "Error while saving webEdition page '%s'!",
				'response_path_exists' => "The webEdition page '%s' could not be saved because another document or directory is positioned at the same location!",
				'autoschedule' => "The webEdition page will be published automatically on %s.",
		)),
		'text/html' => array_merge($l__tmp, array(
				'response_save_ok' => "The HTML page '%s' has been successfully saved!",
				'response_publish_ok' => "The HTML page '%s' has been successfully published!",
				'response_publish_notok' => "Error while publishing HTML page '%s'!",
				'response_unpublish_ok' => "The HTML page '%s' has been successfully unpublished!",
				'response_unpublish_notok' => "Error while unpublishing HTML page '%s'!",
				'response_not_published' => "The HTML page '%s' is not published!",
				'response_save_notok' => "Error while saving HTML page '%s'!",
				'response_path_exists' => "The HTML page '%s' could not be saved because another document or directory is positioned at the same location!",
				'autoschedule' => "The HTML page will be published automatically on %s.",
		)),
		'text/weTmpl' => array_merge($l__tmp, array(
				'response_save_ok' => "The template '%s' has been successfully saved!",
				'response_publish_ok' => "The template'%s' has been successfully published!",
				'response_unpublish_ok' => "The template '%s' has been successfully unpublished!",
				'response_save_notok' => "Error while saving template '%s'!",
				'response_path_exists' => "The template '%s' could not be saved because another document or directory is positioned at the same location!",
				'no_template_save' => "Templates " . "can " . "not " . "saved " . "in the " . "de" . "mo" . " of" . " webEdition.",
		)),
		'text/css' => array_merge($l__tmp, array(
				'response_save_ok' => "The style sheet '%s' has been successfully saved!",
				'response_publish_ok' => "The style sheet '%s' has been successfully published!",
				'response_unpublish_ok' => "The style sheet '%s' has been successfully unpublished!",
				'response_save_notok' => "Error while saving style sheet '%s'!",
				'response_path_exists' => "The style sheet '%s' could not be saved because another document or directory is positioned at the same location!",
		)),
		'text/js' => array_merge($l__tmp, array(
				'response_save_ok' => "The JavaScript '%s' has been successfully saved!",
				'response_publish_ok' => "The JavaScript'%s' has been successfully published!",
				'response_unpublish_ok' => "The JavaScript '%s' has been successfully unpublished!",
				'response_save_notok' => "Error while saving JavaScript '%s'!",
				'response_path_exists' => "The JavaScript '%s' could not be saved because another document or directory is positioned at the same location!",
		)),
		'text/plain' => array_merge($l__tmp, array(
				'response_save_ok' => "The text file '%s' has been successfully saved!",
				'response_publish_ok' => "The text file '%s' has been successfully published!",
				'response_unpublish_ok' => "The text file '%s' has been successfully unpublished!",
				'response_save_notok' => "Error while saving text file '%s'!",
				'response_path_exists' => "The text file '%s' could not be saved because another document or directory is positioned at the same location!",
		)),
		'text/htaccess' => array_merge($l__tmp, array(
				'response_save_ok' => "The file '%s' has been successfully saved!",
				'response_publish_ok' => "The file '%s' has been successfully published!",
				'response_unpublish_ok' => "The file '%s' has been successfully unpublished!",
				'response_save_notok' => "Error while saving the file '%s'!",
				'response_path_exists' => "The file '%s' could not be saved because another document or directory is positioned at the same location!",
		)),
		'text/xml' => array_merge($l__tmp, array(
				'response_save_ok' => "The XML file '%s' has been successfully saved!",
				'response_publish_ok' => "The XML file '%s' has been successfully published!",
				'response_unpublish_ok' => "The XML file '%s' has been successfully unpublished!",
				'response_save_notok' => "Error while saving XML file '%s'!",
				'response_path_exists' => "The XML file '%s' could not be saved because another document or directory is positioned at the same location!",
		)),
		'folder' => array(
				'response_save_ok' => "The directory '%s' has been successfully saved!",
				'response_publish_ok' => "The directory '%s' has been successfully published!",
				'response_unpublish_ok' => "The directory '%s' has been successfully unpublished!",
				'response_save_notok' => "Error while saving directory '%s'!",
				'response_path_exists' => "The directory '%s' could not be saved because another document or directory is positioned at the same location!",
				'filename_empty' => "No name entered for this directory!",
				'we_filename_notValid' => "Invalid folder name\\nValid characters are alpha-numeric, upper and lower case, as well as underscore, hyphen and dot (a-z, A-Z, 0-9, _, -, .)",
				'we_filename_notAllowed' => "The name entered for the directory is not allowed!",
				'response_save_noperms_to_create_folders' => "The directory could not be saved because you do not have the neccessary rights to create folders (%s)!",
		),
		'image/*' => array_merge($l__tmp, array(
				'response_save_ok' => "The image '%s' has been successfully saved!",
				'response_publish_ok' => "The image '%s' has been successfully published!",
				'response_unpublish_ok' => "The image '%s' has been successfully unpublished!",
				'response_save_notok' => "Error while saving image '%s'!",
				'response_path_exists' => "The image '%s' could not be saved because another document or directory is positioned at the same location!",
		)),
		'application/*' => array_merge($l__tmp, array(
				'we_description_missing' => "Please enter a desription in the 'Desription' field!",
				'response_save_ok' => "The document '%s' has been successfully saved!",
				'response_publish_ok' => "The document '%s' has been successfully published!",
				'response_unpublish_ok' => "The document '%s' has been successfully unpublished!",
				'response_save_notok' => "Error while saving document '%s'!",
				'response_path_exists' => "The document '%s' could not be saved because another document or directory is positioned at the same location!",
				'response_save_wrongExtension' => "Error while saving '%s' \\nThe file extension '%s' is not valid for other files!\\nPlease create an HTML page for that purpose!",
		)),
		'application/x-shockwave-flash' => array_merge($l__tmp, array(
				'response_save_ok' => "The Flash movie '%s' has been successfully saved!",
				'response_publish_ok' => "The Flash movie '%s' has been successfully published!",
				'response_unpublish_ok' => "The Flash movie '%s' has been successfully unpublished!",
				'response_save_notok' => "Error while saving Flash movie '%s'!",
				'response_path_exists' => "The Flash movie '%s' could not be saved because another document or directory is positioned at the same location!",
		)),
		'video/quicktime' => array_merge($l__tmp, array(
				'response_save_ok' => "The Quicktime movie '%s' has been successfully saved!",
				'response_publish_ok' => "The Quicktime movie '%s' has been successfully published!",
				'response_unpublish_ok' => "The Quicktime movie '%s' has been successfully unpublished!",
				'response_save_notok' => "Error while saving Quicktime movie '%s'!",
				'response_path_exists' => "The Quicktime movie '%s' could not be saved because another document or directory is positioned at the same location!",
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
