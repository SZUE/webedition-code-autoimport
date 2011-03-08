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
$l_searchtool = array(
		'save_group_ok' => 'The group has been succesfully saved', // TRANSLATE
		'save_ok' => 'The entry has been succesfully saved.', // TRANSLATE
		'save_group_failed' => 'The group could not be saved.', // TRANSLATE
		'save_failed' => 'The entry could not be saved.', // TRANSLATE
		'weSearch' => 'Search', // TRANSLATE

		'suchen' => 'Search',
		'NEW_SUCHE' => "The user is allowed to create new items in the search", // TRANSLATE
		'DELETE_SUCHE' => "The user is allowed to delete items from the search", // TRANSLATE
		'EDIT_SUCHE' => "The user is allowed to edit items in the search", // TRANSLATE
//Tree
		'vordefinierteSuchanfragen' => 'predefined searches', // TRANSLATE
		'dokumente' => 'documents', // TRANSLATE
		'objekte' => 'objects', // TRANSLATE
		'unveroeffentlicheDokumente' => 'unpublished documents', // TRANSLATE
		'statischeDokumente' => 'static documents', // TRANSLATE
		'dynamischeDokumente' => 'dynamic documents', // TRANSLATE
		'unveroeffentlicheObjekte' => 'unpublished objects', // TRANSLATE
		'eigeneSuchanfragen' => 'own searches', // TRANSLATE
		'versionen' => 'versions', // TRANSLATE
		'geloeschteDokumente' => 'deleted documents', // TRANSLATE
		'geloeschteObjekte' => 'deleted objects', // TRANSLATE
//Navigation
		'menu_suche' => 'Search', // TRANSLATE
		'menu_info' => 'Info', // TRANSLATE
		'menu_help' => 'Help', // TRANSLATE
		'menu_new' => 'New Search', // TRANSLATE
		'menu_save' => 'Save', // TRANSLATE
		'menu_delete' => 'Delete', // TRANSLATE
		'menu_exit' => 'Close', // TRANSLATE
		'forDocuments' => 'For Documents', // TRANSLATE
		'forTemplates' => 'For Templates', // TRANSLATE
		'forObjects' => 'For Objects', // TRANSLATE
		'menu_new_group' => 'New Group', // TRANSLATE
		'menu_advSearch' => 'Advanced Search', // TRANSLATE
//Tabs
		'documents' => 'Documents', // TRANSLATE
		'templates' => 'Templates', // TRANSLATE
		'advSearch' => 'Advanced Search', // TRANSLATE
		'properties' => 'Properties', // TRANSLATE

		'objects' => 'Objects', // TRANSLATE
		'classes' => 'Classes', // TRANSLATE
//Top
		'topDir' => 'Folder', // TRANSLATE
		'topSuche' => 'Search', // TRANSLATE
//Content
		'general' => 'General', // TRANSLATE
		'suchenIn' => "Search in", // TRANSLATE
		'text' => 'Text', // TRANSLATE
		'anzeigen' => 'Show', // TRANSLATE
		'dir' => 'Folder', // TRANSLATE
		'optionen' => 'Options', // TRANSLATE
//Fields
		'allFields' => 'all fields', // TRANSLATE
		'ID' => 'ID of entry', // TRANSLATE
		'Text' => 'Name of entry', // TRANSLATE
		'Path' => 'Path of entry', // TRANSLATE
		'ParentIDDoc' => 'parent entry documents', // TRANSLATE
		'ParentIDTmpl' => 'parent entry templates', // TRANSLATE
		'ParentIDObj' => 'parent entry objects', // TRANSLATE
		'temp_template_id' => 'Template', // TRANSLATE
		'MasterTemplateID' => 'Mastertemplate', // TRANSLATE
		'ContentType' => 'Type of content', // TRANSLATE
		'temp_doc_type' => 'Document-type', // TRANSLATE
		'temp_category' => 'Category', // TRANSLATE
		'CreatorID' => 'ID of owner', // TRANSLATE
		'CreatorName' => 'Name of owner', // TRANSLATE
		'WebUserID' => 'ID of webuser', // TRANSLATE
		'WebUserName' => 'Name of webuser', // TRANSLATE
		'Status' => 'Status', // TRANSLATE
		'Speicherart' => 'Save type', // TRANSLATE
		'Published' => 'Date of publishing', // TRANSLATE
		'CreationDate' => 'Date of creation', // TRANSLATE
		'ModDate' => 'Date of modification', // TRANSLATE

		'CONTAIN' => 'contains', // TRANSLATE
		'IS' => 'equal (=)', // TRANSLATE
		'START' => 'starts with', // TRANSLATE
		'END' => 'ends with', // TRANSLATE
		'<' => 'less then (<)', // TRANSLATE
		'<=' => 'less equal (<=)', // TRANSLATE
		'>=' => 'greater equal (>=)', // TRANSLATE
		'>' => 'greater then (>)', // TRANSLATE

		'jeder' => 'show all', // TRANSLATE
		'geparkt' => 'unpublished', // TRANSLATE
		'veroeffentlicht' => 'published', // TRANSLATE
		'geaendert' => 'modified', // TRANSLATE
		'veroeff_geaendert' => 'published and modified', // TRANSLATE
		'geparkt_geaendert' => 'unpublished and modified', // TRANSLATE
		'dynamisch' => 'dynamic', // TRANSLATE
		'statisch' => 'static', // TRANSLATE
		'deleted' => 'deleted', // TRANSLATE


		'onlyTitle' => "In Title", // TRANSLATE
		'onlyFilename' => "In Filename", // TRANSLATE
		'Content' => "In complete Content", // TRANSLATE
//result columns
		'dateiname' => "Filename", // TRANSLATE
		'seitentitel' => "Title", // TRANSLATE
		'created' => "Created", // TRANSLATE
		'modified' => "Modified", // TRANSLATE
//messages
		'predefinedSearchmodify' => "It is not possible to safe predefined searches!", // TRANSLATE
		'predefinedSearchdelete' => "It is not possible to delete predefined searches!", // TRANSLATE
		'nameForSearch' => "Choose a name for your search:", // TRANSLATE
		'no_hochkomma' => "Invalid name! Invalid character are ' (apostrophe) or \" (quote)!", // TRANSLATE
		'confirmDel' => 'Delete entry.\\nAre you sure?', // TRANSLATE
		'nameTooLong' => 'In the name there are allowed at most 30 characters!', // TRANSLATE
		'nothingCheckedAdv' => 'Nothing is checked to search for!', // TRANSLATE
		'nothingCheckedTmplDoc' => 'Nothing is checked to search for!', // TRANSLATE
		'noTempTableRightsSearch' => 'In order to use the search it is necessary to generate a temporary table or to be able to delete tables. Therefore you do not have the specific mysql-user-right.', // TRANSLATE
		'noTempTableRightsDoclist' => 'In order to show all included documents it is necessary to generate a temporary table or to be able to delete tables. Therefore you do not have the specific mysql-user-right.', // TRANSLATE

		'date_format' => 'd.m.Y', // TRANSLATE

		'eintraege_pro_seite' => 'View', // TRANSLATE
		'no_template' => "-", // TRANSLATE
		'creator' => "owner", // TRANSLATE
		'nobody' => "nobody", // TRANSLATE
		'template' => "template", // TRANSLATE
		'metafelder' => "Metafields (max. 6)", // TRANSLATE

		'dateityp' => 'File Type', // TRANSLATE
		'groesse' => 'Size', // TRANSLATE
		'aufloesung' => 'Resolution', // TRANSLATE
		'beschreibung' => 'Description', // TRANSLATE
		'idDiv' => 'ID', // TRANSLATE

		'publish_docs' => 'Do you want to publish the market documents?', // TRANSLATE
		'notChecked' => 'No documents are selected.', // TRANSLATE
		'publishOK' => 'Documents were published.', // TRANSLATE
);

$l_searchtool = array_merge($l_searchtool, array(
		'perm_group_title' => $l_searchtool['weSearch'],
		'perm_group_title' => $l_searchtool['weSearch'],
		'import_tool_weSearch_data' => "Restore " . $l_searchtool['weSearch'] . " data", // TRANSLATE
		'export_tool_weSearch_data' => "Save " . $l_searchtool['weSearch'] . " data", // TRANSLATE
				));