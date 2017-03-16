/* global top, WE */

/**
 * webEdition CMS
 *
 * webEdition CMS
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
'use strict';
var iconBox = WE().util.getDynamicVar(document, 'loadVarMultiIconBox', 'data-iconbox');

function weToggleBox(name, textDown, textRight) {
	var t = document.getElementById('table_' + name);
	var s = document.getElementById('text_' + name);
	if (t.style.display === "none") {
		t.style.display = "";
		s.innerHTML = textDown;
	} else {
		t.style.display = "none";
		s.innerHTML = textRight;
	}
}

function weGetMultiboxLength() {
	var divs = document.getElementsByTagName("DIV");
	var prefix = "div_" + iconBox.name + "_";
	var z = 0;
	for (var i = 0; i < divs.length; i++) {
		if (divs[i].id.length > prefix.length && divs[i].id.substring(0, prefix.length) === prefix) {
			z++;
		}
	}
	return z;
}

function weGetLastMultiboxNr() {
	var divs = document.getElementsByTagName("DIV");
	var prefix = "div_" + iconBox.name + "_";
	var num = -1;
	for (var i = 0; i < divs.length; i++) {
		if (divs[i].id.length > prefix.length && divs[i].id.substring(0, prefix.length) == prefix) {
			num = divs[i].id.substring(prefix.length, divs[i].id.length);
		}
	}
	return parseInt(num);
}

function weDelMultiboxRow(nr) {
	var div = document.getElementById("div_" + iconBox.name + "_" + nr);
	var mainTD = document.getElementById("td_" + iconBox.name);
	mainTD.removeChild(div);
}


function weAppendMultiboxRow(content, headline, icon, space, insertRuleBefore, insertDivAfter) {
	var lastNum = weGetLastMultiboxNr();
	var i = (lastNum + 1);
	headline = headline ? ('<div  id="headline_' + iconBox.name + '_' + i + '" class="weMultiIconBoxHeadline">' + headline + '</div>') : "";

	var mainContent = content ? content : "";
	var leftWidth = space ? space : 0;
	var leftContent = (leftWidth ? headline : "");
	var rightContent = '<div style="float:left;">' + (((leftContent === "")) ? (headline + '<div>' + mainContent + '</div>') : mainContent) + '</div>';

	var mainDiv = document.createElement("DIV");
	mainDiv.className = (iconBox.margin ? 'withMargin' : '');
	mainDiv.id = "div_" + iconBox.name + "_" + i;
	var innerHTML = "";

	if (leftContent || leftWidth) {
		if ((!leftContent) && leftWidth) {
			leftContent = "&nbsp;";
		}
		innerHTML += '<div style="float:left;width:' + leftWidth + 'px">' + leftContent + '</div>';
	}
	mainDiv.innerHTML = rightContent + '<br style="clear:both;">';

	var mainTD = document.getElementById("td_" + iconBox.name);
	mainTD.appendChild(mainDiv);

	var lastDiv = document.createElement("DIV");
	if (insertDivAfter !== -1) {
		lastDiv.style.cssText = "margin:10px 0;clear:both;";
		mainTD.appendChild(lastDiv);
	}

	if (insertRuleBefore && (lastNum != -1)) {
		var rule = document.createElement("DIV");
		rule.style.cssText = "border-top: 1px solid #AFB0AF;margin:10px 0 10px 0;clear:both;";
		var preDIV = document.getElementById("div_" + iconBox.name + "_" + lastNum);
		preDIV.appendChild(rule);
	}
}