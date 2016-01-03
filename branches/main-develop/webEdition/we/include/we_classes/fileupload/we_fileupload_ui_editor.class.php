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
class we_fileupload_ui_editor extends we_fileupload_ui_preview{
	protected $editorJS = array(
		'writebackTarget' => '',
		'editorCallback' => '',
	);
	protected $doImport = true;
	protected $contentType = array();

	public function __construct($contentType = array(), $extensions = '', $doImport = true){
		parent::__construct($contentType, $extensions);

		$permImageEdit = permissionhandler::hasPerm("NEW_GRAFIK");
		$permCat = permissionhandler::hasPerm("EDIT_KATEGORIE");
		$this->formElements = array_merge($this->formElements , array(
			'uploader' => array('set' => true, 'multiIconBox' => true, 'space' => 0, 'rightHeadline' => true, 'noline' => true),
			'parentId' => array('set' => true, 'multiIconBox' => true, 'space' => 130, 'rightHeadline' => false, 'noline' => true),
			'sameName' => array('set' => true, 'multiIconBox' => true, 'space' => 180, 'rightHeadline' => false, 'noline' => false),
			'importMeta' => array('set' => true, 'multiIconBox' => true, 'space' => 120, 'rightHeadline' => false, 'noline' => true),
			'isSearchable' => array('set' => true, 'multiIconBox' => true, 'space' => 120, 'rightHeadline' => false, 'noline' => false),
			'categories' => array('set' => $permCat, 'multiIconBox' => true, 'space' => 120, 'rightHeadline' => false, 'noline' => false),
			'attributes' => array('set' => true, 'multiIconBox' => true, 'space' => 0, 'rightHeadline' => true, 'noline' => false),
			'thumbnails' => array('set' => $permImageEdit, 'multiIconBox' => true, 'space' => 0, 'rightHeadline' => true, 'noline' => false),
			'imageResize' => array('set' => $permImageEdit, 'multiIconBox' => true, 'space' => 130, 'rightHeadline' => false, 'noline' => true),
			'imageRotate' => array('set' => $permImageEdit, 'multiIconBox' => true, 'space' => 130, 'rightHeadline' => false, 'noline' => true),
			'imageQuality' => array('set' => $permImageEdit, 'multiIconBox' => true, 'space' => 130, 'rightHeadline' => false, 'noline' => true),
		));

		$this->dimensions['dragWidth'] = 400;
		$this->moreFieldsToAppend = array_merge($this->moreFieldsToAppend, array(
			array('fu_file_parentID', 'int'),
		));
		$this->doImport = $doImport;

		if($this->doImport){
			$this->responseClass = 'we_fileupload_resp_import';
			if(!$this->contentType || $this->contentType === we_base_ContentTypes::IMAGE){
				$this->formElements['formAttributes'] = $this->formElements['formThumbnails'] = $this->formElements['formImageEdit'] = true;
				$this->moreFieldsToAppend = array_merge($this->moreFieldsToAppend, array(
					array('fu_doc_isSearchable', 'int'),
					array('fu_doc_categories', 'text'),
					array('fu_doc_thumbs', 'text'),
					array('fu_doc_alt', 'text'),
					array('fu_doc_title', 'text'),
					array('fu_doc_width', 'int'),
					array('fu_doc_height', 'int'),
					array('fu_doc_widthSelect', 'int'),
					array('fu_doc_heightSelect', 'int'),
					array('fu_doc_keepRatio', 'int'),
					array('fu_doc_degrees', 'int'),
					array('fu_doc_quality', 'int'),
				));
			}
		} else {
			$this->responseClass = 'we_fileupload_resp_base';
		}
	}

