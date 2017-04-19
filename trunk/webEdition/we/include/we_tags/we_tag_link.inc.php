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
function we_tag_link(array $attribs, $content){
	if(isset($GLOBALS['we']['ll'])){
		$only = weTag_getAttribute('only', $attribs, '', we_base_request::BOOL);
		$link = $GLOBALS['we']['ll']->getLink();
		$linkcontent = $GLOBALS['we']['ll']->getLinkContent();
		if($link){
			return $GLOBALS['we']['ll']->getLinktag($link, $attribs) . ($only ? '' : $linkcontent . '</a>');
		}
		return $linkcontent;
	}
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}
	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);
	$xml = weTag_getAttribute('xml', $attribs, XHTML_DEFAULT, we_base_request::BOOL);
	$text = weTag_getAttribute('text', $attribs, '', we_base_request::STRING);
	$imageid = weTag_getAttribute('imageid', $attribs, 0, we_base_request::INT);
	$id = weTag_getAttribute('id', $attribs, 0, we_base_request::INT);

	// check if target document exists (Bug #7167)
	if($id != 0 && (!f('SELECT 1 FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id)))){
		$id = 0;
	}
	if($imageid != 0 && (!f('SELECT 1 FROM ' . FILE_TABLE . ' WHERE ID=' . intval($imageid)))){
		$imageid = 0;
		$id = 0;
	}

	$attribs = removeAttribs($attribs, array('text', 'id', 'imageid', 'to', 'nameto'));
	$data = $GLOBALS['we_doc']->getElement($name);
	$link = we_unserialize($GLOBALS['we_doc']->getElement($name));

	if(!$GLOBALS['we_editmode']){
		return $GLOBALS['we_doc']->getField($attribs, 'link');
	}

	if(is_array($link)){
		if(!$link){
			$link = array(
				'id' => (isset($id) ? $id : ''),
				'ctype' => ((!empty($imageid)) ? we_base_link::CONTENT_INT : we_base_link::CONTENT_TEXT),
				'img_id' => ((!empty($imageid) ) ? $imageid : ''),
				'type' => (isset($id) ? we_base_link::TYPE_INT : we_base_link::TYPE_EXT),
				'href' => (isset($id) ? '' : we_base_link::EMPTY_EXT),
				'text' => (!empty($imageid) ? (!empty($text) ? $text : g_l('global', '[new_link]')) : '')
			);

			// Link should only displayed if it's a preset link
			if($id || $imageid != 0 || $text){
				$_SESSION['weS']['WE_LINK'] = we_serialize($link, SERIALIZE_JSON);
				$GLOBALS['we_doc']->changeLink($name);
				$GLOBALS['we_doc']->saveInSession($_SESSION['weS']['we_data'][$GLOBALS['we_transaction']]);
			}
		}

		$img = new we_imageDocument();
		$content = we_document::getLinkContent($link, $GLOBALS['we_doc']->ParentID, $GLOBALS['we_doc']->Path, $GLOBALS['DB_WE'], $img, $xml);

		$startTag = we_document::getLinkStartTag($link, $attribs, $GLOBALS['WE_MAIN_DOC']->ParentID, $GLOBALS['WE_MAIN_DOC']->Path, $GLOBALS['DB_WE'], $img);

		$editbut = we_html_button::create_button('fa:btn_edit_link,fa-lg fa-pencil,fa-lg fa-link', "javascript:setScrollTo(); we_cmd('edit_link', '" . $name . "')", true);
		$delbut = we_html_button::create_button(we_html_button::TRASH, "javascript:setScrollTo(); we_cmd('delete_link', '" . $name . "')", true);

		return ($startTag ? : '') . ($content ? : $text) . ($startTag ? '</a>' : '') . $editbut . $delbut;
	}

	return '';
}
