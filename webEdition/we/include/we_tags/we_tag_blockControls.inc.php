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
function we_parse_tag_blockControls($attribs, $content, array $arr){
	return '<?php we_tag_blockControls("##blockControlsREPL##"' . ($arr ? '+' . we_tag_tagParser::printArray($arr, false) : '') . ');?>';
}

function we_tag_blockControls($attribs){
	//if in listview no Buttons are shown!
	if(!$GLOBALS['we_editmode'] || isset($GLOBALS['lv']) || !isset($attribs['list']) || (isset($attribs['show']) && !$attribs['show'])){
		return '';
	}
	$showSelect = weTag_getAttribute('showselect', $attribs, $attribs['ctlShowSelect'], we_base_request::BOOL);

	if(!isset($attribs['ctlName'])){
		$attribs['ctlName'] = md5(str_replace('.', '', uniqid('', true))); // #6590, changed from: uniqid(time())
	}

	if($attribs['pos'] < $attribs['listSize']){
		$tabArray = array();
		if($showSelect && $attribs['ctlShow'] > 0){
			$jsSelector = $attribs['pos'] . ",document.we_form.elements['" . $attribs['ctlName'] . "_" . $attribs['pos'] . "'].options[document.we_form.elements['" . $attribs['ctlName'] . '_' . $attribs['pos'] . "'].selectedIndex].text";
			$tabArray[] = we_html_button::create_button('fa:btn_add_listelement,fa-plus,fa-lg fa-list-ul', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('insert_entry_at_list','" . $attribs['name'] . "'," . $jsSelector . ")", true, 100, 22, '', '', !($attribs['ctlShow'] > 0));

			$selectb = '<select name="' . $attribs['ctlName'] . '_' . $attribs['pos'] . '">';
			for($j = 0; $j < $attribs['ctlShow']; $j++){
				$selectb .= '<option value="' . ($j + 1) . '">' . ($j + 1) . '</option>';
			}
			$selectb .= '</select>';
			$tabArray[] = $selectb;
		} else {
			$tabArray[] = we_html_button::create_button('fa:btn_add_listelement,fa-plus,fa-lg fa-list-ul', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('insert_entry_at_list','" . $attribs['name'] . "','" . $attribs['pos'] . "',1)", true, 100, 22, '', '', !($attribs['ctlShow'] > 0));
			$jsSelector = 1;
		}
		$tabArray[] = (($attribs['pos'] > 0) ?
				//enabled upBtn
				we_html_button::create_button(we_html_button::DIRUP, "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('up_entry_at_list','" . $attribs['name'] . "','" . $attribs['pos'] . "'," . $jsSelector . ")") :
				//disabled upBtn
				we_html_button::create_button(we_html_button::DIRUP, '', true, 0, 0, '', '', true));
		$tabArray[] = (($attribs['pos'] == $attribs['listSize'] - 1) ?
				//disabled downBtn
				we_html_button::create_button(we_html_button::DIRDOWN, '', true, 0, 0, '', '', true) :
				//enabled downBtn
				we_html_button::create_button(we_html_button::DIRDOWN, "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('down_entry_at_list','" . $attribs['name'] . "','" . $attribs['pos'] . "'," . $jsSelector . ")"));
		$tabArray[] = we_html_button::create_button(we_html_button::TRASH, "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('delete_list','" . $attribs['name'] . "','" . $attribs['pos'] . "','" . $GLOBALS['postTagName'] . "')");

		echo '<div class="we_blockControls">' . implode('', $tabArray) . '</div>';
		return;
	}
	if($showSelect && $attribs['ctlShow'] > 0){
		$selectb = '<select name="' . $attribs['ctlName'] . '_00">';
		for($j = 1; $j <= $attribs['ctlShow']; $j++){
			$selectb .= '<option value="' . $j . '">' . $j . '</option>';
		}
		$selectb .= '</select>';
		$plusbut = '<div class="we_blockControls">' .
			we_html_button::create_button('fa:btn_add_listelement,fa-plus,fa-lg fa-list-ul', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('add_entry_to_list','" . $attribs['name'] . "',document.we_form.elements['" . $attribs['ctlName'] . "_00'].options[document.we_form.elements['" . $attribs['ctlName'] . "_00'].selectedIndex].text);", true, 100, 22, '', '', !($attribs['ctlShow'] > 0)) .
			$selectb .
			'</div>';
	} else {
		$plusbut = '<div class="we_blockControls">' .
			we_html_button::create_button('fa:btn_add_listelement,fa-plus,fa-lg fa-list-ul', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('add_entry_to_list','" . $attribs['name'] . "',1)", true, 100, 22, '', '', !($attribs['ctlShow'] > 0)) .
			'</div>';
	}

	echo '<input type="hidden" name="we_' . $GLOBALS['we_doc']->Name . '_block[' . $attribs['name'] . ']" value="' .
	htmlentities(
		(empty($attribs['list']) ? '' : we_serialize($attribs['list'], SERIALIZE_JSON, true, 0, true))
	) . //FIXME: do we really need this serialized in the document???
	'">' . $plusbut;
	return;
}
