/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 11591 $
 * $Author: mokraemer $
 * $Date: 2016-03-07 18:28:18 +0100 (Mo, 07. Mär 2016) $
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

function init() {
	_fo = document.forms[0];
	initPrefs();
}

function save() {
	savePrefs();
	previewPrefs();
	opener.saveSettings();
	top.we_showMessage(WE().consts.g_l.main.prefs_saved_successfully, WE().consts.message.WE_MESSAGE_NOTICE, window);
	self.close();
}

function preview() {
	previewPrefs();
}

function exit_close() {
	previewPrefs();
	exitPrefs();
	self.close();
}
