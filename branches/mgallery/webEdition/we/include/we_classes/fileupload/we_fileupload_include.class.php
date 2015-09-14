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
	protected $isUploadComplete = false;
	protected $fileNameTemp = "";
	protected $fileNameTempParts = array(
		'path' => TEMP_PATH,
		'prefix' => '',
		'postfix' => '',
		'missingDocRoot' => false,
		'useFilenameFromUpload' => false
	);

	const GET_PATH_ONLY = 1;
	const GET_NAME_ONLY = 2;
	const FORCE_DOC_ROOT = true;
	const MISSING_DOC_ROOT = true;
	const USE_FILENAME_FROM_UPLOAD = true;
	const USE_LEGACY_FOR_BACKUP = false;
	const USE_LEGACY_FOR_WEIMPORT = false;

	public function __construct($name, $contentName = '', $footerName = '', $formName = '', $uploadBtnName = '', $disableUploadBtnOnInit = true, $callback = 'document.forms[0].submit()', $fileselectOnclick = '', $width = 400, $isDragAndDrop = true, $isInternalProgress = false, $internalProgressWidth = 0, $acceptedMime = '', $acceptedExt = '', $forbiddenMime = '', $forbiddenExt = '', $externalProgress = array(), $maxUploadSize = -1, $formAction = ''){
		parent::__construct($name, $width, $maxUploadSize, $isDragAndDrop);
		$this->type = 'inc';
		$this->contentName = $contentName;
		$this->footerName = $footerName;
		$this->uploadBtnName = $uploadBtnName ? : 'upload_btn';
		$this->disableUploadBtnOnInit = $disableUploadBtnOnInit;
		$this->form['name'] = $formName;
		$this->form['action'] = $formAction;
		$this->callback = $callback;
		$this->fileselectOnclick = $fileselectOnclick;
		$this->internalProgress['isInternalProgress'] = $isInternalProgress;
		$this->internalProgress['width'] = $internalProgressWidth;
		$this->setTypeCondition('accepted', $acceptedMime, $acceptedExt);
		$this->setTypeCondition('forbidden', $forbiddenMime, $forbiddenExt);
		$this->externalProgress = $externalProgress ? : $this->externalProgress;
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

	public function setInternalProgress($args = array()){
		$this->internalProgress = array(
			'isInternalProgress' => isset($args['isInternalProgress']) ? $args['isInternalProgress'] : $this->internalProgress['isInternalProgress'],
			'width' => isset($args['width']) ? $args['width'] : $this->internalProgress['width'],
		);
	}

	public function setMoreFieldsToAppend($fields = array()){
		$this->moreFieldsToAppend = array_merge($this->moreFieldsToAppend, $fields);
	}

	public function setExternalProgress($isExternalProgress, $parentElemId = '', $create = false, $width = 100, $name = '', $additionalParams = array()){
		$this->externalProgress['isExternalProgress'] = $isExternalProgress;
		$this->externalProgress['parentElemId'] = $parentElemId;
		$this->externalProgress['create'] = $create;
		$this->externalProgress['width'] = $width;
		$this->externalProgress['name'] = $name;
		$this->externalProgress['additionalParams'] = $additionalParams;
	}

	public function setIsDragAndDrop($isDragAndDrop = true){
		$this->isDragAndDrop = $isDragAndDrop;
	}

	public function setIsInternalBtnUpload($flag = true){
		$this->isInternalBtnUpload = $flag;
	}

	public function setIsExtraBtnReset($flag = true){
		//$this->IsSingleBtnReset = $flag;
	}

	//TODO: split and move selector to base
	public function getHTML(){
		$isIE10 = we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11;
		$butBrowse = str_replace(array("\n\r", "\r\n", "\r", "\n"), ' ', $isIE10 ? we_html_button::create_button('fat:browse_harddisk,fa-lg fa-hdd-o', 'javascript:void(0)', true, 84, we_html_button::HEIGHT, '', '', false, false, '_btn') :
				we_html_button::create_button('fat:browse_harddisk,fa-lg fa-hdd-o', 'javascript:void(0)', true, ($this->dimensions['width'] - 110), we_html_button::HEIGHT, '', '', false, false, '_btn'));

		$butReset = str_replace(array("\n\r", "\r\n", "\r", "\n"), ' ', we_html_button::create_button('reset', 'javascript:we_FileUpload.reset()', true, ($isIE10 ? 84 : 100), we_html_button::HEIGHT, '', '', true, false, '_btn'));
		$btnUpload = str_replace(array("\n\r", "\r\n", "\r", "\n"), ' ', we_html_button::create_button('upload', 'javascript:' . $this->getJsBtnCmd(), true, ($isIE10 ? 84 : 100), we_html_button::HEIGHT, '', '', true, false, '_btn'));
		$btnCancel = str_replace(array("\n\r", "\r\n", "\r", "\n"), ' ', we_html_button::create_button('cancel', 'javascript:' . $this->getJsBtnCmd('cancel'), true, ($isIE10 ? 84 : 100), we_html_button::HEIGHT, '', '', false, false, '_btn'));

		$fileInput = we_html_element::htmlInput(array(
				'class' => 'fileInput fileInputHidden' . ($isIE10 ? ' fileInputIE10' : ''),
				'type' => 'file',
				'name' => $this->name,
				'id' => $this->name,
				'accept' => implode(',', $this->typeCondition['accepted']['mime']))
		);

		return (self::isFallback() || self::isLegacyMode()) ?
			( we_html_element::htmlInput(array('type' => 'file', 'name' => $this->name))) :
			('
<div id="div_' . $this->name . '_legacy" style="display:none">' . we_html_element::htmlInput(array('type' => 'file', 'name' => $this->name . '_legacy', 'id' => $this->name . '_legacy')) . '</div>
<div id="div_' . $this->name . '" style="float:left;margin-top:' . $this->dimensions['marginTop'] . 'px;margin-bottom:' . $this->dimensions['marginBottom'] . 'px;">
	<div>
		<div class="we_fileInputWrapper" id="div_' . $this->name . '_fileInputWrapper" style="vertical-align:top;display:inline-block;height:26px; ">
			' . $fileInput . '
			' . $butBrowse . '
		</div>
		<div id="div_' . $this->name . '_btnResetUpload" style="vertical-align: top; display: inline-block; height: 22px">
			' . ($this->isInternalBtnUpload ? $btnUpload : $butReset) . '
		</div>' .
		($this->isInternalBtnUpload ? 
			'<div id="div_' . $this->name . '_btnCancel" div style="vertical-align: top; display: none; height: 22px">
			' . $btnCancel . '
			</div>' : ''
		) .
		'<div class="we_file_drag" id="div_' . $this->name . '_fileDrag" style="margin-top:0.5em;display:' . ($this->isDragAndDrop ? 'block' : 'none') . '">' . g_l('importFiles', '[dragdrop_text]') . '</div>
		<div id="div_' . $this->name . '_fileName" style="height:26px;padding-top:10px;display:' . ($this->isDragAndDrop ? 'none' : 'block') . '"></div>
		<div style="display:block;padding:0.6em 0 0 0.2em">
			<div id="div_' . $this->name . '_message" style="height:26px;font-size:12px;">
				&nbsp;
			</div>
			' . ($this->internalProgress['isInternalProgress'] ? '<div id="div_' . $this->name . '_progress" style="height:26px;display: none">' . $this->_getProgressHTML() . '</div>' : '') . '
		</div>
	</div>
</div>
' .
			we_html_tools::hidden('weFileNameTemp', '') .
			we_html_tools::hidden('weFileName', '') .
			we_html_tools::hidden('weFileCt', '') .
			we_html_tools::hidden('weIsUploadComplete', 0) .
			we_html_tools::hidden('weIsUploading', 1) .
			we_html_tools::hidden('weIsFileInLegacy', 0));
	}

	//FIXME: base intarnal progress on we_progress
	private function _getProgressHTML(){
		return '
<table class="default"><tbody><tr>
	<td style="vertical-align:bottom" width="2"></td>
	<td style="vertical-align:middle"><div class="progress_image" style="width:0px;height:10px;" id="' . $this->name . '_progress_image" style="vertical-align:top"></div><div class="progress_image_bg" style="width:' . $this->internalProgress['width'] . 'px;height:10px;" id="' . $this->name . '_progress_image_bg" style="vertical-align:top"></div></td>
	<td style="vertical-align:bottom" width="8"></td>
	<td class="small" style="color:#006699;font-weight:bold">
		<span id="span_' . $this->name . '_progress_text">0%</span><span class="small" id="span_' . $this->name . '_progress_more_text" style="color:#006699;font-weight:bold"></span>
	</td>
	<td width="14" style="vertical-align:bottom"></td>
</tr></tbody></table>
';
	}

	public function getFileNameTemp(){
		return $this->fileNameTemp;
	}

	private function _makeFileNameTemp($type = 0, $forceDocRoot = false){
		$docRoot = $forceDocRoot && $this->fileNameTempParts['missingDocRoot'] ? $_SERVER['DOCUMENT_ROOT'] : '';
		$filename = !$this->fileNameTempParts['useFilenameFromUpload'] ? $this->fileNameTempParts['prefix'] . we_base_file::getUniqueId() . $this->fileNameTempParts['postfix'] :
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

	public function getJsBtnCmd($btn = 'upload'){
		return self::getJsBtnCmdStatic($btn, $this->contentName, $this->callback);
	}

	public static function getJsBtnCmdStatic($btn = 'upload', $contentName = '', $callback = ''){
		$win = $contentName ? 'top.' . $contentName . '.' : '';
		$callback = $btn === 'upload' ? ($callback ? : 'document.forms[0].submit()') : 'top.close()';
		$call = $win . 'we_FileUpload.' . ($btn === 'upload' ? 'startUpload()' : 'cancelUpload()');

		return 'if(' . $win . 'we_FileUpload === undefined || ' . $win . 'we_FileUpload.isLegacyMode){' . $callback . ';}else{' . $call . ';}';
	}

	public function processFileRequest($retFalseOnFinalError = false){
		$partNum = we_base_request::_(we_base_request::INT, 'wePartNum', 0);
		$partCount = we_base_request::_(we_base_request::INT, 'wePartCount', 0);
		$fileNameTemp = we_base_request::_(we_base_request::STRING, 'weFileNameTemp', '');
		$fileName = we_base_request::_(we_base_request::STRING, 'weFileName', '');
		$fileSize = we_base_request::_(we_base_request::INT, "weFileSize", 1);
		$fileCt = we_base_request::_(we_base_request::STRING, 'weFileCt', '');

		//FIXME: do we really need so much vars for execution control?
		$isUploadComplete = we_base_request::_(we_base_request::BOOL, 'weIsUploadComplete', false);
		$isUploading = we_base_request::_(we_base_request::BOOL, 'weIsUploading', false); //FIXME: weHtmlInputFile
		$error = '';

		if($isUploading){//FIXME: change to $isHtmlInputFile
			if($partCount){
				if(isset($_FILES[$this->name]) && $_FILES[$this->name]['tmp_name']){

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
								$error = !$this->_checkFileType($mime, $fileName) ? 'mime_or extension_not_ok_error' : '';
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
						$response = array('status' => ($partNum == $partCount ? 'success' : 'continue'), 'fileNameTemp' => $fileNameTemp, 'mimePhp' => (!empty($mime) ? $mime : $fileCt), 'message' => '', 'completed' => ($partNum == $partCount ? 1 : 0), 'finished' => '');
					}

					echo json_encode($response);
					return false; //do not continue after return
				}
			} else {
				//all chunks are done, check integrity, set some vars and continue within editor context
				if($fileNameTemp && $isUploadComplete){

					//weFileCt could be manipulated or extension not alloud: make integrity test once again!
					if(!$this->_isFileExtensionOk($fileName)){
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
						'size' => $fileSize,
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

	private function _checkFileType($mime = '', $fileName = '', $mode = ''){
		$tc = $this->typeCondition;
		$fileInfo = pathinfo($fileName);

		switch($mode){
			case 'ext':
				$ext = $fileInfo['extension'];
				$alert = !$ext ? 'Function _checkFileType: params not ok' : '';
				$mime = we_base_util::extension2mime($ext);
				$mimeGroup = $mime ? substr($mime, 0, strpos($mime, '/') + 1) . '*' : false;
				break;
			case 'mime':
				$alert = !$mime ? 'Function _checkFileType: params not ok' : '';
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
				$alert = !$ext || !$mime ? 'Function _checkFileType: params not ok' : '';
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

	private function _isFileExtensionOk($fileName){
		return $this->_checkFileType('', $fileName, 'ext');
	}

	private function _isFileMimeOk($mime){//FIXME:unused!
		return $this->_checkFileType($mime, '', 'mime');
	}

}
