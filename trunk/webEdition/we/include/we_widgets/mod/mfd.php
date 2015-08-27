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

$mode = $_SESSION['weS']['we_mode'];
$uid = $_SESSION['user']['ID'];
session_write_close();

if(!isset($aCols) || count($aCols) < 5){
	$aCols = explode(';', $aProps[3]);
}
$sTypeBinary = $aCols[0];
$pos = 0;
$bTypeDoc = permissionhandler::hasPerm('CAN_SEE_DOCUMENTS') && isset($sTypeBinary{$pos}) && ($sTypeBinary{$pos});
$pos++;
$bTypeTpl = permissionhandler::hasPerm('CAN_SEE_TEMPLATES') && isset($sTypeBinary{$pos}) && ($sTypeBinary{$pos});
$pos++;
$bTypeObj = permissionhandler::hasPerm('CAN_SEE_OBJECTFILES') && isset($sTypeBinary{$pos}) && ($sTypeBinary{$pos});
$pos++;
$bTypeCls = permissionhandler::hasPerm('CAN_SEE_OBJECTS') && isset($sTypeBinary{$pos}) && ($sTypeBinary{$pos});
$pos++;

$iDate = intval($aCols[1]);

$doctable = $where = $_users_where = $workspace = array();

switch($iDate){
	case 1 :
		$where[] = 'h.ModDate=CURDATE()';
		break;
	case 2 :
		$where[] = 'h.ModDate>=(CURDATE()-INTERVAL 1 WEEK)';
		break;
	case 3 :
		$where[] = 'h.ModDate>=(CURDATE()-INTERVAL 1 MONTH)';
		break;
	default:
	case 4 :
		$where[] = 'h.ModDate>=(CURDATE()-INTERVAL 1 YEAR)';
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
			array($uid))));

if($aUsers){
	$aUsers = implode(',', $aUsers);
	$db->query('SELECT Path FROM ' . USER_TABLE . ' WHERE ID IN (' . $aUsers . ') AND IsFolder=1');
	$folders = $db->getAll(true);
	if($folders){
		$db->query('SELECT ID FROM ' . USER_TABLE . ' WHERE IsFolder=0 AND (Path REGEXP "^(' . implode('/|', $folders) . '/)" OR ID IN (' . $aUsers . '))');
		$aUsers = implode(',', $db->getAll(true));
	}
	$where[] = 'h.UID IN (' . $aUsers . ')';
}

$join = array();
$tables = array();
$admin = permissionhandler::hasPerm('ADMINISTRATOR');

if(defined('FILE_TABLE') && $bTypeDoc && permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
	$doctable[] = '"' . stripTblPrefix(FILE_TABLE) . '"';
	$paths = array();
	$t = stripTblPrefix(FILE_TABLE);
	foreach(get_ws(FILE_TABLE, false, true) as $id){
		$paths[] = 'f.Path LIKE ("' . $db->escape(id_to_path($id, FILE_TABLE)) . '%")';
	}
	$join[] = FILE_TABLE . ' f ON (h.DocumentTable="' . $t . '" AND f.ID=h.DID ' . ($paths ? ' AND (' . implode(' OR ', $paths) . ')' : '') .
		($admin ? '' : ' AND (f.RestrictOwners=0 OR(f.RestrictOwners=1 AND (f.CreatorID=' . $_SESSION['user']['ID'] . ' OR FIND_IN_SET(' . $_SESSION['user']['ID'] . ',f.Owners))))') .
		')';
	$tables[] = 'f';
}
if(defined('OBJECT_FILES_TABLE') && $bTypeObj && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES')){
	$doctable[] = '"' . stripTblPrefix(OBJECT_FILES_TABLE) . '"';
	$paths = array();
	$t = stripTblPrefix(OBJECT_FILES_TABLE);
	foreach(get_ws(OBJECT_FILES_TABLE, false, true) as $id){
		$paths[] = 'of.Path LIKE ("' . $db->escape(id_to_path($id, OBJECT_FILES_TABLE)) . '%")';
	}
	$join[] = OBJECT_FILES_TABLE . ' of ON (h.DocumentTable="' . $t . '" AND of.ID=h.DID ' . ($paths ? ' AND (' . implode(' OR ', $paths) . ')' : '') .
		($admin ? '' : ' AND (of.RestrictOwners=0 OR(of.RestrictOwners=1 AND (of.CreatorID=' . $_SESSION['user']['ID'] . ' OR FIND_IN_SET(' . $_SESSION['user']['ID'] . ',of.Owners))))') .
		')';
	$tables[] = 'of';
}
if(defined('TEMPLATES_TABLE') && $bTypeTpl && permissionhandler::hasPerm('CAN_SEE_TEMPLATES') && $mode != we_base_constants::MODE_SEE){
	$doctable[] = '"' . stripTblPrefix(TEMPLATES_TABLE) . '"';
	$join[] = TEMPLATES_TABLE . ' t ON (h.DocumentTable="tblTemplates" AND t.ID=h.DID' .
		($admin ? '' : ' AND (t.RestrictOwners=0 OR(t.RestrictOwners=1 AND (t.CreatorID=' . $_SESSION['user']['ID'] . ' OR FIND_IN_SET(' . $_SESSION['user']['ID'] . ',t.Owners))))') .
		')';
	$tables[] = 't';
}
if(defined('OBJECT_TABLE') && $bTypeCls && permissionhandler::hasPerm('CAN_SEE_OBJECTS') && $mode != we_base_constants::MODE_SEE){
	$doctable[] = '"' . stripTblPrefix(OBJECT_TABLE) . '"';
	$join[] = OBJECT_TABLE . ' o ON (h.DocumentTable="tblObject" AND o.ID=h.DID' .
		($admin ? '' : ' AND (o.RestrictOwners=0 OR(o.RestrictOwners=1 AND (o.CreatorID=' . $_SESSION['user']['ID'] . ' OR FIND_IN_SET(' . $_SESSION['user']['ID'] . ',o.Owners))))') .
		')';
	$tables[] = 'o';
}

