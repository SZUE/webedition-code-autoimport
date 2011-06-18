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

	$name = we_getTagAttribute("name", $attribs);
	$xml = getXmlAttributeValueAsBoolean(we_getTagAttribute("xml", $attribs, ""));
	$text = we_getTagAttribute("text", $attribs, "");
	$imageid = we_getTagAttribute("imageid", $attribs, 0);
	$id = we_getTagAttribute("id", $attribs);

	// check if target document exists (Bug #7167)
	if ($id != 0) {
		if(f("SELECT count(*) as tmp FROM " . FILE_TABLE . " WHERE ID=".abs($id), 'tmp',new DB_WE()) ==0){
			$link = array();
			$id = 0;
		}
	}
	if ($imageid != 0) {
		if(f('SELECT count(*) as tmp FROM ' . FILE_TABLE . " WHERE ID=".abs($imageid), 'tmp',new DB_WE())==0){
			$link = array();
			$imageid = 0;
			if (isset($id))
				$id = 0;
		}
	}

	$attribs = removeAttribs($attribs, array(
		'text', 'id', 'imageid'
	));

	$link = $GLOBALS["we_doc"]->getElement($name) ? unserialize($GLOBALS["we_doc"]->getElement($name)) : array();
	if (!$GLOBALS['we_editmode']) {
		return $GLOBALS["we_doc"]->getField($attribs, "link");
	} else {
		if (is_array($link)) {
			if (!sizeof($link)) {
				$link = array(

						"id" => (isset($id) ? $id : ""),
						'width' => '',
						'height' => '',
						'border' => '',
						'hspace' => '',
						'vspace' => '',
						'align' => '',
						'alt' => '',
						'ctype' => ((isset($imageid) && $imageid != 0) ? "int" : "text"),
						'img_id' => ((isset($imageid) && $imageid != 0) ? $imageid : ""),
						'type' => (isset($id) ? "int" : "ext"),
						'href' => (isset($id) ? "" : "http://"),
						'text' => ((isset($imageid) && $imageid != 0 ? "" : (isset($text) && $text != "" ? $text : g_l('global',"[new_link]"))))
				);

				// Link should only displayed if it's a preset link
				if ($id != "" || $imageid != 0 || $text != "") {
					$_SESSION["WE_LINK"] = serialize($link);
					$GLOBALS['we_doc']->changeLink($name);
					$GLOBALS['we_doc']->saveInSession($_SESSION["we_data"][$GLOBALS['we_transaction']]);
				}
			}

			// Include we_imageDocument class
			include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/we_imageDocument.inc.php");

			$img = new we_imageDocument();
			$content = we_document::getLinkContent(
					$link,
					$GLOBALS["we_doc"]->ParentID,
					$GLOBALS["we_doc"]->Path,
					$GLOBALS["DB_WE"],
					$img,
					$xml);

			$startTag = $GLOBALS["we_doc"]->getLinkStartTag(
					$link,
					$attribs,
					$GLOBALS["WE_MAIN_DOC"]->ParentID,
					$GLOBALS["WE_MAIN_DOC"]->Path,
					$GLOBALS["DB_WE"],
					$img);

			// Include we_button class
			include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/html/we_button.inc.php");

			$we_button = new we_button();

			$editbut = $we_button->create_button(
					"image:btn_edit_link",
					"javascript:setScrollTo(); we_cmd('edit_link', '" . $name . "')",
					true);
			$delbut = $we_button->create_button(
					"image:btn_function_trash",
					"javascript:setScrollTo(); we_cmd('delete_link', '" . $name . "')",
					true);

			if (!$content) {
				$content = $text;
			}
			if ($startTag) {
				return $we_button->create_button_table(
						array(
							$startTag . $content . "</a>", $editbut, $delbut
						),
						5);
			} else {
				return $we_button->create_button_table(array(
					$content, $editbut, $delbut
				), 5);
			}
		}
	}
	return '';
}
