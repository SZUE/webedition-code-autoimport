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
abstract class we_html_multiIconBox{

	/**
	 * @desc 	Get HTML-Code of the multibox
	 * @param	$name				string
	 * @param	$width				int
	 * @param	$content			array
	 * @param	$buttons			string
	 * @param	$foldAtNr			int
	 * @param	$foldRight			unknown
	 * @param	$foldDown			unknown
	 * @param	$displayAtStartup	bool
	 * @param	$headline			string
	 * @return	string
	 */
	static function getHTML($name, array $content, $marginLeft = 0, $buttons = '', $foldAtNr = -1, $foldRight = '', $foldDown = '', $displayAtStartup = false, $headline = '', $delegate = '', $height = 0, $overflow = 'auto'){
		$uniqname = $name ? : md5(uniqid(__FILE__, true));

		$out = $headline ?
			self::_getBoxStartHeadline($name, $headline, $uniqname, $marginLeft, $overflow) :
			self::_getBoxStart($uniqname, $name);

		foreach($content as $i => $c){
			if($c === null){
				continue;
			}
			$_forceRightHeadline = (!empty($c["forceRightHeadline"]));

			$icon = (empty($c["icon"]) ? '' : we_html_element::htmlImg(array('src' => ICON_DIR . $c['icon'], 'class' => 'multiIcon')) )? : (empty($c['iconX']) ? '' : $c['iconX']);
			$headline = (empty($c["headline"]) ?
					'' :
					'<div id="headline_' . $uniqname . '_' . $i . '" class="weMultiIconBoxHeadline" style="margin-bottom:10px;">' . $c["headline"] . '</div>' );

			$mainContent = !empty($c["html"]) ? $c["html"] : '';
			$leftWidth = (!empty($c["space"]) ? abs($c["space"]) : 0);
			$leftContent = $icon ? : (($leftWidth && (!$_forceRightHeadline)) ? $headline : '');

			$rightContent = '<div class="multiIconRight">' . ($icon || !$leftContent || $_forceRightHeadline ? $headline : '') . '<div>' . $mainContent . '</div></div>';

			$out.=(isset($c['class']) ? '<div class="' . $c['class'] . '">' : '') .
				($i == $foldAtNr && $foldAtNr < count($content) ? // only if the folded items contain stuff.
					we_html_element::htmlSpan(array('style' => 'margin-left:' . $marginLeft . 'px;', 'class' => 'btn_direction_weMultibox_table'), self::_getButton($uniqname, "weToggleBox('" . $uniqname . "','" . addslashes($foldDown) . "','" . addslashes($foldRight) . "');" . ($delegate ? : "" ), ($displayAtStartup ? 'down' : 'right'), g_l('global', '[openCloseBox]')) .
						'<span style="cursor: pointer;" id="text_' . $uniqname . '" onclick="weToggleBox(\'' . $uniqname . '\',\'' . addslashes($foldDown) . '\',\'' . addslashes($foldRight) . '\');' . ($delegate ? : "" ) . '">' . ($displayAtStartup ? $foldDown : $foldRight) . '</span>'
					) .
					'<br/><table id="table_' . $uniqname . '" width="100%" class="default" style="' . ($displayAtStartup ? '' : 'display:none') . '"><tr><td>' : '') .
				'<div style="padding-left:' . $marginLeft . 'px;padding-bottom:10px;margin-bottom:10px;'.
					($i < (count($content) - 1) && (!isset($c['noline'])) ?
					'border-bottom: 1px solid #AFB0AF;' :
					'').'" id="div_' . $uniqname . '_' . $i . '">' .
				($leftContent || $leftWidth ?
					'<div style="' . ($leftWidth ? ' width:' . $leftWidth . 'px' : '') . '" class="multiiconleft largeicons">' . ((!$leftContent) && $leftWidth ? "&nbsp;" : $leftContent) . '</div>' :
					'') .
				$rightContent .
					'</div>' .
				(isset($c['class']) ? '</div>' : '');
		}

		if($foldAtNr >= 0 && $foldAtNr < count($content)){
			$out .= '</td></tr></table>';
		}

		$out .= self::_getBoxEnd();

		return ($buttons ?
				//ignore height, replace by bottom:
				'<div style="overflow:' . $overflow . ';position:absolute;width:100%;' . ($height ? 'height:' . $height . 'px;' : 'bottom:40px;') . 'top:0px;left:0px;">' . $out . '</div>
				<div class="editfooter"><div style="padding: 10px;">' . $buttons . '</div></div>' :
				$out);
	}

	static function getJS(){
		return we_html_element::jsScript(JS_DIR . 'multiIconBox.js');
	}

