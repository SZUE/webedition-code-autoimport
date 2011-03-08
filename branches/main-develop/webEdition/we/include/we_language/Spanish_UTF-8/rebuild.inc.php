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
 * Language file: rebuild.inc.php
 * Provides language strings.
 * Language: English
 */
$l_rebuild = array(
		'rebuild_documents' => "Rebuild - documents", // TRANSLATE
		'rebuild_maintable' => "Salvar nuevamente la tabla principal",
		'rebuild_tmptable' => "Salvar nuevamente la tabla temporal",
		'rebuild_objects' => "Objetos",
		'rebuild_index' => "Tabla-índice",
		'rebuild_all' => "Todos documentos y plantillas",
		'rebuild_templates' => "Todos plantillas",
		'rebuild_filter' => "Páginas webEdition estáticas",
		'rebuild_thumbnails' => "Reconstruir - generar imágenes en miniatura",
		'txt_rebuild_documents' => "With this option the documents and templates saved in the data base will be written to the file system.", // TRANSLATE
		'txt_rebuild_objects' => "Escoja esta opción para reescribir las tablas de objetos. Esto es solamente necesario si las tablas de objetos son incorrectas.",
		'txt_rebuild_index' => "If in search some documents can not be found or are being found several times, you can rewrite the index table thus the search index here.", // TRANSLATE
		'txt_rebuild_thumbnails' => "Aquí Ud puede reescribir las imágenes en miniatura de sus gráficos.",
		'txt_rebuild_all' => "Con esta opción serán reescritos todos los documentos y plantillas.",
		'txt_rebuild_templates' => "Con esta opción serán reescritos todos los plantillas.",
		'txt_rebuild_filter' => "Aquí Ud puede especificar cuales páginas webEdition estáticas deben ser reescritas. Si Ud no selecciona un criterio, todas las páginas webEdition estáticas serán reescritas.",
		'rebuild' => "Reconstruir",
		'dirs' => "Directorios",
		'thumbdirs' => "Para los gráficos en los siguientes directorios",
		'thumbnails' => "Generar imágenes en miniatura",
		'documents' => "Documents and templates", // TRANSLATE
		'catAnd' => "Concatenación Y",
		'finished' => "La reconstrucción fue exitosa!",
		'nothing_to_rebuild' => "No hay documentos que se correspondan al criterio!",
		'no_thumbs_selected' => "Por favor, escoja al menos una imagen en miniatura!",
		'savingDocument' => "Salvando documento: ",
		'navigation' => "Navigation", // TRANSLATE
		'rebuild_navigation' => "Rebuild - Navigation", // TRANSLATE
		'txt_rebuild_navigation' => "Here you can rewrite the navigation cache.", // TRANSLATE
		'rebuildStaticAfterNaviCheck' => 'Rebuild static documents afterwards.', // TRANSLATE
		'rebuildStaticAfterNaviHint' => 'For static navigation entries a rebuild of the corresponding documents is necessary, in addition.', // TRANSLATE
		'metadata' => 'Meta data fields', // TRANSLATE
		'txt_rebuild_metadata' => 'To import the meta data of your images subsequently, choose this option.', // TRANSLATE  // TRANSLATE
		'rebuild_metadata' => 'Rebuild - meta data fields', // TRANSLATE
		'onlyEmpty' => 'Import only empty meta data fields', // TRANSLATE
		'expl_rebuild_metadata' => 'Select the meta data fields you want to import. To import only fields which already have no content, select the option "Import only empty meta data fields".', // TRANSLATE // TRANSLATE
		'noFieldsChecked' => "Al least one meta data field must be selected!", // TRANSLATE // TRANSLATE
);