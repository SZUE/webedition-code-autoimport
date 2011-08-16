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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function we_parse_tag_object($attribs, $content) {
	eval('$arr = ' . $attribs . ';');
	$name = we_getTagAttributeTagParser('name', $arr);
	if ($name && strpos($name, ' ') !== false) {
		return parseError(sprintf(g_l('parser', '[name_with_space]'), 'object'));
	}

	return '<?php global $lv;
		if('.we_tagParser::printTag('object', $attribs).'){?>' . $content . '<?php } 
		we_post_tag_listview(); ?>';
}

function we_tag_object($attribs, $content) {
	if (!defined('WE_OBJECT_MODULE_DIR')) {
		print modulFehltError('Object/DB', 'object');
		return false;
	}

	$condition = we_getTagAttribute('condition', $attribs, 0);
	$classid = we_getTagAttribute('classid', $attribs);
	$we_oid = we_getTagAttribute('id', $attribs, 0);
	$name = we_getTagAttribute('name', $attribs);
	//never show name generated inside blocks
	$_showName = we_getTagAttribute('_name_orig', $attribs);
	$size = we_getTagAttribute('size', $attribs, 30);
	$triggerid = we_getTagAttribute('triggerid', $attribs, '0');
	$searchable = we_getTagAttribute('searchable', $attribs, '', true);
	$hidedirindex = we_getTagAttribute('hidedirindex', $attribs, (defined('TAGLINKS_DIRECTORYINDEX_HIDE') && TAGLINKS_DIRECTORYINDEX_HIDE ? 'true' : 'false'), false);
	$objectseourls = we_getTagAttribute('objectseourls', $attribs, (defined('TAGLINKS_OBJECTSEOURLS') && TAGLINKS_OBJECTSEOURLS ? 'true' : 'false'), false);

	if (!isset($GLOBALS['we_lv_array'])) {
		$GLOBALS['we_lv_array'] = array();
	}

	include_once(WE_OBJECT_MODULE_DIR . 'we_objecttag.inc.php');

	if ($classid) {
		$rootDirID = f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path=(SELECT Path FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($classid) . ')', 'ID', $GLOBALS['DB_WE']);
	} else {
		$rootDirID = 0;
	}
	if ($name) {
		if (strpos($name, ' ') !== false) {
			print parseError(sprintf(g_l('parser', '[name_with_space]'), 'object'));
			return;
		}

		$we_doc = $GLOBALS['we_doc'];
		$we_oid = $we_doc->getElement($name) ? $we_doc->getElement($name) : $we_oid;

		$path = f('SELECT Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . $we_oid, 'Path', $GLOBALS['DB_WE']);
		$textname = 'we_' . $we_doc->Name . '_txt[' . $name . '_path]';
		$idname = 'we_' . $we_doc->Name . '_txt[' . $name . ']';
		$table = OBJECT_FILES_TABLE;

		if ($GLOBALS['we_editmode']) {
			include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/html/we_button.inc.php');
			$delbutton = we_button::create_button('image:btn_function_trash', "javascript:document.forms[0].elements['$idname'].value=0;document.forms[0].elements['$textname'].value='';_EditorFrame.setEditorIsHot(false);we_cmd('reload_editpage');");
			$button = we_button::create_button('select', "javascript:we_cmd('openDocselector',document.forms[0].elements['$idname'].value,'$table','document.forms[\'we_form\'].elements[\'$idname\'].value','document.forms[\'we_form\'].elements[\'$textname\'].value','opener.we_cmd(\'reload_editpage\');opener._EditorFrame.setEditorIsHot(true);','" . session_id() . "','$rootDirID','objectFile'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")");
			?>
			<table border="0" cellpadding="0" cellspacing="0" background="<?php print IMAGE_DIR ?>backgrounds/aquaBackground.gif">
				<tr>
					<td style="padding:0 6px;"><span style="color: black; font-size: 12px; font-family: Verdana, sans-serif"><b><?php echo $_showName; ?></b></span></td>
					<td><?php print hidden($idname, $we_oid) ?></td>
					<td><?php print htmlTextInput($textname, $size , $path, "", ' readonly', "text", 0, 0); ?></td>
					<td><?php getPixel(6, 4);?></td>
					<td><?php print $button; ?></td>
					<td><?php getPixel(6, 4);?></td>
					<td><?php print $delbutton; ?></td>
				</tr>
			</table><?php
		}
	} else {

		$we_oid = $we_oid ? $we_oid : (isset($_REQUEST['we_oid']) ? $_REQUEST['we_oid'] : 0);
	}
	$searchable = empty($searchable) ? 'false' : $searchable;
	$GLOBALS['lv'] = new we_objecttag($classid, $we_oid, $triggerid, $searchable, $condition, $hidedirindex, $objectseourls);
	if (is_array($GLOBALS['we_lv_array'])) {
		array_push($GLOBALS['we_lv_array'], clone($GLOBALS['lv']));
	}

	if ($GLOBALS['lv']->avail) {
		if (isset($_SESSION['we_mode']) && $_SESSION['we_mode'] == 'seem') {
			print '<a href="'.$we_oid.'" seem="object"></a>';
		}
	}
	return $GLOBALS['lv']->avail;
}
