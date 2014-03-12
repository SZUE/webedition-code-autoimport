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
$aUsers = makeArrayFromCSV($aCols[4]);
$db = $GLOBALS['DB_WE'];

if(!permissionhandler::hasPerm('EDIT_MFD_USER')){
	$aUsers = array($_SESSION['user']['ID']);
}
foreach($aUsers as $uid){
	$_users_where[] = '"' . basename(id_to_path($uid, USER_TABLE)) . '"';
}

if($aUsers){
	$where[] = 'UserName IN (' . implode(',', $_users_where) . ')';
}

if(defined('FILE_TABLE') && $bTypeDoc && permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
	$doctable[] = '"' . stripTblPrefix(FILE_TABLE) . '"';
	$paths = array();
	foreach(makeArrayFromCSV(get_ws(FILE_TABLE)) as $id){
		$paths[] = 'Path LIKE ("' . $db->escape(id_to_path($id, FILE_TABLE)) . '%")';
	}
	$workspace[FILE_TABLE] = implode(' OR ', $paths);
}
if(defined("OBJECT_FILES_TABLE") && $bTypeObj && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES')){
	$doctable[] = '"' . stripTblPrefix(OBJECT_FILES_TABLE) . '"';
	$paths = array();
	foreach(makeArrayFromCSV(get_ws(OBJECT_FILES_TABLE)) as $id){
		$paths[] = 'Path LIKE ("' . $db->escape(id_to_path($id, OBJECT_FILES_TABLE)) . '%")';
	}
	$workspace[OBJECT_FILES_TABLE] = implode(' OR ', $paths);
}
if(defined("TEMPLATES_TABLE") && $bTypeTpl && permissionhandler::hasPerm('CAN_SEE_TEMPLATES') && $_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE){
	$doctable[] = '"' . stripTblPrefix(TEMPLATES_TABLE) . '"';
}
if(defined("OBJECT_TABLE") && $bTypeCls && permissionhandler::hasPerm('CAN_SEE_OBJECTS') && $_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE){
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
$db->query('SELECT DID,UserName,DocumentTable,MAX(ModDate) AS m,!ISNULL(l.ID) AS isOpen FROM ' . HISTORY_TABLE . ' LEFT JOIN ' . LOCK_TABLE . ' l ON l.ID=DID AND l.tbl=DocumentTable AND l.UserID!=' . $_SESSION['user']['ID'] . ' ' . $where . ' GROUP BY DID,DocumentTable  ORDER BY m DESC LIMIT 0,' . ($iMaxItems + 30));
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

$lastModified = '<table cellspacing="0" cellpadding="0" border="0">';

$j = 0;

if($queries){
	$db->query(implode(' UNION ', $queries) . ' ORDER BY ModDate DESC', true);
	while($db->next_record(MYSQL_ASSOC) && $j < $iMaxItems){
		$file = $db->getRecord();
		$hist = $data[$db->f('ctable')][$db->f('ID')];

		$table = addTblPrefix($db->f('ctable'));

		$show = ($table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && ($table == OBJECT_FILES_TABLE)) ?
						we_history::userHasPerms($file['CreatorID'], $file['Owners'], $file['RestrictOwners']) :
						true);

		if($show){
			$isOpen = $hist['isOpen'];
			$lastModified .= '<tr><td width="20" height="20" valign="middle" nowrap><img src="' . ICON_DIR . $file['Icon'] . '" />' . we_html_tools::getPixel(4, 1) . '</td>' .
					'<td valign="middle" class="middlefont" ' . ($isOpen ? 'style="color:red;"' : '') . '>' .
					($isOpen ? '' : '<a href="javascript:top.weEditorFrameController.openDocument(\'' . $table . '\',' . $file['ID'] . ',\'' . $file['ContentType'] . '\');" title="' . $file['Path'] . '" style="color:#000000;text-decoration:none;">') . $file['Path'] . ($isOpen ? '' : '</a>') . '</td>' .
					($bMfdBy ? '<td>' . we_html_tools::getPixel(5, 1) . '</td><td class="middlefont" nowrap>' . $hist['UserName'] . (($bDateLastMfd) ? ',' : '') . '</td>' : '') .
					($bDateLastMfd ? '<td>' . we_html_tools::getPixel(5, 1) . '</td><td class="middlefont" nowrap>' . date(g_l('date', '[format][default]'), $file['ModDate']) . '</td>' : '') .
					'</tr>';

			$j++;
		}
	}
}

$lastModified .= '</table>';
