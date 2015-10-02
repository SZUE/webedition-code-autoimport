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
		//no wyswyg
	} else {
		echo we_wysiwyg_editor::getHeaderHTML();
	}
}
$hasGD = isset($GLOBALS['we_doc']) && $GLOBALS['we_doc']->ContentType == we_base_ContentTypes::IMAGE && $GLOBALS['we_doc']->gd_support();
echo we_html_element::cssLink(CSS_DIR . 'editor.css') .
 we_html_element::cssElement('.weEditTable{
	font-size: ' . ((we_base_browserDetect::isMAC()) ? '11' : ((we_base_browserDetect::isUNIX()) ? '13' : '12')) . 'px;
	font-family: ' . g_l('css', '[font_family]') . ';
}
');
we_html_element::jsScript(JS_DIR . 'global.js');
?>
<script><!--
	var we_transaction = "<?php echo we_base_request::_(we_base_request::TRANSACTION, "we_transaction", 0); ?>";
	var _oldparentid = <?php echo isset($GLOBALS['we_doc']) ? intval($GLOBALS['we_doc']->ParentID) : 0; ?>;
	var docName = "<?php echo isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Name : ''; ?>";
	var docTable = "<?php echo isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Table : ''; ?>";
	var docClass = "<?php echo isset($GLOBALS['we_doc']) ? get_class($GLOBALS['we_doc']) : ''; ?>";
	var hasCustomerFilter =<?php echo intval(isset($GLOBALS['we_doc']) && defined('CUSTOMER_TABLE') && in_array(we_base_constants::WE_EDITPAGE_WEBUSER, $GLOBALS['we_doc']->EditPageNrs) && isset($GLOBALS['we_doc']->documentCustomerFilter)); ?>;
	var hasGlossary =<?php echo intval(defined('GLOSSARY_TABLE') && isset($GLOBALS['we_doc']) && ($GLOBALS['we_doc']->ContentType == we_base_ContentTypes::WEDOCUMENT || $GLOBALS['we_doc']->ContentType === we_base_ContentTypes::OBJECT_FILE)); ?>;

	var hasGD =<?php echo intval($hasGD); ?>;
	var gdType = "<?php echo $hasGD && isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->getGDType() : ''; ?>";
	var canRotate =<?php echo intval(function_exists("ImageRotate")); ?>;
	var gdSupport =<?php echo intval($hasGD ? $we_doc->gd_support() : 0); ?>;
	var winSelectSize = {
		'docSelect': {
			'width': <?php echo we_selector_file::WINDOW_DOCSELECTOR_WIDTH; ?>,
			'height': <?php echo we_selector_file::WINDOW_DOCSELECTOR_HEIGHT; ?>
		},
		'catSelect': {
			'width': <?php echo we_selector_file::WINDOW_CATSELECTOR_WIDTH; ?>,
			'height': <?php echo we_selector_file::WINDOW_CATSELECTOR_HEIGHT; ?>
		}
	};

	var g_l = {
		'confirm_applyFilter': "<?php echo g_l('alert', ($GLOBALS['we_doc']->IsFolder ? '[confirm][applyWeDocumentCustomerFiltersFolder]' : '[confirm][applyWeDocumentCustomerFiltersDocument]')) ?>",
		'confirm_navDel': "<?php echo g_l('navigation', '[del_question]'); ?>",
		'gdTypeNotSupported': "<?php echo $hasGD ? we_message_reporting::prepareMsgForJS(sprintf(g_l('weClass', '[type_not_supported_hint]'), g_l('weClass', '[convert_' . $we_doc->getGDType() . ']'))) : ''; ?>",
		'noRotate': "<?php $hasGD && function_exists("ImageRotate") ? we_message_reporting::prepareMsgForJS(g_l('weClass', '[rotate_hint]')) : ''; ?>",
		'field_int_value_to_height': "<?php echo g_l('alert', '[field_int_value_to_height]'); ?>",
		'field_contains_incorrect_chars': '<?php echo g_l('alert', '[field_contains_incorrect_chars]'); ?>',
		'field_input_contains_incorrect_length': '<?php echo g_l('alert', '[field_input_contains_incorrect_length]'); ?>',
		'field_int_contains_incorrect_length': '<?php echo g_l('alert', '[field_int_contains_incorrect_length]'); ?>',
		'fieldNameNotValid': '<?php echo g_l('modules_object', '[fieldNameNotValid]'); ?>',
		'fieldNameNotTitleDesc': '<?php echo g_l('modules_object', '[fieldNameNotTitleDesc]'); ?>',
		'fieldNameEmpty': '<?php echo g_l('modules_object', '[fieldNameEmpty]'); ?>'
	};

	var isWEObject =<?php echo intval(isset($GLOBALS['we_doc']) && ($GLOBALS['we_doc']->ContentType === we_base_ContentTypes::OBJECT/* FIXME: only supported for type object || $GLOBALS['we_doc']->ContentType === we_base_ContentTypes::OBJECT_FILE */)); ?>;
	var WE_EDIT_IMAGE =<?php echo intval(defined('WE_EDIT_IMAGE')); ?>;
	var WE_SPELLCHECKER_MODULE_DIR = "<?php echo defined('SPELLCHECKER') ? WE_SPELLCHECKER_MODULE_DIR : ''; ?>";
	var TEMPLATES_TABLE = "<?php echo TEMPLATES_TABLE ?>";
	var CTYPE_TEMPLATE = "<?php echo we_base_ContentTypes::TEMPLATE; ?>";
	var linkPrefix = {
		'TYPE_OBJ_PREFIX': '<?php echo we_base_link::TYPE_OBJ_PREFIX; ?>',
		'TYPE_INT_PREFIX': '<?php echo we_base_link::TYPE_INT_PREFIX; ?>',
		'TYPE_MAIL_PREFIX': '<?php echo we_base_link::TYPE_MAIL_PREFIX; ?>'
	}
	//-->
</script><?php
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
	if (window.addEventListener) {
		window.addEventListener("load", seeMode_dealWithLinks, false);
	} else if (window.attachEvent) {
		window.attachEvent("onload", seeMode_dealWithLinks);
	}
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

if (window.addEventListener) {
	window.addEventListener("load", we_rpc_dw_onload);
} else {
	window.attachEvent("onload", we_rpc_dw_onload);
}
';
}
echo we_html_element::jsScript(JS_DIR . 'we_textarea.js') .
 we_html_element::jsScript(JS_DIR . 'we_editor_script.js') .
 ($js ? we_html_element::jsElement($js) : '');
