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

function we_tag_img($attribs, $content){
	if ($GLOBALS['we_editmode']) {
		// Include we_button class
		include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/html/we_button.inc.php");
		include ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_language/' . $GLOBALS['WE_LANGUAGE'] . '/css/css.inc.php');
		include ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_language/' . $GLOBALS['WE_LANGUAGE'] . '/we_class.inc.php');
	}

	$foo = attributFehltError($attribs, "name", "img");
	if ($foo)
		return $foo;

	$name = we_getTagAttribute("name", $attribs);
	$startid = we_getTagAttribute("startid", $attribs, "");
	$parentid = we_getTagAttribute("parentid", $attribs, "0");
	$showcontrol = we_getTagAttribute("showcontrol", $attribs, "", true, true);
	$showThumb = we_getTagAttribute("showthumbcontrol", $attribs, "", true, false);
	$showimage = we_getTagAttribute("showimage", $attribs, "true", true, true);
	$showinputs = we_getTagAttribute(
			"showinputs",
			$attribs,
			0,
			true,
			defined("SHOWINPUTS_DEFAULT") ? SHOWINPUTS_DEFAULT : true);

	$id = $GLOBALS["we_doc"]->getElement($name, "bdid");
	$id = $id ? $id : $GLOBALS["we_doc"]->getElement($name);
	$id = $id ? $id : we_getTagAttribute("id", $attribs);

	//look if image exists in tblfile
	$imgExists = f("SELECT ID FROM " . FILE_TABLE . " WHERE ID='" . abs($id) . "'", "ID", new DB_WE());
	if ($imgExists == "") {
		$id = 0;
	}

	// images can now have custom attribs ...
	$alt = '';
	$title = '';

	$altField = $name . '_img_custom_alt';
	$titleField = $name . '_img_custom_title';
	$thumbField = $name . '_img_custom_thumb';

	$fname = 'we_' . $GLOBALS["we_doc"]->Name . '_img[' . $name . '#bdid]';
	$altname = 'we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $altField . ']';
	$titlename = 'we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $titleField . ']';
	$thumbname = 'we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $thumbField . ']';

	if ($id) {
		$img = new we_imageDocument();
		$img->initByID($id);

		$alt = $img->getElement('alt');
		$title = $img->getElement('title');
		if ($showThumb){
			$thumb = $img->getElement($thumbname);
		}
	}

	// images can now have custom attribs ...
	if (!(isset($_REQUEST['we_cmd'][0]) && $_REQUEST['we_cmd'][0] == 'reload_editpage' && (isset(
			$_REQUEST['we_cmd'][1]) && $name == $_REQUEST['we_cmd'][1]) && isset($_REQUEST['we_cmd'][2]) && $_REQUEST['we_cmd'][2] == 'change_image') && isset(
			$GLOBALS['we_doc']->elements[$altField])) { // if no other image is selected.
		$alt = $GLOBALS['we_doc']->getElement($altField);
		$title = $GLOBALS['we_doc']->getElement($titleField);
		if ($showThumb){
				$thumb = $GLOBALS['we_doc']->getElement($thumbField);
				$thumbattr = $thumb;
				$attribs['thumbnail'] = $thumbattr;
		}
	} elseif (isset($GLOBALS['we_doc'])) {
		$altattr = $GLOBALS['we_doc']->getElement($altField);
		$titleattr = $GLOBALS['we_doc']->getElement($titleField);	
		$altattr == "" ? "" : $attribs['alt'] = $altattr;
		$titleattr == "" ? "" : $attribs['title'] = $titleattr;
		if ($showThumb){
			$thumbattr = $GLOBALS['we_doc']->getElement($thumbField);
			$attribs['thumbnail'] = $thumbattr;
		}
	}

	if ($GLOBALS['we_editmode'] && !$showimage) {
		$out = '';
	} elseif (!$id) {
		if($GLOBALS['we_editmode'] && $GLOBALS['we_doc']->InWebEdition == 1) {$out = '<img src="' . IMAGE_DIR . 'icons/no_image.gif" width="64" height="64" border="0" alt="" />';} else {$out ='';} //no_image war noch in der Vorscha sichtbar
	} else {
		$out = $GLOBALS["we_doc"]->getField($attribs, "img");
	}

	if (!$id && (!$GLOBALS['we_editmode'])) {
		return "";
	} else
		if (!$id) {
			$id = "";
		}

	if ($showcontrol && $GLOBALS['we_editmode']) {
		// Create object of we_button class
		$we_button = new we_button();

		$out = "
			<table border=\"0\" cellpadding=\"2\" cellspacing=\"2\" background=\"" . IMAGE_DIR . "backgrounds/aquaBackground.gif\" style=\"border: solid #006DB8 1px;\">
				<tr>
					<td class=\"weEditmodeStyle\" colspan=\"2\" align=\"center\">$out
						<input onchange=\"_EditorFrame.setEditorIsHot(true);\" type=\"hidden\" name=\"$fname\" value=\"$id\" /></td>
				</tr>";
		if ($showinputs) { //  only when wanted
			$out .= "
		        <tr>
		            <td class=\"weEditmodeStyle\" align=\"center\" style=\"width: 180px;\">
		            <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                    <tr>
                        <td class=\"weEditmodeStyle\" style=\"color: black; font-size: 12px; font-family: " . $l_css["font_family"] . ";\">" . $l_we_class["alt_kurz"] . ":&nbsp;</td>
                        <td class=\"weEditmodeStyle\">" . htmlTextInput($altname, 16, $alt,'','onchange="_EditorFrame.setEditorIsHot(true);"') . "</td>
                    </tr>
					<tr>
						<td class=\"weEditmodeStyle\"></td>
					</tr>
				    <tr>
		                <td class=\"weEditmodeStyle\" style=\"color: black; font-size: 12px; font-family: " . $l_css["font_family"] . ";\">" . $l_we_class["title"] . ":&nbsp;</td>
		                <td class=\"weEditmodeStyle\">" . htmlTextInput($titlename, 16, $title,'','onchange="_EditorFrame.setEditorIsHot(true);"') . "</td>
                    </tr>
		            </table>
                </tr>";
		}
		if ($showThumb) { //  only when wanted
			
				$db= new DB_WE();
				$db->query("SELECT ID,Name FROM " . THUMBNAILS_TABLE . " ORDER BY Name");
				if ($db->num_rows()){
					$thumbnails = '<select name="'.$thumbname.'" size="1" onchange="top.we_cmd(\'reload_editpage\'); _EditorFrame.setEditorIsHot(true);">'."\n";
					$thumbnails .= '<option value=""'.(($thumbattr=='') ? (' selected="selected"') : "").'></option>'."\n";
					while($db->next_record()){
						$thumbnails .= '<option value="'.$db->f("Name").'"'.(($thumbattr==$db->f("Name")) ? (' selected="selected"') : "").'>'.$db->f("Name").'</option>'."\n";
					}
					$thumbnails .= '</select>';			
					$out .= "
		        		<tr>
		            	<td class=\"weEditmodeStyle\" align=\"center\" colspan=\"2\" style=\"width: 180px;\">".
						$thumbnails
						."</td>		            
                		</tr>";
				}
		}
		$out .= "
				<tr>
					<td class=\"weEditmodeStyle\" colspan=\"2\" align=\"center\">";

		if ($id == "") { // disable edit_image_button
			$_editButton = $we_button->create_button("image:btn_edit_image", "#", false, 100, 20, "", "", true);
		} else { //	show edit_image_button
			//	we use hardcoded Content-Type - because it must be an image -> <we:img ... >
			$_editButton = $we_button->create_button(
					"image:btn_edit_image",
					"javascript:top.doClickDirect($id,'image/*', '" . FILE_TABLE . "'  )");
		}
		//"javascript:we_cmd('openDocselector', '" . ($id != "" ? $id : $startid) . "', '" . FILE_TABLE . "', 'document.forms[\\'we_form\\'].elements[\\'" . $fname . "\\'].value', '', 'opener.setScrollTo(); opener._EditorFrame.setEditorIsHot(true); opener.top.we_cmd(\\'reload_editpage\\',\\'" . $name . "\\',\\'change_image\\'); opener.top.hot = 1;', '" . session_id() . "', " . $parentid . ", 'image/*', " . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")",
		$wecmdenc1= 'WECMDENC_'.base64_encode("document.forms['we_form'].elements['" . $fname . "'].value");
		$wecmdenc3= 'WECMDENC_'.base64_encode("opener.setScrollTo(); opener._EditorFrame.setEditorIsHot(true); opener.top.we_cmd('reload_editpage','" . $name . "','change_image'); opener.top.hot = 1;");
		
		$out .= $we_button->create_button_table(
				array(

						$_editButton,
					
						$we_button->create_button(
								"image:btn_select_image",
								"javascript:we_cmd('openDocselector', '" . ($id != "" ? $id : $startid) . "', '" . FILE_TABLE . "','".$wecmdenc1."','','".$wecmdenc3."','" . session_id() . "'," . $parentid . ",'image/*', " . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")",
								true),
						$we_button->create_button(
								"image:btn_function_trash",
								"javascript:we_cmd('remove_image', '" . $name . "')",
								true)
				),
				5) . "</td></tr></table>";
	}
	return $out;
}
