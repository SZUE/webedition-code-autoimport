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
function we_tag_path(array $attribs){
	if(isset($GLOBALS['lv']) && $GLOBALS['lv'] instanceof stdClass){//listdir
		return $GLOBALS['lv']->Path;
	}

	$db = $GLOBALS['DB_WE'];
	$field = weTag_getAttribute('field', $attribs, '', we_base_request::STRING);
	$dirfield = weTag_getAttribute('dirfield', $attribs, $field, we_base_request::STRING);
	$index = weTag_getAttribute('index', $attribs, '', we_base_request::STRING);
	$oldHtmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, false, we_base_request::BOOL);
	$fieldforfolder = weTag_getAttribute('fieldforfolder', $attribs, false, we_base_request::BOOL);
	$docAttr = weTag_getAttribute('doc', $attribs, '', we_base_request::STRING);
	$sep = weTag_getAttribute('separator', $attribs, '/', we_base_request::RAW_CHECKED);
	$home = weTag_getAttribute('home', $attribs, 'home', we_base_request::RAW_CHECKED);
	$hidehome = weTag_getAttribute('hidehome', $attribs, false, we_base_request::BOOL);
	$class = weTag_getAttribute('class', $attribs, '', we_base_request::STRING);
	$style = weTag_getAttribute('style', $attribs, '', we_base_request::STRING);
	$max = weTag_getAttribute('max', $attribs, 0, we_base_request::INT);

	$doc = we_getDocForTag($docAttr, true);
	$pID = $doc->ParentID;

	$indexArray = $index ? explode(',', $index) : array('index.html', 'index.htm', 'index.php', 'default.htm', 'default.html', 'default.php');

	$class = $class ? ' class="' . $class . '"' : '';
	$style = $style ? ' style="' . $style . '"' : '';

	$path = '';
	$q = ' Text IN ("' . implode('","', array_map('escape_sql_query', $indexArray)) . '")';
	$show = $doc->getElement($field);
	if(!in_array($doc->Text, $indexArray)){
		$show = $show? : $doc->Text;
		$path = $oldHtmlspecialchars ? oldHtmlspecialchars($sep . $show) : $sep . $show;
	}
	while($pID){
		list($fileID, $filePath, $fText) = (getHash('SELECT ID,Path,Text FROM ' . FILE_TABLE . ' WHERE ParentID=' . intval($pID) . ' AND IsFolder=0 AND ' . $q . ' AND Published>0 LIMIT 1', NULL, MYSQLI_NUM)? :
				array(0, '', '')
			);
		if($fileID){
			$show = f('SELECT c.Dat FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON c.ID=l.CID WHERE l.DocumentTable="tblFile" AND l.DID=' . intval($fileID) . ' AND l.nHash=x\'' . md5($dirfield) . '\'');
			if(!$show && $fieldforfolder){
				$show = f('SELECT c.Dat FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON c.ID=l.CID WHERE l.DocumentTable="tblFile" AND l.DID=' . intval($fileID) . ' AND l.nHash=x\'' . md5($field) . '\'');
			}
			$show = $show? : $fText;
			if($fileID != $doc->ID){
				$link_pre = '<a href="' . $filePath . '"' . $class . $style . '>';
				$link_post = '</a>';
			} else {
				$link_pre = $link_post = '';
			}
		} else {
			$link_pre = $link_post = '';
			$show = $fText;
		}
		if($max){
			$show = cutText($show, $max);
		}
		$pID = f('SELECT ParentID FROM ' . FILE_TABLE . ' WHERE ID=' . intval($pID));
		$path = (!$pID && $hidehome ? '' : $sep) . $link_pre . ($oldHtmlspecialchars ? oldHtmlspecialchars($show) : $show) . $link_post . $path;
	}

	if($hidehome){
		return $path;
	}

	$hash = getHash('SELECT ID,Path FROM ' . FILE_TABLE . ' WHERE ParentID=0 AND IsFolder=0 AND ' . $q . ' AND Published>0 LIMIT 1', null);
	$fileID = $hash ? $hash['ID'] : 0;
	$filePath = ($hash ? $hash['Path'] : '');

	if($fileID){
		$show = f('SELECT c.Dat FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON c.ID=l.CID WHERE l.DocumentTable="tblFile" AND l.DID=' . intval($fileID) . ' AND l.nHash=x\'' . md5($field) . '\'');
		if(!$show){
			$show = $home;
		}
		$link_pre = '<a href="' . $filePath . '"' . $class . $style . '>';
		$link_post = '</a>';
	} else {
		$link_pre = $link_post = '';
		$show = $home;
	}
	if($max){
		$show = cutText($show, $max);
	}

	return $link_pre . ($oldHtmlspecialchars ? oldHtmlspecialchars($show) : $show) . $link_post . $path;
}
