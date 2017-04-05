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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
'use strict';
/*
var preview = WE().util.getDynamicVar(document, 'loadVarFileupload_ui_preview', 'data-preview');
var categories_edit = new (WE().util.multi_edit)("categoriesDiv", window, 0, preview.delButton, preview.categoriesDivSize, false);
categories_edit.addVariant();
*/

function handleDragOver(e, name){
	if(e.preventDefault){
		e.preventDefault();
	}
	try {
		document.getElementById("div_" + name + "_fileDrag").className = "we_file_drag we_file_drag_hover";
	} catch(ex){}
}

function handleDragLeave(e, name){
	try {
		document.getElementById("div_" + name + "_fileDrag").className = "we_file_drag";
	} catch(ex){}
}

function handleDrop(e, name, dragFromTree, dragFromExt, cmdTree, cmdExt, cts, tableTree){
	var text;

	try {
		document.getElementById('div_' + name + '_fileDrag').className = 'we_file_drag';
	} catch(ex){}

	e.preventDefault();
	e.stopPropagation();

	text = e.dataTransfer.getData('text');
	if(text){
		if(dragFromTree){
			switch(text.split(',')[0]){
				case "dragItem":
					doDragFromTree(text, cmdTree, cts, tableTree);
					break;
				default:
					// more cases to come
			}
		} else {
			WE().util.showMessage('no drag from tree here', WE().consts.message.WE_MESSAGE_ERROR); // FIXME: GL()
		}
	} else if(e.dataTransfer.files){
		if(dragFromExt){
			doDragFromExternal(e.dataTransfer.files, cmdExt, cts);
		} else {
			WE().util.showMessage('no drag from external here', WE().consts.message.WE_MESSAGE_ERROR); // FIXME: GL()
		}
	}
}

function doDragFromExternal(files, cmdExt, cts){
	if(!files || !cmdExt){
		return false;
	}

	document.presetFileupload = files;
	top.we_cmd('we_fileupload_editor', cts, 1, '', 0, 0, true, cmdExt, files);
}

function doDragFromTree(text, cmdTree, cts, table){
	if(!text || !cmdTree){
		return false;
	}

	var transfer = text.split(',');

	if(transfer[2] && transfer[1] === table && (cts === '' || cts.search(transfer[3])) !== -1){
		var data = {
				id: transfer[2],
				path: transfer[4],
				ct: transfer[3],
				table: transfer[1],
				currentID: transfer[2],
				currentPath: transfer[4],
				currentType: transfer[3],
				currentTable: transfer[1]
			};
		var tmp = cmdTree.split(',');

		tmp.splice(1, 0, data);
		top.we_cmd.apply(top, tmp);
	}
}