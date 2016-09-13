/* global WE */

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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
var orginal;
var retryjava = 0;
var retry = 0;
var to;
var counter = 0;
var table;
var Combobox;

function applyOnEnter() {
	top.frames.glossarycheck.checkForm();
	return true;
}

function closeOnEscape() {
	return true;
}

function customAdapter() {
	this.getSelectedText = function () {
	};
}

function spellcheck() {
	retry = 0;
	if (document.spellchecker.isReady()) {
		top.frames.glossarycheck.document.getElementById("statusText").innerHTML = WE().consts.g_l.glossary.checking + "...";
		var text = getTextOnly(orginal);
		document.spellchecker.check(text);
		window.setTimeout(findNext, 2000);
	} else {
		if (retryjava < 5) {
			window.setTimeout(spellcheck, 1000);
			retryjava++;
		} else {
			fadeout("spinner", 80, 10, 10);
			top.frames.glossarycheck.noJava();
		}
	}
}

function findNext() {
	if (document.spellchecker.isReady()) {
		if (document.spellchecker.isReady()) {
			if (document.spellchecker.nextSuggestion()) {
				temp = document.spellchecker.getMisspelledWord();
				var suggs = document.spellchecker.getSuggestions();
				suggs = suggs + "";
				var suggA = suggs.split("|");
				top.frames.glossarycheck.addRow(temp, suggA);

				clearTimeout(to);
				to = window.setTimeout(findNext, 250);

			} else if (document.spellchecker.isWorking()) {
				clearTimeout(to);
				to = window.setTimeout(findNext, 250);

			} else if (retry < 7) {
				clearTimeout(to);
				to = window.setTimeout(findNext, 250);
				retry++;

			} else {
				if (top.frames.glossarycheck.document.getElementById("spinner").style.display != "none") {
					fadeout("spinner", 80, 10, 10);
					top.frames.glossarycheck.activateButtons();
				}
				retry = 0;
				clearTimeout(to);
			}
		}
	} else {
		window.setTimeout(spellcheck, 250);
	}
}

function add() {
	document.spellchecker.addWords(top.frames.glossarycheck.AddWords);
}

function getTextOnly(text) {
	var newtext = text.replace(/(<([^>]+)>)/ig, " ");
	newtext = newtext.replace(/\&([^; ]+);/ig, " ");
	newtext = newtext.replace("&amp;", "&");

	return newtext;

}

function fade(id, opacity) {
	var styleObj = top.frames.glossarycheck.document.getElementById(id).style;
	styleObj.opacity = (opacity / 100);
	styleObj.MozOpacity = (opacity / 100);
	styleObj.KhtmlOpacity = (opacity / 100);
	styleObj.filter = "alpha(opacity=" + opacity + ")";
}

function fadeout(id, from, step, speed) {
	fade(id, from);
	if (from === 0) {
		top.frames.glossarycheck.document.getElementById(id).style.display = "none";
	} else {
		window.setTimeout(fadeout, speed, id, (from - step), step, speed);
	}
}
function we_save_document() {
	top.opener.doc._showGlossaryCheck = 0;
	top.opener.we_save_document();
	top.close();
}

function we_reloadEditPage() {
	top.opener.top.we_cmd('switch_edit_page', doc.EditPageNr, transaction, 'save_document');
}

function getTextColumn(text, colspan) {
	text = text + '';
	var td = document.createElement('td');
	td.setAttribute('style', 'overflow: hidden;');
	td.setAttribute('title', text);
	if (colspan > 1) {
		td.setAttribute("colspan", colspan);
		td.setAttribute('style', 'text-align:center;vertical-align:middle;height:220px;');
	}
	if (text !== WE().consts.g_l.glossary.all_words_identified && text !== WE().consts.g_l.glossary.no_java) {
		text = shortenWord(text, 20);
	}

	td.appendChild(document.createTextNode(text));
	return td;
}

function shortenWord(text, chars) {
	var newText = "";
	var textlength = text.length;
	if (textlength > chars) {
		var showPointsFrom = Math.round(chars / 2) - 1;
		var showPointsTo = Math.round(chars / 2) + 1;
		for (var i = 0; i < chars; i++) {
			if (i < showPointsFrom) {
				newText += text.charAt(i);
			}
			if (i >= showPointsFrom && i <= showPointsTo) {
				newText += ".";
			}
			if (i > showPointsTo) {
				var pos = textlength - (chars - i);
				newText += text.charAt(pos);
			}
		}
	} else {
		newText = text;
	}

	return newText;
}

function getInnerColumn(html) {
	var td = document.createElement('td');
	td.innerHTML = html;
	return td;
}

