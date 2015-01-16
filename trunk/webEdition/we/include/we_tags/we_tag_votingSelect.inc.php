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
function we_tag_votingSelect($attribs){
	global $DB_WE;

	if($GLOBALS['we_editmode'] && isset($GLOBALS['_we_voting']) && isset($GLOBALS['_we_voting_namespace'])){
		$firstentry = weTag_getAttribute('firstentry', $attribs, '', we_base_request::RAW);
		$submitonchange = weTag_getAttribute('submitonchange', $attribs, false, we_base_request::BOOL);
		$reload = weTag_getAttribute('reload', $attribs, false, we_base_request::BOOL);
		if($submitonchange){
			$reload = true;
		}

		$select_name = $GLOBALS['_we_voting_namespace'];

		$newAttribs = array(
			'name' => 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $select_name . ']'
		);

		$val = oldHtmlspecialchars($GLOBALS['we_doc']->issetElement($select_name) ? $GLOBALS['we_doc']->getElement($select_name) : 0);

		$newAttribs['onchange'] = ($submitonchange ?
				'we_submitForm();' :
				'_EditorFrame.setEditorIsHot(true)' . ($reload ? (';setScrollTo();top.we_cmd(\'reload_editpage\');') : '') );

		$options = (isset($attribs['firstentry']) ? getHtmlTag('option', array('value' => ''), $firstentry, true) : '');

		$hasOpt = false;
		$DB_WE->query('SELECT ID,Text,Path,IsFolder FROM ' . VOTING_TABLE . ' WHERE 1 ' . we_voting_voting::getOwnersSql() . ' ORDER BY Path');
		while($DB_WE->next_record()){
			if($DB_WE->f('IsFolder')){
				$options.=($hasOpt ? '</optgroup>' : '') . '<optgroup label="' . $DB_WE->f('Path') . '">';
				$hasOpt = true;
				continue;
			}
			$options .= getHtmlTag('option', ($DB_WE->f('ID') == $val ? array('value' => $DB_WE->f("ID"), 'selected' => 'selected') : array('value' => $DB_WE->f('ID'))), $DB_WE->f('Text'));
		}
		return getHtmlTag('select', $newAttribs, $options . ($hasOpt ? '</optgroup>' : ''), true);
	}
}
