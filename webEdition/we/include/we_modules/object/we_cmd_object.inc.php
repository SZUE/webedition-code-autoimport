<?php
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
switch($cmd){
	case 'object_delete_entry_at_class':
	case 'object_up_entry_at_class':
	case 'object_down_entry_at_class':
	case 'object_insert_entry_at_class':
	case 'object_reload_entry_at_class':
	case 'object_change_entry_at_class':
	case 'object_down_meta_at_class':
	case 'object_insert_meta_at_class':
	case 'object_delete_meta_class':
	case 'object_up_meta_at_class':
	case 'object_add_user_to_field':
	case 'object_del_user_from_field':
	case 'object_del_all_users':
	case 'object_remove_image_at_class':
	case 'object_delete_link_at_class':
	case 'object_change_link_at_class':
	case 'object_change_multiobject_at_class':
		return 'we_editors/we_editor_contentobject_load.inc.php';

	case 'object_reload_entry_at_object':
	case 'object_down_meta_at_object':
	case 'object_insert_meta_at_object':
	case 'object_delete_meta_at_object':
	case 'object_up_meta_at_object':
	case 'object_change_objectlink':
	case 'object_remove_image_at_object':
	case 'object_delete_link_at_object':
	case 'object_change_link_at_object':
		return 'we_editors/we_editor_contentobjectFile_load.inc.php';

	case 'object_add_css':
	case 'object_del_css':
	case 'object_add_workspace':
	case 'object_del_workspace':
	case 'object_add_extraworkspace':
	case 'object_del_extraworkspace':
	case 'object_changeTempl_ob':
	case 'object_ws_from_class':
//	In this file we cant work with WE_OBJECT_MODULE_PATH, because a prefix is already set in : we_cmd.php
		return 'we_editors/we_editor.inc.php';
	case 'object_toggleExtraWorkspace':
		return 'we_modules/object/we_object_cmds.inc.php';
	case 'object_obj_search':
		return 'we_modules/object/search_submit.php';
	case 'object_preview_objectFile':
		return 'we_modules/object/we_object_showDocument.inc.php';
	case 'object_create_tmpfromClass':
		return 'we_modules/object/we_object_createTemplate.inc.php';
	case 'object_editObjectTextArea':
		return 'we_editors/we_editta.inc.php';
}