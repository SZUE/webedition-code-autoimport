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
class we_widget_pad extends we_widget_base{

	public function __construct($curID = '', $aProps = []){

	}

	public function getInsertDiv($iCurrId, $aProps, we_base_jsCmd $jsCmd){
		list($pad_header_enc, $pad_csv) = explode(',', $aProps[3]);

		$iFrmPadAtts['src'] = WEBEDITION_DIR . 'we_cmd.php?' . http_build_query([
				'mod' => 'pad',
				'we_cmd' => [
					0 => 'widget_cmd',
					1 => 'reload',
					2 => $pad_csv . ' ',
					3 => ' ',
					4 => 'home',
					5 => $aProps[1] . ' ',
					6 => $pad_header_enc . ' ',
					7 => 'm_' . $iCurrId,
		]]);
		$iFrmPadAtts['id'] = 'm_' . $iCurrId . '_inline';
		$iFrmPadAtts['style'] = 'width:100%;height:287px';

		$oTblDiv = str_replace('>', ' allowtransparency="true">', getHtmlTag('iframe', $iFrmPadAtts, '', true));

		$aLang = [
			g_l('cockpit', '[notes]') . " - " . base64_decode($pad_header_enc), ""
		];
		return [$oTblDiv, $aLang];
	}

	public static function getDefaultConfig(){
		return [
			'width' => self::WIDTH_LARGE,
			'expanded' => 1,
			'height' => 307,
			'res' => 1,
			'cls' => 'blue',
			'csv' => base64_encode(g_l('cockpit', '[notepad_defaultTitle_DO_NOT_TOUCH]')) . ',30020',
			'dlgHeight' => 560,
			'isResizable' => 0
		];
	}

	public static function showDialog(){
		list($jsFile, $oSelCls, $oSctClsHTML) = self::getDialogPrefs();

		$oRdoSort = [we_html_forms::radiobutton(0, 0, "rdo_sort", g_l('cockpit', '[by_pubdate]'), true, "defaultfont", "", false, "", 0, ""),
			we_html_forms::radiobutton(1, 0, "rdo_sort", g_l('cockpit', '[by_valid_from]'), true, "defaultfont", "", false, "", 0, ""),
			we_html_forms::radiobutton(2, 0, "rdo_sort", g_l('cockpit', '[by_valid_until]'), true, "defaultfont", "", false, "", 0, ""),
			we_html_forms::radiobutton(3, 0, "rdo_sort", g_l('cockpit', '[by_priority]'), true, "defaultfont", "", false, "", 0, ""),
			we_html_forms::radiobutton(4, 1, "rdo_sort", g_l('cockpit', '[alphabetic]'), true, "defaultfont", "", false, "", 0, "")
		];

		$sort = new we_html_table(['class' => 'default'], 3, 3);
		$sort->setCol(0, 0, ["width" => 145, 'style' => 'padding-right:10px;'], $oRdoSort[0]);
		$sort->setCol(0, 2, ["width" => 145], $oRdoSort[3]);
		$sort->setCol(1, 0, null, $oRdoSort[1]);
		$sort->setCol(1, 2, null, $oRdoSort[4]);
		$sort->setCol(2, 0, null, $oRdoSort[2]);

		$oRdoDisplay = [we_html_forms::radiobutton(0, 1, "rdo_display", g_l('cockpit', '[all_notes]'), true, "defaultfont", "", false, "", 0, ""),
			we_html_forms::radiobutton(1, 0, "rdo_display", g_l('cockpit', '[only_valid]'), true, "defaultfont", "", false, "", 0, ""),
		];

		$display = new we_html_table(['class' => 'default'], 1, 3);
		$display->setCol(0, 0, ["width" => 145, 'style' => 'padding-right:10px;'], $oRdoDisplay[0]);
		$display->setCol(0, 2, ["width" => 145], $oRdoDisplay[1]);

		$oRdoDate = [we_html_forms::radiobutton(0, 1, "rdo_date", g_l('cockpit', '[by_pubdate]'), true, "defaultfont", "", false, "", 0, ""),
			we_html_forms::radiobutton(1, 0, "rdo_date", g_l('cockpit', '[by_valid_from]'), true, "defaultfont", "", false, "", 0, ""),
			we_html_forms::radiobutton(2, 0, "rdo_date", g_l('cockpit', '[by_valid_until]'), true, "defaultfont", "", false, "", 0, "")
		];

		$date = new we_html_table(['class' => 'default'], 3, 1);
		$date->setCol(0, 0, ["width" => 145], $oRdoDate[0]);
		$date->setCol(1, 0, null, $oRdoDate[1]);
		$date->setCol(2, 0, null, $oRdoDate[2]);


		$oRdoPrio = [we_html_forms::radiobutton(0, 0, "rdo_prio", g_l('cockpit', '[high]'), true, "defaultfont", "", false, "", 0, ""),
			we_html_forms::radiobutton(1, 0, "rdo_prio", g_l('cockpit', '[medium]'), true, "defaultfont", "", false, "", 0, ""),
			we_html_forms::radiobutton(2, 1, "rdo_prio", g_l('cockpit', '[low]'), true, "defaultfont", "", false, "", 0, "")
		];

		$prio = new we_html_table(['class' => 'default'], 3, 3);
		$prio->setCol(0, 0, ["width" => 70, 'style' => 'padding-right:10px;'], $oRdoPrio[0]);

		$prio->setCol(0, 2, ["width" => 20], '<i class="fa fa-dot-circle-o" style="color:red"></i>');
		$prio->setCol(1, 0, null, $oRdoPrio[1]);
		$prio->setCol(1, 2, null, '<i class="fa fa-dot-circle-o" style="color:#F2F200"></i>');
		$prio->setCol(2, 0, null, $oRdoPrio[2]);
		$prio->setCol(2, 2, null, '<i class="fa fa-dot-circle-o" style="color:green"></i>');

		$oSctValid = we_html_tools::htmlSelect("sct_valid", [g_l('cockpit', '[always]'), g_l('cockpit', '[from_date]'), g_l('cockpit', '[period]')
				], 1, g_l('cockpit', '[always]'), false, ['style' => "width:120px;", 'onchange' => ""], 'value', 120);


		list($pad_header_enc, ) = explode(',', we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1));
		$pad_header = base64_decode($pad_header_enc);
		$DB_WE = new DB_WE();
		$DB_WE->query('SELECT	distinct(WidgetName) FROM ' . NOTEPAD_TABLE . ' WHERE UserID=' . intval($_SESSION['user']['ID']));
		$options = [$pad_header => $pad_header, g_l('cockpit', '[change]') => g_l('cockpit', '[change]')
		];
		while($DB_WE->next_record()){
			$options[$DB_WE->f('WidgetName')] = $DB_WE->f('WidgetName');
		}
		$oSctTitle = we_html_tools::htmlSelect("sct_title", array_unique($options), 1, "", false, ['id' => "title", 'onchange' => ""], 'value');

