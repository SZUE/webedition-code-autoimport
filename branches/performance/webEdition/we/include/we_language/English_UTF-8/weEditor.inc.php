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
$l_weEditor["doubble_field_alert"] = "The field '%s' already exists! A field name must be unique!";
$l_weEditor["variantNameInvalid"] = "The name of an article variant can not be empty!";

$l_weEditor["folder_save_nok_parent_same"] = "The chosen parent directory is within the actual directory! Please choose another directory and try again!";
$l_weEditor["pfolder_notsave"] = "The directory cannot be saved in the chosen directory!";
$l_weEditor["required_field_alert"] = "The field '%s' is required and must be filled!";

$l_weEditor["category"]["response_save_ok"] = "The category '%s' has been successfully saved!";
$l_weEditor["category"]["response_save_notok"] = "Error while saving category '%s'!";
$l_weEditor["category"]["response_path_exists"] = "The category '%s' could not be saved because another category is positioned at the same location!";
$l_weEditor["category"]["we_filename_notValid"] = 'Invalid name!\n", \' / < > and \\\\ are not allowed!';
$l_weEditor["category"]["filename_empty"]       = "The file name cannot be empty.";
$l_weEditor["category"]["name_komma"] = "Invalid name! A comma is not allowed!";

$l_weEditor["text/webedition"]["response_save_ok"] = "The webEdition page '%s' has been successfully saved!";
$l_weEditor["text/webedition"]["response_publish_ok"] = "The webEdition page '%s' has been successfully published!";
$l_weEditor["text/webedition"]["response_publish_notok"] = "Error while publishing webEdition page '%s'!";
$l_weEditor["text/webedition"]["response_unpublish_ok"] = "The webEdition page '%s' has been successfully unpublished!";
$l_weEditor["text/webedition"]["response_unpublish_notok"] = "Error while unpublishing webEdition page '%s'!";
$l_weEditor["text/webedition"]["response_not_published"] = "The webEdition page '%s' is not published!";
$l_weEditor["text/webedition"]["response_save_notok"] = "Error while saving webEdition page '%s'!";
$l_weEditor["text/webedition"]["response_path_exists"] = "The webEdition page '%s' could not be saved because another document or directory is positioned at the same location!";
$l_weEditor["text/webedition"]["filename_empty"] = "No name has been entered for this document!";
$l_weEditor["text/webedition"]["we_filename_notValid"] = "Invalid file name\\nValid characters are alpha-numeric, upper and lower case, as well as underscore, hyphen and dot (a-z, A-Z, 0-9, _, -, .)";
$l_weEditor["text/webedition"]["we_filename_notAllowed"] = "The file name you have entered is not allowed!";
$l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"] = "The document could not be saved because you do not have the neccessary rights to create folders (%s)!";
$l_weEditor["text/webedition"]["autoschedule"] = "The webEdition page will be published automatically on %s.";

