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
function we_parse_tag_object($attribs, $content, array $arr){
	$name = weTag_getParserAttribute('name', $arr);
	if($name && strpos($name, ' ') !== false){
		return parseError(sprintf(g_l('parser', '[name_with_space]'), 'object'));
	}

	//dont't check if id or name is set, since it is possible to set object ID by request

	return '<?php global $lv;
		if(' . we_tag_tagParser::printTag('object', $attribs) . '){?>' . $content . '<?php }
		we_post_tag_listview(); ?>';
}

function we_tag_object($attribs){
	if(!defined('WE_OBJECT_MODULE_PATH')){
		print modulFehltError('Object/DB', __FUNCTION__);
		return false;
	}

	$condition = weTag_getAttribute('condition', $attribs, 0);
	$classid = weTag_getAttribute('classid', $attribs);
	$we_oid = weTag_getAttribute('id', $attribs, 0);
	$name = weTag_getAttribute('name', $attribs);
	$size = 5 * intval(weTag_getAttribute('size', $attribs, 30));
	$triggerid = weTag_getAttribute('triggerid', $attribs, 0);
	$searchable = weTag_getAttribute('searchable', $attribs, false, true);
	$hidedirindex = weTag_getAttribute('hidedirindex', $attribs, TAGLINKS_DIRECTORYINDEX_HIDE, true);
	$objectseourls = weTag_getAttribute('objectseourls', $attribs, TAGLINKS_OBJECTSEOURLS, true);

	if(!isset($GLOBALS['we_lv_array'])){
		$GLOBALS['we_lv_array'] = array();
	}

	if($name){
		if(strpos($name, ' ') !== false){
			print parseError(sprintf(g_l('parser', '[name_with_space]'), 'object'));
			return false;
		}

		$we_doc = $GLOBALS['we_doc'];
		//handle listview of documents
		$we_oid = isset($GLOBALS['lv']) && is_object($GLOBALS['lv']) && $GLOBALS['lv']->f($name) ? $GLOBALS['lv']->f($name) : ($we_doc->getElement($name) ? $we_doc->getElement($name) : $we_oid);
		$rootDirID = ($classid ? f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path=(SELECT Path FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($classid) . ')') : 0);

		$path = f('SELECT Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . $we_oid);
		$textname = 'we_' . $we_doc->Name . '_txt[' . $name . '_path]';
		$idname = 'we_' . $we_doc->Name . '_txt[' . $name . ']';
		$table = OBJECT_FILES_TABLE;

		if($GLOBALS['we_editmode']){
			$delbutton = we_html_button::create_button('image:btn_function_trash', "javascript:document.forms[0].elements['$idname'].value=0;document.forms[0].elements['$textname'].value='';_EditorFrame.setEditorIsHot(false);we_cmd('reload_editpage');");
			$open = we_html_button::create_button(we_html_button::WE_IMAGE_BUTTON_IDENTIFY . 'function_view', "javascript:if(document.forms[0].elements['$idname'].value){top.weEditorFrameController.openDocument('" . OBJECT_FILES_TABLE . "', document.forms[0].elements['$idname'].value,'');}");

			$button = we_html_button::create_button('select', "javascript:we_cmd('openDocselector',document.forms[0].elements['$idname'].value,'$table','document.forms[\'we_form\'].elements[\'$idname\'].value','document.forms[\'we_form\'].elements[\'$textname\'].value','opener.we_cmd(\'reload_editpage\');opener._EditorFrame.setEditorIsHot(true);','" . session_id() . "','$rootDirID','objectFile'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")");


			$yuiSuggest = &weSuggest::getInstance();
			$yuiSuggest->setAcId($name . we_base_file::getUniqueId(), f('SELECT Path FROM ' . OBJECT_TABLE . ' WHERE ID=' . $classid));
			$yuiSuggest->setContentType('folder,objectFile');
			$yuiSuggest->setInput($textname, $path);
			$yuiSuggest->setResult($idname, $we_oid);
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(1);
			$yuiSuggest->setSelector(weSuggest::DocSelector);
			$yuiSuggest->setTable(OBJECT_FILES_TABLE);
			$yuiSuggest->setWidth($size);
			?>
			<table class="weEditTable padding0 spacing0 border0">
				<tr>
					<td class="weEditmodeStyle" style="padding:0 6px;"><span style="font-weight: bold;"><?php echo weTag_getAttribute('text', $attribs, weTag_getAttribute('_name_orig', $attribs)); ?></span></td>
					<?php echo '<td class="weEditmodeStyle" style="width:' . ($size + 20) . 'px">' . $yuiSuggest->getHTML() . '</td>'; ?>
					<td class="weEditmodeStyle"><?php echo $button; ?></td>
					<td class="weEditmodeStyle"><?php echo $open; ?></td>
					<td class="weEditmodeStyle"><?php echo $delbutton; ?></td>

				</tr>
			</table><?php
		}
	} else {
		$we_oid = $we_oid ? $we_oid : we_base_request::_(we_base_request::INT, 'we_oid',0);
	}
	$GLOBALS['lv'] = new we_object_tag($classid, $we_oid, $triggerid, $searchable, $condition, $hidedirindex, $objectseourls);
	if(is_array($GLOBALS['we_lv_array'])){
		$GLOBALS['we_lv_array'][] = clone($GLOBALS['lv']);
	}

	if($GLOBALS['lv']->avail){
		if(isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
			echo '<a href="' . $we_oid . '" seem="object"></a>';
		}
	}
	return $GLOBALS['lv']->avail;
}
