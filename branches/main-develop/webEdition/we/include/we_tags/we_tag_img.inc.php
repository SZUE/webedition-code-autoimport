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
function we_tag_img($attribs){
	if(!($tagId = weTag_getAttribute('id', $attribs)) && ($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}

	$name = weTag_getAttribute('name', $attribs);
	$startid = weTag_getAttribute('startid', $attribs);
	$parentid = weTag_getAttribute('parentid', $attribs, 0);
	$showcontrol = weTag_getAttribute('showcontrol', $attribs, true, true);
	$showThumb = weTag_getAttribute('showthumbcontrol', $attribs, false, true);
	$showimage = weTag_getAttribute('showimage', $attribs, true, true);
	$showinputs = weTag_getAttribute('showinputs', $attribs, SHOWINPUTS_DEFAULT, true);

	$tagAttribs = removeAttribs($attribs, array('id', 'showcontrol', 'showthumbcontrol', 'showimage', 'showinputs', 'startid', 'parentid'));

	if($name){
		$id = $GLOBALS['we_doc']->getElement($name, 'bdid');
		$id = $id ? $id : $GLOBALS['we_doc']->getElement($name);
		$id = $id ? $id : $tagId;
	} else {
		$showThumb = $showcontrol = false;
		$id = $tagId;
	}

	//look if image exists in tblfile, and is an image
	if(!f('SELECT 1 FROM ' . FILE_TABLE . ' WHERE ContentType="' . we_base_ContentTypes::IMAGE . '" AND ID=' . intval($id))){
		$id = 0;
	}

	$altField = $name . we_imageDocument::ALT_FIELD;
	$titleField = $name . we_imageDocument::TITLE_FIELD;
	$thumbField = $name . we_imageDocument::THUMB_FIELD;

	$fname = 'we_' . $GLOBALS['we_doc']->Name . '_img[' . $name . '#bdid]';
	$altname = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $altField . ']';
	$titlename = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $titleField . ']';
	$thumbname = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $thumbField . ']';

	if($id){
		$img = new we_imageDocument();
		$img->initByID($id);

		$tagAttribs['alt'] = weTag_getAttribute('alt', $attribs, $img->getElement('alt'));
		$tagAttribs['title'] = weTag_getAttribute('title', $attribs, $img->getElement('title'));
		if($showThumb){
			$thumb = $img->getElement($thumbname);
		}
	}

	// images can now have custom attribs
	if(!$GLOBALS['we_editmode'] || !(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) == 'reload_editpage' &&
		($name == we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1)) &&
		we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2) == 'change_image') &&
		isset($GLOBALS['we_doc']->elements[$altField])){ // if no other image is selected.
		$alt = $GLOBALS['we_doc']->getElement($altField);
		$title = $GLOBALS['we_doc']->getElement($titleField);
		$tagAttribs['alt'] = $alt ? $alt : (isset($tagAttribs['alt']) ? $tagAttribs['alt'] : '');
		$tagAttribs['title'] = $title ? $title : (isset($tagAttribs['title']) ? $tagAttribs['title'] : '');
		if($showThumb){
			$thumb = $GLOBALS['we_doc']->getElement($thumbField);
			$thumbattr = $thumb;
			$tagAttribs['thumbnail'] = $thumbattr;
		}
	} elseif(isset($GLOBALS['we_doc'])){
		$alt = $GLOBALS['we_doc']->getElement($altField);
		$title = $GLOBALS['we_doc']->getElement($titleField);
		if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2) == 'change_image'){
			//in case of changed images give them priority to already set text
			$tagAttribs['alt'] = (isset($tagAttribs['alt']) && $tagAttribs['alt'] ? $tagAttribs['alt'] : $alt);
			$tagAttribs['title'] = (isset($tagAttribs['title']) && $tagAttribs['title'] ? $tagAttribs['title'] : $title);
		} else {
			$tagAttribs['alt'] = $alt ? $alt : (isset($tagAttribs['alt']) ? $tagAttribs['alt'] : '');
			$tagAttribs['title'] = $title ? $title : (isset($tagAttribs['title']) ? $tagAttribs['title'] : '');
		}
		if($showThumb){
			$thumbattr = $GLOBALS['we_doc']->getElement($thumbField);
			$tagAttribs['thumbnail'] = $thumbattr;
		}
	}

	if($GLOBALS['we_editmode'] && !$showimage){
		$out = '';
	} elseif($id){
		if($tagId){//bug 6433: später wird so ohne weiteres gar nicht mehr auf die id zurückgegriffen
			$tagAttribs['id'] = $tagId; //siehe korrespondierende Änderung in we:document::getField
			$tagAttribs['showcontrol'] = $showcontrol; //sicherstellen das es boolean iost
		}
		$out = $GLOBALS['we_doc']->getField($tagAttribs, 'img');
	} elseif($GLOBALS['we_editmode'] && $GLOBALS['we_doc']->InWebEdition){
		$tagAttribs = removeAttribs($tagAttribs, array('thumbnail', 'only', 'name'));
		$tagAttribs['src'] = IMAGE_DIR . 'icons/no_image.gif';
		$tagAttribs['style'] = 'width:64px;height:64px;border-style:none;';
		$tagAttribs['alt'] = 'no-img';
		$out = getHtmlTag('img', $tagAttribs);
		$tagAttribs['alt'] = $tagAttribs['title'] = '';
	} else {
		$out = ''; //no_image war noch in der Vorscha sichtbar
	}

	if(!$id && (!$GLOBALS['we_editmode'])){
		return '';
	}

	if($showcontrol && $GLOBALS['we_editmode']){
		if(!$id){
			$id = '';
		}
		$out = '
<table class="weEditTable padding2 spacing2">
	<tr>
		<td class="weEditmodeStyle" colspan="2" align="center">' . $out . '
			<input onchange="_EditorFrame.setEditorIsHot(true);" type="hidden" name="' . $fname . '" value="' . $id . '" />
		</td>
	</tr>' .
			($showinputs ? //  only when wanted
				'
	<tr>
		<td class="weEditmodeStyle" align="center" colspan="2" style="width: 180px;">
			<table class="weEditTable padding0 spacing0 border0">
				<tr>
					<td class="weEditmodeStyle" style="color: black; font-size: 12px; font-family: ' . g_l('css', '[font_family]') . ';">' . g_l('weClass', "[alt_kurz]") . ':&nbsp;</td>
					<td class="weEditmodeStyle">' . we_html_tools::htmlTextInput($altname, 16, $tagAttribs['alt'], '', 'onchange="_EditorFrame.setEditorIsHot(true);"') . '</td>
				</tr>
				<tr>
					<td class="weEditmodeStyle"></td>
					<td class="weEditmodeStyle"></td>
				</tr>
				<tr>
					<td class="weEditmodeStyle" style="color: black; font-size: 12px; font-family: ' . g_l('css', '[font_family]') . ";\">" . g_l('weClass', "[Title]") . ':&nbsp;</td>
					<td class="weEditmodeStyle">' . we_html_tools::htmlTextInput($titlename, 16, $tagAttribs['title'], '', 'onchange="_EditorFrame.setEditorIsHot(true);"') . '</td>
				</tr>
			</table>
		</td>
	</tr>' : ''
			);

		if($showThumb){ //  only when wanted
			$db = $GLOBALS['DB_WE'];
			$db->query('SELECT ID,Name FROM ' . THUMBNAILS_TABLE . ' ORDER BY Name');
			if($db->num_rows()){
				$thumbnails = '<select name="' . $thumbname . '" size="1" onchange="top.we_cmd(\'reload_editpage\'); _EditorFrame.setEditorIsHot(true);">' .
					'<option value=""' . (($thumbattr == '') ? (' selected="selected"') : "") . '></option>';
				while($db->next_record()){
					$thumbnails .= '<option value="' . $db->f("Name") . '"' . (($thumbattr == $db->f("Name")) ? (' selected="selected"') : "") . '>' . $db->f("Name") . '</option>';
				}
				$thumbnails .= '</select>';
				$out .= '
	<tr>
		<td class="weEditmodeStyle" align="center" colspan="2" style="width: 180px;">
			<table class="weEditTable padding0 spacing0 border0">
				<tr>
					<td class="weEditmodeStyle" style="color: black; font-size: 12px; font-family: ' . g_l('css', '[font_family]') . ';">' . g_l('weClass', "[thumbnails]") . ':&nbsp;</td>
					<td class="weEditmodeStyle">' . $thumbnails . '</td>
				</tr>
			</table>
		</td>
	</tr>';
			}
		}
		$out .= '
	<tr>
		<td class="weEditmodeStyle" colspan="2" align="center">';

		$_editButton = (empty($id) ? // disable edit_image_button
				we_html_button::create_button("image:btn_edit_image", "#", false, 100, 20, "", "", true) :
				//	show edit_image_button
				//	we use hardcoded Content-Type - because it must be an image -> <we:img  >
				we_html_button::create_button("image:btn_edit_image", "javascript:top.doClickDirect($id,'" . we_base_ContentTypes::IMAGE . "', '" . FILE_TABLE . "'  )"));

		$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['" . $fname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("opener.setScrollTo(); opener._EditorFrame.setEditorIsHot(true); opener.top.we_cmd('reload_editpage','" . $name . "','change_image'); opener.top.hot = 1;");

		$out .= we_html_button::create_button_table(
				array(
				$_editButton,
				we_html_button::create_button("image:btn_select_image", "javascript:we_cmd('openDocselector', '" . ($id ? $id : $startid) . "', '" . FILE_TABLE . "','" . $wecmdenc1 . "','','" . $wecmdenc3 . "','" . session_id() . "'," . $parentid . ",'" . we_base_ContentTypes::IMAGE . "', " . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")", true),
				we_html_button::create_button("image:btn_function_trash", "javascript:we_cmd('remove_image', '" . $name . "')", true)
				), 5) . '</td></tr></table>';
	}
	return $out;
}
