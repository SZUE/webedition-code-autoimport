/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev: 9450 $
 * $Author: mokraemer $
 * $Date: 2015-03-02 00:54:31 +0100 (Mo, 02. Mär 2015) $
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
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */


function customAdapter() {
	this.innerHTML;
	this.getSelectedText = function () {
	}
}

function setDialog() {
	if (mode === 'tinyMce') {
		editorObj = tinyMCEPopup.editor;
		var text = editorObj.selection.isCollapsed() ? editorObj.getContent({format: "html"}) : editorObj.selection.getContent({format: "html"});
	} else {
		var elements = top.opener.document.getElementsByName(editname);
		if (elements[0]) {
			editorObj = elements[0];
			var text = editorObj.value;
		}
	}

	orginal = text;
	editPanel = document.getElementById('preview');
	editPanel.innerHTML = text;
	setTimeout(setAppletCode, 1000);
}

function getTextFromWysiwyg() {
	var text = "";
	var elements = top.opener.document.getElementsByName(editname);
	if (elements[0]) {
		editorObj = elements[0];
	}

	if (editorObj.getSelectedText) {
		text = editorObj.getSelectedText();
		rangeSelection = true;
	} else if (editorObj.dom.getSelectedText) {
		text = editorObj.dom.getSelectedText();
		rangeSelection = true;

	}

	if (text == "") {
		text = editorObj.getHTML();
		rangeSelection = false;
	}

	return text;
}

function fade(id, opacity) {
	var styleObj = document.getElementById(id).style;
	styleObj.opacity = (opacity / 100);
	styleObj.MozOpacity = (opacity / 100);
	styleObj.KhtmlOpacity = (opacity / 100);
	styleObj.filter = "alpha(opacity=" + opacity + ")";
}

function fadeout(id, from, step, speed) {
	fade(id, from);
	if (from == 0) {
		document.getElementById(id).style.display = "none";
	} else {
		setTimeout("fadeout(\"" + id + "\"," + (from - step) + "," + step + "," + speed + ")", speed);
	}
}

function apply() { // imi
	if (mode === 'tinyMce') {
		if (editorObj.selection.isCollapsed()) {
			editorObj.execCommand('mceSetContent', false, orginal);
		} else {
			editorObj.execCommand('mceInsertContent', false, orginal);
		}
	} else {
		editorObj.value = orginal;
	}
}


function markWord(word) {
	editPanel.innerHTML = orginal;
	editPanel.innerHTML = replaceWord(editPanel.innerHTML, word);

	var first = document.getElementById("highlight0");
	if (first) {
		if (first.offsetTop - 30 > 0) {
			editPanel.scrollTop = first.offsetTop - 30;
		} else {
			editPanel.scrollTop = 0;
		}
	}
}

function changeWord() {
	if (document.spellchecker.isReady()) {
		editPanel.innerHTML = orginal;
		editPanel.innerHTML = replaceWord(editPanel.innerHTML, found, document.we_form.search.value);
		orginal = editPanel.innerHTML;
		findNext();
	}
}

function removeHighlight() {
	editPanel.innerHTML = orginal;
}

function replaceWord(text, search) {

	var replacement = "";
	var i = -1;
	var c = 0;
	var searchsmall = search.toLowerCase();
	var textsmall = text.toLowerCase();

	while (text.length > 0) {

		i = textsmall.indexOf(searchsmall, i + 1);
		if (i < 0) {
			replacement += text;
			text = "";
		} else {

			var next = textsmall.substr(i + searchsmall.length, 1);
			var last = textsmall.substr(i - 1, 1);

			if (next.search("[a-zA-Z0-9]") == -1 && last.search("[a-zA-Z0-9]") == -1) {

				if (text.lastIndexOf(">", i) >= text.lastIndexOf("<", i)) {
					if (textsmall.lastIndexOf("/script>", i) >= textsmall.lastIndexOf("<script", i)) {

						if (arguments[2]) {
							replacement += text.substring(0, i) + arguments[2];
						} else {
							replacement += text.substring(0, i) + "<span class='highlight' id='highlight" + c + "'>" + text.substr(i, search.length) + "</span>";
							c++;
						}

						text = text.substr(i + search.length);
						textsmall = text.toLowerCase();
						i = -1;
					}
				}
			}
		}
	}

	return replacement;
}

function getTextOnly(text) {
	var newtext = text.replace(/(<([^>]+)>)/ig, " ");
	newtext = newtext.replace(/\&([^; ]+);/ig, " ");
	newtext = newtext.replace("&amp;", "&");
	return newtext;
}

function selectDict(dict) {
	hiddenCmd.dispatch("setLangDict", dict);
}

function reloadDoc() {
	location.reload();
}

function enableButtons() {
	weButton.enable("ignore");
	weButton.enable("change");
	weButton.enable("add");
}

function disableButtons() {
	weButton.disable("ignore");
	weButton.disable("change");
	weButton.disable("add");
	weButton.disable("check");
}
