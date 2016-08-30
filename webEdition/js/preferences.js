/* global WE */

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

mainXhtmlFields = ["setXhtml_remove_wrong", "setXhtml_show_wrong"];
showXhtmlFields = ["setXhtml_show_wrong_text", "setXhtml_show_wrong_js", "setXhtml_show_wrong_error_log"];

WE().util.loadConsts("g_l.prefs");

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
	var fields = ["message_reporting_notices", "message_reporting_warnings", "message_reporting_errors"];
	for (i = 0; i < fields.length; i++) {

		if (document.getElementById(fields[i]).checked) {
			val += parseInt(document.getElementById(fields[i]).value);
		}
	}
	document.getElementById("message_reporting").value = val;
}

function set_state_error_handler() {
	if (document.getElementsByName('newconf[WE_ERROR_HANDLER]')[0].checked === true) {
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
	if (document.getElementsByName('useauthEnabler')[0].checked === true) {
		document.getElementsByName('newconf[useauth]')[0].value = 1;
		_new_state = false;
	} else {
		document.getElementsByName('newconf[useauth]')[0].value = 0;
		_new_state = true;
	}

	document.getElementsByName('newconf[HTTP_USERNAME]')[0].disabled = _new_state;
	document.getElementsByName('newconf[HTTP_PASSWORD]')[0].disabled = _new_state;
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
	for (k = 0; k < tmp.length; k++) {
		tmp[k].style.display = "block";
	}
}

function initEditorMode() {
	displayEditorOptions(document.getElementsByName("newconf[editorMode]")[0].options[document.getElementsByName("newconf[editorMode]")[0].options.selectedIndex].value);
}

function resetLocales() {
	if (document.getElementById('locale_temp_locales').options.length > 0) {
		var temp = [document.getElementById('locale_temp_locales').options.length];
		for (i = 0; i < document.getElementById('locale_temp_locales').options.length; i++) {
			temp[i] = document.getElementById('locale_temp_locales').options[i].value;
		}
		document.getElementById('locale_locales').value = temp.join(",");
	}

}

function initLocale(Locale) {
	if (Locale !== "") {
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
	if (i === 0) {
		edit_enabled = WE().layout.button.switch_button_state(document, 'edit', 'disabled');
		delete_enabled = WE().layout.button.switch_button_state(document, 'delete', 'disabled');
	} else {
		edit_enabled = WE().layout.button.switch_button_state(document, 'edit', 'enabled');
		delete_enabled = WE().layout.button.switch_button_state(document, 'delete', 'enabled');
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
	if (document.getElementsByName('newconf[useproxy]')[0].checked === true) {
		_new_state = false;
	} else {
		_new_state = true;
	}

	document.getElementsByName('newconf[proxyhost]')[0].disabled = _new_state;
	document.getElementsByName('newconf[proxyport]')[0].disabled = _new_state;
	document.getElementsByName('newconf[proxyuser]')[0].disabled = _new_state;
	document.getElementsByName('newconf[proxypass]')[0].disabled = _new_state;
}

function addLocale() {
	var LanguageIndex = document.getElementById('locale_language').selectedIndex;
	var LanguageValue = document.getElementById('locale_language').options[LanguageIndex].value;
	var LanguageText = document.getElementById('locale_language').options[LanguageIndex].text;
	var CountryIndex = document.getElementById('locale_country').selectedIndex;
	var CountryValue = document.getElementById('locale_country').options[CountryIndex].value;
	var CountryText = document.getElementById('locale_country').options[CountryIndex].text;
	if (LanguageValue.substr(0, 1) === "~") {
		LanguageValue = LanguageValue.substr(1);
	}
	if (LanguageValue === "") {
		return;
	}

	if (CountryValue.substr(0, 1) === "~") {
		CountryValue = CountryValue.substr(1);
	}
	var LocaleValue;
	var LocaleText;
	if (CountryValue !== "") {
		LocaleValue = LanguageValue + '_' + CountryValue;
		LocaleText = LanguageText + ' (' + CountryText + ')';
	} else {
		LocaleValue = LanguageValue;
		LocaleText = LanguageText;
	}

	var found = false;
	for (i = 0; i < document.getElementById('locale_temp_locales').options.length; i++) {
		if (document.getElementById('locale_temp_locales').options[i].value === LocaleValue) {
			found = true;
		}
	}

	if (found === true) {
		top.we_showMessage(WE().consts.g_l.prefs.language_already_exists, WE().consts.message.WE_MESSAGE_ERROR, window);
	} else if (CountryValue === "") {
		top.we_showMessage(WE().consts.g_l.prefs.language_country_missing, WE().consts.message.WE_MESSAGE_ERROR, window);
	} else {
		document.getElementById('locale_temp_locales').options[document.getElementById('locale_temp_locales').options.length] = new Option(LocaleText, LocaleValue, false, false);
		if (document.getElementById('locale_temp_locales').options.length === 1) {
			setDefaultLocale(LocaleValue);
		}
		if (modules.SPELLCHECKER) {
// Wörterbuch hinzufügen
			if (confirm(WE().consts.g_l.prefs.add_dictionary_question)) {
				top.opener.top.we_cmd('spellchecker_edit_ifthere');
			}
		}

	}
	resetLocales();
}

function deleteLocale() {
	if (document.getElementById('locale_temp_locales').selectedIndex > -1) {
		var LocaleIndex = document.getElementById('locale_temp_locales').selectedIndex;
		var LocaleValue = document.getElementById('locale_temp_locales').options[LocaleIndex].value;
		if (LocaleValue == document.getElementById('locale_default').value) {
			top.we_showMessage(WE().consts.g_l.prefs.cannot_delete_default_language, WE().consts.message.WE_MESSAGE_ERROR, window);
		} else {
			document.getElementById('locale_temp_locales').options[LocaleIndex] = null;
		}
		resetLocales();
	}
}

function delete_recipient() {
	var p = document.forms[0].elements.we_recipient;
	if (p.selectedIndex >= 0) {
		if (confirm(WE().consts.g_l.prefs.delete_recipient)) {
			hot = true;
			var d = document.forms[0].elements["newconf[formmail_deleted]"];
			d.value += ((d.value) ? "," : "") + p.options[p.selectedIndex].value;
			p.options[p.selectedIndex] = null;
			set_state_edit_delete_recipient();
		}
	}
}

function add_recipient() {
	var newRecipient = prompt(WE().consts.g_l.prefs.input_name, "");
	var p = document.forms[0].elements.we_recipient;
	if (newRecipient !== null) {
		if (newRecipient.length > 0) {
			if (newRecipient.length > 255) {
				top.we_showMessage(g_l.max_name_recipient, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}

			if (!inSelectBox(newRecipient)) {
				addElement("#", newRecipient, true);
				hot = true;
				set_state_edit_delete_recipient();
				send_recipients();
			} else {
				top.we_showMessage(WE().consts.g_l.prefs.recipient_exists, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
		} else {
			top.we_showMessage(WE().consts.g_l.prefs.not_entered_recipient, WE().consts.message.WE_MESSAGE_ERROR, window);
		}
	}
}


function edit_recipient() {
	var p = document.forms[0].elements.we_recipient;
	var editRecipient;
	if (p.selectedIndex >= 0) {
		editRecipient = p.options[p.selectedIndex].text;
		editRecipient = prompt(WE().consts.g_l.prefs.recipient_new_name, editRecipient);
	}

	if (p.selectedIndex >= 0 && editRecipient !== null) {
		if (editRecipient !== "") {
			if (p.options[p.selectedIndex].text == editRecipient) {
				return;
			}

			if (editRecipient.length > 255) {
				top.we_showMessage(WE().consts.g_l.prefsmax_name_recipient, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}

			if (!inSelectBox(editRecipient)) {
				p.options[p.selectedIndex].text = editRecipient;
				hot = true;
				send_recipients();
			} else {
				top.we_showMessage(WE().consts.g_l.prefs.recipient_exists, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
		} else {
			top.we_showMessage(WE().consts.g_l.prefs.not_entered_recipient, WE().consts.message.WE_MESSAGE_ERROR, window);
		}
	}
}

function show_seem_chooser(val) {
	switch (val) {
		case 'document':
			if (document.getElementById('selectordummy') !== undefined) {
				document.getElementById('selectordummy').style.display = 'none';
			}
			if (document.getElementById('seem_start_object') !== undefined) {
				document.getElementById('seem_start_object').style.display = 'none';
			}
			if (document.getElementById('seem_start_weapp') !== undefined) {
				document.getElementById('seem_start_weapp').style.display = 'none';
			}
			if (document.getElementById('seem_start_document') !== undefined) {
				document.getElementById('seem_start_document').style.display = 'block';
			}

			break;

		case 'weapp':
			if (document.getElementById('selectordummy') !== undefined) {
				document.getElementById('selectordummy').style.display = 'none';
			}
			if (document.getElementById('seem_start_document') !== undefined) {
				document.getElementById('seem_start_document').style.display = 'none';
			}
			if (document.getElementById('seem_start_weapp') !== undefined) {
				document.getElementById('seem_start_weapp').style.display = 'block';
			}
			if (document.getElementById('seem_start_object') !== undefined) {
				document.getElementById('seem_start_object').style.display = 'none';
			}
			break;
		case 'object':
			if (WE().consts.tables.OBJECT_FILES_TABLE) {
				if (document.getElementById('selectordummy') !== undefined) {
					document.getElementById('selectordummy').style.display = 'none';
				}
				if (document.getElementById('seem_start_weapp') !== undefined) {
					document.getElementById('seem_start_weapp').style.display = 'none';
				}
				if (document.getElementById('seem_start_document') !== undefined) {
					document.getElementById('seem_start_document').style.display = 'none';
				}
				if (document.getElementById('seem_start_object') !== undefined) {
					document.getElementById('seem_start_object').style.display = 'block';
				}
				break;

			}
			/* falls through */
		default:
			if (document.getElementById('selectordummy') !== null) {
				document.getElementById('selectordummy').style.display = 'block';
			}
			if (document.getElementById('seem_start_document') !== null) {
				document.getElementById('seem_start_document').style.display = 'none';
			}
			if (document.getElementById('seem_start_weapp') !== null) {
				document.getElementById('seem_start_weapp').style.display = 'none';
			}
			if (document.getElementById('seem_start_object') !== null) {
				document.getElementById('seem_start_object').style.display = 'none';
			}

	}
}

function selectSidebarDoc() {
	myWindStr = "WE().util.jsWindow.prototype.find('preferences')";
	parent.opener.top.we_cmd('we_selector_document', document.getElementsByName('newconf[SIDEBAR_DEFAULT_DOCUMENT]').value, WE().consts.tables.FILE_TABLE, myWindStr + '.content.document.getElementsByName(\'newconf[SIDEBAR_DEFAULT_DOCUMENT]\')[0].value', myWindStr + '.content.document.getElementsByName(\'ui_sidebar_file_name\')[0].value', '', '', '', WE().consts.contentTypes.WEDOCUMENT, WE().util.hasPerm("CAN_SELECT_OTHER_USERS_FILES"));
}

function select_seem_start() {
	myWindStr = "WE().util.jsWindow.prototype.find('preferences')";
	if (document.getElementById('seem_start_type').value == 'object') {
		if (WE().consts.tables.OBJECT_FILES_TABLE) {
			parent.opener.top.we_cmd('we_selector_document', document.getElementsByName('seem_start_object')[0].value, WE().consts.tables.OBJECT_FILES_TABLE, myWindStr + '.content.document.getElementsByName(\'seem_start_object\')[0].value', myWindStr + '.content.document.getElementsByName(\'seem_start_object_name\')[0].value', '', '', '', 'objectFile', 1);
		}
	} else {
		parent.opener.top.we_cmd('we_selector_document', document.getElementsByName('seem_start_document')[0].value, WE().consts.tables.FILE_TABLE, myWindStr + '.content.document.getElementsByName(\'seem_start_document\')[0].value', myWindStr + '.content.document.getElementsByName(\'seem_start_document_name\')[0].value', '', '', '', WE().consts.contentTypes.WEDOCUMENT, WE().util.hasPerm("CAN_SELECT_OTHER_USERS_FILES"));
	}
}

function startPrefs() {
	initEditorMode();
}
