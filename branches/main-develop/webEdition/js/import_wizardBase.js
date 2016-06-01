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

var ajaxUrl = WE().consts.dirs.WEBEDITION_DIR + 'rpc.php';

var weGetCategoriesHandleSuccess = function (o) {
	if (o.responseText !== undefined) {
		var obj = JSON.parse(o.responseText);


		for (var property in obj) {
			if (obj.hasOwnProperty(property)) {
				top.wizbody.document.getElementById(property).innerHTML = obj[property].innerHTML;
			}
		}
	}

	WE().util.setIconOfDocClass(document,"chooserFileIcon");
};

var weGetCategoriesHandleFailure = function (o) {
	alert("failure");
};

var weGetCategoriesCallback = {
	success: weGetCategoriesHandleSuccess,
	failure: weGetCategoriesHandleFailure,
	scope: self.frame,
	timeout: 1500
};

function weGetCategories(obj, cats, part) {
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
			if (this.wizbody.document.we_form.elements['v[docCategories]'].value.indexOf(',' + args[1] + ',') == -1) {
				cats = args[1].split(/,/);
				for (i = 0; i < cats.length; i++) {
					if (cats[i] && (this.wizbody.document.we_form.elements['v[docCategories]'].value.indexOf(',' + cats[i] + ',') == -1)) {
						if (this.wizbody.document.we_form.elements['v[docCategories]'].value) {
							this.wizbody.document.we_form.elements['v[docCategories]'].value = this.wizbody.document.we_form.elements['v[docCategories]'].value + cats[i] + ',';
						} else {
							this.wizbody.document.we_form.elements['v[docCategories]'].value = ',' + cats[i] + ',';
						}
					}
				}
				setTimeout(weGetCategories, 100, 'doc', this.wizbody.document.we_form.elements['v[docCategories]'].value, 'rows');
			}
			break;
		case 'delete_docCat':
			if (t.wizbody.document.we_form.elements['v[docCategories]'].value.indexOf(',' + args[1] + ',') != -1) {
				if (this.wizbody.document.we_form.elements['v[docCategories]'].value) {
					re = new RegExp(',' + args[1] + ',');
					this.wizbody.document.we_form.elements['v[docCategories]'].value = this.wizbody.document.we_form.elements['v[docCategories]'].value.replace(re, ',');
					if (this.wizbody.document.we_form.elements['v[docCategories]'].value == ',') {
						this.wizbody.document.we_form.elements['v[docCategories]'].value = '';
					}
				}
				this.wizbody.we_submit_form(top.wizbody.document.we_form, 'wizbody', path);
			}
			break;
		case 'add_objCat':
			this.wizbody.document.we_form.elements['v[import_type]'][1].checked = true;
			if (this.wizbody.document.we_form.elements['v[objCategories]'].value.indexOf(',' + args[1] + ',') == -1) {
				cats = args[1].split(/,/);
				for (i = 0; i < cats.length; i++) {
					if (cats[i] && (this.wizbody.document.we_form.elements['v[objCategories]'].value.indexOf(',' + cats[i] + ',') == -1)) {
						if (this.wizbody.document.we_form.elements['v[objCategories]'].value) {
							this.wizbody.document.we_form.elements['v[objCategories]'].value = this.wizbody.document.we_form.elements['v[objCategories]'].value + cats[i] + ',';
						} else {
							this.wizbody.document.we_form.elements['v[objCategories]'].value = ',' + cats[i] + ',';
						}
					}
				}
				setTimeout(weGetCategories, 100, 'obj', this.wizbody.document.we_form.elements['v[objCategories]'].value, 'rows');
			}
			break;
		case 'delete_objCat':
			if (this.wizbody.document.we_form.elements['v[objCategories]'].value.indexOf(',' + args[1] + ',') != -1) {
				if (this.wizbody.document.we_form.elements['v[objCategories]'].value) {
					re = new RegExp(',' + args[1] + ',');
					this.wizbody.document.we_form.elements['v[objCategories]'].value = this.wizbody.document.we_form.elements['v[objCategories]'].value.replace(re, ',');
					if (this.wizbody.document.we_form.elements['v[objCategories]'].value == ',') {
						this.wizbody.document.we_form.elements['v[objCategories]'].value = '';
					}
				}
				this.wizbody.we_submit_form(this.wizbody.document.we_form, 'wizbody', path);
			}
			break;
		case 'reload_editpage':
			break;
		default:
			top.opener.top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}