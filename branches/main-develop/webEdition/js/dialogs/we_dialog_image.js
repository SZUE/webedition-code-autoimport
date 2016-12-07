/* global WE, top */

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
var image = WE().util.getDynamicVar(document, 'loadVarDialogImage','data-image');

function imageChanged(wasThumbnailChange) {
	if (wasThumbnailChange !== null && wasThumbnailChange) {
		document.we_form.wasThumbnailChange.value = '1';
	}
	if (top.opener.tinyMCECallRegisterDialog) {
		top.opener.tinyMCECallRegisterDialog(null, 'block');
	}
	//document.we_form.target = "we_weImageDialog_edit_area";
	document.we_form.target = 'we_we_dialog_image_cmd_frame';//TODO: send form to iFrame cmd for and for not reloading whole editor
	document.we_form.we_what.value = 'cmd';
	document.we_form['we_cmd[0]'].value = 'update_editor';
	document.we_form.imgChangedCmd.value = '1';
	document.we_form.submit();
}

function extSrc_doOnchange(input) {
	if(input.value === '' || input.value === WE().consts.linkPrefix.EMPTY_EXT){
		WE().layout.button.switch_button_state(document, 'btn_edit_ext', 'disabled');
	} else {
		WE().layout.button.switch_button_state(document, 'btn_edit_ext', 'enabled');
	}
	imageChanged();
}

function checkWidthHeight(field) {
	var ratioCheckBox = document.getElementById('check_we_dialog_args[ratio]'),
					v = parseInt(field.value);

	if (ratioCheckBox.checked) {
		if (field.value.indexOf('%') === -1) {

			var ratiow = (parseInt(field.form.elements['we_dialog_args[rendered_width]'].value) / parseInt(field.form.elements['we_dialog_args[rendered_height]'].value));
			var ratioh = (parseInt(field.form.elements['we_dialog_args[rendered_height]'].value) / parseInt(field.form.elements['we_dialog_args[rendered_width]'].value));

			//if ((field.form.elements['we_dialog_args[width]'].value && field.form.elements['we_dialog_args[height]'].value) || (!field.form.elements['we_dialog_args[width]'].value && !field.form.elements['we_dialog_args[height]'].value)) {
			if (field.name === 'we_dialog_args[height]') {
				field.form.elements['we_dialog_args[width]'].value = v ? Math.round(v * ratiow) : '';
			} else {
				field.form.elements['we_dialog_args[height]'].value = v ? Math.round(v * ratioh) : '';
			}
			field.value = v ? v : '';
			//}
		} else {
			ratioCheckBox.checked = false;
		}
	} else {
		field.value = v ? v : '';
	}
	return true;
}

function update_editor(data){
	document.we_form['we_cmd[0]'].value = '';

	var inputElem;

	for (var arg in data.args) {
		if(data.args.hasOwnProperty(arg) && arg !== 'cssclass'){
			if(inputElem = document.we_form.elements['we_dialog_args[' + arg + ']']){
				inputElem.value = data.args[arg];
			}
		}
	}

	// => buggy!
	if(inputElem = document.we_form.elements["we_dialog_args[thumbnail]"]){
		var disabled = (inputElem.value !== '');
		document.we_form.elements["we_dialog_args[height]"].disabled = disabled;
		document.we_form.elements["we_dialog_args[width]"].disabled = disabled;
	}

	try{
		if(data.displayThumbnailSel === 'none'){
			top.document.getElementById('selectThumbnail').setAttribute('disabled', 'disabled');
		} else {
			top.document.getElementById('selectThumbnail').removeAttribute('disabled');
		}
	} catch(err){}

	var rh = 0, rw = 0;
	if(parseInt(data.args.width) * parseInt(data.args.height)){
		rh = data.args.width / data.args.height;
		rw = data.args.height / data.args.width;
	}
	if(document.we_form.tinyMCEInitRatioH !== undefined){
		document.we_form.tinyMCEInitRatioH.value = rh;
	}
	if(document.we_form.tinyMCEInitRatioW !== undefined){
		document.we_form.tinyMCEInitRatioW.value = rw;
	}
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case 'we_selector_document':
		case 'we_selector_image':
		case 'we_selector_directory':
			new (WE().util.jsWindow)(window, url, "we_fileselector", WE().consts.size.docSelect.width, WE().consts.size.docSelect.height, true, true, true, true);
			break;
		case 'browse_server':
			new (WE().util.jsWindow)(window, url, "browse_server",  WE().consts.size.docSelect.width, WE().consts.size.docSelect.height, true, false, true);
			break;
		case "we_fileupload_editor":
			new (WE().util.jsWindow)(window, url, "we_fileupload_editor", 500, WE().consts.size.docSelect.height, true, true, true, true);
			break;
		case "dialog_setType":
			var isInt = this.document.we_form.elements['we_dialog_args[type]'].value === WE().consts.linkPrefix.TYPE_INT;
			this.document.getElementById('imageExt').style.display = isInt ? 'none' :  'block';
			this.document.getElementById('imageInt').style.display = isInt ? 'block' :  'none';
			imageChanged();
			break;
		case "dialog_emptyLongdesc":
			this.document.we_form.elements['we_dialog_args[longdescid]'].value='';
			this.document.we_form.elements['we_dialog_args[longdescsrc]'].value='';
			break;
		case "dialog_imageChanged":
			imageChanged();
			break;
		default :
			top.opener.we_cmd.apply(this, Array.prototype.slice.call(arguments));
			break;
	}
}
