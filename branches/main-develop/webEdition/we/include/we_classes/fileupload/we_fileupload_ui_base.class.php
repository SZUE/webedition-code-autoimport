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
class we_fileupload_ui_base extends we_fileupload{
	protected $responseClass = 'we_fileupload_resp_base';
	protected $genericFilename = '';
	protected $type = 'base';
	protected $form = ['name' => '',
		'action' => ''
	 ];
	protected $externalUiElements = ['contentName' => '',
		'footerName' => 'footer',
		'btnUploadName' => 'upload_btn'
	 ];
	protected $cmdFileselectOnclick = '';
	protected $callback = 'document.we_form.submit()';
	protected $nextCmd = '';
	protected $dimensions = ['width' => 400,
		'width' => 400,
		'dragWidth' => 300,
		'dragHeight' => 30,
		'inputWidth' => 170,
		'alertBoxWidth' => 0,
		'marginTop' => 0,
		'marginBottom' => 0
	 ];
	protected $footerName = '';
	protected $contentName = '';
	protected $disableUploadBtnOnInit = true;
	protected $internalProgress = ['isInternalProgress' => false,
		'width' => 0,
	 ];
	protected $externalProgress = ['isExternalProgress' => false,
		'parentElemId' => 'progressbar',
		'name' => '',
	 ];
	protected $isPreset = false;
	protected $fileTable = '';
	protected $binDocProperties = [];
	protected $isInternalBtnUpload = false; // used in we_fileupload_inc only
	protected $location = '';
	protected $layout = 'horizontal';
	public $moreFieldsToAppend = [];
	protected $fileNameTemp = "";
	protected $cliensideImageEditing = false;

	public function __construct($name){
		parent::__construct($name);
		$this->setGenericFileName();
		$this->doCommitFile = false;
		$this->responseClass = 'we_fileupload_resp_base';
		$this->form['action'] = WEBEDITION_DIR . 'rpc.php?protocol=json&cmd=ProcessFileupload&cns=fileupload';
	}

	public function setResponseClass($responseClass){
		$this->responseClass = class_exists($responseClass) ? $responseClass : $this->responseClass;
	}

	public function setForm($form = []){
		$this->form['name'] = isset($form['name']) ? $form['name'] : $this->form['name'];
		$this->form['action'] = isset($form['action']) ? $form['action'] : $this->form['action'];
		$this->form['action'] = isset($form['additionalParams']) ? $this->form['action'] . '&' . $form['additionalParams'] : $this->form['action'];
	}

	public function setCallback($callback = ''){
		$this->callback = $callback ? : 'if(document.forms["' . $this->form['name'] . '"]){document.forms["' . $this->form['name'] . '"].submit();}';
	}

	public function setNextCmd($nextCmd = ''){
		$this->nextCmd = $nextCmd;
	}

	public function setIsPreset($isPreset = false){
		$this->isPreset = $isPreset;
	}

	public function setIsDragAndDrop($isDragAndDrop = true){
		$this->isDragAndDrop = $isDragAndDrop;
	}

	public function setDisableUploadBtnOnInit($disable = true){
		$this->disableUploadBtnOnInit = $disable;
	}

	public function setCmdFileSelectOnclick($cmd){
		$this->cmdFileselectOnclick = $cmd;
	}

	public function setGenericFileName($genericFileName = ''){
		$this->genericFilename = $genericFileName ? : TEMP_DIR . self::REPLACE_BY_UNIQUEID;
	}

	public function setDimensions($dimensions = []){
		$this->dimensions = array_merge($this->dimensions, $dimensions);
	}

	public function setDoCommitFile($doCommitFile = true){
		$this->doCommitFile = boolval($doCommitFile);
	}

	public function setInternalProgress($internalProgress = []){
		$this->internalProgress = array_merge($this->internalProgress, $internalProgress);
	}

	public function setExternalProgress($externalProgress = []){
		$this->externalProgress = array_merge($this->externalProgress, $externalProgress);
	}

	public function setExternalUiElements($externalUiElements = []){
		$this->externalUiElements = array_merge($this->externalUiElements, $externalUiElements);
	}

	public function setMoreFieldsToAppend($fields = []){
		$this->moreFieldsToAppend = array_merge($this->moreFieldsToAppend, $fields);
	}

	public function setIsInternalBtnUpload($flag = true){
		$this->isInternalBtnUpload = $flag;
	}

