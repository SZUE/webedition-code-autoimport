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
class we_widget_fdl extends we_widget_base{
	private $maxRows = 0;
	private $failedLoginHTML = '';
	private $newSCurrId = '';

	public function __construct($curID = '', $aProps = []){
		if(!(defined('CUSTOMER_TABLE') && we_base_permission::hasPerm('CAN_SEE_CUSTOMER'))){
			return;
		}
		$db = $GLOBALS['DB_WE'];

		$failedLoginsTable = new we_html_table(['class' => 'default',], 1, 4);

		$queryFailedLogins = ' FROM ' . FAILED_LOGINS_TABLE . ' f LEFT JOIN ' . CUSTOMER_TABLE . ' c ON f.Username=c.Username	WHERE f.UserTable="tblWebUser" AND f.isValid="true" AND f.LoginDate>(NOW() - INTERVAL ' . intval(SECURITY_LIMIT_CUSTOMER_NAME_HOURS) . ' hour) ' .
			(!we_base_permission::hasPerm("ADMINISTRATOR") && $_SESSION['user']['workSpace'][CUSTOMER_TABLE] ? ' AND ' . $_SESSION['user']['workSpace'][CUSTOMER_TABLE] : '');


		if(($this->maxRows = f('SELECT COUNT(DISTINCT f.Username) ' . $queryFailedLogins, '', $db))){
			$failedLoginsTable->addRow();
			$failedLoginsTable->setCol(0, 0, ['style' => 'width:25px;']);
			$failedLoginsTable->setCol(0, 1, ['class' => "middlefont", 'style' => "text-align:left"], we_html_element::htmlB(g_l('cockpit', '[kv_failedLogins][username]')));
			$failedLoginsTable->setCol(0, 2, ['class' => "middlefont", 'style' => "text-align:left"], we_html_element::htmlB(g_l('cockpit', '[kv_failedLogins][numberLogins]')));

			$cur = 0;
//	while($this->maxRows > $cur){
			$db->query('SELECT f.Username, COUNT(f.isValid) AS numberFailedLogins,c.ID AS UID' . $queryFailedLogins . ' GROUP BY f.Username LIMIT ' . $cur . ',100');
			$i = 1;
			while($db->next_record()){
				$webUserID = $db->f('UID');
				$prio = (intval($db->f('numberFailedLogins')) >= SECURITY_LIMIT_CUSTOMER_NAME) || !$webUserID ? 'red' : 'green';
				$failedLoginsTable->addRow();
				$failedLoginsTable->setCol($i, 0, ['class' => "middlefont", 'style' => "text-align:center"], '<i class="fa fa-dot-circle-o" style="color:' . $prio . '"></i>');
				$failedLoginsTable->setCol($i, 1, ['class' => "middlefont", 'style' => "text-align:left"], $db->f('Username'));
				$failedLoginsTable->setCol($i, 2, ['class' => "middlefont", 'style' => "text-align:left"], intval($db->f('numberFailedLogins')) . ' / ' . SECURITY_LIMIT_CUSTOMER_NAME . ' ' . sprintf(g_l('cockpit', '[kv_failedLogins][logins]'), SECURITY_LIMIT_CUSTOMER_NAME_HOURS));

				$buttonJSFunction = 'WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR+"rpc.php?cmd=ResetFailedCustomerLogins&cns=customer","custid=' . $webUserID . '", ajaxCallbackResetLogins );';
				$failedLoginsTable->setCol($i, 3, ['class' => "middlefont", 'style' => "text-align:right"], ((intval($db->f('numberFailedLogins')) >= SECURITY_LIMIT_CUSTOMER_NAME && $webUserID) ? we_html_button::create_button('reset', "javascript:" . $buttonJSFunction) : ''));
				$i++;
			}
			//$cur+=1000;
			//}
		} else {
			$this->maxRows = 0;
			$failedLoginsTable->addRow();
			$failedLoginsTable->setCol(1, 0, ['class' => "middlefont", "colspan" => "4", 'style' => "text-align:left;color:green;"], we_html_element::htmlB(g_l("cockpit", "[kv_failedLogins][noFailedLogins]")));
		}
		$this->newSCurrId = $curID ?: we_base_request::_(we_base_request::STRING, 'we_cmd', '', 5);

		$this->failedLoginHTML = $failedLoginsTable->getHtml();
	}

	public function getInsertDiv($iCurrId, we_base_jsCmd $jsCmd){
		$cfg = self::getDefaultConfig();

		$oTblDiv = we_html_element::htmlDiv([
				'id' => 'm_' . $iCurrId . '_inline',
				'style' => "width:100%;height:" . ($cfg["height"] - 25) . "px;overflow:auto;"
				], we_html_element::htmlDiv(['id' => 'fdl_data'], $this->failedLoginHTML)
		);
		$aLang = [g_l('cockpit', '[kv_failedLogins][headline]') . ' (' . $this->maxRows . ')', ""];
		return [$oTblDiv, $aLang];
	}

	public static function getDefaultConfig(){
		return [
			'width' => self::WIDTH_LARGE,
			'expanded' => 1,
			'height' => 210,
			'res' => 1,
			'cls' => 'orange',
			'csv' => '',
			'dlgHeight' => 435,
			'isResizable' => 0
		];
	}

	public static function showDialog(){
		list($jsFile, $oSelCls) = self::getDialogPrefs();

		$parts = [
			["headline" => "",
				"html" => $oSelCls->getHTML(),
			]
		];

		$save_button = we_html_button::create_button(we_html_button::SAVE, "javascript:save();");
		$preview_button = we_html_button::create_button(we_html_button::PREVIEW, "javascript:preview();");
		$cancel_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:exit_close();");
		$buttons = we_html_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

		$sTblWidget = we_html_multiIconBox::getHTML("Props", $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[customer]'));

		echo we_html_tools::getHtmlTop(g_l('cockpit', '[customer]'), '', '', $jsFile .
			we_html_element::jsScript(JS_DIR . 'widgets/fdl.js', '', ['id' => 'loadVarFdl', 'data-fdl' => setDynamicVar([
					'refreshCmd' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)
			])])
			, we_html_element::htmlBody(
				["class" => "weDialogBody", "onload" => "init();"
				], we_html_element::htmlForm("", $sTblWidget)));
	}

	public function showPreview(){
		$jsCmd = new we_base_jsCmd();
		$jsCmd->addCmd('initPreview', [
			'id' => $this->newSCurrId,
			'type' => 'fdl',
			'tb' => g_l('cockpit', '[kv_failedLogins][headline]')
		]);
		echo we_html_tools::getHtmlTop(g_l('cockpit', '[kv_failedLogins][headline]') . ' (' . $this->maxRows . ')', '', '', $jsCmd->getCmds(), we_html_element::htmlBody([
				'style' => 'margin:10px 15px;',
				], we_html_element::htmlDiv(["id" => "fdl"
					], we_html_element::htmlDiv(['id' => 'fdl_data'], $this->failedLoginHTML)
		)));
	}

}