	static function getDynJS($uniqname = '', $marginLeft = 0){
		return we_html_element::jsElement('
function weGetMultiboxLength(){
	var divs = document.getElementsByTagName("DIV");
	var prefix =  "div_' . $uniqname . '_";
	var z = 0;
	for(var i = 0; i<divs.length; i++){
		if(divs[i].id.length > prefix.length && divs[i].id.substring(0,prefix.length) == prefix){
			z++;
		}
	}
	return z;
}

function weGetLastMultiboxNr(){
	var divs = document.getElementsByTagName("DIV");
	var prefix =  "div_' . $uniqname . '_";
	var num = -1;
	for(var i = 0; i<divs.length; i++){
		if(divs[i].id.length > prefix.length && divs[i].id.substring(0,prefix.length) == prefix){
			num = divs[i].id.substring(prefix.length,divs[i].id.length);
		}
	}
	return parseInt(num);
}

function weDelMultiboxRow(nr){
	var div = document.getElementById("div_' . $uniqname . '_"+nr);
	var mainTD = document.getElementById("td_' . $uniqname . '");
	mainTD.removeChild(div);
}

function weAppendMultiboxRow(content,headline,icon,space,insertRuleBefore,insertDivAfter){
	var lastNum = weGetLastMultiboxNr();
	var i = (lastNum + 1);
	icon = icon  ? (\'<img src="' . ICON_DIR . '\' + icon + \'" width="64" height="64" alt="" style="margin-left:20px;" />\') : "";
	headline = headline ? (\'<div  id="headline_' . $uniqname . '_\'+ i + \'" class="weMultiIconBoxHeadline" style="margin-bottom:10px;">\' + headline + \'</div>\') : "";

	var mainContent = content ? content : "";
	var leftWidth = space ? space : 0;
	var leftContent = icon ? icon : (leftWidth ? headline : "");
	var rightContent = \'<div style="float:left;">\' + (((icon && headline) || (leftContent == "")) ? (headline + \'<div>\' + mainContent + \'</div>\') : mainContent)  + \'</div>\';

	var mainDiv = document.createElement("DIV");
	mainDiv.style.cssText = \'margin-left:' . $marginLeft . 'px\';
	mainDiv.id="div_' . $uniqname . '_" + i;
	var innerHTML = "";

	if (leftContent || leftWidth) {
		if ((!leftContent) && leftWidth) {
			leftContent = "&nbsp;";
		}
		innerHTML += \'<div style="float:left;width:\' + leftWidth + \'px">\' + leftContent + \'</div>\';
	}
	innerHTML += rightContent;
	innerHTML += \'<br style="clear:both;">\';
	mainDiv.innerHTML = innerHTML;

	var mainTD = document.getElementById("td_' . $uniqname . '");
	mainTD.appendChild(mainDiv);

	var lastDiv = document.createElement("DIV");
	if(insertDivAfter !== -1){
		lastDiv.style.cssText = "margin:10px 0;clear:both;";
		mainTD.appendChild(lastDiv);
	}

	if(insertRuleBefore && (lastNum != -1)){
		var rule = document.createElement("DIV");
		rule.style.cssText = "border-top: 1px solid #AFB0AF;margin:10px 0 10px 0;clear:both;";
		var preDIV = document.getElementById("div_' . $uniqname . '_"+lastNum);
		preDIV.appendChild(rule);
	}

}');
	}

	private static function _getBoxStartHeadline($name, $headline, $uniqname, $marginLeft = 0, $overflow = 'auto'){
		return '<div class="default multiIcon defaultfont" style="overflow:' . $overflow . '" id="' . $name . '">
	<div style="padding-left:' . $marginLeft . 'px;padding-bottom:10px;" class="weDialogHeadline">' . $headline . '</div>
	<div id="td_' . $uniqname . '">';
	}

	static function _getBoxStart($uniqname, $name = ''){
		return '<div class="default multiIcon defaultfont" style="padding-bottom:2px;" id="' . $name . '">
		<div id="td_' . $uniqname . '">';
	}

	static function _getBoxEnd(){
		return '</div>
</div>';
	}

	static function _getButton($name, $cmd, $state = "right", $title = ""){
		return we_html_element::jsElement('weSetCookieVariable("but_' . $name . '","' . $state . '");') .
			we_html_button::create_button('fa:btn_direction,fa-lg fa-caret-' . $state, "javascript:" . $cmd . ";toggleButton(this,'" . $name . "');", true, we_html_button::WIDTH, we_html_button::HEIGHT, '', '', false, true, $name, false, $title);
	}

}
