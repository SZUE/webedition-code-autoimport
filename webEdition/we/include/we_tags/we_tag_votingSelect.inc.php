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
function we_tag_votingSelect(array $attribs){
	$db = $GLOBALS['DB_WE'];

	if($GLOBALS['we_editmode'] && isset($GLOBALS['_we_voting']) && isset($GLOBALS['_we_voting_namespace'])){
		$submitonchange = weTag_getAttribute('submitonchange', $attribs, false, we_base_request::BOOL);
		$firstentry = weTag_getAttribute('firstentry', $attribs, '', we_base_request::STRING);
		$reload = weTag_getAttribute('reload', $attribs, $submitonchange, we_base_request::BOOL);
		$parentid = weTag_getAttribute('parentid', $attribs, 0, we_base_request::INT);

		$select_name = $GLOBALS['_we_voting_namespace'];

		$newAttribs = ['name' => 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $select_name . ']'
		];

		$val = oldHtmlspecialchars($GLOBALS['we_doc']->issetElement($select_name) ? $GLOBALS['we_doc']->getElement($select_name) : 0);

		$newAttribs['onchange'] = ($submitonchange ?
				'we_submitForm();' :
				'_EditorFrame.setEditorIsHot(true)' . ($reload ? (';setScrollTo();top.we_cmd(\'reload_editpage\');') : '') );

		$options = (isset($attribs['firstentry']) ? getHtmlTag('option', ['value' => ''], $firstentry, true) : '');

		$hasOpt = false;
		if($parentid){
			$db->query('SELECT ID FROM ' . VOTING_TABLE . ' WHERE IsFolder=1 AND ParentID=' . $parentid);
			$folders = $db->getAll(true);
			$folders[] = $parentid;
		}

		$db->query('SELECT ID,Text,Path,IsFolder FROM ' . VOTING_TABLE . ' WHERE 1 ' . ($parentid ? ' AND ParentID IN(' . $folders . ')' : '') . we_voting_voting::getOwnersSql() . ' ORDER BY Path');
		while($db->next_record()){
			if($db->f('IsFolder')){
				$options.=($hasOpt ? '</optgroup>' : '') . '<optgroup label="' . $db->f('Path') . '">';
				$hasOpt = true;
				continue;
			}
			$options .= getHtmlTag('option', ($db->f('ID') == $val ? ['value' => $db->f("ID"), 'selected' => 'selected'] : ['value' => $db->f('ID')]), $db->f('Text'));
		}
		return getHtmlTag('select', $newAttribs, $options . ($hasOpt ? '</optgroup>' : ''), true);
	}
}
