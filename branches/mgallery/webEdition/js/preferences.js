/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 9449 $
 * $Author: mokraemer $
 * $Date: 2015-03-02 00:36:19 +0100 (Mo, 02. Mär 2015) $
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

mainXhtmlFields = ["setXhtml_remove_wrong", "setXhtml_show_wrong"];
showXhtmlFields = ["setXhtml_show_wrong_text", "setXhtml_show_wrong_js", "setXhtml_show_wrong_error_log"];

Array.prototype.contains = function (obj) {
	var i, listed = false;
	for (i = 0; i < this.length; i++) {
		if (this[i] === obj) {
			listed = true;
			break;
		}
	}
	return listed;
}

function checkAllRevert() {
	var checkbox = document.getElementById("version_all");
	checkbox.checked = false;
}

function openVersionWizard() {
	parent.opener.top.we_cmd("versions_wizard");

}

function disable_xhtml_fields(val, fields) {
	for (i = 0; i < fields.length; i++) {
		elem = document.forms[0][fields[i]];
		label = document.getElementById("label_" + fields[i]);
		if (val == 1) {
			elem.disabled = false;
			label.style.color = "black";
			label.style.cursor = document.all ? "hand" : "pointer";
		} else {
			elem.disabled = true;
			label.style.color = "grey";
			label.style.cursor = "";
		}
	}
}

function set_xhtml_field(val, field) {
	document.forms[0][field].value = (val ? 1 : 0);
}

function handle_message_reporting_click() {
	val = 0;
	var fields = new Array("message_reporting_notices", "message_reporting_warnings", "message_reporting_errors");
	for (i = 0; i < fields.length; i++) {

		if (document.getElementById(fields[i]).checked) {
			val += parseInt(document.getElementById(fields[i]).value);
		}
	}
	document.getElementById("message_reporting").value = val;
}

function set_state_error_handler() {
	if (document.getElementsByName('newconf[WE_ERROR_HANDLER]')[0].checked == true) {
		_new_state = false;
		_new_style = 'black';
		_new_cursor = document.all ? 'hand' : 'pointer';
	} else {
		_new_state = true;
		_new_style = 'gray';
		_new_cursor = '';
	}

	document.getElementsByName('newconf[WE_ERROR_NOTICES]')[0].disabled = _new_state;
	document.getElementsByName('newconf[WE_ERROR_WARNINGS]')[0].disabled = _new_state;
	document.getElementsByName('newconf[WE_ERROR_ERRORS]')[0].disabled = _new_state;
	document.getElementsByName('newconf[WE_ERROR_DEPRECATED]')[0].disabled = _new_state;

	document.getElementById('label_newconf[WE_ERROR_NOTICES]').style.color = _new_style;
	document.getElementById('label_newconf[WE_ERROR_WARNINGS]').style.color = _new_style;
	document.getElementById('label_newconf[WE_ERROR_ERRORS]').style.color = _new_style;
	document.getElementById('label_newconf[WE_ERROR_DEPRECATED]').style.color = _new_style;

	document.getElementById('label_newconf[WE_ERROR_NOTICES]').style.cursor = _new_cursor;
	document.getElementById('label_newconf[WE_ERROR_WARNINGS]').style.cursor = _new_cursor;
	document.getElementById('label_newconf[WE_ERROR_ERRORS]').style.cursor = _new_cursor;
	document.getElementById('label_newconf[WE_ERROR_DEPRECATED]').style.cursor = _new_cursor;

	document.getElementsByName('newconf[WE_ERROR_SHOW]')[0].disabled = _new_state;
	document.getElementsByName('newconf[WE_ERROR_LOG]')[0].disabled = _new_state;
	document.getElementsByName('newconf[WE_ERROR_MAIL]')[0].disabled = _new_state;

	document.getElementById('label_newconf[WE_ERROR_SHOW]').style.color = _new_style;
	document.getElementById('label_newconf[WE_ERROR_LOG]').style.color = _new_style;
	document.getElementById('label_newconf[WE_ERROR_MAIL]').style.color = _new_style;

	document.getElementById('label_newconf[WE_ERROR_SHOW]').style.cursor = _new_cursor;
	document.getElementById('label_newconf[WE_ERROR_LOG]').style.cursor = _new_cursor;
	document.getElementById('label_newconf[WE_ERROR_MAIL]').style.cursor = _new_cursor;
}

