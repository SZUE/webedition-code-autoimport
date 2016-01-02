/* global WE, top */

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
function we_cmd() {
	//var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
//	var url = WE().util.getWe_cmdArgsUrl(args);

	if (top.we_cmd) {
		top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}

function startMultiEditor() {
	WE().layout.multiTabs = new TabView(this.document);
	var args = ['start_multi_editor'];
	we_cmd.apply(this, args.concat(Array.prototype.slice.call(arguments)));
}


//--------------------------------------------------------------------
// CLASS:
//   EditorFrameController
//
// DESCRIPTION:
//   manages all available EditorFrames
//
// ARGUMENTS:
//   none
//
// RETURNS:
//   none
//--------------------------------------------------------------------
function EditorFrameController() {

	// reference to the Editor Frameset: used to manipulate the "cols" attribute
	this.MultiEditorFrameset = null;

	// reference to the window containing the EditorFrameset, check if we need this
	this.MultiEditorFramesetWindow = null;

	// currently active Frameset, id of the frame
	this.ActiveEditorFrameId = null;

	// array/object of the Editor Frames (EditorFrame)
	this.EditorFrames = null;

	// array of ids of free EditorFramesets
	this.FreeEditorFrames = null;

	// amount of editorFrames
	this.EditorWindowsAmount = 0;

	//----------------------------------------
	// functions concerning the functionality
	//----------------------------------------

	//--------------------------------------------------------------------
	// FUNCTION:
	//   init
	//
	// DESCRIPTION:
	//   initailises the EditorFrameControler
	//
	// ARGUMENTS:
	//   none
	//
	// RETURNS:
	//   nothing
	//--------------------------------------------------------------------
	this.init = function () {

		// init references to FramesetWindow and Frameset
		this.MultiEditorFramesetWindow = top.bm_content_frame;
		this.MultiEditorFrameset = this.MultiEditorFramesetWindow.document.getElementById("multiEditorFrameset");

		var _frames = this.MultiEditorFramesetWindow.document.getElementsByTagName("iframe");

		if (_frames.length) {

			this.EditorFrames = {};
			this.FreeEditorFrames = [];
			this.EditorWindowsAmount = _frames.length;

			for (i = 0; i < _frames.length; i++) {
				this.EditorFrames[_frames[i].id] = new EditorFrame(_frames[i], _frames[i].id);
				this.FreeEditorFrames.push(_frames[i].id);

			}
		}
	};

	//--------------------------------------------------------------------
	// FUNCTION:
	//   isInitialized
	//
	// DESCRIPTION:
	//   returns if the EditorFrameControlle is initialized
	//
	// ARGUMENTS:
	//   none
	//
	// RETURNS:
	//   boolean
	//--------------------------------------------------------------------
	this.isInitialized = function () {
		return (this.EditorFrames !== null);
	};

	/**
	 * FUNCTION:
	 *	openDocument
	 *
	 * DESCRIPTION:
	 *	tries to open a document in a (new) editor window.
	 *	in Expert mode:
	 *		Switch Editor to the document if it is already open | open document in a new editor if possible
	 *	in SeeMode:
	 *		Open document in window, ask to save unsaved changes
	 *
	 *	possible we_cmds are:
	 *		- new_document
	 *		- edit_document
	 *
	 * ARGUMENTS:
	 *   table: table of the document
	 *   id: id of the document
	 *   ct: contenttype of the document
	 *   editcmd: other command like cockpit, etc.
	 *   dt: ??? (transaction??)
	 *   url: url when opening a document via URL (SeeMode)
	 *   code: ?? (initial code for the document)
	 *   mode: ??
	 *
	 * RETURNS:
	 *   nothing
	 */
	this.openDocument = function (table, id, ct, editcmd, dt, url, code, mode, parameters) {

		if (this.EditorFrames === null) {
			this.init();

		}

		// initalize variables
		dt = dt ? dt : ""; // doctype if open a document via doctype!
		editcmd = editcmd ? editcmd : ""; // editcmd like open_cockpit
		url = url ? url : ""; // doctype if open a document via doctype!
		code = code ? code : ""; // doctype if open a document via doctype!
		mode = mode ? mode : ""; // doctype if open a document via doctype!
		parameters = parameters ? parameters : "";

		// editcmd can be one of open_cockpit

		// check if a already open document shall be opened
		if ((_editorId = this.getEditorIdOfOpenDocument(table, id, editcmd, url))) { // activate open tab

			if (parameters !== this.getEditorFrame(_editorId).getEditorDocumentParameters()) {
				// re-open document
				this.closeDocument(_editorId, "WE().layout.weEditorFrameController.openDocument(\"" + table + "\" ,\"" + id + "\",\"" + ct + "\",\"" + editcmd + "\",\"" + dt + "\",\"" + url + "\",\"" + code + "\",\"" + mode + "\",\"" + parameters + "\");");

			} else if (this.ActiveEditorFrameId !== _editorId) {

				// add to navigationHistory:
				top.weNavigationHistory.addDocToHistory(table, id, ct, editcmd, url, parameters);

				// activate in tree
				if (top.treeData && top.treeData.table === table) {
					top.treeData.selectNode(id);

				} else if (top.treeData) {
					top.treeData.unselectNode();

				}

				// activate tab
				WE().layout.multiTabs.openTab(_editorId);

				this.setActiveEditorFrame(_editorId);
				this.toggleFrames();

			}

		} else { // open new frame if possible

			if (this.getNumberOfFreeWindows() > 0) { // if there is a free frame, use it
				if (editcmd || url || id) {	// add to navigationHistory
					top.weNavigationHistory.addDocToHistory(table, id, ct, editcmd, url, parameters);
				}

				if (editcmd) { // open cockpit !
					// deactivate tree
					if (top.treeData) {
						top.treeData.unselectNode();
					}
					top.we_cmd(editcmd);

				} else if (url) {
					we_cmd('open_extern_document', url, parameters);

				} else if (id) { // edit_document
					if (parameters) {
						we_cmd('edit_document_with_parameters', table, id, ct, parameters);

					} else {
						// instead calling the command we could also build the url and call it from here
						we_cmd('edit_document', table, id, ct);
					}
				} else if (code !== undefined && code) { // open new document with standard code
					we_cmd('new_document', table, id, ct, "", "", "", "", dt, "", code);
				} else {
					we_cmd('new_document', table, id, ct, "", "", "", "", dt);
				}
			} else {
				if (this.EditorWindowsAmount === 1) { // only one active document here, for example SeeMode
					// build nextCmd
					// table,id,ct,editcmd,dt,url,code,mode
					this.closeDocument(this.ActiveEditorFrameId, "WE().layout.weEditorFrameController.openDocument(\"" + table + "\" ,\"" + id + "\",\"" + ct + "\",\"" + editcmd + "\",\"" + dt + "\",\"" + url + "\",\"" + code + "\",\"" + mode + "\",\"" + parameters + "\");");
				} else {
					top.we_showMessage(WE().consts.g_l.main.no_editor_left, WE().consts.message.WE_MESSAGE_ERROR, window);
				}
			}
		}
	};

	/**
	 * FUNCTION:
	 *   closeDocument
	 *
	 * DESCRIPTION:
	 *	This function is called when a document of the editor is closed
	 *	Here are checks, if the document has unsaved changes
	 *
	 * ARGUMENTS:
	 *   frameId - id of the editor window, which should be freed
	 *   nextCommand - string
	 *
	 * RETURNS:
	 *   nothing
	 */
	this.closeDocument = function (editorId, nextCommand) {
		if (this.EditorFrames === null) {
			this.init();
		}

		nextCommand = (nextCommand ? nextCommand : "");

		if (top.we_cmd("eplugin_exit_doc", this.getEditorFrame(editorId).getEditorTransaction())) {

			if (this.EditorFrames[editorId]) {

				// check if there are unsaved changes
				if (this.getEditorFrame(editorId).getEditorIsHot()) {
					this.showEditor(editorId);

					if (!this.getEditorFrame(editorId).EditorExitDocQuestionDialog) { // open exit_doc_question if not already open
						this.getEditorFrame(editorId).EditorExitDocQuestionDialog = true;
						this.getEditorFrame(editorId).EditorExitDocQuestionDialog = top.we_cmd("exit_doc_question", editorId, this.getEditorFrame(editorId).getEditorContentType(), nextCommand);

					} else {
						this.getEditorFrame(editorId).EditorExitDocQuestionDialog.open();
					}
				} else {
					// free frame select next active frame
					this.closeEditorFrame(editorId);
					WE().layout.multiTabs.closeTab(editorId);

					if (WE().session.seeMode_edit_include) { // close window in edit_include_mode
						top.close();
					}

					if (nextCommand) {
						eval(nextCommand);
					}
				}
			} else {
				WE().layout.multiTabs.closeTab(editorId);
			}
		}
	};

	/**
	 * FUNCTION:
	 *   doLogout
	 *
	 * DESCRIPTION:
	 *	On logout all editors must be checked for unsaved changes
	 *
	 * ARGUMENTS:
	 *   none
	 *
	 * RETURNS:
	 *   boolean
	 */
	this.doLogoutMultiEditor = function () {
		// close all none Hot Editors
		if (this.FreeEditorFrames.length !== this.EditorWindowsAmount) {
			_UsedEditors = this.getEditorsInUse();
			for (var frameId in _UsedEditors) {
				// remove all from editor-plugin
				top.we_cmd("remove_from_editor_plugin", _UsedEditors[frameId].getEditorTransaction());
				if (!_UsedEditors[frameId].getEditorIsHot()) {
					this.closeDocument(frameId);
				}
			}
		}

		// if all Editors are closed,
		if (this.FreeEditorFrames.length !== this.EditorWindowsAmount) {
			if ((this.EditorWindowsAmount - this.FreeEditorFrames.length) === 1) { // seeMode
				this.closeDocument(this.ActiveEditorFrameId, 'top.we_cmd("dologout");');
			} else {
				top.we_cmd("exit_multi_doc_question", 'dologout');
			}
		} else {
			return true;
		}
		return false;
	};

	/**
	 * FUNCTION:
	 *   closeAllDocuments
	 *
	 * DESCRIPTION:
	 *	If all editors are closed, all editors must be checked for unsaved changes
	 *
	 * ARGUMENTS:
	 *   none
	 *
	 * RETURNS:
	 *   nothing
	 */
	this.closeAllDocuments = function () {
		if (top.we_cmd("editor_plugin_doc_count") === 0 || confirm(WE().consts.g_l.main.eplugin_exit_doc)) {
			// close all none Hot Editors
			if (this.FreeEditorFrames.length !== this.EditorWindowsAmount) {
				_UsedEditors = this.getEditorsInUse();
				for (var frameId in _UsedEditors) {
					// remove from editor plugin
					top.we_cmd("remove_from_editor_plugin", _UsedEditors[frameId].getEditorTransaction());
					if (!_UsedEditors[frameId].getEditorIsHot()) {
						this.closeDocument(frameId);
					}
				}
			}

			// if all Editors are closed,
			if (this.FreeEditorFrames.length !== this.EditorWindowsAmount) {
				if ((this.EditorWindowsAmount - this.FreeEditorFrames.length) === 1) { // only one document open
					this.closeDocument(this.ActiveEditorFrameId, 'top.we_cmd("close_all_documents");');
				} else {
					top.we_cmd("exit_multi_doc_question", 'close_all_documents');
				}
			} else {
				return true;
			}
			return false;
		}
	};

	this.closeAllButActiveDocument = function (activeId) {

		if (top.we_cmd("editor_plugin_doc_count") === 0 || confirm(WE().consts.g_l.main.eplugin_exit_doc)) {

			// only do something, if more than one editor is open
			if ((this.EditorWindowsAmount - this.FreeEditorFrames.length) > 1) {
				// get active id, if not given
				if (!activeId) {
					activeId = this.ActiveEditorFrameId;

				}

				_UsedEditors = this.getEditorsInUse();
				var frameId;
				// remove all from editor plugin
				for (frameId in _UsedEditors) {
					if (frameId !== activeId) {
						top.we_cmd("remove_from_editor_plugin", _UsedEditors[frameId].getEditorTransaction());
					}
				}

				_UsedEditors = this.getEditorsInUse();
				// close all none Hot editors
				for (frameId in _UsedEditors) {
					if (frameId !== activeId) {
						if (_UsedEditors[frameId].getEditorIsHot()) {
							this.closeDocument(frameId, 'top.we_cmd("close_all_but_active_document", "' + activeId + '");');
							return;

						} else {
							this.closeDocument(frameId);

						}
					}
				}
			}
		}
	};

	//--------------------------------------------------------------------
	// FUNCTION:
	//   closeEditorFrame
	//
	// DESCRIPTION:
	//   Clears the current editor and frees it for the next document
	//
	// ARGUMENTS:
	//   frameId - id of the editor window, which should be freed
	//
	// RETURNS:
	//   nothing
	//--------------------------------------------------------------------
	this.closeEditorFrame = function (frameId) {
		var docRef;
		if (this.EditorFrames[frameId]) {
			switch (this.EditorFrames[frameId].EditorType) {
				case "cockpit":
					docRef = this.EditorFrames[frameId].getDocumentReference();
					// close all modal dialogs
					docRef.closeAllModalWindows();

					if (docRef.isHot()) {
						// save changes, in cockpit
						docRef.saveSettings();
					}
					break;
				case "model":
					docRef = this.EditorFrames[frameId].getDocumentReference();
					if (docRef.closeAllModalWindows) {
						docRef.closeAllModalWindows();
					}
					// unlock document
					trans = this.EditorFrames[frameId].getEditorTransaction();
					if (trans) {
						top.we_cmd('users_unlock', this.EditorFrames[frameId].getEditorDocumentId(), WE().session.userID, this.EditorFrames[frameId].getEditorEditorTable(), trans);
						top.we_cmd("remove_from_editor_plugin", trans);
					}

					if (this.getEditorFrame(frameId).EditorExitDocQuestionDialog) {
						this.getEditorFrame(frameId).EditorExitDocQuestionDialog.close();
						this.getEditorFrame(frameId).EditorExitDocQuestionDialog = false;
					}
					break;
			}

			// remove from tree, if possible
			// deactivate in tree
			if (top.treeData && top.treeData.table === this.getEditorFrame(frameId).getEditorEditorTable() && this.ActiveEditorFrameId === frameId) {
				top.treeData.unselectNode();
			}

			// about:blank
			this.EditorFrames[frameId].freeEditor();

			// add to free frames
			this.FreeEditorFrames.push(frameId);

			// make other frame active, if the closed one was active
			if (this.ActiveEditorFrameId === frameId) { // active frame was closed, show next.

				this.ActiveEditorFrameId = null;
				var _tmpKey = null;
				if (this.FreeEditorFrames.length !== this.EditorWindowsAmount) { // there are filled frames left

					var _reachedCurrent = false;
					this.ActiveEditorFrameId = null;
					for (var frameKey in this.EditorFrames) {

						if (!_reachedCurrent || _tmpKey === null) {
							if (this.EditorFrames[frameKey].getEditorIsInUse()) {
								_tmpKey = frameKey;

							}
							if (frameKey === frameId) {
								_reachedCurrent = true;

							}
						}

					}
					this.showEditor(_tmpKey);
					this.ActiveEditorFrameId = _tmpKey;
				}
			}
			this.toggleFrames();
		}
	};

	/*--------------------------------------------------------------------
	 // FUNCTION:
	 //   showEditor
	 //
	 // DESCRIPTION:
	 //   Activates the EditorFrame with the given Id
	 //
	 // ARGUMENTS:
	 //   editorId - id of the editor window
	 //
	 // RETURNS:
	 //   nothing
	 //--------------------------------------------------------------------*/
	this.showEditor = function (editorId) {
		if (editorId !== this.ActiveEditorFrameId) {
			// add to navigationHistory:
			_currentEditor = this.getEditorFrame(editorId);

			if (_currentEditor.getEditorIsInUse()) {

				top.weNavigationHistory.addDocToHistory(
								_currentEditor.getEditorEditorTable(),
								_currentEditor.getEditorDocumentId(),
								_currentEditor.getEditorContentType(),
								_currentEditor.getEditorEditCmd(),
								_currentEditor.getEditorUrl(),
								_currentEditor.getEditorDocumentParameters()
								);

				// activate tab
				WE().layout.multiTabs.openTab(editorId);

				// highlight tree
				if (top.treeData && top.treeData.table === _currentEditor.getEditorEditorTable() && parseInt(_currentEditor.getEditorDocumentId())) {
					top.treeData.selectNode(_currentEditor.getEditorDocumentId());

				} else if (top.treeData) {
					top.treeData.unselectNode();

				}
				this.setActiveEditorFrame(editorId);
				this.toggleFrames();
			}
		}
	};

	this.switchToContentEditor = function () {
		this.getActiveEditorFrame().switchToContentEditor(2);
	};


	this.switchToNonContentEditor = function () {
		this.getActiveEditorFrame().switchToContentEditor(1);
	};

	this.getVisibleEditorFrame = function () {
		editorFrame = this.getActiveEditorFrame();
		if (!editorFrame) {
			return null;
		}
		return editorFrame.getContentEditor();
	};

	this.isEditTab = function () {
		editorFrame = this.getActiveEditorFrame();
		if (!editorFrame) {
			return null;
		}
		return editorFrame.getContentEditor() === this.getActiveDocumentReference().frames[2];
	};

	//--------------------------------------------------------------------
	// FUNCTION:
	//   toggleFrames
	//
	// DESCRIPTION:
	//   sets the "cols"-Attribute of the EditorFrameset
	//
	// ARGUMENTS:
	//   none
	//
	// RETURNS:
	//   nothing
	//--------------------------------------------------------------------
	this.toggleFrames = function () {
		if (!this.isInitialized()) {

		}
		var frameId;
		//		var _colStr = "";
		if (!this.ActiveEditorFrameId) {
			first = true;
			for (frameId in this.EditorFrames) {

				if (first) {
					this.getEditorFrame(frameId).setEmptyEditor();
					if (WE().session.isChrome) {
						this.getEditorFrame(frameId).EditorFrameReference.style.display = "block";
					} else {
						this.getEditorFrame(frameId).EditorFrameReference.style.width = "100%";
						this.getEditorFrame(frameId).EditorFrameReference.style.height = "100%";
					}
					first = false;
				} else {
					if (WE().session.isChrome) {
						this.getEditorFrame(frameId).EditorFrameReference.style.display = "none";
					} else {
						this.getEditorFrame(frameId).EditorFrameReference.style.height = "0px";
						this.getEditorFrame(frameId).EditorFrameReference.style.witdh = "0px";
					}
				}
			}

		} else {
			for (frameId in this.EditorFrames) {
				if (this.ActiveEditorFrameId === frameId) {
					if (WE().session.isChrome) {
						this.getEditorFrame(frameId).EditorFrameReference.style.display = "block";
					} else {
						this.getEditorFrame(frameId).EditorFrameReference.style.width = "100%";
						this.getEditorFrame(frameId).EditorFrameReference.style.height = "100%";
					}

				} else {
					if (this.getEditorFrame(frameId).getEditorIsInUse() && this.getEditorFrame(frameId).EditorType !== "none_webedition" && this.EditorFrames[frameId].getDocumentReference().closeAllModalWindows) {
						this.EditorFrames[frameId].getDocumentReference().closeAllModalWindows();
					}
					if (this.getEditorFrame(frameId).EditorExitDocQuestionDialog) {
						this.getEditorFrame(frameId).EditorExitDocQuestionDialog.close();
						this.getEditorFrame(frameId).EditorExitDocQuestionDialog = false;
					}
					if (WE().session.isChrome) {
						this.getEditorFrame(frameId).EditorFrameReference.style.display = "none";
					} else {
						this.getEditorFrame(frameId).EditorFrameReference.style.height = "0px";
						this.getEditorFrame(frameId).EditorFrameReference.style.witdh = "0px";
					}
				}
			}
		}
	};

	//--------------------------------------------------------------------
	// FUNCTION:
	//   getFreeWindow
	//
	// DESCRIPTION:
	//   returns next free frame
	//
	// ARGUMENTS:
	//   none
	//
	// RETURNS:
	//   name EditorFrame object or false
	//--------------------------------------------------------------------
	this.getFreeWindow = function () {

		if (this.EditorFrames === null) {
			this.init();
		}

		if (this.FreeEditorFrames.length > 0) {

			return this.EditorFrames[this.FreeEditorFrames.shift()];
			//var objref = this.EditorFrames[this.FreeEditorFrames.shift()];
			//objref.useEditorFrame(); - if there occure any errors use this here
			//return objref;
		}
		return false;
	};

	//--------------------------------------------------------------------
	// FUNCTION:
	//   getNumberOfFreeWindows
	//
	// DESCRIPTION:
	//   returns number of available free frames
	//
	// ARGUMENTS:
	//   none
	//
	// RETURNS:
	//   integer
	//--------------------------------------------------------------------
	this.getNumberOfFreeWindows = function () {
		if (this.FreeEditorFrames === null) {
			return 0;
		}
		return this.FreeEditorFrames.length;
	};


	this.getEditorIdOfOpenDocument = function (table, id, editcmd, url) {

		if (id === "0" && !editcmd && !url) {
			return null;

		}
		for (var _editorId in this.EditorFrames) {
			if (table && id && this.getEditorEditorTable(_editorId) === table && this.getEditorDocumentId(_editorId) == id) { // open by id
				return _editorId;

			} else if (editcmd === "open_cockpit" && this.EditorFrames[_editorId].EditorType === "cockpit") { // open a cmd window
				return _editorId;

			} else if (url && this.EditorFrames[_editorId].EditorUrl === url) { // open with URL
				return _editorId;

			}
		}
		return null;
	};

	this.getEditorsInUse = function () {
		var _ret = {};
		for (var frameId in this.EditorFrames) {
			if (this.EditorFrames[frameId].getEditorIsInUse()) {
				_ret[frameId] = this.EditorFrames[frameId];
			}
		}
		return _ret;
	};

	//----------------------------------------
	// getters
	// all getters can have the transactionnumber as parameter,
	// but take the current editor as default
	//----------------------------------------

	this.getActiveEditorFrame = function () {
		return (this.ActiveEditorFrameId ? this.EditorFrames[this.ActiveEditorFrameId] : false);
	};

	this.getEditorFrameByTransaction = function (theTransaction) {
		for (var frameId in this.EditorFrames) {
			if (this.EditorFrames[frameId] && (this.EditorFrames[frameId].getEditorTransaction() === theTransaction)) {
				return this.EditorFrames[frameId];
			}
		}
		return null;
	};

	this.getEditorFrame = function (frameId) {
		if (frameId !== undefined) {
			return this.EditorFrames[frameId];
		}
		if (this.ActiveEditorFrameId) {
			return this.EditorFrames[this.ActiveEditorFrameId];
		}
		return false;
	};

	this.getDocumentReferenceByTransaction = function (theTransaction) {
		_win = this.getEditorFrameByTransaction(theTransaction);
		return (_win ? _win.getDocumentReference() : false);

	};

	this.getActiveDocumentReference = function () {
		if (this.EditorFrames === null) {
			this.init();
		}

		_win = this.getActiveEditorFrame();
		return (_win ? _win.getDocumentReference() : false);
	};

	//----------------------------------------
	// setters
	//----------------------------------------
	this.setActiveEditorFrame = function (id) {

		if (this.ActiveEditorFrameId !== id) {
			if ((_oldActive = this.getEditorFrame(this.ActiveEditorFrameId))) {
				_oldActive.setEditorIsActive(false);
			}

			this.ActiveEditorFrameId = id;

			_EditorWindow = this.getEditorFrame(id);
			_EditorWindow.setEditorIsActive(true);
		}
	};

	this.setEditorIsHot = function (newVal, id) {
		_EditorWindow = this.getEditorFrame(id);
		if (_EditorWindow) {
			_EditorWindow.setEditorIsHot(newVal);
		}
	};

	//----------------------------------------
	// getters
	//----------------------------------------

	this.getEditorIsHot = function (id) {
		_EditorWindow = this.getEditorFrame(id);
		if (_EditorWindow) {
			return _EditorWindow.getEditorIsHot();
		}
		return null;
	};

	this.getEditorTransaction = function (frameId) {
		if ((_EditorFrame = this.getEditorFrame(frameId))) {
			return _EditorFrame.getEditorTransaction();
		}
		return null;

	};

	this.getEditorDocumentId = function (frameId) {
		if ((_EditorFrame = this.getEditorFrame(frameId))) {
			return _EditorFrame.getEditorDocumentId();
		}
		return null;
	};

	this.getEditorEditorTable = function (frameId) {
		var _EditorFrame = this.getEditorFrame(frameId);
		return _EditorFrame.getEditorEditorTable();

	};

	this.getEditorIsHot = function (frameId) {
		var _EditorFrame = this.getEditorFrame(frameId);
		return _EditorFrame.getEditorIsHot();

	};

	this.getEditorEditPageNr = function (frameId) {
		var _EditorFrame = this.getEditorFrame(frameId);
		return _EditorFrame.getEditorEditPageNr();

	};

	this.getEditorIsActive = function (frameId) {
		var _EditorFrame = this.getEditorFrame(frameId);
		return _EditorFrame.getEditorIsActive(frameId);

	};

	this.getEditorIsInUse = function (frameId) {
		var _EditorFrame = this.getEditorFrame(frameId);
		return _EditorFrame.getEditorIsInUse();
	};

	/*
	 // TODO: make better fn getEditorStateByID(id, table, editPage), returning an array with all status data and references if open
	 this.getEditorIfOpen = function (table, id, editPage) {
	 if(!(table && id && editPage)){
	 return false;
	 }

	 var usedEditors = this.getEditorsInUse(),
	 frameId,
	 editor;

	 for (frameId in usedEditors) {
	 editor = usedEditors[frameId];
	 if (editor.getEditorEditorTable() == table && editor.getEditorDocumentId() == id && editor.getEditorEditPageNr() == editPage) {
	 return editor.getContentEditor();
	 }
	 }
	 return false;
	 };
	 */

}
WE().layout.weEditorFrameController = new EditorFrameController();

//--------------------------------------------------------------------
// CLASS:
//   EditorFrameset
//
// DESCRIPTION:
//   Manages access to one editor-window.
//
// ARGUMENTS:
//   none
//
// RETURNS:
//   none
//--------------------------------------------------------------------
function EditorFrame(ref, elementId) {

	this.FrameId = elementId;

	this.EditorFrameWindow = top.bm_content_frame.window.frames[elementId];
	this.EditorFrameReference = ref; // not needed yet !

	this.EditorType = null;	// model|cockpit|none_webedition, etc

	this.EditorTransaction = null; // is set
	this.EditorDocumentId = ""; // is set
	this.EditorEditorTable = ""; // is set
	this.EditorIsLoading = true; // is set
	this.EditorIsHot = false;
	this.EditorEditPageNr = null;

	// seeMode - url, parameters
	this.EditorUrl = "";
	this.EditorDocumentParameters = "";

	this.EditorEditCmd = "";

	// checkboxes in Editor-Footer:
	this.EditorMakeNewDoc = false;
	this.EditorPublishWhenSave = false;
	this.EditorAutoRebuild = false;
	this.EditorMakeSameDoc = false;

	// wysiwyg in editors
	this.EditorDidSetHiddenText = false;

	// information for tabs
	this.EditorDocumentText = " &hellip; ";
	this.EditorDocumentPath = null;
	this.EditorContentType = ""; // is set

	this.EditorTable = null;
	this.EditorIsActive = false;
	this.EditorIsInUse = false;

	// reload needed?
	this.EditorReloadNeeded = false;
	this.EditorReloadAllNeeded = false;

	// exit_doc_question for this document
	// used in: closeDocument, closeEditorFrame, toggleFrames !
	this.EditorExitDocQuestionDialog = false;


	//----------------------------------------
	// functionality
	//----------------------------------------

	this.freeEditor = function () {

		this.setEmptyEditor();

		this.EditorType = null;	// model|cockpit, etc

		this.EditorTransaction = null;
		this.EditorDocumentId = "";
		this.EditorEditorTable = "";
		this.EditorIsLoading = true;
		this.EditorIsHot = false;

		this.EditorUrl = "";
		this.EditorDocumentParameters = "";
		this.EditorEditCmd = "";

		// checkboxes in Editor-Footer:
		this.EditorMakeNewDoc = false;
		this.EditorPublishWhenSave = false;
		this.EditorAutoRebuild = false;
		this.EditorMakeSameDoc = false;

		// wysiwyg in editors
		this.EditorDidSetHiddenText = false;

		// information for tabs
		this.EditorDocumentText = " &hellip; ";
		this.EditorDocumentPath = null;
		this.EditorContentType = "";


		this.EditorEditPageNr = null;
		this.EditorTable = null;
		this.EditorIsActive = false;
		this.EditorIsInUse = false;
	};

	this.initEditorFrameData = function (obj) {
		this.EditorIsInUse = true;
		if (obj) {
			for (var eigen in obj) {
				if (typeof (obj[eigen]) === "boolean") {
					this[eigen] = obj[eigen];
				} else {
					this[eigen] = obj[eigen];
				}
			}
			this.updateEditorTab();
		}
	};


	this.updateEditorTab = function () {
		this.EditorDocumentText = this.EditorDocumentText.replace(/</g, "&lt;");
		this.EditorDocumentText = this.EditorDocumentText.replace(/>/g, "&gt;");
		WE().layout.multiTabs.setText(this.FrameId, this.EditorDocumentText);
		WE().layout.multiTabs.setTitle(this.FrameId, this.EditorDocumentPath);
		if (this.EditorType === "model") {
			WE().layout.multiTabs.setId(this.FrameId, "ID: " + this.EditorDocumentId);
		} else {
			WE().layout.multiTabs.setId(this.FrameId, this.EditorDocumentText);
		}
		WE().layout.multiTabs.setModified(this.FrameId, this.EditorIsHot);
		WE().layout.multiTabs.setContentType(this.FrameId, this.EditorContentType);
		WE().layout.multiTabs.setLoading(this.FrameId, this.EditorIsLoading);
		WE().layout.multiTabs.setModified(this.FrameId, this.EditorIsHot);
	};

	//--------------------------------------------------------------------
	// FUNCTION:
	//   setEmptyEditor
	//
	// DESCRIPTION:
	//   if all editors are closed, the editor shows an emtpy page
	//
	// ARGUMENTS:
	//   none
	//
	// RETURNS:
	//   nothing
	//--------------------------------------------------------------------
	this.setEmptyEditor = function () {
		this.EditorFrameWindow.location = WE().consts.dirs.WEBEDITION_DIR + "html/blank_editor.html";
	};

	this.getEditorFrameWindow = function () {
		return this.EditorFrameWindow;
	};

	this.getDocumentReference = function () {
		return this.getEditorFrameWindow();
	};

	this.getFrameId = function () {
		return this.FrameId;
	};

	this.getEditorType = function () {
		return this.EditorType;
	};

	this.getEditorTransaction = function () {
		return this.EditorTransaction;
	};

	this.getEditorDocumentId = function () {
		return this.EditorDocumentId;
	};

	this.getEditorEditorTable = function () {
		return this.EditorEditorTable;
	};

	this.getEditorIsHot = function () {
		return this.EditorIsHot;
	};

	this.getEditorEditCmd = function () {
		return this.EditorEditCmd;
	};

	this.getEditorUrl = function () {
		return this.EditorUrl;
	};

	this.getEditorDocumentParameters = function () {
		return this.EditorDocumentParameters;
	};

	this.getEditorMakeNewDoc = function () {
		return this.EditorMakeNewDoc;
	};

	this.getEditorPublishWhenSave = function () {
		return this.EditorPublishWhenSave;
	};

	this.getEditorAutoRebuild = function () {
		return this.EditorAutoRebuild;
	};

	this.getEditorMakeSameDoc = function () {
		return this.EditorMakeSameDoc;
	};

	this.getEditorDidSetHiddenText = function () {
		return this.EditorDidSetHiddenText;
	};

	this.getEditorDocumentPath = function () {
		return this.EditorDocumentPath;
	};

	this.getEditorDocumentText = function () {
		return this.EditorDocumentText;
	};

	this.getEditorEditPageNr = function () {
		return this.EditorEditPageNr;
	};

	this.getEditorContentType = function () {
		return this.EditorContentType;
	};

	this.getEditorTable = function () {
		return this.EditorTable;
	};

	this.getEditorIsActive = function () {
		return this.EditorIsActive;
	};

	this.getEditorIsInUse = function () {
		return this.EditorIsInUse;
	};

	this.getEditorReloadNeeded = function () {
		return this.EditorReloadNeeded;
	};

	this.getEditorReloadAllNeeded = function () {
		return this.EditorReloadAllNeeded;
	};

	// setters
	this.setEditorMakeNewDoc = function (newVal) {
		this.EditorMakeNewDoc = newVal;
	};

	this.setEditorPublishWhenSave = function (newVal) {
		this.EditorPublishWhenSave = newVal;
	};

	this.setEditorAutoRebuild = function (newVal) {
		this.EditorAutoRebuild = newVal;
	};

	this.setEditorMakeSameDoc = function (newVal) {
		this.EditorMakeSameDoc = newVal;
	};

	this.setEditorDidSetHiddenText = function (newVal) {
		this.EditorDidSetHiddenText = newVal;
	};

	this.setEditorIsActive = function (newVal) {
		this.EditorIsActive = newVal;
		if (newVal) {
			var _theEditorFrame = this.getEditorFrameWindow();
			if (this.getEditorReloadAllNeeded()) {
				if (this.EditorType === "cockpit") {
					if (_theEditorFrame.saveSettings !== undefined) {
						_theEditorFrame.saveSettings();
					}
					var _href = _theEditorFrame.location.href;
					if (_href.charAt(_href.length - 1) === "#") {
						_href = _href.substr(0, _href.length - 1);
					}
					_theEditorFrame.location.href = _href;
					//_theEditorFrame.location.reload();
				} else {
					if (_theEditorFrame.frames.editHeader) {
						_theEditorFrame.frames.editHeader.location.reload();
					}
					if (this.getContentEditor()) {
						top.we_cmd("reload_editpage");
					}
					if (_theEditorFrame.frames.editFooter) {
						_theEditorFrame.frames.editFooter.location.reload();
					}
				}
				// reload all 3 frames
				this.setEditorReloadAllNeeded(false);
				this.setEditorReloadNeeded(false);
			} else if (this.getEditorReloadNeeded()) {
				if (this.EditorType === "cockpit") {
					_theEditorFrame.location.reload();
				} else {
					top.we_cmd("reload_editpage");
				}
				this.setEditorReloadNeeded(false);
			}
		}
	};

	this.setEditorEditPageNr = function (newVal) {
		this.EditorEditPageNr = newVal;
	};

	this.setEditorDocumentId = function (newVal) {
		this.EditorDocumentId = newVal.toString();
	};

	this.setEditorIsHot = function (newVal) {
		var _update = this.EditorIsHot !== newVal;
		this.EditorIsHot = newVal;
		if (_update) {
			this.updateEditorTab();
		}
	};

	this.setEditorIsLoading = function (newVal) {
		var _update = this.EditorIsLoading !== newVal;
		this.EditorIsLoading = newVal;
		if (_update) {
			this.updateEditorTab();
		}
	};

	this.setEditorReloadNeeded = function (newVal) {
		this.EditorReloadNeeded = newVal;
	};

	this.setEditorReloadAllNeeded = function (newVal) {
		this.EditorReloadAllNeeded = newVal;
	};

	this.switchToContentEditor = function (nr) {
		var iframe = this.getEditorFrameWindow().document.getElementsByTagName("IFRAME");
		iframe[nr].parentElement.style.display = 'block';
		iframe[(nr === 1 ? 2 : 1)].parentElement.style.display = 'none';
	};

	this.getContentEditorHeightForFrameNr = function (nr) {
		var iframes = this.getEditorFrameWindow().document.getElementsByTagName("IFRAME");
		//note embedded elements such as cockpit don't have a
		return (iframes[nr] && iframes[nr].parentElement.style.display === "none" ? "0" : "+1");

	};

	this.getContentEditor = function () {//iframes are frames in dom too
		if (this.getContentEditorHeightForFrameNr(1) === "0") {
			return this.getEditorFrameWindow().frames[2];
		}
		if (this.getContentEditorHeightForFrameNr(2) === "0") {
			return this.getEditorFrameWindow().frames[1];
		}
		return null;
	};

	this.getContentFrame = function () {
		return this.getEditorFrameWindow().frames[2];
	};

}
