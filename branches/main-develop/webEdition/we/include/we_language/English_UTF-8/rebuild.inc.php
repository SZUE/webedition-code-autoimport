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
		'rebuild_documents' => "Rebuild - documents",
		'rebuild_maintable' => "Resave main table",
		'rebuild_tmptable' => "Resave temporary table",
		'rebuild_objects' => "Objects",
		'rebuild_index' => "Index-table",
		'rebuild_all' => "All documents and templates",
		'rebuild_templates' => "All templates",
		'rebuild_filter' => "Static webEdition pages",
		'rebuild_thumbnails' => "Rebuild - generate thumbnails",
		'txt_rebuild_documents' => "With this option the documents and templates saved in the data base will be written to the file system.",
		'txt_rebuild_objects' => "Choose this option to rewrite the object tables. This is only necessary if the object tables are incorrect.",
		'txt_rebuild_index' => "If in search some documents can not be found or are being found several times, you can rewrite the index table thus the search index here.",
		'txt_rebuild_thumbnails' => "Here you can rewrite the thumbnails of your graphics.",
		'txt_rebuild_all' => "With this option all documents and templates will be rewritten.",
		'txt_rebuild_templates' => "With this option all templates will be rewritten.",
		'txt_rebuild_filter' => "Here you can specify which static webEdition pages should be rewritten. If you don't select a criteria all static webEdition pages will be rewritten.",
		'rebuild' => "Rebuild",
		'dirs' => "Directories",
		'thumbdirs' => "For graphics in the following directories",
		'thumbnails' => "Generate thumbnails",
		'documents' => "Documents and templates",
		'catAnd' => "AND concatenation",
		'finished' => "The rebuild was successful!",
		'nothing_to_rebuild' => "There are no documents that correspond to the criteria!",
		'no_thumbs_selected' => "Please, choose at least one thumbnail!",
		'savingDocument' => "Saving document: ",
		'navigation' => "Navigation",
		'rebuild_navigation' => "Rebuild - Navigation",
		'txt_rebuild_navigation' => "Here you can rewrite the navigation cache.",
		'rebuildStaticAfterNaviCheck' => 'Rebuild static documents afterwards.',
		'rebuildStaticAfterNaviHint' => 'For static navigation entries a rebuild of the corresponding documents is necessary, in addition.',
		'metadata' => 'Meta data fields',
		'txt_rebuild_metadata' => 'To import the meta data of your images subsequently, choose this option.', // TRANSLATE
		'rebuild_metadata' => 'Rebuild - meta data fields',
		'onlyEmpty' => 'Import only empty meta data fields',
		'expl_rebuild_metadata' => 'Select the meta data fields you want to import. To import only fields which already have no content, select the option "Import only empty meta data fields".', // TRANSLATE
		'noFieldsChecked' => "Al least one meta data field must be selected!", // TRANSLATE
);