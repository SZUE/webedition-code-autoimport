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
class we_widget_mfd extends we_widget_base{
	private $lastModified = '';

	public function __construct($curID = '', $aProps = []){
		if(!$curID){//preview requested
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
			$this->lastModified = '';
			return;
		}

		if($doctable){
			$where[] = 'h.DocumentTable IN(' . implode(',', $doctable) . ')';
		}

		/* if($mode == we_base_constants::MODE_SEE){
		  $where[] = ' f.ContentType!="' . we_base_ContentTypes::FOLDER . '" ';
		  } */

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

		$this->lastModified = '<table class="middlefont">';

		while($db->next_record(MYSQL_ASSOC) /* && $j < $iMaxItems */){
			$file = $db->getRecord();

			$isOpen = $file['isOpen'];
			$this->lastModified .= '<tr ' . ($isOpen ? '' : 'onclick="WE().layout.weEditorFrameController.openDocument(\'' . addTblPrefix($file['ctable']) . '\',' . $file['ID'] . ',\'' . $file['ContentType'] . '\');" title="' . $file['Path'] . '"') . '><td class="mfdIcon" data-contenttype="' . $file['ContentType'] . '"></td>' .
				'<td class="mfdDoc' . ($isOpen ? ' isOpen' : '') . '" >' .
				$file['Path'] .
				'</td>' .
				($bMfdBy ? '<td class="mfdUser">' . $file['UserName'] . (($bDateLastMfd) ? ',' : '') . '</td>' : '') .
				($bDateLastMfd ? '<td class="mfdDate">' . $file['MDate'] . '</td>' : '') .
				'</tr>';
		}

		$this->lastModified .= '</table>';
	}

	public function getInsertDiv($iCurrId, we_base_jsCmd $jsCmd){
		$cfg = self::getDefaultConfig();
		$oTblDiv = we_html_element::htmlDiv(['id' => 'm_' . $iCurrId . '_inline',
				'style' => 'width:100%;height:' . ($cfg["height"]) . 'px;overflow:auto;',
				], we_html_element::htmlDiv(['id' => 'mfd_data'], $this->lastModified)
		);
		$jsCmd->addCmd('setIconOfDocClass', 'mfdIcon');
		$aLang = [g_l('cockpit', '[last_modified]'), ""];
		return [$oTblDiv, $aLang];
	}

	public static function getDefaultConfig(){
		$shortCuts = (defined('FILE_TABLE') && we_base_permission::hasPerm('CAN_SEE_DOCUMENTS') ? '1' : '0') .
			(defined('TEMPLATES_TABLE') && (we_base_permission::hasPerm('CAN_SEE_TEMPLATES')) ? '1' : '0') .
			(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm('CAN_SEE_OBJECTFILES') ? '1' : '0') .
			(defined('OBJECT_TABLE') && we_base_permission::hasPerm('CAN_SEE_OBJECTS') ? '1' : '0');

		return [
			'width' => self::WIDTH_LARGE,
			'expanded' => 1,
			'height' => 210,
			'res' => 1,
			'cls' => 'lightCyan',
			'csv' => $shortCuts . ';0;5;00;',
			'dlgHeight' => 435,
			'isResizable' => 0
		];
	}

	public static function showDialog(){
		list($jsFile, $oSelCls) = self::getDialogPrefs();

		list($sType, $iDate, $iAmountEntries, $sDisplayOpt, $sUsers) = explode(';', we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1));

		/* $textname = 'UserNameTmp';
		  $idname = 'UserIDTmp'; */
		$users = array_filter(explode(',', trim($sUsers, ',')));

		//$cmd0 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);

		$content = '<table class="default" style="width:300px;margin-bottom:2px;">
<colgroup><col style="width:20px;"/><col style="width:254px;"/><col style="width:26px;"/></colgroup>';

