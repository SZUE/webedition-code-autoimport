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

var wizzard = WE().util.getDynamicVar(document, 'loadVarEIWizard', 'data-wizzard');
var table = WE().consts.tables.FILE_TABLE;
var type, selection, export_to;


function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function doNextAction() {
	top.body.document.we_form.step.value++;
	top.footer.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=import&step=" + top.body.document.we_form.step.value;
	if (parseInt(top.body.document.we_form.step.value) > 4) {
		top.body.document.we_form.target = "load";
		top.body.document.we_form.pnt.value = "eiload";
	}
	top.body.document.we_form.submit();
}

function selectWeSelect_doOnselect(sel){
	sel.form.elements.xml_to.value = sel.options[sel.selectedIndex].value;
	sel.form.elements.xml_from.value = 1;
	sel.form.elements.dataset.value = sel.options[sel.selectedIndex].text;
	if(sel.options[sel.selectedIndex].value == 1) {
		sel.form.elements.xml_from.disabled = true;
		sel.form.elements.xml_to.disabled = true;
	} else {
		sel.form.elements.xml_from.disabled = false;
		sel.form.elements.xml_to.disabled = false;
	}
}

function we_cmd() {
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	var arg = args[1]; // we use this when using named params

	switch (args[0]) {
		case "uploader_callback":
			we_cmd('set_radio_importFrom', '', 1);
			doNextAction();
			break;
		case "uploader_cancel":
			top.body.weFileUpload_instance.cancelUpload();
			break;
		case "set_radio_importFrom":
			top.body.document.we_form.import_from[args[2]].checked = true;
			break;
		case "set_topVar":
			top[arg.name] = arg.fromInput ? top.body.document.we_form.elements[arg.name].value : arg.value;
			break;
		case "set_formField":
			top.body.document.we_form.elements[arg.name].value = arg.value;
			break;
		case "selectCharset_onchange":
			top.body.document.we_form.elements.the_charset.value = arg.select.options[arg.select.selectedIndex].value;
			arg.select.selectedIndex=-1;
			break;
		case "chooser_onChange":
			top.body.document.we_form.elements[args[2]].value = args[1].options[args[1].selectedIndex].value;
			args[1].selectedIndex=0;
			break;
		case "we_selector_file":
			new (WE().util.jsWindow)(caller, url, "we_selector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "add_customer":
			top.body.document.we_form.wcmd.value = args[0];
			top.body.document.we_form.cus.value = args[1].allIDs.join(',');
			top.body.document.we_form.submit();
			break;
		case "del_customer":
		case "del_all_customers":
			top.body.document.we_form.wcmd.value = args[0];
			top.body.document.we_form.cus.value = args[1];
			top.body.document.we_form.submit();
			break;
		case "do_back":
			we_cmd('load_processCmd', 'export_back', args[1]);
			break;
		case "reload_frame":
			top.frames[arg.frame].location = WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=customer&pnt=' + arg.pnt + '&art=' + arg.art + '&cmd=' + arg.cmd + '&step=' + arg.step;
			break;
		case "process_cmd_load":
			top.load.location = WE().consts.dirs.WEBEDITION_DIR +'we_cmd.php?we_cmd[0]=loadTree&we_cmd[1]=' + args[1] + '&we_cmd[2]=' + args[2] + '&we_cmd[3]=' + args[3];
			break;
		case "load_processCmd":
			switch(arg.cmd){
				case 'export_next':
					top.body.document.we_form.step.value++;
					top.footer.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=" + arg.art + "&step=" + top.body.document.we_form.step.value;
					if (top.body.document.we_form.step.value > 3) {
						top.body.document.we_form.target = "load";
						top.body.document.we_form.pnt.value = "eiload";
						top.body.document.we_form.cmd.value = wizzard.art;
					}
					top.body.document.we_form.submit();
					break;
				case 'export_back':
					top.body.document.we_form.step.value--;
					top.footer.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=" + arg.art + "&step=" + top.body.document.we_form.step.value;
					top.body.document.we_form.submit();
					break;
				case 'import':
					top.step++;
					top.load.document.we_form.submit();
					break;
				case 'import_next':
					if (parseInt(top.body.document.we_form.step.value) === 2 &&
						top.body.weFileUpload_instance !== undefined &&
						top.body.document.we_form.import_from[1].checked) {
						top.body.weFileUpload_instance.startUpload();
						return;
					}
					doNextAction();
					break;
				case 'import_back':
					top.body.document.we_form.step.value--;
					top.footer.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=eifooter&art=" + arg.art + "&step=" + top.body.document.we_form.step.value;
					top.body.document.we_form.submit();
					break;
				case 'do_import':
					if((arg.fstart < arg.fcount)){
						top.load.document.we_form.cmd.value = 'do_import';
					} else {
						top.load.document.we_form.cmd.value = 'import_end';
					}
					if (top.footer.setProgress){
						top.footer.setProgress('', arg.percent);
					}
					top.load.document.we_form.submit();
					break;
				case 'import_end':
					if(top.opener && top.opener.content && top.opener.top.content.applySort){
						top.opener.top.content.applySort();
					}
					top.footer.location = WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=customer&pnt=eifooter&art=' + arg.art +  '&step=6';
					top.load.document.we_form.submit();
					break;
			}
			break;
		case "change_filter":
			switch (args[1]){
				case "add_filter":
				case "del_filter":
				case "del_all_filters":
					top.body.document.we_form.fcmd.value = args[1];
					top.body.document.we_form.submit();
					break;
			}
			break;
		default:
			top.opener.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}