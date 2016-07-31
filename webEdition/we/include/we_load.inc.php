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
$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', FILE_TABLE, 1);
$parentFolder = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2);
$offset = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 6);

function getItems($table, $ParentID, $offset = 0, $segment = 0, $collectionIDs = [], $collections = []){
	global $openFolders, $parentpaths, $wsQuery, $treeItems;
	if(($table === TEMPLATES_TABLE && !permissionhandler::hasPerm('CAN_SEE_TEMPLATES')) ||
		($table === FILE_TABLE && !permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')) ||
		($table === VFILE_TABLE && !permissionhandler::hasPerm('CAN_SEE_COLLECTIONS'))){
		return 0;
	}

	$DB_WE = new DB_WE();
	if($table == VFILE_TABLE){// TODO: permision
		$DB_WE->query('SELECT ID,remObj,remTable,position FROM ' . FILELINK_TABLE . ' WHERE DocumentTable="' . stripTblPrefix(VFILE_TABLE) . '" ORDER BY ID,position ASC');

		$docCollections = $docCollectionIDs = $objCollections = $objCollectionIDs = [];
		while($DB_WE->next_record()){
			if($DB_WE->f('remTable') === stripTblPrefix(FILE_TABLE)){
				$docCollections[$DB_WE->f('ID')] = !isset($docCollections[$DB_WE->f('ID')]) ? [] : $docCollections[$DB_WE->f('ID')];
				$docCollections[$DB_WE->f('ID')][$DB_WE->f('position')] = $DB_WE->f('remObj');
				$docCollectionIDs[] = $DB_WE->f('remObj');
			} else {
				$objectCollections[$DB_WE->f('ID')] = !isset($objectCollections[$DB_WE->f('ID')]) ? [] : $objectCollections[$DB_WE->f('ID')];
				$objectCollections[$DB_WE->f('ID')][$DB_WE->f('position')] = $DB_WE->f('remObj');
				$objCollectionIDs[] = $DB_WE->f('remObj');
			}
		}
	}

	$prevoffset = max(0, $offset - $segment);
	if($offset && $segment){
		$treeItems[] = array(
			'id' => 'prev_' . $ParentID,
			'parentid' => intval($ParentID),
			'text' => 'display (' . $prevoffset . '-' . $offset . ')',
			'contenttype' => 'arrowup',
			'isclassfolder' => 0,
			'table' => $table,
			'checked' => 0,
			'typ' => 'threedots',
			'open' => 0,
			'published' => 0,
			'disabled' => 0,
			'tooltip' => '',
			'offset' => $prevoffset
		);
	}

	$tmp = array_filter($openFolders);
	$tmp[] = $ParentID;

	$elem = 'ID,ParentID,Path,Text,IsFolder,ContentType,ModDate';
	$queryTable = $table;
	switch($table){
		case FILE_TABLE:
			$elem .=',Published,Extension,IF(Published!=0 && Published<ModDate,-1,Published) AS isPublished';
			if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
				$elem .=',st.DID IS NOT NULL AS inSchedule';
				$queryTable.=' LEFT JOIN ' . SCHEDULE_TABLE . ' st ON (st.DID=ID AND st.ClassName IN ("we_webEditionDocument","we_htmlDocument") AND st.Active=1)';
			}
			break;
		case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
			$elem .=',Published,IsClassFolder,IF(Published!=0 && Published<ModDate,-1,Published) AS isPublished';
			if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
				$elem .=',st.DID IS NOT NULL AS inSchedule';
				$queryTable.=' LEFT JOIN ' . SCHEDULE_TABLE . ' st ON (st.DID=ID AND st.ClassName="we_objectFile" AND st.Active=1)';
			}
			break;
		case TEMPLATES_TABLE:
			$elem .=',Extension,1 AS isPublished';
			break;
		case VFILE_TABLE:
			$elem .=',remTable,1 AS isPublished,1 AS IsFolder';
			break;
		default:
			$elem.=',1 AS isPublished';
			break;
	}

	$where = $collectionIDs ? ' WHERE ID IN(' . implode(',', $collectionIDs) . ') AND IsFolder=0 AND ((1' . we_users_util::makeOwnersSql() . ') ' . $wsQuery . ')' :
		' WHERE ID!=' . intval($ParentID) . ' AND ParentID IN(' . implode(',', $tmp) . ') AND ((1' . we_users_util::makeOwnersSql() . ') ' . $wsQuery . ')';
	$DB_WE->query('SELECT ' . $elem . ' FROM ' . $queryTable . ' ' . $where . ' ORDER BY IsFolder DESC,(Text REGEXP "^[0-9]") DESC,ABS(REPLACE(Text,"info","")),Text' . ($segment ? ' LIMIT ' . $offset . ',' . $segment : ''));

	$tmpItems = [];
	$tree_count = 0;
	while($DB_WE->next_record()){
		$tree_count++;
		$ID = intval($DB_WE->f('ID'));
		$Path = $DB_WE->f('Path');

		$tmpItems[$ID] = array(
			"id" => $ID,
			"we_id" => $collectionIDs ? $ID : 0,
			"parentid" => intval($DB_WE->f("ParentID")),
			"text" => $DB_WE->f("Text"),
			"contenttype" => $DB_WE->f("ContentType"),
			"isclassfolder" => intval($DB_WE->f("IsClassFolder")),
			"table" => $table,
			"checked" => 0,
			"typ" => $DB_WE->f("IsFolder") ? "group" : "item",
			"open" => (in_array($ID, $openFolders) ? 1 : 0),
			"published" => intval($DB_WE->f("isPublished")),
			"disabled" => (in_array($Path, $parentpaths) ? 1 : 0),
			"tooltip" => $ID,
			'inSchedule' => intval($DB_WE->f("inSchedule")),
			"offset" => $offset,
			"path" => $DB_WE->f("Path")
		);
	}

	if($collectionIDs){
		foreach($collections as $collectionID => $items){
			$i = 0;
			foreach($items as $itemID){
				if(isset($tmpItems[$itemID])){
					$tmpItems[$itemID]['parentid'] = $collectionID;
					$tmpItems[$itemID]['id'] = $collectionID . '_' . $i++ . '_' . $itemID;
					$treeItems[] = $tmpItems[$itemID];
				}
			}
		}
	} else {
		$treeItems = array_merge($treeItems, $tmpItems);
	}

	if($table === VFILE_TABLE){
		if(count($docCollectionIDs = array_unique($docCollectionIDs))){
			getItems(FILE_TABLE, 0, 0, 0, $docCollectionIDs, $docCollections);
		}
		if(count($objCollectionIDs = array_unique($objCollectionIDs))){
			getItems(OBJECT_FILES_TABLE, 0, 0, 0, $objCollectionIDs, $objCollections);
		}
	}

	$total = f('SELECT COUNT(1) FROM ' . $table . ' ' . $where, '', $DB_WE);
	$nextoffset = $offset + $segment;
	if($segment && $total > $nextoffset){
		$treeItems[] = array(
			"id" => "next_" . $ParentID,
			"parentid" => $ParentID,
			"text" => "display (" . $nextoffset . "-" . ($nextoffset + $segment) . ")",
			"contenttype" => "arrowdown",
			"isclassfolder" => 0,
			"table" => $table,
			"checked" => 0,
			"typ" => "threedots",
			"open" => 0,
			"published" => 0,
			"disabled" => 0,
			"tooltip" => "",
			"offset" => $nextoffset
		);
	}
}