		if(we_base_permission::hasPerm('EDIT_MFD_USER') && $users){
			$db = new DB_WE();
			$db->query('SELECT ID,Path,(IF(IsFolder,"we/userGroup",(IF(Alias>0,"we/alias","we/user")))) AS ContentType FROM ' . USER_TABLE . ' WHERE ID IN (' . implode(',', $users) . ')');
			while($db->next_record(MYSQL_ASSOC)){
				$content .= '<tr><td class="mfdUIcon" data-contenttype="' . $db->f('ContentType') . '"></td><td class="defaultfont">' . $db->f("Path") . '</td><td>' . we_html_button::create_button(we_html_button::TRASH, "javascript:delUser('" . $db->f('ID') . "');") . '</td></tr>';
			}
		} else {
			$content .= '<tr><td class="mfdUIcon" data-contenttype="we/userGroup"></td><td class="defaultfont">' . (we_base_permission::hasPerm('EDIT_MFD_USER') ? g_l('cockpit', '[all_users]') : $_SESSION['user']['Username']) . '</td><td></td><td></td></tr>';
		}
		$content .= '</table>';

		$sUsrContent = '<table class="default" style="width:300px"><tr><td>' . we_html_element::htmlDiv(['class' => "multichooser"], $content) .
			we_html_element::htmlHiddens(["UserNameTmp" => "",
				"UserIDTmp" => ""
			]) .
			'</td></tr>' .
			(we_base_permission::hasPerm('EDIT_MFD_USER') ? '<tr><td style="text-align:right;padding-top:1em;">' .
			we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:delUser(-1)", '', 0, 0, "", "", ($users ? false : true)) .
			we_html_button::create_button(we_html_button::ADD, "javascript:getUser('we_users_selector','UserIDTmp','UserNameTmp','','','addUserToField','','',1);") .
			'</td></tr>' : '') .
			'</table>';

		$oShowUser = we_html_tools::htmlFormElementTable($sUsrContent, g_l('cockpit', '[following_users]'), "left", "defaultfont");

// Typ block
		while(strlen($sType) < 4){
			$sType .= '0';
		}
		if($sType === '0000'){
			$sType = '1111';
		}

		$oChbxDocs = (we_base_permission::hasPerm('CAN_SEE_DOCUMENTS') ?
			we_html_forms::checkbox(1, $sType{0}, "chbx_type", g_l('cockpit', '[documents]'), true, "defaultfont", "", !(defined('FILE_TABLE') && we_base_permission::hasPerm("CAN_SEE_DOCUMENTS")), '', 0, 0) :
			'<input type="hidden" name="chbx_type" value="0"/>');
		$oChbxTmpl = (we_base_permission::hasPerm('CAN_SEE_TEMPLATES') && $_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE ?
			we_html_forms::checkbox(1, $sType{1}, "chbx_type", g_l('cockpit', '[templates]'), true, "defaultfont", "", !(defined('TEMPLATES_TABLE') && we_base_permission::hasPerm('CAN_SEE_TEMPLATES')), "", 0, 0) :
			'<input type="hidden" name="chbx_type" value="0"/>'); //FIXME: this is needed for getBinary!
		$oChbxObjs = (we_base_permission::hasPerm('CAN_SEE_OBJECTFILES') ?
			we_html_forms::checkbox(1, $sType{2}, "chbx_type", g_l('cockpit', '[objects]'), true, "defaultfont", "", !(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm('CAN_SEE_OBJECTFILES')), "", 0, 0) :
			'<input type="hidden" name="chbx_type" value="0"/>');
		$oChbxCls = (we_base_permission::hasPerm('CAN_SEE_OBJECTS') && $_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE ?
			we_html_forms::checkbox(1, $sType{3}, "chbx_type", g_l('cockpit', '[classes]'), true, "defaultfont", "", !(defined('OBJECT_TABLE') && we_base_permission::hasPerm('CAN_SEE_OBJECTS')), "", 0, 0) :
			'<input type="hidden" name="chbx_type" value="0"/>');

		$oDbTableType = $oChbxDocs . $oChbxTmpl . $oChbxObjs . $oChbxCls;

		$oSctDate = new we_html_select(['name' => "sct_date", 'class' => 'defaultfont', "onchange" => ""]);
		$aLangDate = [g_l('cockpit', '[all]'),
			g_l('cockpit', '[today]'),
			g_l('cockpit', '[last_week]'),
			g_l('cockpit', '[last_month]'),
			g_l('cockpit', '[last_year]')
		];
		foreach($aLangDate as $k => $v){
			$oSctDate->insertOption($k, $k, $v);
		}
		$oSctDate->selectOption($iDate);

