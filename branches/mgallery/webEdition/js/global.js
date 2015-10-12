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
function WE() {
	if (top.WebEdition !== undefined) {
		return top.WebEdition;
	}
	if (top.window.WebEdition !== undefined) {
		return top.window.WebEdition;
	}
	if (top.window.opener) {
		if (top.window.opener.top.WebEdition !== undefined) {
			return top.window.opener.top.WebEdition;
		}
		if (top.window.opener.top.opener) {
			if (top.window.opener.top.opener.top.WebEdition !== undefined) {
				return top.window.opener.top.opener.top.WebEdition;
			}
			if (top.window.opener.top.opener.top.opener && top.window.opener.top.opener.top.opener.top.WebEdition !== undefined) {
				return top.window.opener.top.opener.top.opener.top.WebEdition;
			}
		}
	}
	return false;
}

function we_showMessage(message, prio, win) {
	win = (win ? win : this.window);
	if (WE()) {
		WE().util.showMessage(message, prio, win);
	} else { // there is no webEdition window open, just show the alert
		win.alert(message);
	}
}

function initWE() {
//make some assignments to all WE documents
	if (WE()) {
		try {
			window.onerror = WE().handler.errorHandler;
			document.addEventListener('keydown', function (evt) {
				WE().handler.dealWithKeyboardShortCut(evt, window);
			});
		} catch (e) {
			console.log('unable to add listeners');
		}
	} else {
		//console.log('error handler possibly not attached');
	}
}