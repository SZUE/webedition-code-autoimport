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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

function we_tag_flashmovie($attribs, $content){
	// Include Flash class
	include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/we_flashDocument.inc.php");

	$foo = attributFehltError($attribs, "name", "flashmovie");
	if ($foo)
		return $foo;
	$name = we_getTagAttribute("name", $attribs);
	$id = $GLOBALS["we_doc"]->getElement($name, "bdid");
	$id = $id ? $id : we_getTagAttribute("id", $attribs);
	$fname = 'we_' . $GLOBALS["we_doc"]->Name . '_img[' . $name . '#bdid]';
	$wmode = we_getTagAttribute("wmode", $attribs, "window");
	$startid = we_getTagAttribute("startid", $attribs, "");
	$parentid = we_getTagAttribute("parentid", $attribs, "0");
	$showcontrol = we_getTagAttribute("showcontrol", $attribs, "true", true, true);
	$showflash = we_getTagAttribute("showflash", $attribs, "true", true, true);

	$attribs = removeAttribs($attribs, array(
		'showcontrol', 'showflash'
	));

	if ($GLOBALS['we_editmode'] && !$showflash) {
		$out = '';
	} else {
		$out = $GLOBALS["we_doc"]->getField($attribs, "flashmovie");
	}

	if ($showcontrol && $GLOBALS['we_editmode']) {
		// Include button class
		include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/html/we_button.inc.php");

		// Create new button object
		$we_button = new we_button();

		// Create "Edit Flash" button
		$flash_button = $we_button->create_button(
				"image:btn_edit_flash",
				"javascript:we_cmd('openDocselector','" . ($id != "" ? $id : $startid) . "', '" . FILE_TABLE . "', 'document.forms[\'we_form\'].elements[\'" . $fname . "\'].value', '', 'opener.setScrollTo();opener.top.we_cmd(\'reload_editpage\');opener._EditorFrame.setEditorIsHot(true);', '" . session_id() . "'," .$parentid. ", 'application/x-shockwave-flash', " . (we_hasPerm(
						"CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")",
				true);

		// Create "Delete/Clear Flash" button
		$clear_button = $we_button->create_button(
				"image:btn_function_trash",
				"javascript:we_cmd('remove_image', '" . $name . "')",
				true);

		// Create HTML output


		$out = "
			<table border=\"0\" cellpadding=\"0\" cellspacing=\"2\" background=\"" . IMAGE_DIR . "backgrounds/aquaBackground.gif\" style=\"border: solid #006DB8 1px;\">
				<tr>
					<td class=\"weEditmodeStyle\">$out
						<input type=\"hidden\" name=\"$fname\" value=\"" . $GLOBALS["we_doc"]->getElement(
				$name,
				"bdid") . "\" /></td>
				</tr>
				<tr>
					<td class=\"weEditmodeStyle\" align=\"center\">";
		$out .= $we_button->create_button_table(array(
			$flash_button, $clear_button
		), 5) . "</td></tr></table>";
	}
	//	When in SEEM - Mode add edit-Button to tag - textarea
	return $out;
}
