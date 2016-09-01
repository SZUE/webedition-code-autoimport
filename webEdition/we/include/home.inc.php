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
include_once (WE_INCLUDES_PATH . '/we_widgets/cfg.inc.php');
//make sure we know which browser is used
we_html_tools::protect();
echo we_html_tools::getHtmlTop() .
 we_html_element::jsScript(JS_DIR . 'utils/cockpit.js') .
 we_html_element::cssLink(CSS_DIR . 'home.css');

if(permissionhandler::hasPerm('CAN_SEE_QUICKSTART')){
	$iLayoutCols = empty($_SESSION['prefs']['cockpit_amount_columns']) ? 3 : $_SESSION['prefs']['cockpit_amount_columns'];
	$bResetProps = (we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === 'reset_home') ? true : false;
	if(!$bResetProps && $iLayoutCols){
		$aDat = (we_unserialize(we_base_preferences::getUserPref('cockpit_dat'))? : $aCfgProps);
		$aDat = array_filter($aDat)? : $aCfgProps;
		$aTrf = we_unserialize(we_base_preferences::getUserPref('cockpit_rss'))? : $aTopRssFeeds;
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
		$iLayoutCols = $iDefCols;
		$_SESSION['prefs']['cockpit_amount_columns'] = $iDefCols;

		we_base_preferences::setUserPref('cockpit_amount_columns', $iDefCols);
		we_base_preferences::setUserPref('cockpit_dat', we_serialize($aCfgProps, SERIALIZE_JSON));
		we_base_preferences::setUserPref('cockpit_rss', we_serialize($aTopRssFeeds, SERIALIZE_JSON));
		$aDat = $aCfgProps;
		$aTrf = $aTopRssFeeds;
		$iDatLen = count($aDat);
	}
	$cockpit = [
		'_iInitCols' => intval($iLayoutCols),
		'transact' => md5(uniqid(__FILE__, true)),
		'_trf' => [],
		'homeData' => [],
		'oCfg' => $jsoCfg,
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

	echo we_html_element::jsScript(JS_DIR . 'home.js', '', ['id' => 'loadVarHome', 'data-cockpit' => setDynamicVar($cockpit)]); ?>
	</head>
	<?php
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
					if(!defined('MESSAGING_SYSTEM') || !defined('USER_TABLE')){
						continue;
					}
					break;
			}

			$iWidth = ((!$aProps[2]) ? $small : $large);
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
				include(WE_INCLUDES_PATH . 'we_widgets/mod/' . $aProps[0] . '.inc.php');
			}
			if($aProps[2]){
				$bExtendedCol = true;
			}
			if(file_exists(WE_INCLUDES_PATH . 'we_widgets/inc/' . $aProps[0] . '.inc.php')){
				include(WE_INCLUDES_PATH . 'we_widgets/inc/' . $aProps[0] . '.inc.php');
				$widget = we_base_widget::create('m_' . $iCurrId, $aProps[0], $oTblDiv, $aLang, $aProps[1], $aProps[2], $aProps[3], $iWidth, $aPrefs[$aProps[0]]["height"], $aPrefs[$aProps[0]]["isResizable"]);
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
	$oClone = we_base_widget::create("clone", "_reCloneType_", null, ['', ''], "white", 0, "", 100, 60);

	echo
	we_html_element::htmlBody([ 'onload' => "startCockpit();",], we_html_element::htmlForm(
			['name' => 'we_form'
			], we_html_element::htmlHiddens([
				'we_cmd[0]' => 'widget_cmd',
				'we_cmd[1]' => 'save',
				'we_cmd[2]' => '',
				'we_cmd[3]' => ''])
		) .
		we_html_element::htmlDiv(["id" => "rpcBusy", "style" => "display:none;"], '<i class="fa fa-2x fa-spinner fa-pulse"></i>'
		) . we_html_element::htmlDiv(["id" => "widgets"], "") .
		$oTblWidgets->getHtml() .
		we_base_widget::getJs() .
		we_html_element::htmlDiv(["id" => "divClone"], $oClone)
	);
} else { // no right to see cockpit!
	echo we_html_element::jsScript(JS_DIR . 'nohome.js') .
	'</head>' .
	we_html_element::htmlBody(
		[ 'class' => 'noHome', "onload" => "_EditorFrame.initEditorFrameData({'EditorIsLoading':false});"
		], we_html_element::htmlDiv(
			['class' => "defaultfont errorMessage", "style" => "width: 400px;"], (permissionhandler::hasPerm(["CHANGE_START_DOCUMENT", "EDIT_SETTINGS"], false) ?
				we_html_tools::htmlAlertAttentionBox("<strong>" . g_l('SEEM', '[question_change_startdocument]') . '</strong><br/><br/>' .
					we_html_button::create_button('preferences', "javascript:top.we_cmd('openPreferences');"), we_html_tools::TYPE_ALERT, 0, false) :
				we_html_tools::htmlAlertAttentionBox("<strong>" . g_l('SEEM', '[start_with_SEEM_no_startdocument]') . "</strong>", we_html_tools::TYPE_ALERT, 0, false))));
}
//FIXME: remove iframe
?>
<iframe id="RSIFrame" name="RSIFrame" style="border:0px;width:1px;height:1px; visibility:hidden"></iframe></html>
