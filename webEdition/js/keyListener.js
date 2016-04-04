/* global WE */

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

/**
 * parent class for the keylistener, this is implemented with the design pattern:
 * "chain of responsibility"
 */
function keyBoardListener(_successor) {
	var win = null;
	/**
	 * element of type keyBoardListener to forward action if needed
	 */
	this.successor = (_successor ? _successor : null);

	/**
	 * abstract function overwrite this!
	 * @param {Event} evt
	 */
	this.dealEvent = function (evt) {
		alert("You MUST overwrite the function dealEvent!");
		return this.next(evt);

	};
	/**
	 *
	 * @param {Event} evt
	 */
	this.next = function (evt) {
		if (this.successor !== null) {
			this.successor.win = this.win;
			return this.successor.dealEvent(evt);
		}
		return false;
	};

	/**
	 * cancels an event if possible
	 * @param {Event} evt
	 */
	this.cancelEvent = function (evt) {
		evt.preventDefault();
		evt.stopPropagation();
	};
}


/**
 * member of CoR
 * This Object closes a popup-Window, when the "ESCAPE" key is pressed
 *
 * On ESCAPE, top.closeOnEscape() is called, depending on the return value, the
 * dialog is closed and the event killed(true) or the event is fowrarded (false)
 * If the function does not exist, the window is NOT closed, the function
 * top.closeOnEscape() allows a user confirmation, which can be useful for
 * several dialogs
 *
 * On ENTER, top.applyOnEnter(), if exists, with the current event as parameter
 * is called. Each dialog can be forwarded to the original "Ok"-Functions.
 * Furthermore some checks about the context of the event are possible. If the
 * function does not exist, nothing happens. Depending on the return value of
 * this function, the event is cancelled (true) or not (false)
 *
 * otherwise forwards the event to successor
 *
 * @param {keyBoardListener} _successor
 */
function keyDialogListener(_successor) {
	this.successor = (_successor ? _successor : null);

	this.dealEvent = function (evt) {
		switch (evt.keyCode) {
			case 27:// ESCAPE
				// does function closeOnEscape exist!
				if (this.win.top.closeOnEscape !== undefined) {

					if (this.win.top.closeOnEscape()) {
						this.cancelEvent(evt);
						WE().util.jsWindow.prototype.closeAll(this.win);
						//this.win.top.close();
						return true;
					}
				}

				break;
			case 13:// ENTER
				// does function applyOnEnter exist?
				if (this.win.top.applyOnEnter !== undefined) {
					if (this.win.top.applyOnEnter(evt)) {
						this.cancelEvent(evt);
						return true;
					}
				}

				break;
		}
		return this.next(evt);
	};
}

keyDialogListener.prototype = new keyBoardListener();

/**
 * member of CoR
 * This Object closes a popup-Window, when the "ESCAPE" key is pressed
 *
 * On STRG-S, top.saveOnKeyboard(), if exists, is called. This function can save
 * the model. Depending on the return value, the event is cancelled (true) or
 * forwarded (false)
 *
 * @param {keyBoardListener} _successor
 */
function keyDialogSaveListener(_successor) {

	this.successor = (_successor ? _successor : null);
	this.dealEvent = function (evt) {

		switch (evt.keyCode) {
			case 83:// S (Save)
				if (this.win.top.saveOnKeyBoard !== undefined) {
					if (this.win.top.saveOnKeyBoard()) {
						this.cancelEvent(evt);
						return true;
					}
				}
				break;
		}

		return this.next(evt);
	};
}

keyDialogSaveListener.prototype = new keyBoardListener();

/**
 * member of CoR
 * defines several actions for the current active editor, if possible
 * - save (STR+S)
 * - publish (STRG-SHIFT-S)
 * - close current Tab (STR+F4)
 *
 * otherwise forwards the event to successor
 *
 * @param {keyBoardListener} _successor
 */
function keyEditorListener(_successor) {
	this.successor = (_successor ? _successor : null);

	this.dealEvent = function (evt) {
		_editor = false;
		_editorType = "";

		if(!WE()){
			return;
		}
		// check if an editor is open

		_activeEditorFrame = WE().layout.weEditorFrameController.getActiveEditorFrame();
		if (WE().layout.weEditorFrameController.getActiveDocumentReference()) {
			_editorType = _activeEditorFrame.getEditorType();
			if (_editorType === "model") {
				_editor = true;
			}
		}

		if (_editor) {
			switch (evt.keyCode) {
				case 83: //S
					if (evt.shiftKey) { // SHIFT + S (Publish)
						self.focus(); // focus, to avoid a too late onchange of editor
						this.cancelEvent(evt);
						_activeEditorFrame.setEditorPublishWhenSave(true);
						if (_activeEditorFrame.getEditorFrameWindow().frames.editFooter.we_save_document !== undefined) {
							_activeEditorFrame.getEditorFrameWindow().frames.editFooter.we_save_document();
						}
					} else {// S (Save)
						self.focus();  // focus, to avoid a too late onchange of editor
						this.cancelEvent(evt);
						_activeEditorFrame.setEditorPublishWhenSave(false);
						if (_activeEditorFrame.getEditorFrameWindow().frames.editFooter.we_save_document !== undefined) {
							_activeEditorFrame.getEditorFrameWindow().frames.editFooter.we_save_document();
						}
					}
					return true;

				case 90://Strg-z
//					console.log("strg-z canceled");
					return true;
				case 87://W
				case 115: //F4 (closing a tab)
					self.focus();  // focus, to avoid a too late onchange of editor
					this.cancelEvent(evt);
					WE().layout.weEditorFrameController.closeDocument(_activeEditorFrame.getFrameId());
					return true;
			}
		}

		switch (evt.keyCode) {
			case 87://W
			case 115: //F4 (closing a tab)
				if (_editorType === "cockpit") {
					self.focus();  // focus, to avoid a too late onchange of editor
					WE().layout.weEditorFrameController.closeDocument(_activeEditorFrame.getFrameId());
				}
				this.cancelEvent(evt);
				return true;
		}

		return this.next(evt);
	};
}
keyEditorListener.prototype = new keyBoardListener();

