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
	static function getHTML($name, $width, array $content, $marginLeft = 0, $buttons = '', $foldAtNr = -1, $foldRight = '', $foldDown = '', $displayAtStartup = false, $headline = "", $delegate = "", $height = 0, $overflow = "auto"){
		$uniqname = $name ? $name : md5(uniqid(__FILE__, true));

		$out = (isset($headline) && $headline != '') ?
			we_html_multiIconBox::_getBoxStartHeadline($width, $headline, $uniqname, $marginLeft, $overflow) :
			we_html_multiIconBox::_getBoxStart($width, $uniqname);

		foreach($content as $i => $c){
			if($c === null){
				continue;
			}
			$out.=(isset($c['class']) ? '<div class="' . $c['class'] . '">' : '');

			if($i == $foldAtNr && $foldAtNr < count($content)){ // only if the folded items contain stuff.
				$out .= we_html_button::create_button_table(array(
						we_html_multiIconBox::_getButton($uniqname, ($delegate ? $delegate : "" ) . ";weToggleBox('" . $uniqname . "','" . addslashes($foldDown) . "','" . addslashes($foldRight) . "')", ($displayAtStartup ? "down" : "right"), g_l('global', "[openCloseBox]")),
						'<span style="cursor: pointer;" class="defaultfont" id="text_' . $uniqname . '" onclick="' . ($delegate ? $delegate : "" ) . ';weToggleBox(\'' . $uniqname . '\',\'' . addslashes($foldDown) . '\',\'' . addslashes($foldRight) . '\');">' . ($displayAtStartup ? $foldDown : $foldRight) . '</span>'
						), 10, array('style' => 'margin-left:' . $marginLeft . 'px;')
					) .
					'<br/><table id="table_' . $uniqname . '" width="100%" cellpadding="0" style="border-spacing: 0px;border-style:none;' . ($displayAtStartup ? '' : 'display:none') . '"><tr><td>';
			}

			$_forceRightHeadline = (isset($c["forceRightHeadline"]) && $c["forceRightHeadline"]);

			$icon = (isset($c["icon"]) && $c["icon"] ?
					we_html_element::htmlImg(array('src' => IMAGE_DIR . 'icons/' . $c["icon"], 'style' => "margin-left:20px;")) :
					'');
			$headline = (isset($c["headline"]) && $c["headline"] ?
					'<div id="headline_' . $uniqname . '_' . $i . '" class="weMultiIconBoxHeadline" style="margin-bottom:10px;">' . $c["headline"] . '</div>' :
					'');

			$mainContent = (isset($c["html"]) && $c["html"]) ? $c["html"] : '';

			$leftWidth = (isset($c["space"]) && $c["space"]) ? abs($c["space"]) : 0;

			$leftContent = $icon ? $icon : (($leftWidth && (!$_forceRightHeadline)) ? $headline : '');

			$rightContent = '<div ' . ($mainContent == $leftContent && $leftContent == '' ? '' : 'style="float:left;"') . ' class="defaultfont">' . ((($icon && $headline) || ($leftContent === "") || $_forceRightHeadline) ? ($headline . '<div>' . $mainContent . '</div>') : '<div>' . $mainContent . '</div>') . '</div>';

			$out .= '<div style="margin-left:' . $marginLeft . 'px" id="div_' . $uniqname . '_' . $i . '">';

			if($leftContent || $leftWidth){
				if((!$leftContent) && $leftWidth){
					$leftContent = "&nbsp;";
				}
				$out .= '<div style="float:left;width:' . $leftWidth . 'px">' . $leftContent . '</div>';
			}

			$out .= $rightContent .
				'<br style="clear:both;"/>
					</div>' . (we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 10 ? '<br/>' : '') .
				($i < (count($content) - 1) && (!isset($c["noline"])) ?
					'<div style="border-top: 1px solid #AFB0AF;margin:10px 0 10px 0;clear:both;"></div>' :
					'<div style="margin:10px 0;clear:both;"></div>') .
				(isset($c['class']) ? '</div>' : '');
		}

		if($foldAtNr >= 0 && $foldAtNr < count($content)){
			$out .= '</td></tr></table>';
		}

		$boxHTML = $out . we_html_multiIconBox::_getBoxEnd($width);

		if($buttons){
			//ignore height, replace by bottom:
			return '<div style="overflow:' . $overflow . ';position:absolute;width:100%;' . ($height ? 'height:' . $height . 'px;' : 'bottom:40px;') . 'top:0px;left:0px;">' . $boxHTML . '</div>
				<div style="left:0px;height:40px;background-image: url(' . IMAGE_DIR . 'edit/editfooterback.gif);position:absolute;bottom:0px;width:100%"><div style="padding: 10px 10px 0 0;">' . $buttons . '</div></div>';
		} else {
			return $boxHTML;
		}
	}

	static function getJS(){
		return we_html_element::jsElement('
			function weToggleBox(name,textDown,textRight){
				var t = document.getElementById(\'table_\'+name);
				var s = document.getElementById(\'text_\'+name);
				var b = document.getElementById(\'btn_direction_\'+name+\'_middle\');
				if(t.style.display == "none"){
					t.style.display = "";
					s.innerHTML = textDown;
					b.style.background="url(' . BUTTONS_DIR . 'btn_direction_down.gif)";
					weSetCookieVariable("but_"+name,"down")
				}else{
					t.style.display = "none";
					s.innerHTML = textRight;
					b.style.background="url(' . BUTTONS_DIR . 'btn_direction_right.gif)";
					weSetCookieVariable("but_"+name,"right")
				}
			}

			function weGetCookieVariable(name){
				var c = weGetCookie("we' . session_id() . '");
				var vals = new Array();
				if(c != null){
					var parts = c.split(/&/);
					for(var i=0; i<parts.length; i++){
						var foo = parts[i].split(/=/);
						vals[unescape(foo[0])]=unescape(foo[1]);
					}
					return vals[name];
				}
				return null;
			}
			function weGetCookie(name){
				var cname = name + "=";
				var doc = (top.name == "edit_module") ? top.opener.top.document : top.document;
				var dc = doc.cookie;
				if (dc.length > 0) {
					begin = dc.indexOf(cname);
					if (begin != -1) {
						begin += cname.length;
						end = dc.indexOf(";", begin);
						if (end == -1) {
							end = dc.length;
						}
						return unescape(dc.substring(begin, end));
					}
				}
				return null;
			}

			function weSetCookieVariable(name,value){
				var c = weGetCookie("we' . session_id() . '");
				var vals = new Array();
				if(c != null){
					var parts = c.split(/&/);
					for(var i=0; i<parts.length; i++){
						var foo = parts[i].split(/=/);
						vals[unescape(foo[0])]=unescape(foo[1]);
					}
				}
				vals[name] = value;
				c = "";
				for (var i in vals) {
					c += escape(i)+"="+escape(vals[i])+"&";
				}
				if(c.length > 0){
					c=c.substring(0,c.length-1);
				}
				weSetCookie("we' . session_id() . '", c);
			}
			function weSetCookie(name, value, expires, path, domain){
				var doc = (top.name == "edit_module") ? top.opener.top.document : top.document;
				doc.cookie = name + "=" + escape(value) +
				((expires == null) ? "" : "; expires=" + expires.toGMTString()) +
				((path == null)    ? "" : "; path=" + path) +
				((domain == null)  ? "" : "; domain=" + domain);
			}');
	}

	static function getDynJS($uniqname = '', $marginLeft = 0){
		return we_html_element::jsElement('
			if(navigator.product == "Gecko"){
				var CELLPADDING = "cellpadding";
				var CELLSPACING = "cellspacing";
				var CLASSNAME = "classname";
				var VALIGN = "valign";
			}else{
				var CELLPADDING = "cellpadding";
				var CELLSPACING = "cellspacing";
				var CLASSNAME = "className";
				var VALIGN = "vAlign";
			}

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
				icon = icon  ? (\'<img src="' . IMAGE_DIR . 'icons/\' + icon + \'" width="64" height="64" alt="" style="margin-left:20px;" />\') : "";
				headline = headline ? (\'<div  id="headline_' . $uniqname . '_\'+ i + \'" class="weMultiIconBoxHeadline" style="margin-bottom:10px;">\' + headline + \'</div>\') : "";

				var mainContent = content ? content : "";
				var leftWidth = space ? space : 0;
				var leftContent = icon ? icon : (leftWidth ? headline : "");
				var rightContent = \'<div style="float:left;" class="defaultfont">\' + (((icon && headline) || (leftContent == "")) ? (headline + \'<div>\' + mainContent + \'</div>\') : mainContent)  + \'</div>\';

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

' . (we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 10 ? 'mainTD.appendChild(document.createElement("BR"));' : '') . '

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

	static function _getBoxStartHeadline($width, $headline, $uniqname, $marginLeft = 0, $overflow = "auto"){
		return '<table cellpadding="0" style="border-spacing: 0px;border-style:none;margin-top:10px;width:' . $width . (is_numeric($width) ? 'px' : '') . '; overflow:' . $overflow . '">
	<tr>
		<td style="padding-left:' . $marginLeft . 'px;padding-bottom:10px;" class="weDialogHeadline">' . $headline . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(2, 8) . '</td>
	</tr>
	<tr>
		<td id="td_' . $uniqname . '">';
	}

	static function _getBoxStart($w, $uniqname){
		if(strpos($w, "%") === false){
			$wp = abs($w) . "px";
		} else {
			$wp = $w;
		}
		return '<table cellpadding="0" style="border-spacing: 0px;border-style:none;margin-top:10px;width:' . $wp . ';">
	<tr>
		<td class="defaultfont"><b>' . we_html_tools::getPixel($w, 2) . '</b></td>
	</tr>
	<tr>
		<td id="td_' . $uniqname . '">';
	}

	static function _getBoxEnd($w){
		return '</td>
	</tr>
	<tr>
		<td></td>
	</tr>
</table>
';
	}

	static function _getButton($name, $cmd, $state = "right", $title = ""){
		return we_html_element::jsElement('weSetCookieVariable("but_' . $name . '","' . $state . '");var btn_direction_' . $name . '_mouse_event = false;') . '<table cellpadding="0" style="border-spacing: 0px;border-style:none;cursor: pointer; width: 21px;" id="btn_direction_' . $name . '_table" onmouseover="window.status=\'\';return true;"  onmouseup="document.getElementById(\'btn_direction_' . $name . '_middle\').style.background = \'url(' . BUTTONS_DIR . 'btn_direction_\'+weGetCookieVariable(\'but_' . $name . '\')+\'.gif)\';btn_direction_' . $name . '_mouse_event = false;' . $cmd . ';"><tr title="' . $title . '" style="height: 22px;"><td align="center" id="btn_direction_' . $name . '_middle" style="background-image:url(' . BUTTONS_DIR . '/btn_direction_' . $state . '.gif);width: 21px;white-space:nowrap;">' . we_html_tools::getPixel(21, 22) . '</td></tr></table>';
	}

}