		$parts = [["headline" => g_l('cockpit', '[sorting]'),
			"html" => $sort->getHTML(),
			'space' => we_html_multiIconBox::SPACE_MED
			],
			["headline" => g_l('cockpit', '[display]'),
				"html" => $display->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED
			],
			["headline" => g_l('cockpit', '[display_date]'),
				"html" => $date->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED
			],
			["headline" => g_l('cockpit', '[default_priority]'), "html" => $prio->getHTML(), 'space' => we_html_multiIconBox::SPACE_MED],
			["headline" => g_l('cockpit', '[default_validity]'), "html" => $oSctValid, 'space' => we_html_multiIconBox::SPACE_MED],
			["headline" => g_l('cockpit', '[title]'), "html" => $oSctTitle, 'space' => we_html_multiIconBox::SPACE_MED],
			["headline" => g_l('cockpit', '[bg_color]'), "html" => $oSctClsHTML, 'space' => we_html_multiIconBox::SPACE_MED]
		];



		$save_button = we_html_button::create_button(we_html_button::SAVE, "javascript:save();");
		$preview_button = we_html_button::create_button(we_html_button::PREVIEW, "javascript:preview();");
		$cancel_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:exit_close();");
		$buttons = we_html_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

		echo we_html_tools::getHtmlTop(g_l('cockpit', '[notepad]'), '', '', we_html_element::jsScript(JS_DIR . "weCombobox.js") .
			$jsFile .
			we_html_element::jsScript(JS_DIR . 'widgets/pad.js', '', ['id' => 'loadVarWidget', 'data-widget' => setDynamicVar([
					'sObjId' => we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 0),
					'sTb' => '', //($isHome ? $title : g_l('cockpit', '[notes]') . " - " . $title ),
					'sType' => 'pad',
					'sInitProps' => '' //($isHome ? $sInitProps : '')
			])]), we_html_element::htmlBody(
				["class" => "weDialogBody", "onload" => "initDlg();"
				], we_html_element::htmlForm(["onsubmit" => "return false;"
					], we_html_multiIconBox::getHTML("padProps", $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[notepad]')))));
	}

	private static function convertDate($date){
		return implode('.', array_reverse(explode('-', $date)));
	}

	private static function getDateSelector($label, $name, $btn){
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
	private static function getNoteList($sql, $bDate, $bDisplay){
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
					$showDate = ($validity === 'always' ? '-' : self::convertDate($DB_WE->f("ValidFrom")));
					break;
				case 2 :
					$showDate = ($validity === 'always' || $validity === 'date' ? '-' : self::convertDate($DB_WE->f("ValidUntil")));
					break;
				default :
					$showDate = self::convertDate($DB_WE->f("CreationDate"));
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

	public function showPreview(){
		$DB_WE = $GLOBALS['DB_WE'];
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
		$oTblValidity = self::getDateSelector(g_l('cockpit', '[from]'), "f_ValidFrom", "_from") . ' ' . self::getDateSelector(g_l('cockpit', '[until]'), "f_ValidUntil", "_until");
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
				], self::getNoteList('SELECT * FROM ' . NOTEPAD_TABLE . " WHERE
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

		$isHome = ($command === "home");
		$jsCmd = new we_base_jsCmd();
		$jsCmd->addCmd('initPreview', [
			'id' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 5),
			'type' => 'pad',
			'tb' => ($isHome ? $title : g_l('cockpit', '[notes]') . " - " . $title )
		]);

		echo we_html_tools::getHtmlTop(g_l('cockpit', '[notepad]'), '', '', we_html_element::cssLink(CSS_DIR . 'pad.css') .
			we_html_element::jsScript(JS_DIR . 'widgets/pad.js', '', ['id' => 'loadVarWidget', 'data-widget' => setDynamicVar([
					'sObjId' => we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 5),
					'sTb' => ($isHome ? $title : g_l('cockpit', '[notes]') . " - " . $title ),
					'sType' => 'pad',
					'sInitProps' => ($isHome ? $sInitProps : '')
			])]) .
			$jsCmd->getCmds(), we_html_element::htmlBody(
				[
				"onload" => 'toggleTblValidity();'
				], we_html_element::htmlForm(['style' => "display:inline;"], we_html_element::htmlDiv(
						["id" => "pad"], $notepad .
						we_html_element::htmlHidden("mark", "")
		))));
	}

}
