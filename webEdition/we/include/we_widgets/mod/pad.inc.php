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
function convertDate($date){
	return implode('.', array_reverse(explode('-', $date)));
}

/**
 * Creates the HTML code for the date picker button
 *
 * @param unknown_type $label
 * @param unknown_type $name
 * @param unknown_type $btn
 * @return unknown
 */
function getDateSelector($label, $name, $btn){
	//FIXME: convert this to we_html_tools::getDateSelector
	return we_html_element::htmlSpan(['class' => 'default', 'id' => $name . '_cell'], $label .
			we_html_tools::htmlTextInput($name, 55, '', 10, 'id="' . $name . '" class="wetextinput datepicker" readonly="readonly"', "text", 70, 0) .
			we_html_button::create_button(we_html_button::CALENDAR, "javascript:$('#" . $name . "').datepicker('show');", null, null, null, null, null, null, false, $btn)
	);
}

/**
 * Creates the HTML code with the note list
 *
 * @param unknown_type $sql
 * @param unknown_type $bDate
 * @return unknown
 */
function getNoteList($sql, $bDate, $bDisplay){
	global $DB_WE;
	$DB_WE->query($sql);
	$notes = '<table style="width:100%;padding:0px 5px;" class="default">';
	$rcd = 0;
	$fields = ['ID',
		'WidgetName',
		'UserID',
		'CreationDate',
		'Title',
		'Text',
		'Priority',
		'Valid',
		'ValidFrom',
		'ValidUntil'
	];
	while($DB_WE->next_record()){
		foreach($fields as $fld){
			$dbf = $DB_WE->f($fld);

			$fldValue = CheckAndConvertISObackend(str_replace(['<', '>', '\'', '"'], ['&lt;', '&gt;', '&#039;', '&quot;'], ($fld === 'ValidUntil' && ($dbf === '3000-01-01' || $dbf === '0000-00-00' || !$dbf) ? '' : $dbf)));
			$notes .= we_html_element::htmlHidden($rcd . '_' . $fld, $fldValue, $rcd . '_' . $fld);
		}

		$validity = $DB_WE->f("Valid");
		switch($bDate){
			case 1 :
				$showDate = ($validity === 'always' ? '-' : convertDate($DB_WE->f("ValidFrom")));
				break;
			case 2 :
				$showDate = ($validity === 'always' || $validity === 'date' ? '-' : convertDate($DB_WE->f("ValidUntil")));
				break;
			default :
				$showDate = convertDate($DB_WE->f("CreationDate"));
		}

		$today = date("Ymd");
		$vFrom = str_replace('-', '', $DB_WE->f("ValidFrom"));
		$vTill = str_replace('-', '', $DB_WE->f("ValidUntil"));
		if($bDisplay == 1 && $DB_WE->f("Valid") != 'always'){
			if($DB_WE->f('Valid') === 'date'){
				if($today < $vFrom){
					continue;
				}
			} else {
				if($today < $vFrom || $today > $vTill){
					continue;
				}
			}
		}
		$showTitle = strtr($DB_WE->f("Title"), ['<' => '&lt;', '>' => '&gt;', '\'' => '&#039;', '"' => '&quot;']);
		switch($DB_WE->f("Priority")){
			case 'high':
				$color = 'red';
				break;
			case 'medium':
				$color = 'yellow';
				break;
			case 'low':
				$color = 'green';
				break;
		}
		$notes .= '<tr style="cursor:pointer;" id="' . $rcd . '_tr" onmouseover="fo=document.forms[0];if(fo.elements.mark.value==\'\'){setColor(this,' . $rcd . ',\'#EDEDED\');}" onmouseout="if(document.forms[0].elements.mark.value==\'\'){setColor(this,' . $rcd . ',\'#FFFFFF\');}" onmousedown="selectNote(' . $rcd . ');">
		<td style="width:15px;height:20px;vertical-align:middle"><i class="fa fa-dot-circle-o" style="color:' . $color . '"></i></td>
		<td style="width:60px;padding-left:5px;vertical-align:middle;text-align:center" class="middlefont">' . $showDate . '</td>
		<td style="padding-left:5px;vertical-align:middle" class="middlefont">' . CheckAndConvertISObackend($showTitle) . '</td>
		</tr>';
		$rcd++;
	}
	$notes .= '</table>';
	return $notes;
}

we_html_tools::protect();
/**
 * Table with the notes
 * @var string
 */
$sInitProps = substr(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0), -5); //binary data
$bSort = $sInitProps{0};
$bDisplay = $sInitProps{1};
$bDate = $sInitProps{2};
$bPrio = $sInitProps{3};
$bValid = $sInitProps{4};
$title = base64_decode(we_base_request::_(we_base_request::RAW, 'we_cmd', '', 4));
$command = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2);