	public function getCss() {
		$this->dimensions['dragWidth'] = 376;
		return parent::getCss() . we_html_element::cssElement('
			.paddingTop{
				padding-top: 12px;
			}
		');
	}

	public function getHtml($returnRows = false){
		$progress = new we_progressBar(0, true);
		$progress->setStudLen(200);
		$progress->setName('_fileupload');
		$divProgressbar = we_html_element::htmlDiv(array('id' => 'div_fileupload_progressBar', 'style' => 'display:none;'), $progress->getHTML('', 'font-size:11px;'));
		$divButtons = we_html_element::htmlDiv(array('id' => 'div_fileupload_buttons', 'style' => 'width:400px'),
			$this->getDivBtnInputReset($this->dimensions['inputWidth'] - 4)
		);
		$noImage = '<img style="margin:8px 18px;border-style:none;width:64px;height:64px;" src="/webEdition/images/icons/no_image.gif" alt="no-image" />';
		$formUploader = we_html_element::htmlDiv(array('id' => 'imageUpload', 'style' => 'display:block;width:384px;'),
			$this->getJs() .
			$this->getCss() .
			$this->getHiddens() .

			we_html_element::htmlDiv(array('style' => 'width:200px'),
				$divButtons
			) .
			we_html_element::htmlDiv(array('style' => 'width:200px'),
				we_html_element::htmlDiv(array('id' => 'div_fileupload_right', 'style'=>"position:relative;"),
					$this->getHtmlDropZone('preview', $noImage)
				)
			) .
			$divProgressbar
		);

		$parts = $parts = array();
		$parts = is_array($form = $this->makeMultiIconRow('uploader', 'Dateiauswahl', $formUploader)) ? array_merge($parts, array($form)) : $parts;
		if($this->parentID['setField']){
			$parts = is_array($form = $this->getFormParentID()) ? array_merge($parts, array($form)) : $parts;
		}
		$parts = is_array($form = $this->getFormSameName()) ? array_merge($parts, array($form)) : $parts;

		if($this->doImport){
			$parts = is_array($form = $this->getFormImportMeta()) ? array_merge($parts, array($form)) : $parts;
			$parts = is_array($form = $this->getFormIsSearchable()) ? array_merge($parts, array($form)) : $parts;
			$parts = is_array($form = $this->getFormCategories()) ? array_merge($parts, array($form)) : $parts;

			if(!$this->contentType || $this->contentType === we_base_ContentTypes::IMAGE){
				$parts = is_array($form = $this->getFormImageAttributes()) ? array_merge($parts, array($form)) : $parts;
				$parts = is_array($form = $this->getFormThumbnails()) ? array_merge($parts, array($form)) : $parts;
				$parts = is_array($form = $this->getFormImageResize()) ? array_merge($parts, array($form)) : $parts;
				$parts = is_array($form = $this->getFormImageRotate()) ? array_merge($parts, array($form)) : $parts;
				$parts = is_array($form = $this->getFormImageQuality()) ? array_merge($parts, array($form)) : $parts;
			}
		}

		if($returnRows){
			return $parts;
		}

		$box = we_html_multiIconBox::getHTML("", $parts, 20, '', $this->formElements['tableProperties']['foldAtNr'], $this->formElements['tableProperties']['foldAtOpen'], $this->formElements['tableProperties']['foldAtClose'], false);
		$divBtnUpload = we_html_element::htmlDiv(array('style' => 'float:right;padding-top:10px;width:auto;'), $this->getDivBtnUploadCancel(170));

		return we_html_multiIconBox::getJS() . $box . ($this->isExternalBtnUpload ? '' : $divBtnUpload);
	}

	public function getHtmlFooter(){
		return we_html_element::htmlDiv(array('class' => 'weDialogButtonsBody', 'style' => 'width:auto; height:100%;'),
			we_html_element::htmlDiv(array('style' => 'float:right'),
				(we_html_element::htmlDiv(array('style' => 'display:table-cell;'), $this->getDivBtnUploadCancel(170))) .
				we_html_element::htmlDiv(array('style' => 'display:table-cell;'), we_html_button::create_button(we_html_button::CLOSE, 'javascript:top.close()'))
			)
		);
	}

	public function getEditorJS(){ // TODO: move this JS to weFileupload.js!!
		return we_html_element::jsScript(JS_DIR . 'weFileUpload.js') .
			we_html_element::jsElement('
predefinedCallback = "' . $this->editorJS['predefinedCallback'] . '";
customCallback = function(importedDocument){' . $this->editorJS['customCallback'] . '};
writebackTarget = "' . $this->editorJS['writebackTarget'] . '";

doOnImportSuccess = function(importedDocument){
	if(writebackTarget){
		documentWriteback(importedDocument);
	}

	switch(predefinedCallback){
		case "selector":
			opener.top.reloadDir();
			opener.top.unselectAllFiles();
			opener.top.addEntry(importedDocument.id, "noch nichts", false, "importedDocument.path");
			opener.top.doClick(importedDocument, 0);
			setTimeout(function () {
					opener.top.selectFile(importedDocument);
				}, 200);
			reloadMainTree();
			setTimeout(self.close, 250);
			break;
		case "sselector":
			opener.top.fscmd.selectDir();
			opener.top.fscmd.selectFile(importedDocument.text);
			self.close();
		case "weimg":
			break;
		case "imagedialog":
			alert("import done: " + importedDocument.path + " (id: " + importedDocument.id + ")");
			we_FileUpload.reset();
			document.we_form.elements["radio_type"][0].checked=true;
			document.getElementById("imageInt").style.display="block";
			document.getElementById("imageExt").style.display="none";
			document.getElementById("imageUpload").style.display="none";
			document.getElementById("yuiAcResultImage").value = importedDocument.id;
			document.getElementById("yuiAcInputImage").value = importedDocument.path;
			imageChanged();

			break;
		default:
			// do nothing
	}

	customCallback(importedDocument);
}

reloadMainTree = function (table) {
	try {
		top.opener.top.we_cmd(\'load\', \'tblFile\');
	} catch (e) {
		//
	}
};

documentWriteback = function(importedDocument){
	' . ($this->editorJS['writebackTarget'] ? 'WE().layout.weEditorFrameController.getVisibleEditorFrame().' . $this->editorJS['writebackTarget'] . ' = importedDocument.id;' : '//no writeback') . '
}
');
	}

	public function setEditorJS($editorJS = array()){
		$this->editorJS = array_merge($this->editorJS, $editorJS);
	}

	public function setDoImport($doImport = true){
		$this->doImport = $doImport;
		$this->responseClass = $this->doImport ? 'we_fileupload_resp_import' : 'we_fileupload_resp_base';

	}


}

