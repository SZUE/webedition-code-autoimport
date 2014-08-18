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
if(str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) == str_replace(dirname(__FILE__), '', __FILE__)){
	exit();
}

if(!defined('NO_SESS')){
	define('NO_SESS', 1);
}


//leave this
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

/**
 * showContent()
 * @desc     Generates and prints a default template to see the attribs of an object.
 *
 * @param    msg		string
 */
function showContent(){
	$_previewMode = 1;
	require_once(WE_OBJECT_MODULE_PATH . 'we_editor_contentobjectFile.inc.php');
	exit;
}

$_userID = (isset($GLOBALS['we_doc']) && $GLOBALS['we_doc']->InWebEdition ? $GLOBALS['we_doc']->isLockedByUser() : 0);

if(($_userID && $_userID != $_SESSION['user']['ID']) || (we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) == 'switch_edit_page' || (isset($_SESSION['weS']['EditPageNr']) && $_SESSION['weS']['EditPageNr'] == we_base_constants::WE_EDITPAGE_PREVIEW))){ //	Preview-Mode of Tabs
	//	We must choose the right template to show the object.
	//	Therefore we must look, if $_SESSION['weS']['SEEM']['lastPath'] exists to check the workspace.
	//	First check the workspaces for the document.
	//	choose the template matching to the actual workspace.
	//	If wrong workspace or no template can be found, just show the name/value pairs.
	// init document
	if(!we_base_request::_(we_base_request::TRANSACTION, 'we_transaction')){
		exit();
	}

	$we_dt = $_SESSION['weS']['we_data'][$we_transaction];

	if(isset($_SESSION['weS']['we_data'][$we_transaction][0]['Templates'])){
		$tids = makeArrayFromCSV($_SESSION['weS']['we_data'][$we_transaction][0]['Templates']); //	get all templateIds.
		$workspaces = makeArrayFromCSV($_SESSION['weS']['we_data'][$we_transaction][0]['Workspaces']);
		if($_SESSION['weS']['we_data'][$we_transaction][0]['ExtraWorkspaces']){
			$workspaces[] = $_SESSION['weS']['we_data'][$we_transaction][0]['ExtraWorkspaces'];
		}
		if($_SESSION['weS']['we_data'][$we_transaction][0]['ExtraTemplates']){
			$tids[] = $_SESSION['weS']['we_data'][$we_transaction][0]['ExtraTemplates'];
		}

		$tmpDB = new DB_WE();

		//	determine Path from last opened wE-Document
		$_lastDoc = isset($_SESSION['weS']['last_webEdition_document']) ? $_SESSION['weS']['last_webEdition_document'] : array();
		if(isset($_lastDoc['Path'])){
			if($workspaces){ // get the correct template
				//	Select a matching workspace.
				foreach($workspaces as $workspace){
					$workspace = id_to_path($workspace, FILE_TABLE, $tmpDB);

					if($workspace && strpos($_lastDoc['Path'], $workspace) === 0 && $tids){
						//	init document
						$tid = $tids[0];
						/* 						$GLOBALS['we_doc']->we_initSessDat($we_dt);
						  $_REQUEST['we_objectID'] = $_SESSION['weS']['we_data'][$we_transaction][0]['ID']; */
						break;
					}
				}
			}
		}
		if(!isset($tid)){
			foreach($tids as $ltid){
				$path = id_to_path($ltid, TEMPLATES_TABLE, $tmpDB);
				if($path && $path != '/'){
					$tid = $ltid;
					break;
				}
			}
		}
		unset($tmpDB);
	}

	if(isset($tid)){
		//	init document
		$GLOBALS['we_doc']->we_initSessDat($we_dt);
		$_REQUEST['we_objectID'] = $_SESSION['weS']['we_data'][$we_transaction][0]['ID'];
	} else {
		showContent();
		exit;
	}
} else if(($we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', '', 3))){

	$tid = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2);
	// init document
	$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
	include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');
} else { //	view with template
	$tid = we_base_request::_(we_base_request::INT, 'we_cmd', (isset($we_objectTID) ? $we_objectTID : 0), 2);

	$GLOBALS['we_obj'] = new we_objectFile();
	$GLOBALS['we_obj']->initByID(we_base_request::_(we_base_request::INT, 'we_objectID', 0), OBJECT_FILES_TABLE);
	$GLOBALS['we_obj']->setTitleAndDescription();

	if(!$GLOBALS['we_obj']->Published){
		we_html_tools::setHttpCode(404);

		$path = id_to_path(ERROR_DOCUMENT_NO_OBJECTFILE, FILE_TABLE);
		if($path){
			header('Location: ' . $path);
		}
		exit;
	}

	$GLOBALS['we_doc'] = new we_webEditionDocument();
	$GLOBALS['we_doc']->initByID(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1), FILE_TABLE);
	$GLOBALS['we_doc']->elements = $GLOBALS['we_obj']->elements;
	$GLOBALS['we_doc']->Templates = $GLOBALS['we_obj']->Templates;
	$GLOBALS['we_doc']->ExtraTemplates = $GLOBALS['we_obj']->ExtraTemplates;
	$GLOBALS['we_doc']->TableID = $GLOBALS['we_obj']->TableID;
	$GLOBALS['we_doc']->CreatorID = $GLOBALS['we_obj']->CreatorID;
	$GLOBALS['we_doc']->ModifierID = $GLOBALS['we_obj']->ModifierID;
	$GLOBALS['we_doc']->RestrictOwners = $GLOBALS['we_obj']->RestrictOwners;
	$GLOBALS['we_doc']->Owners = $GLOBALS['we_obj']->Owners;
	$GLOBALS['we_doc']->OwnersReadOnly = $GLOBALS['we_obj']->OwnersReadOnly;
	$GLOBALS['we_doc']->Category = $GLOBALS['we_obj']->Category;
	$GLOBALS['we_doc']->OF_ID = $GLOBALS['we_obj']->ID;
	$GLOBALS['we_doc']->Charset = $GLOBALS['we_obj']->Charset;
	$GLOBALS['we_doc']->Language = $GLOBALS['we_obj']->Language;
	$GLOBALS['we_doc']->Url = $GLOBALS['we_obj']->Url;
	$GLOBALS['we_doc']->TriggerID = $GLOBALS['we_obj']->TriggerID;
	$GLOBALS['we_doc']->elements['Charset']['dat'] = $GLOBALS['we_obj']->Charset; // for charset-tag
	$GLOBALS['TITLE'] = $GLOBALS['we_doc']->getElement('Title');
	$GLOBALS['KEYWORDS'] = $GLOBALS['we_doc']->getElement('Keywords');
	$GLOBALS['DESCRIPTION'] = $GLOBALS['we_doc']->getElement('Description');
}

