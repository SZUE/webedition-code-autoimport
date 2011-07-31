<?php

/**
 * webEdition CMS
 *
 * $Rev: 3084 $
 * $Author: mokraemer $
 * $Date: 2011-07-27 21:57:15 +0200 (Mi, 27. Jul 2011) $
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
function we_parse_tag_customer($attribs, $content) {
	eval('$arr = ' . $attribs . ';');
	$name = we_getTagAttributeTagParser("name", $arr);
	if ($name && strpos($name, ' ') !== false) {
		return parseError(sprintf(g_l('parser', '[name_with_space]'), 'customer'));
	}

	return '<?php global $lv;
		'.we_tagParser::printTag('customer', $attribs).';
		if($GLOBALS[\'lv\']->avail){?>' . $content . '<?php } 
		we_post_tag_listview(); ?>';
}

function we_tag_customer($attribs, $content) {
	if (!defined("WE_CUSTOMER_MODULE_DIR")) {
		print modulFehltError('Customer', 'customer');
		return;
	}

	$condition = we_getTagAttribute("condition", $attribs, 0);
	$we_cid = we_getTagAttribute("id", $attribs, 0);
	$name = we_getTagAttribute("name", $attribs);
	$_showName = we_getTagAttribute("_name_orig", $attribs);
	$size = we_getTagAttribute("size", $attribs, 30);
	$hidedirindex = we_getTagAttribute("hidedirindex", $attribs, (defined('TAGLINKS_DIRECTORYINDEX_HIDE') && TAGLINKS_DIRECTORYINDEX_HIDE?'true':'false'), false);

	if (!isset($GLOBALS["we_lv_array"])) {
		$GLOBALS["we_lv_array"] = array();
	}

	include_once(WE_CUSTOMER_MODULE_DIR . "we_customertag.inc.php");

	if ($name) {
		if (strpos($name, " ") !== false) {
			return parseError(sprintf(g_l('parser', '[name_with_space]'), "object"));
		}

		$we_doc = $GLOBALS["we_doc"];
		$we_cid = $we_doc->getElement($name) ? $we_doc->getElement($name) : $we_cid;

		$we_cid = $we_cid ? $we_cid : (isset($_REQUEST["we_cid"]) ? $_REQUEST["we_cid"] : 0);
		$path = f("SELECT Path FROM " . CUSTOMER_TABLE . " WHERE ID=" . abs($we_cid), "Path", $GLOBALS["DB_WE"]);
		$textname = 'we_' . $we_doc->Name . '_txt[' . $name . '_path]';
		$idname = 'we_' . $we_doc->Name . '_txt[' . $name . ']';
		$table = CUSTOMER_TABLE;
		$we_button = new we_button();
		$delbutton = $we_button->create_button("image:btn_function_trash", "javascript:document.forms[0].elements['$idname'].value=0;document.forms[0].elements['$textname'].value='';_EditorFrame.setEditorIsHot(false);we_cmd('reload_editpage');");
		$button = $we_button->create_button("select", "javascript:we_cmd('openSelector',document.forms[0].elements['$idname'].value,'$table','document.forms[\'we_form\'].elements[\'$idname\'].value','document.forms[\'we_form\'].elements[\'$textname\'].value','opener.we_cmd(\'reload_editpage\');opener._EditorFrame.setEditorIsHot(true);','" . session_id() . "',0,'',1)");

		if ($GLOBALS["we_editmode"]) {
			include_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_classes/html/we_button.inc.php");
			$we_button = new we_button();
			?>
			<table border="0" cellpadding="0" cellspacing="0" background="<?php print IMAGE_DIR ?>backgrounds/aquaBackground.gif">
				<tr>
					<td style="padding:0 6px;"><span style="color: black; font-size: 12px; font-family: Verdana, sans-serif"><b><?php print $_showName; ?></b></span></td>
					<td><?php print hidden($idname, $we_cid) ?></td>
					<td><?php print htmlTextInput($textname, $size, $path, "", ' readonly', "text", 0, 0); ?></td>
					<td><?php getPixel(6, 4); ?></td>
					<td><?php print $button; ?></td>
					<td><?php getPixel(6, 4); ?></td>
					<td><?php print $delbutton; ?></td>
				</tr>
			</table><?php
		}
	} else {

		$we_cid = $we_cid ? $we_cid : (isset($_REQUEST["we_cid"]) ? $_REQUEST["we_cid"] : 0);
	}

	$GLOBALS["lv"] = new we_customertag($we_cid, $condition, $hidedirindex);
	$lv = clone($GLOBALS["lv"]); // for backwards compatibility
	if (is_array($GLOBALS["we_lv_array"]))
		array_push($GLOBALS["we_lv_array"], clone($GLOBALS["lv"]));
	if ($GLOBALS["lv"]->avail) {
//implement seem
	}
}