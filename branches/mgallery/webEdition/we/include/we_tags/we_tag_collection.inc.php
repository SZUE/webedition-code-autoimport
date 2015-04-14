<?php

/**
 * webEdition CMS
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

function we_parse_tag_collection($attribs, $content){
	return '<?php global $lv;' . we_tag_tagParser::printTag('collection', $attribs) . ';?>' . $content . '<?php we_post_tag_listview();?>';
}

function we_tag_collection($attribs, $content){
	$id = weTag_getAttribute('id', $attribs, 0, we_base_request::INT);
	$name = weTag_getAttribute('name', $attribs, 0, we_base_request::STRING);

	if(!$id && !$name && ($foo = attributFehltError($attribs, array('name', 'id'), __FUNCTION__))){
		return $foo;
	}
	$intID = $GLOBALS['we_doc']->getElement($name . '_collection', 'bdid');
	$rootdirid = 0;

	if($GLOBALS['we_editmode']){
		$rootdir = '/';
		$intID_elem_Name = 'we_' . $GLOBALS['we_doc']->Name . '_href[' . $nintID . '#bdid]';

		$trashbut = we_html_button::create_button('image:btn_function_trash', "javascript:document.we_form.elements['" . $intID_elem_Name . "'].value = ''; document.we_form.elements['" . $intPath_elem_Name . "'].value = ''; _EditorFrame.setEditorIsHot(true);", true);
		$span = '<span style="color: black;font-size:' . ((we_base_browserDetect::isMAC()) ? "11px" : ((we_base_browserDetect::isUNIX()) ? "13px" : "12px")) . ';font-family:' . g_l('css', '[font_family]') . ';">';

		$size = 5 * intval(weTag_getAttribute('size', $attribs, 20, we_base_request::INT));

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $intID_elem_Name . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $intPath_elem_Name . "'].value");
		$wecmdenc3 = we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true);");

		$but = we_html_button::create_button(we_html_button::WE_IMAGE_BUTTON_IDENTIFY . 'edit_link', "javascript:we_cmd('openDocselector', document.forms[0].elements['" . $intID_elem_Name . "'].value, '" . VFILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','', '" . $rootdirid . "', '', " . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ",'', 0);");

		$open = we_html_button::create_button(we_html_button::WE_IMAGE_BUTTON_IDENTIFY . 'function_view', "javascript:if(document.forms[0].elements['" . $intID_elem_Name . "'].value){top.weEditorFrameController.openDocument('" . VFILE_TABLE . "', document.forms[0].elements['" . $intID_elem_Name . "'].value,'');}");

		$yuiSuggest = &weSuggest::getInstance();
		$yuiSuggest->setAcId($name . we_base_file::getUniqueId(), $rootdir);
		$yuiSuggest->setContentType(implode(',', array(we_base_ContentTypes::FOLDER, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML, we_base_ContentTypes::JS, we_base_ContentTypes::CSS, we_base_ContentTypes::APPLICATION, we_base_ContentTypes::QUICKTIME)));
		$yuiSuggest->setInput($intPath_elem_Name, $intPath);
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setResult($intID_elem_Name, $intID);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setTable(FILE_TABLE);
		$yuiSuggest->setWidth($size);



		echo
			'<table class="weEditTable padding0 spacing2">
<tr>
	<td class="weEditmodeStyle">' . 'x'/* g_l('','') */ . '</td>
	<td class="weEditmodeStyle" style="width:' . ($size + 20) . 'px">' . /*$yuiSuggest->getHTML() .*/ '</td>
	<td class="weEditmodeStyle">' . $but . '</td>
	<td class="weEditmodeStyle">' . $open . '</td>
	<td class="weEditmodeStyle">' . $trashbut . '</td>
	</tr></table>';
		return;
		//FIXME: add sth. to the stack, we need an extra element in editmode
	}

	$GLOBALS['lv'] = new we_listview_collection();
	if(!isset($GLOBALS['we_lv_array']) || !is_array($GLOBALS['we_lv_array'])){
		$GLOBALS['we_lv_array'] = array();
	}

	$GLOBALS['we_lv_array'][] = clone($GLOBALS['lv']);
}
