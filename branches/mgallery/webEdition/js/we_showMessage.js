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

// this function is universal function for all messages in webEdition
var WE_MESSAGE_INFO = -1;
var WE_MESSAGE_FRONTEND = -2;
var WE_MESSAGE_NOTICE = 1;
var WE_MESSAGE_WARNING = 2;
var WE_MESSAGE_ERROR = 4;

function we_showMessage(message, prio, win) {
	if (win && win.top && typeof win.top.showMessage === 'function') {
		win.top.showMessage(message, prio, win);
	} else if (win.top.opener) {
		if (win.top.opener.top.showMessage !== undefined) {
			win.top.opener.top.showMessage(message, prio, win);
		} else if (win.top.opener.top.opener !== undefined && win.top.opener.top.opener.top.showMessage !== undefined) {
			win.top.opener.top.opener.top.showMessage(message, prio, win);
		} else if (win.top.opener.top.opener !== undefined && win.top.opener.top.opener.top.opener !== undefined && win.top.opener.top.opener.top.opener !== null && win.top.opener.top.opener.top.opener.top.showMessage !== undefined) {
			win.top.opener.top.opener.top.opener.top.showMessage(message, prio, win);
		} else {//nichts gefunden
			if (!win) {
				win = window;
			}
			win.alert(message);
		}
	} else { // there is no webEdition window open, just show the alert
		if (!win) {
			win = window;
		}
		win.alert(message);

	}
}

function WE() {
	/*if (WE.instance !== undefined) {
		return WE.instance;
	}*/
	if (top.WebEdition !== undefined) {
		WE.instance = top.WebEdition;
		return WE.instance;
	}
	if (top.window.WebEdition !== undefined) {
		WE.instance = top.window.WebEdition;
		return WE.instance;
	}
	if (top.window.opener.top.WebEdition !== undefined) {
		WE.instance = top.window.opener.top.WebEdition;
		return WE.instance;
	}
	if (top.window.opener.top.opener.top.WebEdition !== undefined) {
		WE.instance = top.window.opener.top.opener.top.WebEdition;
		return WE.instance;
	}
	if (top.window.opener.top.opener.top.opener.top.WebEdition !== undefined) {
		WE.instance = top.window.opener.top.opener.top.opener.top.WebEdition;
		return WE.instance;
	}

	return {};
}