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
class we_fileupload_include extends we_fileupload_base{
	protected $callback = '';
	protected $formFrame = '';
	protected $drop = true;
	protected $progress = true;
	protected $isUploadComplete = false;
	protected $onclick = '';
	protected $fileNameTemp = "";
	protected $fileNameTempParts = array(
		'path' => TEMP_PATH,
		'prefix' => '',
		'postfix' => '',
		'missingDocRoot' => false,
		'useFilenameFromUpload' => false
	);
	protected $externalProgress = array(
		'isExternalProgress' => false,
		'parentElemId' => '',
		'frame' => '',
		'create' => false,
		'width' => 200,
		'name' => '',
		'additionalParams' => array()
	);
	protected $typeCondition = array(
		'accepted' => array(
			'mime' => array(),
			//'mimeGroups' => array(),
			'extensions' => array(),
			'all' => array()
		),
		'forbidden' => array(
			'mime' => array(),
			//'mimeGroups' => array(),
			'extensions' => array(),
			'all' => array()
		)
	);

	const GET_PATH_ONLY = 1;
	const GET_NAME_ONLY = 2;
	const FORCE_DOC_ROOT = true;
	const MISSING_DOC_ROOT = true;
	const USE_FILENAME_FROM_UPLOAD = true;
	const USE_LEGACY_FOR_BACKUP = true;
	const USE_LEGACY_FOR_WEIMPORT = true;

	public function __construct($name, $formFrame = 'top', $onclick = '', $width = 400, $drop = true, $progress = true, $acceptedMime = '', $acceptedExt = '', $forbiddenMime = '', $forbiddenExt = '', $externalProgress = array(), $maxUploadSize = -1){
		parent::__construct($name, $width, $maxUploadSize);

		$this->formFrame = $formFrame;
		$this->onclick = $onclick;
		$this->drop = $drop;
		$this->progress = $progress;
		$this->setTypeCondition('accepted', $acceptedMime, $acceptedExt);
		$this->setTypeCondition('forbidden', $forbiddenMime, $forbiddenExt);
		$this->typeConditionJson = json_encode($this->typeCondition);
		$this->externalProgress = $externalProgress ? $externalProgress : $this->externalProgress;
		$this->setDimensions(array('marginTop' => 6));
	}

	public function setFileNameTemp($parts = array(), $useFilenameFromUpload = false){
		$this->fileNameTempParts = array(
			'prefix' => isset($parts['prefix']) ? $parts['prefix'] : $this->fileNameTempParts['prefix'],
			'postfix' => isset($parts['postfix']) ? $parts['postfix'] : $this->fileNameTempParts['postfix'],
			'path' => isset($parts['path']) ? $parts['path'] : $this->fileNameTempParts['path'],
			'missingDocRoot' => isset($parts['missingDocRoot']) ? $parts['missingDocRoot'] : $this->fileNameTempParts['missingDocRoot'],
			'useFilenameFromUpload' => $useFilenameFromUpload
		);
	}

	public function setTypeCondition($field, $mime = '', $ext = ''){
		$tmp = array();
		$mime = trim(str_replace(' ', '', strtolower($mime)), ', ');
		$ext = trim(str_replace(' ', '', strtolower($ext)), ', ');

		$tmp['mime'] = $mime ? explode(',', $mime) : array();
		$tmp['ext'] = $ext ? explode(',', $ext) : array();
		$tmp['all'] = array_merge($tmp['mime'], $tmp['ext']);

		$this->typeCondition[$field] = $tmp;
	}

	public function setCallback($callback){
		$this->callback = $callback;
	}

	public function setExternalProgressbar($isExternalProgress, $parentElemId = '', $create = false, $frame = '', $width = 100, $name = '', $additionalParams = array()){
		$this->externalProgress['isExternalProgress'] = $isExternalProgress;
		$this->externalProgress['parentElemId'] = $parentElemId;
		$this->externalProgress['create'] = $create;
		$this->externalProgress['frame'] = $frame;
		$this->externalProgress['width'] = $width;
		$this->externalProgress['name'] = $name;
		$this->externalProgress['additionalParams'] = $additionalParams;
	}

	public function setFormFrame($frame = 'top'){
		$this->formFrame = $frame;
	}

	public function setUseDragAndDrop($useDragAndDrop = true){
		$this->drop = $useDragAndDrop;
	}

