<?php
/**
 * webEdition CMS
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
	global $l_dayShort, $l_monthLong, $l_dayLong, $l_monthShort;

	$type = we_getTagAttribute("type", $attribs);
	$format = we_getTagAttribute("format", $attribs, $GLOBALS["l_global"]["date_format"]);

	$xml = we_getTagAttribute("xml", $attribs, "");

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
		foreach ($l_dayShort as $d) {
			$js .= '"' . $d . '",';
		}
		$js = ereg_replace('^(.+),$', '\1', $js);
		$js .= ');' . "\n";

		$js .= '	var l_monthLong = new Array(';
		foreach ($l_monthLong as $d) {
			$js .= '"' . $d . '",';
		}
		$js = ereg_replace('^(.+),$', '\1', $js);
		$js .= ');' . "\n";

		$js .= '	var l_dayLong = new Array(';
		foreach ($l_dayLong as $d) {
			$js .= '"' . $d . '",';
		}
		$js = ereg_replace('^(.+),$', '\1', $js);
		$js .= ');' . "\n";

		$js .= '	var l_monthShort = new Array(';
		foreach ($l_monthShort as $d) {
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

		if (ereg('[^\\]Y', $f) || ereg('^Y', $f))
			$js .= "var Y = heute.getYear();Y = (Y < 1900) ? (Y + 1900) : Y;\n";
		if (ereg('[^\\]y', $f) || ereg('^y', $f))
			$js .= "var y = heute.getYear();y = (y < 1900) ? (y + 1900) : y;y=y.substring(2,4);\n";
		;

		if (ereg('[^\\]a', $f) || ereg('^a', $f))
			$js .= "var a = (heute.getHours() > 11) ? 'pm' : 'am';\n";
		if (ereg('[^\\]A', $f) || ereg('^A', $f))
			$js .= "var A = (heute.getHours() > 11) ? 'PM' : 'AM';\n";
		if (ereg('[^\\]s', $f) || ereg('^s', $f))
			$js .= "var s = heute.getSeconds();\n";
		if (ereg('[^\\]m', $f) || ereg('^m', $f))
			$js .= "var m = heute.getMonth()+1;m = '00'+m;m=m.substring(m.length-2,m.length);\n";
		if (ereg('[^\\]n', $f) || ereg('^n', $f))
			$js .= "var n = heute.getMonth()+1;\n";
		if (ereg('[^\\]d', $f) || ereg('^d', $f))
			$js .= "var d = heute.getDate();d = '00'+d;d=d.substring(d.length-2,d.length);\n";
		if (ereg('[^\\]j', $f) || ereg('^j', $f))
			$js .= "var j = heute.getDate();\n";
		if (ereg('[^\\]h', $f) || ereg('^h', $f))
			$js .= "var h = heute.getHours();if(h > 12){h -= 12;};h = '00'+h;h=h.substring(h.length-2,h.length);\n";
		if (ereg('[^\\]H', $f) || ereg('^H', $f))
			$js .= "var H = heute.getHours();H = '00'+H;H=H.substring(H.length-2,H.length);\n";
		if (ereg('[^\\]g', $f) || ereg('^g', $f))
			$js .= "var g = heute.getHours();if(g > 12){ g -= 12;};\n";
		if (ereg('[^\\]G', $f) || ereg('^G', $f))
			$js .= "var G = heute.getHours();\n";
		if (ereg('[^\\]i', $f) || ereg('^i', $f))
			$js .= "var i = heute.getMinutes();i = '00'+i;i=i.substring(i.length-2,i.length);\n";
		if (ereg('[^\\]S', $f) || ereg('^S', $f))
			$js .= "var S = getDateS(heute.getDate());\n";

		if (ereg('[^\\]D', $f) || ereg('^D', $f))
			$js .= "var D = getDateWord('D',heute);\n";
		if (ereg('[^\\]F', $f) || ereg('^F', $f))
			$js .= "var F = getDateWord('F',heute);\n";
		if (ereg('[^\\]l', $f) || ereg('^l', $f))
			$js .= "var l = getDateWord('l',heute);\n";
		if (ereg('[^\\]M', $f) || ereg('^M', $f))
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
