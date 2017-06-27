/* global top, WE */

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
'use strict';

var loaded = false;

window.dynVars = WE().util.getDynamicVar(document, 'loadVarExport_prop', 'data-dynVars');
/* $(function () {}); */

function doOnload() { // FIXME: if this script wasn't used in cmd frame too, we could call start() directly (without this fn and attrib onload on body)!
	loaded=1;
	if(typeof window.startTree === 'function'){
		window.startTree();
	}
	start();
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function start(){
	if(!window.dynVars.modelProperties.isFolder){
		if(typeof window.dynVars.initialTreeData.selectedItems === 'object'){
			top.content.editor.edbody.treeData.SelectedItems = window.dynVars.initialTreeData.selectedItems;
		}
		if(typeof window.dynVars.initialTreeData.openFolders === 'object'){
			top.content.editor.edbody.treeData.openFolders = window.dynVars.initialTreeData.openFolders;
		}
		window.setHead(window.dynVars.modelProperties.currentTable);
	}
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "switchPage":
			document.we_form.cmd.value = args[0];
			document.we_form.tabnr.value = args[1];
			submitForm();
			break;
		case "we_export_dirSelector": // FIXME: does not work
			//url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
			new (WE().util.jsWindow)(caller, url, "we_exportselector", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true);
			break;
		case "we_selector_category":
			we_cmd('setHot');
			new (WE().util.jsWindow)(caller, url, "we_catselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "del_cat":
		case "del_all_cats":
			we_cmd('setHot');
			document.we_form.cmd.value = args[0];
			top.content.editor.edbody.document.we_form.pnt.value = "edbody";
			document.we_form.tabnr.value = top.content.activ_tab;
			document.we_form.cat.value = args[1];
			submitForm();
			break;
		case "we_selector_directory":
			we_cmd('setHot');
			new (WE().util.jsWindow)(caller, url, "we_selector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "add_cat":
			document.we_form.cmd.value = args[0];
			top.content.editor.edbody.document.we_form.pnt.value = "edbody";
			document.we_form.tabnr.value = top.content.activ_tab;
			document.we_form.cat.value = args[1].allIDs.join(",");
			submitForm();
			break;
		case 'inputChooser_syncChoice':
			document.we_form[args[2]].value=args[1].options[args[1].selectedIndex].value;
			args[1].selectedIndex = 0;
			we_cmd('setHot');
			break;
		case 'toggle_selection':
			closeAllSelection();
			toggle(args[1]);
			toggleSelectionType('doctype');
			we_cmd('setHot');
			break;
		case 'switch_type':
			var type = args[1].value || WE().consts.exim.TYPE_WE;
			var i = 0;
			var opts;

			// hide all typespecific options on tab 2
			var exportOptions = top.content.editor.edbody.document.getElementsByClassName('exportOptions');
			for(i = 0; i < exportOptions.length; i++){
				exportOptions[i].style.display = 'none';
			}

			var extensions = top.content.editor.edbody.document.getElementsByClassName('exportExtension');

			switch(type){
				case WE().consts.exim.TYPE_XML:
					// auto: all selectenTypes alloud => no changes needed but selector may be disabled
					top.content.editor.edbody.document.we_form.SelectionType.disabled = false;

					// manual
					top.content.editor.edbody.document.we_form.headerSwitch.disabled = false;

					// disable all tables but object and document
					opts = top.content.editor.edbody.document.we_form.headerSwitch.options;
					for(i = 0; i < opts.length; i++){
						switch(opts[i].value){
							case WE().consts.tables.OBJECT_FILES_TABLE:
							case WE().consts.tables.FILE_TABLE:
								continue;
							default:
								opts[i].disabled = true;
						}
					}

					// set to alloud table if needed
					if(top.content.editor.edbody.document.we_form.headerSwitch.value !== WE().consts.tables.OBJECT_FILES_TABLE ||
							top.content.editor.edbody.document.we_form.headerSwitch.value !== WE().consts.tables.FILE_TABLE){
						window.setHead(WE().consts.tables.OBJECT_FILES_TABLE);
						top.content.editor.edbody.document.we_form.headerSwitch.value = WE().consts.tables.FILE_TABLE;
					}

					// some typespecific stuff
					top.content.editor.edbody.document.getElementById('optionsGXML').style.display = 'block';
					for(i = 0; i < extensions.length; i++){
						extensions[i].innerHTML = '.xml';
					}
					top.content.editor.edbody.document.we_form.Extension.value = '.xml';
					break;
				case WE().consts.exim.TYPE_CSV:
					// auto
					if(!WE().consts.exim.export.ENABLE_DOCUMENTS2CSV){
						// auto: set selectionType to classname and disable selector
						top.content.editor.edbody.document.we_form.SelectionType.value = WE().consts.exim.export.SELECTIONTYPE_CLASSNAME;
						top.content.editor.edbody.document.we_form.SelectionType.disabled = true;
						toggleSelectionType(WE().consts.exim.export.SELECTIONTYPE_CLASSNAME);

						// manual: set tree to object and disable table selector
						window.setHead(WE().consts.tables.OBJECT_FILES_TABLE);
						top.content.editor.edbody.document.we_form.headerSwitch.value = WE().consts.tables.OBJECT_FILES_TABLE;
						top.content.editor.edbody.document.we_form.headerSwitch.disabled = true;
					} else {
						// auto: all selectenTypes alloud => no changes needed but selector may be disabled
						top.content.editor.edbody.document.we_form.SelectionType.disabled = false;

						// manual
						top.content.editor.edbody.document.we_form.headerSwitch.disabled = false;

						// disable all tables but object and document
						opts = top.content.editor.edbody.document.we_form.headerSwitch.options;
						for(i = 0; i < opts.length; i++){
							switch(opts[i].value){
								case WE().consts.tables.OBJECT_FILES_TABLE:
								case WE().consts.tables.FILE_TABLE:
									continue;
								default:
									opts[i].disabled = true;
							}
						}

						// set to alloud table if needed
						if(top.content.editor.edbody.document.we_form.headerSwitch.value !== WE().consts.tables.OBJECT_FILES_TABLE ||
								top.content.editor.edbody.document.we_form.headerSwitch.value !== WE().consts.tables.FILE_TABLE){
							window.setHead(WE().consts.tables.OBJECT_FILES_TABLE);
							top.content.editor.edbody.document.we_form.headerSwitch.value = WE().consts.tables.FILE_TABLE;
						}
					}

					// some typespecific stuff
					top.content.editor.edbody.document.getElementById('optionsCSV').style.display = 'block';
					for(i = 0; i < extensions.length; i++){
						extensions[i].innerHTML = '.csv';
					}
					top.content.editor.edbody.document.we_form.Extension.value = '.csv';
					break;
				case WE().consts.exim.TYPE_CSV:
				/*falls through*/
				default:
					// auto: all selectenTypes alloud => no changes needed but selector may be disabled
					top.content.editor.edbody.document.we_form.SelectionType.disabled = false;

					// auto
					top.content.editor.edbody.document.we_form.headerSwitch.disabled = false;

					// enable all tables
					opts = top.content.editor.edbody.document.we_form.headerSwitch.options;
					for(i = 0; i < opts.length; i++){
						opts[i].disabled = false;
					}

					// some typespecific stuff
					top.content.editor.edbody.document.getElementById('optionsWXML').style.display = 'block';
					for(i = 0; i < extensions.length; i++){
						extensions[i].innerHTML = '.xml';
					}
					top.content.editor.edbody.document.we_form.Extension.value = '.xml';
			}
			top.content.hot=true;
			break;
		case "setTreeHead":
			document.we_form.XMLTable.value = args[1].replace(WE().consts.tables.TBL_PREFIX, '');
			window.setHead(args[1]);
			break;
		case 'setExportDepth':
			var r = parseInt(args[1]);
			if(isNaN(r)){
				this.value = args[2];
			} else {
				this.value = r;
				we_cmd('setHot');
			}
			break;
		case 'setHeaderTitlePath':
			top.content.editor.edheader.weTabs.setTitlePath(args[1] + top.content.editor.edbody.document.we_form.Extension.value);
			break;
		case 'setHot':
			top.content.setHot();
			break;
		default:
			top.content.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function submitForm(target, action, method) {
	var f = window.document.we_form;
	f.target = (target ? target : "edbody");
	f.action = (action ? action : WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=export');
	f.method = (method ? method : "post");
	f.submit();
}

function toggle(id) {
	var elem = document.getElementById(id);
	if (elem.style.display === "none") {
		elem.style.display = "";
	} else {
		elem.style.display = "none";
	}
	we_cmd('setHot');
}

function toggleSelectionType(selType) {
	var displayElems, cl, i;
	var allElems = document.getElementsByClassName('selectionTypes');

	for(i = 0; i < allElems.length; i++){
		allElems[i].style.display = 'none';
	}

	switch(selType){
		case WE().consts.exim.export.SELECTIONTYPE_DOCTYPE:
		case WE().consts.exim.export.SELECTIONTYPE_CLASSNAME:
			cl = selType;
			break;
		case WE().consts.exim.export.SELECTIONTYPE_DOCUMENT:
		/*falls through*/
		default:
			cl = WE().consts.exim.export.SELECTIONTYPE_DOCUMENT;
	}

	displayElems = document.getElementsByClassName(cl);

	for(i = 0; i < displayElems.length; i++){
		displayElems[i].style.display = '';
	}

	we_cmd('setHot');
}

function clearLog() {
	top.content.editor.edbody.document.getElementById("log").innerHTML = "";
}

function addLog(text) {
	top.content.editor.edbody.document.getElementById("log").innerHTML += text + "<br/>";
	top.content.editor.edbody.document.getElementById("log").scrollTop = 50000;
}

function closeAllSelection() {
	var elem = document.getElementById("auto");
	elem.style.display = "none";
	elem = document.getElementById("manual");
	elem.style.display = "none";
}

function closeAllType() {
	var allElems = document.getElementsByClassName('selectionTypes');

	for(var i = 0; i < allElems.length; i++){
		allElems[i].style.display = 'none';
	}
}

function formFileChooser() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "browse_server":
			new (WE().util.jsWindow)(window, url, "server_selector", WE().consts.size.dialog.small, WE().consts.size.dialog.tiny, true, false, true);
			break;
	}
}