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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
if(!isset($_REQUEST['we_cmd'])){
	exit();
}

//start autoloader!
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/lib/we/core/autoload.inc.php');
require_once (WE_INCLUDES_PATH . 'we_global.inc.php');
$INCLUDE = '';


switch(weRequest('string', 'we_cmd', '', 0)){
	case 'versions_preview':
		$INCLUDE = 'we_versions/weVersionsPreview.inc.php';
		break;
	case 'versions_wizard':
		$INCLUDE = 'we_versions/version_wizard/we_versions.inc.php';
		break;
	case 'versioning_log':
		$INCLUDE = 'we_logging/versions/versionsLog.inc.php';
		break;
	case 'import_files':
		$INCLUDE = 'we_import_files.inc.php';
		break;
	case 'reset_home':
	case 'mod_home':
		$INCLUDE = 'we_modules/home.inc.php';
		break;
	case 'loadSidebarDocument':
		$INCLUDE = 'sidebar.inc.php';
		break;
	case 'siteImportSaveWePageSettings':
	case 'siteImportCreateWePageSettings':
	case 'siteImport':
	case 'updateSiteImportTable':
		$INCLUDE = 'we_siteimport.inc.php';
		break;
	case 'openSelector':
	case 'openDirselector':
	case 'openDocselector':
	case 'openCatselector':
	case 'openDelSelector':
		$INCLUDE = 'we_fs.inc.php';
		break;
	case 'open_tag_wizzard':
		$INCLUDE = 'weTagWizard/we_tag_wizzard.inc.php';
		break;
	case 'change_passwd':
		$INCLUDE = 'we_editors/we_editPasswd.inc.php';
		break;
	case 'exit_delete':
	case 'exit_move':
	case 'home':
	case 'open_cockpit':
		$INCLUDE = 'home.inc.php';
		break;
	case 'logout':
		$INCLUDE = 'we_logout.inc.php';
		break;
	case 'openColorChooser':
		$INCLUDE = 'we_editors/we_colorChooser.inc.php';
		break;
	case 'newDocType':
	case 'doctypes':
	case 'save_docType':
	case 'change_docType':
	case 'deleteDocType':
	case 'deleteDocTypeok':
	case 'add_dt_template':
	case 'delete_dt_template':
	case 'dt_delete_cat':
	case 'dt_add_cat':
		$INCLUDE = 'we_editors/doctypeEdit.inc.php';
		break;
	case 'rebuild':
		$INCLUDE = 'we_editors/we_rebuild.inc.php';
		break;
	case 'help':
		#$INCLUDE = 'we_help.php';
		break;
	case 'info':
		$INCLUDE = 'we_show_info.inc.php';
		break;
	case 'openPreferences':
		$INCLUDE = 'we_editors/we_preferences_frameset.inc.php';
		break;
	case 'editThumbs':
		$INCLUDE = 'we_editors/we_thumbnails.inc.php';
		break;
	case 'editMetadataFields':
		$INCLUDE = 'we_editors/we_metadata_fields/edit_metadatafields.inc.php';
		break;
	case 'show':
		$FROM_WE_SHOW_DOC = true;
		$INCLUDE = 'we_showDocument.inc.php';
		break;
	case 'open_url_in_editor': // Beim ungewollten Verlassen (Klick auf Link im Bearbeitenmodus) des Editors wird die Location auf diese Seite weitergeleitet. Hier wird dann ein Kommando gebildet
		$INCLUDE = '/we_seem/open_url_in_editor.php';
		break;
	case 'open_form_in_editor': // Formular wird an dieses Skript umgeleitet, hier wird ein Kommando daraus gebaut, um das Dokument korrekt zu �ffnen
		$INCLUDE = '/we_seem/open_form_in_editor.php';
		break;
	case 'open_extern_document'; // wird ben�tigt um ein externes Dokument aufzurufen
		$INCLUDE = '/we_seem/we_SEEM_openExtDoc_frameset.php';
		break;
	case 'edit_document_with_parameters':
		$parastr = $_REQUEST['we_cmd'][4];
	case 'edit_document':
	case 'new_document':
	case 'new_folder':
	case 'edit_folder':
		$INCLUDE = 'we_editors/we_edit_frameset.inc.php';
		break;
	case 'edit_include_document':
		$INCLUDE = 'we_editors/SEEM_edit_include_document.inc.php';
		break;
	case 'load_editor':
		$INCLUDE = 'we_editors/we_editor.inc.php';
		break;

	case 'load_edit_header':
		$INCLUDE = 'we_editors/we_editor_header.inc.php';
		break;
	case 'load_edit_footer':
	case 'reload_editfooter':
		$INCLUDE = 'we_editors/we_editor_footer.inc.php';
		break;
	case 'load_import':
	case 'do_import':
		$INCLUDE = 'we_editors/we_import_editor.inc.php';
		break;
	case 'save_document':
	case 'new_alias':
	case 'delete_alias':
	case 'switch_edit_page':
	case 'update_image':
	case 'update_file':
	case 'copyDocument':
	case 'insert_entry_at_list':
	case 'down_entry_at_list':
	case 'up_entry_at_list':
	case 'down_link_at_list':
	case 'up_link_at_list':
	case 'edit_list':
	case 'delete_list':
	case 'add_entry_to_list':
	case 'add_link_to_linklist':
	case 'change_linklist':
	case 'change_link':
	case 'delete_linklist':
	case 'insert_link_at_linklist':
	case 'reload_editpage':
	case 'doctype_changed':
	case 'remove_image':
	case 'wrap_on_off':
	case 'restore_defaults':
	case 'publish':
	case 'unpublish':
	case 'delete_link':
	case 'add_cat':
	case 'delete_cat':
	case 'delete_all_cats':
	case 'do_add_thumbnails':
	case 'del_thumb':
	case 'resizeImage':
	case 'rotateImage':
	case 'doImage_convertGIF':
	case 'doImage_convertPNG':
	case 'doImage_convertJPEG':
	case 'doImage_crop':
	case 'template_changed':
	case 'add_navi':
	case 'delete_navi':
	case 'delete_all_navi':
	case 'revert_published':
		$INCLUDE = 'we_editors/we_editor.inc.php';
		break;
	case 'edit_linklist':
	case 'edit_link':
	case 'edit_link_at_class':
	case 'edit_link_at_object':
		$INCLUDE = 'we_editors/we_linklistedit.inc.php';
		break;
	case 'load':
	case 'loadFolder':
	case 'closeFolder':
		$INCLUDE = 'we_load.inc.php';
		break;
	case 'showLoadInfo':
		$INCLUDE = 'we_loadInfo.inc.php';
		break;
	case 'delete':
		$INCLUDE = ($_REQUEST['we_cmd'][1] ? 'we_delete.inc.php' : 'home.inc.php');
		break;
	case 'move':
		$INCLUDE = ($_REQUEST['we_cmd'][1] ? 'we_move.inc.php' : 'home.inc.php');
		break;
	case 'do_delete':
	case 'delete_single_document':
		$INCLUDE = 'we_delete.inc.php';
		break;
	case 'do_move':
	case 'move_single_document':
		$INCLUDE = 'we_move.inc.php';
		break;
	case 'editor_uploadFile':
		$INCLUDE = 'we_editors/uploadFile.inc.php';
		break;
	case 'show_binaryDoc':
		$INCLUDE = 'we_editors/we_showBinaryDoc.inc.php';
		break;
	case 'pref_ext_changed':
		$INCLUDE = 'we_prefs.inc.php';
		break;
	case 'exit_doc_question':
		$INCLUDE = 'we_templates/we_exit_doc_question.inc.php';
		break;
	case 'exit_multi_doc_question':
		$INCLUDE = 'we_templates/we_exit_multi_doc_question.inc.php';
		break;
	case 'browse_server':
		$INCLUDE = 'we_editors/we_sfileselector_frameset.inc.php';
		break;
	case 'make_backup':
		$INCLUDE = 'we_editors/we_make_backup.php';
		break;
	case 'recover_backup':
		$INCLUDE = 'we_editors/we_recover_backup.php';
		break;
	case 'import_docs':
		$INCLUDE = 'we_editors/we_import_documents.inc.php';
		break;
	case 'browse_users':
		$INCLUDE = 'we_modules/users/browse_users_frameset.inc.php';
		break;
	case 'start_multi_editor':
		$INCLUDE = 'multiEditor/start_multi_editor.inc.php';
		break;
	case 'import':
		$INCLUDE = 'we_import/we_wiz_frameset.php';
		break;
	case 'export':
		$INCLUDE = 'we_modules/export/export_frameset.php';
		break;
	case 'copyFolder':
		$INCLUDE = 'copyFolder.inc.php';
		break;
	case 'copyWeDocumentCustomerFilter':
		$INCLUDE = 'we_modules/customer/we_customer_copyWeDocumentFilter.inc.php';
		break;
	case 'changeLanguageRecursive':
		$INCLUDE = 'changeLanguage_rec.inc.php';
		break;
	case 'changeTriggerIDRecursive':
		$INCLUDE = 'changeTriggerID_rec.inc.php';
		break;
	case 'add_thumbnail':
		$INCLUDE = 'we_editors/add_thumbnail.inc.php';
		break;
	case 'image_resize':
	case 'image_convertJPEG':
	case 'image_rotate':
	case 'image_crop':
		$INCLUDE = 'we_editors/image_edit.inc.php';
		break;
	case 'open_wysiwyg_window':
		$INCLUDE = 'wysiwygWindow.inc.php';
		break;
	//  stuff about accessibility/validation
	case 'checkDocument':
		$INCLUDE = 'accessibility/checkDocument.inc.php'; //  Here request is performed
		break;
	case 'customValidationService':
		$INCLUDE = 'we_templates/customizeValidation.inc.php'; //  edit parameters
		break;

	default:
		//	Insert all config files for all modules.
		include_once (WE_INCLUDES_PATH . 'conf/we_active_integrated_modules.inc.php');

		//	In we.inc.php all names of the active modules have already been searched
//	so we only have to use the array $GLOBALS['_we_active_integrated_modules']
		if(isset($GLOBALS['_we_active_integrated_modules'])){
			foreach($GLOBALS['_we_active_integrated_modules'] as $m){
				if(file_exists(WE_MODULES_PATH . $m . '/we_cmd_' . $m . '.inc.php')){
					include_once(WE_MODULES_PATH . $m . '/we_cmd_' . $m . '.inc.php');
				}
				if($INCLUDE){
					break 2; //break foreach+switch
				}
			}
		}
		// search tools for command
		$INCLUDE = we_tool_lookup::getPhpCmdInclude();
		if($INCLUDE){
			break;
		}
		//	In we.inc.php all names of the installed modules have already been searched
		//	so we only have to use the array $_we_active_integrated_modules

		$foo = false;
		if(isset($_REQUEST['we_cmd'][0])){
			$mods = we_base_moduleInfo::getAllModules();
			foreach($mods as $m){
				if($_REQUEST['we_cmd'][0] == 'edit_' . $m['name'] . '_ifthere' && !we_base_moduleInfo::isActive($m['name'])){
					$foo = true;
					$_moduleName = $m['text_short'];
					$INCLUDE = 'messageModuleNotActivated.inc.php';
					break;
				}
			}
		}
		if($foo == false){
			//	This is ONLY used in the edit-mode of the documents.
			//	This statement prevents the page from being reloaded.
			echo we_html_element::jsElement('parent.openedWithWE = 1;');
			t_e('error', 'command \'' . $_REQUEST['we_cmd'][0] . '\' not known!');
			exit('command \'' . $_REQUEST['we_cmd'][0] . '\' not known!');
		}
}



