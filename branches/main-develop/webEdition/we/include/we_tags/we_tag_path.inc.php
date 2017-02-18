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
	$realPID = 0;

	$indexArray = $index ? explode(',', $index) : ['index.html', 'index.htm', 'index.php', 'default.htm', 'default.html', 'default.php'];

	$class = $class ? ' class="' . $class . '"' : '';
	$style = $style ? ' style="' . $style . '"' : '';

	$path = (!in_array($doc->Text, $indexArray) ?
		$sep . ($oldHtmlspecialchars ? oldHtmlspecialchars(($doc->getElement($field) ?: $doc->Text)) : ($doc->getElement($field) ?: $doc->Text)) :
		'');

	$q = '"' . implode('","', array_map('escape_sql_query', $indexArray)) . '"';
	$mdf = md5($dirfield);
	$mf = md5($field);

	while($pID){
		list($fileID, $filePath, $fileName, $show) = (getHash('SELECT
	f.ID,f.Path,f.Text,c.Dat
FROM ' .
				FILE_TABLE . ' f LEFT JOIN ' .
				LINK_TABLE . ' l ON (l.DID=f.ID AND l.DocumentTable="tblFile" AND l.nHash=x\'' . $mdf . '\') LEFT JOIN ' .
				CONTENT_TABLE . ' c ON c.ID=l.CID
WHERE
	f.ParentID=' . intval($pID) . ' AND
	f.IsFolder=0 AND
	f.Text IN (' . $q . ') AND
	f.Published>0 LIMIT 1', NULL, MYSQLI_NUM) ?:
			[0, '', '']
			);

		if($fileID){
			if(!$show && $fieldforfolder){
				$show = f('SELECT
	c.Dat FROM ' .
					LINK_TABLE . ' l JOIN ' .
					CONTENT_TABLE . ' c ON c.ID=l.CID
WHERE
	l.DocumentTable="tblFile" AND
	l.DID=' . intval($fileID) . ' AND
	l.nHash=x\'' . $mf . '\'');
			}
			$show = $show ?: $fileName;
			$link_pre = ($fileID != $doc->ID ? '<a href="' . $filePath . '"' . $class . $style . '>' : '');
		} else {
			$link_pre = '';
			//set show empty, we get it when we query the parent
			$show = '';
		}

		//for multidomains we stop if we find a "document root"
		list($pID, $realPID, $folderName) = getHash('SELECT IF(urlMap,0,ParentID),ParentID,Text FROM ' . FILE_TABLE . ' WHERE ID=' . intval($pID), null, MYSQL_NUM);
		//if no name is given, take the folder name
		$show = $show ?: $folderName;

		$cutted = ($max ? cutText($show, $max) : $show);

		$path = (!$pID && $hidehome ? '' : $sep) .
			$link_pre .
			($oldHtmlspecialchars ? oldHtmlspecialchars($cutted) : $cutted) .
			($link_pre ? '</a>' : '') . $path;
	}

	if($hidehome){
		return $path;
	}

	list($fileID, $filePath, $show) = getHash('SELECT
	f.ID,
	f.Path,
	c.Dat
FROM ' .
			FILE_TABLE . ' f LEFT JOIN ' .
			LINK_TABLE . ' l ON (l.DID=f.ID AND l.DocumentTable="tblFile" AND l.nHash=x\'' . $mf . '\') LEFT JOIN ' .
			CONTENT_TABLE . ' c ON c.ID=l.CID
WHERE
	f.ParentID=' . $realPID . ' AND
	f.IsFolder=0 AND
	f.Text IN (' . $q . ') AND
	f.Published>0 LIMIT 1', null, MYSQLI_NUM) ?: [0, ''];

	$show = $show ?: $home;
	$link_pre = ($fileID ? '<a href="' . $filePath . '"' . $class . $style . '>' : '');

	$cutted = ($max ? cutText($show, $max) : $show);

	return $link_pre . ($oldHtmlspecialchars ? oldHtmlspecialchars($cutted) : $cutted) . ($link_pre ? '</a>' : '') . $path;
}
