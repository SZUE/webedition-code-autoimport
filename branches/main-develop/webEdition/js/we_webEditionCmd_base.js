/* global WE, we_cmd_modules,we_cmd, top,treeData */

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
'use strict';

we_cmd_modules.base = function (args, url, caller) {
	var postData, table, win, EditorFrame, frameId;
	switch (args[0]) {
		case "exit_doc_question":
			//next args editorFrameId, table, next_cmd
			WE().util.showConfirm(window, "", WE().consts.g_l.alert.exit_doc_question[args[2]], ["exit_doc_question_yes", args[1], args[2], args[3]], ["exit_doc_question_no", args[1], args[2], args[3]]);
			break;
		case "exit_doc_question_yes":
			EditorFrame = WE().layout.weEditorFrameController.getEditorFrame(args[1]);
			EditorFrame.getDocumentReference().frames.editFooter.we_save_document("WE().layout.weEditorFrameController.closeDocument('" + args[1] + "');" + (args[3] ? "top.setTimeout('" + args[3] + "', 1000);" : ""));
			break;
		case "exit_doc_question_no":
			EditorFrame = WE().layout.weEditorFrameController.getEditorFrame(args[1]);
			EditorFrame.setEditorIsHot(false);
			WE().layout.weEditorFrameController.closeDocument(args[1]);
			if (args[3]) {
				//FIXME: eval
				window.setTimeout(args[3], 1000);
			}
			break;
		case "eplugin_exit_doc" :
			if (top.plugin !== undefined && top.plugin.document.WePlugin !== undefined) {
				if (top.plugin.isInEditor(args[1])) {
					return window.confirm(WE().consts.g_l.main.eplugin_exit_doc);
				}
			}
			return true;
		case "editor_plugin_doc_count":
			if (top.plugin.document.WePlugin !== undefined) {
				return top.plugin.getDocCount();
			}
			return 0;
		case "setIconOfDocClass":
			WE().util.setIconOfDocClass(caller.document, args[1]);
			break;
		case 'updateMainTree':
			updateMainTree(args[1], args[2], args[3]);
			break;
		case "closeDeletedEntries":
			table = args[1];
			var ids = args[2];
			if (top.treeData && top.treeData.table === table) {
				for (var i = 0; i < ids.length; i++) {
					top.treeData.deleteEntry(ids[i]);
				}
				top.drawTree();
			}
			var usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();
			for (frameId in usedEditors) {
				if (table === usedEditors[frameId].getEditorEditorTable() && (ids.indexOf(usedEditors[frameId].getEditorDocumentId()) !== -1)) {
					usedEditors[frameId].setEditorIsHot(false);
					WE().layout.weEditorFrameController.closeDocument(frameId);
				}
			}
			break;
		case "loadVTab":
			var op = top.treeData.makeFoldersOpenString();
			window.parent.we_cmd("load", args[1], 0, op, top.treeData.table);
			break;
		case 'updateMenu':
			document.getElementById("nav").parentNode.innerHTML = args[1];
			break;
		case "exit_modules":
			WE().util.jsWindow.prototype.closeByName('edit_module');
			break;
		case "openUnpublishedObjects":
			we_cmd("tool_weSearch_edit", "", "", 7, 3);
			break;
		case "openUnpublishedPages":
			we_cmd("tool_weSearch_edit", "", "", 4, 3);
			break;
		case "reloadMainEditor":
			WE().layout.weEditorFrameController.getActiveDocumentReference().frames[2].reloadContent = true;
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(caller, url, "we_cateditor", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "openSidebar":
			WE().layout.sidebar.open("default");
			break;
		case "loadSidebarDocument":
			top.weSidebarContent.location.href = url;
			break;
		case "versions_preview":
			new (WE().util.jsWindow)(caller, url, "version_preview", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, false, true, false);
			break;
		case "versions_wizard":
			new (WE().util.jsWindow)(caller, url, "versions_wizard", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, false, true);
			break;
		case "versioning_log":
			new (WE().util.jsWindow)(caller, url, "versioning_log", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, false, true);
			break;
		case "delete_single_document_question":
			we_cmd_delete_single_document_question(url);
			break;
		case "delete_single_document":
			we_cmd_delete_single_document(url);
			break;
		case "do_delete":
			WE().util.we_sbmtFrm(window.load, url, document.getElementsByName("treeheader")[0]);
			break;
		case "move_single_document":
			WE().util.we_sbmtFrm(window.load, url, WE().layout.weEditorFrameController.getActiveDocumentReference().editFooter);
			break;
		case "do_move":
			WE().util.we_sbmtFrm(window.load, url, document.getElementsByName("treeheader")[0]);
			break;
		case "do_addToCollection":
			WE().util.we_sbmtFrm(window.load, url, document.getElementsByName("treeheader")[0]);
			break;
		case "change_passwd":
			new (WE().util.jsWindow)(caller, url, "we_change_passwd", WE().consts.size.dialog.tiny, WE().consts.size.dialog.smaller, true, false, true, false);
			break;
		case "update":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "liveUpdate/liveUpdate.php?active=update", "we_update_" + WE().session.sess_id, WE().consts.size.dialog.medium, WE().consts.size.dialog.small, true, true, true);
			break;
		case "upgrade":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "liveUpdate/liveUpdate.php?active=upgrade", "we_update_" + WE().session.sess_id, WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, true, true);
			break;
		case "languageinstallation":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "liveUpdate/liveUpdate.php?active=languages", "we_update_" + WE().session.sess_id, WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, true, true);
			break;
		case "del":
			we_cmd('delete', 1, args[2]);
			top.treeData.setState(top.treeData.tree_states.select);
			top.treeData.unselectNode();
			top.drawTree();
			break;
		case "mv":
			we_cmd('move', 1, args[2]);
			top.treeData.setState(treeData.tree_states.selectitem);
			top.treeData.unselectNode();
			top.drawTree();
			break;//add_to_collection
		case "tocollection":
			we_cmd('addToCollection', 1, args[2]);
			treeData.setState(treeData.tree_states.select);
			top.treeData.unselectNode();
			top.drawTree();
			break;
		case "changeLanguageRecursive":
		case "changeTriggerIDRecursive":
			top.we_repl(window.load, url);
			break;
		case "logout":
			top.we_repl(window.load, url);
			break;
		case "dologout":
			// before the command 'logout' is executed, ask if unsaved changes should be saved
			if (WE().layout.weEditorFrameController.doLogoutMultiEditor()) {
				WE().layout.regular_logout = true;
				we_cmd('logout');
			}
			break;
		case "exit_multi_doc_question":
			WE().util.showConfirm(caller, "", '<div>' + WE().consts.g_l.alert.exit_multi_doc_question + '<br /><br /><div style="height: 150px; overflow: auto;"><ul id="ulHotDocuments">' + getHotDocumentsString() + '</ul></div></div>', [
				"exit_multi_doc_question_yes", args[1]]);
			break;
		case "exit_multi_doc_question_yes":
			var allHotDocuments = WE().layout.weEditorFrameController.getEditorsInUse();
			for (frameId in allHotDocuments) {
				if (allHotDocuments[frameId].getEditorIsHot()) {
					allHotDocuments[frameId].setEditorIsHot(false);
				}
			}
			we_cmd(args[1]);
			break;
		case "load":
			if (WE().session.seemode) {
				break;
			}

			we_cmd("setTab", (args[1] !== undefined && args[1]) ? args[1] : WE().consts.tables.FILE_TABLE);
			/* falls through */
		case "loadFolder":
		case "closeFolder":
			loadCloseFolder(args);
			break;
		case "reload_editfooter":
			top.we_repl(WE().layout.weEditorFrameController.getActiveDocumentReference().frames.editFooter, url);
			break;
		case "reload_edit_header":
			top.we_repl(WE().layout.weEditorFrameController.getActiveDocumentReference().frames.editHeader, url);
			break;
		case "rebuild":
			new (WE().util.jsWindow)(caller, url, "rebuild", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, false, true);
			break;
		case "openPreferences":
			new (WE().util.jsWindow)(caller, url, "preferences", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "editCat":
			we_cmd("we_selector_category", 0, WE().consts.tables.CATEGORY_TABLE, "", "", "", "", "", 1);
			break;
		case "editThumbs":
			new (WE().util.jsWindow)(caller, url, "thumbnails", WE().consts.size.dialog.small, WE().consts.size.dialog.medium, true, true, true);
			break;
		case "editMetadataFields":
			new (WE().util.jsWindow)(caller, url, "metadatafields", WE().consts.size.dialog.small, WE().consts.size.dialog.medium, true, true, true);
			break;
		case "doctypes":
			new (WE().util.jsWindow)(caller, url, "doctypes", WE().consts.size.dialog.medium, WE().consts.size.dialog.medium, true, true, true);
			break;
		case "info":
			new (WE().util.jsWindow)(caller, url, "info", WE().consts.size.dialog.smaller, WE().consts.size.dialog.smaller, true, false, true);
			break;
		case "webEdition_online":
			new (WE().util.jsWindow)(caller, "http://www.webedition.org/", "webEditionOnline", WE().consts.size.dialog.fullScreen, WE().consts.size.dialog.fullScreen, true, true, true, true);
			break;
		case "info_modules":
			WE().util.jsWindow.prototype.focus('edit_module');
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=info";
			new (WE().util.jsWindow)(caller, url, "info", WE().consts.size.dialog.smaller, WE().consts.size.dialog.smaller, true, false, true);
			break;
		case "help_modules":
		case "help":
			url = "http://help.webedition.org/index.php?language=" + WE().session.lang.long;
			new (WE().util.jsWindow)(caller, url, "help", WE().consts.size.dialog.medium, WE().consts.size.dialog.small, true, false, true, true);
			break;
		case "help_forum":
			new (WE().util.jsWindow)(caller, "http://forum.webedition.org", "help_forum", WE().consts.size.dialog.medium, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "help_bugtracker":
			new (WE().util.jsWindow)(caller, "http://qa.webedition.org/tracker/", "help_bugtracker", WE().consts.size.dialog.medium, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "help_changelog":
			new (WE().util.jsWindow)(caller, "http://www.webedition.org/de/webedition-cms/versionshistorie/", "help_changelog", WE().consts.size.dialog.medium, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "we_customer_selector":
		case "we_selector_file":
			new (WE().util.jsWindow)(caller, url, "we_fileselector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(caller, url, "we_fileselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "we_selector_image":
		case "we_selector_document":
			new (WE().util.jsWindow)(caller, url, "we_fileselector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_fileupload_editor":
			new (WE().util.jsWindow)(caller, url, "we_fileupload_editor", WE().consts.size.dialog.small, WE().consts.size.dialog.big, true, true, true, true);
			break;
		case "setHot":
			WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);
			break;
		case "unsetHot":
			WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(false);
			break;
		case 'setCreateTemplate':
			document.we_form.CreateTemplate.checked = true;
			break;
		case "setTab":
			if (top.treeData !== undefined) {
				WE().layout.vtab.setActiveTab(args[1]);
				top.treeData.table = args[1];
			} else {
				window.setTimeout(we_cmd, 500, "setTab", args[1]);
			}
			break;
		case "revert_published_question":
			WE().util.showConfirm(caller, "", WE().consts.g_l.alert.revert_publish_question, ["revert_published"]);
			break;
		case "checkSameMaster":
			WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);
			if (args[1].currentID == args[2]) {
				WE().util.showMessage(WE().consts.g_l.alert.same_master_template, WE().consts.message.WE_MESSAGE_ERROR, window);
				caller.document.we_form.elements[args[1].JSIDName].value = '';
				window.opener.document.we_form.elements[args[1].JSTextName].value = '';
			}
			break;
		case "add_cat":
			url += "&we_cmd[1]=" + args[1].allIDs.join(",");
			doReloadCmd(args, url, true);
			break;
		case "copyDocumentSelect":
			url += "&we_cmd[1]=" + args[1].currentID;
			doReloadCmd(args, url, true);
			break;
		case "setDoReload":
			caller.document.we_form.elements.do.value = args[1];
			args[0] = 'reload_editpage';
			args[1] = '';
			doReloadCmd(args, WE().util.getWe_cmdArgsUrl(args), true);
			break;
		case "update_image":
		case "update_file":
		case "copyDocument":
		case "insert_entry_at_list":
		case "delete_list":
		case "down_entry_at_list":
		case "up_entry_at_list":
		case "down_link_at_list":
		case "up_link_at_list":
		case "add_entry_to_list":
		case "add_link_to_linklist":
		case "change_link":
		case "change_linklist":
		case "delete_linklist":
		case "insert_link_at_linklist":
		case "change_doc_type":
		case "doctype_changed":
		case "remove_image":
		case "delete_link":
		case "delete_cat":
		case "delete_all_cats":
		case "schedule_add":
		case "schedule_del":
		case "schedule_add_schedcat":
		case "schedule_delete_all_schedcats":
		case "schedule_delete_schedcat":
		case "template_changed":
		case "add_navi":
		case "delete_navi":
		case "delete_all_navi":
		case "reload_hot_editpage":
			doReloadCmd(args, url, true);
			WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);
			break;
		case "reload_editpage":
		case "wrap_on_off":
		case "restore_defaults":
		case "do_add_thumbnails":
		case "del_thumb":
		case "resizeImage":
		case "rotateImage":
		case "doImage_convertGIF":
		case "doImage_convertPNG":
		case "doImage_convertJPEG":
		case "doImage_crop":
		case "revert_published":
			doReloadCmd(args, url, false);
			break;
		case "revert_published":
			doReloadCmd(args, url, false);
			EditorFrame = WE().layout.weEditorFrameController.getActiveEditorFrame();
			EditorFrame.setEditorIsHot(false);
			EditorFrame.getDocumentReference().frames.editFooter.location.reload();

			break;
		case "edit_document_with_parameters":
		case "edit_document":
			wecmd_editDocument(args, url);
			break;
		case "seem_open_extern_document":
			window.open(args[1], '_blank');
			break;
		case "open_extern_document":
		case "new_document":
			we_cmd_new_document(url);
			break;
		case "close_document":
			var _currentEditor;
			if (args[1]) { // close special tab
				WE().layout.weEditorFrameController.closeDocument(args[1]);
			} else if ((_currentEditor = WE().layout.weEditorFrameController.getActiveEditorFrame())) {
				// close active tab
				WE().layout.weEditorFrameController.closeDocument(_currentEditor.getFrameId());
			}
			break;
		case "close_all_documents":
			WE().layout.weEditorFrameController.closeAllDocuments();
			break;
		case "close_all_but_active_document":
			var activeId = null;
			if (args[1]) {
				activeId = args[1];
			}
			WE().layout.weEditorFrameController.closeAllButActiveDocument(activeId);
			break;
		case "open_url_in_editor":
			top.we_repl(window.load, url);
			break;
		case "publish":
		case "unpublish":
			doPublish(url, args[1]);
			break;
		case "publishWhenSave":
			WE().layout.weEditorFrameController.getActiveEditorFrame().getEditorPublishWhenSave();
			break;
		case "save_document":
			EditorFrame = WE().layout.weEditorFrameController.getActiveEditorFrame();
			if (EditorFrame && EditorFrame.getEditorFrameWindow().frames && EditorFrame.getEditorFrameWindow().frames[1]) {
				EditorFrame.getEditorFrameWindow().frames[1].focus();
			}

			if (!args[1]) {
				args[1] = EditorFrame.getEditorTransaction();
			}

			doSave(url, args[1]);
			break;
		case "we_selector_delete":
			new (WE().util.jsWindow)(caller, url, "we_del_selector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "browse":
			WE().layout.openBrowser();
			break;
		case "home":
			if (top.treeData) {
				top.treeData.unselectNode();
			}
			WE().layout.weEditorFrameController.openDocument('', '', '', 'open_cockpit');
			break;
		case "browse_server":
			new (WE().util.jsWindow)(caller, url, "browse_server", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, false, true);
			break;
		case "make_backup":
			new (WE().util.jsWindow)(caller, url, "export_backup", WE().consts.size.dialog.medium, WE().consts.size.dialog.small, true, true, true);
			break;
		case "recover_backup":
			new (WE().util.jsWindow)(caller, url, "recover_backup", WE().consts.size.dialog.medium, WE().consts.size.dialog.small, true, true, true);
			break;
		case "import":
			new (WE().util.jsWindow)(caller, url, "import", WE().consts.size.dialog.small, WE().consts.size.dialog.medium, true, false, true);
			break;
		case "import_files":
			new (WE().util.jsWindow)(caller, url, "import_files", WE().consts.size.dialog.small, WE().consts.size.dialog.medium, true, false, true);
			break;
		case "export":
			new (WE().util.jsWindow)(caller, url, "export", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, false, true);
			break;
		case "copyWeDocumentCustomerFilter":
			new (WE().util.jsWindow)(caller, url, "copyWeDocumentCustomerFilter", WE().consts.size.dialog.smaller, WE().consts.size.dialog.tiny, true, true, true);
			break;
		case 'copyFolderCheck':
			//parents element start from 4
			if (args.indexOf(args[1].currentID, 3) > -1) {
				WE().util.showMessage(WE().consts.g_l.alert.copy_folder_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else {
				we_cmd('copyFolder', args[1].currentID, args[2], 1, args[3]);
			}
			break;
		case "copyFolder":
			new (WE().util.jsWindow)(caller, url, "copyfolder", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true);
			break;
		case "del_frag":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=delFrag&currentID=" + args[1], "we_del", WE().consts.size.dialog.small, WE().consts.size.dialog.tiny, true, true, true);
			break;
		case "open_wysiwyg_window":
			open_wysiwyg_window(args, caller);
			break;
		case "start_multi_editor":
			top.we_repl(window.load, url);
			break;
		case "customValidationService":
			new (WE().util.jsWindow)(caller, url, "we_customizeValidation", WE().consts.size.dialog.medium, WE().consts.size.dialog.medium, true, false, true);
			break;
		case "edit_home":
			if (args[1] === 'add') {
				window.load.location = WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=widget_cmd&we_cmd[1]=' + args[1] + '&we_cmd[2]=' + args[2] + '&we_cmd[3]=' + args[3];
			}
			break;
		case "edit_navi":
			new (WE().util.jsWindow)(caller, url, "we_navieditor", WE().consts.size.dialog.smaller, WE().consts.size.dialog.smaller, true, true, true, true);
			break;
		case "initPlugin":
			WE().layout.weplugin_wait = new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "editors/content/eplugin/weplugin_wait.php?callback=" + args[1], "weplugin_wait", WE().consts.size.dialog.tiny, WE().consts.size.dialog.tiny, true, false, true);
			break;
		case "edit_settings_editor":
			if (top.plugin.editSettings) {
				top.plugin.editSettings();
			} else {
				we_cmd("initPlugin", "top.plugin.editSettings()");
			}
			break;
		case "sysinfo":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=sysinfo", "we_sysinfo", WE().consts.size.dialog.medium, WE().consts.size.dialog.small, true, false, true);
			break;
		case "showerrorlog":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=showerrorlog", "we_errorlog", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, false, true);
			break;
		case "view_backuplog":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=backupLog", "we_backuplog", WE().consts.size.dialog.medium, WE().consts.size.dialog.small, true, false, true);
			break;
		case "show_message_console":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=messageConsole", "we_jsMessageConsole", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, false, true, false);
			break;
		case "remove_from_editor_plugin":
			if (args[1] && top.plugin && top.plugin.remove) {
				top.plugin.remove(args[1]);
			}
			break;
		case "doClickWithParameters":
			WE().layout.weEditorFrameController.openDocument(args[1], args[2], args[3], '', '', '', '', '', args[4]);
			break;
		case "new":
			if (WE().session.seemode) {
				WE().layout.weEditorFrameController.openDocument(args[1], args[2], args[3], "", args[4], "", args[5]);
				break;
			}
			top.treeData.unselectNode();
			if (args[5] !== undefined) {
				WE().layout.weEditorFrameController.openDocument(args[1], args[2], args[3], "", args[4], "", args[5]);
			} else {
				WE().layout.weEditorFrameController.openDocument(args[1], args[2], args[3], "", args[4]);
			}
			break;
		case "exit_delete":
		case "exit_move":
		case "exit_addToCollection":
			WE().session.deleteMode = false;
			if (!WE().session.seemode) {
				top.treeData.setState(top.treeData.tree_states.edit);
				drawTree();
				var cl = window.document.getElementById("bm_treeheaderDiv").classList;
				cl.remove('deleteSelector');
				cl.remove('moveSelector');
				cl.remove('collectionSelector');
				cl = window.document.getElementById("treetable").classList;
				cl.remove('deleteSelector');
				cl.remove('moveSelector');
				cl.remove('collectionSelector');
				WE().layout.tree.setWidth(WE().layout.tree.widthBeforeDeleteMode);
				WE().layout.sidebar.setWidth(WE().layout.sidebar.widthBeforeDeleteMode);
			}
			break;
		case "delete":
			we_cmd_delete(args, url);
			break;
		case "move":
			we_cmd_move(args, url);
			break;
		case "addToCollection":
			addToCollection(args, url);
			break;
		case "reset_home":
			var _currEditor = WE().layout.weEditorFrameController.getActiveEditorFrame();
			if (_currEditor && _currEditor.getEditorType() === "cockpit") {
				WE().util.showConfirm(caller, "", WE().consts.g_l.cockpit.reset_settings, ["reset_home_do"]);
			} else {
				WE().util.showMessage(WE().consts.g_l.cockpit.not_activated, WE().consts.message.WE_MESSAGE_NOTICE, window);
			}
			break;
		case "reset_home_do":
//FIXME: currently this doesn't work
			WE().layout.weEditorFrameController.getActiveDocumentReference().location = WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=widget_cmd&we_cmd[1]' + args[0];
			if ((top.treeData !== undefined) && top.treeData) {
				top.treeData.unselectNode();
			}
			break;
		case "new_widget":
			if (WE().layout.weEditorFrameController.getActiveDocumentReference() && WE().layout.weEditorFrameController.getActiveDocumentReference().quickstart) {
				WE().layout.weEditorFrameController.getActiveDocumentReference().createWidget(args[1], 1, 1);
			} else {
				WE().util.showMessage(WE().consts.g_l.cockpit.not_activated, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
			break;
		case "open_document":
			we_cmd("load", WE().consts.tables.FILE_TABLE);
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=we_selector_document&we_cmd[2]=" + WE().consts.tables.FILE_TABLE + "&we_cmd[5]=" + encodeURIComponent("WE().layout.weEditorFrameController.openDocument(table,top.fileSelect.data.currentID,top.fileSelect.data.currentType)") + "&we_cmd[9]=1";
			new (WE().util.jsWindow)(caller, url, "we_dirChooser", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "open_collection":
			we_cmd("load", WE().consts.tables.VFILE_TABLE);
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=we_selector_document&we_cmd[2]=" + WE().consts.tables.VFILE_TABLE + "&we_cmd[5]=" + encodeURIComponent("WE().layout.weEditorFrameController.openDocument(table,top.fileSelect.data.currentID,top.fileSelect.data.currentType)") + "&we_cmd[9]=1";
			new (WE().util.jsWindow)(caller, url, "we_dirChooser", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "edit_new_collection":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=editNewCollection&we_cmd[1]=" + args[1] + "&we_cmd[2]=" + args[2] + "&fixedpid=" + args[3] + "&fixedremtable=" + args[4];
			new (WE().util.jsWindow)(caller, url, "weNewCollection", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case 'collection_insertFiles':
			collection_insertFiles(args);
			break;
		case 'collection_insertFiles_rpc':
			// TODO: make some tests and return with alert when not ok
			postData = '&we_cmd[ids]=' + encodeURIComponent(args[1] ? args[1] : '') +
				'&we_cmd[collection]=' + encodeURIComponent(args[2] ? args[2] : 0) +
				'&we_cmd[transaction]=' + encodeURIComponent(args[3] ? args[3] : '') +
				'&we_cmd[full]=0' +
				'&we_cmd[position]=' + encodeURIComponent(args[4] ? args[4] : -1) +
				'&we_cmd[recursive]=' + encodeURIComponent(args[5] ? args[4] : 0);
			WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR + "rpc.php?cmd=InsertValidItemsByID&cns=collection", postData);

			break;
		case "help_documentation":
			new (WE().util.jsWindow)(caller, "http://documentation.webedition.org/", "help_documentation", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;

		case "help_tagreference":
			new (WE().util.jsWindow)(caller, "http://tags.webedition.org/de/", "help_tagreference", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "open_tagreference":
			var docupath = "http://tags.webedition.org/de/" + args[1];
			new (WE().util.jsWindow)(caller, docupath, "we_tagreference", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true);
			break;
		case "open_template":
			we_cmd("load", WE().consts.tables.TEMPLATES_TABLE);
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=we_selector_document&we_cmd[2]=" + WE().consts.tables.TEMPLATES_TABLE + "&we_cmd[5]=" + encodeURIComponent("WE().layout.weEditorFrameController.openDocument(table,top.fileSelect.data.currentID,top.fileSelect.data.currentType)") + "&we_cmd[8]=" + WE().consts.contentTypes.TEMPLATE + "&we_cmd[9]=1";
			new (WE().util.jsWindow)(caller, url, "we_dirChooser", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "switch_edit_page":
			switchEditPage(args, url);
			break;
		case "insert_variant":
		case "move_variant_up":
		case "move_variant_down":
		case "remove_variant":
			url += "#f" + (parseInt(args[1]) - 1);
			WE().util.we_sbmtFrm(WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1], url);
			break;
		case 'preview_variant':
			url += "#f" + (parseInt(args[1]) - 1);
			var prevWin = new (WE().util.jsWindow)(caller, url, "previewVariation", WE().consts.size.dialog.fullScreen, WE().consts.size.dialog.fullScreen, true, true, true, true);
			WE().util.we_sbmtFrm(prevWin.wind, url);
			break;
		case 'cloneDocument':
			var act = WE().layout.weEditorFrameController.getActiveEditorFrame();
			if (!act.EditorEditorTable || !act.EditorDocumentId) {
				break;
			}
			top.we_cmd("new", act.EditorEditorTable, "", act.EditorContentType);
			top.we_cmd("copyDocument", act.EditorDocumentId);
			break;
		case 'new_webEditionPage':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.WEDOCUMENT);
			break;
		case 'new_image':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.IMAGE);
			break;
		case 'new_html_page':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.HTML);
			break;
		case 'new_flash_movie':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.FLASH);
			break;
		case 'new_video_movie':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.VIDEO);
			break;
		case 'new_audio_audio':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.AUDIO);
			break;
		case 'new_javascript':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.JS);
			break;
		case 'new_text_plain':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.TEXT);
			break;
		case 'new_text_xml':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.XML);
			break;
		case 'new_text_htaccess':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.HTACCESS);
			break;
		case 'new_css_stylesheet':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.CSS);
			break;
		case 'new_binary_document':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.APPLICATION);
			break;
		case 'new_template':
			top.we_cmd("new", WE().consts.tables.TEMPLATES_TABLE, "", WE().consts.contentTypes.TEMPLATE);
			break;
		case 'new_document_folder':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.FOLDER);
			break;
		case 'new_template_folder':
			top.we_cmd("new", WE().consts.tables.TEMPLATES_TABLE, "", WE().consts.contentTypes.FOLDER);
			break;
		case 'new_collection_folder':
			top.we_cmd("new", WE().consts.tables.VFILE_TABLE, "", WE().consts.contentTypes.FOLDER);
			break;
		case 'new_collection':
			top.we_cmd("new", WE().consts.tables.VFILE_TABLE, "", WE().consts.contentTypes.COLLECTION);
			break;
		case 'delete_documents':
			top.we_cmd("del", 1, WE().consts.tables.FILE_TABLE);
			break;
		case 'delete_templates':
			top.we_cmd("del", 1, WE().consts.tables.TEMPLATES_TABLE);
			break;
		case 'delete_collections':
			top.we_cmd("del", 1, WE().consts.tables.VFILE_TABLE);
			break;
		case 'move_documents':
			top.we_cmd("mv", 1, WE().consts.tables.FILE_TABLE);
			break;
		case 'move_templates':
			top.we_cmd("mv", 1, WE().consts.tables.TEMPLATES_TABLE);
			break;
		case 'add_documents_to_collection':
			top.we_cmd("tocollection", 1, WE().consts.tables.FILE_TABLE);
			break;
		case 'add_objectfiles_to_collection':
			top.we_cmd("tocollection", 1, WE().consts.tables.OBJECT_FILES_TABLE);
			break;
		case 'new_dtPage':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.WEDOCUMENT, args[1]);
			break;
		case 'new_ClObjectFile':
			top.we_cmd("new", WE().consts.tables.OBJECT_FILES_TABLE, "", WE().consts.contentTypes.OBJECT_FILE, args[1]);
			break;
		case 'we_selector_delete':
			top.we_cmd('we_selector_delete', '', -1, '', '', '', '', '', '', 1);
			break;
		case 'doExtClick':
			WE().util.showConfirm(caller, "", WE().consts.g_l.alert.ext_doc_selected, ['doExtClick_yes', args[1]]);
			break;
		case 'doExtClick_yes':
			top.info(' ');
			doExtClick(args[1]);
			break;
		case 'tag_weimg_insertImage':
			table = args[6] ? args[6] : WE().consts.tables.FILE_TABLE;
			var tab = args[7] ? args[7] : 1;
			var editorFrame = WE().layout.weEditorFrameController.getEditorFrameByExactParams(args[4], table, tab, args[5]);

			if (editorFrame) {
				editorFrame.getContentEditor().setScrollTo();
				editorFrame.setEditorIsHot(true);
				editorFrame.getContentEditor().document.we_form.elements[args[3]].value = args[1].id ? args[1].id : args[1].currentID;
				if (editorFrame.getEditorIsActive()) {
					we_cmd('reload_editpage', args[3], 'change_image');
				} else {
					editorFrame.setEditorReloadNeeded(true);
				}
			} else {
				var verifiedTransaction = WE().layout.weEditorFrameController.getEditorTransactionByIdTable(args[4], table);
				we_cmd('wedoc_setPropertyOrElement_rpc', {id: args[4], table: table, transaction: verifiedTransaction},
					{name: args[2], type: 'img', key: 'bdid', value: parseInt(args[1].id)});
			}
			break;
		case 'wedoc_setPropertyOrElement_rpc':
			if (!args[1] || !args[2] || !args[1].id || !args[1].table || !args[2].name) {
				return;
			}

			postData = '&we_cmd[id]=' + encodeURIComponent(args[1].id) +
				'&we_cmd[table]=' + encodeURIComponent(args[1].table) +
				'&we_cmd[transaction]=' + encodeURIComponent(args[1].transaction ? args[1].transaction : '') +
				'&we_cmd[name]=' + encodeURIComponent(args[2].name) +
				'&we_cmd[type]=' + encodeURIComponent(args[2].type ? args[2].type : '') +
				'&we_cmd[key]=' + encodeURIComponent(args[2].key ? args[2].key : 'dat') +
				'&we_cmd[value]=' + encodeURIComponent(args[2].value ? args[2].value : '');

			WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR + "rpc.php?cmd=SetPropertyOrElement&cns=document" + postData);
			break;
		case "suggest_writeBack":
			WE().layout.weSuggest.writebackExternalSelection(caller, args[1], args[2]);
			break;
		case "check_radio_option":
			// to be callable from selectors we skip args[1]
			this.we_form.elements[args[2]][args[3]].checked = true;
			if (args[4]) {
				this.we_cmd('setHot');
			}
			break;
		case "multiedit_addItem":
			switch (args[2]) {
				case 'customer':
					win = this;
					win.addToMultiEdit(win[args[3]], args[1].allTexts, args[1].allIDs);
					win.we_cmd('setHot');
					break;
				case 'category':
					break;
			}
			break;
		case "multiedit_delAll":
			switch (args[1]) {
				case 'customer':
					win = this;
					win.removeFromMultiEdit(win[args[2]]);
					win.we_cmd('setHot');
					break;
				case 'category':
					break;
			}
			break;
		case "toggle_checkbox_with_hidden":
			// to be callable from selectors we skip args[1]
			this.we_form.elements[args[2]].value = args[3];
			this.we_form.elements['check_' + args[2]].checked = args[3];
			if (args[4]) {
				this.we_cmd('setHot');
			}
			break;
		case 'open_cockpit':
			top.we_showInNewTab(args, url);
			break;
		default:
			//WE().t_e('no command matched to request', args[0]);
			return false;
	}
	return true;
};

