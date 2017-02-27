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
class rpcWidgetCmd extends we_rpc_cmd{

	function execute(){
		//FIXME: this needs change, it is only a copy of widget_cmd
		$resp = new we_rpc_response();

		$cmd2 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2);
		switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1)){
			case 'save' :
				we_base_preferences::setUserPref('cockpit_dat', $cmd2);
				we_base_preferences::setUserPref('cockpit_rss', we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3));
				break;
			case 'reload':
				$mod = we_base_request::_(we_base_request::STRING, 'mod');
				array_shift($_REQUEST['we_cmd']);
				array_shift($_REQUEST['we_cmd']);

				break;
			case 'add' :
				$newSCurrId = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3);
				$className = 'we_widget_' . $cmd2;
				$cfg = $className::getDefaultConfig();

				$aProps = [
					$cmd2,
					$cfg['cls'],
					$cfg['res'],
					$cfg['csv'],
				];
				$aCfgProps = we_main_cockpit::getDefaultCockpit();
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
				}
				$className = 'we_widget_' . $aProps[0];
				$widgetInst=new $className($iCurrId);
				list($oTblDiv, $aLang) = $widgetInst->getInsertDiv($iCurrId, $iWidth);

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

		return $resp;
	}

}
