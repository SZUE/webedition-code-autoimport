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

var oldTreeWidth = size.tree.default;

function toggleTree() {
	var tDiv = self.document.getElementById("left");
	var w = getTreeWidth();

	if (tDiv.style.display == "none") {
		oldTreeWidth = (oldTreeWidth < size.tree.min ? size.tree.default : oldTreeWidth);
		setTreeWidth(oldTreeWidth);
		tDiv.style.display = "block";
		setTreeArrow("left");
		storeTreeWidth(oldTreeWidth);
	} else {
		tDiv.style.display = "none";
		oldTreeWidth = w;
		setTreeWidth(size.tree.hidden);
		setTreeArrow("right");
	}
}

function setTreeArrow(direction) {
	try {
		self.document.getElementById("arrowImg").src = dirs.BUTTONS_DIR + "icons/direction_" + direction + ".gif";
		if (direction == "right") {
			self.document.getElementById("incBaum").style.backgroundColor = "gray";
			self.document.getElementById("decBaum").style.backgroundColor = "gray";
		} else {
			self.document.getElementById("incBaum").style.backgroundColor = "";
			self.document.getElementById("decBaum").style.backgroundColor = "";
		}
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
	if (w > size.tree.hidden) {
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
	if ((w > size.tree.min) && (w < size.tree.max)) {
		w += size.tree.step;
		setTreeWidth(w);
	}
	if (w >= size.tree.max) {
		w = size.tree.max;
		self.document.getElementById("incBaum").style.backgroundColor = "grey";
	}
}

function decTree() {
	var w = parseInt(getTreeWidth());
	w -= size.tree.step;
	if (w > size.tree.min) {
		setTreeWidth(w);
		self.document.getElementById("incBaum").style.backgroundColor = "";
	}
	if (w <= size.tree.min && ((w + size.tree.step) >= size.tree.min)) {
		toggleTree();
	}
}

function weSetCookie(module, value, expires, path, domain) {
	var moduleVals = size.tree.jsWidth;
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