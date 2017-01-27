/* global WE, CropTool, _EditorFrame */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
'use strict';

var table = WE().consts.tables.FILE_TABLE;
var step = 0;
var activetab = 0;
var selection = 'auto';
var extype = WE().consts.import.TYPE_WE_XML;
var type = 'doctype';
var categories = '';
var doctype = '';
var classname = '';
var dir = '';
var file_format = WE().consts.import.TYPE_GENERIC_XML;
var filename = '';
var export_to = 'server';
var path = '/';

function we_submit() {
	var actualStep = top.body.we_form.elements.step.value ? parseInt(top.body.we_form.elements.step.value) - 1 : -1;

	switch (actualStep) {
		case 1:
			if (top.body.we_form.elements.typeTmp.value === 'csv' && top.body.we_form.selection[1].checked) {
				top.body.we_form.step.value = 3;
			}
			top.body.we_form.submit();
			break;
		case 3:

			break;
	}
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case 'we_selector_category':
			new (WE().util.jsWindow)(caller, url, "we_catselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case 'add_cat':
		case 'del_cat':
		case 'del_all_cats':
			caller.document.we_form.wcmd.value = args[0];
			caller.document.we_form.cat.value = args[1];
			caller.document.we_form.step.value = 2;
			caller.document.we_form.submit();
			break;
		case 'we_selector_directory':
			new (WE().util.jsWindow)(caller, url, "we_selector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case 'load_frame':
			top.console.log('load', args);
			top.frames[args[1].frame].location = args[1].location;
			break;
		case 'set_focus':
			top.frames[args[1].frame].focus();
			break;
		case 'exit_to_moduleExport':
			top.console.log('exit');
			top.opener.top.we_cmd("export_edit_ifthere"); // do we have to call on top.opener.top because of scope?
			top.close();
			break;
		default:
			top.opener.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}