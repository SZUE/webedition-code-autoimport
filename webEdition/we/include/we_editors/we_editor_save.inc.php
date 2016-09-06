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
$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', we_base_request::_(we_base_request::TRANSACTION, 'we_transaction'), 1);
$reload = [];
if(!empty($wasSaved)){
	// DOC was saved, mark open tabs to reload if necessary
	// was saved - not hot anymore

	switch($GLOBALS['we_doc']->ContentType){
		case we_base_ContentTypes::FOLDER:
			if($GLOBALS['we_doc']->wasMoved()){
				$reload[$GLOBALS['we_doc']->Table] = implode(',', $GLOBALS['DB_WE']->getAllq('SELECT f.ID FROM ' . $GLOBALS['we_doc']->Table . ' f INNER JOIN ' . LOCK_TABLE . ' l ON f.ID=l.ID AND l.tbl="' . stripTblPrefix($GLOBALS['we_doc']->Table) . '" WHERE f.Path LIKE "' . $GLOBALS['we_doc']->Path . '/%"', true));
			}
			break;

		case we_base_ContentTypes::TEMPLATE: // #538 reload documents based on this template
			$reloadDocsTempls = we_rebuild_base::getTemplAndDocIDsOfTemplate($GLOBALS['we_doc']->ID, false, false, true, true);

			// reload all documents based on this template
			$reload[FILE_TABLE] = implode(',', $reloadDocsTempls['documentIDs']);
			//no need to reload the edit tab, since this is not changed & Preview is always regenerated
//			$reload[TEMPLATES_TABLE] = implode(',', $reloadDocsTempls['templateIDs']);

			break;
		case we_base_ContentTypes::OBJECT:
			$GLOBALS['DB_WE']->query('SELECT of.ID FROM ' . OBJECT_FILES_TABLE . ' of INNER JOIN ' . LOCK_TABLE . ' l ON of.ID=l.ID AND l.tbl="' . stripTblPrefix(OBJECT_FILES_TABLE) . '" WHERE of.IsFolder=0 AND of.TableID=' . intval($GLOBALS['we_doc']->ID));
			$reload[OBJECT_FILES_TABLE] = implode(',', $GLOBALS['DB_WE']->getAll(true));
	}
}

echo we_html_tools::getHtmlTop('','','', we_html_element::jsScript(JS_DIR . 'editor_save.js', '', ['id' => 'loadVarEditor_save', 'data-editorSave' => setDynamicVar([
		'we_editor_save' => true,
		'we_transaction' => $we_transaction,
		'isHot' => ($we_responseText && $we_responseTextType == we_message_reporting::WE_MESSAGE_ERROR),
		'wasSaved' => $wasSaved,
		'wasPublished' => !empty($GLOBALS['we_doc']->Published),
		'isPublished' => !empty($GLOBALS["publish_doc"]),
		'isSEEMode' => $_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE,
		'reloadEditors' => array_filter($reload),
		'ContentType' => $we_doc->ContentType,
		'docID' => $we_doc->ID,
		'EditPageNr' => $GLOBALS['we_doc']->EditPageNr,
		'saveTmpl' => intval(!empty($saveTemplate)),
		'isClose' => intval(isset($isClose) && $isClose),
		'showAlert' => (isset($showAlert) && $showAlert),
		'we_responseText' => $we_responseText,
		'we_responseTextType' => $we_responseTextType,
		//FIXME:we_JavaScript is evaled
		'we_JavaScript' => (isset($we_JavaScript) ? $we_JavaScript : ""),
		//FIXME:we_cmd5 is evaled
		'we_cmd5' => we_base_request::_(we_base_request::RAW, 'we_cmd', '', 5),
		//FIXME:we_responseJS
		'we_responseJS' => (isset($GLOBALS['we_responseJS']) ? $GLOBALS['we_responseJS'] : ''),
		'docHasPreview' => in_array(we_base_constants::WE_EDITPAGE_PREVIEW, $GLOBALS['we_doc']->EditPageNrs),
])]),  we_html_element::htmlBody());
