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

/**
 * Converts the date from ##-##-#### to ##.##.####
 *
 * @param unknown_type $date
 * @return unknown
 */
function convertDate($date){
	return implode('.', array_reverse(explode('-', $date)));
}

/**
 * Creates the HTML code for the date picker button
 *
 * @param unknown_type $_label
 * @param unknown_type $_name
 * @param unknown_type $_btn
 * @return unknown
 */
function getDateSelector($_label, $_name, $_btn){
	$btnDatePicker = we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, $_btn);
	$oSelector = new we_html_table(array(
		"cellpadding" => 0, "cellspacing" => 0, "border" => 0, "id" => $_name . "_cell"
		), 1, 5);
	$oSelector->setCol(0, 0, array(
		"class" => "middlefont"
		), $_label);
	$oSelector->setCol(0, 1, null, we_html_tools::getPixel(5, 1));
	$oSelector->setCol(0, 2, null, we_html_tools::htmlTextInput($_name, 55, "", 10, 'id="' . $_name . '" readonly="1"', "text", 70, 0));
	$oSelector->setCol(0, 3, null, we_html_tools::getPixel(5, 1));
	$oSelector->setCol(0, 4, null, we_html_element::htmlA(array(
			"href" => "#"
			), $btnDatePicker));
	return $oSelector->getHTML();
}

/**
 * Creates the HTML code with the note list
 *
 * @param unknown_type $_sql
 * @param unknown_type $bDate
 * @return unknown
 */
function getNoteList($_sql, $bDate, $bDisplay){
	global $DB_WE;
	$DB_WE->query($_sql);
	$_notes = '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
	$_rcd = 0;
	$_fields = array(
		'ID',
		'WidgetName',
		'UserID',
		'CreationDate',
		'Title',
		'Text',
		'Priority',
		'Valid',
		'ValidFrom',
		'ValidUntil'
	);
	while($DB_WE->next_record()){
		foreach($_fields as $_fld){
			$dbf = $DB_WE->f($_fld);
			$_fldValue = ($_fld == 'ValidUntil' && ($dbf == '3000-01-01' || $dbf == '0000-00-00' || empty($dbf)) ?
					'' : $dbf);

			$_fldValue = CheckAndConvertISObackend(str_replace(array('<', '>', '\'', '"'), array('&lt;', '&gt;', '&#039;', '&quot;'), $_fldValue));
			$_notes .= we_html_element::htmlHidden(
					array(
						'id' => $_rcd . '_' . $_fld,
						'style' => 'display:none;',
						'value' => $_fldValue
			));
		}

		$validity = $DB_WE->f("Valid");
		switch($bDate){
			case 1 :
				$showDate = ($validity == 'always' ? '-' : convertDate($DB_WE->f("ValidFrom")));
				break;
			case 2 :
				$showDate = ($validity == 'always' || $validity == 'date' ? '-' : convertDate($DB_WE->f("ValidUntil")));
				break;
			default :
				$showDate = convertDate($DB_WE->f("CreationDate"));
		}

		$today = date("Ymd");
		$vFrom = str_replace('-', '', $DB_WE->f("ValidFrom"));
		$vTill = str_replace('-', '', $DB_WE->f("ValidUntil"));
		if($bDisplay == 1 && $DB_WE->f("Valid") != 'always'){
			if($DB_WE->f('Valid') == 'date'){
				if($today < $vFrom){
					continue;
				}
			} else {
				if($today < $vFrom || $today > $vTill){
					continue;
				}
			}
		}
		$showTitle = str_replace(array('<', '>', '\'', '"'), array('&lt;', '&gt;', '&#039;', '&quot;'), $DB_WE->f("Title"));
		$_notes .= '<tr style="cursor:pointer;" id="' . $_rcd . '_tr" onmouseover="fo=document.forms[0];if(fo.elements[\'mark\'].value==\'\'){setColor(this,' . $_rcd . ',\'#EDEDED\');}" onmouseout="fo=document.forms[0];if(fo.elements[\'mark\'].value==\'\'){setColor(this,' . $_rcd . ',\'#FFFFFF\');}" onmousedown="selectNote(' . $_rcd . ');">
		<td width="5">' . we_html_tools::getPixel(5, 1) . '</td>
		<td width="15" height="20" valign="middle" nowrap>' . we_html_element::htmlImg(
				array(
					"src" => IMAGE_DIR . "pd/prio_" . $DB_WE->f("Priority") . ".gif",
					"width" => 13,
					"height" => 14
			)) . '</td>
		<td width="5">' . we_html_tools::getPixel(5, 1) . '</td>
		<td width="60" valign="middle" class="middlefont" align="center">' . $showDate . '</td>
		<td width="5">' . we_html_tools::getPixel(5, 1) . '</td>
		<td valign="middle" class="middlefont">' . CheckAndConvertISObackend($showTitle) . '</td>
		<td width="5">' . we_html_tools::getPixel(5, 1) . '</td>
		</tr>';
		$_rcd++;
	}
	$_notes .= '</table>';
	return $_notes;
}

function getCSS(){
	return '
	body{
		background-color:transparent;
	}
	.cl_notes{
		background-color:#FFFFFF;
	}
	#notices{
		position:relative;
		top:0px;
		display:block;
		height:250px;
		overflow:auto;
	}
	#props{
		position:absolute;
		bottom:0px;
		display:none;
	}
	#view{
		position:absolute;
		bottom:0px;
		display:block;
		height:22px;
	}
	.wetextinput{
		color:black;
		border:#AAAAAA solid 1px;
		height:18px;
		vertical-align:middle;
		' . (we_base_browserDetect::isIE() ? '' : 'line-height:normal;') . ';
		font-size:' . ((we_base_browserDetect::isMAC()) ? "10px" : ((we_base_browserDetect::isUNIX()) ? "12px" : "11px")) . ';
		font-family:' . g_l('css', '[font_family]') . ';
	}
	.wetextinputselected{
		color:black;
		border:#888888 solid 1px;
		background-color:#DCE6F2;
		height:18px;
		' . (we_base_browserDetect::isIE() ? "" : "line-height:normal;") . ';
		font-size:' . ((we_base_browserDetect::isMAC()) ? "10px" : ((we_base_browserDetect::isUNIX()) ? "12px" : "11px")) . ";
		font-family:" . g_l('css', '[font_family]') . ';
	}
	.wetextarea{
		color:black;
		border:#AAAAAA solid 1px;
		height:80px;
		' . (we_base_browserDetect::isIE() ? "" : "line-height:normal;") . ";
		font-size:" . ((we_base_browserDetect::isMAC()) ? "10px" : ((we_base_browserDetect::isUNIX()) ? "12px" : "11px")) . ";
		font-family:" . g_l('css', '[font_family]') . ';
	}
	.wetextareaselected{
		color:black;
		border:#888888 solid 1px;
		background-color:#DCE6F2;
		height:80px;
		' . (we_base_browserDetect::isIE() ? "" : "line-height:normal;") . ";
		font-size:" . ((we_base_browserDetect::isMAC()) ? "10px" : ((we_base_browserDetect::isUNIX()) ? "12px" : "11px")) . ";
		font-family:" . g_l('css', '[font_family]') . ';
	}
	select{
		border:#AAAAAA solid 1px;
	}';
}