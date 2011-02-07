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

function we_tag_block($attribs, $content){
	global $we_editmode;

	if ($we_editmode) {
		$we_button = new we_button();
	}

	$foo = attributFehltError($attribs, 'name', 'block');
	if ($foo)
		return $foo;
	include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tagParser.inc.php');

	$name = we_getTagAttribute('name', $attribs);
	$showselect = we_getTagAttribute('showselect', $attribs, '', true, true);

	$isInListview = isset($GLOBALS['lv']);

	if ($isInListview) {
		$list = $GLOBALS['lv']->f($name);
		$GLOBALS['lv']->BlockInside = true;
	} else {
		$list = $GLOBALS['we_doc']->getElement($name);
	}

	// Bug Fix #1909 and #415
	$start = we_getTagAttribute('start', $attribs);
	$limit = we_getTagAttribute('limit', $attribs);
	if (!$list && $start) {
		$listarray = array();
		if ($limit && $limit > 0 && $limit < $start) {
			$start = $limit;
		}
		for ($i = 0; $i < $start; $i++) {
			$listarray[$i] = '_' . ($i + 1);
		}
		$list = serialize($listarray);
	}
	// Bug Fix #1909 and #415


	$blkPreName = 'blk_' . $name . '_';

	$content = str_replace('<we:ref', '<we_:_ref', $content);

	$tp = new we_tagParser();
	$tags = $tp->getAllTags($content);

	$names = implode(',', $tp->getNames($tags));

	if (strpos($content, '<we:object') === false && strpos($content, '<we:metadata') === false && strpos($content, '<we:listview') === false) { //	no we:object is used
		//	parse name of we:field
		$tp->parseTags($tags, $content, '<we_:_ref>', array());
	} else { //	we:object is used
		//	dont parse name of we:field !!!
		$tp->parseTags(
				$tags,
				$content,
				'<we_:_ref>',
				array(
					'we:field',
					'we:ifField',
					'we:ifNotField',
					'we:ifFieldEmpty',
					'we:ifFieldNotEmpty',
					'we:ifPageLanguage',
					'we:ifNotPageLanguage',
					'we:ifObjectLanguage',
					'we:ifNotObjectLanguage'
				));
	}
	$out = '';

	$tmpname = md5(uniqid(time()));

	$noButCode = '';
	if ($list) {

		$listarray = unserialize($list);
		$listlen = sizeof($listarray);

		if ($listlen != 0) {

			if (!$we_editmode) {
				if ($limit > 0 && $listlen > $limit) {
					$listlen = $limit;
				}
			}

			for ($i = 0; $i < $listlen; $i++) {
				$listRef = $blkPreName . $listarray[$i];

				$foo = $content;

				$foo = str_replace('<we_:_ref>', $listRef, $foo);

				//	handle we:ifPosition:
				if (strpos($foo, 'position') || strpos($foo, 'ifPosition') || strpos(
						$foo,
						'ifNotPosition')) { //	set information for ifPosition


					$foo = '<?php $GLOBALS[\'we_position\'][\'block\'][\'' . $name . '\'] = array(\'position\' => ' . ($i + 1) . ', \'size\'=>' . $listlen . '); ?>' . $foo . '<?php unset($GLOBALS[\'we_position\'][\'block\'][\'' . $name . '\']); ?>';
				}

				$noButCode .= $foo;
				if ($we_editmode) {

					$show = 10;
					if ($limit && $limit > 0) {
						$diff = $limit - $listlen;
						if ($diff > 0) {
							$show = min($show, $diff);
						} else {
							$show = 0;
						}
					}
					$selectb = '<select name="' . $tmpname . '_' . $i . '">';
					for ($j = 0; $j < $show; $j++) {
						$selectb .= '<option value="' . ($j + 1) . '">' . ($j + 1) . '</option>';
					}
					$selectb .= '</select>';

					$upbut = $we_button->create_button('image:btn_direction_up',
							"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('up_entry_at_list','$name','$i')");
					$upbutDis = $we_button->create_button('image:btn_direction_up', '', true, 21, 22, '', '', true);
					$downbut = $we_button->create_button('image:btn_direction_down',
							"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('down_entry_at_list','$name','$i')");
					$downbutDis = $we_button->create_button('image:btn_direction_down',
							'',true,21,22,'','',true);
					if ($showselect && $show > 0) {
						$plusbut = $we_button->create_button('image:btn_add_listelement',
								"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('insert_entry_at_list','$name','$i',document.we_form.elements['" . $tmpname . "_" . $i . "'].options[document.we_form.elements['" . $tmpname . "_" . $i . "'].selectedIndex].text)",
								true,100,22,'','',($show > 0 ? false : true));
					} else {
						$plusbut = $we_button->create_button('image:btn_add_listelement',
								"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('insert_entry_at_list','$name','$i',1)",
								true,100,22,'','',($show > 0 ? false : true));
					}
					$trashbut = $we_button->create_button('image:btn_function_trash',
							"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('delete_list','$name','$i','$names',1)");
					$buts = '';

					if (!$isInListview) {

						if ($showselect) {

							$buts = $we_button->create_button_table(
									array(

											$plusbut,
											$selectb,
											(($i > 0) ? $upbut : $upbutDis),
											(($i < ($listlen - 1)) ? $downbut : $downbutDis),
											$trashbut
									),
									5);
						} else {
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
					if (preg_match('/^< ?td/i', trim($foo)) || preg_match('/^< ?tr/i', trim($foo))) {
						$foo = str_replace('=>', '#####PHPCALSSARROW####', $foo);
						$foo = str_replace('?>', '#####PHPENDBRACKET####', $foo);
						$foo = preg_replace('|(< ?td[^>]*>)(.*)(< ?/ ?td[^>]*>)|i', '$1' . $buts . '$2$3', $foo,1);
						$foo = str_replace('#####PHPCALSSARROW####', '=>', $foo);
						$foo = str_replace('#####PHPENDBRACKET####', '?>', $foo);
					} else {
						$foo = $buts . $foo;
					}
				}
				$out .= $foo;
			}
		}
	}
	if ($we_editmode) {

		$show = 10;
		if ($limit && $limit > 0) {
			$diff = $limit - (isset($listlen) ? $listlen : 0);
			if ($diff > 0) {
				$show = min($show, $diff);
			} else {
				$show = 0;
			}
		}

		if ($show > 0) {
			$selectb = '<select name="' . $tmpname . '_00">';
			for ($j = 0; $j < $show; $j++) {
				$selectb .= '<option value="' . ($j + 1) . '">' . ($j + 1) . '</option>';
			}
			$selectb .= '</select>';

			if ($showselect) {
				$plusbut = $we_button->create_button('image:btn_add_listelement',
						"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('add_entry_to_list','$name',document.we_form.elements['" . $tmpname . "_00'].options[document.we_form.elements['" . $tmpname . "_00'].selectedIndex].text)",
						true,100,22,'','',($show > 0 ? false : true));
				$plusbut = $we_button->create_button_table(array(
					$plusbut, $selectb
				));
			} else {
				$plusbut = $we_button->create_button('image:btn_add_listelement',
						"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('add_entry_to_list','$name',1)",
						true,100,22,'','',($show > 0 ? false : true));
			}

			if (preg_match('/^< ?td/i', $content) || preg_match('/^< ?tr/i', $content)) {
				$foo = makeEmptyTable(rmPhp($content));
				$plusbut = preg_replace('|(< ?td[^>]*>)(.*)(< ?/ ?td[^>]*>)|i', '$1$2' . $plusbut . '$3', $foo,1);
			} else {
				$plusbut = '<p>' . $plusbut;
			}
			$out .= (!$isInListview) ? ('<input type="hidden" name="we_' . $GLOBALS["we_doc"]->Name . '_list[' . $name . ']" value="' . htmlentities(
					$list) . '"><input type="hidden" name="we_' . $GLOBALS["we_doc"]->Name . '_list[' . $name . '#content]" value="' . htmlspecialchars(
					$content) . '" />' . $plusbut) : '';
		}

	}

	//	When in SEEM - Mode add edit-Button to tag - textarea
	return $out;
}
