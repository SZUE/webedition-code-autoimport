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

function we_tag_list($attribs, $content){
	include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/" . "we_tagParser.inc.php");
	global $we_editmode;

	if ($we_editmode)
		$we_button = new we_button();

	$foo = attributFehltError($attribs, "name", "list");
	if ($foo)
		return $foo;
	$name = we_getTagAttribute("name", $attribs);
	$content = eregi_replace('<we:ref ?/?>', '<we_:_ref>', $content);
	$tp = new we_tagParser();
	$tags = $tp->getAllTags($content);
	$names = implode(",", $tp->getNames($tags));
	$isInListview = isset($GLOBALS["lv"]);
	if ($isInListview) {
		$list = $GLOBALS["lv"]->f($name);
	} else {
		$list = $GLOBALS["we_doc"]->getElement($name);
	}
	$out = "";
	if ($list) {
		$listarray = unserialize($list);
		$listlen = sizeof($listarray);
		for ($i = 0; $i < $listlen; $i++) {
			$listRef = $listarray[$i];
			$foo = $content;

			$foo = eregi_replace('<we_:_ref>', $listRef, $foo);
			$tp->parseTags($tags, $foo, $listRef);

			$buts = "";
			if ($we_editmode) {
				$upbut = $we_button->create_button(
						"image:btn_direction_up",
						"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('up_entry_at_list','$name','$i');");
				$upbutDis = $we_button->create_button("image:btn_direction_up", "#", true, 21, 22, "", "", true);
				$downbut = $we_button->create_button(
						"image:btn_direction_down",
						"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('down_entry_at_list','$name','$i');");
				$downbutDis = $we_button->create_button("image:btn_direction_down", "", true, 21, 22, "", "", true);
				$plusbut = $we_button->create_button(
						"image:btn_add_listelement",
						"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('insert_entry_at_list','$name','$i');");
				$trashbut = $we_button->create_button(
						"image:btn_function_trash",
						"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('delete_list','$name','$i','$names');");

				if (!$isInListview) {

					$buts = $we_button->create_button_table(
							array(

									$plusbut,
									(($i > 0) ? $upbut : $upbutDis),
									(($i < ($listlen - 1)) ? $downbut : $downbutDis),
									$trashbut
							),
							5);
				}
			}

			if (eregi('^< ?td', $foo) || eregi('^< ?tr', $foo)) {
				$foo = eregi_replace('(< ?td[^>]*>)(.*)(< ?/ ?td[^>]*>)', '\1' . $buts . '\2\3', $foo);
			} else {
				$foo = $buts . $foo;
			}
			$out .= $foo;

		}
	}
	if ($we_editmode) {
		$plusbut = $we_button->create_button(
				"image:btn_add_listelement",
				"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('add_entry_to_list','$name')");

		if (eregi('^< ?td', $content) || eregi('^< ?tr', $content)) {
			$foo = makeEmptyTable($content);
			$plusbut = eregi_replace('(< ?td[^>]*>)(.*)(< ?/ ?td[^>]*>)', '\1\2' . $plusbut . '\3', $foo);
		} else {
			$plusbut = "<p>" . $plusbut;
		}
	}

	$out .= ((!$isInListview) && $we_editmode) ? ('<input type="hidden" name="we_' . $GLOBALS["we_doc"]->Name . '_list[' . $name . ']" value="' . htmlspecialchars(
			$list) . '" /><input type="hidden" name="we_' . $GLOBALS["we_doc"]->Name . '_list[' . $name . '#content]" value="' . htmlspecialchars(
			$content) . '" />' . $plusbut) : '';
	//	When in SEEM - Mode add edit-Button to tag - textarea
	return $out;
}
