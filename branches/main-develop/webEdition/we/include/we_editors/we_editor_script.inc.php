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
if(isset($GLOBALS['we_doc'])){
	if($GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT && $GLOBALS['we_doc']->ContentType == we_base_ContentTypes::TEMPLATE){
		//no wysiwyg
	} else {
		echo we_wysiwyg_editor::getHeaderHTML();
	}
}
$hasGD = isset($GLOBALS['we_doc']) && $GLOBALS['we_doc']->ContentType == we_base_ContentTypes::IMAGE && $GLOBALS['we_doc']->gd_support();

$js = '';

if(isset($GLOBALS['we_doc'])){
	if(!empty($GLOBALS['we_doc']->ApplyWeDocumentCustomerFiltersToChilds) && $GLOBALS['we_doc']->ParentID){
		$js.="top.we_cmd('copyWeDocumentCustomerFilter', '" . $GLOBALS['we_doc']->ID . "', '" . $GLOBALS['we_doc']->Table . "', '" . $GLOBALS['we_doc']->ParentID . "');";
	}

	$useSeeModeJS = array(
		we_base_ContentTypes::WEDOCUMENT => array(we_base_constants::WE_EDITPAGE_CONTENT, we_base_constants::WE_EDITPAGE_PREVIEW),
		we_base_ContentTypes::TEMPLATE => array(we_base_constants::WE_EDITPAGE_PREVIEW, we_base_constants::WE_EDITPAGE_PREVIEW_TEMPLATE),
		we_base_ContentTypes::OBJECT_FILE => array(we_base_constants::WE_EDITPAGE_CONTENT, we_base_constants::WE_EDITPAGE_PREVIEW)
	);

	if(isset($useSeeModeJS[$GLOBALS['we_doc']->ContentType]) && in_array($GLOBALS['we_doc']->EditPageNr, $useSeeModeJS[$GLOBALS['we_doc']->ContentType])){
		$js.='
// add event-Handler, replace links after load
	window.addEventListener("load", seeMode_dealWithLinks, false);
';
	}
}

// Dreamweaver RPC Command ShowPreparedPreview
// disable javascript errors
if(we_base_request::_(we_base_request::STRING, 'cmd') === 'ShowPreparedPreview'){

	$js.='
// overwrite/disable some functions in javascript!!!!
window.open = function(){};
window.onerror = function () {
	return true;
}

window.addEventListener("load", we_rpc_dw_onload);
';
}
?>
<script><!--
	var we_transaction = "<?= we_base_request::_(we_base_request::TRANSACTION, "we_transaction", 0); ?>";
	var _oldparentid = <?= isset($GLOBALS['we_doc']) ? intval($GLOBALS['we_doc']->ParentID) : 0; ?>;
	var docName = "<?= isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Name : ''; ?>";
	var isFolder = <?= isset($GLOBALS['we_doc']) ? intval($GLOBALS['we_doc']->IsFolder) : 0; ?>;
	var docTable = "<?= isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Table : ''; ?>";
	var docClass = "<?= isset($GLOBALS['we_doc']) ? get_class($GLOBALS['we_doc']) : ''; ?>";
	var hasCustomerFilter =<?= intval(isset($GLOBALS['we_doc']) && defined('CUSTOMER_TABLE') && in_array(we_base_constants::WE_EDITPAGE_WEBUSER, $GLOBALS['we_doc']->EditPageNrs) && isset($GLOBALS['we_doc']->documentCustomerFilter)); ?>;
	var hasGlossary =<?= intval(defined('GLOSSARY_TABLE') && isset($GLOBALS['we_doc']) && ($GLOBALS['we_doc']->ContentType == we_base_ContentTypes::WEDOCUMENT || $GLOBALS['we_doc']->ContentType === we_base_ContentTypes::OBJECT_FILE)); ?>;
	var gdType = "<?= $hasGD && isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->getGDType() : ''; ?>";
	var gdSupport = <?= intval($hasGD && isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->gd_support() : 0); ?>;
	var isWEObject =<?= intval(isset($GLOBALS['we_doc']) && ($GLOBALS['we_doc']->ContentType === we_base_ContentTypes::OBJECT/* FIXME: only supported for type object || $GLOBALS['we_doc']->ContentType === we_base_ContentTypes::OBJECT_FILE */)); ?>;
	var WE_EDIT_IMAGE =<?= intval(defined('WE_EDIT_IMAGE')); ?>;
	//-->
</script><?= we_html_element::cssLink(CSS_DIR . 'editor.css') .
 we_html_element::jsScript(JS_DIR . 'we_textarea.js') .
 we_html_element::jsScript(JS_DIR . 'we_editor_script.js') .
 ($js ? we_html_element::jsElement($js) : '');
