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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

function we_tag_date($attribs, $content){
	$type = weTag_getAttribute("type", $attribs);
	$format = weTag_getAttribute("format", $attribs, g_l('date','[format][default]'));

	$xml = weTag_getAttribute("xml", $attribs);

	if (strtolower($type) == "js") {
		$js = "\nheute = new Date();\n";
		$js .= 'function getDateS(d){' . "\n";
		$js .= '	switch(d){' . "\n";
		$js .= '		case 1:' . "\n";
		$js .= '		case 21:' . "\n";
		$js .= '		case 31:' . "\n";
		$js .= '			return "st";' . "\n";
		$js .= '		case 2:' . "\n";
		$js .= '		case 22:' . "\n";
		$js .= '			return "nd";' . "\n";
		$js .= '		case 3:' . "\n";
		$js .= '		case 23:' . "\n";
		$js .= '			return "rd";' . "\n";
		$js .= '		default:' . "\n";
		$js .= '			return "th";' . "\n";
		$js .= '	}' . "\n";
		$js .= '}' . "\n";

		$js .= 'function getDateWord(f,dateObj){' . "\n";
		$js .= '	var l_day_Short = new Array(';
		foreach (g_l('date','[day][short]') as $d) {
			$js .= '"' . $d . '",';
		}
		$js = ereg_replace('^(.+),$', '\1', $js);
		$js .= ');' . "\n";

		$js .= '	var l_monthLong = new Array(';
		foreach (g_l('date','[month][long]') as $d) {
			$js .= '"' . $d . '",';
		}
		$js = ereg_replace('^(.+),$', '\1', $js);
		$js .= ');' . "\n";

		$js .= '	var l_dayLong = new Array(';
		foreach (g_l('date','[day][long]') as $d) {
			$js .= '"' . $d . '",';
		}
		$js = ereg_replace('^(.+),$', '\1', $js);
		$js .= ');' . "\n";

		$js .= '	var l_monthShort = new Array(';
		foreach (g_l('date','[month][short]') as $d) {
			$js .= '"' . $d . '",';
		}
		$js = ereg_replace('^(.+),$', '\1', $js);
		$js .= ');' . "\n";

		$js .= '	switch(f){' . "\n";
		$js .= '		case "D":' . "\n";
		$js .= '			return l_day_Short[dateObj.getDay()];' . "\n";
		$js .= '		case "F":' . "\n";
		$js .= '			return l_monthLong[dateObj.getMonth()];' . "\n";
		$js .= '		case "l":' . "\n";
		$js .= '			return l_dayLong[dateObj.getDay()];' . "\n";
		$js .= '		case "M":' . "\n";
		$js .= '			return l_monthShort[dateObj.getMonth()];' . "\n";
		$js .= '	}' . "\n";
		$js .= '}' . "\n";

		$f = $format;

		if (preg_match('|[^\\]Y|', $f) || preg_match('|^Y|', $f))
			$js .= "var Y = heute.getYear();Y = (Y < 1900) ? (Y + 1900) : Y;\n";
		if (preg_match('|[^\\]y|', $f) || preg_match('|^y|', $f))
			$js .= "var y = heute.getYear();y = (y < 1900) ? (y + 1900) : y;y=y.substring(2,4);\n";
		;

		if (preg_match('|[^\\]a|', $f) || preg_match('|^a|', $f))
			$js .= "var a = (heute.getHours() > 11) ? 'pm' : 'am';\n";
		if (preg_match('|[^\\]A|', $f) || preg_match('|^A|', $f))
			$js .= "var A = (heute.getHours() > 11) ? 'PM' : 'AM';\n";
		if (preg_match('|[^\\]s|', $f) || preg_match('|^s|', $f))
			$js .= "var s = heute.getSeconds();\n";
		if (preg_match('|[^\\]m|', $f) || preg_match('|^m|', $f))
			$js .= "var m = heute.getMonth()+1;m = '00'+m;m=m.substring(m.length-2,m.length);\n";
		if (preg_match('|[^\\]n|', $f) || preg_match('|^n|', $f))
			$js .= "var n = heute.getMonth()+1;\n";
		if (preg_match('|[^\\]d|', $f) || preg_match('|^d|', $f))
			$js .= "var d = heute.getDate();d = '00'+d;d=d.substring(d.length-2,d.length);\n";
		if (preg_match('|[^\\]j|', $f) || preg_match('|^j|', $f))
			$js .= "var j = heute.getDate();\n";
		if (preg_match('|[^\\]h|', $f) || preg_match('|^h|', $f))
			$js .= "var h = heute.getHours();if(h > 12){h -= 12;};h = '00'+h;h=h.substring(h.length-2,h.length);\n";
		if (preg_match('|[^\\]H|', $f) || preg_match('|^H|', $f))
			$js .= "var H = heute.getHours();H = '00'+H;H=H.substring(H.length-2,H.length);\n";
		if (preg_match('|[^\\]g|', $f) || preg_match('|^g|', $f))
			$js .= "var g = heute.getHours();if(g > 12){ g -= 12;};\n";
		if (preg_match('|[^\\]G|', $f) || preg_match('|^G|', $f))
			$js .= "var G = heute.getHours();\n";
		if (preg_match('|[^\\]i|', $f) || preg_match('|^i|', $f))
			$js .= "var i = heute.getMinutes();i = '00'+i;i=i.substring(i.length-2,i.length);\n";
		if (preg_match('|[^\\]S|', $f) || preg_match('|^S|', $f))
			$js .= "var S = getDateS(heute.getDate());\n";

		if (preg_match('|[^\\]D|', $f) || preg_match('|^D|', $f))
			$js .= "var D = getDateWord('D',heute);\n";
		if (preg_match('|[^\\]F|', $f) || preg_match('|^F|', $f))
			$js .= "var F = getDateWord('F',heute);\n";
		if (preg_match('|[^\\]l|', $f) || preg_match('|^l|', $f))
			$js .= "var l = getDateWord('l',heute);\n";
		if (preg_match('|[^\\]M|', $f) || preg_match('|^M|', $f))
			$js .= "var M = getDateWord('M',heute);\n";

		$f = ereg_replace('([^\\])(Y)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(y)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(m)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(n)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(d)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(j)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(H)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(i)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(h)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(G)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(g)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(S)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(D)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(F)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(l)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(M)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(s)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(a)', '\1"+\2+"', $f);
		$f = ereg_replace('([^\\])(A)', '\1"+\2+"', $f);

		$f = ereg_replace('^([SYymndjHihGgDFlMsaA])', '"+\1+"', $f);
		$f = stripslashes($f);

		$js .= 'document.write("' . $f . '");' . "\n";

		$atts['language'] = 'JavaScript';
		$atts['type'] = 'text/javascript';
		$atts['xml'] = $xml;

		return getHtmlTag('script', $atts, $js);
	} else {
		return date(correctDateFormat($format));
	}
}

