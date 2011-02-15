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
$l_weEditor["doubble_field_alert"] = "El campo '%s' ya existe! El nombre de campo debe ser único!";
$l_weEditor["variantNameInvalid"] = "The name of an article variant can not be empty!"; // TRANSLATE

$l_weEditor["folder_save_nok_parent_same"] = "El directorio primario seleccionado está dentro del directorio actual! Por favor, escoja otro directorio e intentelo nuevamente!";
$l_weEditor["pfolder_notsave"] = "El directorio no puede ser salvado en el directorio seleccionado!";
$l_weEditor["required_field_alert"] = "El campo '%s' es requerido y debe ser llenado!";

$l_weEditor["category"]["response_save_ok"] = "La categoría '%s' ha sido exitosamente salvada!";
$l_weEditor["category"]["response_save_notok"] = "Error mientras se salvaba la categoría '%s'!";
$l_weEditor["category"]["response_path_exists"] = "La categoría '%s' no pudo ser salvada porque otra categoría está situada en la misma ubicación!";
$l_weEditor["category"]["we_filename_notValid"] = "Nombre no válido!\\n\", \\' < > y / no están permitidos!";
$l_weEditor["category"]["filename_empty"]       = "El nombre del archivo no puede estar vacío.";
$l_weEditor["category"]["name_komma"] = "Nombre no válido! La coma no está permitida!";

$l_weEditor["text/webedition"]["response_save_ok"] = "La página webEdition '%s' ha sido exitosamente salvada!";
$l_weEditor["text/webedition"]["response_publish_ok"] = "La página webEdition '%s' ha sido exitosamente publicada!";
$l_weEditor["text/webedition"]["response_publish_notok"] = "Error mientras publicaba la página webEdition '%s'!";
$l_weEditor["text/webedition"]["response_unpublish_ok"] = "La página webEdition '%s' ha sido exitosamente des-publicada!";
$l_weEditor["text/webedition"]["response_unpublish_notok"] = "Error mientras se despublicaba la página webEdition '%s'!";
$l_weEditor["text/webedition"]["response_not_published"] = "La página webEdition '%s' no está publicada!";
$l_weEditor["text/webedition"]["response_save_notok"] = "Error mientras se salvaba la página webEdition '%s'!";
$l_weEditor["text/webedition"]["response_path_exists"] = "La página webEdition '%s' no pudo ser salvada porque otro documento o directorio está situado en la misma ubicación!";
$l_weEditor["text/webedition"]["filename_empty"] = "Ningún nombre ha sido entrado para este documento!";
$l_weEditor["text/webedition"]["we_filename_notValid"] = "Nombre de archivo no válido\\nLos carácteres válidos son alpha-númericos, mayúsculas y minúsculas, así como también subrayados, guión y punto (a-z, A-Z, 0-9, _, -, .)";
$l_weEditor["text/webedition"]["we_filename_notAllowed"] = "El nombre de archivo entrado no es permitido!";
$l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"] = "El documento no pudo ser salvado porque Ud no tiene los derechos necesarios para crear carpetas (%s)!";
$l_weEditor["text/webedition"]["autoschedule"] = "La página webEdition será publicada automáticamente en %s.";