function getImageColumn(src, width, height) {
	var td = document.createElement('td');
	td.innerHTML = '<img src="' + src + '" style="width:' + width + 'px;height:' + height + 'px" />';
	return td;
}

function getTitleColumn(word, suggestions, title) {
	var td = document.createElement('td');
	var html;

	html = '<input class="wetextinput" type="text" name="item[' + word + '][title]" size="24" value="' + title + '" maxlength="100" id="title_' + counter + '" style="display: inline; width: 200px;" disabled=\"disabled\" " />' +
					'<select class="defaultfont" name="suggest_' + counter + '" id="suggest_' + counter + '" onchange="document.getElementById(\'title_' + counter + '\').value=this.value;this.value=\'\';" disabled=\"disabled\" style="width: 200px; display: none;">' +
					'<option value="' + word + '">' + word + '</option>' +
					'<optgroup label="' + WE().consts.g_l.glossary.change_to + '">' +
					'<option value="">-- ' + WE().consts.g_l.glossary.input + ' --</option>' +
					'</optgroup>';
	if (suggestions.length > 1) {
		html += '<optgroup label="' + WE().consts.g_l.glossary.suggestions + '">';
		for (i = 0; i < suggestions.length; i++) {
			if (suggestions[i] !== '') {
				html += '<option value="' + suggestions[i] + '">' + suggestions[i] + '</option>';
			}
		}
		html += '</optgroup>';
	}
	html += '</select>';

	td.innerHTML = html;

	return td;
}

function getColumn(text) {
	var td = document.createElement('td');
	td.appendChild(document.createTextNode(text));
	return td;
}

function addRow(word, suggestions) {
	var tr = document.createElement('tr');
	tr = document.createElement('tr');
	tr.appendChild(getTextColumn(word, 1));
	tr.appendChild(getInnerColumn(' '));
	tr.appendChild(getActionColumn(word, ''));
	tr.appendChild(getInnerColumn(' '));
	tr.appendChild(getTitleColumn(word, suggestions, ''));
	tr.appendChild(getInnerColumn(' '));
	tr.appendChild(getLanguageColumn(word, ''));
	table.appendChild(tr);

	Combobox.init('suggest_' + counter, 'wetextinput');
	Combobox.init('lang_' + counter, 'wetextinput');

	counter++;
}

function addPredefinedRow(word, suggestions, type, title, lang) {
	var tr = document.createElement('tr');

	tr = document.createElement('tr');
	tr.appendChild(getTextColumn(word, 1));
	tr.appendChild(getInnerColumn(' '));
	tr.appendChild(getActionColumn(word, type));
	tr.appendChild(getInnerColumn(' '));
	tr.appendChild(getTitleColumn(word, suggestions, title));
	tr.appendChild(getInnerColumn(' '));
	tr.appendChild(getLanguageColumn(word, lang));
	table.appendChild(tr);

	Combobox.init('suggest_' + counter, 'wetextinput');
	Combobox.init('lang_' + counter, 'wetextinput');

	disableItem(counter, type);

	counter++;

}

function disableItem(id, value) {
	switch (value) {
		case WE().consts.glossary.TYPE_FOREIGNWORD :
			document.getElementById('title_' + id).disabled = true;
			document.getElementById('lang_' + id).disabled = false;
			document.getElementById('title_' + id).style.display = 'inline';
			document.getElementById('suggest_' + id).style.display = 'none';
			break;
		case 'ignore':
		case 'exception':
		case 'dictionary':
			document.getElementById('title_' + id).disabled = true;
			document.getElementById('lang_' + id).disabled = true;
			document.getElementById('suggest_' + id).style.display = 'none';
			document.getElementById('title_' + id).style.display = 'inline';
			break;
		case 'correct':
			document.getElementById('title_' + id).style.display = 'none';
			document.getElementById('lang_' + id).disabled = true;
			document.getElementById('suggest_' + id).disabled = false;
			document.getElementById('title_' + id).disabled = false;
			document.getElementById('suggest_' + id).style.display = 'inline';
			break;
		case "":
			document.getElementById('title_' + id).disabled = true;
			document.getElementById('lang_' + id).disabled = true;
			document.getElementById('suggest_' + id).style.display = 'none';
			document.getElementById('title_' + id).style.display = 'inline';
			break;
		default:
			document.getElementById('title_' + id).disabled = false;
			document.getElementById('lang_' + id).disabled = false;
			document.getElementById('suggest_' + id).style.display = 'none';
			document.getElementById('title_' + id).style.display = 'inline';
	}
}

