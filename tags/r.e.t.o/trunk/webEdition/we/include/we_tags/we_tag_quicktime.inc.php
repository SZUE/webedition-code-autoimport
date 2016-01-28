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
function we_tag_quicktime($attribs){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}


	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);
	$showcontrol = weTag_getAttribute('showcontrol', $attribs, true, we_base_request::BOOL);
	$showvideo = weTag_getAttribute('showquicktime', $attribs, true, we_base_request::BOOL);
	$lid = weTag_getAttribute("id", $attribs, 0, we_base_request::INT);

	$id = $GLOBALS['we_doc']->getElement($name, 'bdid')? : $lid;
	if(isset($attribs['showcontrol']) && !$showcontrol && $lid){//bug 6433: später wird so ohne weiteres gar nicht mehr auf die id zurückgegriffen
		$attribs['id'] = $lid; //siehe korrespondierende Änderung in we:document::getField
		$attribs['showcontrol'] = $showcontrol; //sicherstellen das es boolean iost
	}
	$fname = 'we_' . $GLOBALS['we_doc']->Name . '_img[' . $name . '#bdid]';

	$out = ($GLOBALS['we_editmode'] && !$showvideo ?
			'' :
			$GLOBALS['we_doc']->getField($attribs, "quicktime"));

	if($showcontrol && $GLOBALS['we_editmode']){
		$startid = weTag_getAttribute('startid', $attribs, 0, we_base_request::INT);
		$parentid = weTag_getAttribute('parentid', $attribs, 0, we_base_request::INT);
		// Create "Edit" button
		$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['" . $fname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("opener.setScrollTo(); opener._EditorFrame.setEditorIsHot(true); opener.top.we_cmd('reload_editpage'); opener._EditorFrame.setEditorIsHot(true);");

		$button = we_html_button::create_button('image:btn_edit_quicktime', "javascript:we_cmd('openDocselector','" . ($id ? : $startid) . "', '" . FILE_TABLE . "','" . $wecmdenc1 . "','','" . $wecmdenc3 . "',''," . $parentid . ", '" . we_base_ContentTypes::QUICKTIME . "', " . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")", true);
		// Create "Delete/Clear" button
		$clear_button = we_html_button::create_button('image:btn_function_trash', "javascript:we_cmd('remove_image', '" . $name . "')", true);

		return '
<table class="weEditTable spacing2 padding0">
	<tr>
		<td class="weEditmodeStyle">' . $out . '<input type="hidden" name="' . $fname . '" value="' . $GLOBALS['we_doc']->getElement($name, "bdid") . '"/></td>
	</tr>
	<tr>
		<td class="weEditmodeStyle" align="center">' .
			we_html_button::create_button_table(array(
				$button, $clear_button
				), 5) . '</td></tr></table>';
	}
	//	When in SEEM - Mode add edit-Button to tag - textarea
	return $out;
}