switch($command){
	case 'delete' :
		$DB_WE->query('DELETE FROM ' . NOTEPAD_TABLE . ' WHERE ID=' . we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1));
		break;
	case 'update' :
		list($q_ID, $q_Title, $q_Text, $q_Priority, $q_Valid, $q_ValidFrom, $q_ValidUntil) = explode(';', we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1));
		$entTitle = strtr(base64_decode($q_Title), ["'" => '&#039;', '"' => '&quot;']);
		$entText = strtr(base64_decode($q_Text), ["'" => '&#039;', '"' => '&quot;']);
		if($q_Valid === "always" || $q_Valid === "date"){
			$q_ValidUntil = "3000-01-01";
		}
		$DB_WE->query('UPDATE ' . NOTEPAD_TABLE . ' SET ' . we_database_base::arraySetter(['Title' => $entTitle,
				'Text' => $entText,
				'Priority' => $q_Priority,
				'Valid' => $q_Valid,
				'ValidFrom' => $q_ValidFrom,
				'ValidUntil' => $q_ValidUntil]) . ' WHERE ID = ' . intval($q_ID));
		break;
	case 'insert' :
		list($q_Title, $q_Text, $q_Priority, $q_Valid, $q_ValidFrom, $q_ValidUntil) = explode(';', we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1));
		if($q_Valid === "always"){
			$q_ValidUntil = "3000-01-01";
			$q_ValidFrom = date("Y-m-d");
		} elseif($q_Valid === "date"){
			$q_ValidUntil = "3000-01-01";
		}

		$entTitle = strtr(base64_decode($q_Title), ["'" => '&#039;', '"' => '&quot;']);
		$entText = strtr(base64_decode($q_Text), ["'" => '&#039;', '"' => '&quot;']);
		$DB_WE->query('INSERT INTO ' . NOTEPAD_TABLE . ' SET ' . we_database_base::arraySetter(['WidgetName' => $title,
				'UserID' => intval($_SESSION['user']['ID']),
				'CreationDate' => sql_function('CURDATE()'),
				'Title' => $entTitle,
				'Text' => $entText,
				'Priority' => $q_Priority,
				'Valid' => $q_Valid,
				'ValidFrom' => $q_ValidFrom,
				'ValidUntil' => $q_ValidUntil
		]));
		break;
}

switch($bSort){
	case 1 :
		$q_sort = 'Priority DESC, Title';
		break;
	case 2 :
		$q_sort = 'ValidFrom, Title';
		break;
	case 3 :
		$q_sort = 'Title';
		break;
	case 4 :
		$q_sort = 'ValidUntil, Title';
		break;
	default :
		$q_sort = 'CreationDate, Title';
}

// validity settings
$sctValid = we_html_tools::htmlSelect("sct_valid", [g_l('cockpit', '[always]'), g_l('cockpit', '[from_date]'), g_l('cockpit', '[period]')
		], 1, g_l('cockpit', '[always]'), false, ['style' => "width:100px;", 'onchange' => "toggleTblValidity()"], 'value', 100, 'middlefont');
$oTblValidity = getDateSelector(g_l('cockpit', '[from]'), "f_ValidFrom", "_from") . ' ' . getDateSelector(g_l('cockpit', '[until]'), "f_ValidUntil", "_until");
$oTblPeriod = new we_html_table(["width" => "100%", 'class' => 'default'], 1, 2);
$oTblPeriod->setCol(0, 0, ['class' => "middlefont"], $sctValid);
$oTblPeriod->setCol(0, 1, ['style' => "text-align:right"], $oTblValidity);

// Edit note prio settings
$rdoPrio = [we_html_forms::radiobutton(0, 0, "rdo_prio", '<i class="fa fa-dot-circle-o" style="color:red;margin-left:5px;" title="' . g_l('cockpit', '[high]') . '" ></i>', true, "middlefont", "", false, "", 0, ""),
	we_html_forms::radiobutton(1, 0, "rdo_prio", '<i class="fa fa-dot-circle-o" style="color:#F2F200;margin-left:5px;" title="' . g_l('cockpit', '[medium]') . '"></i>', true, "middlefont", "", false, "", 0, ""),
	we_html_forms::radiobutton(2, 1, "rdo_prio", '<i class="fa fa-dot-circle-o" style="color:green;margin-left:5px;" title="' . g_l('cockpit', '[low]') . '"></i>', true, "middlefont", "", false, "", 0, "")
];
$oTblPrio = new we_html_table(['class' => 'default'], 1, 3);
$oTblPrio->setCol(0, 0, null, $rdoPrio[0]);
$oTblPrio->setCol(0, 1, null, $rdoPrio[1]);
$oTblPrio->setCol(0, 2, null, $rdoPrio[2]);

