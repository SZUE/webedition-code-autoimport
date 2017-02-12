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
$cmd1 = we_base_request::_(we_base_request::SERIALIZED_KEEP, 'we_cmd', '', 2);
switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1)){
	case 'save' :
		we_base_preferences::setUserPref('cockpit_dat', $cmd1);
		we_base_preferences::setUserPref('cockpit_rss', we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3));
		header('Content-Type: application/json; charset=UTF-8');

		echo json_encode(['OK'], JSON_UNESCAPED_UNICODE);

		exit();
	case 'reload':
		$mod = we_base_request::_(we_base_request::STRING, 'mod');
		array_shift($_REQUEST['we_cmd']);
		array_shift($_REQUEST['we_cmd']);
		include_once (WE_INCLUDES_PATH . 'we_widgets/mod/' . $mod . '.inc.php');
		break;
	case 'add' :
		include_once(WE_INCLUDES_PATH . 'we_widgets/cfg.inc.php');
		$newSCurrId = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3);

		$aProps = [
			$cmd1,
			$aPrefs[$cmd1]['cls'],
			$aPrefs[$cmd1]['res'],
			$aPrefs[$cmd1]['csv'],
		];
		foreach($aCfgProps as $a){
			foreach($a as $arr){
				if($arr[0] == $aProps[0]){
					$aProps[3] = $arr[3];
					break 2;
				}
			}
		}
		$iCurrId = str_replace('m_', '', $newSCurrId);
		$iWidth = $aPrefs[$aProps[0]]['width'];
		switch($aProps[0]){
			case 'rss':
			case 'pad':
				break;
			case 'msg':
				$transact = md5(uniqid(__FUNCTION__, true));
			default:
				include_once (WE_INCLUDES_PATH . 'we_widgets/mod/' . $aProps[0] . '.inc.php');
		}
		include_once (WE_INCLUDES_PATH . 'we_widgets/inc/' . $aProps[0] . '.inc.php');

		echo we_html_tools::getHtmlTop('', '', '', we_html_element::cssElement('div,span{display:none;}'), we_html_element::htmlBody(
				['onload' => 'WE().layout.cockpitFrame.transmit(this,\'' . $aProps[0] . '\',\'m_' . $iCurrId . '\');'
				], we_html_element::htmlDiv(['id' => 'content'], $oTblDiv) .
				we_html_element::htmlSpan(['id' => 'prefix'], $aLang[0]) .
				we_html_element::htmlSpan(['id' => 'postfix'], $aLang[1]) .
				we_html_element::htmlSpan(['id' => 'csv'], (isset($aProps[3]) ? $aProps[3] : '')))
		);
		break;

	//added to fix bug #6538
	case 'reset_home':
		$id = intval($_SESSION['user']['ID']);
		//delete user's cockpit preferences from db
		$GLOBALS['DB_WE']->query('REPLACE INTO ' . PREFS_TABLE . ' (`userID`,`key`,`value`) VALUES (' . $id . ',"cockpit_dat",""),(' . $id . ',"cockpit_amount_columns",""),(' . $id . ',"cockpit_rss","")');
		we_main_cockpit::getEditor();
		break;
}
