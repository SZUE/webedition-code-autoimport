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
		'filename_empty' => "Ningún nombre ha sido entrado para este documento!",
		'we_filename_notValid' => "Nombre de archivo no válido\\nLos carácteres válidos son alpha-númericos, mayúsculas y minúsculas, así como también subrayados, guión y punto (a-z, A-Z, 0-9, _, -, .)",
		'we_filename_notAllowed' => "El nombre de archivo entrado no es permitido!",
		'response_save_noperms_to_create_folders' => "El documento no pudo ser salvado porque Ud no tiene los derechos necesarios para crear carpetas (%s)!",
);
$l_weEditor = array(
		'doubble_field_alert' => "El campo '%s' ya existe! El nombre de campo debe ser único!",
		'variantNameInvalid' => "The name of an article variant can not be empty!", // TRANSLATE

		'folder_save_nok_parent_same' => "El directorio primario seleccionado está dentro del directorio actual! Por favor, escoja otro directorio e intentelo nuevamente!",
		'pfolder_notsave' => "El directorio no puede ser salvado en el directorio seleccionado!",
		'required_field_alert' => "El campo '%s' es requerido y debe ser llenado!",
		'category' => array(
				'response_save_ok' => "La categoría '%s' ha sido exitosamente salvada!",
				'response_save_notok' => "Error mientras se salvaba la categoría '%s'!",
				'response_path_exists' => "La categoría '%s' no pudo ser salvada porque otra categoría está situada en la misma ubicación!",
				'we_filename_notValid' => "Nombre no válido!\\n\", \\' < > y / no están permitidos!",
				'filename_empty' => "El nombre del archivo no puede estar vacío.",
				'name_komma' => "Nombre no válido! La coma no está permitida!",
		),
		'text/webedition' => array_merge($l__tmp, array(
				'response_save_ok' => "La página webEdition '%s' ha sido exitosamente salvada!",
				'response_publish_ok' => "La página webEdition '%s' ha sido exitosamente publicada!",
				'response_publish_notok' => "Error mientras publicaba la página webEdition '%s'!",
				'response_unpublish_ok' => "La página webEdition '%s' ha sido exitosamente des-publicada!",
				'response_unpublish_notok' => "Error mientras se despublicaba la página webEdition '%s'!",
				'response_not_published' => "La página webEdition '%s' no está publicada!",
				'response_save_notok' => "Error mientras se salvaba la página webEdition '%s'!",
				'response_path_exists' => "La página webEdition '%s' no pudo ser salvada porque otro documento o directorio está situado en la misma ubicación!",
				'autoschedule' => "La página webEdition será publicada automáticamente en %s.",
		)),
		'text/html' => array_merge($l__tmp, array(
				'response_save_ok' => "La página HTML '%s' ha sido exitosamente salvada!",
				'response_publish_ok' => "La página HTML '%s' ha sido exitosamente publicada!",
				'response_publish_notok' => "Error mientras se publicaba la página HTML '%s'!",
				'response_unpublish_ok' => "La página HTML '%s' ha sido exitosamente des-publicada!",
				'response_unpublish_notok' => "Error mientras se des-publicaba la página HTML '%s'!",
				'response_not_published' => "La página HTML '%s' no está publicada!",
				'response_save_notok' => "Error mientras se salvaba la página HTML '%s'!",
				'response_path_exists' => "La página HTML '%s' no pudo ser salvada porque otro documento o directorio está situado en la misma ubicación!",
				'autoschedule' => "The HTML page will be published automatically on %s.",
		)),
		'text/weTmpl' => array_merge($l__tmp, array(
				'response_save_ok' => "La plantilla '%s' ha sido exitosamente salvada!",
				'response_publish_ok' => "La plantilla '%s' ha sido exitosamente publicada!",
				'response_unpublish_ok' => "La plantilla '%s' ha sido exitosamente des-publicada!",
				'response_save_notok' => "Error mientras se salvaba la plantilla '%s'!",
				'response_path_exists' => "La plantilla '%s' no pudo ser salvada porque otro documento o directorio está situado en la misma ubicación!",
				'no_template_save' => "Templates " . "can " . "not " . "saved " . "in the " . "de" . "mo" . " of" . " webEdition.",
		)),
		'text/css' => array_merge($l__tmp, array(
				'response_save_ok' => "La hoja de estilo '%s' ha sido exitosamente salvada!",
				'response_publish_ok' => "La hoja de estilo '%s' ha sido exitosamente publicada!",
				'response_unpublish_ok' => "La hoja de estilo '%s' ha sido exitosamente des-publicada!",
				'response_save_notok' => "Error mientras se salvaba la hoja de estilo '%s'!",
				'response_path_exists' => "La hoja de estilo '%s' no pudo ser salvada porque otro documento o directorio está situado en la misma ubicación!",
		)),
		'text/js' => array_merge($l__tmp, array(
				'response_save_ok' => "The JavaScript '%s' has been successfully saved!",
				'response_publish_ok' => "El JavaScript '%s' ha sido exitosamente publicado!",
				'response_unpublish_ok' => "El JavaScript '%s' ha sido exitosamente des-publicado!",
				'response_save_notok' => "Error mientras se salvaba JavaScript '%s'!",
				'response_path_exists' => "El JavaScript '%s' no pudo ser salvado porque otro documento o directorio está situado en la misma ubicación!",
		)),
		'text/plain' => array_merge($l__tmp, array(
				'response_save_ok' => "The text file '%s' has been successfully saved!",
				'response_publish_ok' => "El archivo de texto '%s' ha sido exitosamente publicado!",
				'response_unpublish_ok' => "El archivo de texto '%s' ha sido exitosamente des-publicado!",
				'response_save_notok' => "Error mientras se salvaba el archivo de texto '%s'!",
				'response_path_exists' => "El archivo de texto '%s' no pudo ser salvado porque otro documento o directorio está situado en la misma ubicación!",
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
				'response_publish_ok' => "The XML file '%s' has been successfully published!", // TRANSLATE
				'response_unpublish_ok' => "The XML file '%s' has been successfully unpublished!", // TRANSLATE
				'response_save_notok' => "Error while saving XML file '%s'!", // TRANSLATE
				'response_path_exists' => "The XML file '%s' could not be saved because another document or directory is positioned at the same location!", // TRANSLATE
		)),
		'folder' => array(
				'response_save_ok' => "The directory '%s' has been successfully saved!",
				'response_publish_ok' => "El directorio '%s' ha sido exitosamente publicado!",
				'response_unpublish_ok' => "El directorio '%s' ha sido exitosamente des-publicado!",
				'response_save_notok' => "Error mientras se salvaba el directorio '%s'!",
				'response_path_exists' => "El directorio '%s' no pudo ser salvado porque otro documento o directorio está situado en la misma ubicación!",
				'filename_empty' => "Ningún nombre entrado para este directorio!",
				'we_filename_notValid' => "Nombre de carpeta no válido\\nLos carácteres válidos son alpha-númericos, mayúsculas y minúsculas, así como también subrayados, guión y punto (a-z, A-Z, 0-9, _, -, .)",
				'we_filename_notAllowed' => "El nombre entrado para el drectorio no está permitido!",
				'response_save_noperms_to_create_folders' => "El directorio no pudo ser salvado porque Ud no tiene los derechos necesarios para crear carpetas (%s)!",
		),
		'image/*' => array_merge($l__tmp, array(
				'response_save_ok' => "La imagen '%s' ha sido exitosamente salvada!",
				'response_publish_ok' => "La imagen '%s' ha sido exitosamente publicada!",
				'response_unpublish_ok' => "La imagen '%s' ha sido exitosamente des-publicada!",
				'response_save_notok' => "Error mientras se salvaba la imagen '%s'!",
				'response_path_exists' => "La image '%s' no pudo ser salvada porque otro documento o directorio está situado en la misma ubicación!",
		)),
		'application/*' => array_merge($l__tmp, array(
				'response_save_ok' => "The document '%s' has been successfully saved!",
				'response_publish_ok' => "El documento '%s' ha sido exitosamente publicado!",
				'response_unpublish_ok' => "El documento '%s' ha sido exitosamente des-publicado!",
				'response_save_notok' => "Error mientras se salvaba el documento '%s'!",
				'response_path_exists' => "El documento '%s' no pudo ser salvado porque otro documento o directorio está situado en la misma ubicación!",
				'we_description_missing' => "Please enter a desription in the 'Desription' field!",
				'response_save_wrongExtension' => "Error tratando de guardar '%s' \\nLa extensión de fichero '%s' no es valida para otros ficheros!\\nPor favor, cree una pagina HTML para ese propósito!",
		)),
		'application/x-shockwave-flash' => array_merge($l__tmp, array(
				'response_save_ok' => "La película Flash '%s' ha sido exitosamente salvada!",
				'response_publish_ok' => "La película Flash '%s' ha sido exitosamente publicada!",
				'response_unpublish_ok' => "La película Flash '%s' ha sido exitosamente des-publicada!",
				'response_save_notok' => "Error mientras se salvaba la película Flash '%s'!",
				'response_path_exists' => "La película Flash '%s' no pudo ser salvada porque otro documento o directorio está situado en la misma ubicación!",
		)),
		'video/quicktime' => array_merge($l__tmp, array(
				'response_save_ok' => "The Quicktime movie '%s' has been successfully saved!",
				'response_publish_ok' => "La película Quicktime '%s' ha sido exitosamente publicada!",
				'response_unpublish_ok' => "La película Quicktime '%s' ha sido exitosamente des-publicad!",
				'response_save_notok' => "Error mientras se salvaba la película Quicktime '%s'!",
				'response_path_exists' => "La película Quicktime '%s' no pudo ser salvada porque otro documento o directorio está situado en la misma ubicación!!",
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
