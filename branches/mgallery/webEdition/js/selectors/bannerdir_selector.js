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

function addEntry(ID, icon, text, isFolder, path) {
	entries.push({
		"ID": ID,
		"icon": icon,
		"text": text,
		"isFolder": isFolder,
		"path": path
	});
}

function writeBody(d) {
	var body = (top.we_editDirID ?
					'<input type="hidden" name="what" value="' + consts.DORENAMEFOLDER + '" />' +
					'<input type="hidden" name="we_editDirID" value="' + top.we_editDirID + '" />' :
					'<input type="hidden" name="what" value="' + consts.CREATEFOLDER + '" />'
					) +
					'<input type="hidden" name="order" value="' + top.order + '" />' +
					'<input type="hidden" name="rootDirID" value="' + top.options.rootDirID + '" />' +
					'<input type="hidden" name="table" value="' + top.options.table + '" />' +
					'<input type="hidden" name="id" value="' + top.currentDir + '" />' +
					'<table class="selector">' +
					(makeNewFolder ?
									'<tr style="background-color:#DFE9F5;">' +
									'<td align="center"><img class="treeIcon" src="' + top.dirs.TREE_ICON_DIR + consts.FOLDER_ICON + '"/></td>' +
									'<td><input type="hidden" name="we_FolderText" value="' + g_l.newbannergroup + '" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="' + g_l.newbannergroup + '"  class="wetextinput" style="width:100%" /></td>' +
									'</tr>'
									: '');
	for (i = 0; i < entries.length; i++) {
		var onclick = ' onclick="weonclick(event);tout=setTimeout(\'if(top.wasdblclick==0){top.doClick(' + entries[i].ID + ',0);}else{top.wasdblclick=0;}\',300);return true"';
		var ondblclick = ' onDblClick="top.wasdblclick=1;clearTimeout(tout);top.doClick(' + entries[i].ID + ',1);return true;"';
		body += '<tr id="line_' + entries[i].ID + '" style="' + ((entries[i].ID == top.currentID && (!makeNewFolder)) ? 'background-color:#DFE9F5;' : '') + 'cursor:pointer;' + ((we_editDirID != entries[i].ID) ? '' : '') + '"' + ((we_editDirID || makeNewFolder) ? '' : onclick) + (entries[i].isFolder ? ondblclick : '') + '>' +
						'<td class="selector" width="25" align="center">' +
						'<img class="treeIcon" src="' + top.dirs.TREE_ICON_DIR + entries[i].icon + '"/>' +
						'</td>' +
						(we_editDirID == entries[i].ID ?
										'<td class="selector"><input type="hidden" name="we_FolderText" value="' + entries[i].text + '" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="' + entries[i].text + '" class="wetextinput" style="width:100%" />' :
										'<td class="selector filename" style="" ><div class="cutText">' + entries[i].text + '</div>'
										) +
						'</td></tr>';
	}
	d.innerHTML = '<form name="we_form" target="fscmd" action="' + top.options.formtarget + '" onsubmit="document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);return true;">' + body + '</table></form>';
	if (makeNewFolder || top.we_editDirID) {
		top.fsbody.document.we_form.we_FolderText_tmp.focus();
		top.fsbody.document.we_form.we_FolderText_tmp.select();
	}
}

function queryString(what, id, o, we_editDirID) {
	if (!o) {
		o = top.order;
	}
	if (!we_editDirID) {
		we_editDirID = "";
	}
	return options.formtarget + '?what=' + what + '&rootDirID=' + options.rootDirID + '&open_doc="+options.open_doc+"&table=' + options.table + '&id=' + id + (o ? ("&order=" + o) : "") + (we_editDirID ? ("&we_editDirID=" + we_editDirID) : "");
}