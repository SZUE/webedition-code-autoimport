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
abstract class we_editor_script{

	public static function get(){
		$ret = '';
		if(isset($GLOBALS['we_doc'])){
			if($GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT && $GLOBALS['we_doc']->ContentType == we_base_ContentTypes::TEMPLATE){
				//no wysiwyg
			} else {
				$ret = we_wysiwyg_editor::getHTMLHeader();
			}
		}
		$hasGD = isset($GLOBALS['we_doc']) && $GLOBALS['we_doc']->ContentType === we_base_ContentTypes::IMAGE && $GLOBALS['we_doc']->gd_support();

		$doc = [
			'we_transaction' => we_base_request::_(we_base_request::TRANSACTION, "we_transaction", 0),
			'docId' => isset($GLOBALS['we_doc']) ? intval($GLOBALS['we_doc']->ID) : 0,
			'docPath' => isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Path : '',
			'docText' => isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Text : '',
			'oldparentid' => isset($GLOBALS['we_doc']) ? intval($GLOBALS['we_doc']->ParentID) : 0,
			'docName' => isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Name : '',
			'isFolder' => isset($GLOBALS['we_doc']) ? intval($GLOBALS['we_doc']->IsFolder) : 0,
			'docTable' => isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Table : '',
			'docClass' => isset($GLOBALS['we_doc']) ? get_class($GLOBALS['we_doc']) : '',
			'hasCustomerFilter' => intval(isset($GLOBALS['we_doc']) && defined('CUSTOMER_TABLE') && in_array(we_base_constants::WE_EDITPAGE_WEBUSER, $GLOBALS['we_doc']->EditPageNrs) && isset($GLOBALS['we_doc']->documentCustomerFilter)),
			'hasGlossary' => intval(defined('GLOSSARY_TABLE') && isset($GLOBALS['we_doc']) && ($GLOBALS['we_doc']->ContentType == we_base_ContentTypes::WEDOCUMENT || $GLOBALS['we_doc']->ContentType === we_base_ContentTypes::OBJECT_FILE)),
			'gdType' => $hasGD && isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->getGDType() : '',
			'gdSupport' => intval($hasGD && isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->gd_support() : 0),
			'isWEObject' => intval(isset($GLOBALS['we_doc']) && ($GLOBALS['we_doc']->ContentType === we_base_ContentTypes::OBJECT/* FIXME: only supported for type object || $GLOBALS['we_doc']->ContentType === we_base_ContentTypes::OBJECT_FILE */)),
			'WE_EDIT_IMAGE' => intval(defined('WE_EDIT_IMAGE')),
			'useSEE_MODE' => false,
			'cmd' => false,
		];

		if(isset($GLOBALS['we_doc'])){
			if(!empty($GLOBALS['we_doc']->ApplyWeDocumentCustomerFiltersToChilds) && $GLOBALS['we_doc']->ParentID){
				$doc['cmd'] = ['copyWeDocumentCustomerFilter', $GLOBALS['we_doc']->ID, $GLOBALS['we_doc']->Table, $GLOBALS['we_doc']->ParentID];
			}

			$useSeeModeJS = [
				we_base_ContentTypes::WEDOCUMENT => [we_base_constants::WE_EDITPAGE_CONTENT, we_base_constants::WE_EDITPAGE_PREVIEW],
				we_base_ContentTypes::TEMPLATE => [we_base_constants::WE_EDITPAGE_PREVIEW, we_base_constants::WE_EDITPAGE_PREVIEW_TEMPLATE],
				we_base_ContentTypes::OBJECT_FILE => [we_base_constants::WE_EDITPAGE_CONTENT, we_base_constants::WE_EDITPAGE_PREVIEW]
			];

			if(isset($useSeeModeJS[$GLOBALS['we_doc']->ContentType]) && in_array($GLOBALS['we_doc']->EditPageNr, $useSeeModeJS[$GLOBALS['we_doc']->ContentType])){
				$doc['useSEE_MODE'] = true;
			}
		}

		$ret .= we_html_element::cssLink(CSS_DIR . 'editor.css') .
				we_html_element::jsScript(JS_DIR . 'we_textarea.js') .
				we_html_element::jsScript(JS_DIR . 'we_editor_script.js', '', ['id' => 'loadVarEditor_script', 'data-doc' => setDynamicVar($doc)]);

		unset($doc);

		return $ret;
	}

}
