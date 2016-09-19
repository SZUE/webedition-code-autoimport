/* global WE */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

var oldTreeWidth = WE().consts.size.tree.default;

function toggleTree() {
	var tfd = window.document.getElementById("left");
	var w = getTreeWidth();

	if (tfd.style.display === "none") {
		oldTreeWidth = (oldTreeWidth < WE().consts.size.tree.min ? WE().consts.size.tree.default : oldTreeWidth);
		setTreeWidth(oldTreeWidth);
		tfd.style.display = "block";
		setTreeArrow("left");
		storeTreeWidth(oldTreeWidth);
	} else {
		tfd.style.display = "none";
		oldTreeWidth = w;
		setTreeWidth(WE().consts.size.tree.hidden);
		setTreeArrow("right");
	}
}

function setTreeArrow(direction) {
	try {
		var arrImg = window.document.getElementById("arrowImg");
		if (direction == "right") {
			arrImg.classList.remove("fa-caret-left");
			window.document.getElementById("incBaum").style.backgroundColor = "gray";
			window.document.getElementById("decBaum").style.backgroundColor = "gray";
		} else {
			arrImg.classList.remove("fa-caret-right");
			window.document.getElementById("incBaum").style.backgroundColor = "";
			window.document.getElementById("decBaum").style.backgroundColor = "";
		}
		arrImg.classList.add("fa-caret-" + direction);
	} catch (e) {
		// Nothing
	}
}

function getTreeWidth() {
	var w = window.document.getElementById("lframeDiv").style.width;
	return w.substr(0, w.length - 2);
}

function setTreeWidth(w) {
	window.document.getElementById("lframeDiv").style.width = w + "px";
	window.document.getElementById("right").style.left = w + "px";
	if (w > WE().consts.size.tree.hidden) {
		storeTreeWidth(w);
	}
}

function storeTreeWidth(w) {
	var ablauf = new Date();
	var newTime = ablauf.getTime() + 30758400000;
	ablauf.setTime(newTime);

	var moduleVals = sizeTreeJsWidth;
	moduleVals[module] = w;
	var val = "";
	for (var param in moduleVals) {
		val += val ? "," + param + ":" + moduleVals[param] : param + " : " + moduleVals[param];
	}

	WE().util.weSetCookie(window.document, "treewidth_modules", val, ablauf, "/");

}

function incTree() {
	var w = parseInt(getTreeWidth());
	if ((w > WE().consts.size.tree.min) && (w < WE().consts.size.tree.max)) {
		w += WE().consts.size.tree.step;
		setTreeWidth(w);
	}
	if (w >= WE().consts.size.tree.max) {
		w = WE().consts.size.tree.max;
		window.document.getElementById("incBaum").style.backgroundColor = "grey";
	}
}

function decTree() {
	var w = parseInt(getTreeWidth());
	w -= WE().consts.size.tree.step;
	if (w > WE().consts.size.tree.min) {
		setTreeWidth(w);
		window.document.getElementById("incBaum").style.backgroundColor = "";
	}
	if (w <= WE().consts.size.tree.min && ((w + WE().consts.size.tree.step) >= WE().consts.size.tree.min)) {
		toggleTree();
	}
}
