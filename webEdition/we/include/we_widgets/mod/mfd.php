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
// widget LAST MODIFIED

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();
if(!isset($aCols) || count($aCols) < 5){
	$aCols = explode(';', $aProps[3]);
}
$sTypeBinary = $aCols[0];
$pos = 0;
$bTypeDoc = permissionhandler::hasPerm('CAN_SEE_DOCUMENTS') && isset($sTypeBinary{$pos}) && ($sTypeBinary{$pos});
$pos++;
$bTypeTpl = permissionhandler::hasPerm('CAN_SEE_TEMPLATES') && isset($sTypeBinary{$pos}) && ($sTypeBinary{$pos});
$pos++;
$bTypeObj = permissionhandler::hasPerm('CAN_SEE_OBJECTS') && isset($sTypeBinary{$pos}) && ($sTypeBinary{$pos});
$pos++;
$bTypeCls = permissionhandler::hasPerm('CAN_SEE_CLASSES') && isset($sTypeBinary{$pos}) && ($sTypeBinary{$pos});
$pos++;

$iDate = intval($aCols[1]);

$doctable = $where = $_users_where = $workspace = array();

switch($iDate){
	case 1 :
		$where[] = 'ModDate >=CURDATE()';
		break;
	case 2 :
		$where[] = 'ModDate >=(CURDATE()-INTERVAL 1 WEEK)';
		break;
	case 3 :
		$where[] = 'ModDate >=(CURDATE()-INTERVAL 1 MONTH)';
		break;
	default:
	case 4 :
		$where[] = 'ModDate >=(CURDATE()-INTERVAL 1 YEAR)';
		break;
}
$iNumItems = $aCols[2];
switch($iNumItems){
	case 0 :
		$iMaxItems = 200;
		break;
	case 11 :
		$iMaxItems = 15;
		break;
	case 12 :
		$iMaxItems = 20;
		break;
	case 13 :
		$iMaxItems = 25;
		break;
	case 14 :
		$iMaxItems = 50;
		break;
	default :
		$iMaxItems = min(200, $iNumItems);
}
$sDisplayOpt = $aCols[3];
$bMfdBy = $sDisplayOpt{0};
$bDateLastMfd = $sDisplayOpt{1};

$db = $GLOBALS['DB_WE'];

$aUsers = array_filter(array_map('intval', (permissionhandler::hasPerm('EDIT_MFD_USER') ?
						makeArrayFromCSV($aCols[4]) :
						array($_SESSION['user']['ID']))));

if($aUsers){
	$aUsers = implode(',', $aUsers);
	$db->query('SELECT Path FROM ' . USER_TABLE . ' WHERE ID IN (' . $aUsers . ') AND IsFolder=1');
	$folders = $db->getAll(true);
	if($folders){
		$db->query('SELECT ID FROM ' . USER_TABLE . ' WHERE IsFolder=0 AND (Path REGEXP "^(' . implode('/|', $folders) . '/)" OR ID IN (' . $aUsers . '))');
	}
	$where[] = 'UID IN (' . $aUsers . ')';
}

if(defined('FILE_TABLE') && $bTypeDoc && permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
	$doctable[] = '"' . stripTblPrefix(FILE_TABLE) . '"';
	$paths = array();
	foreach(makeArrayFromCSV(get_ws(FILE_TABLE)) as $id){
		$paths[] = 'Path LIKE ("' . $db->escape(id_to_path($id, FILE_TABLE)) . '%")';
	}
	$workspace[FILE_TABLE] = implode(' OR ', $paths);
}
if(defined('OBJECT_FILES_TABLE') && $bTypeObj && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES')){
	$doctable[] = '"' . stripTblPrefix(OBJECT_FILES_TABLE) . '"';
	$paths = array();
	foreach(makeArrayFromCSV(get_ws(OBJECT_FILES_TABLE)) as $id){
		$paths[] = 'Path LIKE ("' . $db->escape(id_to_path($id, OBJECT_FILES_TABLE)) . '%")';
	}
	$workspace[OBJECT_FILES_TABLE] = implode(' OR ', $paths);
}
if(defined('TEMPLATES_TABLE') && $bTypeTpl && permissionhandler::hasPerm('CAN_SEE_TEMPLATES') && $_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE){
	$doctable[] = '"' . stripTblPrefix(TEMPLATES_TABLE) . '"';
}
if(defined('OBJECT_TABLE') && $bTypeCls && permissionhandler::hasPerm('CAN_SEE_OBJECTS') && $_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE){
	$doctable[] = '"' . stripTblPrefix(OBJECT_TABLE) . '"';
}

