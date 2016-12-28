/* global WE */

/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software, you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
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
'use strict';
var selector = WE().util.getDynamicVar(document, 'loadVarSelectorColor', 'data-selector');

var we_color2 = {
	"#000000": "#000000",
	"#003300": "#003300",
	"#006600": "#006600",
	"#009900": "#009900",
	"#00CC00": "#00CC00",
	"#00FF00": "#00FF00",
	"#330000": "#330000",
	"#333300": "#333300",
	"#336600": "#336600",
	"#339900": "#339900",
	"#33CC00": "#33CC00",
	"#33FF00": "#33FF00",
	"#660000": "#660000",
	"#663300": "#663300",
	"#666600": "#666600",
	"#669900": "#669900",
	"#66CC00": "#66CC00",
	"#66FF00": "#66FF00",
	"#000033": "#000033",
	"#003333": "#003333",
	"#006633": "#006633",
	"#009933": "#009933",
	"#00CC33": "#00CC33",
	"#00FF33": "#00FF33",
	"#330033": "#330033",
	"#333333": "#333333",
	"#336633": "#336633",
	"#339933": "#339933",
	"#33CC33": "#33CC33",
	"#33FF33": "#33FF33",
	"#660033": "#660033",
	"#663333": "#663333",
	"#666633": "#666633",
	"#669933": "#669933",
	"#66CC33": "#66CC33",
	"#66FF33": "#66FF33",
	"#000066": "#000066",
	"#003366": "#003366",
	"#006666": "#006666",
	"#009966": "#009966",
	"#00CC66": "#00CC66",
	"#00FF66": "#00FF66",
	"#330066": "#330066",
	"#333366": "#333366",
	"#336666": "#336666",
	"#339966": "#339966",
	"#33CC66": "#33CC66",
	"#33FF66": "#33FF66",
	"#660066": "#660066",
	"#663366": "#663366",
	"#666666": "#666666",
	"#669966": "#669966",
	"#66CC66": "#66CC66",
	"#66FF66": "#66FF66",
	"#000099": "#000099",
	"#003399": "#003399",
	"#006699": "#006699",
	"#009999": "#009999",
	"#00CC99": "#00CC99",
	"#00FF99": "#00FF99",
	"#330099": "#330099",
	"#333399": "#333399",
	"#336699": "#336699",
	"#339999": "#339999",
	"#33CC99": "#33CC99",
	"#33FF99": "#33FF99",
	"#660099": "#660099",
	"#663399": "#663399",
	"#666699": "#666699",
	"#669999": "#669999",
	"#66CC99": "#66CC99",
	"#66FF99": "#66FF99",
	"#0000CC": "#0000CC",
	"#0033CC": "#0033CC",
	"#0066CC": "#0066CC",
	"#0099CC": "#0099CC",
	"#00CCCC": "#00CCCC",
	"#00FFCC": "#00FFCC",
	"#3300CC": "#3300CC",
	"#3333CC": "#3333CC",
	"#3366CC": "#3366CC",
	"#3399CC": "#3399CC",
	"#33CCCC": "#33CCCC",
	"#33FFCC": "#33FFCC",
	"#6600CC": "#6600CC",
	"#6633CC": "#6633CC",
	"#6666CC": "#6666CC",
	"#6699CC": "#6699CC",
	"#66CCCC": "#66CCCC",
	"#66FFCC": "#66FFCC",
	"#0000FF": "#0000FF",
	"#0033FF": "#0033FF",
	"#0066FF": "#0066FF",
	"#0099FF": "#0099FF",
	"#00CCFF": "#00CCFF",
	"#00FFFF": "#00FFFF",
	"#3300FF": "#3300FF",
	"#3333FF": "#3333FF",
	"#3366FF": "#3366FF",
	"#3399FF": "#3399FF",
	"#33CCFF": "#33CCFF",
	"#33FFFF": "#33FFFF",
	"#6600FF": "#6600FF",
	"#6633FF": "#6633FF",
	"#6666FF": "#6666FF",
	"#6699FF": "#6699FF",
	"#66CCFF": "#66CCFF",
	"#66FFFF": "#66FFFF",
	"#990000": "#990000",
	"#993300": "#993300",
	"#996600": "#996600",
	"#999900": "#999900",
	"#99CC00": "#99CC00",
	"#99FF00": "#99FF00",
	"#CC0000": "#CC0000",
	"#CC3300": "#CC3300",
	"#CC6600": "#CC6600",
	"#CC9900": "#CC9900",
	"#CCCC00": "#CCCC00",
	"#CCFF00": "#CCFF00",
	"#FF0000": "#FF0000",
	"#FF3300": "#FF3300",
	"#FF6600": "#FF6600",
	"#FF9900": "#FF9900",
	"#FFCC00": "#FFCC00",
	"#FFFF00": "#FFFF00",
	"#990033": "#990033",
	"#993333": "#993333",
	"#996633": "#996633",
	"#999933": "#999933",
	"#99CC33": "#99CC33",
	"#99FF33": "#99FF33",
	"#CC0033": "#CC0033",
	"#CC3333": "#CC3333",
	"#CC6633": "#CC6633",
	"#CC9933": "#CC9933",
	"#CCCC33": "#CCCC33",
	"#CCFF33": "#CCFF33",
	"#FF0033": "#FF0033",
	"#FF3333": "#FF3333",
	"#FF6633": "#FF6633",
	"#FF9933": "#FF9933",
	"#FFCC33": "#FFCC33",
	"#FFFF33": "#FFFF33",
	"#990066": "#990066",
	"#993366": "#993366",
	"#996666": "#996666",
	"#999966": "#999966",
	"#99CC66": "#99CC66",
	"#99FF66": "#99FF66",
	"#CC0066": "#CC0066",
	"#CC3366": "#CC3366",
	"#CC6666": "#CC6666",
	"#CC9966": "#CC9966",
	"#CCCC66": "#CCCC66",
	"#CCFF66": "#CCFF66",
	"#FF0066": "#FF0066",
	"#FF3366": "#FF3366",
	"#FF6666": "#FF6666",
	"#FF9966": "#FF9966",
	"#FFCC66": "#FFCC66",
	"#FFFF66": "#FFFF66",
	"#990099": "#990099",
	"#993399": "#993399",
	"#996699": "#996699",
	"#999999": "#999999",
	"#99CC99": "#99CC99",
	"#99FF99": "#99FF99",
	"#CC0099": "#CC0099",
	"#CC3399": "#CC3399",
	"#CC6699": "#CC6699",
	"#CC9999": "#CC9999",
	"#CCCC99": "#CCCC99",
	"#CCFF99": "#CCFF99",
	"#FF0099": "#FF0099",
	"#FF3399": "#FF3399",
	"#FF6699": "#FF6699",
	"#FF9999": "#FF9999",
	"#FFCC99": "#FFCC99",
	"#FFFF99": "#FFFF99",
	"#9900CC": "#9900CC",
	"#9933CC": "#9933CC",
	"#9966CC": "#9966CC",
	"#9999CC": "#9999CC",
	"#99CCCC": "#99CCCC",
	"#99FFCC": "#99FFCC",
	"#CC00CC": "#CC00CC",
	"#CC33CC": "#CC33CC",
	"#CC66CC": "#CC66CC",
	"#CC99CC": "#CC99CC",
	"#CCCCCC": "#CCCCCC",
	"#CCFFCC": "#CCFFCC",
	"#FF00CC": "#FF00CC",
	"#FF33CC": "#FF33CC",
	"#FF66CC": "#FF66CC",
	"#FF99CC": "#FF99CC",
	"#FFCCCC": "#FFCCCC",
	"#FFFFCC": "#FFFFCC",
	"#9900FF": "#9900FF",
	"#9933FF": "#9933FF",
	"#9966FF": "#9966FF",
	"#9999FF": "#9999FF",
	"#99CCFF": "#99CCFF",
	"#99FFFF": "#99FFFF",
	"#CC00FF": "#CC00FF",
	"#CC33FF": "#CC33FF",
	"#CC66FF": "#CC66FF",
	"#CC99FF": "#CC99FF",
	"#CCCCFF": "#CCCCFF",
	"#CCFFFF": "#CCFFFF",
	"#FF00FF": "#FF00FF",
	"#FF33FF": "#FF33FF",
	"#FF66FF": "#FF66FF",
	"#FF99FF": "#FF99FF",
	"#FFCCFF": "#FFCCFF",
	"#FFFFFF": "#FFFFFF"
};

function selectColor(c) {
	document.we_form.colorvalue.value = c;
}

function init(color) {
	top.focus();
	document.we_form.colorvalue.value = color;
	var html = '<table class="colorTable" class="default" >';
	var z = 0;
	for (var col in we_color2) {
		if (z === 0) {
			html += '<tr>';
		}

		html += '<td style="background-color:' + col + '" onclick="selectColor(\'' + col + '\');" title="' + we_color2[col] + '" >&nbsp;</td>';

		if (z === 17) {
			html += '</tr>';
			z = 0;
		} else {
			z++;
		}
	}
	if (z !== 0) {
		for (var i = z; i < 18; i++) {
			html += '<td></td>';
		}
		html += '</tr>';
	}

	document.getElementById("colorTable").innerHTML = html;
}

function setColor() {
	if (selector.isA) {
		window.opener.document.we_form.elements[selector.cmd1].value = document.we_form.colorvalue.value;
		//FIXME: eval
		if (selector.cmd3) {
			eval(selector.cmd3);
		} else {
			window.opener._EditorFrame.setEditorIsHot(true);
			window.opener.we_cmd("reload_editpage");
		}
	} else {
		window.returnValue = document.we_form.colorvalue.value;
	}
	window.close();
}