if($doctable){
	$where[] = 'h.DocumentTable IN(' . implode(',', $doctable) . ')';
}

if($mode == we_base_constants::MODE_SEE){
	$where[] = ' ContentType!="folder" ';
}

$where = ($where ? ' WHERE ' . implode(' AND ', $where) : '');

$db->query('SELECT h.DID,
(SELECT UserName FROM ' . HISTORY_TABLE . ' hh WHERE MAX(h.ModDate)=hh.ModDate AND hh.DID=h.DID AND h.DocumentTable=hh.DocumentTable) AS UserName,
h.DocumentTable AS ctable,
DATE_FORMAT(h.ModDate,"' . g_l('date', '[format][mysql]') . '") AS MDate,
!ISNULL(l.ID) AS isOpen,
COALESCE(' . implode('.ID,', $tables) . '.ID) AS ID,
COALESCE(' . implode('.Path,', $tables) . '.Path) AS Path,
COALESCE(' . implode('.Icon,', $tables) . '.Icon) AS Icon,
COALESCE(' . implode('.Text,', $tables) . '.Text) AS Text,
COALESCE(' . implode('.ContentType,', $tables) . '.ContentType) AS ContentType,
COALESCE(' . implode('.ModDate,', $tables) . '.ModDate) AS ModDate
FROM ' . HISTORY_TABLE . ' h
LEFT JOIN ' .
	LOCK_TABLE . ' l ON l.ID=DID AND l.tbl=h.DocumentTable AND l.UserID!=' . $uid . ($join ? ' LEFT JOIN ' . implode(' LEFT JOIN ', $join) : '') . '
' . $where . '
GROUP BY h.DID,h.DocumentTable
ORDER BY ModDate DESC LIMIT 0,' . ($iMaxItems));

$lastModified = '<table style="width:100%">';

while($db->next_record(MYSQL_ASSOC) /* && $j < $iMaxItems */){
	$file = $db->getRecord();

	$isOpen = $file['isOpen'];
	$lastModified .= '<tr><td style="width:20px;height:20px;padding-right:4px;" nowrap><img style="max-width:20px;max-height:20px" src="' . TREE_ICON_DIR . $file['Icon'] . '" />' . '</td>' .
		'<td style="vertical-align: middle;" class="middlefont" ' . ($isOpen ? 'style="color:red;"' : '') . '>' .
		($isOpen ? '' : '<a style="color:#000000;text-decoration:none;" href="javascript:top.weEditorFrameController.openDocument(\'' . addTblPrefix($db->f('ctable')) . '\',' . $file['ID'] . ',\'' . $file['ContentType'] . '\');" title="' . $file['Path'] . '" >') .
		$file['Path'] . ($isOpen ? '' : '</a>') .
		'</td>' .
		($bMfdBy ? '<td style="padding-left:.5em;" class="middlefont" nowrap>' . $file['UserName'] . (($bDateLastMfd) ? ',' : '') . '</td>' : '') .
		($bDateLastMfd ? '<td style="padding-left:.5em;" class="middlefont" nowrap>' . $file['MDate'] . '</td>' : '') .
		'</tr>';
}

$lastModified .= '</table>';