if($doctable){
	$where[] = 'DocumentTable IN(' . implode(',', $doctable) . ')';
}

if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
	$where[] = ' ContentType!="folder" ';
}
$where = ($where ? ' WHERE ' . implode(' AND ', $where) : '');

$tables = $data = array();
$db->query('SELECT DID,UserName,DocumentTable,ModDate,!ISNULL(l.ID) AS isOpen FROM ' . HISTORY_TABLE . ' LEFT JOIN ' . LOCK_TABLE . ' l ON l.ID=DID AND l.tbl=DocumentTable AND l.UserID!=' . $_SESSION['user']['ID'] . ' ' . $where . ' GROUP BY DID,DocumentTable  ORDER BY ModDate DESC LIMIT 0,' . ($iMaxItems + 30));
while($db->next_record(MYSQL_ASSOC)){
	$tables[$db->f('DocumentTable')][] = $db->f('DID');
	$data[$db->f('DocumentTable')][$db->f('DID')] = $db->getRecord();
}
$queries = array();
foreach($tables as $ctable => $ids){
	$table = addTblPrefix($ctable);
	$paths = ((!permissionhandler::hasPerm('ADMINISTRATOR') || ($table != TEMPLATES_TABLE && (defined('OBJECT_TABLE') ? ($table != OBJECT_TABLE) : true))) && isset($workspace[$table]) ?
					$workspace[$table] : '');

	$queries[] = '(SELECT ID,Path,Icon,Text,ContentType,ModDate,CreatorID,Owners,RestrictOwners,"' . $ctable . '" AS ctable FROM ' . $db->escape($table) . ' WHERE ID IN(' . implode(',', $ids) . ')' . ($paths ? (' AND (' . $paths . ')') : '') . ')';
}

$lastModified = '<table style="width:100%">';

$j = 0;

if($queries){
	$admin = permissionhandler::hasPerm('ADMINISTRATOR');
	$db->query(implode(' UNION ', $queries) . ' ORDER BY ModDate DESC', true);
	while($db->next_record(MYSQL_ASSOC) && $j < $iMaxItems){
		$file = $db->getRecord();
		$hist = $data[$db->f('ctable')][$db->f('ID')];

		$table = addTblPrefix($db->f('ctable'));

		$show = ($table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && ($table == OBJECT_FILES_TABLE)) ?
						$admin || we_history::userHasPerms($file['CreatorID'], $file['Owners'], $file['RestrictOwners']) :
						true);

		if($show){
			$isOpen = $hist['isOpen'];
			$lastModified .= '<tr><td style="width:20px;height:20px;padding-right:4px;" nowrap><img src="' . TREE_ICON_DIR . $file['Icon'] . '" />' . '</td>' .
					'<td style="vertical-align: middle;" class="middlefont" ' . ($isOpen ? 'style="color:red;"' : '') . '>' .
					($isOpen ? '' : '<a style="color:#000000;text-decoration:none;" href="javascript:top.weEditorFrameController.openDocument(\'' . $table . '\',' . $file['ID'] . ',\'' . $file['ContentType'] . '\');" title="' . $file['Path'] . '" >') .
					$file['Path'] . ($isOpen ? '' : '</a>') .
					'</td>' .
					($bMfdBy ? '<td style="padding-left:.5em;" class="middlefont" nowrap>' . $hist['UserName'] . (($bDateLastMfd) ? ',' : '') . '</td>' : '') .
					($bDateLastMfd ? '<td style="padding-left:.5em;" class="middlefont" nowrap>' . date(g_l('date', '[format][default]'), $hist['ModDate']) . '</td>' : '') .
					'</tr>';

			$j++;
		}
	}
}

$lastModified .= '</table>';
