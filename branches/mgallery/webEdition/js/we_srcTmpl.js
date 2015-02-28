/*
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
var weIsTextEditor = true;
var reloadContent = false;
try {
	top.we_setEditorWasLoaded(false);
} catch (e) {

}
var countJEditorInitAttempts = 0;
var wizardHeight = {
	"open": 305,
	"closed": 140
};

var editor = null;


function initCM() {
	try {
		document.getElementById("bodydiv").style.display = "block";
		editor = CodeMirror.fromTextArea(document.getElementById("editarea"), CMoptions);
		sizeEditor();
		if (editorHighlightCurrentLine) {
			hlLine = editor.addLineClass(0, "background", "activeline");
			//highlight current line
			editor.on("cursorActivity", function () {
				var cur = editor.getLineHandle(editor.getCursor().line);
				if (cur != hlLine) {
					editor.removeLineClass(hlLine, "background", "activeline");
					hlLine = editor.addLineClass(cur, "background", "activeline");
				}
			});
		} else { //FIX for CM which doesn't display lines beyond 27 if this line is missing....                                     ?>
			hlLine = editor.addLineClass(0, "background", "");

		}
		editor.on("change", function () {
			//this wil save content from CodeMirror2 to our original <textarea>.
			var currentTemplateCode = editor.getValue().replace(/\r/g, "\n");
			if (window.orignalTemplateContent != currentTemplateCode) {
				document.getElementById("editarea").value = currentTemplateCode;
				_EditorFrame.setEditorIsHot(true);
			} else {
				document.getElementById("editarea").value = currentTemplateCode;
				_EditorFrame.setEditorIsHot(false);
			}
		});

	} catch (e) {
	}
}

function initDefaultEdior() {
	sizeEditor();
	document.getElementById("bodydiv").style.display = "block";
	window.setTimeout(scrollToPosition, 50);
}

function initJava() {
	countJEditorInitAttempts++;
	// imi: console.log("init: " + countJEditorInitAttempts);
	if (countJEditorInitAttempts < 10) {
		if (document.weEditorApplet && top.weEditorWasLoaded && document.weEditorApplet.setCode !== undefined && document.weEditorApplet.initUndoManager !== undefined) {
			try {
				sizeEditor();
				document.getElementById("weEditorApplet").style.left = "0";
				javaEditorSetCode();
				checkAndSetHot();
			} catch (err) {
				setTimeout(initJava, 500);
			}
		} else {
			setTimeout(initJava, 500);
		}
	} else {
		top.opener.we_showMessage(g_l.no_java, WE_MESSAGE_ERROR, window);
	}
}


function sizeEditor() { // to be fixed (on 12.12.11)
	var h = window.innerHeight ? window.innerHeight : document.body.offsetHeight;
	var w = window.innerWidth ? window.innerWidth : document.body.offsetWidth;
	w = Math.max(w, 350);
	var editorWidth = w - editorRightSpace;
	var wizardOpen = weGetCookieVariable("but_weTMPLDocEdit") == "right";

	var editarea = document.getElementById("editarea");

	var wizardTable = document.getElementById("wizardTable");
	var tagAreaCol = document.getElementById("tagAreaCol");
	var tagSelectCol = document.getElementById("tagSelectCol");
	var spacerCol = document.getElementById("spacerCol");
	var tag_edit_area = document.getElementById("tag_edit_area");

	if (editarea) {
		editarea.style.width = editorWidth + "px";
		if (editarea.nextSibling !== undefined && editarea.nextSibling.style) {
			editarea.nextSibling.style.width = editorWidth + "px";
		}
	}

	if (document.weEditorApplet && document.weEditorApplet.width !== undefined) {
		document.weEditorApplet.width = editorWidth;
	}

	if (window.editor && window.editor.frame) {
		if (window.editor.frame.nextSibling !== undefined) {
			editorWidth -= window.editor.frame.nextSibling.offsetWidth;
			document.getElementById("reindentButton").style.marginRight = (window.editor.frame.nextSibling.offsetWidth - 3) + "px";
		}
		window.editor.frame.style.width = editorWidth + "px";
	}

	if (h) { // h must be set (h!=0), if several documents are opened very fast -> editors are not loaded then => h = 0
		if (wizardTable !== null) {
			var editorHeight = (h - (wizardOpen ? wizardHeight.closed : wizardHeight.open));

			if (editarea) {
				editarea.style.height = (h - (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";
				if (editarea.nextSibling !== undefined && editarea.nextSibling.style)
					editarea.nextSibling.style.height = (h - (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";
			}

			if (window.editor && window.editor.frame) {
				window.editor.frame.style.height = (h - (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";
			}

			if (document.weEditorApplet && document.weEditorApplet.setSize !== undefined) {
				try {
					document.weEditorApplet.height = editorHeight;
					//document.weEditorApplet.setSize(editorWidth,editorHeight);
				} catch (err) {/*nothing*/
				}

			}

			wizardTable.style.width = editorWidth + "px";
			//wizardTableButtons.style.width=editorWidth+"px"; // causes problems with codemirror2
			tagAreaCol.style.width = (editorWidth - 300) + "px";
			tag_edit_area.style.width = (editorWidth - 300) + "px";
			tagSelectCol.style.width = "250px";
			spacerCol.style.width = "50px";

		} else {
			if (editarea) {
				editarea.style.height = (h - wizardHeight.closed) + "px";
				if (editarea.nextSibling !== undefined && editarea.nextSibling.style) {
					editarea.nextSibling.style.height = (h - wizardHeight.closed) + "px";
				}
			}

			if (window.editor && window.editor.frame) {
				window.editor.frame.style.height = (h - wizardHeight.closed) + "px";
			}

			if (document.weEditorApplet && document.weEditorApplet.setSize !== undefined) {
				try {
					document.weEditorApplet.height = h - wizardHeight.closed;
					//document.weEditorApplet.setSize(editorWidth,h - wizardHeight.closed);
				} catch (err) {/*nothing*/
				}
			}
		}
	}
	window.scroll(0, 0);
}