	public static function getExternalDropZone($name = 'we_File', $content = '', $style = '', $dragFromTree = true, $dragFromExt = true, $jsCmdTree = '', $jsCmdExt = '', array $contentTypes = [], $table = ''){
		if(!self::isDragAndDrop()){
			return $content;
		}

		$table = $table ? : FILE_TABLE;
		$cts = $contentTypes ? ',' . implode(',', $contentTypes) . ',' : '';

		return we_html_element::cssLink(CSS_DIR . 'we_fileupload.css') . we_html_element::jsScript(JS_DIR . 'we_fileupload_externalDropzone.js') . // insert this in every top
			we_html_element::htmlDiv(['id' => 'div_' . $name . '_fileDrag',
					'class' => 'we_file_drag',
					'ondrop' => "handleDrop(event,'" . $name . "', " . ($dragFromTree ? 'true' : 'false') . ", " . ($dragFromExt ? 'true' : 'false') . ", '" . $jsCmdTree . "', '" . $jsCmdExt . "', '" . $cts . "', '" . $table . "');",
					'ondragover' => "handleDragOver(event, '" . $name . "');",
					'ondragleave' => "handleDragLeave(event, '" . $name . "');",
					'style' => 'margin-top:0.5em;display:' . (self::isDragAndDrop() ? 'block;' : 'none;') . $style
				], $content);
	}

	public function getButtonWrapped($type, $disabled = false, $width = 170, $notWrapped = false){
		switch($type){
			case 'browse':
				$isIE10 = we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11;

				$fileInput = we_html_element::htmlInput(['class' => 'fileInput fileInputHidden' . ($isIE10 ? ' fileInputIE10' : ''),
						'style' => 'width:' . $width . 'px;',
						'type' => 'file',
						'name' => $this->name,
						'id' => $this->name,
						'accept' => trim($this->typeCondition['accepted']['all'], ','),
						]);
				$btn = we_html_button::create_button('fat:browse_harddisk,fa-lg fa-hdd-o', 'javascript:void(0)', '', 0, 0, '', '', $disabled, false, '_btn', false, '', 'weBtn noMarginLeft');

				return we_html_element::htmlDiv(['id' => 'div_' . $this->name . '_fileInputWrapper', 'class' => 'we_fileInputWrapper', 'style' => 'vertical-align:top;display:inline-block;'], $fileInput . $btn
				);
			case 'reset':
				$btn = we_html_button::create_button('reset', 'javascript:weFileUpload_instance.reset()', '', 0, 0, '', '', $disabled, false, '_btn', true, '', 'weBtn noMarginLeft');
				return $notWrapped ? $btn : we_html_element::htmlDiv(['id' => 'div_fileupload_btnReset', 'style' => 'height:30px;margin-top:18px;display:none;'], $btn);

			case 'upload':
				$js = ($this->externalUiElements['contentName'] ? 'top.' . $this->externalUiElements['contentName'] . '.' : '') . 'weFileUpload_instance.startUpload();';
				$btn = we_html_button::create_button(we_html_button::UPLOAD, 'javascript:' . $js, '', 0, 0, '', '', $disabled, false, '_btn', true, '', 'weBtn noMarginLeft');
				return we_html_element::htmlDiv(['id' => 'div_fileupload_btnUpload', 'style' => 'margin-top: 4px;'], $btn);

			case 'cancel':
				$js = 'top.' . ($this->externalUiElements['contentName'] ? $this->externalUiElements['contentName'] . '.' : '') . 'weFileUpload_instance.cancelUpload();';
				$btn = we_html_button::create_button(we_html_button::CANCEL, 'javascript:' . $js, '', 0, 0, '', '', $disabled, false, '_btn', true, '', 'weBtn noMarginLeft');
				return we_html_element::htmlDiv(['id' => 'div_fileupload_btnCancel', 'style' => 'margin-top: 4px;display:none;'], $btn);
		}
	}

