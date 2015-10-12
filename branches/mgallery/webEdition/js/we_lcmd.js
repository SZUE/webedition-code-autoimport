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

function we_lcmd(par) {
	switch (par) {
		case 'trigger_save_document':
			if (WE().layout.weEditorFrameController.getActiveDocumentReference() && WE().layout.weEditorFrameController.getActiveDocumentReference().frames.editFooter && WE().layout.weEditorFrameController.getActiveDocumentReference().frames.editFooter.weCanSave) {
				WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(false);
				WE().layout.weEditorFrameController.getActiveDocumentReference().frames.editFooter.we_save_document();
			} else {
				top.we_showMessage(WE().consts.g_l.main.nothing_to_save, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
			return;
		case 'trigger_publish_document':
			if (WE().layout.weEditorFrameController.getActiveDocumentReference() && WE().layout.weEditorFrameController.getActiveDocumentReference().frames.editFooter && WE().layout.weEditorFrameController.getActiveDocumentReference().frames.editFooter.weCanSave) {
				WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(true);
				WE().layout.weEditorFrameController.getActiveDocumentReference().frames.editFooter.we_save_document();
			} else {
				top.we_showMessage(WE().consts.g_l.main.nothing_to_publish, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
			return;

		case 'new_webEditionPage':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.WEDOCUMENT);
			return;
		case 'new_image':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.IMAGE);
			return;
		case 'new_html_page':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.HTML);
			return;
		case 'new_flash_movie':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.FLASH);
			return;
		case 'new_quicktime_movie':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.QUICKTIME);
			return;
		case 'new_video_movie':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.VIDEO);
			return;
		case 'new_audio_audio':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.AUDIO);
			return;
		case 'new_javascript':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.JS);
			return;
		case 'new_text_plain':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.TEXT);
			return;
		case 'new_text_xml':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.XML);
			return;
		case 'new_text_htaccess':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.HTACESS);
			return;
		case 'new_css_stylesheet':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.CSS);
			return;
		case 'new_binary_document':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.APPLICATION);
			return;
		case 'new_template':
			top.we_cmd("new", WE().consts.tables.TEMPLATES_TABLE, "", WE().consts.contentTypes.TEMPLATE);
			return;
		case 'new_document_folder':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", "folder");
			return;
		case 'new_template_folder':
			top.we_cmd("new", WE().consts.tables.TEMPLATES_TABLE, "", "folder");
			return;
		case 'new_collection_folder':
			top.we_cmd("new", WE().consts.tables.VFILE_TABLE, "", "folder");
			return;
		case 'new_collection':
			top.we_cmd("new", WE().consts.tables.VFILE_TABLE, "", WE().consts.contentTypes.COLLECTION);
			return;
		case 'delete_documents':
			top.we_cmd("del", 1, WE().consts.tables.FILE_TABLE);
			return;
		case 'delete_templates':
			top.we_cmd("del", 1, WE().consts.tables.TEMPLATES_TABLE);
			return;
		case 'delete_collections':
			top.we_cmd("del", 1, WE().consts.tables.VFILE_TABLE);
			return;
		case 'move_documents':
			top.we_cmd("mv", 1, WE().consts.tables.FILE_TABLE);
			return;
		case 'move_templates':
			top.we_cmd("mv", 1, WE().consts.tables.TEMPLATES_TABLE);
			return;
		case 'add_documents_to_collection':
			top.we_cmd("tocollection", 1, WE().consts.tables.FILE_TABLE);
			return;
		case 'add_objectfiles_to_collection':
			top.we_cmd("tocollection", 1, WE().consts.tables.OBJECT_FILES_TABLE);
			return;
		case 'new_dtPage':
			top.we_cmd("new", WE().consts.tables.FILE_TABLE, "", WE().consts.contentTypes.WEDOCUMENT, arguments[1]);
			return;
		case 'new_ClObjectFile':
			top.we_cmd("new", WE().consts.tables.OBJECT_FILES_TABLE, "", WE().consts.contentTypes.OBJECT_FILE, arguments[1]);
			return;
		case 'we_selector_delete':
			top.we_cmd('we_selector_delete', '', -1, '', '', '', '', '', '', 1);
			return;
		default:
			var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}

			top.we_cmd.apply(this, args);

	}
}