/* global WE, top */

/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 12778 $
 * $Author: mokraemer $
 * $Date: 2016-09-14 21:00:48 +0200 (Mi, 14. Sep 2016) $
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

function init(){
	_fo=document.forms[0];
	initPrefs();
}

function save(){
	savePrefs();
	previewPrefs();
	refresh();
	top.we_showMessage(WE().consts.g_l.main.prefs_saved_successfully, WE().consts.message.WE_MESSAGE_NOTICE, window);
	self.close();
}

function preview(){
	previewPrefs();
	refresh();
}

function exit_close(){
	//previewPrefs();
	refresh();
	exitPrefs();
	self.close();
}
