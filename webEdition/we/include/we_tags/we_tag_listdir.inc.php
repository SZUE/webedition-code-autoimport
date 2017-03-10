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
function we_parse_tag_listdir($attribs, $content){
	return '<?php
		' . we_tag_tagParser::printTag('listdir', $attribs) . ';
while(' . we_tag_tagParser::printTag('listdir', ['_internal' => true]) . '){
	?>' . $content . '<?php
}
unset($GLOBALS[\'we_position\'][\'listdir\']);
we_post_tag_listview();?>';
}

function we_tag_listdir(array $attribs){
	static $files = [];
	if(weTag_getAttribute('_internal', $attribs, false, we_base_request::BOOL)){
		$pos = $GLOBALS['we_position']['listdir']['position'];
		if(!isset($files[$pos])){
			return false;
		}
		$we_locfile = $files[$pos];
		$GLOBALS['lv']->field = $we_locfile['Text'];
		$GLOBALS['lv']->ID = $we_locfile['ID'];
		$GLOBALS['lv']->Path = $we_locfile['Path'];
		$GLOBALS['we_position']['listdir'] = ['position' => $pos + 1,
			'size' => count($files),
			'field' => $we_locfile['Text'],
			'id' => $we_locfile['ID'],
			'path' => $we_locfile['Path']];
		return true;
	}
	$files = [];

	$dirID = weTag_getAttribute('id', $attribs, $GLOBALS['we_doc']->ParentID, we_base_request::INT);
	$index = explode(',', weTag_getAttribute('index', $attribs, 'index.html,index.htm,index.php,default.htm,default.html,default.php', we_base_request::STRING));
	$name = weTag_getAttribute('field', $attribs, '', we_base_request::STRING);
	$dirfield = weTag_getAttribute('dirfield', $attribs, $name, we_base_request::STRING);
	$sort = weTag_getAttribute('order', $attribs, '', we_base_request::STRING);
	$desc = weTag_getAttribute('desc', $attribs, false, we_base_request::BOOL);
	$searchable = weTag_getAttribute('searchable', $attribs, false, we_base_request::BOOL);

	$indexes = ' ';
	$db = new DB_WE();

	$db->query('SELECT f.ID,f.Text,f.IsFolder,f.Path,IF(f.IsFolder,pI.ID,0) AS FolderIndex,
c.Dat AS name,
' . ($sort ? 'sc.Dat' : 'Text') . ' AS sort
FROM ' .
			FILE_TABLE . ' f JOIN ' .
			CONTENT_TABLE . ' c ON (c.DID=f.ID AND c.nHash=IF(f.IsFolder,x\'' . md5($dirfield) . '\',x\'' . md5($name) . '\')) LEFT JOIN ' .
			($sort ? CONTENT_TABLE . ' sc ON (sc.DID=f.ID AND sc.nHash=x\'' . md5($sort) . '\') LEFT JOIN ' : '') .
			FILE_TABLE . ' pI ON (pI.ParentID=f.ID AND pI.IsFolder=0 AND pI.Text IN ("' . implode('","', $index) . '") AND (pI.Published>0 ' . ($searchable ? 'AND pI.IsSearchable=1' : '') . '))

WHERE ((f.Published>0 ' . ($searchable ? 'AND f.IsSearchable=1' : '') . ') OR (f.IsFolder=1)) AND f.ParentID=' . intval($dirID) . ' GROUP BY f.ID ORDER BY ' . ($sort ? 'sort' : 'f.Text') . ($desc ? ' DESC' : ''));

	while($db->next_record(MYSQL_ASSOC)){
		$id = intval($db->f('IsFolder') ?
				$db->f('FolderIndex') :
				$db->f('ID'));

		if($id){
			$files[] = ['ID' => $db->f('ID'),
				'Path' => $db->f('Path'),
				'Text' => $db->f('Text'),
				'sort' => $db->f('sort'),
			];
		}
	}
	$GLOBALS['we_position']['listdir'] = ['position' => 0];
	//Fake listview
	we_pre_tag_listview(new stdClass());
}
