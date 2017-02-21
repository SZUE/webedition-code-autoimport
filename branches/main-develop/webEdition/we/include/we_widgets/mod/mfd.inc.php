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
if(!isset($aProps)){//preview requested
	$aCols = we_base_request::_(we_base_request::STRING, 'we_cmd');
}
$mode = $_SESSION['weS']['we_mode'];
$uid = $_SESSION['user']['ID'];
session_write_close();

if(!isset($aCols) || count($aCols) < 5){
	$aCols = explode(';', $aProps[3]);
}
$sTypeBinary = $aCols[0];
$pos = 0;
$bTypeDoc = defined('FILE_TABLE') && we_base_permission::hasPerm('CAN_SEE_DOCUMENTS') && isset($sTypeBinary{$pos}) && ($sTypeBinary{$pos});
$pos++;
$bTypeTpl = defined('TEMPLATES_TABLE') && we_base_permission::hasPerm('CAN_SEE_TEMPLATES') && isset($sTypeBinary{$pos}) && ($sTypeBinary{$pos});
$pos++;
$bTypeObj = defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm('CAN_SEE_OBJECTFILES') && isset($sTypeBinary{$pos}) && ($sTypeBinary{$pos});
$pos++;
$bTypeCls = defined('OBJECT_TABLE') && we_base_permission::hasPerm('CAN_SEE_OBJECTS') && isset($sTypeBinary{$pos}) && ($sTypeBinary{$pos});
$pos++;

$iDate = intval($aCols[1]);

$doctable = $where = $workspace = [];

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

$aUsers = array_filter(array_map('intval', (we_base_permission::hasPerm('EDIT_MFD_USER') ?
		makeArrayFromCSV($aCols[4]) :
		[$uid])));

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

$join = $tables = [];
$admin = we_base_permission::hasPerm('ADMINISTRATOR');

if($bTypeDoc){
	$doctable[] = '"' . stripTblPrefix(FILE_TABLE) . '"';
	$paths = [];
	$t = stripTblPrefix(FILE_TABLE);
	foreach(get_ws(FILE_TABLE, true) as $id){
		$paths[] = 'f.Path LIKE ("' . $db->escape(id_to_path($id, FILE_TABLE)) . '%")';
	}
	$join[] = FILE_TABLE . ' f ON (h.DocumentTable="' . $t . '" AND f.ID=h.DID ' . ($paths ? ' AND (' . implode(' OR ', $paths) . ')' : '') .
		($admin ? '' : ' AND (f.RestrictOwners=0 OR f.CreatorID=' . $_SESSION['user']['ID'] . ' OR FIND_IN_SET(' . $_SESSION['user']['ID'] . ',f.Owners))') .
		')';
	$tables[] = 'f';
}
if($bTypeObj){
	$doctable[] = '"' . stripTblPrefix(OBJECT_FILES_TABLE) . '"';
	$paths = [];
	$t = stripTblPrefix(OBJECT_FILES_TABLE);
	foreach(get_ws(OBJECT_FILES_TABLE, true) as $id){
		$paths[] = 'of.Path LIKE ("' . $db->escape(id_to_path($id, OBJECT_FILES_TABLE)) . '%")';
	}
	$join[] = OBJECT_FILES_TABLE . ' of ON (h.DocumentTable="' . $t . '" AND of.ID=h.DID ' . ($paths ? ' AND (' . implode(' OR ', $paths) . ')' : '') .
		($admin ? '' : ' AND (of.RestrictOwners=0 OR of.CreatorID=' . $_SESSION['user']['ID'] . ' OR FIND_IN_SET(' . $_SESSION['user']['ID'] . ',of.Owners))') .
		')';
	$tables[] = 'of';
}
if($bTypeTpl && $mode != we_base_constants::MODE_SEE){
	$doctable[] = '"' . stripTblPrefix(TEMPLATES_TABLE) . '"';
	$join[] = TEMPLATES_TABLE . ' t ON (h.DocumentTable="tblTemplates" AND t.ID=h.DID' .
		($admin ? '' : ' AND (t.RestrictOwners=0 OR t.CreatorID=' . $_SESSION['user']['ID'] . ' OR FIND_IN_SET(' . $_SESSION['user']['ID'] . ',t.Owners))') .
		')';
	$tables[] = 't';
}
if($bTypeCls && $mode != we_base_constants::MODE_SEE){
	$doctable[] = '"' . stripTblPrefix(OBJECT_TABLE) . '"';
	$join[] = OBJECT_TABLE . ' o ON (h.DocumentTable="tblObject" AND o.ID=h.DID' .
		($admin ? '' : ' AND (o.RestrictOwners=0 OR(o.RestrictOwners=1 AND (o.CreatorID=' . $_SESSION['user']['ID'] . ' OR FIND_IN_SET(' . $_SESSION['user']['ID'] . ',o.Owners))))') .
		')';
	$tables[] = 'o';
}

