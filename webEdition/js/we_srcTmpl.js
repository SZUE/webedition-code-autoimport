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

/* global _EditorFrame */
var el = document.getElementById('loadVarSrcTmpl');
var doc = (el ?
				WE().util.getDynamicVar(el.getAttribute('data-doc')) :
				{}
);

var editor = null;
var weIsTextEditor = true;
var reloadContent = false;
var lastPos = null, lastQuery = null, marked = [];
var countJEditorInitAttempts = 0;
var wizardHeight = {
	"open": 305,
	"closed": 140
};

function initCM() {
	try {
		document.getElementById("bodydiv").style.display = "block";
		editor = CodeMirror.fromTextArea(document.getElementById("editarea"), CMoptions);
		sizeEditor();
		if (doc.editorHighlightCurrentLine) {
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
	document.getElementById("bodydiv").style.display = "block";
	sizeEditor();
	window.setTimeout(scrollToPosition, 50);
}

function sizeEditor() { // to be fixed (on 12.12.11)
	if (window.editor && window.editor.frame && window.editor.frame.nextSibling !== undefined) {
		document.getElementById("reindentButton").style.marginRight = (window.editor.frame.nextSibling.offsetWidth - 3) + "px";
	}
	var srtable = document.getElementById("srtable");
	if (srtable) {
		var wizardTable = document.getElementById("weTMPLDocEdit");
		var editorDiv = document.getElementById("editorDiv");
		var editarea = document.getElementById("editarea");
		var cm = document.getElementsByClassName("CodeMirror");

		editorDiv.style.bottom = (wizardTable ? wizardTable.offsetHeight : 0) + "px";
		editarea.style.height = srtable.offsetTop + "px";
		if (cm && cm.length) {
			cm[0].style.height = srtable.offsetTop + "px";
		}

		window.scroll(0, 0);
	}
}

// ################## Textarea specific functions #############

function getScrollPosTop() {
	var elem = document.getElementById("editarea");
	return (elem ? elem.scrollTop : 0);
}

function getScrollPosLeft() {
	var elem = document.getElementById("editarea");
	return  (elem ? elem.scrollLeft : 0);
}

function scrollToPosition() {
	var elem = document.getElementById("editarea");
	if (elem) {
		elem.scrollTop = parent.editorScrollPosTop;
		elem.scrollLeft = parent.editorScrollPosLeft;
	}
}

function wedoKeyDown(ta, ev) {
	modifiers = (ev.altKey || ev.ctrlKey || ev.shiftKey);
	if (!modifiers && ev.keycode == 9) { // TAB
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
		}
		if (document.selection) {
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
	document.forms.we_form.elements['we_' + doc.docName + '_txt[data]'].value = source;
	//Codemirror
	if (editor !== undefined && editor !== null && typeof editor === 'object') {
		editor.setValue(source);
	}
}

function getSource() {
	return document.forms.we_form.elements['we_' + doc.docName + '_txt[data]'].value;
}

function getCharset() {
	return doc.docCharSet;
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

function executeEditButton() {
	if (document.getElementById('weTagGroupSelect').value == 'snippet_custom') {
		YUIdoAjax(document.getElementById('codesnippet_custom').value);

	} else if (document.getElementById('weTagGroupSelect').value == 'snippet_standard') {
		YUIdoAjax(document.getElementById('codesnippet_standard').value);

	} else {
		var _sel = document.getElementById('tagSelection');
		if (_sel.selectedIndex > -1) {
			edit_wetag(_sel.value);
		}
	}
}

function YUIdoAjax(value) {
	YAHOO.util.Connect.asyncRequest('POST', WE().consts.dirs.WEBEDITION_DIR + "rpc.php", {
		success: function (o) {
			if (o.responseText !== undefined && o.responseText !== '') {
				document.getElementById('tag_edit_area').value = o.responseText;
			}
		},
		failure: function (o) {
			alert("Failure");
		}
	}, 'protocol=text&cmd=GetSnippetCode&we_cmd[1]=' + value);
}

function edit_wetag(tagname, insertAtCursor) {
	if (!insertAtCursor) {
		insertAtCursor = 0;
	}
	we_cmd("open_tag_wizzard", tagname, insertAtCursor);
}

function selectTagGroup(groupname) {
	switch (groupname) {
		case '-1':
			break;
		case "snippet_custom":
			document.getElementById('codesnippet_standard').style.display = 'none';
			document.getElementById('tagSelection').style.display = 'none';
			document.getElementById('codesnippet_custom').style.display = 'block';
			break;
		case "snippet_standard":
			document.getElementById('codesnippet_custom').style.display = 'none';
			document.getElementById('tagSelection').style.display = 'none';
			document.getElementById('codesnippet_standard').style.display = 'block';
			break;
		default:
			document.getElementById('codesnippet_custom').style.display = 'none';
			document.getElementById('codesnippet_standard').style.display = 'none';
			document.getElementById('tagSelection').style.display = 'block';
			elem = document.getElementById("tagSelection");
			var i;
			for (i = (elem.options.length - 1); i >= 0; i--) {
				elem.options[i] = null;
			}

			for (i = 0; i < tagGroups[groupname].length; i++) {
				elem.options[i] = new Option(tagGroups[groupname][i], tagGroups[groupname][i]);
			}
	}
}

function openTagWizWithReturn(Ereignis) {
	if (!Ereignis) {
		Ereignis = window.event;
	}
	if (Ereignis.which) {
		Tastencode = Ereignis.which;
	} else if (Ereignis.keyCode) {
		Tastencode = Ereignis.keyCode;
	}
	if (Tastencode == 13) {
		edit_wetag(document.getElementById("tagSelection").value);
	}
	//return false;
}

function openTagWizardPrompt(_wrongTag) {
	var _prompttext = WE().consts.g_l.weTagWizard.insert_tagname;
	if (_wrongTag) {
		_prompttext = WE().consts.g_l.weTagWizard.insert_tagname_not_exist.replace(/_wrongTag/, _wrongTag) + _prompttext;
	}

	var _tagName = prompt(_prompttext);
	var _tagExists = false;

	if (typeof (_tagName) == "string") {
		for (i = 0; i < tagGroups.alltags.length && !_tagExists; i++) {
			if (tagGroups.alltags[i] == _tagName) {
				_tagExists = true;
			}
		}

		if (_tagExists) {
			edit_wetag(_tagName, 1);
		} else {
			openTagWizardPrompt(_tagName);

		}
	}
}

function insertAtStart(tagText) {
	if (window.editor && window.editor.frame) {
		window.editor.insertIntoLine(window.editor.firstLine(), 0, tagText + "\n");
	} else {
		document.we_form["we_" + doc.docName + "_txt[data]"].value = tagText + "\n" + document.we_form["we_" + doc.docName + "_txt[data]"].value;
	}
	_EditorFrame.setEditorIsHot(true);
}

function insertAtEnd(tagText) {
	if (window.editor && window.editor.frame) {
		window.editor.insertIntoLine(window.editor.lastLine(), "end", "\n" + tagText);
	} else {
		document.we_form["we_" + doc.docName + "_txt[data]"].value += "\n" + tagText;
	}
	_EditorFrame.setEditorIsHot(true);
}

function addCursorPosition(tagText) {
	if (window.editor && window.editor.frame) {
		window.editor.replaceSelection(tagText);
		return;
	}
	var weForm = document.we_form["we_" + doc.docName + "_txt[data]"];
	if (document.selection) {
		weForm.focus();
		document.selection.createRange().text = tagText;
		document.selection.createRange().select();
	} else if (weForm.selectionStart || weForm.selectionStart == "0") {
		intStart = weForm.selectionStart;
		intEnd = weForm.selectionEnd;
		weForm.value = (weForm.value).substring(0, intStart) + tagText + (weForm.value).substring(intEnd, weForm.value.length);
		window.setTimeout(scrollToPosition, 50);
		weForm.focus();
		weForm.selectionStart = parseInt(intStart) + tagText.length;
		weForm.selectionEnd = parseInt(intStart) + tagText.length;
	} else {
		weForm.value += tagText;
	}
}

function refreshContentCompare() {
	window.orignalTemplateContent = document.getElementById("editarea").value.replace(/\r/g, "\n");
}

function editorChanged() {
	_EditorFrame.setEditorIsHot(window.orignalTemplateContent !== document.getElementById("editarea").value.replace(/\r/g, "\n"));
}