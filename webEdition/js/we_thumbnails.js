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
 * free software, you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
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

WE().util.loadConsts(document, "g_l.thumbnail");
var thumbnails = WE().util.getDynamicVar(document, 'loadVarThumbnails', 'data-thumbnails');

function init() {
	window.focus();
	changeFormat();
}

function closeOnEscape() {
	return true;
}

function saveOnKeyBoard() {
	we_save();
	return true;
}

function we_save() {
	top.document.getElementById("thumbnails_dialog").style.display = "none";
	top.document.getElementById("thumbnails_save").style.display = "";
	top.document.we_form.save_thumbnails.value = "1";
	top.document.we_form.submit();
}

function changeFormat() {
	if (document.getElementById('Format').value == 'jpg' || document.getElementById('Format').value == 'none') {
		document.getElementById('thumbnail_quality_text_cell').style.display = '';
		document.getElementById('thumbnail_quality_value_cell').style.display = '';
	} else {
		document.getElementById('thumbnail_quality_text_cell').style.display = 'none';
		document.getElementById('thumbnail_quality_value_cell').style.display = 'none';
	}
}

function change_thumbnail(id) {
	window.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=editThumbs&id=" + id;
}

function add_thumbnail() {
	var name = window.prompt(WE().consts.g_l.thumbnail.new, '');

	if (name === null) {
		return;
	}
	if ((name.indexOf('<') !== -1) || (name.indexOf('>') !== -1)) {
		top.we_showMessage(WE().consts.g_l.main.name_nok, WE().consts.message.WE_MESSAGE_ERROR, window);
		return;
	}

	if (name.indexOf("'") !== -1 || name.indexOf(",") !== -1) {
		top.we_showMessage(WE().consts.g_l.thumbnail.hochkomma, WE().consts.message.WE_MESSAGE_ERROR, window);
	} else if (name === "") {
		top.we_showMessage(WE().consts.g_l.thumbnail.empty, WE().consts.message.WE_MESSAGE_ERROR, window);
	} else if (WE().util.in_array(name, thumbnails.thumbnail_names)) {
		top.we_showMessage(WE().consts.g_l.thumbnail.exists, WE().consts.message.WE_MESSAGE_ERROR, window);
	} else {
		window.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=editThumbs&newthumbnail=" + encodeURI(name);
	}

}


function delete_thumbnail() {
	if (WE().util.hasPerm('ADMINISTRATOR')) {
		var deletion = window.confirm(WE().util.sprintf(WE().consts.g_l.thumbnail.delete_prompt, thumbnails.selectedName));
		if (deletion) {
			window.location = WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=editThumbs&deletethumbnail=' + thumbnails.selectedID;
		}
	}
}