$l_weEditor["text/html"]["response_save_ok"] = "The HTML page '%s' has been successfully saved!";
$l_weEditor["text/html"]["response_publish_ok"] = "The HTML page '%s' has been successfully published!";
$l_weEditor["text/html"]["response_publish_notok"] = "Error while publishing HTML page '%s'!";
$l_weEditor["text/html"]["response_unpublish_ok"] = "The HTML page '%s' has been successfully unpublished!";
$l_weEditor["text/html"]["response_unpublish_notok"] = "Error while unpublishing HTML page '%s'!";
$l_weEditor["text/html"]["response_not_published"] = "The HTML page '%s' is not published!";
$l_weEditor["text/html"]["response_save_notok"] = "Error while saving HTML page '%s'!";
$l_weEditor["text/html"]["response_path_exists"] = "The HTML page '%s' could not be saved because another document or directory is positioned at the same location!";
$l_weEditor["text/html"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/html"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/html"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/html"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["text/html"]["autoschedule"] = "The HTML page will be published automatically on %s.";

$l_weEditor["text/weTmpl"]["response_save_ok"] = "The template '%s' has been successfully saved!";
$l_weEditor["text/weTmpl"]["response_publish_ok"] = "The template'%s' has been successfully published!";
$l_weEditor["text/weTmpl"]["response_unpublish_ok"] = "The template '%s' has been successfully unpublished!";
$l_weEditor["text/weTmpl"]["response_save_notok"] = "Error while saving template '%s'!";
$l_weEditor["text/weTmpl"]["response_path_exists"] = "The template '%s' could not be saved because another document or directory is positioned at the same location!";
$l_weEditor["text/weTmpl"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/weTmpl"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/weTmpl"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/weTmpl"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["text/weTmpl"]["no_template_save"] = "Templates " . "can " . "not " . "saved " . "in the " . "de" . "mo" . " of" . " webEdition.";

$l_weEditor["text/css"]["response_save_ok"] = "The style sheet '%s' has been successfully saved!";
$l_weEditor["text/css"]["response_publish_ok"] = "The style sheet '%s' has been successfully published!";
$l_weEditor["text/css"]["response_unpublish_ok"] = "The style sheet '%s' has been successfully unpublished!";
$l_weEditor["text/css"]["response_save_notok"] = "Error while saving style sheet '%s'!";
$l_weEditor["text/css"]["response_path_exists"] = "The style sheet '%s' could not be saved because another document or directory is positioned at the same location!";
$l_weEditor["text/css"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/css"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/css"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/css"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/js"]["response_save_ok"] = "The JavaScript '%s' has been successfully saved!";
$l_weEditor["text/js"]["response_publish_ok"] = "The JavaScript'%s' has been successfully published!";
$l_weEditor["text/js"]["response_unpublish_ok"] = "The JavaScript '%s' has been successfully unpublished!";
$l_weEditor["text/js"]["response_save_notok"] = "Error while saving JavaScript '%s'!";
$l_weEditor["text/js"]["response_path_exists"] = "The JavaScript '%s' could not be saved because another document or directory is positioned at the same location!";
$l_weEditor["text/js"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/js"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/js"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/js"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/plain"]["response_save_ok"] = "The text file '%s' has been successfully saved!";
$l_weEditor["text/plain"]["response_publish_ok"] = "The text file '%s' has been successfully published!";
$l_weEditor["text/plain"]["response_unpublish_ok"] = "The text file '%s' has been successfully unpublished!";
$l_weEditor["text/plain"]["response_save_notok"] = "Error while saving text file '%s'!";
$l_weEditor["text/plain"]["response_path_exists"] = "The text file '%s' could not be saved because another document or directory is positioned at the same location!";
$l_weEditor["text/plain"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/plain"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/plain"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/plain"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/htaccess"]["response_save_ok"] = "The file '%s' has been successfully saved!";
$l_weEditor["text/htaccess"]["response_publish_ok"] = "The file '%s' has been successfully published!";
$l_weEditor["text/htaccess"]["response_unpublish_ok"] = "The file '%s' has been successfully unpublished!";
$l_weEditor["text/htaccess"]["response_save_notok"] = "Error while saving the file '%s'!";
$l_weEditor["text/htaccess"]["response_path_exists"] = "The file '%s' could not be saved because another document or directory is positioned at the same location!";
$l_weEditor["text/htaccess"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/htaccess"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/htaccess"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/htaccess"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/xml"]["response_save_ok"] = "The XML file '%s' has been successfully saved!";
$l_weEditor["text/xml"]["response_publish_ok"] = "The XML file '%s' has been successfully published!";
$l_weEditor["text/xml"]["response_unpublish_ok"] = "The XML file '%s' has been successfully unpublished!";
$l_weEditor["text/xml"]["response_save_notok"] = "Error while saving XML file '%s'!";
$l_weEditor["text/xml"]["response_path_exists"] = "The XML file '%s' could not be saved because another document or directory is positioned at the same location!";
$l_weEditor["text/xml"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/xml"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/xml"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/xml"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["folder"]["response_save_ok"] = "The directory '%s' has been successfully saved!";
$l_weEditor["folder"]["response_publish_ok"] = "The directory '%s' has been successfully published!";
$l_weEditor["folder"]["response_unpublish_ok"] = "The directory '%s' has been successfully unpublished!";
$l_weEditor["folder"]["response_save_notok"] = "Error while saving directory '%s'!";
$l_weEditor["folder"]["response_path_exists"] = "The directory '%s' could not be saved because another document or directory is positioned at the same location!";
$l_weEditor["folder"]["filename_empty"] = "No name entered for this directory!";
$l_weEditor["folder"]["we_filename_notValid"] = "Invalid folder name\\nValid characters are alpha-numeric, upper and lower case, as well as underscore, hyphen and dot (a-z, A-Z, 0-9, _, -, .)";
$l_weEditor["folder"]["we_filename_notAllowed"] = "The name entered for the directory is not allowed!";
$l_weEditor["folder"]["response_save_noperms_to_create_folders"] = "The directory could not be saved because you do not have the neccessary rights to create folders (%s)!";

$l_weEditor["image/*"]["response_save_ok"] = "The image '%s' has been successfully saved!";
$l_weEditor["image/*"]["response_publish_ok"] = "The image '%s' has been successfully published!";
$l_weEditor["image/*"]["response_unpublish_ok"] = "The image '%s' has been successfully unpublished!";
$l_weEditor["image/*"]["response_save_notok"] = "Error while saving image '%s'!";
$l_weEditor["image/*"]["response_path_exists"] = "The image '%s' could not be saved because another document or directory is positioned at the same location!";
$l_weEditor["image/*"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["image/*"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["image/*"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["image/*"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["application/*"]["response_save_ok"] = "The document '%s' has been successfully saved!";
$l_weEditor["application/*"]["response_publish_ok"] = "The document '%s' has been successfully published!";
$l_weEditor["application/*"]["response_unpublish_ok"] = "The document '%s' has been successfully unpublished!";
$l_weEditor["application/*"]["response_save_notok"] = "Error while saving document '%s'!";
$l_weEditor["application/*"]["response_path_exists"] = "The document '%s' could not be saved because another document or directory is positioned at the same location!";
$l_weEditor["application/*"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["application/*"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["application/*"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["application/*"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["application/*"]["we_description_missing"] = "Please enter a desription in the 'Desription' field!";
$l_weEditor["application/*"]["response_save_wrongExtension"] =  "Error while saving '%s' \\nThe file extension '%s' is not valid for other files!\\nPlease create an HTML page for that purpose!";

$l_weEditor["application/x-shockwave-flash"]["response_save_ok"] = "The Flash movie '%s' has been successfully saved!";
$l_weEditor["application/x-shockwave-flash"]["response_publish_ok"] = "The Flash movie '%s' has been successfully published!";
$l_weEditor["application/x-shockwave-flash"]["response_unpublish_ok"] = "The Flash movie '%s' has been successfully unpublished!";
$l_weEditor["application/x-shockwave-flash"]["response_save_notok"] = "Error while saving Flash movie '%s'!";
$l_weEditor["application/x-shockwave-flash"]["response_path_exists"] = "The Flash movie '%s' could not be saved because another document or directory is positioned at the same location!";
$l_weEditor["application/x-shockwave-flash"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["application/x-shockwave-flash"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["application/x-shockwave-flash"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["application/x-shockwave-flash"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["video/quicktime"]["response_save_ok"] = "The Quicktime movie '%s' has been successfully saved!";
$l_weEditor["video/quicktime"]["response_publish_ok"] = "The Quicktime movie '%s' has been successfully published!";
$l_weEditor["video/quicktime"]["response_unpublish_ok"] = "The Quicktime movie '%s' has been successfully unpublished!";
$l_weEditor["video/quicktime"]["response_save_notok"] = "Error while saving Quicktime movie '%s'!";
$l_weEditor["video/quicktime"]["response_path_exists"] = "The Quicktime movie '%s' could not be saved because another document or directory is positioned at the same location!";
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