/**
 * member of CoR
 * defines several actions for the modules
 * - save (STR+S)
 *
 * otherwise forwards the event to successor
 *
 * @param {keyBoardListener} _successor
 */
function keyModuleListener(_successor) {
	this.successor = (_successor ? _successor : null);

	this.dealEvent = function (evt) {
		if (this.win.top.weModuleWindow !== undefined) {
			if (evt.keyCode === 83) { // S (Save)
				if (this.win.top.content &&
								this.win.top.content.editor &&
								this.win.top.content.editor.edfooter &&
								this.win.top.content.editor.edfooter.we_save !== undefined) {
					this.cancelEvent(evt);
					this.win.top.content.editor.edfooter.we_save();
					return true;
				}
			}
		}
		return this.next(evt);
	};

}
keyModuleListener.prototype = new keyBoardListener();


/**
 * member of CoR
 * defines several actions for the tools
 * - save (STR+S)
 *
 * otherwise forwards the event to successor
 *
 * @param {keyBoardListener} _successor
 */

function keyToolListener(_successor) {
	this.successor = (_successor ? _successor : null);

	this.dealEvent = function (evt) {
		if (this.win.top.weToolWindow !== undefined) {
			if (evt.keyCode == 83) { // S (Save)
				if (this.win.top.content &&
								this.win.top.content.resize &&
								this.win.top.content.resize.editor &&
								this.win.top.content.resize.editor.edfooter &&
								this.win.top.content.resize.editor.edfooter.we_save !== undefined) {
					this.cancelEvent(evt);
					this.win.top.content.resize.editor.edfooter.we_save();
					return true;
				}
				if (this.win.top.content &&
								this.win.top.content.resize &&
								this.win.top.content.resize.editor &&
								this.win.top.content.resize.editor.edfooter &&
								this.win.top.content.weCmdController) {
					this.win.top.content.weCmdController.fire({
						"cmdName": "app_" + this.win.top.content.appName + "_save"
					});
					return true;
				}
			}
		}
		return this.next(evt);
	};
}
keyToolListener.prototype = new keyBoardListener();

/**
 * member of CoR
 * - opens a prompt to input a tagname, this is opened with Tag-Wizard then
 *
 * @param {keyBoardListener} _successor
 */
function keyTagWizardListener(_successor) {
	this.successor = (_successor ? _successor : null);
	this.dealEvent = function (evt) {

		if (evt.keyCode === 73) { // I (Open Tag-Wizard Prompt)

			_activeEditorFrame = WE().layout.weEditorFrameController.getActiveEditorFrame();

			if (_activeEditorFrame.getEditorContentType() === "text/weTmpl" &&
							_activeEditorFrame.getEditorFrameWindow().frames.editFooter.tagGroups.alltags !== undefined) {

				_activeEditorFrame.getEditorFrameWindow().frames.editFooter.openTagWizardPrompt();
				this.cancelEvent(evt);
				return true;
			}
		}
		return this.next(evt);
	};
}
keyTagWizardListener.prototype = new keyBoardListener();

/**
 * member of CoR
 * trys to avoid the reload button in the main window, if possible
 * - F5
 *
 * otherwise forwards the event to successor
 *
 * @param {keyBoardListener} _successor
 */
function keyReloadListener(_successor) {
	this.successor = (_successor ? _successor : null);
	this.dealEvent = function (evt) {
		switch (evt.keyCode) {
			case 82:// R Reload
			case 90://Z Back
			case 116: // F5
				this.cancelEvent(evt);
				return true;
		}
		return this.next(evt);
	};
}

keyReloadListener.prototype = new keyBoardListener();


// build the CoR
WE().handler.keyListener = new keyEditorListener(new keyModuleListener(new keyToolListener(new keyDialogListener(new keyDialogSaveListener(new keyTagWizardListener(new keyReloadListener( )))))));

/**
 * Receives all Keyboard Events and forwards them, if required
 *
 * @param {Event} evt
 */

function dealWithKeyboardShortCut(evt, win) {
	// This function receives all events, when a key is pressed and forwards the event to
	// the first keyboardlistener ("chain of responsibility")
	WE().handler.keyListener.win = win;
	switch (evt.keyCode) {
		case -1:
			WE().handler.keyListener.cancelEvent(evt);
			return true;
		case 27: // ESCAPE
		case 13: // ENTER
		case 116: // F5 - works only in FF
			return WE().handler.keyListener.dealEvent(evt);
		case 45://ins
		case 46://del
		case 67: //C
		case 86: //V
			break;
		default:
			return (evt.ctrlKey || evt.metaKey ?
							WE().handler.keyListener.dealEvent(evt) : true);
	}
}
