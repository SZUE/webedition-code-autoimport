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
function we_tag_link($attribs, $content){
	if(isset($GLOBALS['we']['ll'])){
		$only = weTag_getAttribute('only', $attribs);
		$link = $GLOBALS['we']['ll']->getLink();
		$linkcontent = $GLOBALS['we']['ll']->getLinkContent();
		if($link){
			return $GLOBALS['we']['ll']->getLinktag($link, $attribs) . ($only ? '' : $linkcontent . '</a>');
		}
		return $linkcontent;
	}
	$name = weTag_getAttribute('name', $attribs);
	$xml = weTag_getAttribute('xml', $attribs, XHTML_DEFAULT, true);
	$text = weTag_getAttribute('text', $attribs);
	$imageid = weTag_getAttribute('imageid', $attribs, 0);
	$id = weTag_getAttribute('id', $attribs);

	// check if target document exists (Bug #7167)
	if($id != 0 && (f('SELECT 1 AS tmp FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), 'tmp', $GLOBALS['DB_WE']) != '1')){
		$id = 0;
	}
	if($imageid != 0 && (f('SELECT 1 AS tmp FROM ' . FILE_TABLE . ' WHERE ID=' . intval($imageid), 'tmp', $GLOBALS['DB_WE']) != '1')){
		$imageid = 0;
		if(isset($id)){
			$id = 0;
		}
	}

	$attribs = removeAttribs($attribs, array('text', 'id', 'imageid', 'to', 'nameto'));

	$link = $GLOBALS['we_doc']->getElement($name) ? unserialize($GLOBALS['we_doc']->getElement($name)) : array();

	if(!$GLOBALS['we_editmode']){
		return $GLOBALS['we_doc']->getField($attribs, 'link');
	}

	if(is_array($link)){
		if(empty($link)){
			$link = array(
				'id' => (isset($id) ? $id : ''),
				'width' => '',
				'height' => '',
				'border' => '',
				'hspace' => '',
				'vspace' => '',
				'align' => '',
				'alt' => '',
				'ctype' => ((isset($imageid) && $imageid != 0) ? we_base_link::CONTENT_INT : we_base_link::CONTENT_TEXT),
				'img_id' => ((isset($imageid) && $imageid != 0) ? $imageid : ''),
				'type' => (isset($id) ? we_base_link::TYPE_INT : we_base_link::TYPE_EXT),
				'href' => (isset($id) ? '' : we_base_link::EMPTY_EXT),
				'text' => (isset($imageid) && $imageid ? (isset($text) && $text != '' ? $text : g_l('global', '[new_link]')) : '')
			);

			// Link should only displayed if it's a preset link
			if(!empty($id) || $imageid != 0 || !empty($text)){
				$_SESSION['weS']['WE_LINK'] = serialize($link);
				$GLOBALS['we_doc']->changeLink($name);
				$GLOBALS['we_doc']->saveInSession($_SESSION['weS']['we_data'][$GLOBALS['we_transaction']]);
			}
		}

		$img = new we_imageDocument();
		$content = we_document::getLinkContent($link, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], $img, $xml);

		$startTag = $GLOBALS['we_doc']->getLinkStartTag($link, $attribs, $GLOBALS['WE_MAIN_DOC']->ParentID, $GLOBALS['WE_MAIN_DOC']->Path, $GLOBALS['DB_WE'], $img);

		$editbut = we_html_button::create_button('image:btn_edit_link', "javascript:setScrollTo(); we_cmd('edit_link', '" . $name . "')", true);
		$delbut = we_html_button::create_button('image:btn_function_trash', "javascript:setScrollTo(); we_cmd('delete_link', '" . $name . "')", true);

		return we_html_button::create_button_table(
				array(
				($startTag ? $startTag : '') . ($content ? $content : $text) . ($startTag ? '</a>' : ''), $editbut, $delbut
				), 5);
	}
	return '';
}
