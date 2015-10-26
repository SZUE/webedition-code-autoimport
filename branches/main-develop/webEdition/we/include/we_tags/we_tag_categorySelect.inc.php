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
function we_tag_categorySelect($attribs, $content){
	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);
	$isuserinput = empty($name);
	$name = $isuserinput ? 'we_ui_' . $GLOBALS['WE_FORM'] . '_categories' : $name;

	$type = weTag_getAttribute('type', $attribs, '', we_base_request::STRING);
	$rootdir = weTag_getAttribute('rootdir', $attribs, '/', we_base_request::FILE);
	$firstentry = weTag_getAttribute('firstentry', $attribs, '', we_base_request::STRING);
	$showpath = weTag_getAttribute('showpath', $attribs, false, we_base_request::BOOL);
	$indent = weTag_getAttribute('indent', $attribs, '', we_base_request::RAW_CHECKED);
	$multiple = weTag_getAttribute('multiple', $attribs, false, we_base_request::BOOL);

	$catIDs = weTag_getAttribute('catIDs', $attribs, -1, we_base_request::INTLIST);
	$fromTag = weTag_getAttribute('fromTag', $attribs, false, we_base_request::STRING);

	$values = '';
	if($isuserinput && $GLOBALS['WE_FORM']){
		$objekt = isset($GLOBALS['we_object'][$GLOBALS['WE_FORM']]) ?
				$GLOBALS['we_object'][$GLOBALS['WE_FORM']] :
				(isset($GLOBALS['we_document'][$GLOBALS['WE_FORM']]) ?
						$GLOBALS['we_document'][$GLOBALS['WE_FORM']] :
						(isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc'] :
								false));
		if($objekt){
			$values = $objekt->Category;
		}
		$valuesArray = id_to_path($values, CATEGORY_TABLE, $GLOBALS['DB_WE'], false, true);
	} elseif($type === 'request'){
		// Bug Fix #750
		$valuesArray = we_base_request::_(we_base_request::STRING_LIST, $name, array());
	} else {
		// Bug Fix #750
		$valuesArray = (isset($GLOBALS[$name]) && is_array($GLOBALS[$name])) ?
				$GLOBALS[$name] :
				explode(',', $GLOBALS[$name]);
	}

	$attribs['name'] = $name;

	// Bug Fix #750
	if($multiple){
		$attribs['name'] .= '[]';
		$attribs['multiple'] = 'multiple';
	} else {
		$attribs = removeAttribs($attribs, array('size', 'multiple'));
	}

	$attribs = removeAttribs($attribs, array('showpath', 'rootdir', 'firstentry', 'type', 'shopCat'));

	$content = trim($content);
	if(!$content){
		if($firstentry){
			$content .= getHtmlTag('option', array('value' => ($fromTag === 'shopcategory' ? 0 : '')), $firstentry);
		}
		$db = $GLOBALS['DB_WE'];
		$dbfield = $showpath || $indent ? 'Path' : 'Category';
		$valueField = $fromTag ? 'ID' : 'Path';
		//$whereTag = !$fromTag ? '' : ($fromTag === 'shopcategory' ? ' AND IsFolder=0' : ' AND ID IN('. trim($catIDs, ',') .')');
		$whereTag = !$fromTag ? '' : ' AND ID IN(' . trim($catIDs, ',') . ')';

		$db->query('SELECT ID,Path,Category FROM ' . CATEGORY_TABLE . ' WHERE ' . ($rootdir === '/' ? 1 : ' Path LIKE "' . $db->escape(rtrim($rootdir, '/')) . '/%"') . $whereTag . ' ORDER BY ' . $dbfield);
		while($db->next_record()){
			$deep = count(explode('/', $db->f('Path'))) - 2;
			$field = ($rootdir && ($rootdir != '/') && $showpath ?
							preg_replace('|^' . preg_quote($rootdir, '|') . '|', '', $db->f($dbfield)) :
							$db->f($dbfield));

			if($field){
				$content .= getHtmlTag('option', array(
					'value' => $db->f($valueField),
					(in_array($db->f($valueField), $valuesArray) ? 'selected' : null) => 'selected'
						), str_repeat($indent, $deep) . $field);
			}
		}
	} else {
		foreach($valuesArray as $catPaths){
			if(stripos($content, '<option>') !== false){
				$content = preg_replace('/<option>' . preg_quote($catPaths) . '( ?[<\n\r\t])/i', '<option selected="selected">' . $catPaths . '${1}', $content);
			}
			$content = str_replace('<option value="' . $catPaths . '">', '<option value="' . $catPaths . '" selected="selected">', $content);
		}
	}

	return getHtmlTag('select', $attribs, $content, true);
}
