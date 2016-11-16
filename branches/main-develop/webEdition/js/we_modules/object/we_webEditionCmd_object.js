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
we_cmd_modules.object = function (args, url) {
	switch (args[0]) {
		case "object_edit_ifthere":
		case "edit_object":
			new (WE().util.jsWindow)(this, url, "edit_module", -1, -1, 380, 250, true, true, true, true);
			break;
		case "new_objectFile":
			we_cmd("new", WE().consts.tables.OBJECT_FILES_TABLE, "", "objectFile");
			break;
		case "new_objectfile_folder":
			we_cmd("new", WE().consts.tables.OBJECT_FILES_TABLE, "", "class_folder");
			break;
		case "new_objectfile_nested_folder":
			we_cmd("new", WE().consts.tables.OBJECT_FILES_TABLE, "", "nested_class_folder");
			break;
		case "new_object":
			we_cmd("new", WE().consts.tables.OBJECT_TABLE, "", "object");
			break;
			/* this is probably obsolete: looks like it never worked!
			 case "new_object_folder":
			 we_cmd("new", WE().consts.tables.OBJECT_TABLE, "", "folder");
			 break;
			 */
		case "object_change_link_at_class":
			top.load.location = url;
			break;

		case "object_add_user_to_field":
			args[4] = args[1].allIDs.join(',');
			/* FALLTHROUGH */
		case "object_reload_entry_at_class":
			// modified for use as selector callback: args[1] is reserved now for selector results
			WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);
			// splice args[1]
			args.splice(1, 1);
			url = WE().util.getWe_cmdArgsUrl(args);
			/* FALLTHROUGH */
		case "object_insert_entry_at_class":
		case "object_delete_entry_at_class":
		case "object_up_entry_at_class":
		case "object_down_entry_at_class":
		case "object_up_meta_at_class":
		case "object_down_meta_at_class":
		case "object_insert_meta_at_class":
		case "object_delete_meta_class":
		case "object_change_entry_at_class":
		case "object_del_user_from_field":
		case "object_del_all_users":
		case "object_remove_image_at_class":
		case "object_delete_link_at_class":
		case "object_change_multiobject_at_class":
			WE().util.we_sbmtFrm(top.load, url);
			break;
		case "object_change_link_at_object":
			top.load.location = url;
			break;

		case "object_reload_entry_at_object":
			if(args[4] === 'setScrollTo'){
				 this.setScrollTo();
			}
			/* FALLTHROUGH */
		case "object_change_objectlink":
			// modified for use as selector callback: args[1] is reserved now for selector results
			WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);
			// splice args[1]
			args.splice(1, 1);
			url = WE().util.getWe_cmdArgsUrl(args);
			/* FALLTHROUGH */
		case "object_remove_image_at_object":
		case "object_up_meta_at_object":
		case "object_down_meta_at_object":
		case "object_insert_meta_at_object":
		case "object_delete_meta_at_object":
		case "object_reload_entry_at_object":
		case "object_delete_link_at_object":
			url += "#f" + (parseInt(args[1]) - 1);
			WE().util.we_sbmtFrm(top.load, url);
			break;

		case "object_add_workspace":
		case "object_add_css":
			WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);
			if (typeof (args[1]) === "object") {
				url += "&we_cmd[1]=" + args[1].allIDs.join(",");
			}
			if (!WE().util.we_sbmtFrm(WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1], url)) {
				url += "&we_transaction=" + args[2];
				we_repl(WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1], url, args[0]);
			}
			break;

		case "object_del_workspace":
		case "object_del_css":
		case "object_changeTempl_ob":
		case "object_ws_from_class":
			if (!WE().util.we_sbmtFrm(WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1], url)) {
				url += "&we_transaction=" + args[2];
				we_repl(WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1], url, args[0]);
			}
			break;
		case "object_obj_search":
			we_repl(window.load, url, args[0]);
			break;
		case "object_selectDirectory_callback":
			WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);
			this.pathOfDocumentChanged();
			break;
		case 'fieldHref_selectIntHref_callback':
			WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);
			if(args[2]){
				this.document.we_form.elements[args[3]][0].checked = true;
			}
			break;
		case 'fieldHref_selectExtHref_callback':
			WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);
			if(args[2] === 'showRadio'){
				this.document.we_form.elements[args[3]][1].checked = true;
			}
			break;
		case 'fieldMultiobject_selectMultiobject_callback':
			if(parseInt(args[1].currentID) === this.doc.docId){
				top.we_showMessage(WE().consts.g_l.object.multiobject_recursion, WE().consts.message.WE_MESSAGE_ERROR, window);
				this.document.we_form.elements[args[1].JSIDName].value = '';
				this.document.we_form.elements[args[1].JSTextName].value = '';
			}
			if(args[2] === 'isSEEM'){
				we_cmd('object_change_objectlink', '', this.doc.we_transaction, args[3]);
			}
			break;
		case "delete_object":
			top.we_cmd("del", 1, WE().consts.tables.OBJECT_TABLE);
			break;
		case "delete_objectfile":
			top.we_cmd("del", 1, WE().consts.tables.OBJECT_FILES_TABLE);
			break;
		case "move_objectfile":
			top.we_cmd("mv", 1, WE().consts.tables.OBJECT_FILES_TABLE);
			break;
		case "object_preview_objectFile":
			new (WE().util.jsWindow)(this, url, "preview_object", -1, -1, 1600, 1200, true, true, true, true);
			break;
		case "object_create_tmpfromClass":
			new (WE().util.jsWindow)(this, url, "tmpfromClass", -1, -1, 580, 200, true, false, true, false);
			break;
		case "open_object":
			we_cmd("load", WE().consts.tables.OBJECT_TABLE);
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=we_selector_document&we_cmd[8]=object&we_cmd[2]=" + WE().consts.tables.OBJECT_TABLE + "&we_cmd[5]=" + encodeURIComponent("WE().layout.weEditorFrameController.openDocument(table,top.fileSelect.data.currentID,top.fileSelect.data.currentType)") + "&we_cmd[9]=1";
			new (WE().util.jsWindow)(this, url, "we_dirChooser", -1, -1, WE().consts.size.docSelect.width, WE().consts.size.docSelect.height, true, true, true);
			break;
		case "open_objectFile":
			we_cmd("load", WE().consts.tables.OBJECT_FILES_TABLE);
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=we_selector_document&we_cmd[8]=objectFile&we_cmd[2]=" + WE().consts.tables.OBJECT_FILES_TABLE + "&we_cmd[5]=" + encodeURIComponent("WE().layout.weEditorFrameController.openDocument(table,top.fileSelect.data.currentID,top.fileSelect.data.currentType)") + "&we_cmd[9]=1";
			new (WE().util.jsWindow)(this, url, "we_dirChooser", -1, -1, WE().consts.size.docSelect.width, WE().consts.size.docSelect.height, true, true, true);
			break;
		default:
			return false;
	}
	return true;
};