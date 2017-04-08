<?php

/**
 * These should be consts only used INSIDE WE, we should not need to load this class in frontend
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
 * @package constants
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
abstract class we_base_jsConstants{

	static function process($what){
		//first set header for all
		header('Content-Type: text/javascript;charset=UTF-8', true);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT', true);
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime(__FILE__)) . ' GMT', true);
		header('Cache-Control: max-age=86400, must-revalidate', true);
		header('Pragma: ', true);
		//set charset, so g_l will use UTF-8, which is needed for json-encode
		$GLOBALS['we']['PageCharset']='UTF-8';

		switch($what){
			/* other Consts */
			case 'collection':
				return we_collection::getJSConsts();
			case 'selectors':
				return we_selector_file::getJSConsts();
			case 'tagWizzard':
				return we_template::getJSTWConsts();
			case 'weSearch':
				return we_search_view::getJSConsts();
			/* Langs */
			case 'g_l.banner':
				return we_banner_banner::getJSLangConsts();
			case 'g_l.backupWizard'://FIXME!!!
				return we_backup_wizard::getJSLangConsts();
			case 'g_l.customer':
				return we_customer_customer::getJSLangConsts();
			case 'g_l.doctypeEdit':
				return we_docTypes::getJSLangConsts();
			case 'g_l.exports':
				return we_export_export::getJSLangConsts();
			case 'g_l.fileupload':
				return we_fileupload_ui_base::getJSLangConsts();
			case 'g_l.fileselector':
				return we_selector_file::getJSLangConsts();
			case 'g_l.glossary':
				return we_glossary_glossary::getJSConsts();
			case 'g_l.import':
				return we_import_wizard::getJSLangConsts();
			case 'g_l.liveUpdate':
				return liveUpdateFrames::getJSLangConsts();
			case 'g_l.main':
				return self::getMainJSLangConsts();
			case 'g_l.metadatafields':
				return we_metadata_metaData::getJSLangConsts();
			case 'g_l.navigation':
				return we_navigation_navigation::getJSConsts();
			case 'g_l.newsletter':
				return we_newsletter_newsletter::getJSLangConsts();
			case 'g_l.prefs':
				return we_base_preferences::getJSLangConsts();
			case 'g_l.rebuild':
				return we_rebuild_wizard::getJSLangConsts();
			case 'g_l.shop':
				return we_shop_shop::getJSLangConsts();
			case 'g_l.selectors.category':
				return we_category::getJSLangConsts();
			case 'g_l.tagWizzard':
				return we_template::getJSLangConsts();
			case 'g_l.thumbnail':
				return we_thumbnail::getJSLangConsts();
			case 'g_l.users':
				return we_users_user::getJSLangConsts();
			case 'g_l.versions':
				return we_versions_version::getJSLangConsts();
			case 'g_l.voting':
				return we_voting_voting::getJSLangConsts();
			case 'g_l.weSearch':
				return we_search_search::getJSLangConsts();
			case 'g_l.workflow':
				return we_workflow_workflow::getJSLangConsts();
			default:
				t_e('loading of JS consts ' . $what . ' failed');
		}
	}

	private static function getMainJSLangConsts(){
		$ctLngs = [];
		foreach(g_l('contentTypes', '') as $key => $lng){
			$ctLngs[$key] = $lng;
		}

		return 'WE().consts.g_l=JSON.parse("' . setLangString(
				[
				'main' => [
					'close_include' => g_l('SEEM', '[alert][close_include]'),
					'confirm_ext_change' => g_l('weClass', '[confirm_ext_change]'),
					'delete_single_confirm_delete' => g_l('alert', '[delete_single][confirm_delete]') . '\n',
					'eplugin_exit_doc' => g_l('alert', '[eplugin_exit_doc]'),
					'folder_copy_success' => g_l('copyFolder', '[copy_success]'),
					'name_nok' => g_l('alert', '[name_nok]'),
					'nav_first_document' => g_l('alert', '[navigation][first_document]'),
					'nav_last_document' => g_l('alert', '[navigation][last_document]'),
					'nav_no_entry' => g_l('alert', '[navigation][no_entry]'),
					'nav_no_open_document' => g_l('alert', '[navigation][no_open_document]'),
					'no_document_opened' => g_l('global', '[no_document_opened]'),
					'no_editor_left' => g_l('multiEditor', '[no_editor_left]'),
					'no_perms' => g_l('alert', '[no_perms]'),
					'no_perms_action' => g_l('alert', '[no_perms_action]'),
					'notValidFolder' => g_l('weClass', '[notValidFolder]'),
					'nothing_to_delete' => g_l('alert', '[nothing_to_delete]'),
					'nothing_to_move' => g_l('alert', '[nothing_to_move]'),
					'nothing_to_publish' => g_l('alert', '[nothing_to_publish]'),
					'nothing_to_save' => g_l('alert', '[nothing_to_save]'),
					'open_link_in_SEEM_edit_include' => g_l('SEEM', '[open_link_in_SEEM_edit_include]'),
					'prefs_saved_successfully' => g_l('cockpit', '[prefs_saved_successfully]'),
					'save_error_fields_value_not_valid' => g_l('alert', '[save_error_fields_value_not_valid]'),
					'unable_to_call_ping' => g_l('global', '[unable_to_call_ping]'),
					'unable_to_call_setpagenr' => g_l('global', '[unable_to_call_setpagenr]'),
					'unlockRequest' => g_l('global', '[unlock][request]'),
					'unlockRequestTextPrompt' => g_l('global', '[unlock][requestPrompt]'),
					'unlockRequestTimePrompt' => g_l('global', '[unlock][requestTimePrompt]'),
					'unlockRequestForceTo' => g_l('global', '[unlock][request_force_to]'),
					'unlockReleaseDone' => g_l('global', '[unlock][releasedone]'),
					'untitled' => g_l('global', '[untitled]'),
				],
				'message_reporting' => [
					'notice' => g_l('alert', '[notice]'),
					'warning' => g_l('alert', '[warning]'),
					'error' => g_l('alert', '[error]'),
					'msgNotice' => g_l('messageConsole', '[iconBar][notice]'),
					'msgWarning' => g_l('messageConsole', '[iconBar][warning]'),
					'msgError' => g_l('messageConsole', '[iconBar][error]'),
					'question' => g_l('global', '[question]'),
					'yes' => g_l('global', '[yes]'),
					'no' => g_l('buttons_global', '[no][value]'),
					'cancel' => g_l('buttons_global', '[cancel][value]'),
					'ok' => g_l('buttons_global', '[ok][value]'),
				],
				'alert' => [
					'anchor_invalid' => g_l('linklistEdit', '[anchor_invalid]'),
					'browser_crashed' => g_l('alert', '[browser_crashed]'),
					'changed_include' => g_l('SEEM', '[alert][changed_include]'),
					'confirm_applyFilterDocument' => g_l('alert', '[confirm][applyWeDocumentCustomerFiltersDocument]'),
					'confirm_applyFilterFolder' => g_l('alert', '[confirm][applyWeDocumentCustomerFiltersFolder]'),
					'confirm_change_to_preview' => g_l('SEEM', '[confirm][change_to_preview]'),
					'copy_folder_not_valid' => g_l('alert', '[copy_folder_not_valid]'),
					'discard_changed_data' => g_l('alert', '[discard_changed_data]'),
					'exit_doc_question' => [
						FILE_TABLE => g_l('alert', '[' . stripTblPrefix(FILE_TABLE) . '][exit_doc_question]'),
						TEMPLATES_TABLE => g_l('alert', '[' . stripTblPrefix(TEMPLATES_TABLE) . '][exit_doc_question]'),
						'tools' => g_l('tools', '[exit_doc_question]'),
						(defined('OBJECT_TABLE') ? OBJECT_TABLE : '') => (defined('OBJECT_TABLE') ? g_l('alert', '[' . stripTblPrefix(OBJECT_TABLE) . '][exit_doc_question]') : ''),
						(defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : '') => (defined('OBJECT_FILES_TABLE') ? g_l('alert', '[' . stripTblPrefix(OBJECT_FILES_TABLE) . '][exit_doc_question]') : ''),
					],
					'exit_multi_doc_question' => g_l('alert', '[exit_multi_doc_question]'),
					'ext_doc_selected' => g_l('SEEM', '[ext_doc_selected]'),
					'found_in_workflow' => g_l('alert', '[found_in_workflow]'),
					'image_edit_null_not_allowed' => g_l('weClass', '[image_edit_null_not_allowed]'),
					'in_wf_warning' => [
						'tblFile' => (defined('WORKFLOW_TABLE') ? g_l('alert', '[tblFile][in_wf_warning]') : ''),
						'tblObjectFiles' => (defined('WORKFLOW_TABLE') ? g_l('alert', '[tblObjectFiles][in_wf_warning]') : ''),
						'tblObject' => (defined('WORKFLOW_TABLE') ? g_l('alert', '[tblObject][in_wf_warning]') : ''),
						'tblTemplates' => (defined('WORKFLOW_TABLE') ? g_l('alert', '[tblTemplates][in_wf_warning]') : ''),
						'tblVFiles' => (defined('WORKFLOW_TABLE') ? g_l('alert', '[tblVFiles][in_wf_warning]') : ''),
					],
					'link_does_not_work' => g_l('SEEM', '[link_does_not_work]'),
					'move' => g_l('alert', '[move]'),
					'move_exit_open_docs_continue' => g_l('alert', '[move_exit_open_docs_continue]'),
					'move_exit_open_docs_question' => g_l('alert', '[move_exit_open_docs_question]'),
					'newWEAvailable' => g_l('sysinfo', '[newWEAvailable]'),
					'pwd_startupRegExFailed' => g_l('global', '[pwd][startupRegExFailed]'),
					'revert_publish_question' => g_l('weEditorInfo', '[revert_publish_question]'),
					'same_master_template' => g_l('weClass', '[same_master_template]'),
				],
				'scheduler' => [
					'activeSchedule' => [
						'title' => g_l('button', '[saveInScheduler][alt]'),
						'value' => g_l('button', '[saveInScheduler][value]'),
					],
					'inActiveSchedule' => [
						'title' => g_l('button', '[publish][alt]'),
						'value' => g_l('button', '[publish][value]'),
					],
				],
				'cockpit' => [
					'all_selected' => g_l('cockpit', '[all_selected]'),
					'increase_size' => g_l('cockpit', '[increase_size]'),
					'invalid_url' => g_l('cockpit', '[invalid_url]'),
					'no_type_selected' => g_l('cockpit', '[no_type_selected]'),
					'not_activated' => g_l('alert', '[cockpit_not_activated]'),
					'remove' => g_l('cockpit', '[pre_remove]') . ' %s ' . g_l('cockpit', '[post_remove]'),
					'reduce_size' => g_l('cockpit', '[reduce_size]'),
					'reset_settings' => g_l('alert', '[cockpit_reset_settings]'),
					'tabName' => g_l('cockpit', '[cockpit]'),
					'fdl' => [
						'kv_failedLogins' => g_l('cockpit', '[kv_failedLogins][deleted]'),
					],
					'mfd' => [
						'last_modified' => g_l('cockpit', '[last_modified]'),
					],
					'pad' => [
						'until_befor_from' => g_l('cockpit', '[until_befor_from]'),
						'note_not_modified' => g_l('cockpit', '[note_not_modified]'),
						'title_empty' => g_l('cockpit', '[title_empty]'),
						'date_empty' => g_l('cockpit', '[date_empty]'),
					],
				],
				'editorScript' => [
					'confirm_navDel' => g_l('navigation', '[del_question]'),
					'fieldNameEmpty' => g_l('modules_object', '[fieldNameEmpty]'),
					'fieldNameNotTitleDesc' => g_l('modules_object', '[fieldNameNotTitleDesc]'),
					'fieldNameNotValid' => g_l('modules_object', '[fieldNameNotValid]'),
					'field_contains_incorrect_chars' => g_l('alert', '[field_contains_incorrect_chars]'),
					'field_input_contains_incorrect_length' => g_l('alert', '[field_input_contains_incorrect_length]'),
					'field_int_contains_incorrect_length' => g_l('alert', '[field_int_contains_incorrect_length]'),
					'field_int_value_to_height' => g_l('alert', '[field_int_value_to_height]'),
					'gdTypeNotSupported' => g_l('weClass', '[type_not_supported_hint]'),
					'noRotate' => g_l('weClass', '[rotate_hint]'),
				],
				'weTagWizard' => [
					'insert_tagname' => g_l('weTagWizard', '[insert_tagname]'),
					'insert_tagname_not_exist' => sprintf(g_l('weTagWizard', '[insert_tagname_not_exist]'), '_wrongTag') . '\n\n',
				],
				'contentTypes' => $ctLngs,
				'selectors' => [
				],
				'tinyMceTranslationObject' => we_wysiwyg_editor::getTranslationObject(),
				'weCollection' => [
					'element_not_set' => g_l('weClass', '[collection][notSet]'),
					'info_insertion' => g_l('weClass', '[collection][infoAddFiles]'),
				],
				'object' => [
					'multiobject_recursion' => g_l('modules_object', '[multiobject_recursion]')
				]
				]) . '");';
	}

}