	//TODO: split and move selector to base
	public function getHTML(){
		$butBrowse = str_replace(array("\n", "\r"), ' ', we_html_button::create_button('browse_harddisk', 'javascript:alert("clicked")', true, ($this->dimensions['width'] - 103), 22));

		$butReset = str_replace(array("\n", "\r"), " ", we_html_button::create_button('reset', 'javascript:weFU.reset()', true, 100, 22, '', '', false));

		$fileInput = we_html_element::htmlInput(array(
				'class' => 'fileInput fileInputHidden',
				'type' => 'file',
				'name' => $this->name,
				'id' => $this->name,
				'accept' => implode(',', $this->typeCondition['accepted']['mime']))
		);

		return !$this->useLegacy ? ('
<div id="div_' . $this->name . '_legacy" style="display:none">' . we_html_element::htmlInput(array('type' => 'file', 'name' => $this->name . '_legacy', 'id' => $this->name . '_legacy')) . '</div>
<div id="div_' . $this->name . '" style="float:left;margin-top:' . $this->dimensions['marginTop'] . 'px;margin-bottomp:' . $this->dimensions['marginBottom'] . 'px;">
	<div>
		<div id="div_' . $this->name . '_fileInputWrapper" style="vertical-align:top;display:inline-block;height:22px; ">
			' . $fileInput . '
			' . $butBrowse . '
		</div>
		<div style="vertical-align: top; display: inline-block; height: 22px">
			' . $butReset . '
		</div>
		' . ($this->drop ? '<div id="div_' . $this->name . '_fileDrag">' . g_l('importFiles', "[dragdrop_text]") . '</div>' : '
			<div id="div_' . $this->name . '_fileName" style="height:26px;padding-top:10px;display:none"></div>
		') . '
		<div style="display:block;">
			<div id="div_' . $this->name . '_message" style="height:26px;font-size:12px;">
				&nbsp;
			</div>
			' . ($this->progress ? '<div id="div_' . $this->name . '_progress" style="height:26px;display: none">' . $this->getProgressHTML() . '</div>' : '') . '
		</div>
	</div>
</div>
' .
			we_html_tools::hidden('weFileNameTemp', '') .
			we_html_tools::hidden('weFileName', '') .
			we_html_tools::hidden('weFileCt', '') .
			we_html_tools::hidden('weIsUploadComplete', 0) .
			we_html_tools::hidden('weIsUploading', 1) .
			we_html_tools::hidden('weIsFileInLegacy', 0)) : (
			we_html_element::htmlInput(array('type' => 'file', 'name' => $this->name)));
	}

	private function getProgressHTML(){
		return '
<table cellpadding="0" style="border-spacing: 0px;border-style:none;"><tbody><tr>
	<td valign="bottom" width="2"></td>
	<td valign="middle"><img width="0" height="10" src="/webEdition/images/balken.gif" name="' . $this->name . '_progress_image" valign="top"></td>
	<td valign="middle"><img width="' . $this->dimensions['progressWidth'] . '" height="10" src="/webEdition/images/balken_bg.gif" name="' . $this->name . '_progress_image_bg" valign="top"></td>
	<td valign="bottom" width="8"></td>
	<td class="small" style="color:#006699;font-weight:bold">
		<span id="span_' . $this->name . '_progress_text">0%</span><span class="small" id="span_' . $this->name . '_progress_more_text" style="color:#006699;font-weight:bold"></span>
	</td>

	<td width="14" valign="bottom"></td>
</tr></tbody></table>
';
	}

	public function getFileNameTemp(){
		return $this->fileNameTemp;
	}

	private function _makeFileNameTemp($type = 0, $forceDocRoot = false){
		$docRoot = $forceDocRoot && $this->fileNameTempParts['missingDocRoot'] ? $_SERVER['DOCUMENT_ROOT'] : '';
		$filename = !$this->fileNameTempParts['useFilenameFromUpload'] ? $this->fileNameTempParts['prefix'] . we_base_file::getUniqueId() . $this->fileNameTempParts['postfix']
				:
			($_FILES[$this->name] && $_FILES[$this->name]['name'] ? $_FILES[$this->name]['name'] : we_base_request::_(we_base_request::STRING, 'weFileName', ''));

		if(!$filename){
			//return false;
		}

		switch($type){
			case self::GET_NAME_ONLY:
				return $filename;
			case self::GET_PATH_ONLY:
				return $docRoot . $this->fileNameTempParts['path'];
			default:
				return $docRoot . $this->fileNameTempParts['path'] . $filename;
		}
	}

