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
function we_tag_categorySelect($attribs, $content){
	$name = weTag_getAttribute('name', $attribs);
	$isuserinput = empty($name);
	$name = $isuserinput ? 'we_ui_' . $GLOBALS['WE_FORM'] . '_categories' : $name;

	$type = weTag_getAttribute('type', $attribs);
	$rootdir = weTag_getAttribute('rootdir', $attribs, '/');
	$firstentry = weTag_getAttribute('firstentry', $attribs);
	$showpath = weTag_getAttribute('showpath', $attribs, false, true);
	$indent = weTag_getAttribute('indent', $attribs);
	$multiple = weTag_getAttribute('multiple', $attribs, false, true);

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
		$valuesArray = makeArrayFromCSV(id_to_path($values, CATEGORY_TABLE));
	} else {
		if($type == 'request'){
			// Bug Fix #750
			$values = filterXss(isset($_REQUEST[$name]) ?
					(is_array($_REQUEST[$name]) ?
						implode(',', $_REQUEST[$name]) :
						$_REQUEST[$name]) :
					'');
		} else {
			// Bug Fix #750
			$values = (isset($GLOBALS[$name]) && is_array($GLOBALS[$name])) ?
				implode(',', $GLOBALS[$name]) :
				$GLOBALS[$name];
		}
		$valuesArray = makeArrayFromCSV($values, CATEGORY_TABLE);
	}

	$attribs['name'] = $name;

	// Bug Fix #750
	if($multiple){
		$attribs['name'] .= '[]';
		$attribs['multiple'] = 'multiple';
	} else {
		$attribs = removeAttribs($attribs, array('size', 'multiple'));
	}

	$attribs = removeAttribs($attribs, array('showpath', 'rootdir', 'firstentry', 'type'));

	$content = trim($content);
	if(!$content){
		if($firstentry){
			$content .= getHtmlTag('option', array('value' => ''), $firstentry);
		}
		$db = $GLOBALS['DB_WE'];
		$dbfield = $showpath || $indent ? 'Path' : 'Category';
		$valueField = (weTag_getAttribute('fromTag', $attribs, false, true) ? 'ID' : 'Path');
		$db->query('SELECT ID,Path,Category FROM ' . CATEGORY_TABLE . ' WHERE ' . ($rootdir == '/' ? 1 : ' Path LIKE "' . $db->escape($rootdir) . '%"') . ' ORDER BY ' . $dbfield);
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
				$content = preg_replace('/<option>' . preg_quote($catPaths) . '( ?[<\n\r\t])/i', '<option selected="selected">' . $catPaths . '\1', $content);
			}
			$content = str_replace('<option value="' . $catPaths . '">', '<option value="' . $catPaths . '" selected="selected">', $content);
		}
	}
	return getHtmlTag('select', $attribs, $content, true);
}