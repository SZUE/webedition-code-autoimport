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
		'rebuild_maintable' => "Пересохранить главную таблицу",
		'rebuild_tmptable' => "Пересохранить временную таблицу",
		'rebuild_objects' => "Объекты",
		'rebuild_index' => "Таблицы индексов",
		'rebuild_all' => "Все документы и шаблоны",
		'rebuild_templates' => "All templates", // TRANSLATE
		'rebuild_filter' => "Статические страницы webEdition",
		'rebuild_thumbnails' => "Перестройка – создание иконок",
		'txt_rebuild_documents' => "With this option the documents and templates saved in the data base will be written to the file system.", // TRANSLATE
		'txt_rebuild_objects' => "Выберите данную опцию для перезаписи таблиц объектов. Это необходимо при наличии ошибок в таблицах объектов.",
		'txt_rebuild_index' => "If in search some documents can not be found or are being found several times, you can rewrite the index table thus the search index here.", // TRANSLATE
		'txt_rebuild_thumbnails' => "Здесь можно переписать иконки графических изображений.",
		'txt_rebuild_all' => "С помощью данной опции переписываются все документы и шаблоны.",
		'txt_rebuild_templates' => "With this option all templates will be rewritten.", // TRANSLATE
		'txt_rebuild_filter' => "Здесь можно указать статические страницы, предназначенные для перезаписи. Если Вы ничего не указали, все статические страницы webEdition будут переписаны вновь.",
		'rebuild' => "Перестроить",
		'dirs' => "директории",
		'thumbdirs' => "графику в следующих директориях",
		'thumbnails' => "создать иконки",
		'documents' => "Documents and templates", // TRANSLATE
		'catAnd' => "и соединение",
		'finished' => "Перестройка успешно завершена!",
		'nothing_to_rebuild' => "Нет статических документов, отвечающих выбранным критериям!",
		'no_thumbs_selected' => "Выберите, пожалуйста, по крайней мере одну иконку!",
		'savingDocument' => "Сохранение документа: ",
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