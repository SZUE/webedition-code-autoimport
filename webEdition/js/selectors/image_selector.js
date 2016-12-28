/* global top, fileSelect, WE, entries,writeBodyDocument */

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

function writeBody(d) {
	switch (top.fileSelect.options.view) {
		case WE().consts.selectors.VIEW_LIST:
			writeBodyDocument(d);
			break;
		default:
			var body = (top.fileSelect.data.we_editDirID ?
				'<input type="hidden" name="what" value="' + WE().consts.selectors.DORENAMEFOLDER + '" />' +
				'<input type="hidden" name="we_editDirID" value="' + top.fileSelect.data.we_editDirID + '" />' :
				'<input type="hidden" name="what" value="' + WE().consts.selectors.CREATEFOLDER + '" />'
				) +
				'<input type="hidden" name="order" value="' + top.fileSelect.data.order + '" />' +
				'<input type="hidden" name="rootDirID" value="' + top.fileSelect.options.rootDirID + '" />' +
				'<input type="hidden" name="table" value="' + top.fileSelect.options.table + '" />' +
				'<input type="hidden" name="id" value="' + top.fileSelect.data.currentDir + '" />' +
				(top.fileSelect.data.makeNewFolder ?
					'<div class="imgDiv">' + WE().util.getTreeIcon(WE().consts.contentTypes.FOLDER, false) + '<br/>' +
					'<input type="hidden" name="we_FolderText" value="' + WE().consts.g_l.fileselector.new_folder_name + '" /><input onMouseDown="window.metaKeys.inputClick=true" name="we_FolderText_tmp" type="text" value="' + WE().consts.g_l.fileselector.new_folder_name + '" class="wetextinput" style="width:100%" />' +
					'</div>' :
					'');
			for (var i = 0; i < entries.length; i++) {
				var onclick = ' onclick="return selectorOnClick(event,' + entries[i].ID + ');"';
				var ondblclick = ' onDblClick="return selectorOnDblClick(' + entries[i].ID + ');"';
				body += '<div class="imgDiv ' + ((entries[i].ID == top.fileSelect.data.currentID) ? "selected" : "") + '" id="line_' + entries[i].ID + '" title="' + entries[i].text + '" ' + ((top.fileSelect.data.we_editDirID || top.fileSelect.data.makeNewFolder) ? "" : onclick) + (entries[i].isFolder ? ondblclick : "") + '>' +
					(entries[i].isFolder ? WE().util.getTreeIcon(WE().consts.contentTypes.FOLDER) : '<img src="' + WE().consts.dirs.WEBEDITION_DIR + "thumbnail.php?id=" + entries[i].ID + "&amp;size[width]=150&amp;path=" + entries[i].path + "&amp;extension=.jpg&amp;size[height]=200" + '" class="icon"/>') +
					'<div class="imgText selector">' +
					(top.fileSelect.data.we_editDirID == entries[i].ID ?
						'<input type="hidden" name="we_FolderText" value="' + entries[i].text + '" /><input onmousedown="window.metaKeys.inputClick=true" name="we_FolderText_tmp" type="text" value="' + entries[i].text + '" class="wetextinput" style="width:100%" />' :
						entries[i].text) +
					'</div></div>';
			}
			d.innerHTML = '<form name="we_form" target="fscmd" method="post" action="' + top.fileSelect.options.formtarget + '" onsubmit="document.we_form.we_FolderText.value=document.we_form.we_FolderText_tmp.value;return true;">' + body + '</form>';
			if (top.fileSelect.data.makeNewFolder || top.fileSelect.data.we_editDirID) {
				top.fsbody.document.we_form.we_FolderText_tmp.focus();
				top.fsbody.document.we_form.we_FolderText_tmp.select();
			}
			break;
	}
}