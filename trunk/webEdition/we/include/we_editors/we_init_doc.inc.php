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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
// exit if script called directly
//FIXME: make this a function!
if(str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) == str_replace(dirname(__FILE__), '', __FILE__)){
	exit();
}
if(isset($GLOBALS['we_ContentType']) && !isset($we_ContentType)){
	$we_ContentType = $GLOBALS['we_ContentType'];
}
if((!isset($we_ContentType)) && ((!isset($we_dt)) || (!is_array($we_dt)) || (!$we_dt[0]['ClassName'])) && isset($we_ID) && $we_ID && isset($we_Table) && $we_Table){
	$we_ContentType = f('SELECT ContentType FROM ' . $GLOBALS['DB_WE']->escape($we_Table) . ' WHERE ID=' . intval($we_ID));
}

$showDoc = isset($GLOBALS['FROM_WE_SHOW_DOC']) && $GLOBALS['FROM_WE_SHOW_DOC'];
switch(isset($we_ContentType) ? $we_ContentType : ''){
	case we_base_ContentTypes::VIDEO:
		$we_doc = new we_document_video();
		break;
	case we_base_ContentTypes::AUDIO:
		$we_doc = new we_document_audio();
		break;
	case we_base_ContentTypes::FLASH:
		$we_doc = new we_flashDocument();
		break;
	case we_base_ContentTypes::QUICKTIME:
		$we_doc = new we_quicktimeDocument();
		break;
	case we_base_ContentTypes::IMAGE:
		$we_doc = new we_imageDocument();
		break;
	case we_base_ContentTypes::FOLDER:
		$we_doc = new we_folder();
		break;
	case 'class_folder':
		$we_doc = new we_class_folder();
		break;
	case 'nested_class_folder':
		$we_doc = new we_class_folder();
		$we_doc->IsClassFolder = 0;
		$we_ContentType = 'folder';
		break;
	case we_base_ContentTypes::TEMPLATE:
		$we_doc = new we_template();
		break;
	case we_base_ContentTypes::WEDOCUMENT:
		$we_doc = new we_webEditionDocument(); //($showDoc ? new we_webEditionDocument() : new we_view_webEditionDocument());
		break;
	case we_base_ContentTypes::HTML:
		$we_doc = new we_htmlDocument();
		break;
	case we_base_ContentTypes::XML:
	case we_base_ContentTypes::JS:
	case we_base_ContentTypes::CSS:
	case we_base_ContentTypes::TEXT:
	case we_base_ContentTypes::HTACESS:
		$we_doc = new we_textDocument();
		break;
	case we_base_ContentTypes::APPLICATION:
		$we_doc = new we_otherDocument();
		break;
	case '':
		$we_doc = (isset($we_dt[0]['ClassName']) && $we_dt[0]['ClassName'] && ($classname = $we_dt[0]['ClassName']) ?
						new $classname() :
						new we_webEditionDocument());
		break;
	default:
		$classname = 'we_' . $we_ContentType;
		if(class_exists($classname)){
			$we_doc = new $classname();
		} else {
			t_e('Can NOT initialize document of type -' . $we_ContentType . '- ' . 'we_' . $we_ContentType . '.inc.php');
			exit(1);
		}
}

if(isset($we_ID) && $we_ID){
	$we_doc->initByID($we_ID, $we_Table, ( (isset($GLOBALS['FROM_WE_SHOW_DOC']) && $GLOBALS['FROM_WE_SHOW_DOC']) || (isset($GLOBALS['WE_RESAVE']) && $GLOBALS['WE_RESAVE']) ) ? we_class::LOAD_MAID_DB : we_class::LOAD_TEMP_DB);
} else if(isset($we_dt)){
	$we_doc->we_initSessDat($we_dt);

//	in some templates we must disable some EDIT_PAGES and disable some buttons
	$we_doc->executeDocumentControlElements();
} else {
	$we_doc->ContentType = $we_ContentType;
	$we_doc->Table = (isset($we_Table) && $we_Table) ? $we_Table : FILE_TABLE;
	$we_doc->we_new();
}

if(!isset($dontMakeGlobal)){
//FIXME: remove this clone => where do we need this?!
	$GLOBALS['we_doc'] = clone($we_doc);
}

//if document opens get initial object for versioning if no versions exist
if(in_array(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0), array('load_edit_footer', 'switch_edit_page'))){
	$version = new we_versions_version();
	$version->setInitialDocObject($GLOBALS['we_doc']);
}