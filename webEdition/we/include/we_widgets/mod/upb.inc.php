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
$preview = !isset($aProps); //preview requested
if($preview){

	$aProps = array(
		0,
		0,
		0,
		we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)
	);
}
// widget UNPUBLISHED
$bTypeDoc = (bool) $aProps[3]{0};
$bTypeObj = (bool) $aProps[3]{1};
$objectFilesTable = defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : "";
$numRows = 25;

$tbls = [];
if($bTypeDoc && $bTypeObj){
	if(defined('FILE_TABLE')){
		$tbls[] = FILE_TABLE;
	}
	if(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES')){
		$tbls[] = OBJECT_FILES_TABLE;
	}
} else {
	if($bTypeDoc && defined('FILE_TABLE')){
		$tbls[] = FILE_TABLE;
	}
	if($bTypeObj && defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES')){
		$tbls[] = OBJECT_FILES_TABLE;
	}
}

$cont = [];
$db = $GLOBALS['DB_WE'];
foreach($tbls as $table){
	if(defined('WORKFLOW_TABLE')){
		$myWfDocsArray = we_workflow_utility::getWorkflowDocsForUser($_SESSION['user']['ID'], $table, permissionhandler::hasPerm('ADMINISTRATOR'), permissionhandler::hasPerm("PUBLISH"), ($table == $objectFilesTable) ? '' : get_ws($table));
		$myWfDocsCSV = implode(',', $myWfDocsArray);
		$wfDocsArray = we_workflow_utility::getAllWorkflowDocs($table, $db);
		$wfDocsCSV = implode(',', $wfDocsArray);
	} else {
		$wfDocsCSV = $myWfDocsCSV = '';
	}

	$offset = we_base_request::_(we_base_request::INT, 'offset', 0);
	$order = we_base_request::_(we_base_request::STRING, 'order', "ModDate DESC");

	#### get workspace query ###


	$parents = $childs = [];
	$parentlist = $childlist = '';

	if($table == FILE_TABLE){
		if(($wsArr = get_ws($table, true))){
			foreach($wsArr as $i){
				$parents[] = $i;
				$childs[] = $i;
				we_readParents($i, $parents, $table, 'ContentType', we_base_ContentTypes::FOLDER, $db);
				we_readChilds($i, $childs, $table, true, '', 'ContentType', we_base_ContentTypes::FOLDER, $db);
			}
			$childlist = implode(',', $childs);
			$parentlist = implode(',', $parents);

			$wsQuery = ($parentlist ? ' t.ID IN(' . $parentlist . ') ' . ($childlist ? ' OR ' : '') : '') .
				($childlist ? ' t.ParentID IN(' . $childlist . ') ' : '');
		}
	}

	#####
	$sqld = g_l('weEditorInfo', '[mysql_date_only_format]');


	$s = 'SELECT ' . ($wfDocsCSV ? "(t.ID IN($wfDocsCSV)) AS wforder," : '') . ' ' . ($myWfDocsCSV ? "(t.ID IN($myWfDocsCSV)) AS mywforder," : '') . ' '
		. 't.ContentType,t.ID,t.Text,t.ParentID,t.Path,t.ModDate,'
		. 'IF(t.Published>0,FROM_UNIXTIME(t.Published,"' . $sqld . '"),"-") AS Published,'
		. 'IF(t.ModDate>0,FROM_UNIXTIME(t.ModDate,"' . $sqld . '"),"-") AS Modified,'
		. 'IF(t.CreationDate>0,FROM_UNIXTIME(t.CreationDate,"' . $sqld . '"),"-") AS CreationDate,'
		. 'u2.username AS Modifier,'
		. 'u1.username AS Creator ';
	$q = 'FROM ' . $db->escape($table) . ' t LEFT JOIN ' . USER_TABLE . ' u1 ON u1.ID=t.CreatorID LEFT JOIN ' . USER_TABLE . ' u2 ON u2.ID=t.ModifierID ' .
		" WHERE (((t.Published=0 OR t.Published<t.ModDate) AND t.ContentType IN ('" . we_base_ContentTypes::WEDOCUMENT . "','" . we_base_ContentTypes::HTML . "','" . we_base_ContentTypes::OBJECT_FILE . "'))" .
		($myWfDocsCSV ? ' OR (t.ID IN(' . $myWfDocsCSV . ')) ' : '') . ')' .
		(isset($wsQuery) ? ' AND (' . $wsQuery . ') ' : '');
	$order = ' ORDER BY ' . ($myWfDocsCSV ? 'mywforder DESC,' : '') . $order;

	$anz = f('SELECT COUNT(1) ' . $q, '', $db);

	$db->query($s . $q . $order . ' LIMIT ' . intval($offset) . ',' . intval($numRows));
	$content = [];

	while($db->next_record()){
		$cont[$db->f("ModDate")] = $path = '<tr><td class="upbIcon" data-contenttype="' . $db->f('ContentType') . '"></td><td class="upbEntry middlefont ' . ($db->f("Published") != '-' ? 'changed' : "notpublished") . '" onclick="WE().layout.weEditorFrameController.openDocument(\'' . $table . '\',' . $db->f("ID") . ',\'' . $db->f("ContentType") . '\')" title="' . $db->f("Path") . '">' . $db->f("Path") . '</td></tr>';
		$row = array(
			array('dat' => $path),
			/* array('dat' => $db->f("Creator") ? : '-'),
			  array('dat' => $db->f('CreationDate')),
			  array('dat' => $db->f("Modifier") ? : '-'),
			  array('dat' => $db->f("Modified")),
			  array('dat' => $db->f("Published")), */
		);
		if(defined('WORKFLOW_TABLE')){
			if($db->f("wforder")){
				$step = we_workflow_utility::findLastActiveStep($db->f("ID"), $table) + 1;
				$steps = count(we_workflow_utility::getNumberOfSteps($db->f("ID"), $table));
				$row[] = array('dat' => $step . '&nbsp;' . g_l('resave', '[of]') . '&nbsp;' . $steps . '&nbsp;<i class="fa fa-lg fa-circle" style="color:#' . ($db->f("mywforder") ? '006DB8' : 'E7E7E7') . ';"></i>');
			} else {
				$row[] = array('dat' => "-");
			}
		}
		$content[] = $row;
	}
}

asort($cont);
$ct = '<table class="default">' . implode('', $cont) . '</table>';

if($preview){
	$sTb = g_l('cockpit', ($bTypeDoc && $bTypeObj ? '[upb_docs_and_objs]' : ($bTypeDoc ? '[upb_docs]' : ($bTypeObj ? '[upb_objs]' : '[upb_docs_and_objs]'))));

	$jsCode = "
var _sObjId='" . we_base_request::_(we_base_request::STRING, 'we_cmd', '', 5) . "';
var _sType='upb';
var _sTb='" . $sTb . "';

function init(){
	parent.rpcHandleResponse(_sType,_sObjId,document.getElementById(_sType),_sTb);
}
";

	echo we_html_tools::getHtmlTop(g_l('cockpit', '[unpublished]'), '', '', we_html_element::jsElement($jsCode), we_html_element::htmlBody(['style' => 'margin:10px 15px;',
			"onload" => 'if(parent!=self){init();}WE().util.setIconOfDocClass(document,"upbIcon");'
			], we_html_element::htmlDiv(["id" => "upb"
				], $ct)));
}