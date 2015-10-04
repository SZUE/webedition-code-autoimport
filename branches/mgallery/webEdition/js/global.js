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

/**This file is intended to be a global file for many js functions in WE*/
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
	if (top.WebEdition !== undefined) {
		return top.WebEdition;
	}
	if (top.window.WebEdition !== undefined) {
		return top.window.WebEdition;
	}
	if (top.window.opener.top.WebEdition !== undefined) {
		return top.window.opener.top.WebEdition;
	}
	if (top.window.opener.top.opener.top.WebEdition !== undefined) {
		return top.window.opener.top.opener.top.WebEdition;
	}
	if (top.window.opener.top.opener.top.opener.top.WebEdition !== undefined) {
		return top.window.opener.top.opener.top.opener.top.WebEdition;
	}

	return {};
}