	public function getHTML(){
		$isIE10 = we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11;
		// FIXME: do we need thos replacements?
		$butReset = str_replace(["\n\r", "\r\n", "\r", "\n"], ' ', $this->getButtonWrapped('reset', true, ($isIE10 ? 84 : 100), true));
		$btnUpload = str_replace(["\n\r", "\r\n", "\r", "\n"], ' ', $this->getButtonWrapped('upload', true, ($isIE10 ? 84 : 100)));
		$btnCancel = str_replace(["\n\r", "\r\n", "\r", "\n"], ' ', $this->getButtonWrapped('cancel', false, ($isIE10 ? 84 : 100)));

		return we_html_element::htmlDiv(['id' => 'div_' . $this->name, 'style' => 'float:left;margin-top:' . $this->dimensions['marginTop'] . 'px;margin-bottom:' . $this->dimensions['marginBottom'] . 'px;'], we_html_element::htmlDiv([], $this->getButtonWrapped('browse', false, $isIE10 ? 84 : ($this->dimensions['width'] - 110)) .
					we_html_element::htmlDiv(['id' => 'div_' . $this->name . '_btnResetUpload', 'style' => 'vertical-align: top; display: inline-block; height: 22px;'], ($this->isInternalBtnUpload ? $btnUpload : $butReset)
					) .
					($this->isInternalBtnUpload ? we_html_element::htmlDiv(['id' => 'div_' . $this->name . '_btnCancel', 'style' => 'vertical-align: top; display: none; height: 22px;'], $btnCancel
						) : ''
					) .
					$this->getHtmlDropZone() . $this->getHtmlFileInfo()
				)
			) .
			$this->getHiddens();
	}

	protected function getHiddens(){
		return we_html_element::htmlHiddens(['weFileNameTemp' => '',
				'weFileName' => '',
				'weFileCt' => '',
		]);
	}

	public function getHtmlAlertBoxes(){
		$text = we_fileupload::getMaxUploadSizeMB() ? sprintf(g_l('newFile', '[size_limit_set_to]'), we_fileupload::getMaxUploadSizeMB()) : g_l('newFile', '[no_size_limit]');
		$box = we_html_tools::htmlAlertAttentionBox($text, we_html_tools::TYPE_INFO, ($this->dimensions['alertBoxWidth'] ? : $this->dimensions['width']));

		return we_html_element::htmlDiv(['id' => 'div_alert'], $box);
	}

	protected function _getHtmlFileRow(){
		return '';
	}

	protected function _getHtmlFileRow_legacy(){
		return '';
	}

	protected function getHtmlDropZone(){
		return we_html_element::htmlDiv(['id' => 'div_' . $this->name . '_fileDrag', 'class' => 'we_file_drag', 'style' => 'margin-top:0.5em;display:' . ($this->isDragAndDrop ? 'block' : 'none')], g_l('importFiles', '[dragdrop_text]'));
	}

	protected function getHtmlFileInfo(){
		return we_html_element::htmlDiv(['id' => 'div_' . $this->name . '_fileName', 'style' => 'height:26px;padding-top:10px;display:' . ($this->isDragAndDrop ? 'none' : 'block') . ';'], '') .
			we_html_element::htmlDiv(['style' => 'display:block;padding:0.6em 0 0 0.2em'], we_html_element::htmlDiv(['id' => 'div_' . $this->name . '_message', 'style' => 'height:26px;font-size:1em;'], '&nbsp;') .
				($this->internalProgress['isInternalProgress'] ? $this->getProgress_tmp() : '')
		);
	}

	protected function getProgress_tmp(){ // FIXME: use standard progress and adapt functions in uploader abstract to it!
		return '<div style="display: none;height:26px;" id="div_' . $this->name . '_progress">
					<table class="default"><tbody><tr>
						<td style="vertical-align:middle"><div class="progress_image" style="width:0px;height:10px;" id="' . $this->name . '_progress_image" style="vertical-align:top"></div><div class="progress_image_bg" style="width:130px;height:10px;" id="' . $this->name . '_progress_image_bg" style="vertical-align:top"></div></td>
						<td class="small bold" style="color:#006699;padding-left:8px;" id="span_' . $this->name . '_progress_text">0%</td>
						<td class="small bold"><span id="span_' . $this->name . '_progress_more_text" style="color:#006699;"></span></td>
					</tr></tbody></table>
				</div>';
		/*
		  $progress = new we_progressBar(0, 170,'_fileupload');
		  return $progress->getHTML('', 'font-size:11px;');
		  $divProgressbar = we_html_element::htmlDiv(['id' => 'div_fileupload_progressBar', 'style' => 'margin: 13px 0 10px 0;display:none;'), $progress->getHTML('', 'font-size:11px;'));
		 *
		 */
	}

