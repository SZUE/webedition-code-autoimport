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
	protected $contentType = [];
	protected $posBtnUpload = 'bottom';

	//protected $predefinedConfigs = [];

	public function __construct($contentType = '', $extensions = '', $doImport = true){
		parent::__construct($contentType, $extensions);

		$this->type = 'editor';
		$permImageEdit = permissionhandler::hasPerm("NEW_GRAFIK");
		$permCat = permissionhandler::hasPerm("EDIT_KATEGORIE");
		$moreElements = we_fileupload::EDIT_IMAGES_CLIENTSIDE ? []: [
			'imageResize' => ['set' => true && $permImageEdit, 'multiIconBox' => true, 'space' => we_html_multiIconBox::SPACE_BIG, 'rightHeadline' => false, 'noline' => true],
			'imageRotate' => ['set' => true && $permImageEdit, 'multiIconBox' => true, 'space' => we_html_multiIconBox::SPACE_BIG, 'rightHeadline' => false, 'noline' => true],
			'imageQuality' => ['set' => true && $permImageEdit, 'multiIconBox' => true, 'space' => we_html_multiIconBox::SPACE_BIG, 'rightHeadline' => false, 'noline' => true],
			];
		$this->formElements = array_merge($this->formElements, [
			'uploader' => ['set' => true, 'multiIconBox' => true, 'rightHeadline' => true, 'noline' => false],
			'parentId' => ['set' => true, 'multiIconBox' => true, 'rightHeadline' => true, 'noline' => true],
			'sameName' => ['set' => true, 'multiIconBox' => true, 'space' => we_html_multiIconBox::SPACE_BIG, 'rightHeadline' => false],
			'importMeta' => ['set' => true, 'multiIconBox' => true, 'space' => we_html_multiIconBox::SPACE_MED, 'rightHeadline' => false, 'noline' => true],
			'isSearchable' => ['set' => true, 'multiIconBox' => true, 'space' => we_html_multiIconBox::SPACE_MED, 'rightHeadline' => false],
			'categories' => ['set' => $permCat, 'multiIconBox' => true, 'space' => we_html_multiIconBox::SPACE_MED, 'rightHeadline' => false],
			'attributes' => ['set' => true, 'multiIconBox' => true, 'rightHeadline' => true],
			'thumbnails' => ['set' => $permImageEdit, 'multiIconBox' => true, 'rightHeadline' => true],
			], $moreElements);

		$this->dimensions['dragWidth'] = 400;
		$this->moreFieldsToAppend = array_merge($this->moreFieldsToAppend, [
			['fu_file_parentID', 'int'],
			]);
		$this->doImport = $doImport;

		if($this->doImport){
			$this->responseClass = 'we_fileupload_resp_import';
			if(!$this->contentType || $this->contentType === we_base_ContentTypes::IMAGE){
				$this->formElements['formAttributes'] = $this->formElements['formThumbnails'] = $this->formElements['formImageEdit'] = true;
				// FIXME: do not append all of these when clientside scaling is active! <= we need php const CLIENTSIDE_IMAGE_EDIT
				$evenMoreFields = array(
					array('fu_doc_width', 'int'),
					array('fu_doc_height', 'int'),
					array('fu_doc_widthSelect', 'int'),
					array('fu_doc_heightSelect', 'int'),
					array('fu_doc_keepRatio', 'int'),
					array('fu_doc_degrees', 'int'),
					array('fu_doc_quality', 'int'),
				);

				$this->moreFieldsToAppend = array_merge($this->moreFieldsToAppend, array(
					array('fu_doc_isSearchable', 'int'),
					array('fu_doc_categories', 'text'),
					array('fu_doc_thumbs', 'text'),
					array('fu_doc_alt', 'text'),
					array('fu_doc_title', 'text'),
				), $evenMoreFields);
			}
		} else {
			$this->responseClass = 'we_fileupload_resp_base';
		}
	}

	public function getCss(){
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
		$divProgressbar = we_html_element::htmlDiv(['id' => 'div_fileupload_progressBar', 'style' => 'display:none;'], $progress->getHTML('', 'font-size:11px;'));
		$divButtons = we_html_element::htmlDiv(['id' => 'div_fileupload_buttons', 'style' => 'width:400px'], $this->getDivBtnInputReset($this->dimensions['inputWidth'] - 4)
		);
		$divBtnUpload = we_html_element::htmlDiv(['style' => 'float:right;padding-top:10px;width:auto;'], $this->getDivBtnUploadCancel(170));

		$noImage = '<img style="margin:8px 18px;border-style:none;width:64px;height:64px;" src="/webEdition/images/icons/no_image.gif" alt="no-image" />';
		$formUploader = we_html_element::htmlDiv(['id' => 'imageUpload', 'style' => 'display:block;width:384px;'], $this->getJs() .
				$this->getCss() .
				$this->getHiddens() .
				we_html_element::htmlDiv(['style' => 'width:200px'], $divButtons
				) .
				we_html_element::htmlDiv(['style' => 'width:400px'], we_html_element::htmlDiv(['id' => 'div_fileupload_right', 'style' => "position:relative;"],
						$this->getHtmlDropZone('preview', $noImage) .
						(we_fileupload::EDIT_IMAGES_CLIENTSIDE ? we_html_element::htmlDiv([], $this->getFormImageEditClientside()) : '')
					)
				) .
				$divProgressbar . ($this->posBtnUpload === 'top' && !$this->isExternalBtnUpload ? $divBtnUpload : '')
		);

		$parts = is_array($form = $this->makeMultiIconRow('uploader', 'Dateiauswahl', $formUploader)) ? [$form] : [];
		if($this->parentID['setField']){
			$parts = is_array($form = $this->getFormParentID()) ? array_merge($parts, [$form]) : $parts;
		}
		$parts = is_array($form = $this->getFormSameName()) ? array_merge($parts, [$form]) : $parts;

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

		$box = we_html_multiIconBox::getHTML("", $parts, 20, '', $this->formElements['tableProperties']['foldAtNr'], $this->formElements['tableProperties']['foldAtOpen'], $this->formElements['tableProperties']['foldAtClose']);

		return we_html_multiIconBox::getJS() . $box . ($this->isExternalBtnUpload || $this->posBtnUpload === 'top' ? '' : $divBtnUpload);
	}

	public function getHtmlFooter(){
		return we_html_element::htmlDiv(array('class' => 'weDialogButtonsBody', 'style' => 'width:auto; height:100%;'), we_html_element::htmlDiv(array('style' => 'float:right'), (we_html_element::htmlDiv(array('style' => 'display:table-cell;'), $this->getDivBtnUploadCancel(170))) .
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
			opener.top.addEntry(importedDocument.id, importedDocument.text, false, "importedDocument.path");
			opener.top.doClick(importedDocument.id);
			setTimeout(opener.top.selectFile, 200, importedDocument.id);
			reloadMainTree();
			setTimeout(self.close, 100);
			break;
		case "sselector":
			opener.top.fscmd.selectDir();
			opener.top.fscmd.selectFile(importedDocument.text);
			self.close();
		case "weimg":
			break;
		/*
		case "imagedialog":
			alert("import done: " + importedDocument.path + " (id: " + importedDocument.id + ")");
			we_FileUpload.reset();
			document.we_form.elements["radio_type"][0].checked=true;
			document.getElementById("imageInt").style.display="block";
			document.getElementById("imageExt").style.display="none";
			document.getElementsByClassName("weFileuploadEditor")[0].style.display="none";
			document.getElementById("yuiAcResultImage").value = importedDocument.id;
			document.getElementById("yuiAcInputImage").value = importedDocument.path;
			imageChanged();
			break;
		*/
		case "imagedialog_popup":
			alert("import done: " + importedDocument.path + " (id: " + importedDocument.id + ")");
			we_FileUpload.reset();
			top.opener.document.getElementById("imageInt").style.display="block";
			top.opener.document.getElementById("imageExt").style.display="none";
			top.opener.document.getElementById("yuiAcResultImage").value = importedDocument.id;
			top.opener.document.getElementById("yuiAcInputImage").value = importedDocument.path;
			' . (weSuggest::USE_DRAG_AND_DROP ? 'top.opener.dropzoneAddPreview(\'Image\', -1);' : '') . '
			top.opener.imageChanged();
			top.close();
			break;
		case "we_suggest_ext":
			alert("import done: " + importedDocument.path + " (id: " + importedDocument.id + ")");
			if(top.opener.document.we_form && top.opener.document.we_form.elements["we_dialog_args[fileSrc]"]){
				top.opener.document.we_form.elements["we_dialog_args[fileSrc]"].value=importedDocument.path;
				top.opener.document.we_form.elements["we_dialog_args[fileID]"].value=importedDocument.id;
			} else if(WE().layout.weEditorFrameController.getVisibleEditorFrame().document.we_form &&
					WE().layout.weEditorFrameController.getVisibleEditorFrame().document.we_form.elements["we_dialog_args[fileSrc]"]){
				top.opener.document.we_form.elements["we_dialog_args[fileSrc]"].value=importedDocument.path;
				top.opener.document.we_form.elements["we_dialog_args[fileID]"].value=importedDocument.id;
			}
			' . (weSuggest::USE_DRAG_AND_DROP ? 'top.opener.dropzoneAddPreview(\'Image\', -1);' : '') . '
			top.opener.imageChanged();
			self.close();
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

	public function setEditorJS($editorJS = []){
		$this->editorJS = array_merge($this->editorJS, $editorJS);
	}

	public function setDoImport($doImport = true){
		$this->doImport = $doImport;
		$this->responseClass = $this->doImport ? 'we_fileupload_resp_import' : 'we_fileupload_resp_base';
	}

	public function setPositionBtnUpload($pos = 'bottom'){
		$this->posBtnUpload = $pos;
	}

	public static function showFrameset(){
		$contentType = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 1));
		$doImport = boolval(we_base_request::_(we_base_request::CMD, 'we_cmd', true, 2));
		$predefinedConfig = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
		$writebackTarget = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4));
		$customCallback = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5));
		$importToID = we_base_request::_(we_base_request::CMD, 'we_cmd', 0, 6);
		$setFixedImportTo = we_base_request::_(we_base_request::CMD, 'we_cmd', 0, 7);
		$predefinedCallback = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 8);
		$isPreset = boolval(we_base_request::_(we_base_request::CMD, 'we_cmd', false, 9));

		$fileUpload = new we_fileupload_ui_editor($contentType, '', $doImport);
		$fileUpload->setPredefinedConfig($predefinedConfig);
		$fileUpload->setCallback('top.doOnImportSuccess(scope.weDoc);');
		$fileUpload->setDimensions(array('dragWidth' => 374, 'inputWidth' => 378));
		$fileUpload->setIsPreset($isPreset);
		$fileUpload->setIsExternalBtnUpload(true);
		$fileUpload->setFieldParentID(array('setField' => true, 'preset' => $importToID, 'setFixed' => $setFixedImportTo));
		$fileUpload->setEditorJS(array(
			'writebackTarget' => $writebackTarget,
			'customCallback' => $customCallback,
			'predefinedCallback' => $predefinedCallback
		));
		$yuiSuggest = &weSuggest::getInstance();

		echo we_html_tools::getHtmlTop('fileupload', '', '', STYLESHEET . $fileUpload->getEditorJS() .
			we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
			we_html_element::jsScript(JS_DIR . 'keyListener.js') .
			we_html_element::jsScript(JS_DIR . 'dialogs/we_dialog_base.js'), we_html_element::htmlBody(array('class' => 'weDialogBody'), we_html_element::htmlForm([], we_html_element::htmlDiv(array('id' => 'we_fileupload_editor', 'class' => 'weDialogBody', 'style' => 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;overflow: auto;'), $fileUpload->getHtml()) .
					we_html_element::htmlDiv(array('id' => 'we_fileupload_footer', 'class' => '', 'style' => 'position:absolute;height:40px;bottom:0px;left:0px;right:0px;overflow: hidden;'), $fileUpload->getHtmlFooter())
				) .
				weSuggest::getYuiFiles() .
				$yuiSuggest->getYuiJs()
			)
		);
	}

}
