/* global WE, top */

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
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
'use strict';

var _EditorFrame = WE().layout.weEditorFrameController.getEditorFrame(window.parent.name);

function we_cmd() {
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case 'switch_edit_page':
			_EditorFrame.setEditorEditPageNr(args[1]);
			window.parent.we_cmd.apply(caller, args);
			break;
		default:
			if (top.we_cmd) {
				top.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
			}
	}

}

var we_editor_header = {
	timeout: null,
	evtCounter: 0,
	dragEnter: function () {
		++this.evtCounter;
		this.scrollUpEditorContent();
	},
	dragLeave: function () {
		if (--this.evtCounter === 0) {
			clearTimeout(this.timeout);
		}
	},
	scrollUpEditorContent: function () {
		_EditorFrame.getContentEditor().scrollBy(0, -10);
		if (this.evtCounter) {
			this.timeout = setTimeout(we_editor_header.scrollUpEditorContent, 66);
		}
	}
};

var weTabs = WE().session.seemode ? '' : new (WE().layout.we_tabs)(document, window);

if (WE().session.seemode) {
	top.setFrameSize = function () {
	};
}

function setTab(we_cmd_args){
	//first arg is always switch_edit_page
	if(false && window.parent.editFooter.doc.isBinary){// IMI: checking fileupload.ready is temprarily deactivated
		WE().layout.checkFileUpload(we_cmd_args);
	}else{
		we_cmd.apply(window, we_cmd_args);
	}
}