function set_state_auth() {
	if (document.getElementsByName('useauthEnabler')[0].checked == true) {
		document.getElementsByName('newconf[useauth]')[0].value = 1;
		_new_state = false;
	} else {
		document.getElementsByName('newconf[useauth]')[0].value = 0;
		_new_state = true;
	}

	document.getElementsByName('newconf[HTTP_USERNAME]')[0].disabled = _new_state;
	document.getElementsByName('newconf[HTTP_PASSWORD]')[0].disabled = _new_state;
}

function IsDigit(e) {
	var key = (e != null && e.charCode ? e.charCode : event.keyCode);
	return (((key >= 48) && (key <= 57)) || (key == 0) || (key == 13));
}

function setJavaEditorDisabled(disabled) {
	document.getElementById("_newconf[specify_jeditor_colors]").disabled = disabled;
	document.getElementById("label__newconf[specify_jeditor_colors]").style.color = (disabled ? "grey" : "");
	document.getElementById("label__newconf[specify_jeditor_colors]").style.cursor = (disabled ? "default" : "pointer");
	if (document.getElementById("_newconf[specify_jeditor_colors]").checked) {
		setEditorColorsDisabled(disabled);
	} else {
		setEditorColorsDisabled(true);
	}
}

function setEditorColorsDisabled(disabled) {
	setColorChooserDisabled("editorFontcolor", disabled);
	setColorChooserDisabled("editorWeTagFontcolor", disabled);
	setColorChooserDisabled("editorWeAttributeFontcolor", disabled);
	setColorChooserDisabled("editorHTMLTagFontcolor", disabled);
	setColorChooserDisabled("editorHTMLAttributeFontcolor", disabled);
	setColorChooserDisabled("editorPiTagFontcolor", disabled);
	setColorChooserDisabled("editorCommentFontcolor", disabled);
}

function setColorChooserDisabled(id, disabled) {
	var td = document.getElementById("color_newconf[" + id + "]");
	td.setAttribute("class", disabled ? "disabled" : "");
	td.firstChild.style.cursor = disabled ? "default" : "pointer";
	document.getElementById("label_" + id).style.color = disabled ? "grey" : "";
}

function displayEditorOptions(editor) {
	tmp = document.getElementsByClassName("editor");
	for (var k = 0; k < tmp.length; k++) {
		tmp[k].style.display = "none";
	}

	tmp = document.getElementsByClassName("editor_" + editor);
	for (var k = 0; k < tmp.length; k++) {
		tmp[k].style.display = "block";
	}
}

function initEditorMode() {
	displayEditorOptions(document.getElementsByName("newconf[editorMode]")[0].options[document.getElementsByName("newconf[editorMode]")[0].options.selectedIndex].value);
}

function resetLocales() {
	if (document.getElementById('locale_temp_locales').options.length > 0) {
		var temp = new Array(document.getElementById('locale_temp_locales').options.length);
		for (i = 0; i < document.getElementById('locale_temp_locales').options.length; i++) {
			temp[i] = document.getElementById('locale_temp_locales').options[i].value;
		}
		document.getElementById('locale_locales').value = temp.join(",");
	}

}

function initLocale(Locale) {
	if (Locale != "") {
		setDefaultLocale(Locale);
	}
	resetLocales();
}

function defaultLocale() {
	if (document.getElementById('locale_temp_locales').selectedIndex > -1) {
		var LocaleIndex = document.getElementById('locale_temp_locales').selectedIndex;
		var LocaleValue = document.getElementById('locale_temp_locales').options[LocaleIndex].value;

		setDefaultLocale(LocaleValue);
	}
}