		$oChbxShowMfdBy = we_html_forms::checkbox(0, $sDisplayOpt{0}, "chbx_display_opt", g_l('cockpit', '[modified_by]'), true, "defaultfont", "", false, "", 0, 0);
		$oChbxShowDate = we_html_forms::checkbox(0, $sDisplayOpt{1}, "chbx_display_opt", g_l('cockpit', '[date_last_modification]'), true, "defaultfont", "", false, "", 0, 0);
		$oSctNumEntries = new we_html_select(['name' => "sct_amount_entries", 'class' => 'defaultfont']);
		$oSctNumEntries->insertOption(0, 0, g_l('cockpit', '[all]'));
		for($iCurrEntry = 1; $iCurrEntry <= 50; $iCurrEntry++){
			$oSctNumEntries->insertOption($iCurrEntry, $iCurrEntry, $iCurrEntry);
			if($iCurrEntry >= 10){
				$iCurrEntry += ($iCurrEntry == 25) ? 24 : 4;
			}
		}
		$oSctNumEntries->selectOption($iAmountEntries);

		$oSelMaxEntries = new we_html_table(["height" => "100%", 'class' => 'default'], 1, 3);
		$oSelMaxEntries->setCol(0, 0, ['class' => 'defaultfont', 'style' => 'vertical-align:middle;padding-right:5px;'], g_l('cockpit', '[max_amount_entries]'));
		$oSelMaxEntries->setCol(0, 2, ['style' => 'vertical-align:middle;'], $oSctNumEntries->getHTML());

		$show = $oSelMaxEntries->getHTML() . $oChbxShowMfdBy . $oChbxShowDate . we_html_element::htmlBr() . $oShowUser;

		$parts = [
			["headline" => g_l('cockpit', '[type]'),
				"html" => $oDbTableType,
				'space' => we_html_multiIconBox::SPACE_MED
			],
			["headline" => g_l('cockpit', '[date]'),
				"html" => $oSctDate->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED
			],
			["headline" => g_l('cockpit', '[display]'),
				"html" => $show,
				'space' => we_html_multiIconBox::SPACE_MED
			],
			["headline" => "",
				"html" => $oSelCls->getHTML(),
			]
		];

		$save_button = we_html_button::create_button(we_html_button::SAVE, "javascript:save();");
		$preview_button = we_html_button::create_button(we_html_button::PREVIEW, "javascript:preview();");
		$cancel_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:exit_close();");
		$buttons = we_html_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

		$sTblWidget = we_html_multiIconBox::getHTML('mfdProps', $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[last_modified]'));

		echo we_html_tools::getHtmlTop(g_l('cockpit', '[last_modified]'), '', '', $jsFile .
			we_html_element::jsScript(JS_DIR . 'widgets/mfd.js', '', ['id' => 'loadVarWidget', 'data-widget' => setDynamicVar([
					'sUsers' => $sUsers
			])]), we_html_element::htmlBody(
				["class" => "weDialogBody", "onload" => "init();"], we_html_element::htmlForm("", $sTblWidget)));
	}

	public function showPreview(){
		$jsCmd = new we_base_jsCmd();
		$jsCmd->addCmd('initPreview', [
			'id' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 5),
			'type' => 'mfd',
			'tb' => g_l('cockpit', '[last_modified]'),
		]);
		$jsCmd->addCmd('setIconOfDocClass', 'mfdIcon');

		echo we_html_tools::getHtmlTop(g_l('cockpit', '[last_modified]'), '', '', $jsCmd->getCmds(), we_html_element::htmlBody(
				['style' => 'margin:10px 15px;',
				"onload" => 'init();'
				], we_html_element::htmlDiv(['id' => 'mfd'], we_html_element::htmlDiv(['id' => 'mfd_data'], $this->lastModified)
		)));
	}

	public function getLastModified(){
		return $this->lastModified;
	}

}
