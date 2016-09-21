/* global WE, YAHOO, top */

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

var weGetCategoriesCallback = {
	success: function (o) {
		if (o.responseText !== undefined) {
			var obj = JSON.parse(o.responseText);


			for (var property in obj) {
				if (obj.hasOwnProperty(property)) {
					top.wizbody.document.getElementById(property).innerHTML = obj[property].innerHTML;
				}
			}
		}

		WE().util.setIconOfDocClass(document, "chooserFileIcon");
	},
	failure: function (o) {
		alert("failure");
	},
	scope: window.frame,
	timeout: 1500
};

function weGetCategories(obj, cats, part) {
	ajaxData = 'protocol=json&cmd=GetCategory&obj=' + obj + '&cats=' + cats + '&part=' + part + '&targetId=docCatTable&catfield=v[' + obj + 'Categories]';
	_executeAjaxRequest('POST', weGetCategoriesCallback, ajaxData);
}

function _executeAjaxRequest(method, callback, ajaxData) {
	return YAHOO.util.Connect.asyncRequest(method, WE().consts.dirs.WEBEDITION_DIR + 'rpc.php', callback, ajaxData);
}

function wiz_next(frm, url) {
	window[frm].location.href = url;
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	var cats;
	var i;

	switch (args[0]) {
		case 'we_selector_directory':
		case 'we_selector_image':
		case 'we_selector_document':
			new (WE().util.jsWindow)(this, url, 'we_fileselector', -1, -1, WE().consts.size.docSelect.width, WE().consts.size.docSelect.height, true, true, true);
			break;
		case 'we_selector_file':
			new (WE().util.jsWindow)(this, url, 'we_selector', -1, -1, WE().consts.size.windowSelect.width, WE().consts.size.windowSelect.height, true, true, true, true);
			break;
		case "we_navigation_dirSelector":
			new (WE().util.jsWindow)(this, url, "we_navigation_dirselector", -1, -1, 600, 400, true, true, true);
			break;
		case 'browse_server':
			new (WE().util.jsWindow)(this, url, 'browse_server', -1, -1, 840, 400, true, false, true);
			break;
		case 'we_selector_category':
			new (WE().util.jsWindow)(this, url, 'we_catselector', -1, -1, WE().consts.size.catSelect.width, WE().consts.size.catSelect.width, true, true, true);
			break;
		case 'add_docCat':
			if (WE().consts.tables.OBJECT_TABLE !== 'OBJECT_TABLE') {
				this.wizbody.document.we_form.elements['v[import_type]'][0].checked = true;
			}
			var found = false;
			cats = args[1].allIDs;
			for (i = 0; i < cats.length; i++) {
				if (cats[i] && (this.wizbody.document.we_form.elements['v[docCategories]'].value.indexOf(',' + cats[i] + ',') === -1)) {
					found = true;
					if (this.wizbody.document.we_form.elements['v[docCategories]'].value) {
						this.wizbody.document.we_form.elements['v[docCategories]'].value = this.wizbody.document.we_form.elements['v[docCategories]'].value + cats[i] + ',';
					} else {
						this.wizbody.document.we_form.elements['v[docCategories]'].value = ',' + cats[i] + ',';
					}
				}
				if (found) {
					setTimeout(weGetCategories, 100, 'doc', this.wizbody.document.we_form.elements['v[docCategories]'].value, 'rows');
				}
			}
			break;
		case 'delete_docCat':
			if (this.wizbody.document.we_form.elements['v[docCategories]'].value.indexOf(',' + args[1] + ',') !== -1) {
				if (this.wizbody.document.we_form.elements['v[docCategories]'].value) {
					re = new RegExp(',' + args[1] + ',');
					this.wizbody.document.we_form.elements['v[docCategories]'].value = this.wizbody.document.we_form.elements['v[docCategories]'].value.replace(re, ',');
					if (this.wizbody.document.we_form.elements['v[docCategories]'].value === ',') {
						this.wizbody.document.we_form.elements['v[docCategories]'].value = '';
					}
				}
				this.wizbody.we_submit_form(top.wizbody.document.we_form, 'wizbody', WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=import");
			}
			break;
		case 'add_objCat':
			this.wizbody.document.we_form.elements['v[import_type]'][1].checked = true;
			var found = false;
			cats = args[1].allIDs;
			for (i = 0; i < cats.length; i++) {
				if (cats[i] && (this.wizbody.document.we_form.elements['v[objCategories]'].value.indexOf(',' + cats[i] + ',') === -1)) {
					found = true;
					if (this.wizbody.document.we_form.elements['v[objCategories]'].value) {
						this.wizbody.document.we_form.elements['v[objCategories]'].value = this.wizbody.document.we_form.elements['v[objCategories]'].value + cats[i] + ',';
					} else {
						this.wizbody.document.we_form.elements['v[objCategories]'].value = ',' + cats[i] + ',';
					}
				}
				if (found) {
					setTimeout(weGetCategories, 100, 'obj', this.wizbody.document.we_form.elements['v[objCategories]'].value, 'rows');
				}
			}
			break;
		case 'delete_objCat':
			if (this.wizbody.document.we_form.elements['v[objCategories]'].value.indexOf(',' + args[1] + ',') !== -1) {
				if (this.wizbody.document.we_form.elements['v[objCategories]'].value) {
					re = new RegExp(',' + args[1] + ',');
					this.wizbody.document.we_form.elements['v[objCategories]'].value = this.wizbody.document.we_form.elements['v[objCategories]'].value.replace(re, ',');
					if (this.wizbody.document.we_form.elements['v[objCategories]'].value === ',') {
						this.wizbody.document.we_form.elements['v[objCategories]'].value = '';
					}
				}
				this.wizbody.we_submit_form(this.wizbody.document.we_form, 'wizbody', WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=import");
			}
			break;
		case 'reload_hot_editpage':
		case 'reload_editpage':
			break;
		default:
			top.opener.top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}

function deleteCategory(obj, cat) {
	if (document.we_form.elements['v[' + obj + 'Categories]'].value.indexOf(',' + cat + ',') != -1) {
		re = new RegExp(',' + cat + ',');
		document.we_form.elements['v[' + obj + 'Categories]'].value = document.we_form.elements['v[' + obj + 'Categories]'].value.replace(re, ',');
		document.getElementById(obj + "Cat" + cat).parentNode.removeChild(document.getElementById(obj + "Cat" + cat));
		if (document.we_form.elements['v[' + obj + 'Categories]'].value == ',') {
			document.we_form.elements['v[' + obj + 'Categories]'].value = '';
			document.getElementById(obj + "CatTable").innerHTML = "<tr><td style='font-size:8px'>&nbsp;</td></tr>";
		}
	}
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

function weChangeDocType(f) {
	ajaxData = 'protocol=json&cmd=ChangeDocType&cns=importExport&docType=' + f.value;
	_executeAjaxRequest('POST', {
		success: function (o) {
			if (o.responseText !== undefined) {
				var elems = JSON.parse(o.responseText).elems;
				var node, prop;
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
			}
		},
		failure: function (o) {

		},
		timeout: 1500
	}, ajaxData);
}

function handle_eventStep0(evt) {
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

function handle_eventWXMLImportStep2(evt) {
	var we_form = window.document.we_form;
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

function handle_eventGXMLImportStep3(evt) {
	var f = window.document.we_form;
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

function handle_eventCSVImportStep3(evt) {
	var f = window.document.we_form;
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
	var elem = document.we_form.elements['v[classID]'];
	document.we_form.elements['v[obj_path]'].value = '/' + elem.options[elem.selectedIndex].text;
	document.we_form.elements['v[obj_path_id]'].value = document.we_form.elements['v[classID]'].value.split('_')[1];
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
