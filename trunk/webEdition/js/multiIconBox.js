/**
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

function weToggleBox(name, textDown, textRight) {
	var t = document.getElementById('table_' + name);
	var s = document.getElementById('text_' + name);
	var b = document.getElementById('btn_direction_' + name + '_middle');
	if (t.style.display == "none") {
		t.style.display = "";
		s.innerHTML = textDown;
		weSetCookieVariable("but_" + name, "down");
	} else {
		t.style.display = "none";
		s.innerHTML = textRight;
		weSetCookieVariable("but_" + name, "right");
	}
}

function toggleButton(but, name) {
	but.getElementsByTagName("i")[0].className = "fa fa-lg fa-caret-" + weGetCookieVariable("but_" + name);
}

function weGetCookieVariable(name) {
	var c = WE().util.weGetCookie((top.name == "edit_module") ? top.opener.top.document : top.document, "we" + WE().session.sess_id);
	var vals = [];
	if (c !== null) {
		var parts = c.split(/&/);
		for (var i = 0; i < parts.length; i++) {
			var foo = parts[i].split(/=/);
			vals[unescape(foo[0])] = unescape(foo[1]);
		}
		return vals[name];
	}
	return null;
}

function weSetCookieVariable(name, value) {
	var c = WE().util.weGetCookie((top.name == "edit_module") ? top.opener.top.document : top.document, "we" + WE().session.sess_id);
	var vals = [];
	var i;
	if (c !== null) {
		var parts = c.split(/&/);
		for (i = 0; i < parts.length; i++) {
			var foo = parts[i].split(/=/);
			vals[unescape(foo[0])] = unescape(foo[1]);
		}
	}
	vals[name] = value;
	c = "";
	for (i in vals) {
		c += encodeURI(i) + "=" + encodeURI(vals[i]) + "&";
	}
	if (c.length > 0) {
		c = c.substring(0, c.length - 1);
	}
	WE().util.weSetCookie((top.name == "edit_module") ? top.opener.top.document : top.document, "we" + WE().session.sess_id, c);
}