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


//  Diese we_cmds werden auf den Seiten gespeichert und nicht übergeben!!!!!
//  Sie kommen von showDoc.php
$we_ID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
$tmplID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4);
//these come from external!
$we_editmode = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 6);

$we_Table = FILE_TABLE;

$we_dt = isset($_SESSION['weS']['we_data'][$GLOBALS['we_transaction']]) ? $_SESSION['weS']['we_data'][$GLOBALS['we_transaction']] : '';

// init document
include (WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');
$cmd = we_base_request::_(we_base_request::STRING, 'cmd');
if($cmd && $cmd != 'ResetVersion' && $cmd != 'PublishDocs'){
	if(!empty($FROM_WE_SHOW_DOC)){ // when called showDoc.php
		if((!$we_doc->IsDynamic) && (!$tmplID)){ // if the document is not a dynamic php-doc and is published we make a redirect to the static page
			header('Location: ' . ($we_doc->Published ? $we_doc->Path : '/this_file_does_not_exist_on_this_server'));
			exit();
		}
	}
}
if(we_base_request::_(we_base_request::BOOL, 'vers_we_obj')){
	$f = $_SERVER['DOCUMENT_ROOT'] . VERSION_DIR . 'tmpSavedObj.txt';
	$_REQUEST['vers_we_obj'] = false;
	$tempFile = we_base_file::load($f);
	$obj = we_unserialize($tempFile);
	$we_doc = $obj;

// deal with customerFilter
// @see we_showObject.inc.php
} else if($we_doc->documentCustomerFilter && !isset($GLOBALS['getDocContentVersioning'])){

	// call session_start to init session, otherwise NO customer can exist
	if(!isset($_SESSION)){
		new we_base_sessionHandler();
	}

	if(($visitorHasAccess = $we_doc->documentCustomerFilter->accessForVisitor($we_doc->ID, $we_doc->ContentType))){

		if(!($visitorHasAccess == we_customer_documentFilter::ACCESS || $visitorHasAccess == we_customer_documentFilter::CONTROLONTEMPLATE)){
			// user has NO ACCESS => show errordocument
			$errorDocId = $we_doc->documentCustomerFilter->getErrorDoc($visitorHasAccess);
			if(($errorDocPath = id_to_path($errorDocId, FILE_TABLE))){ // use given document instead !
				if($errorDocId){
					we_html_tools::setHttpCode(401);
					unset($errorDocId);
					@include($_SERVER['DOCUMENT_ROOT'] . $errorDocPath);
					unset($errorDocPath);
				}
				return;
			}
			die('Customer has no access to this document');
		}
	}
}

//FIXME: is this relevant at this point?!
$we_doc->EditPageNr = $we_editmode ? we_base_constants::WE_EDITPAGE_CONTENT : we_base_constants::WE_EDITPAGE_PREVIEW;

if($tmplID && ($we_doc->ContentType == we_base_ContentTypes::WEDOCUMENT)){ // if the document should displayed with an other template
	$we_doc->setTemplateID($tmplID);
}

if(($we_include = $we_doc->editor())){
	if(substr(strtolower($we_include), 0, strlen($_SERVER['DOCUMENT_ROOT'])) == strtolower($_SERVER['DOCUMENT_ROOT'])){
		if((!defined('WE_CONTENT_TYPE_SET')) && !empty($we_doc->elements['Charset']['dat'])){ //	send charset which might be determined in template
			define('WE_CONTENT_TYPE_SET', 1);
			we_html_tools::headerCtCharset('text/html', $we_doc->elements['Charset']['dat'], true);
		}

		$urlReplace = we_folder::getUrlReplacements($GLOBALS['DB_WE']);
// --> Glossary Replacement
		$useGlossary = ((defined('GLOSSARY_TABLE') && (!isset($GLOBALS['WE_MAIN_DOC']) || $GLOBALS['WE_MAIN_ID'] == $GLOBALS['we_doc']->ID)) && (isset($we_doc->InGlossar) && $we_doc->InGlossar == 0) && we_glossary_replace::useAutomatic());
		$useBuffer = !empty($urlReplace) || $useGlossary;
		if($useBuffer){
			ob_start();
		}
		include($we_include);
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
		return;
	}
	we_html_tools::protect(); //	only inside webEdition !!!
	include(WE_INCLUDES_PATH . $we_include);
	return;
}
exit('Nothing to include ...');