	public function getCss(){
		return we_html_element::cssLink(CSS_DIR . 'we_fileupload.css') .
			we_html_element::cssElement('
				div.we_file_drag{
					padding-top: ' . (($this->dimensions['dragHeight'] - 10) / 2) . 'px;
					height: ' . $this->dimensions['dragHeight'] . 'px;
					width: ' . $this->dimensions['dragWidth'] . 'px;
				}
			');
	}

	public function getJs(){
		return we_html_element::jsScript(JS_DIR . 'weFileUpload_init.js', '', ['id' => 'loadVarWeFileUpload_init', 'data-initObject' => setDynamicVar($this->getJSDynamic())]);
	}

	public function getJSDynamic(){
		return ['uiType' => $this->type,
				'fieldName' => $this->name,
				'genericFilename' => $this->genericFilename,
				'doCommitFile' => $this->doCommitFile,
				'form' => $this->form,
				'footerName' => $this->footerName,
				'uploadBtnName' => $this->externalUiElements['btnUploadName'],
				'maxUploadSize' => $this->maxUploadSizeBytes,
				'typeCondition' => str_replace(['\n\r', '\r\n', '\r', '\n'], '', $this->typeCondition),
				'isDragAndDrop' => $this->isDragAndDrop,
				'isPreset' => $this->isPreset,
				'nextCmd' => $this->nextCmd,
				'cmdFileselectOnclick' => $this->cmdFileselectOnclick,
				'chunkSize' => self::CHUNK_SIZE,
				'intProgress' => $this->internalProgress,
				'extProgress' => $this->externalProgress,
				'isGdOk' => $this->isGdOk,
				'htmlFileRow' => (we_fileupload::EDIT_IMAGES_CLIENTSIDE ? $this->_getHtmlFileRow() : $this->_getHtmlFileRow_legacy()),
				'fileTable' => $this->fileTable,
				'binDocProperties' => $this->binDocProperties,
				'disableUploadBtnOnInit' => $this->disableUploadBtnOnInit,
				'moreFieldsToAppend' => $this->moreFieldsToAppend,
				'isInternalBtnUpload' => $this->isInternalBtnUpload,
				'responseClass' => $this->responseClass,
				'clientsideImageEditing' => ($this->cliensideImageEditing && we_fileupload::EDIT_IMAGES_CLIENTSIDE ? 1 : 0)
			];
	}

	public static function getJSLangConsts(){
		return '
WE().consts.g_l.fileupload = {
		btnCancel : "' . g_l('button', '[cancel][value]') . '",
		btnClose : "' . g_l('button', '[close][value]') . '",
		btnProcess: "' . g_l('importFiles', '[btnProcess]') . '",
		btnUpload : "' . g_l('button', '[upload][value]') . '",
		cancelled : "' . g_l('importFiles', '[cancelled]') . '",
		doImport : "' . g_l('importFiles', '[do_import]') . '",
		dropText : "' . g_l('importFiles', '[dragdrop_text]') . '",
		editNotEdited: "' . g_l('importFiles', '[not_edited]') . '",
		editQuality: "' . g_l('weClass', '[quality]') . '",
		editRotation: "' . g_l('importFiles', '[rotation]') . '",
		editRotationLeft: "' . g_l('global', '[left]') . '",
		editRotationRight: "' . g_l('global', '[right]') . '",
		editScaled: "' . g_l('importFiles', '[scaled_to]') . '",
		editTargetsizeTooLarge: "' . g_l('importFiles', '[targettsize_too_large]') . '",
		errorFileSize : "' . g_l('newFile', '[error_file_size]') . '",
		errorFileSizeType : "' . g_l('newFile', '[error_size_type]') . '",
		errorFileType : "' . g_l('newFile', '[error_file_type]') . '",
		errorNoFileSelected : "' . g_l('newFile', '[error_no_file]') . '",
		file : "' . g_l('importFiles', '[file]') . '",
		maskImporterProcessImages: "' . addslashes(g_l('importFiles', '[maskImporterProcessImages]')) . '",
		maskImporterReadImages: "' . addslashes(g_l('importFiles', '[maskImporterReadImages]')) . '",
		maskProcessImage: "' . g_l('importFiles', '[maskProcessImage]') . '",
		maskReadImage: "' . g_l('importFiles', '[maskReadImage]') . '",
		sizeTextNok : "' . g_l('newFile', '[file_size]') . ': &gt; ' . we_fileupload::getMaxUploadSizeMB() . ' MB, ",
		sizeTextOk : "' . g_l('newFile', '[file_size]') . ': ",
		typeTextNok : "' . g_l('newFile', '[file_type_forbidden]') . ': ",
		typeTextOk : "' . g_l('newFile', '[file_type]') . ': ",
		uploadCancelled : "' . g_l('importFiles', '[upload_cancelled]') . '",
};';
	}
}
