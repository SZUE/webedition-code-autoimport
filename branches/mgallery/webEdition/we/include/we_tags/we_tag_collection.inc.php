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

function we_tag_collection($attribs){
	$id = weTag_getAttribute('id', $attribs, 0, we_base_request::INT);
	$name = weTag_getAttribute('name', $attribs, 0, we_base_request::STRING);

	if(!$id && !$name && ($foo = attributFehltError($attribs, array('name', 'id'), __FUNCTION__))){
		return $foo;
	}

	$intID = $GLOBALS['we_doc']->getElement($name, 'bdid')? : $id;
	$rootDirID = 0;

	if($GLOBALS['we_editmode']){
		$path = f('SELECT Path FROM ' . VFILE_TABLE . ' WHERE ID=' . $intID);
		$textname = 'we_' . $GLOBALS['we_doc']->Name . '_collection[' . $name . '_path]';
		$idname = 'we_' . $GLOBALS['we_doc']->Name . '_collection[' . $name . '#bdid]';

		$delbutton = we_html_button::create_button('image:btn_function_trash', "javascript:document.forms[0].elements['" . $idname . "'].value=0;document.forms[0].elements['" . $textname . "'].value='';_EditorFrame.setEditorIsHot(false);we_cmd('reload_editpage');");
		$open = we_html_button::create_button(we_html_button::WE_IMAGE_BUTTON_IDENTIFY . 'function_view', "javascript:if(document.forms[0].elements['" . $idname . "'].value){top.weEditorFrameController.openDocument('" . VFILE_TABLE . "', document.forms[0].elements['" . $idname . "'].value,'');}");
		$cmd1 = "document.we_form.elements['" . $idname . "'].value";
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $textname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("opener.we_cmd('reload_editpage');opener._EditorFrame.setEditorIsHot(true);");


		$button = we_html_button::create_button('select', "javascript:we_cmd('openDocselector'," . $cmd1 . ",'" . VFILE_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "',''," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")"); //FIXME: permissions in collections


		$yuiSuggest = &weSuggest::getInstance();
		$yuiSuggest->setAcId($name . we_base_file::getUniqueId(), f('SELECT Path FROM ' . VFILE_TABLE . ' WHERE ID=' . $rootDirID));
		$yuiSuggest->setContentType(we_base_ContentTypes::COLLECTION);
		$yuiSuggest->setInput($textname, $path);
		$yuiSuggest->setResult($idname, $intID);
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setTable(VFILE_TABLE);
		$yuiSuggest->setWidth(200);
		?>
		<table class="weEditTable padding0 spacing0 border0">
			<tr>
				<td class="weEditmodeStyle" style="padding:0 6px;"><span style="font-weight: bold;"><?php echo weTag_getAttribute('text', $attribs, weTag_getAttribute('_name_orig', $attribs, '', we_base_request::STRING), we_base_request::STRING); ?></span></td>
				<td class="weEditmodeStyle" style="width: <?php echo (200 + 20); ?>px"><?php echo $yuiSuggest->getHTML(); ?></td>
				<td class="weEditmodeStyle"><?php echo $button; ?></td>
				<td class="weEditmodeStyle"><?php echo $open; ?></td>
				<td class="weEditmodeStyle"><?php echo $delbutton; ?></td>
			</tr>
		</table><?php
		return;
		//FIXME: add sth. to the stack, we need an extra element in editmode
	}

	$GLOBALS['lv'] = new we_listview_collection($intID);
	if(!isset($GLOBALS['we_lv_array']) || !is_array($GLOBALS['we_lv_array'])){
		$GLOBALS['we_lv_array'] = array();
	}

	$GLOBALS['we_lv_array'][] = clone($GLOBALS['lv']);
}
