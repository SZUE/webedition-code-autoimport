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
			if (top.weEditorFrameController.getActiveDocumentReference() && top.weEditorFrameController.getActiveDocumentReference().frames.editFooter && top.weEditorFrameController.getActiveDocumentReference().frames.editFooter.weCanSave) {
				top.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(false);
				top.weEditorFrameController.getActiveDocumentReference().frames.editFooter.we_save_document();
			} else {
				top.we_showMessage(g_l.nothing_to_save, WE_MESSAGE_ERROR, window);
			}
			return;
		case 'trigger_publish_document':
			if (top.weEditorFrameController.getActiveDocumentReference() && top.weEditorFrameController.getActiveDocumentReference().frames.editFooter && top.weEditorFrameController.getActiveDocumentReference().frames.editFooter.weCanSave) {
				top.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(true);
				top.weEditorFrameController.getActiveDocumentReference().frames.editFooter.we_save_document();
			} else {
				top.we_showMessage(g_l.nothing_to_publish, WE_MESSAGE_ERROR, window);
			}
			return;

		case 'new_webEditionPage':
			top.we_cmd("new", tables.FILE_TABLE, "", contentTypes.WEDOCUMENT);
			return;
		case 'new_image':
			top.we_cmd("new", tables.FILE_TABLE, "", contentTypes.IMAGE);
			return;
		case 'new_html_page':
			top.we_cmd("new", tables.FILE_TABLE, "", contentTypes.HTML);
			return;
		case 'new_flash_movie':
			top.we_cmd("new", tables.FILE_TABLE, "", contentTypes.FLASH);
			return;
		case 'new_quicktime_movie':
			top.we_cmd("new", tables.FILE_TABLE, "", contentTypes.QUICKTIME);
			return;
		case 'new_video_movie':
			top.we_cmd("new", tables.FILE_TABLE, "", contentTypes.VIDEO);
			return;
		case 'new_audio_audio':
			top.we_cmd("new", tables.FILE_TABLE, "", contentTypes.AUDIO);
			return;
		case 'new_javascript':
			top.we_cmd("new", tables.FILE_TABLE, "", contentTypes.JS);
			return;
		case 'new_text_plain':
			top.we_cmd("new", tables.FILE_TABLE, "", contentTypes.TEXT);
			return;
		case 'new_text_xml':
			top.we_cmd("new", tables.FILE_TABLE, "", contentTypes.XML);
			return;
		case 'new_text_htaccess':
			top.we_cmd("new", tables.FILE_TABLE, "", contentTypes.HTACESS);
			return;
		case 'new_css_stylesheet':
			top.we_cmd("new", tables.FILE_TABLE, "", contentTypes.CSS);
			return;
		case 'new_binary_document':
			top.we_cmd("new", tables.FILE_TABLE, "", contentTypes.APPLICATION);
			return;
		case 'new_template':
			top.we_cmd("new", tables.TEMPLATES_TABLE, "", contentTypes.TEMPLATE);
			return;
		case 'new_document_folder':
			top.we_cmd("new", tables.FILE_TABLE, "", "folder");
			return;
		case 'new_template_folder':
			top.we_cmd("new", tables.TEMPLATES_TABLE, "", "folder");
			return;
		case 'new_collection_folder':
			top.we_cmd("new", tables.VFILE_TABLE, "", "folder");
			return;
		case 'new_collection':
			top.we_cmd("new", tables.VFILE_TABLE, "", contentTypes.COLLECTION);
			return;
		case 'delete_documents':
			top.we_cmd("del", 1, tables.FILE_TABLE);
			return;
		case 'delete_templates':
			top.we_cmd("del", 1, tables.TEMPLATES_TABLE);
			return;
		case 'delete_collections':
			top.we_cmd("del", 1, tables.VFILE_TABLE);
			return;
		case 'move_documents':
			top.we_cmd("mv", 1, tables.FILE_TABLE);
			return;
		case 'move_templates':
			top.we_cmd("mv", 1, tables.TEMPLATES_TABLE);
			return;
		case 'add_documents_to_collection':
			top.we_cmd("tocollection", 1, tables.FILE_TABLE);
			return;
		case 'add_objectfiles_to_collection':
			top.we_cmd("tocollection", 1, tables.OBJECT_FILES_TABLE);
			return;
		case 'new_dtPage':
			top.we_cmd("new", tables.FILE_TABLE, "", contentTypes.WEDOCUMENT, arguments[1]);
			return;
		case 'new_ClObjectFile':
			top.we_cmd("new", tables.OBJECT_FILES_TABLE, "", contentTypes.OBJECT_FILE, arguments[1]);
			return;
		case 'openDelSelector':
			//setTimeout(function () {
			top.we_cmd('openDelSelector', '', -1, '', '', '', '', '', '', 1);
			//}, 50);
			return;
		default:
			var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}

			top.we_cmd.apply(this, args);

	}
}