if($INCLUDE){
	//  When pressing a link in edit-mode, the page is being reloaded from
	//  webedition. If a webedition link was pressed this page shall not be
	//  reloaded. All entries in this array represent values for we_cmd[0]
	//  when the javascript command shall NOT be inserted (p.ex while saving the file.)
	//	This is ONLY used in the edit-mode of the documents.
	$cmds_no_js = array('siteImport', 'mod_home', 'import_images', 'getWeDocFromID', 'rebuild', 'open_url_in_editor', 'open_form_in_editor', 'unlock', 'edit_document', 'load_editor', 'load_edit_header', 'load_edit_footer', 'exchange', 'validateDocument', 'show');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
	we_html_tools::protect();
	if(substr($INCLUDE, 0, 5) == 'apps/'){
		if(!defined('NO_SESS')){
			define('NO_SESS', 1);
		}
		$path = WEBEDITION_PATH;
	} else {
		$path = WE_INCLUDES_PATH;
	}
	require($path . $INCLUDE);
	//  This statement prevents the page from being reloaded
	echo (!in_array($_REQUEST['we_cmd'][0], $cmds_no_js) ? we_html_element::jsElement('parent.openedWithWE = 1;') : '') .
	($_REQUEST['we_cmd'][0] == 'edit_document' || $_REQUEST['we_cmd'][0] == 'switch_edit_page' || $_REQUEST['we_cmd'][0] == 'load_editor' ? we_html_element::jsScript(JS_DIR . 'attachKeyListener.js') : '');
}
exit;