function we_cmd_delete_single_document_question(url) {
	var ctrl = WE().layout.weEditorFrameController;
	var cType = ctrl.getActiveEditorFrame().getEditorContentType();
	var eTable = ctrl.getActiveEditorFrame().getEditorEditorTable();
	var path = ctrl.getActiveEditorFrame().getEditorDocumentPath();

	if (ctrl.getActiveDocumentReference()) {
		if (!WE().util.hasPermDelete(eTable, (cType === WE().consts.contentTypes.FOLDER))) {
			WE().util.showMessage(WE().consts.g_l.main.no_perms_action, WE().consts.message.WE_MESSAGE_ERROR, window);
		} else if (window.confirm(WE().consts.g_l.main.delete_single_confirm_delete + path)) {
			var url2 = url.replace(/we_cmd\[0\]=delete_single_document_question/g, "we_cmd[0]=delete_single_document");
			WE().util.we_sbmtFrm(window.load, url2 + "&we_cmd[2]=" + ctrl.getActiveEditorFrame().getEditorEditorTable(), ctrl.getActiveDocumentReference().frames.editFooter);
		}
	} else {
		WE().util.showMessage(WE().consts.g_l.main.no_document_opened, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}

function loadCloseFolder(args) {
	WE().util.rpc(WE().util.getWe_cmdArgsUrl(args, WE().consts.dirs.WEBEDITION_DIR + 'rpc.php?cmd=LoadMainTree&'), null, function (weResponse) {
		if (weResponse && weResponse.Success) {
			if (weResponse.DataArray.treeName) {
				top.document.getElementById("treeName").innerHTML = weResponse.DataArray.treeName;
			}
			if (weResponse.DataArray.items) {
				if (!weResponse.DataArray.parentFolder) {
					top.treeData.clear();
					top.treeData.add(top.Node.prototype.rootEntry(0, 'root', 'root', weResponse.DataArray.offset));
				}
				for (var i = 0; i < weResponse.DataArray.items.length; i++) {
					if (!weResponse.DataArray.parentFolder || top.treeData.indexOfEntry(weResponse.DataArray.items[i].id) < 0) {
						top.treeData.add(new top.Node(weResponse.DataArray.items[i]));
					}
				}
			}
			top.drawTree();
		}
		top.scrollToY();
	});
}

function we_cmd_delete_single_document(url) {
	var ctrl = WE().layout.weEditorFrameController;
	var cType = ctrl.getActiveEditorFrame().getEditorContentType();
	var eTable = ctrl.getActiveEditorFrame().getEditorEditorTable();

	if (ctrl.getActiveDocumentReference()) {
		if (!WE().util.hasPermDelete(eTable, (cType === WE().consts.contentTypes.FOLDER))) {
			WE().util.showMessage(WE().consts.g_l.main.no_perms_action, WE().consts.message.WE_MESSAGE_ERROR, window);
		} else {
			WE().util.we_sbmtFrm(window.load, url + "&we_cmd[2]=" + ctrl.getActiveEditorFrame().getEditorEditorTable(), ctrl.getActiveDocumentReference().editFooter);
		}
	} else {
		WE().util.showMessage(WE().consts.g_l.main.no_document_opened, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}

function doReloadCmd(args, url, hot) {
	if (hot) {
		// set Editor hot
		var _EditorFrame = WE().layout.weEditorFrameController.getActiveEditorFrame();
		_EditorFrame.setEditorIsHot(true);
	}
	if (top.setScrollTo) {
		window.setScrollTo();
	}
	// get editor root frame of active tab
	var _currentEditorRootFrame = WE().layout.weEditorFrameController.getActiveDocumentReference();
	// get visible frame for displaying editor page
	var _visibleEditorFrame = WE().layout.weEditorFrameController.getVisibleEditorFrame();

	// focus visible editor frame
	if (_visibleEditorFrame) {
		_visibleEditorFrame.focus();
	}

	url += getActiveEditors();

	// if cmd equals "reload_editpage" and there are parameters, attach them to the url
	if (args[0] === "reload_editpage" || args[0] === "reload_hot_editpage") {
		url += (_currentEditorRootFrame.parameters ? _currentEditorRootFrame.parameters : '') +
			(args[1] ? '#f' + args[1] : '');
	} else if (args[0] === "remove_image" && args[2]) {
		url += '#f' + args[2];
	}

	if (_currentEditorRootFrame) {
		if (!WE().util.we_sbmtFrm(_visibleEditorFrame, url, _visibleEditorFrame)) {
			if (args[0] !== "update_image") {
				// add we_transaction, if not set
				if (!args[2]) {
					args[2] = WE().layout.weEditorFrameController.getActiveEditorFrame().getEditorTransaction();
				}
				url += "&we_transaction=" + args[2];
			}
			top.we_repl(_visibleEditorFrame, url);
		}
	}
}

function wecmd_editDocument(args, url) {
	try {
		if (top.treeData) {
			top.treeData.unselectNode();
			if (args[1]) {
				top.treeData.selection_table = args[1];
			}
			if (args[2]) {
				top.treeData.selection = args[2];
			}
			if (top.treeData.selection_table === top.treeData.table) {
				top.treeData.selectNode(treeData.selection);
			}
		}
	} catch (e) {
	}
	url += getActiveEditors();

	var ctrl = WE().layout.weEditorFrameController;
	var nextWindow, nextContent;
	if ((nextWindow = ctrl.getFreeWindow())) {
		nextContent = nextWindow.getDocumentReference();
		// activate tab and set state to loading
		WE().layout.multiTabs.addTab(nextWindow.getFrameId(), nextWindow.getFrameId(), nextWindow.getFrameId());
		// use Editor Frame
		nextWindow.initEditorFrameData(
			{
				EditorType: "model",
				EditorEditorTable: args[1],
				EditorDocumentId: args[2],
				EditorContentType: args[3]
			}
		);
		// set Window Active and show it
		ctrl.setActiveEditorFrame(nextWindow.FrameId);
		ctrl.toggleFrames();
		if (nextContent.frames && nextContent.frames[1]) {
			if (!WE().util.we_sbmtFrm(nextContent, url)) {
				top.we_repl(nextContent, url + "&frameId=" + nextWindow.getFrameId());
			}
		} else {
			top.we_repl(nextContent, url + "&frameId=" + nextWindow.getFrameId());
		}
	} else {
		WE().util.showMessage(WE().consts.g_l.main.no_editor_left, WE().consts.message.WE_MESSAGE_ERROR);
	}
}

function we_cmd_new_document(url) {
	var ctrl = WE().layout.weEditorFrameController,
		nextWindow, nextContent;
	if ((nextWindow = ctrl.getFreeWindow())) {
		nextContent = nextWindow.getDocumentReference();
		// activate tab and set it status loading ...
		WE().layout.multiTabs.addTab(nextWindow.getFrameId(), nextWindow.getFrameId(), nextWindow.getFrameId());
		nextWindow.updateEditorTab();
		// set Window Active and show it
		ctrl.setActiveEditorFrame(nextWindow.getFrameId());
		ctrl.toggleFrames();
		// load new document editor
		top.we_repl(nextContent, url + "&frameId=" + nextWindow.getFrameId());
	} else {
		WE().util.showMessage(WE().consts.g_l.main.no_editor_left, WE().consts.message.WE_MESSAGE_ERROR);
	}
}

function open_wysiwyg_window(args, caller) {
	if (WE().layout.weEditorFrameController.getActiveDocumentReference()) {
		WE().layout.weEditorFrameController.getActiveDocumentReference().openedWithWE = false;
	}

	var wyw = args[2];
	wyw = Math.max((wyw ? wyw : 0), 400);
	var wyh = args[3];
	wyh = Math.max((wyh ? wyh : 0), 300);
	if (window.screen) {
		var screen_height = ((screen.height - 50) > screen.availHeight) ? screen.height - 50 : screen.availHeight;
		screen_height = screen_height - 100;
		var screen_width = screen.availWidth - 10;
		wyw = Math.min(screen_width, wyw);
		wyh = Math.min(screen_height, wyh);
	}

	var url = WE().consts.dirs.WEBEDITION_DIR + 'we_cmd_frontend.php?we_cmd[0]=open_wysiwyg_window&we_dialog_args[dialogProperties]=' + args[1];
	new (WE().util.jsWindow)(caller, url, "we_wysiwygWin", Math.max(220, wyw + (document.all ? 0 : ((navigator.userAgent.toLowerCase().indexOf('safari') > -1) ? 20 : 4))), Math.max(100, wyh + 60), true, false, true);
}

function addToCollection(args, url) {
	if (WE().session.seemode) {
		//
	} else {
		if (WE().session.deleteMode != args[1]) {
			WE().session.deleteMode = args[1];
		}
		if (!WE().session.deleteMode && top.treeData.state == top.treeData.tree_states.select) {
			top.treeData.setState(top.treeData.tree_states.edit);
			drawTree();
		}
		window.document.getElementById("bm_treeheaderDiv").classList.add('collectionSelector');
		window.document.getElementById("treetable").classList.add('collectionSelector');
		WE().layout.tree.toggle(true);
		var width = WE().layout.tree.getWidth();
		WE().layout.tree.widthBeforeDeleteMode = width;
		if (width < WE().consts.size.tree.moveWidth) {
			WE().layout.tree.setWidth(WE().consts.size.tree.moveWidth);
		}
		WE().layout.tree.storeWidth(WE().layout.tree.widthBeforeDeleteMode);

		WE().layout.sidebar.widthBeforeDeleteMode = WE().layout.sidebar.getWidth();

		if (args[2] != 1) {
			top.we_repl(document.getElementsByName("treeheader")[0], url);
		}
	}
}

function updateMainTree(select, attribs, adv) {
	if (!top.treeData || top.treeData.table !== attribs.table) {
		return;
	}
	if (select) {
		top.treeData.selection_table = attribs.table;
		top.treeData.selection = attribs.id;
	} else {
		top.treeData.unselectNode();
	}

	if (top.treeData[top.treeData.indexOfEntry(attribs.parentid)]) {
		/*var visible = (top.treeData.indexOfEntry(attribs.parentid) !== -1 ?
		 top.treeData[top.treeData.indexOfEntry(attribs.parentid)].open :
		 0);*/
		if (top.treeData.indexOfEntry(attribs.id) !== -1) {
			top.treeData.updateEntry(attribs);
		} else {
			top.treeData.addSort(new top.Node(objectAssign(attribs, adv)));
		}
		top.drawTree();
	} else if (top.treeData.indexOfEntry(attribs.id) !== -1) {
		top.treeData.deleteEntry(attribs.id);
	}
}

function getHotDocumentsString() {
	var allHotDocuments = WE().layout.weEditorFrameController.getEditorsInUse();
	var ct, ulCtElem, i;
	var ret = "";
	var hotDocumentsOfCt = {};
	for (var frameId in allHotDocuments) {
		ct = allHotDocuments[frameId].getEditorContentType();
		if (!hotDocumentsOfCt[ct]) {
			hotDocumentsOfCt[ct] = [];
		}
		hotDocumentsOfCt[ct].push(allHotDocuments[frameId]);
	}

	for (ct in hotDocumentsOfCt) {
		ulCtElem = "";

		for (i = 0; i < hotDocumentsOfCt[ct].length; i++) {
			ulCtElem += "<li>" + (hotDocumentsOfCt[ct][i].getEditorDocumentText() ?
				hotDocumentsOfCt[ct][i].getEditorDocumentPath() :
				"<em>" + WE().consts.g_l.main.untitled + "</em>") + "</li>";
		}

		ret += "<li>" + WE().consts.g_l.contentTypes[ct] + "<ul>" + ulCtElem + "</ul></li>";
	}
	return ret;
}

function collection_insertFiles(args) {
	if (args[1] === undefined || args[2] === undefined) {
		return;
	}

	var collection = parseInt(args[2]);
	var ids = (args[1].success !== undefined ? args[1].success : (args[1].currentID !== undefined ? [
		args[1].currentID] : args[1]));

	if (collection && ids) {
		var usedEditors = WE().layout.weEditorFrameController.getEditorsInUse(),
			editor = null,
			index = args[3] !== undefined ? args[3] : -1,
			recursive = args[5] !== undefined ? args[5] : false,
			transaction, frameId, candidate;

		for (frameId in usedEditors) {
			candidate = usedEditors[frameId];
			if (candidate.getEditorEditorTable() === WE().consts.tables.VFILE_TABLE && parseInt(candidate.getEditorDocumentId()) === collection) {
				if (candidate.getEditorEditPageNr() == 1) {
					editor = candidate;
				} else {
					transaction = candidate.getEditorTransaction();
				}
				break;
			}
		}

		if (editor) {
			// FIXME: we need a consize distinction between index and position
			//var index = editor.getContentEditor().weCollectionEdit.getItemId(editor.getContentEditor().document.getElementById('collectionItem_staticIndex_' + index))
			editor.getContentEditor().weCollectionEdit.callForValidItemsAndInsert(index, ids.join(), 'bla', recursive, true);
		} else {
			var position = args[4] !== undefined ? args[4] : index;
			we_cmd('collection_insertFiles_rpc', ids, collection, transaction, position, recursive);
		}
	}
}

function we_cmd_move(args, url) {
	if (WE().session.seemode) {
		if (WE().session.deleteMode != args[1]) {
			WE().session.deleteMode = args[1];
		}
		if (args[2] != 1) {
			top.we_repl(WE().layout.weEditorFrameController.getActiveDocumentReference(), url);
		}
	} else {
		if (WE().session.deleteMode != args[1]) {
			WE().session.deleteMode = args[1];
		}
		if (!WE().session.deleteMode && top.treeData.state == top.treeData.tree_states.selectitem) {
			top.treeData.setState(top.treeData.tree_states.edit);
			drawTree();
		}
		window.document.getElementById("bm_treeheaderDiv").classList.add('moveSelector');
		window.document.getElementById("treetable").classList.add('moveSelector');
		WE().layout.tree.toggle(true);
		var width = WE().layout.tree.getWidth();

		WE().layout.tree.widthBeforeDeleteMode = width;

		if (width < WE().consts.size.tree.moveWidth) {
			WE().layout.tree.setWidth(WE().consts.size.tree.moveWidth);
		}
		WE().layout.tree.storeWidth(WE().layout.tree.widthBeforeDeleteMode);

		WE().layout.sidebar.widthBeforeDeleteMode = WE().layout.sidebar.getWidth();

		if (args[2] != 1) {
			top.we_repl(document.getElementsByName("treeheader")[0], url);
		}
	}
}

function we_cmd_delete(args, url) {
	if (WE().session.seemode) {
		if (WE().session.deleteMode != args[1]) {
			WE().session.deleteMode = args[1];
		}
		if (args[2] != 1) {
			top.we_repl(WE().layout.weEditorFrameController.getActiveDocumentReference(), url);
		}
		return;
	}
	if (WE().session.deleteMode != args[1]) {
		WE().session.deleteMode = args[1];
	}
	if (!WE().session.deleteMode && top.treeData.state == top.treeData.tree_states.select) {
		top.treeData.setState(top.treeData.tree_states.edit);
		drawTree();
	}
	window.document.getElementById("bm_treeheaderDiv").classList.add('deleteSelector');
	window.document.getElementById("treetable").classList.add('deleteSelector');
	WE().layout.tree.toggle(true);
	var width = WE().layout.tree.getWidth();

	WE().layout.tree.widthBeforeDeleteMode = width;

	if (width < WE().consts.size.tree.deleteWidth) {
		WE().layout.tree.setWidth(WE().consts.size.tree.deleteWidth);
	}
	WE().layout.tree.storeWidth(WE().layout.tree.widthBeforeDeleteMode);

	WE().layout.sidebar.widthBeforeDeleteMode = WE().layout.sidebar.getWidth();

	if (args[2] != 1) {
		top.we_repl(document.getElementsByName("treeheader")[0], url);
	}

}

function doSave(url, trans) {
	var _EditorFrame = WE().layout.weEditorFrameController.getEditorFrameByTransaction(trans);
	// _EditorFrame.setEditorIsHot(false);
	if (_EditorFrame.getEditorAutoRebuild()) {
		url += "&we_cmd[8]=1";
	}
	if (!WE().util.we_sbmtFrm(window.load, url)) {
		url += "&we_transaction=" + trans;
		we_repl(window.load, url);
	}
}

function doPublish(url, trans) {
	if (!WE().util.we_sbmtFrm(window.load, url)) {
		url += "&we_transaction=" + trans;
		we_repl(window.load, url);
	}
}

function doExtClick(url) {
	// split url in url and parameters !
	var parameters = "",
		_position = 0;

	if ((_position = url.indexOf("?")) !== -1) {
		parameters = url.substring(_position);
		url = url.substring(0, _position);
	}

	WE().layout.weEditorFrameController.openDocument('', '', '', '', '', url, '', '', parameters);
}
