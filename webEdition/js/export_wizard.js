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
	var actualStep = top.body.document.we_form.elements.step.value ? parseInt(top.body.document.we_form.elements.step.value) - 1 : -1;

	switch (actualStep) {
		case 1:
			if (top.body.document.we_form.elements.typeTmp.value === 'csv' && top.body.we_form.selection[1].checked) {
				top.body.document.we_form.step.value = 3;
			}
			top.body.document.we_form.submit();
			break;
		case 3:
			top.body.document.we_form.selDocs.value = top.content.editor.edbody.SelectedItems[WE().consts.tables.FILE_TABLE].join(',');
			top.body.document.we_form.selTempl.value = top.content.editor.edbody.SelectedItems[WE().consts.tables.TEMPLATES_TABLE].join(',');
			if(WE().consts.tables.OBJECT_FILES_TABLE !== 'OBJECT_FILES_TABLE'){
				top.body.we_form.selObjs.value=top.content.editor.edbody.SelectedItems[WE().consts.tables.OBJECT_FILES_TABLE].join(',');
			}
			if(WE().consts.tables.OBJECT_TABLE !== 'OBJECT_TABLE'){
				top.body.we_form.selClasses.value=top.content.editor.edbody.SelectedItems[WE().consts.tables.OBJECT_TABLE].join(',');
			}
			top.body.we_form.submit();
			break;
	}
}

function setHead(tab){ // step 3
	var c = ['#DDDDDD', '#DDDDDD', '#DDDDDD', '#DDDDDD'];
	var fw = ['normal', 'normal', 'normal', 'normal'];
	var elFt, elTt, elOt, elOft;
	c[tab] = '#DFE9F5';
	fw[tab] = 'bold';


	switch (tab){
		case 0:
			top.table = WE().consts.tables.FILE_TABLE;
			break;
		case 1:
			top.table = WE().consts.tables.TEMPLATES_TABLE;
			break;
		case 2:
			top.table = WE().consts.tables.OBJECT_FILES_TABLE;
			break;
		case 3:
			top.table = WE().consts.tables.OBJECT_TABLE;
			break;
	}

	window.setTimeout(top.startTree,100);
	elFt = top.body.document.getElementById(WE().consts.tables.FILE_TABLE);
	elFt.style.backgroundColor = c[0];
	elFt.style.fontWeight = fw[0];
	elTt = top.body.document.getElementById(WE().consts.tables.TEMPLATES_TABLE);
	elTt.style.backgroundColor = c[1];
	elTt.style.fontWeight = fw[1];
	if(WE().consts.tables.OBJECT_FILES_TABLE !== 'OBJECT_FILES_TABLE'){
		elOft = top.body.document.getElementById(WE().consts.tables.OBJECT_FILES_TABLE);
		elOft.style.backgroundColor = c[2];
		elOft.style.fontWeight = fw[2];
	}
	if(WE().consts.tables.OBJECT_TABLE !== 'OBJECT_TABLE'){
		elOt = top.body.document.getElementById(WE().consts.tables.OBJECT_TABLE);
		elOt.style.backgroundColor = c[3];
		elOt.style.fontWeight = fw[3];
	}
}

function setState(a) { // step 4
	var new_state = (top.body.document.getElementsByName(a)[0].checked == true ? false : true);

	if(a === '_handle_templates'){
		if(new_state === true){
			top.body.document.getElementsByName('handle_document_linked')[0].value = 0;
			top.body.document.getElementsByName('handle_object_linked')[0].value = 0;

			top.body.document.getElementsByName('_handle_document_linked')[0].checked = false;
			top.body.document.getElementsByName('_handle_object_linked')[0].checked = false;
		}

		top.body.document.getElementsByName('_handle_document_linked')[0].disabled = new_state;
		setLabelState('label__handle_document_linked', new_state);

		top.body.document.getElementsByName('_handle_object_linked')[0].disabled = new_state;
		setLabelState('label__handle_object_linked', new_state);
	}
	if(a === '_handle_classesfff'){
		if(new_state === true){
			top.body.document.getElementsByName('handle_object_includes')[0].value = 0;
			top.body.document.getElementsByName('_handle_object_includes')[0].checked = false;
		}
		top.body.document.getElementsByName('_handle_object_includes')[0].disabled = new_state;
		setLabelState('label__handle_object_includes', new_state);

		top.body.document.getElementsByName('link_object_depth')[0].disabled = new_state;
		setLabelState('label_link_object_depth', new_state);
	}
}

function doNext(step, nextStep, frameset){ // set frameset as dynvar anload
	switch(parseInt(step)){
		case 0:
		case 4:
			top.body.document.we_form.submit();
			break;
		case 1:
			top.we_submit(); // is this correct?
			break;
		case 2: 
			top.body.document.we_form.step.value = nextStep;
			top.body.document.we_form.submit();
			break;
		case 3:
			top.body.document.we_form.step.value = 7;
			top.body.document.we_submit();
			break;
		case 7:
			top.body.document.we_form.target='load';
			top.body.document.we_form.pnt.value='load';
			top.body.document.we_form.submit();
			break;
		default:
			top.load.location = frameset + '?pnt=load&cmd=next&step=' + step;
	}
}

function doBack(step){
	switch(parseInt(step)){
		case 1:
			top.body.document.we_form.step.value = 0;
			top.body.document.we_form.submit();
			break;
		case 2:
			top.body.document.we_form.step.value = 1;
			top.body.document.we_form.submit();
			break;
		case 3: 
			top.body.document.we_form.step.value=2;
			top.body.we_submit();
			break;
		
	}
}

function doCancel(){
	top.close();
}

function setLabelState(l, disable){ // step 4
		top.body.getElementById(l).style.color = disable ? 'grey' : 'black';
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
		case 'startTree_delayed':
			window.setTimeout(top.startTree,100);
			break;
		case 'setTable':
			switch(args[1].art){
				case 'objects':
					top.table = WE().consts.tables.OBJECT_FILES_TABLE !== 'OBJECT_FILES_TABLE' ? WE().consts.tables.OBJECT_FILES_TABLE : '';
					break;
				case 'docs':
					top.table = WE().consts.tables.FILE_TABLE;
					break;
				default:
					top.table = '';
			}
			break;
		case 'setClassname':
			top.classname = args[1].classname;
			break;
		default:
			top.opener.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}