// deal with customerFilter
// @see we_showDocument.inc.php
if(isset($GLOBALS['we_obj']) && $GLOBALS['we_obj']->documentCustomerFilter && !isset($GLOBALS['getDocContentVersioning'])){

	// call session_start to init session, otherwise NO customer can exist
	if(!isset($_SESSION)){
		new we_base_sessionHandler();
	}

	if(($_visitorHasAccess = $GLOBALS['we_obj']->documentCustomerFilter->accessForVisitor($GLOBALS['we_obj']->ID, $GLOBALS['we_obj']->ContentType))){

		if(!($_visitorHasAccess == we_customer_documentFilter::ACCESS || $_visitorHasAccess == we_customer_documentFilter::CONTROLONTEMPLATE)){

			// user has NO ACCESS => show errordocument
			$_errorDocId = $GLOBALS['we_obj']->documentCustomerFilter->getErrorDoc($_visitorHasAccess);

			if(($_errorDocPath = id_to_path($_errorDocId, FILE_TABLE))){ // use given document instead !
				if($_errorDocId){
					unset($_errorDocId);
					include($_SERVER['DOCUMENT_ROOT'] . $_errorDocPath);
					unset($_errorDocPath);
				}
				return;
			} else {
				die('Customer has no access to this document');
			}
		}
	}
}

if(!isset($pid) || !($pid)){
	$pid = f('SELECT ParentID FROM ' . FILE_TABLE . ' WHERE Path="' . $DB_WE->escape($_SERVER['SCRIPT_NAME']) . '"');
}

if(!isset($tid) || !($tid)){
	$tid = $GLOBALS['we_obj']->getTemplateID($pid);
}

if(!$tid){
	$tids = makeArrayFromCSV(f('SELECT Templates FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($GLOBALS['we_obj']->TableID)));
	if($tids){
		$tid = $tids[0];
	}
}

if(!$tid){
	we_html_tools::setHttpCode(404);
	if(($path = id_to_path(ERROR_DOCUMENT_NO_OBJECTFILE, FILE_TABLE))){
		header('Location: ' . $path);
	}
	exit;
}

$tmplPath = preg_replace('/.tmpl$/i', '.php', f('SELECT Path FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . intval($tid)));

if((!defined('WE_CONTENT_TYPE_SET')) && isset($GLOBALS['we_doc']->Charset) && $GLOBALS['we_doc']->Charset){ //	send charset which might be determined in template
	define('WE_CONTENT_TYPE_SET', 1);
	//	@ -> to aware of unproper use of this element, f. ex in include-File
	we_html_tools::headerCtCharset('text/html', $GLOBALS['we_doc']->Charset);
}

//	If in webEdition, parse the document !!!!
if(isset($_SESSION['weS']['we_data'][$we_transaction]['0']['InWebEdition']) && $_SESSION['weS']['we_data'][$we_transaction]['0']['InWebEdition']){ //	In webEdition, parse the file.
	$contentOrig = implode('', file(TEMPLATES_PATH . $tmplPath));

	ob_start();
	//FIXME:eval
	eval('?>' . $contentOrig);
	$contents = ob_get_contents();
	ob_end_clean();
	print we_SEEM::parseDocument($contents);
} else { //	Not in webEdition, just show the file.
	//
		// --> Start Glossary Replacement
	//
		$urlReplace = we_folder::getUrlReplacements($GLOBALS['DB_WE']);
// --> Glossary Replacement
	$useGlossary = ((defined('GLOSSARY_TABLE') && (!isset($GLOBALS['WE_MAIN_DOC']) || $GLOBALS['WE_MAIN_ID'] == $GLOBALS['we_doc']->ID)) && (isset($we_doc->InGlossar) && $we_doc->InGlossar == 0) && we_glossary_replace::useAutomatic());
	$useBuffer = !empty($urlReplace) || $useGlossary;
	if($useBuffer){
		ob_start();
	}
	include(TEMPLATES_PATH . $tmplPath);
	if($useBuffer){
		$content = ob_get_contents();
		ob_end_clean();
		if($useGlossary){
			$content = we_glossary_replace::replace($content, $GLOBALS['we_doc']->Language);
		}
		if($urlReplace){
			$content = preg_replace($urlReplace, array_keys($urlReplace), $content);
		}

		echo $content;
	}
}