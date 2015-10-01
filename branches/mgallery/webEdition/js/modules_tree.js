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

var oldTreeWidth = top.WE().consts.size.tree.default;

function toggleTree() {
	var tfd = self.document.getElementById("left");
	var w = getTreeWidth();

	if (tfd.style.display == "none") {
		oldTreeWidth = (oldTreeWidth < top.WE().consts.size.tree.min ? top.WE().consts.size.tree.default : oldTreeWidth);
		setTreeWidth(oldTreeWidth);
		tfd.style.display = "block";
		setTreeArrow("left");
		storeTreeWidth(oldTreeWidth);
	} else {
		tfd.style.display = "none";
		oldTreeWidth = w;
		setTreeWidth(top.WE().consts.size.tree.hidden);
		setTreeArrow("right");
	}
}

function setTreeArrow(direction) {
	try {
		var arrImg = self.document.getElementById("arrowImg");
		if (direction == "right") {
			arrImg.classList.remove("fa-caret-left");
			self.document.getElementById("incBaum").style.backgroundColor = "gray";
			self.document.getElementById("decBaum").style.backgroundColor = "gray";
		} else {
			arrImg.classList.remove("fa-caret-right");
			self.document.getElementById("incBaum").style.backgroundColor = "";
			self.document.getElementById("decBaum").style.backgroundColor = "";
		}
		arrImg.classList.add("fa-caret-" + direction);
	} catch (e) {
		// Nothing
	}
}

function getTreeWidth() {
	var w = self.document.getElementById("lframeDiv").style.width;
	return w.substr(0, w.length - 2);
}

function setTreeWidth(w) {
	self.document.getElementById("lframeDiv").style.width = w + "px";
	self.document.getElementById("right").style.left = w + "px";
	if (w > top.WE().consts.size.tree.hidden) {
		storeTreeWidth(w);
	}
}

function storeTreeWidth(w) {
	var ablauf = new Date();
	var newTime = ablauf.getTime() + 30758400000;
	ablauf.setTime(newTime);
	weSetCookie(currentModule, w, ablauf, "/");
}

function incTree() {
	var w = parseInt(getTreeWidth());
	if ((w > top.WE().consts.size.tree.min) && (w < top.WE().consts.size.tree.max)) {
		w += top.WE().consts.size.tree.step;
		setTreeWidth(w);
	}
	if (w >= top.WE().consts.size.tree.max) {
		w = top.WE().consts.size.tree.max;
		self.document.getElementById("incBaum").style.backgroundColor = "grey";
	}
}

function decTree() {
	var w = parseInt(getTreeWidth());
	w -= top.WE().consts.size.tree.step;
	if (w > top.WE().consts.size.tree.min) {
		setTreeWidth(w);
		self.document.getElementById("incBaum").style.backgroundColor = "";
	}
	if (w <= top.WE().consts.size.tree.min && ((w + top.WE().consts.size.tree.step) >= top.WE().consts.size.tree.min)) {
		toggleTree();
	}
}

function weSetCookie(module, value, expires, path, domain) {
	var moduleVals = sizeTreeJsWidth;
	var doc = self.document;
	moduleVals[module] = value;
	var val = "";
	for (var param in moduleVals) {
		val += val ? "," + param + ":" + moduleVals[param] : param + " : " + moduleVals[param];
	}
	doc.cookie = "treewidth_modules" + "=" + val +
					((expires === null) ? "" : "; expires=" + expires.toGMTString()) +
					((path === null) ? "" : "; path=" + path) +
					((domain === null) ? "" : "; domain=" + domain);
}