$l_weEditor["text/html"]["response_save_ok"] = "La página HTML '%s' ha sido exitosamente salvada!";
$l_weEditor["text/html"]["response_publish_ok"] = "La página HTML '%s' ha sido exitosamente publicada!";
$l_weEditor["text/html"]["response_publish_notok"] = "Error mientras se publicaba la página HTML '%s'!";
$l_weEditor["text/html"]["response_unpublish_ok"] = "La página HTML '%s' ha sido exitosamente des-publicada!";
$l_weEditor["text/html"]["response_unpublish_notok"] = "Error mientras se des-publicaba la página HTML '%s'!";
$l_weEditor["text/html"]["response_not_published"] = "La página HTML '%s' no está publicada!";
$l_weEditor["text/html"]["response_save_notok"] = "Error mientras se salvaba la página HTML '%s'!";
$l_weEditor["text/html"]["response_path_exists"] = "La página HTML '%s' no pudo ser salvada porque otro documento o directorio está situado en la misma ubicación!";
$l_weEditor["text/html"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/html"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/html"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/html"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["text/html"]["autoschedule"] = "The HTML page will be published automatically on %s.";

$l_weEditor["text/weTmpl"]["response_save_ok"] = "La plantilla '%s' ha sido exitosamente salvada!";
$l_weEditor["text/weTmpl"]["response_publish_ok"] = "La plantilla '%s' ha sido exitosamente publicada!";
$l_weEditor["text/weTmpl"]["response_unpublish_ok"] = "La plantilla '%s' ha sido exitosamente des-publicada!";
$l_weEditor["text/weTmpl"]["response_save_notok"] = "Error mientras se salvaba la plantilla '%s'!";
$l_weEditor["text/weTmpl"]["response_path_exists"] = "La plantilla '%s' no pudo ser salvada porque otro documento o directorio está situado en la misma ubicación!";
$l_weEditor["text/weTmpl"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/weTmpl"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/weTmpl"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/weTmpl"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["text/weTmpl"]["no_template_save"] = "Templates " . "can " . "not " . "saved " . "in the " . "de" . "mo" . " of" . " webEdition.";

$l_weEditor["text/css"]["response_save_ok"] = "La hoja de estilo '%s' ha sido exitosamente salvada!";
$l_weEditor["text/css"]["response_publish_ok"] = "La hoja de estilo '%s' ha sido exitosamente publicada!";
$l_weEditor["text/css"]["response_unpublish_ok"] = "La hoja de estilo '%s' ha sido exitosamente des-publicada!";
$l_weEditor["text/css"]["response_save_notok"] = "Error mientras se salvaba la hoja de estilo '%s'!";
$l_weEditor["text/css"]["response_path_exists"] = "La hoja de estilo '%s' no pudo ser salvada porque otro documento o directorio está situado en la misma ubicación!";
$l_weEditor["text/css"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/css"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/css"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/css"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/js"]["response_save_ok"] = "The JavaScript '%s' has been successfully saved!";
$l_weEditor["text/js"]["response_publish_ok"] = "El JavaScript '%s' ha sido exitosamente publicado!";
$l_weEditor["text/js"]["response_unpublish_ok"] = "El JavaScript '%s' ha sido exitosamente des-publicado!";
$l_weEditor["text/js"]["response_save_notok"] = "Error mientras se salvaba JavaScript '%s'!";
$l_weEditor["text/js"]["response_path_exists"] = "El JavaScript '%s' no pudo ser salvado porque otro documento o directorio está situado en la misma ubicación!";
$l_weEditor["text/js"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/js"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/js"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/js"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/plain"]["response_save_ok"] = "The text file '%s' has been successfully saved!";
$l_weEditor["text/plain"]["response_publish_ok"] = "El archivo de texto '%s' ha sido exitosamente publicado!";
$l_weEditor["text/plain"]["response_unpublish_ok"] = "El archivo de texto '%s' ha sido exitosamente des-publicado!";
$l_weEditor["text/plain"]["response_save_notok"] = "Error mientras se salvaba el archivo de texto '%s'!";
$l_weEditor["text/plain"]["response_path_exists"] = "El archivo de texto '%s' no pudo ser salvado porque otro documento o directorio está situado en la misma ubicación!";
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

$l_weEditor["text/xml"]["response_save_ok"] = "The XML file '%s' has been successfully saved!";
$l_weEditor["text/xml"]["response_publish_ok"] = "The XML file '%s' has been successfully published!"; // TRANSLATE
$l_weEditor["text/xml"]["response_unpublish_ok"] = "The XML file '%s' has been successfully unpublished!"; // TRANSLATE
$l_weEditor["text/xml"]["response_save_notok"] = "Error while saving XML file '%s'!"; // TRANSLATE
$l_weEditor["text/xml"]["response_path_exists"] = "The XML file '%s' could not be saved because another document or directory is positioned at the same location!"; // TRANSLATE
$l_weEditor["text/xml"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/xml"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/xml"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/xml"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["folder"]["response_save_ok"] = "The directory '%s' has been successfully saved!";
$l_weEditor["folder"]["response_publish_ok"] = "El directorio '%s' ha sido exitosamente publicado!";
$l_weEditor["folder"]["response_unpublish_ok"] = "El directorio '%s' ha sido exitosamente des-publicado!";
$l_weEditor["folder"]["response_save_notok"] = "Error mientras se salvaba el directorio '%s'!";
$l_weEditor["folder"]["response_path_exists"] = "El directorio '%s' no pudo ser salvado porque otro documento o directorio está situado en la misma ubicación!";
$l_weEditor["folder"]["filename_empty"] = "Ningún nombre entrado para este directorio!";
$l_weEditor["folder"]["we_filename_notValid"] = "Nombre de carpeta no válido\\nLos carácteres válidos son alpha-númericos, mayúsculas y minúsculas, así como también subrayados, guión y punto (a-z, A-Z, 0-9, _, -, .)";
$l_weEditor["folder"]["we_filename_notAllowed"] = "El nombre entrado para el drectorio no está permitido!";
$l_weEditor["folder"]["response_save_noperms_to_create_folders"] = "El directorio no pudo ser salvado porque Ud no tiene los derechos necesarios para crear carpetas (%s)!";

$l_weEditor["image/*"]["response_save_ok"] = "La imagen '%s' ha sido exitosamente salvada!";
$l_weEditor["image/*"]["response_publish_ok"] = "La imagen '%s' ha sido exitosamente publicada!";
$l_weEditor["image/*"]["response_unpublish_ok"] = "La imagen '%s' ha sido exitosamente des-publicada!";
$l_weEditor["image/*"]["response_save_notok"] = "Error mientras se salvaba la imagen '%s'!";
$l_weEditor["image/*"]["response_path_exists"] = "La image '%s' no pudo ser salvada porque otro documento o directorio está situado en la misma ubicación!";
$l_weEditor["image/*"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["image/*"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["image/*"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["image/*"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["application/*"]["response_save_ok"] = "The document '%s' has been successfully saved!";
$l_weEditor["application/*"]["response_publish_ok"] = "El documento '%s' ha sido exitosamente publicado!";
$l_weEditor["application/*"]["response_unpublish_ok"] = "El documento '%s' ha sido exitosamente des-publicado!";
$l_weEditor["application/*"]["response_save_notok"] = "Error mientras se salvaba el documento '%s'!";
$l_weEditor["application/*"]["response_path_exists"] = "El documento '%s' no pudo ser salvado porque otro documento o directorio está situado en la misma ubicación!";
$l_weEditor["application/*"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["application/*"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["application/*"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["application/*"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["application/*"]["we_description_missing"] = "Please enter a desription in the 'Desription' field!";
$l_weEditor["application/*"]["response_save_wrongExtension"] =  "Error tratando de guardar '%s' \\nLa extensión de fichero '%s' no es valida para otros ficheros!\\nPor favor, cree una pagina HTML para ese propósito!";

$l_weEditor["application/x-shockwave-flash"]["response_save_ok"] = "La película Flash '%s' ha sido exitosamente salvada!";
$l_weEditor["application/x-shockwave-flash"]["response_publish_ok"] = "La película Flash '%s' ha sido exitosamente publicada!";
$l_weEditor["application/x-shockwave-flash"]["response_unpublish_ok"] = "La película Flash '%s' ha sido exitosamente des-publicada!";
$l_weEditor["application/x-shockwave-flash"]["response_save_notok"] = "Error mientras se salvaba la película Flash '%s'!";
$l_weEditor["application/x-shockwave-flash"]["response_path_exists"] = "La película Flash '%s' no pudo ser salvada porque otro documento o directorio está situado en la misma ubicación!";
$l_weEditor["application/x-shockwave-flash"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["application/x-shockwave-flash"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["application/x-shockwave-flash"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["application/x-shockwave-flash"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["video/quicktime"]["response_save_ok"] = "The Quicktime movie '%s' has been successfully saved!";
$l_weEditor["video/quicktime"]["response_publish_ok"] = "La película Quicktime '%s' ha sido exitosamente publicada!";
$l_weEditor["video/quicktime"]["response_unpublish_ok"] = "La película Quicktime '%s' ha sido exitosamente des-publicad!";
$l_weEditor["video/quicktime"]["response_save_notok"] = "Error mientras se salvaba la película Quicktime '%s'!";
$l_weEditor["video/quicktime"]["response_path_exists"] = "La película Quicktime '%s' no pudo ser salvada porque otro documento o directorio está situado en la misma ubicación!!";
$l_weEditor["video/quicktime"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["video/quicktime"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["video/quicktime"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["video/quicktime"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

/*****************************************************************************
 * PLEASE DON'T TOUCH THE NEXT LINES
 * UNLESS YOU KNOW EXACTLY WHAT YOU ARE DOING!
 *****************************************************************************/

$_language_directory = $_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/modules";
$_directory = dir($_language_directory);

while (false !== ($entry = $_directory->read())) {
	if (strstr($entry, '_we_editor')) {
		include($_language_directory."/".$entry);
	}
}