function correctDateFormat($format, $t = ''){
	if (!$t){
		$t = time();
	}
	
	$format = str_replace('\B', '%%%4%%%', $format);
	$format = str_replace('\I', '%%%5%%%', $format);
	$format = str_replace('\L', '%%%6%%%', $format);
	$format = str_replace('\T', '%%%8%%%', $format);
	$format = str_replace('\U', '%%%9%%%', $format);
	$format = str_replace('\Z', '%%%10%%%', $format);

	$format = str_replace('B', '\\B', $format);
	$format = str_replace('I', '\\I', $format);
	$format = str_replace('L', '\\L', $format);
	$format = str_replace('T', '\\T', $format);
	$format = str_replace('U', '\\U', $format);
	$format = str_replace('Z', '\\Z', $format);

	$format = str_replace('%%%4%%%', '\B', $format);
	$format = str_replace('%%%5%%%', '\I', $format);
	$format = str_replace('%%%6%%%', '\L', $format);
	$format = str_replace('%%%8%%%', '\T', $format);
	$format = str_replace('%%%9%%%', '\U', $format);
	$format = str_replace('%%%10%%%', '\Z', $format);

	$format = str_replace('D', '%%%0%%%', $format);
	$format = str_replace('F', '%%%1%%%', $format);
	$format = str_replace('l', '%%%2%%%', $format);
	$format = str_replace('M', '%%%3%%%', $format);

	$foo = g_l('date','[day][short]['.date('w', $t).']');
	$foo = ereg_replace('([a-zA-Z])', '\\\1', $foo);
	$format = str_replace('%%%0%%%', $foo, $format);
	$foo = g_l('date','[month][long]['.(date('n', $t) - 1).']');
	$foo = ereg_replace('([a-zA-Z])', '\\\1', $foo);
	$format = str_replace('%%%1%%%', $foo, $format);
	$foo = g_l('date','[day][long]['.date('w', $t).']');
	$foo = ereg_replace('([a-zA-Z])', '\\\1', $foo);
	$format = str_replace('%%%2%%%', $foo, $format);
	$foo = g_l('date','[month][short]['.(date('n', $t) - 1).']');
	$foo = ereg_replace('([a-zA-Z])', '\\\1', $foo);
	$format = str_replace('%%%3%%%', $foo, $format);
	return $format;
}
