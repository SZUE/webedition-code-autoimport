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
		'save_group_ok' => 'The group has been succesfully saved',
		'save_ok' => 'The entry has been succesfully saved.',
		'save_group_failed' => 'The group could not be saved.',
		'save_failed' => 'The entry could not be saved.',
		'weSearch' => 'Search',
		'suchen' => 'Search',
		'NEW_SUCHE' => "The user is allowed to create new items in the search",
		'DELETE_SUCHE' => "The user is allowed to delete items from the search",
		'EDIT_SUCHE' => "The user is allowed to edit items in the search",
//Tree
		'vordefinierteSuchanfragen' => 'predefined searches',
		'dokumente' => 'documents',
		'objekte' => 'objects',
		'unveroeffentlicheDokumente' => 'unpublished documents',
		'statischeDokumente' => 'static documents',
		'dynamischeDokumente' => 'dynamic documents',
		'unveroeffentlicheObjekte' => 'unpublished objects',
		'eigeneSuchanfragen' => 'own searches',
		'versionen' => 'versions',
		'geloeschteDokumente' => 'deleted documents',
		'geloeschteObjekte' => 'deleted objects',
//Navigation
		'menu_suche' => 'Search',
		'menu_info' => 'Info',
		'menu_help' => 'Help',
		'menu_new' => 'New Search',
		'menu_save' => 'Save',
		'menu_delete' => 'Delete',
		'menu_exit' => 'Close',
		'forDocuments' => 'For Documents',
		'forTemplates' => 'For Templates',
		'forObjects' => 'For Objects',
		'menu_new_group' => 'New Group',
		'menu_advSearch' => 'Advanced Search',
//Tabs
		'documents' => 'Documents',
		'templates' => 'Templates',
		'advSearch' => 'Advanced Search',
		'properties' => 'Properties',
		'objects' => 'Objects',
		'classes' => 'Classes',
//Top
		'topDir' => 'Folder',
		'topSuche' => 'Search',
//Content
		'general' => 'General',
		'suchenIn' => "Search in",
		'text' => 'Text',
		'anzeigen' => 'Show',
		'dir' => 'Folder',
		'optionen' => 'Options',
//Fields
		'allFields' => 'all fields',
		'ID' => 'ID of entry',
		'Text' => 'Name of entry',
		'Path' => 'Path of entry',
		'ParentIDDoc' => 'parent entry documents',
		'ParentIDTmpl' => 'parent entry templates',
		'ParentIDObj' => 'parent entry objects',
		'temp_template_id' => 'Template',
		'MasterTemplateID' => 'Mastertemplate',
		'ContentType' => 'Type of content',
		'temp_doc_type' => 'Document-type',
		'temp_category' => 'Category',
		'CreatorID' => 'ID of owner',
		'CreatorName' => 'Name of owner',
		'WebUserID' => 'ID of webuser',
		'WebUserName' => 'Name of webuser',
		'Status' => 'Status',
		'Speicherart' => 'Save type',
		'Published' => 'Date of publishing',
		'CreationDate' => 'Date of creation',
		'ModDate' => 'Date of modification',
		'CONTAIN' => 'contains',
		'IS' => 'equal (=)',
		'START' => 'starts with',
		'END' => 'ends with',
		'<' => 'less then (<)',
		'<=' => 'less equal (<=)',
		'>=' => 'greater equal (>=)',
		'>' => 'greater then (>)',
		'jeder' => 'show all',
		'geparkt' => 'unpublished',
		'veroeffentlicht' => 'published',
		'geaendert' => 'modified',
		'veroeff_geaendert' => 'published and modified',
		'geparkt_geaendert' => 'unpublished and modified',
		'dynamisch' => 'dynamic',
		'statisch' => 'static',
		'deleted' => 'deleted',
		'onlyTitle' => "In Title",
		'onlyFilename' => "In Filename",
		'Content' => "In complete Content",
//result columns
		'dateiname' => "Filename",
		'seitentitel' => "Title",
		'created' => "Created",
		'modified' => "Modified",
//messages
		'predefinedSearchmodify' => "It is not possible to safe predefined searches!",
		'predefinedSearchdelete' => "It is not possible to delete predefined searches!",
		'nameForSearch' => "Choose a name for your search:",
		'no_hochkomma' => "Invalid name! Invalid character are ' (apostrophe) or \" (quote)!",
		'confirmDel' => 'Delete entry.\\nAre you sure?',
		'nameTooLong' => 'In the name there are allowed at most 30 characters!',
		'nothingCheckedAdv' => 'Nothing is checked to search for!',
		'nothingCheckedTmplDoc' => 'Nothing is checked to search for!',
		'noTempTableRightsSearch' => 'In order to use the search it is necessary to generate a temporary table or to be able to delete tables. Therefore you do not have the specific mysql-user-right.',
		'noTempTableRightsDoclist' => 'In order to show all included documents it is necessary to generate a temporary table or to be able to delete tables. Therefore you do not have the specific mysql-user-right.',
		'date_format' => 'd.m.Y',
		'eintraege_pro_seite' => 'View',
		'no_template' => "-",
		'creator' => "owner",
		'nobody' => "nobody",
		'template' => "template",
		'metafelder' => "Metafields (max. 6)",
		'dateityp' => 'File Type',
		'groesse' => 'Size',
		'aufloesung' => 'Resolution',
		'beschreibung' => 'Description',
		'idDiv' => 'ID',
		'publish_docs' => 'Do you want to publish the market documents?',
		'notChecked' => 'No documents are selected.',
		'publishOK' => 'Documents were published.',
);

$l_searchtool = array_merge($l_searchtool, array(
		'perm_group_title' => $l_searchtool['weSearch'],
		'perm_group_title' => $l_searchtool['weSearch'],
		'import_tool_weSearch_data' => "Restore " . $l_searchtool['weSearch'] . " data",
		'export_tool_weSearch_data' => "Save " . $l_searchtool['weSearch'] . " data",
				));