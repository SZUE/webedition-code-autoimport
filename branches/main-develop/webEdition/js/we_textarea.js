/* global WE, top */

/**
 *
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

var weTextarea_count = 0;

//FIXME: change/remove this!
var we_textarea = function (name, value, autobr, autobrName, showAutobr, attribs, xml) {
	showSpell = false;
	this.TAName = name;
	this.name = "weTextarea" + (weTextarea_count++);
	this.obj = this.name + "Object";
	this.autobr = (autobr === "on") ? true : false;
	this.autobrName = autobrName;
	this.xml = xml;

	this.nl2br = function (i, xml) {
		return (xml ?
			i.replace(/\r\n/g, "<br />").replace(/\n/g, "<br />").replace(/\r/g, "<br />").replace(/<br *\/>/g, "<br />\n") :
			i.replace(/\r\n/g, "<br>").replace(/\n/g, "<br>").replace(/\r/g, "<br>").replace(/<br>/g, "<br>\n")
			);
	};
	this.br2nl = function (i) {
		return i.replace(/[\n\r]/g, "").replace(/<br *\/?>/gi, "\n");
	};
	this.appendText = function (text) {
		this.form.elements[this.TAName].value += text;
		this.form.elements["areatmp_" + this.TAName].value = (this.autobr ?
			this.br2nl(this.form.elements[this.TAName].value) :
			this.form.elements[this.TAName].value);
	};
	this.htmlspecialchars = function (i) {
		return i.replace(/&/g, "&amp;").replace(/"/g, "&quot;").replace(/'/g, "&#039;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
	};

	var val = (value ?
		value.replace(/##\|n##/gi, "\n").replace(/<##scr#ipt##/gi, "<script").replace(/<\/##scr#ipt##/gi, "</script").replace(/##\|lt\;\?##/gi, "<?") :
		""
		);

	document.writeln(
		'<input type="hidden" name="' + autobrName + '" value="' + (this.autobr ? 'on' : 'off') + '">' +
		'<table class="default" style="background-color: #F5F5F5;">' +
		(showAutobr ?
			'<tr><td><table class="default"><tr>' +
			'<td><input type="checkbox" name="check' + this.TAName + '" id="check' + this.TAName + '" onchange="' + this.name + 'Object.setAutoBr(this);"' + (this.autobr ? ' checked="checked"' : '') + '>&nbsp;</td>' +
			'<td style="color:black;font-weight: bold; font-size: 10px; cursor: pointer;"><label for="check' + this.TAName + '">autobr</label></td>' +
			'</tr></table></td></tr>'
			:
			"") +
		'<tr><td><textarea name="areatmp_' + this.TAName + '" ' + attribs + ' onkeydown="window.' + this.name + 'Object.onchange(this);" onblur="window.' + this.name + 'Object.onblur(this);">' +
		(val ? this.htmlspecialchars(this.autobr ? this.br2nl(val) : val) : '') +
		'</textarea>' +
		'<input type="hidden" name="' + this.TAName + '" value=""></td></tr></table>'
		);
	this.form = null;

	for (var i = 0; i < document.forms.length; i++) {
		if (document.forms[i].elements[name]) {
			this.form = document.forms[i];
			break;
		}
	}
	if (this.form !== null) {
		this.form.elements[name].value = val;
	}

	this.translate = function (check) {
		if (check.checked) {
			this.autobr = true;
			check.form.elements["areatmp_" + this.TAName].value = this.br2nl(check.form.elements[this.TAName].value);
		} else {
			this.autobr = false;
			check.form.elements["areatmp_" + this.TAName].value = check.form.elements[this.TAName].value;
		}
	};

	this.onblur = function (elem) {
		document.getElementsByName(this.TAName).value = (this.autobr ? this.nl2br(elem.value, this.xml) : elem.value);
	};

	this.onchange = function (elem) {
		if (top._EditorFrame && elem.value !== elem.form.elements[this.TAName].value) {
			top._EditorFrame.setEditorIsHot(true);
		}
		document.getElementsByName(this.TAName).value = (this.autobr ? this.nl2br(elem.value, this.xml) : this.value);
	};

	this.setAutoBr = function (elem) {
		this.translate(elem);
		document.getElementsByName(this.autobrName).value = (elem.checked ? 'on' : 'off');
	};
	//FIXME: do we need this as a global var?
	window[this.obj] = this;
};

//used for we:userInput
function open_wysiwyg_win() {
	var url = "/webEdition/we_cmd_frontend.php?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}

	/*if (window.screen) {
	 h = ((screen.height - 100) > screen.availHeight) ? screen.height - 100 : screen.availHeight;
	 w = screen.availWidth;
	 }*/
	var wyw = Math.max(arguments[2], arguments[9]);
	wyw = wyw ? wyw : 800;
	var wyh = parseInt(arguments[3]) + parseInt(arguments[10]);
	wyh = wyh ? wyh : 600;
	if (window.screen) {
		var screen_height = ((screen.height - 50) > screen.availHeight) ? screen.height - 50 : screen.availHeight;
		screen_height = screen_height - 40;
		var screen_width = screen.availWidth - 10;
		wyw = Math.min(screen_width, wyw);
		wyh = Math.min(screen_height, wyh);
	}
// set new width & height;

	url = url.replace(/we_cmd\[2\]=[^&]+/, "we_cmd[2]=" + wyw).replace(/we_cmd\[3\]=[^&]+/, "we_cmd[3]=" + (wyh - arguments[10]));
	new (WE !== undefined ? WE().util.jsWindow : jsWindow)(window, url, "we_wysiwygWin", Math.max(220, wyw + (document.all ? 0 : ((navigator.userAgent.toLowerCase().indexOf('safari') > -1) ? 20 : 4))), Math.max(100, wyh + 60), true, false, true);
}
