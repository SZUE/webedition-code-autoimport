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
function we_tag_date(array $attribs){
	$format = weTag_getAttribute('format', $attribs, g_l('date', '[format][default]'), we_base_request::RAW);

	switch(strtolower(weTag_getAttribute('type', $attribs, 'php', we_base_request::STRING))){
		case 'js':
			$monthsLong = g_l('date', '[month][long]');
			ksort($monthsLong);
			$monthsShort = g_l('date', '[month][short]');
			ksort($monthsShort);
			$js = 'function getDateS(d){
	switch(d){
		case 1:
		case 21:
		case 31:
			return "st";
		case 2:
		case 22:
			return "nd";
		case 3:
		case 23:
			return "rd";
		default:
			return "th";
	}
}
function getDateWord(f,dateObj){
	var l_day_Short = ["' . implode('","', g_l('date', '[day][short]')) . '"];
	var l_monthLong = ["' . implode('","', $monthsLong) . '"];
	var l_dayLong = ["' . implode('","', g_l('date', '[day][long]')) . '"];
	var l_monthShort = ["' . implode('","', $monthsShort) . '"];
	switch(f){
		case "D":
			return l_day_Short[dateObj.getDay()];
		case "F":
			return l_monthLong[dateObj.getMonth()];
		case "l":
			return l_dayLong[dateObj.getDay()];
		case "M":
			return l_monthShort[dateObj.getMonth()];
	}
}';

			$ret = $js_arr = [];

			for($i = 0; $i < strlen($format); $i++){
				$skip = false;
				switch($format[$i]){
					case '\\'://skip next char
						$i++;
					default:
						$ret[] = '"' . $format[$i] . '"';
						$skip = true;
						break;
					case 'Y':
						$js_arr['Y'] = 'var Y = heute.getYear();Y = (Y < 1900) ? (Y + 1900) : Y;';
						break;
					case 'y':
						$js_arr['y'] = 'var y = heute.getYear();y = (y < 1900) ? (y + 1900) : y; y=String(y).substr(2,2);';
						break;
					case 'a':
						$js_arr['a'] = "var a = (heute.getHours() > 11) ? 'pm' : 'am';";
						break;
					case 'A':
						$js_arr['A'] = "var A = (heute.getHours() > 11) ? 'PM' : 'AM';";
						break;
					case 's':
						$js_arr['s'] = 'var s = heute.getSeconds();';
						break;
					case 'm':
						$js_arr['m'] = "var m = heute.getMonth()+1;m = '00'+m;m=m.substring(m.length-2,m.length);";
						break;
					case 'n':
						$js_arr['n'] = "var n = heute.getMonth()+1;";
						break;
					case 'd':
						$js_arr['d'] = "var d = heute.getDate();d = '00'+d;d=d.substring(d.length-2,d.length);";
						break;
					case 'd':
						$js_arr['j'] = "var j = heute.getDate();";
						break;
					case 'h':
						$js_arr['h'] = "var h = heute.getHours();if(h > 12){h -= 12;};h = '00'+h;h=h.substring(h.length-2,h.length);";
						break;
					case 'H':
						$js_arr['H'] = "var H = heute.getHours();H = '00'+H;H=H.substring(H.length-2,H.length);";
						break;
					case 'g':
						$js_arr['g'] = 'var g = heute.getHours();if(g > 12){ g -= 12;};';
						break;
					case 'G':
						$js_arr['G'] = 'var G = heute.getHours();';
						break;
					case 'i':
						$js_arr['i'] = "var i = heute.getMinutes();i = '00'+i;i=i.substring(i.length-2,i.length);";
						break;
					case 'S':
						$js_arr['S'] = 'var S = getDateS(heute.getDate());';
						break;
					case 'D':
						$js_arr['D'] = "var D = getDateWord('D',heute);";
						break;
					case 'F':
						$js_arr['F'] = "var F = getDateWord('F',heute);";
						break;
					case 'l':
						$js_arr['l'] = "var l = getDateWord('l',heute);";
						break;
					case 'M':
						$js_arr['M'] = "var M = getDateWord('M',heute);";
						break;
				}
				if(!$skip){
					$ret[] = $format[$i];
				}
			}
			$js.='(function(){
	var heute = new Date();'.
implode('', $js_arr) .'
	document.write(' . stripslashes(implode('+', $ret)) . ');
})();';

			return we_html_element::jsElement($js);
		case 'php':
		default:
			$langcode = (isset($GLOBALS['WE_MAIN_DOC']) && $GLOBALS['WE_MAIN_DOC']->Language ? $GLOBALS['WE_MAIN_DOC']->Language : $GLOBALS['weDefaultFrontendLanguage']);

			return we_base_country::dateformat($langcode, new DateTime(), $format);
	}
}