	protected function _getInitJS(){
		if($this->useLegacy){
			return we_html_element::jsElement('
function weFU(){};
weFU();
weFU.legacyMode = true;
			');
		}

		$isExternalProgress = $this->externalProgress['isExternalProgress'] && $this->externalProgress['parentElemId'] ? true : false;
		$createExternalProgress = $isExternalProgress && $this->externalProgress['create'] ? true : false;
		if($createExternalProgress){
			$pb = new we_progressBar();
			$pb->setName($this->externalProgress['name']);
			$pb->setStudLen($this->externalProgress['width']);
		}

		//return we_html_element::jsScript(JS_DIR . 'utils/spark-md5.js') . we_html_element::jsElement('
		return we_html_element::jsElement('
function weFU(){
	//vars to be set oninit
	var legacyMode = true,
		typeCondition = null,
		maxUploadSize = 0,
		useFileDrag = false,
		isExternalProgress = false,
		elems = {},
		chunkSize = 0,
		form = null,
		action = "' . $this->action . '",
		callback = null,
		error = "";

	//vars to be set when using updater
	var originalFiles = null,
		preparedFiles = null,
		currentFile = null; //will PreparedFile object, elem of peraparedFiles

	window.addEventListener("load", function () {
		var xhrTestObj = new XMLHttpRequest(),
			xhrTest = xhrTestObj && xhrTestObj.upload ? true : false;
		if (xhrTest && window.File && window.FileReader && window.FileList && window.Blob) {
			weFU.legacyMode = false;
			_init();
		} else {
			var fileselect = document.getElementById("' . $this->name . '"),
				fileselectLegacy = document.getElementById("' . $this->name . '_legacy"),
				alertbox = document.getElementById("div_' . $this->name . '_alert"),
				alertboxLegacy = document.getElementById("div_' . $this->name . '_alert_legacy");

			fileselect.id = fileselect.name = "' . $this->name . '_alt";
			fileselectLegacy.id = fileselectLegacy.name = "' . $this->name . '";
			document.getElementById("div_' . $this->name . '").style.display = "none";
			document.getElementById("div_' . $this->name . '_legacy").style.display = "";
			if(typeof alertbox !== "undefined" && typeof alertboxLegacy !== "undefined"){
				alertbox.style.display = "none";
				alertboxLegacy.style.display = "";
			}
			weFU.legacyMode = true;
			' . $this->formFrame . '.document.forms[0].weIsFileInLegacy.value = 1;
			' . $this->formFrame . '.document.forms[0].weIsUploading.value = 0;
			//FIXME: change state of hidden fiels weIsFileInLegacy and weIsUploading
		}
	});

	function _init(){
		weFU.typeCondition = JSON.parse(\'' . $this->typeConditionJson . '\');
		weFU.maxUploadSize = ' . $this->maxUploadSizeBytes . ';
		weFU.chunkSize = ' . self::CHUNK_SIZE . '*1024,
		weFU.action = "' . $this->action . '",
		weFU.elems = {
			message : document.getElementById("div_' . $this->name . '_message"),
			progress : document.getElementById("div_' . $this->name . '_progress"),
			progressText : document.getElementById("span_' . $this->name . '_progress_text"),
			progressMoreText : document.getElementById("span_' . $this->name . '_progress_more_text"),
			fileDrag : document.getElementById("div_' . $this->name . '_fileDrag"),
			fileName : document.getElementById("div_' . $this->name . '_fileName"),
			fileSelect : document.getElementById("' . $this->name . '"),
			fileInputWrapper : document.getElementById("div_' . $this->name . '_fileInputWrapper")
		}

		weFU.preparedFiles = new Array(),

		weFU.elems.fileSelect.addEventListener("change", weFU.fileSelectHandler, false);
		weFU.elems.fileInputWrapper.addEventListener("click", function(){' . $this->onclick . '}, false);
		if(weFU.elems.fileDrag){
			weFU.useFileDrag = true;
			weFU.elems.fileDrag.addEventListener("dragover", weFU.fileDragHover, false);
			weFU.elems.fileDrag.addEventListener("dragleave", weFU.fileDragHover, false);
			weFU.elems.fileDrag.addEventListener("drop", weFU.fileSelectHandler, false);
			weFU.elems.fileDrag.style.display = "block";
		}

		' . ($createExternalProgress ?
					'
		if(' . $this->externalProgress['frame'] . 'document && ' . $this->externalProgress['frame'] . 'document.getElementById("' . $this->externalProgress['parentElemId'] . '")){
			var externalProgressDocument = ' . $this->externalProgress['frame'] . 'document;
			weFU.elems.externalProgressDiv = externalProgressDocument.getElementById("' . $this->externalProgress['parentElemId'] . '");
			weFU.elems.externalProgressDiv.innerHTML = \'' . str_replace("\n", " ", str_replace("\r", " ", $pb->getHTML())) . '\';
			weFU.isExternalProgress = true;
		}
				' : '') .
				'
	}

};

weFU();

weFU.isUploading = false;
weFU.isCancelled = false;

weFU.gl = {
	dropText : "' . g_l('importFiles', "[dragdrop_text]") . '",
	sizeTextOk: "' . g_l('newFile', '[file_size]') . ': ",
	sizeTextNok: "' . g_l('newFile', '[file_size]') . ': &gt; ' . $this->maxUploadSizeMBytes . ' MB, ",
	typeTextOk: "' . g_l('newFile', '[file_type]') . ': ",
	typeTextNok: "' . g_l('newFile', '[file_type_forbidden]') . ': ",

	errorNoFileSelected: "' . g_l('newFile', '[error_no_file]') . '",
	errorFileSize: "' . g_l('newFile', '[error_file_size]') . '",
	errorFileType: "' . g_l('newFile', '[error_file_type]') . '",
	errorFileSizeType: "' . g_l('newFile', '[error_size_type]') . '"
};
		') . ($createExternalProgress ? $pb->getJS() : '');
	}

	protected function _getSelectorJS(){
		return we_html_element::jsElement('
weFU.fileSelectHandler = function(e){
	var files = e.target.files || e.dataTransfer.files,
		file = null;
		tmpFileType = "";
		fileSizeOk = false,
		fileTypeOk = false;
		errorMsg = [
				weFU.gl.errorNoFileSelected,
				weFU.gl.errorFileSize,
				weFU.gl.errorFileType,
				weFU.gl.errorFileSizeType,
			]

	weFU.originalFiles = files;

	//the following code is specific for single file upload:
	//FIXME: pack it to an extra function and call this with weFU.files as aparam!

	newFile = {
		file: files[0],
		fileNum: 0,
		uploadConditionsOk: false,
		error: "",
		dataArray: null,
		currentPos: 0,
		partNum: 0,
		totalParts: 0,
		lastChunkSize: 0,
		currentWeightFile: 0,
		mimePHP: "none",
		fileNameTemp: ""
	}

	weFU.preparedFiles.push(newFile);
	weFU.totalWeight += newFile.size;

	if(weFU.useFileDrag){
		if(e.type == "drop"){
			e.stopPropagation();
			e.preventDefault();
			e.target.className = "";
		}
		weFU.elems.fileDrag.innerHTML = newFile.file.name;
	} else {
		weFU.elems.fileName.innerHTML = newFile.file.name;
		weFU.elems.fileName.style.display = "";
	}

	tmpFileType = newFile.file.type ? newFile.file.type : "text/plain";
	fileSizeOk = newFile.file.size <= ' . $this->maxUploadSizeBytes . ';
	fileTypeOk = weFU.checkFileType(tmpFileType, newFile.file.name);

	sizeText = fileSizeOk ? weFU.gl.sizeTextOk + weFU.weComputeSize(newFile.file.size) + ", ":
		\'<span style="color:red;">\' + weFU.gl.sizeTextNok + \'</span>\';
	typeText = fileTypeOk ? weFU.gl.typeTextOk + tmpFileType :
		\'<span style="color:red;">\' + weFU.gl.typeTextNok + tmpFileType + \'</span>\';

	weFU.elems.message.innerHTML = sizeText + typeText;
	newFile.uploadConditionsOk = fileSizeOk && fileTypeOk;
	newFile.error = errorMsg[fileSizeOk && fileTypeOk ? 0 : (!fileSizeOk && fileTypeOk ? 1 : (fileSizeOk && !fileTypeOk ? 2 : 3))];
};

weFU.fileDragHover = function(e){
	e.stopPropagation();
	e.preventDefault();
	e.target.className = (e.type == "dragover" ? "hover" : "");
	' . $this->onclick . '
};

weFU.weComputeSize = function(size){
	return size = size/1024 > 1023 ? ((size/1024)/1024).toFixed(1) + \' MB\' :
			(size/1024).toFixed(1) + \' KB\';
};

weFU.inArray = function(needle, haystack) {
	var length = haystack.length;
	for(var i = 0; i < length; i++) {
		if(haystack[i] == needle){
			return true;
		}
	}
	return false;
};

weFU.checkFileType = function(type, name){
	var ext = name.split(".").pop(),
		tc = weFU.typeCondition,
		typeGroup = type.split("/").shift() + "/*";

	//FIXME: mime- and ext-conditions are OR-conected: implement optional AND
	if(tc.accepted.mime && tc.accepted.mime.length > 0 && type == ""){
		return false;
	}
	if(tc.accepted.all && tc.accepted.all.length > 0 &&
			!weFU.inArray(type, tc.accepted.all) &&
			!weFU.inArray(typeGroup, tc.accepted.all) &&
			!weFU.inArray(ext, tc.accepted.all)){
		return false;
	}
	if(tc.forbidden.all && tc.forbidden.all.length > 0 &&
			(weFU.inArray(type, tc.forbidden.all) ||
				weFU.inArray(typeGroup, tc.forbidden.all) ||
				weFU.inArray(ext, tc.forbidden.all))){
		return false;
	}

	return true;
};

weFU.setProgressText = function(name, text){
	var div = document.getElementById("span_' . $this->name . '_" + name);
	div.innerHTML = text;
};

weFU.setProgress = function(progress){
	koef = ' . $this->dimensions['progressWidth'] . ' / 100;
	document.images["' . $this->name . '_progress_image"].width=koef*progress;
	document.images["' . $this->name . '_progress_image_bg"].width=(koef*100)-(koef*progress);
	weFU.setProgressText("progress_text", progress + "%");
};

weFU.setProgressCompleted = function(success){
	if(success){
		weFU.setProgress(100);
		//document.images["' . $this->name . '_progress_image"].src = "/webEdition/images/fileUpload/balken_gr.gif";
	} else {
		document.images["' . $this->name . '_progress_image"].src = "/webEdition/images/fileUpload/balken_red.gif";
	}
};
		');
	}

	protected function _getSenderJS_additional(){
		return we_html_element::jsElement('
weFU.prepareUpload = function(){
	//will do some of fileSelectHandlers job
};

weFU.upload = function(form, callback){
	if(weFU.legacyMode){
		callback();
		return;
	}

	weFU.totalFiles = weFU.preparedFiles.length;
	weFU.currentWeight = 0;

	weFU.form = form;
	weFU.callback = callback
	if(weFU.preparedFiles.length > 0){
		weFU.action = weFU.action ? weFU.action : form.action;
		weFU.sendNextFile();
	} else {
		weFU.processError({"from" : "gui", "msg" : weFU.gl.errorNoFileSelected});
	}
};

weFU.postProcess = function(){
	var form = weFU.form,
		callback = weFU.callback,
		cur = weFU.currentFile;

	form.elements["weFileNameTemp"].value = cur.fileNameTemp;
	form.elements["weFileCt"].value = cur.mimePHP;
	form.elements["weFileName"].value = cur.file.name;
	form.elements["weIsUploadComplete"].value = 1;
	setTimeout(function(){callback()}, 500);
};

weFU.repaintGUI = function(arg){
	switch(arg.what){
		case "chunkOK":
			var prog = (100 / weFU.currentFile.file.size) * weFU.currentFile.currentWeightFile,
				digits = weFU.currentFile.totalParts > 1000 ? 2 : (weFU.currentFile.totalParts > 100 ? 1 : 0);
			if(weFU.elems.progress){
				weFU.setProgress(prog.toFixed(digits));
			}
			if(weFU.isExternalProgress){
				setProgress' . $this->externalProgress['name'] . '(prog.toFixed(digits));
			}
			break;
		case "fileOK":
			if(weFU.elems.progress){
				weFU.setProgressCompleted(true);
			}
			if(weFU.isExternalProgress){
				setProgress' . $this->externalProgress['name'] . '(100);
			}
			break;
		case "fileNOK":
			if(weFU.elems.progress){
				weFU.setProgressCompleted(false);
			}
			break;
		case "startSendFile":
			if(weFU.elems.progress){
				weFU.elems.message.style.display = "none";
				weFU.elems.progress.style.display = "";
				weFU.elems.progressMoreText.innerHTML = " von " + weFU.weComputeSize(weFU.currentFile.file.size);
			}
			if(weFU.isExternalProgress){
				weFU.elems.externalProgressDiv.style.display = "";
			}
			break;
	}
};

weFU.processError = function(arg){
	switch(arg.from){
		case "gui":
			top.we_showMessage(arg.msg, 4, window);
		case "request":
			weFU.repaintGUI({"what" : "fileNOK"});
			weFU.reset();
	}
};

weFU.reset = function(){
	weFU.file = null;
	weFU.preparedFiles = new Array();
	weFU.uploadConditionsOk = false;
	if(weFU.elems.fileDrag){
		weFU.elems.fileDrag.innerHTML = weFU.gl.dropText;
	}
	if(weFU.elems.progress){
		weFU.elems.progress.style.display = "none";
	}
	weFU.elems.message.innerHTML = "";
	weFU.elems.message.innerHTML.display = "none";
};
		');
	}

	public function getJsSubmitCall($callback = ''){
		$callback = $callback ? $callback : ($this->callback ? $this->callback : '');
		return self::getJsSubmitCallStatic($this->formFrame, 0, $callback);
	}

	public static function getJsSubmitCallStatic($formFrame = 'top', $formName = 0, $callback = 'document.forms[0].submit()'){
		$quotes = is_int($formName) ? '' : '"';
		$call = $formFrame . '.weFU.upload(' . $formFrame . '.document.forms[' . $quotes . $formName . $quotes . '], function(){' . $callback . '})';
		return 'if(typeof ' . $formFrame . '.weFU === "undefined" || (' . $formFrame . '.weFU.legacyMode)){' . $callback . ';}else{' . $call . ';}';
	}

	public function processFileRequest($retFalseOnFinalError = false){
		$partNum = we_base_request::_(we_base_request::INT, 'wePartNum', 0);
		$partCount = we_base_request::_(we_base_request::INT, 'wePartCount', 0);
		$fileNameTemp = we_base_request::_(we_base_request::STRING, 'weFileNameTemp', '');
		$fileName = we_base_request::_(we_base_request::STRING, 'weFileName', '');
		$fileCt = we_base_request::_(we_base_request::STRING, 'weFileCt', '');

		//FIXME: do we really need so much vars for execution control?
		$isUploadComplete = we_base_request::_(we_base_request::BOOL, 'weIsUploadComplete', false);
		$isUploading = we_base_request::_(we_base_request::BOOL, 'weIsUploading', false); //FIXME: weHtmlInputFile
		$error = '';

		if($isUploading){//FIXME: change to $isHtmlInputFile
			if($partCount){
				if(isset($_FILES[$this->name]) && strlen($_FILES[$this->name]["tmp_name"])){

					$tempName = $partNum == 1 ? $this->_makeFileNameTemp(self::GET_NAME_ONLY) : we_base_file::getUniqueId();
					$tempPath = $this->_makeFileNameTemp(self::GET_PATH_ONLY, self::FORCE_DOC_ROOT);

					$error = (!$tempName ?
							'no_filename_error' :
							($this->maxChunkCount && $partNum > $this->maxChunkCount ?
								'oversized_error' :
								(!@move_uploaded_file($_FILES[$this->name]["tmp_name"], $tempPath . $tempName) ?
									'move_file_error' :
									''
								)
							)
						);

					//check mime type integrity when receiving first chunk
					if($partNum == 1 && !$error){
						if(($mime = we_base_util::getMimeType('', $tempPath . $tempName, we_base_util::MIME_BY_HEAD))){
							//IMPORTANT: finfo_file returns text/plain where FILE returns ""!
							if($mime !== $fileCt){
								//t_e("Mime type determined by finfo_file differ from type detemined by JS File", $mime, $fileCt);
							} else {
								$error = !$this->checkFileType($mime, $fileName) ? 'mime_or extension_not_ok_error' : '';
							}
						} else {
							//t_e("No Mime type could be determined by finfo_file or mime_content_type");
							//we ignore this and test against extension when file is completed
						}
					}

					if($error){
						$response = array('status' => 'failure', 'fileNameTemp' => $fileNameTemp, 'message' => $error, 'completed' => 0, 'finished' => '');
					} else {
						$fileNameTemp = $partNum == 1 ? $tempName : $fileNameTemp;
						if($partCount > 1 && $partNum > 1){
							file_put_contents($tempPath . $fileNameTemp, file_get_contents($tempPath . $tempName), FILE_APPEND);
							unlink($tempPath . $tempName);
						}
						$response = array('status' => ($partNum == $partCount ? 'success' : 'continue'), 'fileNameTemp' => $fileNameTemp, 'mimePhp' => (isset($mime) && $mime ? $mime
									: $fileCt), 'message' => '', 'completed' => ($partNum == $partCount ? 1 : 0), 'finished' => '');
					}

					echo json_encode($response);
					return false; //do not continue after return
				}
			} else {
				//all chunks are done, check integrity, set some vars and continue within editor context
				if($fileNameTemp && $isUploadComplete){

					//weFileCt could be manipulated or extension not alloud: make integrity test once again!
					if(!$this->isFileExtensionOk($fileName)){
						$error = 'extension_not_ok_error';
						$response = array('status' => 'failure', 'fileNameTemp' => $fileNameTemp, 'message' => $error, 'completed' => 1, 'finished' => '');
					}
					/* FIXME
					  if($mime = we_base_util::getMimeType('', TEMP_PATH . $fileNameTemp, we_base_util::MIME_BY_HEAD) && $mime !== $fileCt){
					  $message = 'mime != weFileCT_error';
					  $response = array('status' => 'failure', 'fileNameTemp' => $fileNameTemp, 'message' => $message, 'completed' => 1, 'finished' => '');
					  }
					 */
					if($error){
						if($retFalseOnFinalError){
							return false;
						}
						echo json_encode($response);
						return false; // do not continue after return
					}

					$_FILES[$this->name] = array(
						'type' => $fileCt,
						'tmp_name' => 'notempty',
						'name' => $fileName,
						'size' => 1,
						'error' => UPLOAD_ERR_OK,
					);
					//FIXME: make some integrity test for the whole and for every chunk (md5)

					$this->fileNameTemp = $this->_makeFileNameTemp(self::GET_PATH_ONLY) . $fileNameTemp;
					return true;
				}
				return true;
			}
		} else {
			return true;
		}
	}

	private function checkFileType($mime = '', $fileName = '', $mode = ''){
		$tc = $this->typeCondition;
		$fileInfo = pathinfo($fileName);

		switch($mode){
			case 'ext':
				$ext = $fileInfo['extension'];
				$alert = !$ext ? 'Function checkFileType: params not ok' : '';
				$mime = we_base_util::extension2mime($ext);
				$mimeGroup = $mime ? substr($mime, 0, strpos($mime, '/') + 1) . '*' : false;
				break;
			case 'mime':
				$alert = !$mime ? 'Function checkFileType: params not ok' : '';
				foreach($tc['accepted']['ext'] as $e){
					$tc['accepted']['all'] = we_base_util::extension2mime($e);
				}
				foreach($tc['forbidden']['ext'] as $e){
					$tc['forbidden']['all'] = we_base_util::extension2mime($e);
				}
				$ext = false;
				$mimeGroup = substr($mime, 0, strpos($mime, '/') + 1) . '*';
				break;
			default:
				$ext = $fileInfo['extension'];
				$alert = !$ext || !$mime ? 'Function checkFileType: params not ok' : '';
				$mimeGroup = substr($mime, 0, strpos($mime, '/') + 1) . '*';
		}
		if($alert){
			//t_e($alert);
		}

		if($tc['accepted']['all']){
			if(in_array($mime, $tc['accepted']['all']) ||
				in_array($mimeGroup, $tc['accepted']['all']) ||
				in_array($ext, $tc['accepted']['all'])){
				//true
			} else {
				return false;
			}
		}

		if($tc['forbidden']['all'] &&
			(in_array($mime, $tc['forbidden']['all']) ||
			in_array($mimeGroup, $tc['forbidden']['all']) ||
			in_array($ext, $tc['forbidden']['all']))){
			return false;
		}
		return true;
	}

	private function isFileExtensionOk($fileName){
		return $this->checkFileType('', $fileName, 'ext');
	}

	private function isFileMimeOk($mime){//FIXME:unused!
		return $this->checkFileType($mime, '', 'mime');
	}

}
