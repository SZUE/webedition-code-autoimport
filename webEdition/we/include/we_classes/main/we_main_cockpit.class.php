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
abstract class we_main_cockpit{
	const iDefCols = 2;

	private static function getFullDefaultConfig(){
		return [
			'sct' => we_widget_sct::getDefaultConfig(),
			'rss' => we_widget_rss::getDefaultConfig(),
			'mfd' => we_widget_mfd::getDefaultConfig(),
			'shp' => we_widget_shp::getDefaultConfig(),
			'fdl' => we_widget_fdl::getDefaultConfig(),
			'usr' => we_widget_usr::getDefaultConfig(),
			'upb' => we_widget_upb::getDefaultConfig(),
			'mdc' => we_widget_mdc::getDefaultConfig(),
			'pad' => we_widget_pad::getDefaultConfig(),
		];
	}

	public static function getEditor(){
//make sure we know which browser is used
		$aCfgProps = self::getDefaultCockpit();
		if(we_base_permission::hasPerm('CAN_SEE_QUICKSTART')){
			$jsCmd = new we_base_jsCmd();
			$iLayoutCols = empty($_SESSION['prefs']['cockpit_amount_columns']) ? 3 : $_SESSION['prefs']['cockpit_amount_columns'];
			$bResetProps = (we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === 'reset_home');
			if(!$bResetProps && $iLayoutCols){
				$aDat = array_filter((we_unserialize(we_base_preferences::getUserPref('cockpit_dat')) ? : $aCfgProps)) ? : $aCfgProps;
				$aTrf = we_unserialize(we_base_preferences::getUserPref('cockpit_rss')) ? : we_widget_rss::getTopFeeds();
				if(count($aDat) > $iLayoutCols){
					while(count($aDat) > $iLayoutCols){
						$aDelCol = array_pop($aDat);
						foreach($aDelCol as $aShiftWidget){
							$aDat[count($aDat) - 1][] = $aShiftWidget;
						}
					}
					we_base_preferences::setUserPref('cockpit_dat', we_serialize($aDat, SERIALIZE_JSON));
					we_base_preferences::setUserPref('cockpit_rss', we_serialize($aTrf, SERIALIZE_JSON));
				}
				$iDatLen = count($aDat);
			} else {
				$iLayoutCols = self::iDefCols;
				$_SESSION['prefs']['cockpit_amount_columns'] = self::iDefCols;

				we_base_preferences::setUserPref('cockpit_amount_columns', self::iDefCols);
				we_base_preferences::setUserPref('cockpit_dat', we_serialize($aCfgProps, SERIALIZE_JSON));
				we_base_preferences::setUserPref('cockpit_rss', we_serialize(we_widget_rss::getTopFeeds(), SERIALIZE_JSON));
				$aDat = $aCfgProps;
				$aTrf = we_widget_rss::getTopFeeds();
				$iDatLen = count($aDat);
			}
			$cockpit = [
				'_iInitCols' => intval($iLayoutCols),
				'transact' => md5(uniqid(__FILE__, true)),
				'_trf' => [],
				'homeData' => [],
				'oCfg' => self::getFullDefaultConfig(),
				'widgetData' => []
			];
			foreach($aTrf as $aRssFeed){
				$cockpit['_trf'][] = [$aRssFeed[0], $aRssFeed[1]];
			}
			foreach($aDat as $d){
				$tmp = [];
				foreach($d as $v){
					$tmp[] = ['type' => $v[0], 'cls' => $v[1], 'res' => $v[2], 'csv' => $v[3]];
				}
				$cockpit['homeData'][] = $tmp;
			}

			we_base_moduleInfo::isActive(we_base_moduleInfo::USERS);
			$aCmd = explode('_', we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0));
			if($aCmd[0] === 'new'){
				$in = [substr($aCmd[2], -3), 1, 1];
				$aDat[0] = array_merge(array_slice($aDat[0], 0, 0), [$in], array_slice($aDat[0], 0));
			}
			$aDiscard = ['rss', 'pad'];
			$s1 = '';
			$iCurrCol = $iCurrId = 0;

			foreach($aDat as $d){
				$bExtendedCol = false;
				$s2 = '';
				$iCurrCol++;
				foreach($d as $aProps){
					$iCurrId++;
					switch($aProps[0]){
						case 'usr':
							if(!defined('USER_TABLE')){
								continue;
							}
							break;
						case 'msg':
							continue;
					}

					$iWidth = ((!$aProps[2]) ? we_widget_base::WIDTH_SMALL : we_widget_base::WIDTH_LARGE);
					$newSCurrId = '';
					if(!in_array($aProps[0], $aDiscard)){
						switch($aProps[0]){
							case 'usr':
								if(!defined('USER_TABLE')){
									continue;
								}
								break;
							case 'msg':
								continue;
						}

						$iWidth = ((!$aProps[2]) ? we_widget_base::WIDTH_SMALL : we_widget_base::WIDTH_LARGE);
						if(!in_array($aProps[0], $aDiscard)){
							switch($aProps[0]){
								case 'upb':
									if($aProps[3] === ''){
										$aProps[3] = (defined('OBJECT_TABLE') ? '11' : '10');
									}
									break;
								case 'usr':
								case 'msg':
									$aDiscard[] = $aProps[0];
									break;
							}
							$newSCurrId = 'm_' . $iCurrId;
						}
					}
					if($aProps[2]){
						$bExtendedCol = true;
					}
					$className = 'we_widget_' . $aProps[0];
					if(class_exists($className)){
						$widgetInst = new $className($newSCurrId, $aProps);
						list($oTblDiv, $aLang) = $widgetInst->getInsertDiv($iCurrId, $aProps, $jsCmd);
						$cfg = $className::getDefaultConfig();
						$widget = we_widget_base::create('m_' . $iCurrId, $aProps[0], $oTblDiv, $aLang, $aProps[1], $aProps[2], $aProps[3], $iWidth, $cfg["height"], $cfg["isResizable"]);
						$s2 .= we_html_element::htmlDiv(["id" => "m_" . $iCurrId, "class" => "le_widget"], $widget);
					}
				}
				$s1 .= '<td id="c_' . $iCurrCol . '" class="cls_' . (($bExtendedCol) ? 'expand' : 'collapse') . '">' .
					$s2 .
					we_html_element::htmlDiv(['class' => "wildcard", 'style' => ($iDatLen > $iCurrCol ? 'margin-right:5px' : '')], '') . '</td>';
			}
			while($iCurrCol < $iLayoutCols){
				$iCurrCol++;
				$s1 .= '<td id="c_' . $iCurrCol . '" class="cls_collapse">' .
					we_html_element::htmlDiv(['class' => "wildcard"], "") . '</td>' .
					($iLayoutCols > $iCurrCol ? '<td>&nbsp;&nbsp;</td>' : '');
			}

			$oTblWidgets = new we_html_table(['class' => 'default'], 1, 1);
			$oTblWidgets->setCol(0, 0, [], we_html_element::htmlDiv(["id" => "modules"], '<table id="le_tblWidgets"><tr id="rowWidgets">' . $s1 . '</tr></table>'));

			// this is the clone widget
			$oClone = we_widget_base::create("clone", "_reCloneType_", null, ['', ''], "white", 0, "", 100, 60);
			$cockpit['widgetData'] = we_widget_base::getJson();

			echo
			we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'utils/cockpit.js') .
				we_html_element::jsScript(LIB_DIR . 'additional/gauge/gauge.min.js') .
				we_html_element::cssLink(CSS_DIR . 'home.css') .
				we_html_element::jsScript(JS_DIR . 'home.js', '', ['id' => 'loadVarHome', 'data-cockpit' => setDynamicVar($cockpit)]) .
				$jsCmd->getCmds()
				, we_html_element::htmlBody(['onload' => "startCockpit();",], we_html_element::htmlDiv(["id" => "rpcBusy", 'style' => "display:none;"], '<i class="fa fa-2x fa-spinner fa-pulse"></i>'
					) . we_html_element::htmlDiv(["id" => "widgets"], "") .
					$oTblWidgets->getHtml() .
					we_html_element::htmlDiv(["id" => "divClone"], $oClone) .
					'<iframe id="RSIFrame" name="RSIFrame" style="border:0px;width:1px;height:1px; visibility:hidden"></iframe>'
			));
		} else { // no right to see cockpit!
			echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'utils/cockpit.js') .
				we_html_element::cssLink(CSS_DIR . 'home.css') .
				we_html_element::jsScript(JS_DIR . 'nohome.js'), we_html_element::htmlBody(
					['class' => 'noHome', "onload" => "_EditorFrame.initEditorFrameData({'EditorIsLoading':false});"
					], we_html_element::htmlDiv(
						['class' => "defaultfont errorMessage", 'style' => "width: 400px;"], (we_base_permission::hasPerm(["CHANGE_START_DOCUMENT", "EDIT_SETTINGS"], false) ?
							we_html_tools::htmlAlertAttentionBox("<strong>" . g_l('SEEM', '[question_change_startdocument]') . '</strong><br/><br/>' .
								we_html_button::create_button('preferences', "javascript:top.we_cmd('openPreferences');"), we_html_tools::TYPE_ALERT, 0, false) :
							we_html_tools::htmlAlertAttentionBox("<strong>" . g_l('SEEM', '[start_with_SEEM_no_startdocument]') . "</strong>", we_html_tools::TYPE_ALERT, 0, false))) .
					'<img class="blank_editor_logo" src="/webEdition/images/backgrounds/bg-editor.png" alt="logo"/>'
			));
		}
	}

	public static function processCommand(){
		$cmd1 = we_base_request::_(we_base_request::SERIALIZED_KEEP, 'we_cmd', '', 2);
		switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1)){
			case "loadTree" :
				if(($pid = we_base_request::_(we_base_request::INT, "pid")) !== false){
					echo we_html_tools::getHtmlTop('', '', '', we_base_jsCmd::singleCmd('location', ['doc' => 'document', 'loc' => WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=loadTree&we_cmd[1]=' . we_base_request::_(we_base_request::TABLE, "tab") . '&we_cmd[2]=' . $pid . '&we_cmd[3]=' . (we_base_request::_(we_base_request::STRING, 'openFolders') ? : "") . '&we_cmd[4]=top']), we_html_element::htmlBody());
				}
				break;
			case 'dialog':
				array_splice($_REQUEST['we_cmd'], 0, 3);
				$cmd1::showDialog();
				break;
			case 'save' :
				we_base_preferences::setUserPref('cockpit_dat', $cmd1);
				we_base_preferences::setUserPref('cockpit_rss', we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3));
				header('Content-Type: application/json; charset=UTF-8');

				echo json_encode(['OK'], JSON_UNESCAPED_UNICODE);

				exit();
			case 'reload':
				$mod = we_base_request::_(we_base_request::STRING, 'mod');
				array_splice($_REQUEST['we_cmd'], 0, 2);
				$className = 'we_widget_' . $mod;
				$widget = new $className();
				$widget->showPreview();
				break;
			case 'add' :
				$aCfgProps = self::getDefaultCockpit();
				$newSCurrId = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3);
				$className = 'we_widget_' . $cmd1;
				$cfg = $className::getDefaultConfig();
				$jsCmd = new we_base_jsCmd();

				$aProps = [
					$cmd1,
					$cfg['cls'],
					$cfg['res'],
					$cfg['csv'],
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
				$className = 'we_widget_' . $aProps[0];
				$widgetInst = new $className($newSCurrId);
				list($oTblDiv, $aLang) = $widgetInst->getInsertDiv($iCurrId, $aProps, $jsCmd);

				echo we_html_tools::getHtmlTop('', '', '', we_html_element::cssElement('div,span{display:none;}') . $jsCmd->getCmds(), we_html_element::htmlBody(
						['onload' => 'WE().layout.cockpitFrame.transmit(window,\'' . $aProps[0] . '\',\'m_' . $iCurrId . '\');'
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
				self::getEditor();
				break;
		}
	}

	public static function getDefaultCockpit(){
		// define shortcuts
		$shortCuts = (defined('FILE_TABLE') && we_base_permission::hasPerm('CAN_SEE_DOCUMENTS') ? '1' : '0') .
			(defined('TEMPLATES_TABLE') && (we_base_permission::hasPerm('CAN_SEE_TEMPLATES')) ? '1' : '0') .
			(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm('CAN_SEE_OBJECTFILES') ? '1' : '0') .
			(defined('OBJECT_TABLE') && we_base_permission::hasPerm('CAN_SEE_OBJECTS') ? '1' : '0');

		$shortCuts_left = [];
		$shortCuts_right = [];

		if(defined('FILE_TABLE') && we_base_permission::hasPerm('CAN_SEE_DOCUMENTS')){
			$shortCuts_left[] = 'open_document';
			$shortCuts_left[] = 'new_document';
		}

		if(defined('TEMPLATES_TABLE') && we_base_permission::hasPerm('NEW_TEMPLATE')){
			$shortCuts_left[] = 'new_template';
		}
		$shortCuts_left[] = 'new_directory';
		if(defined('FILE_TABLE') && we_base_permission::hasPerm('CAN_SEE_DOCUMENTS')){
			$shortCuts_left[] = 'unpublished_pages';
		}
		if(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm('CAN_SEE_OBJECTFILES')){
			$shortCuts_right[] = 'unpublished_objects';
		}
		if(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm('NEW_OBJECTFILE')){
			$shortCuts_right[] = 'new_object';
		}

		if(defined('OBJECT_TABLE') && we_base_permission::hasPerm('NEW_OBJECT')){
			$shortCuts_right[] = 'new_class';
		}
		if(we_base_permission::hasPerm('EDIT_SETTINGS')){
			$shortCuts_right[] = 'preferences';
		}

		return [
			[
				["pad", "blue", 1, base64_encode(g_l('cockpit', '[notepad_defaultTitle_DO_NOT_TOUCH]')) . ',30020'],
				["mfd", "green", 1, $shortCuts . ';0;5;00;']
			],
			[
				["rss", "yellow", 1, base64_encode('http://www.webedition.org/de/feeds/aktuelles.xml') . ',111000,0,110000,1'],
				["sct", "red", 1, implode(',', $shortCuts_left) . ';' . implode(',', $shortCuts_right)]
			]
		];
	}

}
