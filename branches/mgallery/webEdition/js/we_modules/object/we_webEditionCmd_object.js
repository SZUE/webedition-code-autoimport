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
function we_cmd_object(args, url) {
	switch (args[0]) {

		case "object_edit_ifthere":
		case "edit_object":
			new (WE().util.jsWindow)(top.window, url, "edit_module", -1, -1, 380, 250, true, true, true, true);
			break;
		case "new_objectFile":
			we_cmd("new", top.WE().consts.tables.OBJECT_FILES_TABLE, "", "objectFile");
			break;
		case "new_objectfile_folder":
			we_cmd("new", top.WE().consts.tables.OBJECT_FILES_TABLE, "", "class_folder");
			break;
		case "new_objectfile_nested_folder":
			we_cmd("new", top.WE().consts.tables.OBJECT_FILES_TABLE, "", "nested_class_folder");
			break;
		case "new_object":
			we_cmd("new", top.WE().consts.tables.OBJECT_TABLE, "", "object");
			break;
			/* this is probably obsolete: looks like it never worked!
			 case "new_object_folder":
			 we_cmd("new", top.WE().consts.tables.OBJECT_TABLE, "", "folder");
			 break;
			 */
		case "object_change_link_at_class":
			top.load.location = url;
			break;
		case "object_insert_entry_at_class":
		case "object_delete_entry_at_class":
		case "object_up_entry_at_class":
		case "object_down_entry_at_class":
		case "object_up_meta_at_class":
		case "object_down_meta_at_class":
		case "object_insert_meta_at_class":
		case "object_delete_meta_class":
		case "object_reload_entry_at_class":
		case "object_change_entry_at_class":
		case "object_add_user_to_field":
		case "object_del_user_from_field":
		case "object_del_all_users":
		case "object_remove_image_at_class":
		case "object_delete_link_at_class":
		case "object_change_multiobject_at_class":
//url += "#f"+(parseInt(args[1])-1);
			we_sbmtFrm(top.load, url);
			break;
		case "object_change_link_at_object":
			top.load.location = url;
			break;
		case "object_remove_image_at_object":
		case "object_up_meta_at_object":
		case "object_down_meta_at_object":
		case "object_insert_meta_at_object":
		case "object_delete_meta_at_object":
		case "object_reload_entry_at_object":
		case "object_change_objectlink":
		case "object_delete_link_at_object":
			url += "#f" + (parseInt(args[1]) - 1);
			we_sbmtFrm(top.load, url);
			break;
		case "object_add_workspace":
		case "object_del_workspace":
		case "object_add_css":
		case "object_del_css":
		case "object_add_extraworkspace":
		case "object_del_extraworkspace":
		case "object_changeTempl_ob":
		case "object_ws_from_class":
			if (!we_sbmtFrm(top.weEditorFrameController.getActiveDocumentReference().frames[1], url)) {
				url += "&we_transaction=" + args[2];
				we_repl(top.weEditorFrameController.getActiveDocumentReference().frames[1], url, args[0]);
			}
			break;
		case "object_toggleExtraWorkspace":
		case "object_obj_search":
			we_repl(self.load, url, args[0]);
			break;
		case "delete_object":
			top.we_cmd("del", 1, top.WE().consts.tables.OBJECT_TABLE);
			break;
		case "delete_objectfile":
			top.we_cmd("del", 1, top.WE().consts.tables.OBJECT_FILES_TABLE);
			break;
		case "move_objectfile":
			top.we_cmd("mv", 1, top.WE().consts.tables.OBJECT_FILES_TABLE);
			break;
		case "object_preview_objectFile":
			new (WE().util.jsWindow)(top.window, url, "preview_object", -1, -1, 1600, 1200, true, true, true, true);
			break;
		case "object_create_tmpfromClass":
			new (WE().util.jsWindow)(top.window, url, "tmpfromClass", -1, -1, 580, 200, true, false, true, false);
			break;
		case "open_object":
			we_cmd("load", top.WE().consts.tables.OBJECT_TABLE);
			url = top.WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=we_selector_document&we_cmd[8]=object&we_cmd[2]=" + top.WE().consts.tables.OBJECT_TABLE + "&we_cmd[5]=" + encodeURIComponent("opener.top.weEditorFrameController.openDocument(table,currentID,currentType)") + "&we_cmd[9]=1";
			new (WE().util.jsWindow)(top.window, url, "we_dirChooser", -1, -1, top.WE().consts.size.docSelect.width, top.WE().consts.size.docSelect.height, true, true, true);
			break;
		case "open_objectFile":
			we_cmd("load", top.WE().consts.tables.OBJECT_FILES_TABLE);
			url = top.WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=we_selector_document&we_cmd[8]=objectFile&we_cmd[2]=" + top.WE().consts.tables.OBJECT_FILES_TABLE + "&we_cmd[5]=" + encodeURIComponent("opener.top.weEditorFrameController.openDocument(table,currentID,currentType)") + "&we_cmd[9]=1";
			new (WE().util.jsWindow)(top.window, url, "we_dirChooser", -1, -1, top.WE().consts.size.docSelect.width, top.WE().consts.size.docSelect.height, true, true, true);
			break;
		default:
			return false;
	}
	return true;
}