function setDefaultLocale(Value) {
	if (document.getElementById('locale_temp_locales').options.length > 0) {
		Index = 0;
		for (i = 0; i < document.getElementById('locale_temp_locales').options.length; i++) {
			if (document.getElementById('locale_temp_locales').options[i].value == Value) {
				Index = i;
			}
			document.getElementById('locale_temp_locales').options[i].style.background = '#ffffff';
		}
		document.getElementById('locale_temp_locales').options[Index].style.background = '#cccccc';
		document.getElementById('locale_temp_locales').options[Index].selected = false;
		document.getElementById('locale_default').value = Value;
	}
}

function set_state_edit_delete_recipient() {
	var p = document.forms[0].elements.we_recipient;
	var i = p.length;

	if (i == 0) {
		edit_enabled = switch_button_state('edit', 'edit_enabled', 'disabled');
		delete_enabled = switch_button_state('delete', 'delete_enabled', 'disabled');
	} else {
		edit_enabled = switch_button_state('edit', 'edit_enabled', 'enabled');
		delete_enabled = switch_button_state('delete', 'delete_enabled', 'enabled');
	}
}

function inSelectBox(val) {
	var p = document.forms[0].elements.we_recipient;

	for (var i = 0; i < p.options.length; i++) {
		if (p.options[i].text == val) {
			return true;
		}
	}
	return false;
}

function addElement(value, text, sel) {
	var p = document.forms[0].elements.we_recipient;
	var i = p.length;

	p.options[i] = new Option(text, value);

	if (sel) {
		p.selectedIndex = i;
	}
}

function in_array(n, h) {
	for (var i = 0; i < h.length; i++) {
		if (h[i] == n) {
			return true;
		}
	}
	return false;
}

function send_recipients() {
	if (hot) {
		var p = document.forms[0].elements.we_recipient;
		var v = document.forms[0].elements["newconf[formmail_values]"];

		v.value = "";

		for (var i = 0; i < p.options.length; i++) {
			v.value += p.options[i].value + "<#>" + p.options[i].text + ((i < (p.options.length - 1)) ? "<##>" : "");
		}
	}
}

function formmailLogOnOff() {
	var formmail_log = document.forms[0].elements["newconf[FORMMAIL_LOG]"];
	var formmail_block = document.forms[0].elements["newconf[FORMMAIL_BLOCK]"];
	var formmail_emptylog = document.forms[0].elements["newconf[FORMMAIL_EMPTYLOG]"];
	var formmail_span = document.forms[0].elements["newconf[FORMMAIL_SPAN]"];
	var formmail_trials = document.forms[0].elements["newconf[FORMMAIL_TRIALS]"];
	var formmail_blocktime = document.forms[0].elements["newconf[FORMMAIL_BLOCKTIME]"];

	var flag = formmail_log.options[formmail_log.selectedIndex].value == 1;

	formmail_emptylog.disabled = !flag;

	formmail_block.disabled = !flag;
	if (formmail_block.options[formmail_block.selectedIndex].value == 1) {
		formmail_span.disabled = !flag;
		formmail_trials.disabled = !flag;
		formmail_blocktime.disabled = !flag;
	}
}
function formmailBlockOnOff() {
	var formmail_block = document.forms[0].elements["newconf[FORMMAIL_BLOCK]"];
	var formmail_span = document.forms[0].elements["newconf[FORMMAIL_SPAN]"];
	var formmail_trials = document.forms[0].elements["newconf[FORMMAIL_TRIALS]"];
	var formmail_blocktime = document.forms[0].elements["newconf[FORMMAIL_BLOCKTIME]"];

	var flag = formmail_block.options[formmail_block.selectedIndex].value == 1;

	formmail_span.disabled = !flag;
	formmail_trials.disabled = !flag;
	formmail_blocktime.disabled = !flag;
}

function set_state() {
	if (document.getElementsByName('newconf[useproxy]')[0].checked == true) {
		_new_state = false;
	} else {
		_new_state = true;
	}

	document.getElementsByName('newconf[proxyhost]')[0].disabled = _new_state;
	document.getElementsByName('newconf[proxyport]')[0].disabled = _new_state;
	document.getElementsByName('newconf[proxyuser]')[0].disabled = _new_state;
	document.getElementsByName('newconf[proxypass]')[0].disabled = _new_state;
}