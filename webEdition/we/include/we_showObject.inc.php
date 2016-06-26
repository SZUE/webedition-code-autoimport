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
	$previewMode = 1;
	require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_contentobjectFile.inc.php');
	exit;
}

$userID = (isset($GLOBALS['we_doc']) && $GLOBALS['we_doc']->InWebEdition ? $GLOBALS['we_doc']->isLockedByUser() : 0);

if(($userID && $userID != $_SESSION['user']['ID']) || (we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === 'switch_edit_page' || (isset($_SESSION['weS']['EditPageNr']) && $_SESSION['weS']['EditPageNr'] == we_base_constants::WE_EDITPAGE_PREVIEW))){ //	Preview-Mode of Tabs
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
		$lastDoc = isset($_SESSION['weS']['last_webEdition_document']) ? $_SESSION['weS']['last_webEdition_document'] : [];
		if(isset($lastDoc['Path'])){
			if($workspaces){ // get the correct template
				//	Select a matching workspace.
				foreach($workspaces as $workspace){
					$workspace = id_to_path($workspace, FILE_TABLE, $tmpDB);

					if($workspace && strpos($lastDoc['Path'], $workspace) === 0 && $tids){
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
	$we_doc = we_document::initDoc('', $we_dt);
} else { //	view with template
	$tid = we_base_request::_(we_base_request::INT, 'we_cmd', (isset($we_objectTID) ? $we_objectTID : 0), 2);

	if(($oid = we_base_request::_(we_base_request::INT, 'we_objectID', 0))){
		$GLOBALS['we_obj'] = new we_objectFile();
		$GLOBALS['we_obj']->initByID($oid, OBJECT_FILES_TABLE);
		$GLOBALS['we_obj']->setTitleAndDescription();
	}

	if(!$oid || !$GLOBALS['we_obj']->Published){
		we_html_tools::setHttpCode(404);

		if(ERROR_DOCUMENT_NO_OBJECTFILE && ($path = id_to_path(ERROR_DOCUMENT_NO_OBJECTFILE, FILE_TABLE))){
			//if set, we show object again!
			unset($_REQUEST);
			include($_SERVER['DOCUMENT_ROOT'] . $path);
			exit();
		}
		echo 'Sorry, we are unable to locate your requested Page.';
		exit();
	}

	$GLOBALS['we_doc'] = new we_webEditionDocument();
	$GLOBALS['we_doc']->initByID(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1), FILE_TABLE);
	$GLOBALS['we_doc']->initByObj($GLOBALS['we_obj']);
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

	if(($visitorHasAccess = $GLOBALS['we_obj']->documentCustomerFilter->accessForVisitor($GLOBALS['we_obj']->ID, $GLOBALS['we_obj']->ContentType))){
		switch($visitorHasAccess){
			case we_customer_documentFilter::ACCESS:
			case we_customer_documentFilter::CONTROLONTEMPLATE:
				break;
			default:
				// user has NO ACCESS => show errordocument
				$errorDocId = $GLOBALS['we_obj']->documentCustomerFilter->getErrorDoc($visitorHasAccess);

				if($errorDocId && ($errorDocPath = id_to_path($errorDocId, FILE_TABLE))){ // use given document instead !
					we_html_tools::setHttpCode(401);
					//if REQUEST is set, we show object again!
					unset($errorDocId, $_REQUEST);
					include($_SERVER['DOCUMENT_ROOT'] . $errorDocPath);
					unset($errorDocPath);
					return;
				}
				die('Customer has no access to this document');
		}
	}
}

$pid = (empty($pid) ? f('SELECT ParentID FROM ' . FILE_TABLE . ' WHERE Path="' . $DB_WE->escape($_SERVER['SCRIPT_NAME']) . '"') : $pid);
$tid = (empty($tid) ? $GLOBALS['we_obj']->getTemplateID($pid) : $tid);
$tmplPath = $tid ? preg_replace('/.tmpl$/i', '.php', f('SELECT Path FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . intval($tid))) : '';

if(!$tid || !$tmplPath || !is_readable(TEMPLATES_PATH . $tmplPath)){
	we_html_tools::setHttpCode(SUPPRESS404CODE ? 200 : 404);

	if(ERROR_DOCUMENT_NO_OBJECTFILE && ($path = id_to_path(ERROR_DOCUMENT_NO_OBJECTFILE, FILE_TABLE))){
		//if set, we show object again!
		unset($_REQUEST);
		include($_SERVER['DOCUMENT_ROOT'] . $path);
		exit();
	}
	echo 'Sorry, we are unable to locate your requested Page.';
	exit();
}


if((!defined('WE_CONTENT_TYPE_SET')) && !empty($GLOBALS['we_doc']->Charset)){ //	send charset which might be determined in template
	define('WE_CONTENT_TYPE_SET', 1);
	we_html_tools::headerCtCharset('text/html', $GLOBALS['we_doc']->Charset);
}

//	If in webEdition, parse the document !!!!
if(!empty($_SESSION['weS']['we_data'][$we_transaction]['0']['InWebEdition'])){ //	In webEdition, parse the file.
	ob_start();
	include(TEMPLATES_PATH . $tmplPath);

	$contents = ob_get_clean();

	echo we_SEEM::parseDocument($contents);
	return;
} //	Not in webEdition, just show the file.
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
	$content = ob_get_clean();
	if($useGlossary){
		$content = we_glossary_replace::replace($content, $GLOBALS['we_doc']->Language);
	}
	if($urlReplace){
		$content = preg_replace($urlReplace, array_keys($urlReplace), $content);
	}

	echo $content;
}