if(!$tables){
	$lastModified = '';
	return;
}

if($doctable){
	$where[] = 'h.DocumentTable IN(' . implode(',', $doctable) . ')';
}

/*if($mode == we_base_constants::MODE_SEE){
	$where[] = ' f.ContentType!="' . we_base_ContentTypes::FOLDER . '" ';
}*/

$where = ($where ? ' WHERE ' . implode(' AND ', $where) : '');

$db->query('SELECT h.DID,
(SELECT UserName FROM ' . HISTORY_TABLE . ' hh WHERE MAX(h.ModDate)=hh.ModDate AND hh.DID=h.DID AND h.DocumentTable=hh.DocumentTable) AS UserName,
h.DocumentTable AS ctable,
DATE_FORMAT(h.ModDate,"' . g_l('date', '[format][mysql]') . '") AS MDate,
!ISNULL(l.ID) AS isOpen,
COALESCE(' . implode('.ID,', $tables) . '.ID) AS ID,
COALESCE(' . implode('.Path,', $tables) . '.Path) AS Path,
COALESCE(' . implode('.Text,', $tables) . '.Text) AS Text,
COALESCE(' . implode('.ContentType,', $tables) . '.ContentType) AS ContentType,
COALESCE(' . implode('.ModDate,', $tables) . '.ModDate) AS ModDate
FROM ' . HISTORY_TABLE . ' h
LEFT JOIN ' .
	LOCK_TABLE . ' l ON l.ID=DID AND l.tbl=h.DocumentTable AND l.UserID!=' . $uid . ($join ? ' LEFT JOIN ' . implode(' LEFT JOIN ', $join) : '') . '
' . $where . '
GROUP BY h.DID,h.DocumentTable
ORDER BY ModDate DESC LIMIT 0,' . ($iMaxItems));

$lastModified = '<table class="middlefont">';

while($db->next_record(MYSQL_ASSOC) /* && $j < $iMaxItems */){
	$file = $db->getRecord();

	$isOpen = $file['isOpen'];
	$lastModified .= '<tr ' . ($isOpen ? '' : 'onclick="WE().layout.weEditorFrameController.openDocument(\'' . addTblPrefix($file['ctable']) . '\',' . $file['ID'] . ',\'' . $file['ContentType'] . '\');" title="' . $file['Path'] . '"') . '><td class="mfdIcon" data-contenttype="' . $file['ContentType'] . '"></td>' .
		'<td class="mfdDoc' . ($isOpen ? ' isOpen' : '') . '" >' .
		$file['Path'] .
		'</td>' .
		($bMfdBy ? '<td class="mfdUser">' . $file['UserName'] . (($bDateLastMfd) ? ',' : '') . '</td>' : '') .
		($bDateLastMfd ? '<td class="mfdDate">' . $file['MDate'] . '</td>' : '') .
		'</tr>';
}

$lastModified .= '</table>';
if(isset($aProps)){//normal mode
	return $lastModified;
}
//preview mode

echo we_html_tools::getHtmlTop(g_l('cockpit', '[last_modified]'), '', '', we_html_element::jsScript(JS_DIR . 'widgets/preview.js', '', [
		'id' => 'loadVarPreview',
		'data-preview' => setDynamicVar([
			'id' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 5),
			'type' => 'mfd',
			'tb' => g_l('cockpit', '[last_modified]'),
			'iconClass' => 'mfdIcon'
	])]), we_html_element::htmlBody(
		['style' => 'margin:10px 15px;',
		"onload" => 'init();'
		], we_html_element::htmlDiv(['id' => 'mfd'], we_html_element::htmlDiv(['id' => 'mfd_data'], $lastModified)
)));
