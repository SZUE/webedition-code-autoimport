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
	this.TAName = name;
	this.name = "weTextarea" + (weTextarea_count++);
	this.obj = this.name + "Object";
	this.autobr = (autobr == "on") ? true : false;
	this.nl2br = we_textarea_nl2br;
	this.br2nl = we_textarea_br2nl;
	this.appendText = we_textarea_appendText;
	this.htmlspecialchars = we_textarea_htmlspecialchars;
	this.ButtonNormal = we_textarea_ButtonNormal;
	this.ButtonOverUp = we_textarea_ButtonOverUp;
	this.ButtonOverDown = we_textarea_ButtonOverDown;
	this.ButtonDown = we_textarea_ButtonDown;
	this.xml = xml;
	this.id = id;
	if (style.length) {
		if (style.substring(style.length - 1, style.length) != ";") {
			style += ";";
		}
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
		val = val.replace(/<##scr#ipt##/gi, "<script");
		val = val.replace(/<\/##scr#ipt##/gi, "</script");
		val = val.replace(/##\|lt\;\?##/gi, "<?");
	}
	out = '<input type="hidden" name="' +
					autobrName +
					'" value="' +
					(this.autobr ? 'on' : 'off') +
					'"><table class="default" style="background-color: #F5F5F5;">';
	if (showAutobr) {
		out += '<tr><td><table class="default">' +
						'<td><input type="checkbox" name="check' +
						name +
						'" id="check' +
						name +
						'" onClick="if(self.' + this.name + 'Object){' +
						this.name +
						'Object.translate(this);this.form.elements[\'' + autobrName + '\'].value=(this.checked ? \'on\' : \'off\');}"' +
						(this.autobr ? ' checked' : '') +
						'>&nbsp;</td><td style=" color:black;font-weight: bold; font-size: 10px; font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;cursor: pointer;" onClick="if(self.' + this.name + 'Object){var cb=document.getElementById(\'check' + name + '\');cb.checked=cb.checked ? false : true;' + this.name + 'Object.translate(cb);cb.form.elements[\'' + autobrName + '\'].value=(cb.checked ? \'on\' : \'off\');}">autobr</td>';
	}

	if (showAutobr && (showRTF || showSpell)) {
		out += '<td style="color:black;font-weight: bold; font-size: 10px; font-family: Verdana, Geneva, Arial, Helvetica, sans-serif">&nbsp;</td><td><div unselectable="on" style="border-right: #999999 solid 1px; font-size: 0px; height:22px; width:2px;"></div></td><td style="color:black;font-weight: bold; font-size: 10px; font-family: Verdana, Geneva, Arial, Helvetica, sans-serif">&nbsp;</td>';
	}

	if (showSpell) {
		out += '<td unselectable="on"><div unselectable="on">' +
						'<img  style="border: 0px; margin: 1px;" unselectable="on" width="23" height="22" src="/webEdition/images/wysiwyg/spellcheck.gif"' +
						'onmouseover="if(self.' + this.name + 'Object){' + this.name + 'Object.ButtonOverUp(this);}"' +
						'onmouseout="if(self.' + this.name + 'Object){' + this.name + 'Object.ButtonNormal(this);}"' +
						'onmousedown="if(self.' + this.name + 'Object){' + this.name + 'Object.ButtonOverDown(this);}"' +
						'onclick="window.open(\'/webEdition/we/include/we_modules/spellchecker/weSpellchecker.php?editname=areatmp_' + encodeURI(name) + '\',\'spellchechecker\',\'height=450,width=500,scrollbars=0\');"></div></td>';
	}

	if (showAutobr) {
		out += '</table></td></tr>';
	}

	out += '<tr><td><textarea name="areatmp_' + name + '"' +
					'class="' + (classname ? classname + ' ' : '') + 'wetextarea wetextarea-' + origName + '"' +
					(cols ? ' cols="' + cols + '"' : '') +
					(wrap ? ' wrap="' + wrap + '"' : '') +
					(rows ? ' rows="' + rows + '"' : '') +
					(id ? ' id="' + id + '"' : '') +
					(style ? ' style="' + style + '"' : '') +
					' ' + changehandler + '="if (_EditorFrame && this.value != this.form.elements[\'' +
					name + '\'].value){_EditorFrame.setEditorIsHot(true)};this.form.elements[\'' +
					name +
					'\'].value=(' +
					this.name +
					'Object.autobr ? ' +
					this.name +
					'Object.nl2br(this.value,' + (this.xml ? 'true' : 'false') + ') : this.value);" onblur="if(self.' + this.name + 'Object){this.form.elements[\'' +
					name +
					'\'].value=(' +
					this.name + 'Object.autobr ? ' +
					this.name +
					'Object.nl2br(this.value,' + (this.xml ? 'true' : 'false') + ') : this.value);}">' +
					(val ? this.htmlspecialchars(this.autobr ? this.br2nl(val) : val) : '') +
					'</textarea>' +
					'<input type="hidden" name="' + name + '" value=""></td></tr></table>';
	this.form = null;
	document.writeln(out);
	for (var i = 0; i < document.forms.length; i++) {
		if (document.forms[i].elements[name]) {
			this.form = document.forms[i];
			break;
		}
	}
	if (this.form !== null) {
		this.form.elements[name].value = val;
	}

	this.translate = we_textarea_translate;
	eval(this.obj + "=this");
}

function we_textarea_translate(check) {
	if (check.checked) {
		this.autobr = true;
		check.form.elements["areatmp_" + this.TAName].value = this.br2nl(check.form.elements[this.TAName].value);
	} else {
		this.autobr = false;
		check.form.elements["areatmp_" + this.TAName].value = check.form.elements[this.TAName].value;
	}
}

function we_textarea_appendText(text) {
	this.form.elements[this.TAName].value += text;
	if (this.autobr) {
		this.form.elements["areatmp_" + this.TAName].value = this.br2nl(this.form.elements[this.TAName].value);
	} else {
		this.form.elements["areatmp_" + this.TAName].value = this.form.elements[this.TAName].value;
	}
}


function we_textarea_nl2br(i, xml) {
	if (!xml) {
		i = i.replace(/\r\n/g, "<br>");
		i = i.replace(/\n/g, "<br>");
		i = i.replace(/\r/g, "<br>");
		return i.replace(/<br>/g, "<br>\n");
	} else {
		i = i.replace(/\r\n/g, "<br />");
		i = i.replace(/\n/g, "<br />");
		i = i.replace(/\r/g, "<br />");
		return i.replace(/<br *\/>/g, "<br />\n");
	}
}
function we_textarea_br2nl(i) {
	i = i.replace(/[\n\r]/g, "");
	return i.replace(/<br *\/?>/gi, "\n");
}

function we_textarea_htmlspecialchars(i) {
	i = i.replace(/&/g, "&amp;");
	i = i.replace(/"/g, "&quot;");
	i = i.replace(/'/g, "&#039;");
	i = i.replace(/</g, "&lt;");
	return i.replace(/>/g, "&gt;");
}

function we_textarea_ButtonNormal(bt) {
	bt.style.border = "0px groove";
	bt.style.margin = "1px";
}

function we_textarea_ButtonOverUp(bt) {
	bt.style.margin = "0px";
	bt.style.borderBottom = "#000000 solid 1px";
	bt.style.borderLeft = "#CCCCCC solid 1px";
	bt.style.borderRight = "#000000 solid 1px";
	bt.style.borderTop = "#CCCCCC solid 1px";
}

function we_textarea_ButtonOverDown(bt) {
	bt.style.margin = "0px";
	bt.style.borderBottom = "#CCCCCC solid 1px";
	bt.style.borderLeft = "#000000 solid 1px";
	bt.style.borderRight = "#CCCCCC solid 1px";
	bt.style.borderTop = "#000000 solid 1px";
}

function we_textarea_ButtonDown(bt) {
	bt.style.margin = "0px";
	bt.style.backgroundColor = "#dfdfdf";
	bt.style.borderBottom = "#CCCCCC solid 1px";
	bt.style.borderLeft = "#000000 solid 1px";
	bt.style.borderRight = "#CCCCCC solid 1px";
	bt.style.borderTop = "#000000 solid 1px";
}

//used for we:userInput
function open_wysiwyg_win() {
	var url = "/webEdition/we_cmd_frontend.php?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1))
			url += "&";
	}

	if (window.screen) {
		h = ((screen.height - 100) > screen.availHeight) ? screen.height - 100 : screen.availHeight;
		w = screen.availWidth;
	}
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
	//doPostCmd(arguments,"we_wysiwygWin");
}
