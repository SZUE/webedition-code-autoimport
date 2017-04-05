/* global WE, top */

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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
'use strict';
WE().util.loadConsts(document, 'g_l.import');

function weGetCategories(obj, cats, part) {
	//FIXME: why do we send so much data, if the data is gathered from obj???
	WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR + 'rpc.php?cmd=GetCategory', 'obj=' + obj + '&cats=' + cats + '&part=' + part + '&targetId=docCatTable&catfield=v[' + obj + 'Categories]', function (weResponse) {
		for (var property in weResponse.DataArray.elements) {
			if (obj.hasOwnProperty(property)) {
				top.wizbody.document.getElementById(property).innerHTML = obj[property].innerHTML;
			}
		}


		WE().util.setIconOfDocClass(document, "chooserFileIcon");
	});
}

function wiz_next(frm, url) {
	window[frm].location.href = url;
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	var cats, found, i;

	switch (args[0]) {
		case 'we_selector_directory':
		case 'we_selector_image':
		case 'we_selector_document':
			new (WE().util.jsWindow)(caller, url, "we_fileselector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true);
			break;
		case 'we_selector_file':
			new (WE().util.jsWindow)(caller, url, "we_selector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_navigation_dirSelector":
			new (WE().util.jsWindow)(caller, url, "we_navigation_dirselector", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true);
			break;
		case 'browse_server':
			new (WE().util.jsWindow)(caller, url, "browse_server", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, false, true);
			break;
		case 'we_selector_category':
			new (WE().util.jsWindow)(caller, url, "we_catselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true);
			break;
		case 'add_docCat':
			if (WE().consts.modules.active.indexOf("object") > 0) {
				caller.wizbody.document.we_form.elements['v[import_type]'][0].checked = true;
			}
			found = false;
			cats = args[1].allIDs;
			for (i = 0; i < cats.length; i++) {
				if (cats[i] && (caller.wizbody.document.we_form.elements['v[docCategories]'].value.indexOf(',' + cats[i] + ',') === -1)) {
					found = true;
					if (caller.wizbody.document.we_form.elements['v[docCategories]'].value) {
						caller.wizbody.document.we_form.elements['v[docCategories]'].value = caller.wizbody.document.we_form.elements['v[docCategories]'].value + cats[i] + ',';
					} else {
						caller.wizbody.document.we_form.elements['v[docCategories]'].value = ',' + cats[i] + ',';
					}
				}
				if (found) {
					window.setTimeout(weGetCategories, 100, 'doc', caller.wizbody.document.we_form.elements['v[docCategories]'].value, 'rows');
				}
			}
			break;
		case 'delete_docCat':
			caller.we_cmd('delete_Cat', 'doc', args[1], false);
			break;
		case 'add_objCat':
			caller.wizbody.document.we_form.elements['v[import_type]'][1].checked = true;
			found = false;
			cats = args[1].allIDs;
			for (i = 0; i < cats.length; i++) {
				if (cats[i] && (caller.wizbody.document.we_form.elements['v[objCategories]'].value.indexOf(',' + cats[i] + ',') === -1)) {
					found = true;
					if (caller.wizbody.document.we_form.elements['v[objCategories]'].value) {
						caller.wizbody.document.we_form.elements['v[objCategories]'].value = caller.wizbody.document.we_form.elements['v[objCategories]'].value + cats[i] + ',';
					} else {
						caller.wizbody.document.we_form.elements['v[objCategories]'].value = ',' + cats[i] + ',';
					}
				}

				if (found) {
					window.setTimeout(weGetCategories, 100, 'obj', caller.wizbody.document.we_form.elements['v[objCategories]'].value, 'rows');
				}
			}
			break;
		case 'delete_objCat':
			caller.we_cmd('delete_Cat', 'obj', args[1], false);
			break;
		case 'delete_Cat':
			var obj = args[1],
							cat = args[2],
							reload = !args[3];

			if (caller.wizbody.document.we_form.elements['v[' + obj + 'Categories]'].value.indexOf(',' + cat + ',') !== -1) {
				if (caller.wizbody.document.we_form.elements['v[' + obj + 'Categories]'].value) {
					var re = new RegExp(',' + cat + ',');
					caller.wizbody.document.we_form.elements['v[' + obj + 'Categories]'].value = caller.wizbody.document.we_form.elements['v[' + obj + 'Categories]'].value.replace(re, ',');
					if (!reload) {
						caller.wizbody.document.getElementById(obj + 'Cat' + cat).parentNode.removeChild(caller.wizbody.document.getElementById(obj + 'Cat' + cat));
					}
					if (caller.wizbody.document.we_form.elements['v[' + obj + 'Categories]'].value === ',') {
						caller.wizbody.document.we_form.elements['v[' + obj + 'Categories]'].value = '';
						if (!reload) {
							caller.wizbody.document.getElementById(obj + 'docCatTable').innerHTML = '<tr><td style="font-size:8px">&nbsp;</td></tr>';
						}
					}
				}

				if (reload) {
					we_submit_form(top.wizbody.document.we_form, 'wizbody', WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import');
				}
			}
			break;
		case 'setFormField':
			// args[1] is not used so we can use this command as selector callback
			setFormField(args[2], args[3], args[4], args[5], args[6]);
			break;
		case 'reload_hot_editpage':
		case 'reload_editpage':
			break;
		case 'handle_event':
			top.handleEvent(args[1]);
			break;
		case 'confirm_start_recoverBackup':
			//FIXME: command yes/no missing!
			WE().util.showConfirm(window, "", WE().consts.g_l.import.backup_file_found + ' \n\n' + WE().consts.g_l.import.backup_file_found_question, ["recover_backup"]);
			break;
		case "recover_backup":
			top.close();
			top.opener.top.we_cmd("recover_backup");
			break;
		case 'fileupload_callbackWXMLImport':
			top.doNext_WXMLImportStep1();
			break;
		case 'fileupload_callbackGXMLImport':
			top.doNext_GXMLImportStep1();
			break;
		case 'fileupload_callbackCSVImport':
			top.doNext_CSVImportStep1();
			break;
		case 'fileupload_doOnFileSelect':
			top.setFormField('v[rdofloc]', true, 'radio', 1);
			break;
		case 'cycle':
			top.cycle();
			break;
		case 'we_import':
			top.we_import(args[1], args[2], args[3]);
			break;
		case 'set_button_state':
			top.set_button_state();
			break;
		case 'finish':
		{
			top.finish(args[1]);
			break;
		}
		case 'doOnImportFinished':
			top.doOnImportFinished(args[1].progressText);
			break;
		case 'addLog_buffered':
			if (!args[1]) {
				return;
			}

			for (i = 0; i < args[1].length; i++) {
				top.addLog(args[1][i]);
			}
			break;
		case 'setProgress_footer':
			top.wizbusy.setProgress(Math.floor(args[1]));
			break;
		case 'setProgressText_footer':
			top.wizbusy.setProgressText(args[1], args[2]);
			break;
		case 'call_delayed':
			if (args[1]['function'] && args[1].delay) {
				setTimeout(args[1].function, args[1].delay, args[1].param_1, args[1].param_2, args[1].param_3);
			}
			break;
		default:
			top.opener.top.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function deleteCategory(obj, cat) {
	top.we_cmd('delete_Cat', obj, cat, false);
}

function we_submit_form(f, target, url) {
	f.target = target;
	f.action = url;
	f.method = 'post';
	f.submit();
}

function toggle(name) {
	var con = document.getElementById(name);
	con.style.display = (con.style.display == "none" ? "" : "none");
}

function addLog(text) {
	document.getElementById("log").innerHTML += text + "<br/>";
	document.getElementById("log").scrollTop = 50000;
}

function set_button_state() {
	var f = top.wizbody.document.we_form;

// FIXME: something is wrong...
//	WE().layout.button.switch_button_state(top.wizbusy.document, 'back', f.elements['v[btnState_back]'].value);
//	WE().layout.button.switch_button_state(top.wizbusy.document, 'next', f.elements['v[btnState_next]'].value);
}

function weChangeDocType(f) {
	WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR + 'rpc.php?cmd=ChangeDocType', 'cns=importExport&docType=' + f.value, function (weResponse) {
		var node, prop,
						elems = weResponse.DataArray.elements;

		for (var i = 0; i < elems.length; i++) {
			if ((node = elems[i].type === 'formelement' ? window.document.we_form.elements[elems[i].name] : document.getElementById(elems[i].name))) {
				for (var j = 0; j < elems[i].props.length; j++) {
					prop = elems[i].props[j];
					switch (prop.type) {
						case 'attrib':
							node.setAttribute(prop.name, prop.val);
							break;
						case 'style':
							node.style[prop.name] = prop.val;
							break;
						case 'innerHTML':
							node.innerHTML = prop.val;
							break;
					}
				}
			}
		}
		switchExt();
	});
}

function switchExt() {
	var a = top.wizbody.document.we_form.elements;
	a['v[we_Extension]'].value = (a['v[is_dynamic]'].value == 1 ? WE().consts.global.DEFAULT_DYNAMIC_EXT : WE().consts.global.DEFAULT_STATIC_EXT);
}

function setFormField(name, value, type, index, frame) {
	frame = frame ? frame : 'wizbody';
	var f = frame === 'top' ? top.document.we_form : top.frames[frame].document.we_form;
	var field = f.elements[name];

	if (index !== undefined) {
		field = field[index];
	}

	if (type === 'checkbox' || type === 'radio') {
		field.checked = value;
	} else {
		field.value = value;
	}
}

function handleEvent(evt) {
	var step = parseInt(top.wizbody.document.we_form.elements.step.value),
					type = top.wizbody.document.we_form.elements.type.value,
					task = 'get' + type + '_step_' + step;

	switch (task) {
		case 'getFileImport_step_1':
		case 'getWXMLImport_step_1':
		case 'getGXMLImport_step_1':
		case 'getCSVImport_step_1':
			handleEvent_step_1(evt, type);
			break;
		case 'getFileImport_step_2':
			handleEvent_FileImport_step_2(evt);
			break;
		case 'getFileImport_step_3':
			break;
		case 'getWXMLImport_step_2':
			handleEvent_WXMLImport_step_2(evt);
			break;
		case 'getWXMLImport_step_3':
			handleEvent_WXMLImport_step_3(evt);
			break;
		case 'getGXMLImport_step_2':
			handleEvent_GXMLImport_step_2(evt);
			break;
		case 'getGXMLImport_step_3':
			handleEvent_GXMLImport_step_3(evt);
			break;
		case 'getCSVImport_step_2':
			handleEvent_CSVImportStep_2(evt);
			break;
		case 'getCSVImport_step_3':
			handleEvent_CSVImportStep_3(evt);
			break;
		default:
			if (step === 0) {
				handleEvent_step_0(evt);
			}
			//
	}
}


function handleEvent_step_0(evt) {
	var f = top.wizbody.document.we_form;
	switch (evt) {
		case 'previous':
			break;
		case 'next':
			for (var i = 0; i < f.type.length; i++) {
				if (f.type[i].checked) {
					switch (f.type[i].value) {
						case 'file_import':
							top.location.href = WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import_files';
							break;
						case 'site_import':
							top.location.href = WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=siteImport';
							break;
						default:
							f.type.value = f.type[i].value;
							f.step.value = 1;
							f.mode.value = 0;
							f.target = 'wizbody';
							f.action = WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import';
							f.method = 'post';
							f.submit();
							break;
					}
				}
			}
			break;
		case 'cancel':
			top.close();
			break;
	}
}

function handleEvent_step_1(evt, type) {
	var f = top.wizbody.document.we_form;

	if (type === 'GXMLImport') {
		f.elements['v[we_TemplateID]'].value = (f.elements['v[docType]'].value == -1 ? f.elements.noDocTypeTemplateId.value :
						f.elements.docTypeTemplateId.value);
	}

	switch (evt) {
		case 'previous':
			f.step.value = 0;
			top.location.href = WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import&we_cmd[1]=' + type;
			break;
		case 'next':
			if (f.elements['v[rdofloc]'] && f.elements['v[rdofloc]'][1].checked === true) {
				top.wizbody.weFileUpload_instance.startUpload(); //FIXME: move this to the respective functions!
			} else {
				switch (type) {
					case 'WXMLImport':
						doNext_WXMLImportStep1();
						break;
					case 'GXMLImport':
						doNext_GXMLImportStep1();
						break;
					case 'CSVImport':
						doNext_CSVImportStep1();
						break;
					case 'FileImport':
						doNext_FileImportStep1();
						break;
				}
			}
			break;
		case 'cancel':
			top.close();
			break;
	}

	top.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'back', 'enabled');
	top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'next', 'enabled');
}

function handleEvent_FileImport_step_2(evt) {
	var frame = top.wizbody;

	switch (evt) {
		case "previous":
			top.location.href = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=import_files";
			break;
		case "next":
			if (frame.weFileUpload_instance !== undefined) {
				frame.weFileUpload_instance.startUpload();
			} else {
				WE().t_e('fileupload instance missing');
			}
			break;
		case "cancel":
			if (frame.weFileUpload_instance !== undefined) {
				frame.weFileUpload_instance.cancelUpload();
			} else {
				WE().t_e('fileupload instance missing');
			}
			break;
	}
}

function handleEvent_WXMLImport_step_2(evt) {
	var we_form = top.wizbody.document.we_form;
	switch (evt) {
		case "previous":
			we_form.step.value = 1;
			we_submit_form(we_form, "wizbody", WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=import");
			break;
		case "next":
			we_form.elements.step.value = 3;
			we_form.mode.value = 1;
			we_form.elements["v[mode]"].value = 1;
			we_submit_form(we_form, "wizbusy", WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=import&pnt=wizcmd");
			break;
		case "cancel":
			top.close();
			break;
	}
}

function handleEvent_WXMLImport_step_3(evt) {
	switch (evt) {
		case "cancel":
			top.close();
			break;
	}
}

function handleEvent_GXMLImport_step_2(evt) {
	var f = top.wizbody.document.we_form;
	switch (evt) {
		case 'previous':
			f.step.value = 1;
			we_submit_form(f, 'wizbody', WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import');
			break;
		case 'next':
			f.elements['v[from_elem]'].value = f.elements['v[from_iElem]'].value;
			f.elements['v[to_elem]'].value = f.elements['v[to_iElem]'].value;
			var iStart = isNaN(parseInt(f.elements['v[from_iElem]'].value)) ? 0 : f.elements['v[from_iElem]'].value;
			var iEnd = isNaN(parseInt(f.elements['v[to_iElem]'].value)) ? 0 : f.elements['v[to_iElem]'].value;
			var iElements = parseInt(f.elements.we_select.options[f.elements.we_select.selectedIndex].value);

			if ((iStart < 1) || (iStart > iElements) || (iEnd < 1) || (iEnd > iElements)) {
				WE().util.showMessage((WE().consts.g_l.import.num_elements + iElements), WE().consts.message.WE_MESSAGE_ERROR, window);
			} else {
				f.elements['v[rcd]'].value = f.we_select.options[f.we_select.selectedIndex].text;
				f.step.value = 3;
				we_submit_form(f, 'wizbody', WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import');
			}
			break;
		case 'cancel':
			top.close();
			break;
	}
}

function handleEvent_GXMLImport_step_3(evt) {
	var f = top.wizbody.document.we_form;
	switch (evt) {
		case 'previous':
			f.step.value = 2;
			we_submit_form(f, 'wizbody', WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import');
			break;
		case 'next':
			f.step.value = 3;
			f.mode.value = 1;
			f.elements['v[mode]'].value = 1;
			top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'next', 'disabled');
			we_submit_form(f, 'wizbody', WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import&mode=1');
			break;
		case 'cancel':
			top.close();
			break;
	}
}

function doNext_FileImportStep1() {
	var frame = top.wizbody;

	if (frame.selectCategories !== undefined) {
		frame.selectCategories();
	}
	frame.document.we_form.step.value = 2;
	frame.document.we_form.submit();
}

function doNext_WXMLImportStep1() {
	var f = top.wizbody.document.we_form,
					fs = f.elements['v[fserver]'].value,
					fl = 'placeholder.xml';

	if ((f.elements['v[rdofloc]'][0].checked) && fs !== '/') {
		if (fs.match(/\.\./) == '..') {
			WE().util.showMessage(WE().consts.g_l.import.invalid_path, WE().consts.message.WE_MESSAGE_ERROR, window);
			return;
		}
		//ext = fs.substr(fs.length - 4, 4);
		f.elements['v[import_from]'].value = fs;

	} else if (f.elements['v[rdofloc]'][1].checked && fl !== '') {
		//ext = fl.substr(fl.length - 4, 4);
		f.elements['v[import_from]'].value = fl;
	} else if (fs === '/' || fl === '') {
		WE().util.showMessage(WE().consts.g_l.import.select_source_file, WE().consts.message.WE_MESSAGE_ERROR, window);
		return;
	}
	f.step.value = 2;
	// timing Problem with Safari
	window.setTimeout(top.we_submit_form, 50, top.wizbody.document.forms.we_form, 'wizbody', WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import');
}

function doNext_GXMLImportStep1() {
	var f = top.wizbody.document.we_form;
	f.elements['v[we_TemplateID]'].value = f.elements['v[docType]'].value == -1 ? f.elements.noDocTypeTemplateId.value : f.elements.docTypeTemplateId.value;

	var fs = f.elements['v[fserver]'].value;
	var fl = top.wizbody.weFileUpload_instance !== undefined ? 'placeholder.xml' : f.elements.uploaded_xml_file.value;

	if ((f.elements['v[rdofloc]'][0].checked) && fs !== '/') {
		if (fs.match(/\.\./) === '..') {
			WE().util.showMessage(WE().consts.g_l.import.invalid_path, WE().consts.message.WE_MESSAGE_ERROR, window);
			return;
		}
		//ext = fs.substr(fs.length - 4, 4);
		f.elements['v[import_from]'].value = fs;
	} else if (f.elements['v[rdofloc]'][1].checked && fl !== '') {
		//ext = fl.substr(fl.length - 4, 4);
		f.elements['v[import_from]'].value = fl;
	} else if (fs === '/' || fl === '') {
		WE().util.showMessage(WE().consts.g_l.import.select_source_file, WE().consts.message.WE_MESSAGE_ERROR, window);
		return;
	}
	if (!f.elements['v[we_TemplateID]'].value) {
		f.elements['v[we_TemplateID]'].value = f.elements.noDocTypeTemplateId.value;
	}

	if (WE().consts.modules.active.indexOf("object") > 0) {
		if ((f.elements['v[import_type]'][0].checked && f.elements['v[we_TemplateID]'].value !== "0") || (f.elements['v[import_type]'][1].checked)) {
			f.step.value = 2;
			top.we_submit_form(f, 'wizbody', WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import');
		} else if (f.elements['v[import_type]'][0].checked) {
			WE().util.showMessage(WE().consts.g_l.import.select_docType, WE().consts.message.WE_MESSAGE_ERROR, window);
		}
	} else {
		if (f.elements['v[we_TemplateID]'].value !== "0") {
			f.step.value = 2;
			top.wizbody.we_submit_form(f, 'wizbody', WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import');
		} else {
			WE().util.showMessage(WE().consts.g_l.import.select_docType, WE().consts.message.WE_MESSAGE_ERROR, window);
		}
	}
}

function doNext_CSVImportStep1() {
	var f = top.wizbody.document.we_form,
					fvalid = true,
					fs = f.elements['v[fserver]'].value,
					fl = 'placeholder.xml';

	if ((f.elements['v[rdofloc]'][0].checked) && fs != '/') {
		if (fs.match(/\.\./) === '..') {
			WE().util.showMessage(WE().consts.g_l.import.invalid_path, WE().consts.message.WE_MESSAGE_ERROR, window);
			return;
		}
		//ext = fs.substr(fs.length - 4, 4);
		f.elements['v[import_from]'].value = fs;
	} else if (f.elements['v[rdofloc]'][1].checked && fl !== '') {
		//ext = fl.substr(fl.length - 4, 4);
		f.elements['v[import_from]'].value = fl;
	} else if (fs === '/' || fl === '') {
		WE().util.showMessage(WE().consts.g_l.import.select_source_file, WE().consts.message.WE_MESSAGE_ERROR, window);
		return;
	}

	if (fvalid) {
		if (f.elements['v[csv_seperator]'].value === '') {
			fvalid = false;
			WE().util.showMessage(WE().consts.g_l.import.select_seperator, WE().consts.message.WE_MESSAGE_ERROR, window);
		} else {
			f.step.value = 2;
			top.we_submit_form(f, 'wizbody', WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import');
		}
	}
}

function handleEvent_CSVImportStep_2(evt) {
	var f = top.wizbody.document.we_form;
	if (f.elements['v[import_type]'].value == 'documents') {
		f.elements['v[we_TemplateID]'].value = f.elements['v[docType]'].value == -1 ? f.elements.noDocTypeTemplateId.value : f.elements.docTypeTemplateId.value;
	}

	switch (evt) {
		case 'previous':
			f.step.value = 1;
			we_submit_form(f, 'wizbody', WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import');
			break;
		case 'next':
			if (f.elements['v[import_type]'].value == 'documents') {
				if (!f.elements['v[we_TemplateID]'].value) {
					f.elements['v[we_TemplateID]'].value = f.elements.DocTypeTemplateId.value;
				}
			}
			if (WE().consts.modules.active.indexOf("object") > 0) {
				if (f.elements['v[import_from]'].value !== '/' && ((f.elements['v[import_type]'][0].checked && f.elements['v[we_TemplateID]'].value !== "0") || (f.elements['v[import_type]'][1].checked))) {
					f.step.value = 3;
					we_submit_form(f, 'wizbody', WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import');
				} else {
					if (f.elements['v[import_from]'].value == '/') {
						WE().util.showMessage(WE().consts.g_l.import.select_source_file, WE().consts.message.WE_MESSAGE_ERROR, window);
					} else if (f.elements['v[import_type]'][0].checked) {
						WE().util.showMessage(WE().consts.g_l.import.select_docType, WE().consts.message.WE_MESSAGE_ERROR, window);
					}
				}
			} else {
				if (f.elements['v[import_from]'].value !== '/' && f.elements['v[we_TemplateID]'].value !== "0") {
					f.step.value = 3;
					we_submit_form(f, 'wizbody', WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import');
				} else {
					if (f.elements['v[import_from]'].value == '/') {
						WE().util.showMessage(WE().consts.g_l.import.select_source_file, WE().consts.message.WE_MESSAGE_ERROR, window);
					} else {
						WE().util.showMessage(WE().consts.g_l.import.select_docType, WE().consts.message.WE_MESSAGE_ERROR, window);
					}
				}
			}
			break;
		case 'cancel':
			top.close();
			break;
	}
}

function handleEvent_CSVImportStep_3(evt) {
	var f = top.wizbody.document.we_form;
	switch (evt) {
		case 'previous':
			f.step.value = 1;
			we_submit_form(f, 'wizbody', WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import');
			break;
		case 'next':
			f.step.value = 3;
			f.mode.value = 1;
			f.elements['v[mode]'].value = 1;
			f.elements['v[startCSVImport]'].value = 1;
			top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'next', 'disabled');
			we_submit_form(f, 'wizbody', WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import&mode=1');
			break;
		case 'cancel':
			top.close();
			break;
	}
}

function onChangeSelectXMLNode(node) {
	node.form.elements['v[to_iElem]'].value = node.options[node.selectedIndex].value;
	node.form.elements['v[from_iElem]'].value = 1;
	node.form.elements['v[sct_node]'].value = node.options[node.selectedIndex].text;
	if (node.options[node.selectedIndex].value == 1) {
		node.form.elements['v[from_iElem]'].disabled = true;
		node.form.elements['v[to_iElem]'].disabled = true;
	} else {
		node.form.elements['v[from_iElem]'].disabled = false;
		node.form.elements['v[to_iElem]'].disabled = false;
	}
}

function onChangeSelectObject(node) {
	var elem = top.wizbody.document.we_form.elements['v[classID]'];
	top.wizbody.document.we_form.elements['v[obj_path]'].value = '/' + elem.options[elem.selectedIndex].text;
	top.wizbody.document.we_form.elements['v[obj_path_id]'].value = top.wizbody.document.we_form.elements['v[classID]'].value.split('_')[1];
}

function addField(form, fieldType, fieldName, fieldValue) {
	if (document.getElementById) {
		var input = document.createElement('INPUT');
		if (document.all) {
			input.type = fieldType;
			input.name = fieldName;
			input.value = fieldValue;
		} else if (document.getElementById) {
			input.setAttribute('type', fieldType);
			input.setAttribute('name', fieldName);
			input.setAttribute('value', fieldValue);
		}
		form.appendChild(input);
	}
}
function getField(form, fieldName) {
	if (!document.all) {
		return form[fieldName];
	} else {
		for (var e = 0; e < form.elements.length; e++) {
			if (form.elements[e].name === fieldName) {
				return form.elements[e];
			}
		}
	}
	return null;
}
function removeField(form, fieldName) {
	var field = getField(form, fieldName);
	if (field && !field.length) {
		field.parentNode.removeChild(field);
	}
}
function toggleField(form, fieldName, value) {
	var field = getField(form, fieldName);
	if (field) {
		removeField(form, fieldName);
	} else {
		addField(form, 'hidden', fieldName, value);
	}
}

function finish(rebuild) {
	var std = top.wizbusy.document.getElementById('standardDiv');
	if (std !== undefined) {
		std.style.display = 'none';
	}
	var cls = top.wizbusy.document.getElementById('closeDiv');
	if (cls !== undefined) {
		cls.style.display = 'block';
	}
	if (rebuild) {
		new (WE().util.jsWindow)(top.opener, WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=rebuild&step=2&btype=rebuild_all&responseText=' . g_l('import', '[finished_success]') . '", "rebuildwin", WE().consts.size.dialog.small, WE().consts.size.dialog.tiny, true, 0, true);
	}
}

function cycle() {
	var cf = window.document.we_form;
	var bf = top.wizbody.document.we_form;
	for (var i = 0; i < bf.elements.length; i++) {
		if ((bf.elements[i].name.indexOf('v') > -1) || (bf.elements[i].name.indexOf('records') > -1) ||
						(bf.elements[i].name.indexOf('we_flds') > -1) || (bf.elements[i].name.indexOf('attributes') > -1)) {
			addField(cf, 'hidden', bf.elements[i].name, bf.elements[i].value);
		}
	}
}

function we_import(mode, cid, reload) {
	if (reload) {
		top.wizbody.location = WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import&pnt=wizbody&step=3&type=WXMLImport&noload=1';
	}

	var we_form = top.wizcmd.document.we_form;
	we_form.elements['v[mode]'].value = mode;
	we_form.elements['v[cid]'].value = cid;
	we_form.target = 'wizcmd';
	we_form.action = WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import&pnt=wizcmd';
	we_form.method = 'post';
	we_form.submit();
}

function doOnImportFinished(progressText) {
	top.wizbusy.setProgressText('pb1', progressText);
	top.wizbusy.setProgress(100);
	top.opener.top.we_cmd('load', top.opener.top.treeData.table, 0);
	if (WE().layout.weEditorFrameController.getActiveDocumentReference().quickstart && WE().layout.weEditorFrameController.getActiveDocumentReference().quickstart !== undefined) {
		WE().layout.weEditorFrameController.getActiveDocumentReference().location.reload();
	}
	if (top.wizbusy && top.wizbusy.document.getElementById('progress')) {
		var progress = top.wizbusy.document.getElementById('progress');
		if (progress !== undefined) {
			progress.style.display = 'none';
		}
	}
}