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

var ajaxUrl = WE().consts.dirs.WEBEDITION_DIR + "rpc/rpc.php";

var weGetCategoriesHandleSuccess = function (o) {
	if (o.responseText !== undefined) {
		var json = eval('(' + o.responseText + ')');

		for (var elemNr in json.elemsById) {
			for (var propNr in json.elemsById[elemNr].props) {
				var propval = json.elemsById[elemNr].props[propNr].val;
				propval = propval.replace(/\\\'/g, "'");
				propval = propval.replace(/'/g, "\\\'");
				var eId = json.elemsById[elemNr].elemId;
				eval("self.wizbody.document.getElementById(json.elemsById[" + elemNr + "].elemId)." + json.elemsById[elemNr].props[propNr].prop + "='" + propval + "'");
			}
		}
	}
}

var weGetCategoriesHandleFailure = function (o) {
	alert("failure");
}

var weGetCategoriesCallback = {
	success: weGetCategoriesHandleSuccess,
	failure: weGetCategoriesHandleFailure,
	scope: self.frame,
	timeout: 1500
};

function weGetCategories(obj, cats, part, target) {
	ajaxData = 'protocol=json&cmd=GetCategory&obj=' + obj + '&cats=' + cats + '&part=' + part + '&targetId=docCatTable&catfield=v[' + obj + 'Categories]';
	_executeAjaxRequest('POST', ajaxUrl, weGetCategoriesCallback, ajaxData);
}

function _executeAjaxRequest(method, aUrl, callback, ajaxData) {
	return YAHOO.util.Connect.asyncRequest(method, aUrl, callback, ajaxData);
}

function wiz_next(frm, url) {
	window[frm].location.href = url;
}


function we_cmd() {
	var url = WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?';
	for (var i = 0; i < arguments.length; i++) {
		url += 'we_cmd[' + i + ']=' + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += '&';
		}
	}
	switch (arguments[0]) {
		case 'we_selector_directory':
		case 'we_selector_image':
		case 'we_selector_document':
			new (WE().util.jsWindow)(window, url, 'we_fileselector', -1, -1, WE().consts.size.docSelect.width, WE().consts.size.docSelect.height, true, true, true);
			break;
		case 'browse_server':
			new (WE().util.jsWindow)(window, url, 'browse_server', -1, -1, 840, 400, true, false, true);
			break;
		case 'we_selector_category':
			new (WE().util.jsWindow)(window, url, 'we_catselector', -1, -1, WE().consts.size.catSelect.width, WE().consts.size.catSelect.width, true, true, true);
			break;
		case 'add_docCat':
			if (WE().consts.tables.OBJECT_TABLE !== 'OBJECT_TABLE') {
				self.wizbody.document.we_form.elements['v[import_type]'][0].checked = true;
			}
			if (self.wizbody.document.we_form.elements['v[docCategories]'].value.indexOf(',' + arguments[1] + ',') == -1) {
				var cats = arguments[1].split(/,/);
				for (var i = 0; i < cats.length; i++) {
					if (cats[i] && (self.wizbody.document.we_form.elements['v[docCategories]'].value.indexOf(',' + cats[i] + ',') == -1)) {
						if (self.wizbody.document.we_form.elements['v[docCategories]'].value) {
							self.wizbody.document.we_form.elements['v[docCategories]'].value = self.wizbody.document.we_form.elements['v[docCategories]'].value + cats[i] + ',';
						} else {
							self.wizbody.document.we_form.elements['v[docCategories]'].value = ',' + cats[i] + ',';
						}
						setTimeout(function () {
							weGetCategories('doc', self.wizbody.document.we_form.elements['v[docCategories]'].value, 'rows');
						}, 100);
					}
				}
			}
			break;
		case 'delete_docCat':
			if (self.wizbody.document.we_form.elements['v[docCategories]'].value.indexOf(',' + arguments[1] + ',') != -1) {
				if (self.wizbody.document.we_form.elements['v[docCategories]'].value) {
					re = new RegExp(',' + arguments[1] + ',');
					self.wizbody.document.we_form.elements['v[docCategories]'].value = self.wizbody.document.we_form.elements['v[docCategories]'].value.replace(re, ',');
					if (self.wizbody.document.we_form.elements['v[docCategories]'].value == ',') {
						self.wizbody.document.we_form.elements['v[docCategories]'].value = '';
					}
				}
				self.wizbody.we_submit_form(self.wizbody.document.we_form, 'wizbody', path);
			}
			break;
		case 'add_objCat':
			self.wizbody.document.we_form.elements['v[import_type]'][1].checked = true;
			if (self.wizbody.document.we_form.elements['v[objCategories]'].value.indexOf(',' + arguments[1] + ',') == -1) {
				var cats = arguments[1].split(/,/);
				for (var i = 0; i < cats.length; i++) {
					if (cats[i] && (self.wizbody.document.we_form.elements['v[objCategories]'].value.indexOf(',' + cats[i] + ',') == -1)) {
						if (self.wizbody.document.we_form.elements['v[objCategories]'].value) {
							self.wizbody.document.we_form.elements['v[objCategories]'].value = self.wizbody.document.we_form.elements['v[objCategories]'].value + cats[i] + ',';
						} else {
							self.wizbody.document.we_form.elements['v[objCategories]'].value = ',' + cats[i] + ',';
						}
						setTimeout(function () {
							weGetCategories('obj', self.wizbody.document.we_form.elements['v[objCategories]'].value, 'rows');
						}, 100);
					}
				}
			}
			break;
		case 'delete_objCat':
			if (self.wizbody.document.we_form.elements['v[objCategories]'].value.indexOf(',' + arguments[1] + ',') != -1) {
				if (self.wizbody.document.we_form.elements['v[objCategories]'].value) {
					re = new RegExp(',' + arguments[1] + ',');
					self.wizbody.document.we_form.elements['v[objCategories]'].value = self.wizbody.document.we_form.elements['v[objCategories]'].value.replace(re, ',');
					if (self.wizbody.document.we_form.elements['v[objCategories]'].value == ',') {
						self.wizbody.document.we_form.elements['v[objCategories]'].value = '';
					}
				}
				self.wizbody.we_submit_form(self.wizbody.document.we_form, 'wizbody', path);
			}
			break;
		case 'reload_editpage':
			break;
		default:
			var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			top.opener.top.we_cmd.apply(this, args);
	}
}