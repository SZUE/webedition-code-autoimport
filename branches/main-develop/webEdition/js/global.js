/* global top,window */

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
'use strict';

/**This file is intended to be a global file for many js functions in WE*/
// this function is universal function for all messages in webEdition
function WE(retBool) {
	if (top === null || top === undefined) {
		throw new Error("webedition (top) not found");
	}
	if (top.WebEdition !== undefined) {
		return top.WebEdition;
	}
	if (top.window.WebEdition !== undefined) {
		return top.window.WebEdition;
	}
	var cur = top;
	for (var i = 0; i < 10; i++) {
		cur = cur.top.opener;
		if (cur && cur.top) {
			if (cur.top.WebEdition) {
				return cur.top.WebEdition;
			}
		} else {
			if (retBool) {
				return false;
			}
			throw new Error("WE not found (1)");
		}
	}
	if (retBool) {
		return false;
	}
	throw new Error("webedition (final) not found");
}

function initWE() {
//make some assignments to all WE documents
	if (WE(true)) {
		try {
			window.onerror = WE().handler.errorHandler;
			window.document.addEventListener('keydown', function (evt) {
				WE().handler.dealWithKeyboardShortCut(evt, window);
			});
			window.document.addEventListener('drop', function (evt) {
				evt.stopPropagation();
				evt.preventDefault();
			});
			window.document.addEventListener('dragover', function (evt) {
				evt.preventDefault();
			});
		} catch (e) {
			window.console.log('unable to add listeners');
		}
	} else {
		//console.log('error handler possibly not attached');
	}
}