function javaEditorSetCode() {// imi: console.log("javaEditorSetCode() called");
	if (document.weEditorApplet.height != 3000) {
		try {
			document.weEditorApplet.setCode(document.forms.we_form.elements["we_" + docName + "_txt[data]"].value);
			countJEditorInitAttempts = 0;
		} catch (err) {
			setTimeout(javaEditorSetCode, 1000);
		}
	} else { // change size not yet finished
		setTimeout(javaEditorSetCode, 1000);
	}
}


function toggleTagWizard() {
	var w = window.innerWidth ? window.innerWidth : document.body.offsetWidth;
	w = Math.max(w, 350);
	var editorWidth = w - 37;
	var h = window.innerHeight ? window.innerHeight : document.body.offsetHeight;
	var wizardOpen = weGetCookieVariable("but_weTMPLDocEdit") == "down";
	if (document.weEditorApplet) {
		var editorHeight = h - (wizardOpen ? wizardHeight.closed : wizardHeight.open);
		document.weEditorApplet.height = editorHeight;
	} else {
		var editarea = document.getElementById("editarea");
		editarea.style.height = (h - (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";
		if (editarea.nextSibling !== undefined && editarea.nextSibling.style)
			editarea.nextSibling.style.height = (h - (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";

		if (window.editor && window.editor.frame) {
			window.editor.frame.style.height = (h - (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";
		}
	}
}

// ################ Java Editor specific Functions

function weEditorSetHiddenText() {
	if (document.weEditorApplet && document.weEditorApplet.getCode !== undefined) {
		if (document.weEditorApplet.isHot()) {
			_EditorFrame.setEditorIsHot(true);
			document.weEditorApplet.setHot(false);
		}
		document.forms.we_form.elements["we_" + docName + "_txt[data]"].value = document.weEditorApplet.getCode();
	}
}


function checkAndSetHot() {
	if (document.weEditorApplet && document.weEditorApplet.isHot !== undefined) {
		if (document.weEditorApplet.isHot()) {
			_EditorFrame.setEditorIsHot(true);
		} else {
			setTimeout(checkAndSetHot, 1000);
		}
	}
}


function setCode() {
	if (document.weEditorApplet && document.weEditorApplet.setCode !== undefined) {
		document.weEditorApplet.setCode(document.forms.we_form.elements["we_" + docName + "_txt[data]"].value);
	}
}

// ################## Textarea specific functions #############

function getScrollPosTop() {
	var elem = document.getElementById("editarea");
	if (elem) {
		return elem.scrollTop;
	}
	return 0;

}

function getScrollPosLeft() {
	var elem = document.getElementById("editarea");
	if (elem) {
		return elem.scrollLeft;
	}
	return 0;
}

function scrollToPosition() {
	var elem = document.getElementById("editarea");
	if (elem) {
		elem.scrollTop = parent.editorScrollPosTop;
		elem.scrollLeft = parent.editorScrollPosLeft;
	}
}

function wedoKeyDown(ta, keycode) {
	modifiers = (event.altKey || event.ctrlKey || event.shiftKey);
	if (!modifiers && keycode == 9) { // TAB
		if (ta.setSelectionRange) {
			var selectionStart = ta.selectionStart;
			var selectionEnd = ta.selectionEnd;
			ta.value = ta.value.substring(0, selectionStart) +
							"\t" +
							ta.value.substring(selectionEnd);
			ta.focus();
			ta.setSelectionRange(selectionEnd + 1, selectionEnd + 1);
			ta.focus();
			return false;

		} else if (document.selection) {
			var selection = document.selection;
			var range = selection.createRange();
			range.text = "\t";
			return false;
		}
	}

	return true;
}
// ############ EDITOR PLUGIN ################

function setSource(source) {
	document.forms.we_form.elements['we_' + docName + '_txt[data]'].value = source;
	//Codemirror
	if (editor !== undefined && editor !== null && typeof editor === 'object') {
		editor.setValue(source);
	}
	// for Applet
	setCode(source);
}

function getSource() {
	if (document.weEditorApplet && document.weEditorApplet.getCode !== undefined) {
		return document.weEditorApplet.getCode();
	}
	return document.forms.we_form.elements['we_' + docName + '_txt[data]'].value;

}

function getCharset() {
	return docCharSet;
}

// ############ CodeMirror Functions ################

function reindent() { // reindents code of CodeMirror2
	if (editor.somethingSelected()) {
		start = editor.getCursor(true).line;
		end = editor.getCursor(false).line;
	} else {
		start = 0;
		end = editor.lineCount();
	}
	for (i = start; i < end; ++i) {
		editor.indentLine(i, 'smart');
	}
}
var lastPos = null, lastQuery = null, marked = [];
function unmark() {
	for (var i = 0; i < marked.length; ++i) {
		marked[i].clear();
	}
	marked.length = 0;
}

function cmSearch(event) {
	if (event === null || event.keyCode === 13 || event.keyCode === 10) {
		search(document.getElementById("query").value, !document.getElementById("caseSens").checked);
	}
}

function cmReplace(event) {
	if (event === null || event.keyCode === 13 || event.keyCode === 10) {
		myReplace(document.getElementById("query").value, document.getElementById("replace").value, !document.getElementById("caseSens").checked);
	}
}

function search(text, caseIns) {
	unmark();
	if (!text) {
		return;
	}
	var cursor;
	for (cursor = editor.getSearchCursor(text, 0, caseIns); cursor.findNext(); ) {
		marked.push(editor.markText(cursor.from(), cursor.to(), {className: "searched"}));
	}
	if (lastQuery !== text) {
		lastPos = null;
	}
	cursor = editor.getSearchCursor(text, lastPos || editor.getCursor(), caseIns);
	if (!cursor.findNext()) {
		cursor = editor.getSearchCursor(text, 0, caseIns);
		if (!cursor.findNext()) {
			return;
		}
	}
	editor.setSelection(cursor.from(), cursor.to());
	marked.push(editor.markText(cursor.from(), cursor.to(), {className: "searchedHighlight"}));
	lastQuery = text;
	lastPos = cursor.to();
}

function myReplace(text, replaceby, caseIns) {
	if (!text) {
		return;
	}
	if (editor.getSelection() !== text) {
		search(text, caseIns);
	}
	if (editor.getSelection() !== text) {
		return;
	}
	editor.replaceSelection(replaceby);
	search(text, caseIns);
}
