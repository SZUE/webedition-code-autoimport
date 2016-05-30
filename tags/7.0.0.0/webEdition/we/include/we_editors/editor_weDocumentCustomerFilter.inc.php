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
we_html_tools::protect();
$parts = array();
$_space_size = 120;


if($we_doc->ClassName != 'we_imageDocument' && permissionhandler::hasPerm('CAN_EDIT_CUSTOMERFILTER')){
	$_filter = $we_doc->documentCustomerFilter;
	if(!$_filter){
		$_filter = we_customer_documentFilter::getEmptyDocumentCustomerFilter();
	}
	$_view = new we_customer_documentFilterView($_filter, 'WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);', 520);

	$parts[] = array(
		'headline' => g_l('modules_customerFilter', '[customerFilter]'),
		'html' => $_view->getFilterHTML(),
		'space' => $_space_size
	);
}


$parts[] = array(
	'headline' => g_l('modules_customer', '[one_customer]'),
	'html' => formWebuser(permissionhandler::hasPerm("CAN_CHANGE_DOCS_CUSTOMER"), 434),
	'space' => $_space_size
);



echo we_html_tools::getHtmlTop() .
 STYLESHEET .
 weSuggest::getYuiFiles();
require(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
echo we_html_element::jsScript(JS_DIR . 'utils/multi_edit.js') .
 '</head><body class="weEditorBody"><form name="we_form" onsubmit="return false">' .
 we_class::hiddenTrans() .
 (!($we_doc instanceof we_imageDocument) && permissionhandler::hasPerm('CAN_EDIT_CUSTOMERFILTER') ?
	we_html_tools::hidden('we_edit_weDocumentCustomerFilter', 1) : '') .
 we_html_multiIconBox::getHTML('weDocProp', $parts, 20, '', -1, g_l('weClass', '[moreProps]'), g_l('weClass', '[lessProps]')) .
 we_html_element::htmlHidden("we_complete_request", 1) . '</form>
</body>
</html>';

function formWebuser($canChange, $width = 388){
	if(!$GLOBALS['we_doc']->WebUserID){
		$GLOBALS['we_doc']->WebUserID = 0;
	}
	$webuser = ""; //g_l('weClass',"[nobody]");

	if($GLOBALS['we_doc']->WebUserID != 0){
		$webuser = id_to_path($GLOBALS['we_doc']->WebUserID, CUSTOMER_TABLE);
		if(!$webuser){
			$webuser = ""; //g_l('weClass',"[nobody]");
		}
	}

	if(!$canChange){
		return $webuser;
	}

	$textname = 'wetmp_' . $GLOBALS['we_doc']->Name . '_WebUserID';
	$idname = 'we_' . $GLOBALS['we_doc']->Name . '_WebUserID';

	//$attribs = ' readonly';
	//$inputFeld=we_html_tools::htmlTextInput($textname,24,$webuser,"",$attribs,"",$width);
	//$idfield = $GLOBALS['we_doc']->htmlHidden($idname,$GLOBALS['we_doc']->WebUserID);

	$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_customer_selector',document.we_form.elements['" . $idname . "'].value,'" . CUSTOMER_TABLE . "','document.we_form.elements[\\'" . $idname . "\\'].value','document.we_form.elements[\\'" . $textname . "\\'].value')");

	$_trashBut = we_html_button::create_button(we_html_button::TRASH, "javascript:document.we_form.elements['" . $idname . "'].value=0;document.we_form.elements['" . $textname . "'].value='';WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);");
	/*
	  $out = we_html_tools::htmlFormElementTable($inputFeld,
	  g_l('modules_customer','[connected_with_customer]'),
	  "left",
	  "defaultfont",
	  $idfield,
	  $button,$_trashBut);
	 */
	$yuiSuggest = & weSuggest::getInstance();
	$yuiSuggest->setAcId("Customer");
	$yuiSuggest->setContentType("");
	$yuiSuggest->setInput($textname, $webuser, '', '', 1);
	$yuiSuggest->setLabel(g_l('modules_customer', '[connected_with_customer]'));
	$yuiSuggest->setMaxResults(20);
	$yuiSuggest->setMayBeEmpty(true);
	$yuiSuggest->setResult($idname, $GLOBALS['we_doc']->WebUserID);
	$yuiSuggest->setSelector(weSuggest::DocSelector);
	$yuiSuggest->setWidth(434);
	$yuiSuggest->setSelectButton($button);
	$yuiSuggest->setOpenButton(we_html_button::create_button(we_html_button::EDIT, "javascript:top.we_cmd('customer_edit_ifthere', document.we_form.elements['yuiAcResultCustomer'].value);"));
	$yuiSuggest->setTrashButton($_trashBut);
	$yuiSuggest->setTable(CUSTOMER_TABLE);

	return weSuggest::getYuiFiles() . $yuiSuggest->getHTML() . $yuiSuggest->getYuiJs();
}
