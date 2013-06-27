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
function we_parse_tag_listdir($attribs, $content){
	$content = preg_replace(array(
		'|"<\?php printElement\(we_tag\(\'field\',array\(\.*\)\)\);[ \t]*\?>"|Us', //inside tag
		'|"<\?php printElement\(we_tag\(\'id\',array\(\.*\)\)\);[ \t]*\?>"|Us', //inside tag
		'|"<\?php printElement\(we_tag\(\'path\',array\(\.*\)\)\);[ \t]*\?>"|Us', //inside tag
		'|we_tag\(\'field\',array\(\.*\)\)|Us',
		'|we_tag\(\'id\',array\(\.*\)\)|Us',
		'|we_tag\(\'path\',array\(\.*\)\)|Us',
		'|(we_tag\(\'a\',array\()(.*\).*\))|Us',
		'|(we_tag\(\'ifSelf\',array\()(\.*\).*\))|Us',
		'|(we_tag\(\'ifNotSelf\',array\()(\.*\).*\))|Us',
		), array(
		'$we_locfield',
		'$we_locid',
		'$we_locpath',
		'$we_locfield',
		'$we_locid',
		'$we_locpath',
		'\1\'id\'=>$we_locid,\2',
		'\1\'id\'=>$we_locid,\2',
		'\1\'id\'=>$we_locid,\2',
		), $content);

	return '<?php $we_locfiles=' . we_tag_tagParser::printTag('listdir', $attribs) . ';
foreach($we_locfiles as $we_locpos=>$we_locfile){
	$we_locfield=$we_locfile[\'name\'];
	$we_locid=$we_locfile[\'ID\'];
	$we_locpath=$we_locfile[\'Path\'];
	$GLOBALS[\'we_position\'][\'listdir\'] = array(\'position\' => ($we_locpos + 1), \'size\' => count($we_locfiles), \'field\' => $we_locfield, \'id\' => $we_locid, \'path\' => $we_locpath);
	?>' . $content . '<?php
}
unset($we_locfiles);unset($we_locfield);unset($we_locid);unset($we_locpath);unset($GLOBALS[\'we_position\'][\'listdir\']);?>';
}

function we_tag_listdir($attribs, $content){
	$dirID = weTag_getAttribute('id', $attribs, $GLOBALS['we_doc']->ParentID);
	$index = explode(',', weTag_getAttribute('index', $attribs, 'index.html,index.htm,index.php,default.htm,default.html,default.php'));
	$name = weTag_getAttribute('field', $attribs);
	$dirfield = weTag_getAttribute('dirfield', $attribs, $name);
	$sort = weTag_getAttribute('order', $attribs, $name);
	$desc = weTag_getAttribute('desc', $attribs, false, true);

	$q = array();
	foreach($index as $i => $v){
		$q[] = ' Text="' . $v . '"';
	}
	$q = implode(' OR ', $q);

	$files = array();

	$db = new DB_WE();
	$db2 = $GLOBALS['DB_WE'];

	$db->query('SELECT ID,Text,IsFolder,Path FROM ' . FILE_TABLE . ' WHERE ((Published > 0 AND IsSearchable = 1) OR (IsFolder = 1)) AND ParentID=' . intval($dirID));

	while($db->next_record()) {
		$sortfield = $namefield = '';
		$id = intval($db->f('IsFolder') ?
				f('SELECT ID FROM ' . FILE_TABLE . ' WHERE ParentID=' . intval($db->f('ID')) . ' AND IsFolder = 0 AND (' . $q . ') AND (Published > 0 AND IsSearchable = 1)', 'ID', $db2) :
				$db->f('ID'));

		if($id){
			$files[] = array(
				'ID' => $db->f('ID'),
				'Path'=>$db->f('Path'),
				'Text'=>$db->f('Text'),
				'sort' => _listdir_getSortField($sort, $id, $db->f('Text')),
				'name' => _listdir_getNameField($dirfield, $id, $db->f('Text'))
			);
		}
	}

	usort($files, ($sort ? 'we_cmpField' : 'we_cmpText') . ($desc ? 'Desc' : ''));
	return $files;
}

function _listdir_getSortField($sort, $id, $text){
	if($sort){
		$db = $GLOBALS['DB_WE'];
		$dat = f('SELECT ' . CONTENT_TABLE . '.Dat as Dat FROM ' . LINK_TABLE . ',' . CONTENT_TABLE . ' WHERE ' . LINK_TABLE . '.DID=' . intval($id) . ' AND ' . LINK_TABLE . '.Name="' . $db->escape($sort) . '" AND ' . CONTENT_TABLE . '.ID=' . LINK_TABLE . '.CID', 'Dat', $db);
		return $dat ? $dat : $text;
	} else{
		return $text;
	}
}

function _listdir_getNameField($dirfield, $id, $text){
	if($dirfield){
		$db = $GLOBALS['DB_WE'];
		$dat = f('SELECT ' . CONTENT_TABLE . '.Dat as Dat FROM ' . LINK_TABLE . ',' . CONTENT_TABLE . ' WHERE ' . LINK_TABLE . '.DID=' . intval($id) . ' AND ' . LINK_TABLE . '.Name="' . $db->escape($dirfield) . '" AND ' . CONTENT_TABLE . '.ID=' . LINK_TABLE . '.CID', 'Dat', $db);
		return $dat ? $dat : $text;
	} else{
		return $text;
	}
}

function we_cmpText($a, $b){
	$x = strtolower(correctUml($a['Text']));
	$y = strtolower(correctUml($b['Text']));
	if($x == $y){
		return 0;
	}
	return ($x < $y) ? -1 : 1;
}

function we_cmpTextDesc($a, $b){
	$x = strtolower(correctUml($a['Text']));
	$y = strtolower(correctUml($b['Text']));
	if($x == $y){
		return 0;
	}
	return ($x > $y) ? -1 : 1;
}

function we_cmpField($a, $b){
	$x = strtolower(correctUml($a['sort']));
	$y = strtolower(correctUml($b['sort']));
	if($x == $y){
		return 0;
	}
	return ($x < $y) ? -1 : 1;
}

function we_cmpFieldDesc($a, $b){
	$x = strtolower(correctUml($a['sort']));
	$y = strtolower(correctUml($b['sort']));
	if($x == $y){
		return 0;
	}
	return ($x > $y) ? -1 : 1;
}