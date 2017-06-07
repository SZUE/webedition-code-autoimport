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
class we_widget_usr extends we_widget_base{

	public function __construct($curID = '', $aProps = []){

	}

	public function getInsertDiv($iCurrId, $aProps, we_base_jsCmd $jsCmd){
		list($num, $usr) = we_users_online::getUsers();
		$oTblCont = new we_html_table(
			["id" => "m_" . $iCurrId . "_inline",

			], 1, 1);
		$oTblCont->setCol(0, 0, null, '<div id="users_online">' . $usr . '</div>');
		$aLang = [g_l('cockpit', '[users_online]'), ' (<span id="num_users">' . $num . '</span>)'];

		$oTblDiv = $oTblCont->getHtml();
		return [$oTblDiv, $aLang];
	}

	public static function getDefaultConfig(){
		return [
			'width' => self::WIDTH_SMALL,
			'expanded' => 0,
			'height' => 210,
			'res' => 0,
			'cls' => 'lightCyan',
			'csv' => '',
			'dlgHeight' => 140,
			'isResizable' => 1
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

		$sTblWidget = we_html_multiIconBox::getHTML("usrProps", $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[users_online]'));

		echo we_html_tools::getHtmlTop(g_l('cockpit', '[users_online]'), '', '', $jsFile .
			we_html_element::jsScript(JS_DIR . 'widgets/usr.js'), we_html_element::htmlBody(
				['class' => "weDialogBody", "onload" => "init();"
				], we_html_element::htmlForm("", $sTblWidget)));
	}

	public function showPreview(){

	}

}
