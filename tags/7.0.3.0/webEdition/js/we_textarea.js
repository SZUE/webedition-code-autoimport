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

weTextarea_count = 0;

//FIXME: change/remove this!
function we_textarea(name, value, cols, rows, width, height, autobr, autobrName, showAutobr, showRTF, language, classname, style, wrap, changehandler, xml, id, showSpell, origName) {
	showSpell = false;
	this.TAName = name;
	this.name = "weTextarea" + (weTextarea_count++);
	this.obj = this.name + "Object";
	this.autobr = (autobr == "on") ? true : false;
	this.autobrName = autobrName;
	this.xml = xml;
	this.id = id;

	this.nl2br = function (i, xml) {
		if (!xml) {
			i = i.replace(/\r\n/g, "<br>");
			i = i.replace(/\n/g, "<br>");
			i = i.replace(/\r/g, "<br>");
			return i.replace(/<br>/g, "<br>\n");
		}
		i = i.replace(/\r\n/g, "<br />");
		i = i.replace(/\n/g, "<br />");
		i = i.replace(/\r/g, "<br />");
		return i.replace(/<br *\/>/g, "<br />\n");
	};
	this.br2nl = function (i) {
		i = i.replace(/[\n\r]/g, "");
		return i.replace(/<br *\/?>/gi, "\n");
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
	this.ButtonNormal = function (bt) {
		bt.style.border = "0px groove";
		bt.style.margin = "1px";
	};
	this.ButtonOverUp = function (bt) {
		bt.style.margin = "0px";
		bt.style.borderBottom = "#000000 solid 1px";
		bt.style.borderLeft = "#CCCCCC solid 1px";
		bt.style.borderRight = "#000000 solid 1px";
		bt.style.borderTop = "#CCCCCC solid 1px";
	};
	this.ButtonOverDown = function (bt) {
		bt.style.margin = "0px";
		bt.style.borderBottom = "#CCCCCC solid 1px";
		bt.style.borderLeft = "#000000 solid 1px";
		bt.style.borderRight = "#CCCCCC solid 1px";
		bt.style.borderTop = "#000000 solid 1px";
	};
	this.ButtonDown = function (bt) {
		bt.style.margin = "0px";
		bt.style.backgroundColor = "#dfdfdf";
		bt.style.borderBottom = "#CCCCCC solid 1px";
		bt.style.borderLeft = "#000000 solid 1px";
		bt.style.borderRight = "#CCCCCC solid 1px";
		bt.style.borderTop = "#000000 solid 1px";
	};

	if (style.length && style.substring(style.length - 1, style.length) != ";") {
		style += ";";
	}
	if (width) {
		style += "width:" + width + "px;";
	}
	if (height) {
		style += "height:" + height + "px;";
	}
	val = value ? value : "";
	if (val) {
		val = val.replace(/##\|n##/gi, "\n");
		val = val.replace(/<##scr#ipt##/gi, "<script").replace(/<\/##scr#ipt##/gi, "</script");
		val = val.replace(/##\|lt\;\?##/gi, "<?");
	}
	document.writeln(
					'<input type="hidden" name="' + autobrName + '" value="' + (this.autobr ? 'on' : 'off') + '">' +
					'<table class="default" style="background-color: #F5F5F5;">' +
					(showAutobr ?
									'<tr><td><table class="default"><tr>' +
									'<td><input type="checkbox" name="check' + this.TAName + '" id="check' + this.TAName + '" onchange="' + this.name + 'Object.setAutoBr(this);"' + (this.autobr ? ' checked="checked"' : '') + '>&nbsp;</td>' +
									'<td style="color:black;font-weight: bold; font-size: 10px; cursor: pointer;"><label for="check' + this.TAName + '">autobr</label></td>' :
									"") +
					(showAutobr && (showRTF || showSpell) ?
									'<td style="color:black;font-weight: bold; font-size: 10px;">&nbsp;</td><td><div style="border-right: #999999 solid 1px; font-size: 0px; height:22px; width:2px;"></div></td><td>&nbsp;</td>' :
									"") +
					(showSpell ?
									'<td><div>' +
									'<img style="margin: 1px;width:23px;height:22px;" src="/webEdition/images/wysiwyg/spellcheck.gif"' +
									'onmouseover="' + this.name + 'Object.ButtonOverUp(this);"' +
									'onmouseout="' + this.name + 'Object.ButtonNormal(this);"' +
									'onmousedown="' + this.name + 'Object.ButtonOverDown(this);"' +
									'onclick="new (WE().util.jsWindow)(window, \'' + WE().consts.dirs.WE_MODULES_DIR + 'spellchecker/weSpellchecker.php?editname=areatmp_' + encodeURI(name) + '\', "spellchechecker", -1, -1, 450, 500, true, true, true);"></div></td>' :
									"") +
					(showAutobr ?
									'</tr></table></td></tr>' :
									"") +
					'<tr><td><textarea name="areatmp_' + this.TAName + '"' +
					'class="' + (classname ? classname + ' ' : '') + 'wetextarea wetextarea-' + origName + '"' +
					(cols ? ' cols="' + cols + '"' : '') +
					(wrap ? ' wrap="' + wrap + '"' : '') +
					(rows ? ' rows="' + rows + '"' : '') +
					(id ? ' id="' + id + '"' : '') +
					(style ? ' style="' + style + '"' : '') + ' ' +
					changehandler + '="self.' + this.name + 'Object.onchange(this);" onblur="self.' + this.name + 'Object.onblur(this);">' +
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
		elem.form.elements[this.TAName].value = (this.autobr ? this.nl2br(elem.value, this.xml) : elem.value);
	};

	this.onchange = function (elem) {
		if (top._EditorFrame && elem.value !== elem.form.elements[this.TAName].value) {
			top._EditorFrame.setEditorIsHot(true);
		}
		elem.form.elements[this.TAName].value = (this.autobr ? this.nl2br(elem.value, this.xml) : this.value);
	};

	this.setAutoBr = function (elem) {
		this.translate(elem);
		elem.form.elements[this.autobrName].value = (elem.checked ? 'on' : 'off');
	};
	//FIXME: do we need this as a global var?
	window[this.obj] = this;
}

//used for we:userInput
function open_wysiwyg_win() {
	var url = "/webEdition/we_cmd_frontend.php?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1))
			url += "&";
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

	url = url.replace(/we_cmd\[2\]=[^&]+/, "we_cmd[2]=" + wyw);
	url = url.replace(/we_cmd\[3\]=[^&]+/, "we_cmd[3]=" + (wyh - arguments[10]));
	new (WE !== undefined ? WE().util.jsWindow : jsWindow)(window, url, "we_wysiwygWin", -1, -1, Math.max(220, wyw + (document.all ? 0 : ((navigator.userAgent.toLowerCase().indexOf('safari') > -1) ? 20 : 4))), Math.max(100, wyh + 60), true, false, true);
}