// Edit note buttons
$delete_button = we_html_button::create_button(we_html_button::DELETE, "javascript:deleteNote();", '', 0, 0, "", "", true, false);
$cancel_button = we_html_button::create_button(we_html_button::CANCEL, "javascript:cancelNote();");
$save_button = we_html_button::create_button(we_html_button::SAVE, "javascript:saveNote();");
$buttons = we_html_button::position_yes_no_cancel($delete_button, $cancel_button, $save_button);

// Edit note dialog
$oTblProps = new we_html_table(["width" => "100%", 'class' => 'default'], 9, 2);
$oTblProps->setCol(0, 0, ['class' => "middlefont", 'style' => "padding-bottom:8px;"], g_l('cockpit', '[valid]') . '&nbsp;');
$oTblProps->setCol(0, 1, ["colspan" => 2, 'style' => "text-align:right"], $oTblPeriod->getHTML());
$oTblProps->setCol(2, 0, ['class' => "middlefont", 'style' => "padding-bottom:8px;"], g_l('cockpit', '[prio]'));
$oTblProps->setCol(2, 1, null, $oTblPrio->getHTML());
$oTblProps->setCol(4, 0, ['class' => "middlefont", 'style' => "padding-bottom:8px;"], g_l('cockpit', '[title]'));
$oTblProps->setCol(4, 1, null, we_html_tools::htmlTextInput("props_title", 255, "", 255, "", "text", "100%", 0));
$oTblProps->setCol(6, 0, ['class' => "middlefont", 'style' => 'vertical-align:top;padding-bottom:8px;'], g_l('cockpit', '[note]'));
$oTblProps->setCol(6, 1, null, we_html_element::htmlTextArea([
		'name' => 'props_text',
		'id' => 'previewCode',
		'style' => 'width:100%;height:70px;',
		'class' => 'wetextinput',
		], ""));
$oTblProps->setCol(8, 0, ["colspan" => 3], $buttons);

// Button: add note
$oTblBtnProps = we_html_button::create_button('fa:btn_add_note,fa-plus,fa-lg fa-newspaper-o', "javascript:displayNote();");

// Table with the note list
$oPad = new we_html_table([
	'style' => "table-layout:fixed;width:100%;padding-top:6px;padding-bottom:6px;background-color:white;",
	'class' => 'default'
	], 1, 1);

$oPad->setCol(0, 0, ["colspan" => 3, "class" => "cl_notes"], we_html_element::htmlDiv(["id" => "notices"
		], getNoteList( 'SELECT * FROM ' . NOTEPAD_TABLE . " WHERE
		WidgetName = '" . $GLOBALS['DB_WE']->escape($title) . "' AND
		UserID = " . intval($_SESSION['user']['ID']) .
	($bDisplay ?
	" AND (
			Valid = 'always' OR (
				Valid = 'date' AND ValidFrom <= DATE_FORMAT(NOW(), \"%Y-%m-%d\")
			) OR (
				Valid = 'period' AND ValidFrom <= DATE_FORMAT(NOW(), \"%Y-%m-%d\") AND ValidUntil >= DATE_FORMAT(NOW(), \"%Y-%m-%d\")
			)
		)" : ''
	) .
	' ORDER BY ' . $q_sort, $bDate, $bDisplay)));

$notepad = $oPad->getHTML() .
	we_html_element::htmlDiv(["id" => "props"], $oTblProps->getHTML()) .
	we_html_element::htmlDiv(["id" => "view"], $oTblBtnProps);

echo we_html_tools::getHtmlTop(g_l('cockpit', '[notepad]'), '', '', we_html_element::cssLink(CSS_DIR . 'pad.css') .
	we_html_element::jsElement("
var _sObjId='" . we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 5) . "';
" .
		(($command === "home") ? "
var _sTb='" . $title . "';
var _sInitProps='" . $sInitProps . "';
" : "
var _sCls_=parent.document.getElementById(_sObjId+'_cls').value;
var _sType='pad';
var _sTb='" . g_l('cockpit', '[notes]') . " - " . $title . "';
") . "
var _ttlB64Esc=escape(window.btoa(_sTb));
") . we_html_element::jsScript(JS_DIR . 'widgets/pad.js'), we_html_element::htmlBody(
		[
		"onload" => (($command !== "home") ? "if(parent!=self){init();}" : "") . 'toggleTblValidity();'
		], we_html_element::htmlForm(['style' => "display:inline;"], we_html_element::htmlDiv(
				["id" => "pad"], $notepad .
				we_html_element::htmlHidden("mark", "")
))));
