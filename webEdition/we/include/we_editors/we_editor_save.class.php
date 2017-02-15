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
abstract class we_editor_save{

	public static function unPublishInc($we_transaction, $we_responseText = '', $we_responseTextType = '', we_base_jsCmd $jsCmd = null, $we_responseJS = []){
		echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'editor_save.js', '', ['id' => 'loadVarEditor_save', 'data-editorSave' => setDynamicVar([
					'we_editor_save' => false,
					'we_transaction' => $we_transaction,
					'isSEEMode' => $_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE,
					'we_JavaScript' => [],
					'we_responseText' => $we_responseText,
					'we_responseTextType' => $we_responseTextType,
					'we_responseJS' => $we_responseJS,
			])]) . ($jsCmd ? $jsCmd->getCmds() : ''), we_html_element::htmlBody());
	}

	public static function saveInc($we_transaction, $we_doc, $we_responseText = '', $we_responseTextType = '', array $we_JavaScript = [], $wasSaved = false, $saveTemplate = false, $we_responseJS = [
	], $isClose = false, $showAlert = false, $publish_doc = false){
		$reload = [];
		if(!empty($wasSaved)){
			// DOC was saved, mark open tabs to reload if necessary
			// was saved - not hot anymore

			switch($we_doc->ContentType){
				case we_base_ContentTypes::FOLDER:
					if($we_doc->wasMoved()){
						$reload[$we_doc->Table] = implode(',', $GLOBALS['DB_WE']->getAllq('SELECT f.ID FROM ' . $we_doc->Table . ' f INNER JOIN ' . LOCK_TABLE . ' l ON f.ID=l.ID AND l.tbl="' . stripTblPrefix($we_doc->Table) . '" WHERE f.Path LIKE "' . $we_doc->Path . '/%"', true));
					}
					break;

				case we_base_ContentTypes::TEMPLATE: // #538 reload documents based on this template
					$reloadDocsTempls = we_rebuild_base::getTemplAndDocIDsOfTemplate($we_doc->ID, false, false, true, true);

					// reload all documents based on this template
					$reload[FILE_TABLE] = implode(',', $reloadDocsTempls['documentIDs']);
					//no need to reload the edit tab, since this is not changed & Preview is always regenerated
//			$reload[TEMPLATES_TABLE] = implode(',', $reloadDocsTempls['templateIDs']);

					break;
				case we_base_ContentTypes::OBJECT:
					$GLOBALS['DB_WE']->query('SELECT of.ID FROM ' . OBJECT_FILES_TABLE . ' of INNER JOIN ' . LOCK_TABLE . ' l ON of.ID=l.ID AND l.tbl="' . stripTblPrefix(OBJECT_FILES_TABLE) . '" WHERE of.IsFolder=0 AND of.TableID=' . intval($we_doc->ID));
					$reload[OBJECT_FILES_TABLE] = implode(',', $GLOBALS['DB_WE']->getAll(true));
			}
		}

		echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'editor_save.js', '', ['id' => 'loadVarEditor_save', 'data-editorSave' => setDynamicVar([
					'we_editor_save' => true,
					'we_transaction' => $we_transaction,
					'isHot' => ($we_responseText && $we_responseTextType == we_message_reporting::WE_MESSAGE_ERROR),
					'wasSaved' => $wasSaved,
					'wasPublished' => !empty($we_doc->Published),
					'isPublished' => $publish_doc,
					'isSEEMode' => $_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE,
					'reloadEditors' => array_filter($reload),
					'ContentType' => $we_doc->ContentType,
					'docID' => $we_doc->ID,
					'EditPageNr' => $we_doc->EditPageNr,
					'saveTmpl' => $saveTemplate,
					'isClose' => $isClose,
					'showAlert' => $showAlert,
					'we_responseText' => $we_responseText,
					'we_responseTextType' => $we_responseTextType,
					//FIXME:we_JavaScript is evaled
					'we_JavaScript' => $we_JavaScript,
					'we_cmd5' => we_base_request::_(we_base_request::JSON, 'we_cmd', '', 5), // this is we_responseJS through save-template-question
					'we_responseJS' => $we_responseJS,
					'docHasPreview' => in_array(we_base_constants::WE_EDITPAGE_PREVIEW, $we_doc->EditPageNrs),
			])]), we_html_element::htmlBody());
	}

	public static function templateSave($we_transaction){

		echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsElement('var url=WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?' . http_build_query(
					['we_cmd' => [
						0 => 'save_document',
						1 => $we_transaction,
						2 => 1,
						5 => we_base_request::_(we_base_request::RAW, 'we_cmd', '', 5), //is json64
						6 => we_base_request::_(we_base_request::RAW, 'we_cmd', '', 6), //is json64
					],
					'we_transaction' => $we_transaction,
					'we_complete_request' => 1
					], null, '&') .
				'";
new (WE().util.jsWindow)(window, url,"templateSaveQuestion",WE().consts.size.dialog.smaller,WE().consts.size.dialog.tiny,true,false,true);
'), '<body></body>');
	}

	public static function templateSaveQuestion($we_transaction, $isTemplatesUsedByThisTemplate, $nrDocsUsedByThisTemplate, $we_responseJS){
		$we_cmd6 = we_base_request::_(we_base_request::JSON, 'we_cmd', '', 6);

		$alerttext = ($isTemplatesUsedByThisTemplate ?
			g_l('alert', '[template_save_warning2]') :
			sprintf((g_l('alert', ($nrDocsUsedByThisTemplate == 1) ? '[template_save_warning1]' : '[template_save_warning]')), $nrDocsUsedByThisTemplate)
			);

		echo we_html_tools::getHtmlTop(g_l('global', '[question]'), '', '', we_html_element::jsScript(JS_DIR . 'template_save_question.js', '', ['id' => 'loadVarTemplate_save_question',
				'data-editorSave' => setDynamicVar([
					'we_transaction' => $we_transaction,
					'we_responseJS' => $we_responseJS,
					'we_cmd6' => $we_cmd6
			])]), we_html_element::htmlBody(['class' => "weEditorBody", 'onload' => "self.focus();", 'onblur' => "self.focus()"], we_html_tools::htmlYesNoCancelDialog($alerttext, '<span class="fa-stack fa-lg" style="color:#F2F200;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>', true, true, true, 'pressed_yes_button()', 'pressed_no_button()', 'pressed_cancel_button()')
			)
		);
	}

}