if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === "closeFolder"){
	$openDirs = array_flip(makeArrayFromCSV($_SESSION["prefs"]["openFolders_" . stripTblPrefix($table)]));
	unset($openDirs[$parentFolder]);
	$openDirs = array_keys($openDirs);
	$_SESSION["prefs"]["openFolders_" . stripTblPrefix($table)] = implode(',', $openDirs);
} else {
	$counts = $parents = $childs = $parentpaths = $wspaces = [];
	$parentlist = $childlist = "";

	if(($ws = get_ws($table, true))){
		$wsPathArray = id_to_path($ws, $table, $DB_WE, true);

		foreach($wsPathArray as $path){
			$wspaces[] = ' Path LIKE "' . $DB_WE->escape($path) . '/%" OR ' . we_tool_treeDataSource::getQueryParents($path);
			while($path != '/' && $path != '\\' && $path){
				$parentpaths[] = $path;
				$path = dirname($path);
			}
		}
	} elseif(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE && (!permissionhandler::hasPerm("ADMINISTRATOR"))){
		if(($ac = we_users_util::getAllowedClasses($DB_WE))){
			$paths = id_to_path($ac, OBJECT_TABLE, $DB_WE, true);
			$wspaces[] = 'IsClassFolder=1';
			foreach($paths as $path){
				$wspaces[] = 'Path LIKE "' . $DB_WE->escape($path) . '/%"';
			}
		}
	}

	$wsQuery = ($wspaces ? (isset($ac) ? ' AND TableID IN (' . implode(',', $ac) . ')' : '') . ' AND (' . implode(' OR ', $wspaces) . ') ' : ' OR RestrictOwners=0 ' );

	if(($of = we_base_request::_(we_base_request::INTLIST, 'we_cmd', '', 3))){
		$openFolders = explode(',', $of);
		$_SESSION["prefs"]["openFolders_" . stripTblPrefix(we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 4))] = $of;
	}

	$openFolders = (isset($_SESSION["prefs"]["openFolders_" . stripTblPrefix($table)]) ?
			explode(',', $_SESSION["prefs"]["openFolders_" . stripTblPrefix($table)]) :
			[]);


	if($parentFolder){
		if(!in_array($parentFolder, $openFolders)){
			$openFolders[] = $parentFolder;
			$_SESSION["prefs"]["openFolders_" . stripTblPrefix($table)] = implode(",", $openFolders);
		}
	}

	if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
		$js = '';
	} else {
		$Tree = new we_tree_main("webEdition.php", "top", "top", "top.load");
		$treeItems = [];
		getItems($table, $parentFolder, $offset, $Tree->default_segment);

		switch($table){
			case FILE_TABLE:
				$name = g_l('global', '[documents]');
				break;
			case TEMPLATES_TABLE:
				$name = g_l('global', '[templates]');
				break;
			case OBJECT_FILES_TABLE:
				$name = g_l('global', '[objects]');
				break;
			case OBJECT_TABLE:
				$name = g_l('javaMenu_object', '[classes]');
				break;
			case VFILE_TABLE:
				$name = g_l('global', '[vfile]');
				break;
			default:
				$name = '';
		}

		$js = we_html_element::jsElement('
top.document.getElementById("treeName").innerHTML="' . $name . '";
function loadTreeData(){
	if(!top.treeData) {
		window.setTimeout(loadTreeData,500);
		return;
	}' .
				($parentFolder ? '' :
					'top.treeData.clear();
top.treeData.add(top.node.prototype.rootEntry(\'' . $parentFolder . '\',\'root\',\'root\',\'' . $offset . '\'));'
				) .
				$Tree->getJSLoadTree(!$parentFolder, $treeItems) . '
}
loadTreeData();
top.scrollToY();');
	}

	echo we_html_tools::getHtmlTop('File-Tree', '', '', $js, we_html_element::htmlBody());
}
we_users_user::writePrefs($_SESSION['prefs']['userID'], $GLOBALS['DB_WE']);
