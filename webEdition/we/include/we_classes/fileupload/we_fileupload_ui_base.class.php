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
	protected $form = array(
		'name' => '',
		'action' => ''
	);
	protected $externalUiElements = array(
		'contentName' => '',
		'footerName' => 'footer',
		'btnUploadName' => 'upload_btn'
	);
	protected $fileselectOnclick = '';
	protected $callback = 'document.we_form.submit()';
	protected $dimensions = array(
		'width' => 400,
		'dragWidth' => 300,
		'dragHeight' => 30,
		'inputWidth' => 170,
		'alertBoxWidth' => 0,
		'marginTop' => 0,
		'marginBottom' => 0
	);
	protected $footerName = '';
	protected $contentName = '';
	protected $disableUploadBtnOnInit = true;
	protected $internalProgress = array(
		'isInternalProgress' => false,
		'width' => 0,
	);
	protected $externalProgress = array(
		'isExternalProgress' => false,
		'parentElemId' => 'progressbar',
		'create' => true,
		'html' => '',
		'width' => 120,
		'name' => '',
		'additionalParams' => array()
	);
	protected $isPreset = false;
	protected $fileTable = '';
	protected $binDocProperties = array();
	protected $isInternalBtnUpload = false; // used in we_fileupload_inc only
	protected $location = '';
	protected $layout = 'horizontal';
	public $moreFieldsToAppend = array();
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

	public function setForm($form = array()){
		$this->form['name'] = isset($form['name']) ? $form['name'] : $this->form['name'];
		$this->form['action'] = isset($form['action']) ? $form['action'] : $this->form['action'];
		$this->form['action'] = isset($form['additionalParams']) ? $this->form['action'] . '&' . $form['additionalParams'] : $this->form['action'];
	}

	public function setCallback($callback = ''){
		$this->callback = $callback ? : 'if(document.forms["' . $this->form['name'] . '"]){document.forms["' . $this->form['name'] . '"].submit();}';
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

	public function setFileSelectOnclick($onclick = ''){
		$this->fileselectOnclick = $onclick;
	}

	public function setGenericFileName($genericFileName = ''){
		$this->genericFilename = $genericFileName ? : TEMP_DIR . self::REPLACE_BY_UNIQUEID;
	}

	public function setDimensions($dimensions = array()){
		$this->dimensions = array_merge($this->dimensions, $dimensions);
	}

	public function setDoCommitFile($doCommitFile = true){
		$this->doCommitFile = boolval($doCommitFile);
	}

	public function setInternalProgress($internalProgress = array()){
		$this->internalProgress = array_merge($this->internalProgress, $internalProgress);
	}

	public function setExternalProgress($externalProgress = array()){
		$this->externalProgress = array_merge($this->externalProgress, $externalProgress);
	}

	public function setExternalUiElements($externalUiElements = array()){
		$this->externalUiElements = array_merge($this->externalUiElements, $externalUiElements);
	}

	public function setMoreFieldsToAppend($fields = array()){
		$this->moreFieldsToAppend = array_merge($this->moreFieldsToAppend, $fields);
	}

	public function setIsInternalBtnUpload($flag = true){
		$this->isInternalBtnUpload = $flag;
	}

	public static function getExternalDropZone($name = 'we_File', $content = '', $style = '', array $contentType = array(), $callback = array(), $writebackId = '', $writebackTarget = '', $predefinedCallbackInt = '', $predefinedCallbackExt = '', $dropFromTree = true, $dropFromExt = true, $table = ''){
		// FIXME: replace all PHP in JS by JS-params (to avoid "indexed" function names for hadleDrop(), doDragFromExternal() and doDragFromTree)
		// => then move this JS to separate file to be included in edit headers once!

		if(!self::isDragAndDrop()){
			return $content;
		}
		$table = $table ? : FILE_TABLE;
		$callback = array_merge(array('external' => '', 'tree' => ''), $callback);

		// // FIXME: make the following functions more concise und move to extarnal js file
		$js = we_html_element::jsElement('
handleDragOver = function(e, name){
	if(e.preventDefault){
		e.preventDefault();
	}
	try {
		document.getElementById("div_" + name + "_fileDrag").className = "we_file_drag we_file_drag_hover";
	} catch(e){}
}

handleDragLeave = function(e, name){
	try {
		document.getElementById("div_" + name + "_fileDrag").className = "we_file_drag";
	} catch(e){}
}

handleDrop' . md5($name) . ' = function(e, writebackId, writebackTarget,divName){
	var text, files;

	try {
		document.getElementById("div_"+divName+"_fileDrag").className = "we_file_drag";
	} catch(e){}

	e.preventDefault();
	e.stopPropagation();

	if(text = e.dataTransfer.getData("text")){
		if(' . ($dropFromTree ? 'true' : 'false') . '){
			switch(text.split(",")[0]){
				case "dragItem": // drag from tree
					doDragFromTree' . md5($name) . '(text, writebackId);
					break;
				default:
					// more cases to come
			}
		} else {alert("no drop from tree here");}
	} else if(e.dataTransfer.files){
		if(' . ($dropFromExt ? 'true' : 'false') . '){
			doDragFromExternal' . md5($name) . '(e.dataTransfer.files, writebackTarget);
		} else {alert("no drop from external here");}
	}
}

doDragFromExternal' . md5($name) . ' = function(files, writebackTarget){
	document.presetFileupload = files;
	top.we_cmd("we_fileupload_editor", "' .implode(',', $contentType) . '", 1, "", writebackTarget, "' . $callback['external'] . '", 0, 0, "' . $predefinedCallbackExt . '", true);
}
doDragFromTree' . md5($name) . ' = function(text, writebackId){
	var data = text.split(",");

	cts = "' . (empty($contentType) ? '' : ',' . implode(',', $contentType)  . ',') . '";
	if(data[2] && data[1] === "' . $table . '" && (cts === "" || cts.search("," + data[3])) != -1){
		var table = data[1], id = data[2], ct = data[3], path = data[4];
		' . (strpos($callback['tree'], 'WECMDENC_') !== false ? base64_decode(urldecode(substr($callback['tree'], 9))) : $callback['tree']) . '
	}
}
');

		return we_html_element::cssLink(CSS_DIR . 'we_fileupload.css') . $js .
				we_html_element::htmlDiv(array('id' => 'div_' . $name . '_fileDrag', 'class' => 'we_file_drag', 'ondrop' => 'handleDrop' . ($name ? md5($name) : '') . '(event, \'' . $writebackId . '\', \'' . $writebackTarget . '\',\'' . $name . '\');', 'ondragover' => 'handleDragOver(event, \'' . $name . '\');', 'ondragleave' => 'handleDragLeave(event, \'' . $name . '\');', 'style' => 'margin-top:0.5em;display:' . (self::isDragAndDrop() ? 'block;' : 'none;') . $style), $content);
	}

	public function getButtonWrapped($type, $disabled = false, $width = 170, $notWrapped = false){
		switch($type){
			case 'browse':
				$isIE10 = we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11;

				$fileInput = we_html_element::htmlInput(array(
							'class' => 'fileInput fileInputHidden' . ($isIE10 ? ' fileInputIE10' : ''),
							'style' => 'width:' . $width . 'px;',
							'type' => 'file',
							'name' => $this->name,
							'id' => $this->name,
							'accept' => trim($this->typeCondition['accepted']['all'], ','),
				));
				$btn = we_html_button::create_button('fat:browse_harddisk,fa-lg fa-hdd-o', 'javascript:void(0)', true, 0, 0, '', '', $disabled, false, '_btn', false, '', 'weBtn noMarginLeft');

				return we_html_element::htmlDiv(array('id' => 'div_' . $this->name . '_fileInputWrapper', 'class' => 'we_fileInputWrapper', 'style' => 'vertical-align:top;display:inline-block;'), $fileInput . $btn
				);
			case 'reset':
				$btn = we_html_button::create_button('reset', 'javascript:we_FileUpload.reset()', true, 0, 0, '', '', $disabled, false, '_btn', true, '', 'weBtn noMarginLeft');
				return $notWrapped ? $btn : we_html_element::htmlDiv(array('id' => 'div_fileupload_btnReset', 'style' => 'height:30px;margin-top:18px;display:none;'), $btn);

			case 'upload':
				$btn = we_html_button::create_button(we_html_button::UPLOAD, 'javascript:' . $this->getJsBtnCmd('upload'), true, 0, 0, '', '', $disabled, false, '_btn', true, '', 'weBtn noMarginLeft');
				return we_html_element::htmlDiv(array('id' => 'div_fileupload_btnUpload', 'style' => 'margin-top: 4px;'), $btn);

			case 'cancel':
				$btn = we_html_button::create_button(we_html_button::CANCEL, 'javascript:' . $this->getJsBtnCmd('cancel'), true, 0, 0, '', '', $disabled, false, '_btn', true, '', 'weBtn noMarginLeft');
				return we_html_element::htmlDiv(array('id' => 'div_fileupload_btnCancel', 'style' => 'margin-top: 4px;display:none;'), $btn);
		}
	}

	public function getHTML(){
		$isIE10 = we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11;
		// FIXME: do we need thos replacements?
		$butReset = str_replace(array("\n\r", "\r\n", "\r", "\n"), ' ', $this->getButtonWrapped('reset', true, ($isIE10 ? 84 : 100), true));
		$btnUpload = str_replace(array("\n\r", "\r\n", "\r", "\n"), ' ', $this->getButtonWrapped('upload', true, ($isIE10 ? 84 : 100)));
		$btnCancel = str_replace(array("\n\r", "\r\n", "\r", "\n"), ' ', $this->getButtonWrapped('cancel', false, ($isIE10 ? 84 : 100)));

		return we_html_element::htmlDiv(array('id' => 'div_' . $this->name, 'style' => 'float:left;margin-top:' . $this->dimensions['marginTop'] . 'px;margin-bottom:' . $this->dimensions['marginBottom'] . 'px;'), we_html_element::htmlDiv(array(), $this->getButtonWrapped('browse', false, $isIE10 ? 84 : ($this->dimensions['width'] - 110)) .
								we_html_element::htmlDiv(array('id' => 'div_' . $this->name . '_btnResetUpload', 'style' => 'vertical-align: top; display: inline-block; height: 22px;'), ($this->isInternalBtnUpload ? $btnUpload : $butReset)
								) .
								($this->isInternalBtnUpload ? we_html_element::htmlDiv(array('id' => 'div_' . $this->name . '_btnCancel', 'style' => 'vertical-align: top; display: none; height: 22px;'), $btnCancel
										) : ''
								) .
								$this->getHtmlDropZone() . $this->getHtmlFileInfo()
						)
				) .
				$this->getHiddens();
	}

	protected function getHiddens(){
		return we_html_element::htmlHiddens(array(
					'weFileNameTemp' => '',
					'weFileName' => '',
					'weFileCt' => '',
		));
	}

	public function getHtmlAlertBoxes(){
		$text = $this->maxUploadSizeMBytes ? sprintf(g_l('newFile', '[size_limit_set_to]'), $this->maxUploadSizeMBytes) : g_l('newFile', '[no_size_limit]');
		$box = we_html_tools::htmlAlertAttentionBox($text, we_html_tools::TYPE_INFO, ($this->dimensions['alertBoxWidth'] ? : $this->dimensions['width']));

		return we_html_element::htmlDiv(array('id' => 'div_alert'), $box);
	}

	protected function _getHtmlFileRow(){
		return '';
	}

	protected function _getHtmlFileRow_legacy(){
		return '';
	}

	protected function getHtmlDropZone(){
		return we_html_element::htmlDiv(array('id' => 'div_' . $this->name . '_fileDrag', 'class' => 'we_file_drag', 'style' => 'margin-top:0.5em;display:' . ($this->isDragAndDrop ? 'block' : 'none')), g_l('importFiles', '[dragdrop_text]'));
	}

	protected function getHtmlFileInfo(){
		return we_html_element::htmlDiv(array('id' => 'div_' . $this->name . '_fileName', 'style' => 'height:26px;padding-top:10px;display:' . ($this->isDragAndDrop ? 'none' : 'block') . ';'), '') .
				we_html_element::htmlDiv(array('style' => 'display:block;padding:0.6em 0 0 0.2em'), we_html_element::htmlDiv(array('id' => 'div_' . $this->name . '_message', 'style' => 'height:26px;font-size:1em;'), '&nbsp;') .
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
		  $progress = new we_progressBar(0, true);
		  $progress->setStudLen(170);
		  $progress->setProgressTextPlace(0);
		  $progress->setName('_fileupload');
		  return $progress->getHTML('', 'font-size:11px;');
		  $divProgressbar = we_html_element::htmlDiv(array('id' => 'div_fileupload_progressBar', 'style' => 'margin: 13px 0 10px 0;display:none;'), $progress->getHTML('', 'font-size:11px;'));
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
		$this->externalProgress['create'] = $this->externalProgress['create'] && $this->externalProgress['isExternalProgress'] && $this->externalProgress['parentElemId'];
		if($this->externalProgress['create']){
			$progressbar = new we_progressBar();
			$progressbar->setName($this->externalProgress['name']);
			$progressbar->setStudLen($this->externalProgress['width']);
			$this->externalProgress['html'] = str_replace(array("\n\r", "\r\n", "\r", "\n"), "", $progressbar->getHTML());
		}
		$this->callback = strpos($this->callback, 'WECMDENC_') !== false ? base64_decode(urldecode(substr($this->callback, 9))) : $this->callback;

		return we_html_element::jsScript('/webEdition/js/weFileUpload.js') .
				we_html_element::jsScript('/webEdition/lib/additional/ExifReader/ExifReader.js') .
				we_html_element::jsScript('/webEdition/lib/additional/pngChunksEncode/index.js') .
				we_html_element::jsScript('/webEdition/lib/additional/pngChunksExtract/index.js') .
				we_html_element::jsScript('/webEdition/lib/additional/pngChunksExtract/crc32.js') .
				we_html_element::jsScript('/webEdition/lib/additional/pica/pica.js') .
				we_html_element::jsElement('
we_FileUpload = new weFileUpload("' . $this->type . '");
we_FileUpload.init({
	uiType : "' . $this->type . '",
	fieldName : "' . $this->name . '",
	genericFilename : ' . json_encode($this->genericFilename) . ',
	doCommitFile: ' . ($this->doCommitFile ? 'true' : 'false') . ',
	form : ' . json_encode($this->form) . ',
	footerName : "' . $this->footerName . '",
	uploadBtnName : "' . $this->externalUiElements['btnUploadName'] . '",
	maxUploadSize : ' . $this->maxUploadSizeBytes . ',
	typeCondition : ' . str_replace(array("\n\r", "\r\n", "\r", "\n"), "", json_encode($this->typeCondition)) . ',
	isDragAndDrop : ' . ($this->isDragAndDrop ? 'true' : 'false') . ',
	isPreset: ' . ($this->isPreset ? 'true' : 'false') . ',
	callback : function(scope){' . $this->callback . '},
	fileselectOnclick : function(){' . $this->fileselectOnclick . '},
	chunkSize : ' . self::CHUNK_SIZE . ',
	intProgress : ' . json_encode($this->internalProgress) . ',
	extProgress : ' . json_encode($this->externalProgress) . ',
	gl: ' . $this->_getJsGl() . ',
	isGdOk : ' . ($this->isGdOk ? 'true' : 'false') . ',
	htmlFileRow : \'' . (we_fileupload::EDIT_IMAGES_CLIENTSIDE ? $this->_getHtmlFileRow() : $this->_getHtmlFileRow_legacy()) . '\',
	fileTable : "' . $this->fileTable . '",
	binDocProperties : ' . json_encode($this->binDocProperties) . ',
	disableUploadBtnOnInit : ' . ($this->disableUploadBtnOnInit ? 'true' : 'false') . ',
	moreFieldsToAppend : ' . json_encode($this->moreFieldsToAppend) . ',
	isInternalBtnUpload : ' . ($this->isInternalBtnUpload ? 'true' : 'false') . ',
	responseClass : "' . $this->responseClass . '",
	clientsideImageEditing : ' . ($this->cliensideImageEditing && we_fileupload::EDIT_IMAGES_CLIENTSIDE ? 1 : 0) . '
});
			') . ($this->externalProgress['create'] ? $progressbar->getJSCode() : '');
	}

	protected function _getJsGl(){
		return '{
		dropText : "' . g_l('importFiles', '[dragdrop_text]') . '",
		sizeTextOk : "' . g_l('newFile', '[file_size]') . ': ",
		sizeTextNok : "' . g_l('newFile', '[file_size]') . ': &gt; ' . $this->maxUploadSizeMBytes . ' MB, ",
		typeTextOk : "' . g_l('newFile', '[file_type]') . ': ",
		typeTextNok : "' . g_l('newFile', '[file_type_forbidden]') . ': ",
		errorNoFileSelected : "' . g_l('newFile', '[error_no_file]') . '",
		errorFileSize : "' . g_l('newFile', '[error_file_size]') . '",
		errorFileType : "' . g_l('newFile', '[error_file_type]') . '",
		errorFileSizeType : "' . g_l('newFile', '[error_size_type]') . '",
		uploadCancelled : "' . g_l('importFiles', '[upload_cancelled]') . '",
		cancelled : "' . g_l('importFiles', '[cancelled]') . '",
		doImport : "' . g_l('importFiles', '[do_import]') . '",
		file : "' . g_l('importFiles', '[file]') . '",
		btnClose : "' . g_l('button', '[close][value]') . '",
		btnCancel : "' . g_l('button', '[cancel][value]') . '",
		btnUpload : "' . g_l('button', '[upload][value]') . '",
		btnProcess: "' . g_l('importFiles', '[btnProcess]') . '",
		maskReadImage: "' . g_l('importFiles', '[maskReadImage]') . '",
		maskProcessImage: "' . g_l('importFiles', '[maskProcessImage]') . '",
		maskImporterReadImages: "' . addslashes(g_l('importFiles', '[maskImporterReadImages]')) . '",
		maskImporterProcessImages: "' . addslashes(g_l('importFiles', '[maskImporterProcessImages]')) . '",
		editScaled: "' . g_l('importFiles', '[scaled_to]') . '",
		editRotation: "' . g_l('importFiles', '[rotation]') . '",
		editRotationLeft: "' . g_l('global', '[left]') . '",
		editRotationRight: "' . g_l('global', '[right]') . '",
		editQuality: "' . g_l('weClass', '[quality]') . '",
		editNotEdited: "' . g_l('importFiles', '[not_edited]') . '",
		editTargetsizeTooLarge: "' . g_l('importFiles', '[targettsize_too_large]') . '"
}';
	}

	public function getJsBtnCmd($btn = 'upload'){
		return self::getJsBtnCmdStatic($btn, $this->externalUiElements['contentName'], $this->callback);
	}

	public static function getJsBtnCmdStatic($btn = 'upload', $contentName = '', $callback = ''){
		//FIXME: still need direct callback
		$win = $contentName ? 'top.' . $contentName . '.' : '';
		$callback = $btn === 'upload' ? ($callback ? : 'document.forms[0].submit()') : 'top.close()';
		$call = $win . 'we_FileUpload.' . ($btn === 'upload' ? 'startUpload()' : 'cancelUpload()');

		return 'if(' . $win . 'we_FileUpload === undefined){' . $callback . ';}else{' . $call . ';}';
	}

}
