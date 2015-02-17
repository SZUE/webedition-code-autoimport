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
class we_fileupload_binaryDocument extends we_fileupload_base{
	protected $isUploadComplete = false;
	protected $fileNameTemp = "";
	protected $fileNameTempParts = array(
		'path' => TEMP_PATH,
		'prefix' => '',
		'postfix' => '',
		'missingDocRoot' => false,
		'useFilenameFromUpload' => false
	);
	private $transaction;
	private $contentType;
	private $extension;

	const GET_PATH_ONLY = 1;
	const GET_NAME_ONLY = 2;

	public function __construct($contentType = '', $extension = ''){
		parent::__construct('we_File', 200, -1, true);
		$this->type = 'binDoc';
		$this->name = 'we_File';
		$this->contentType = $contentType;
		$this->extension = $extension;
		$this->uploadBtnName = 'upload_btn';
		$this->form['name'] = 'we_form';
		$this->internalProgress['isInternalProgress'] = true;
		$this->internalProgress['width'] = 170;
		$this->setTypeCondition();
		$this->setDimensions(array('dragHeight' => 116));
		$this->binDocProperties = $this->getDocProperties();
		$this->dimensions['alertBoxWidth'] = 507;
	}

	public function setTypeCondition(){
		switch($this->contentType){
			case we_base_ContentTypes::IMAGE;
				$mime = implode(',', we_base_ContentTypes::inst()->getRealContentTypes($this->contentType));
				$ext = we_base_imageEdit::IMAGE_EXTENSIONS;
				break;
			case we_base_ContentTypes::VIDEO:
				$mime = we_base_util::mimegroup2mimes(we_base_ContentTypes::VIDEO);
				$ext = implode(',', we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::VIDEO));
			case we_base_ContentTypes::AUDIO:
				$mime = we_base_util::mimegroup2mimes(we_base_ContentTypes::AUDIO);
				$ext = implode(',', we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::AUDIO));
			case we_base_ContentTypes::APPLICATION;
				$mime = '';
				$ext = '';
				break;
			default:
				$mime = $this->contentType;
				$ext = '';
		}

		$mime = trim(str_replace(' ', '', strtolower($mime)), ', ');
		$ext = trim(str_replace(' ', '', strtolower($ext)), ', ');

		$tmp = array(
			'mime' => $mime ? explode(',', $mime) : array(),
			'ext' => $ext ? explode(',', $ext) : array(),
		);
		$tmp['all'] = array_merge($tmp['mime'], $tmp['ext']);

		$this->typeCondition['accepted'] = $tmp;
	}

	private function getDocProperties(){
		switch($this->contentType){
			case we_base_ContentTypes::IMAGE;
				return array('type' => 'image', 'icon' => ICON_DIR . 'no_image.gif');
			case we_base_ContentTypes::FLASH;
				return array('type' => 'flash', 'icon' => ICON_DIR . 'no_flashmovie.gif');
			case we_base_ContentTypes::VIDEO;
				return array('type' => 'video', 'icon' => ICON_DIR . 'doclist/video.svg');
			case we_base_ContentTypes::AUDIO;
				return array('type' => 'audio', 'icon' => ICON_DIR . 'doclist/audio.svg');
			case we_base_ContentTypes::QUICKTIME;
				return array('type' => 'quicktime', 'icon' => ICON_DIR . 'no_quicktime.gif');
			default:
				return array('type' => 'other', 'icon' => ICON_DIR . 'doc.gif');
		}
	}

	/* 	private function getType(){
	  switch($this->contentType){
	  case we_base_ContentTypes::IMAGE;
	  return IMAGE_DIR . 'image';
	  case we_base_ContentTypes::FLASH;
	  return IMAGE_DIR . 'flash';
	  break;
	  case we_base_ContentTypes::QUICKTIME;
	  return IMAGE_DIR . 'quicktime';
	  default:
	  //$mime = $this->contentType;
	  //$ext = '';
	  return IMAGE_DIR . 'other';
	  }
	  }
	 */

	public function getCss(){

		return self::isFallback() || self::isLegacyMode() ? '' : we_html_element::cssLink(CSS_DIR . 'we_fileupload.css') . we_html_element::cssElement('
div.we_file_drag{
	width: 300px;
	border-radius: 0px;
	border: dotted 2px gray;
	height: 116px;
}
div.we_file_drag_binDoc{
	float:left;
	margin: 1px 0 10px 0;
	border: solid 2px #ededed;
}
div.we_file_drag_hover{
	color: #00cc00;
	border: 2px solid #00cc00;
	box-shadow: inset 0 3px 4px #888;
	background-color: rgb(243, 247, 255);
}
div.dropzone_right{
	display:table-cell;
	height:116px;
	width:120px;
	vertical-align:middle;
	text-align:center;
}
.dropzone_right img{
	max-height: 100px;
	max-width: 100px;
}
.fileInputIE10{
	left: 0;
}');
	}

	public function getHTML($fs = '', $ft = '', $md = '', $thumbnailSmall = '', $thumbnailBig = ''){
		$isIE10 = we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11 ? true : false;
		$width = $isIE10 ? array('input' => 84, 'button' => 168) : array('input' => 170, 'button' => 170);
		$dropText = g_l('newFile', $this->isDragAndDrop ? '[drop_text_ok]' : '[drop_text_nok]');

		$btnBrowse = we_html_button::create_button('browse_harddisk', 'javascript:void(0)', true, $width['button'], we_html_button::HEIGHT, '', '', false, false, '_btn');
		$btnUpload = we_html_button::create_button("upload", "javascript:" . $this->getJsBtnCmd('upload'), true, $width['button'], 22, "", "", true, false, "_btn", true);
		$btnReset = we_html_button::create_button("reset", 'javascript:we_FileUpload.reset()', true, $width['button'], 22, "", "", true, false, "_btn", true);
		$btnCancel = we_html_button::create_button("cancel", 'javascript:we_FileUpload.cancelUpload()', true, $width['button'], 22, "", "", false, false, "_btn", true);
		$fileInput = we_html_element::htmlInput(array(
				'class' => 'fileInput fileInputHidden' . (we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11 ? ' fileInputIE10' : ''),
				'style' => 'width:' . $width['input'] . 'px;',
				'type' => 'file',
				'name' => $this->name,
				'id' => $this->name,
				'accept' => implode(',', $this->typeCondition['accepted']['mime']))
		);
		$fileInput .=!$isIE10 ? '' :
			we_html_element::htmlInput(array(
				'class' => 'fileInput fileInputHidden' . (we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11 ? ' fileInputIE10' : ''),
				'style' => 'width:' . $width['input'] . 'px; left:' . $width['input'] . 'px;',
				'type' => 'file',
				'name' => $this->name . '_x2',
				'id' => $this->name . '_x2',
				'accept' => implode(',', $this->typeCondition['accepted']['mime']))
		);
		$divFileInput = we_html_element::htmlDiv(array('id' => 'div_we_File_fileInputWrapper', 'class' => 'we_fileInputWrapper', 'style' => 'height:23px;margin-top:22px;width:' . $width['button'] . 'px;'), $fileInput . $btnBrowse);
		$divBtnReset = we_html_element::htmlDiv(array('id' => 'div_fileupload_btnReset', 'style' => 'margin-top:22px;display:none;'), $btnReset);
		$divBtnUpload = we_html_element::htmlDiv(array('id' => 'div_fileupload_btnUpload', 'style' => 'margin-top: 6px;'), $btnUpload);
		$divBtnCancel = we_html_element::htmlDiv(array('id' => 'div_fileupload_btnCancel', 'style' => 'margin-top:16px;display:none;'), $btnCancel);

		$progress = new we_progressBar(20, 0, true);
		$progress->setStudLen(170);
		$progress->setProgressTextPlace(0);
		$progress->setName('_fileupload');
		$divProgressbar = we_html_element::htmlDiv(array('id' => 'div_fileupload_progressBar', 'style' => 'margin-top: 13px; display: none;'), $progress->getHTML());

		$divButtons = we_html_element::htmlDiv(array('id' => 'div_fileupload_buttons', 'style' => 'width:204px'), $divFileInput .
				$divProgressbar .
				$divBtnReset .
				$divBtnUpload .
				$divBtnCancel
		);

		$btnUploadLegacy = we_html_button::create_button("upload", "javascript:we_cmd('editor_uploadFile', 'legacy')", true, 150, 22, "", "", false, false, "_legacy_btn", true);
		$divBtnUploadLegacy = we_html_element::htmlDiv(array('id' => 'div_fileupload_btnUploadLegacy', 'style' => 'margin:0px 0 16px 0;display:' . (self::isFallback() || self::isLegacyMode() ? '' : 'none' ) . ';'), $btnUploadLegacy);

		$leftMarginTop = we_base_browserDetect::isIE() ? 4 : (we_base_browserDetect::isChrome() ? 3 : 0);
		$divFileInfo = we_html_element::htmlDiv(array('style' => 'margin-top: ' . $leftMarginTop . 'px'), $fs . '<br />' . $ft . '<br />' . $md);

		//TODO: clean out css and use more we_html_element
		$divDropzone = !(self::isFallback() || self::isLegacyMode()) ? ('
			<div class="we_file_drag we_file_drag_binDoc" id="div_fileupload_fileDrag_state_0" style="' . (!$this->isDragAndDrop ? 'border-color:white;' : '') . '">
				<div style="display:table-cell;width:178px;height:116px;padding-left:4px;vertical-align:middle;color:#cccccc;font-weight:normal;font-size:' . ($this->isDragAndDrop ? 16 : 14) . 'px">' .
			$dropText . '
				</div>' .
			we_html_element::htmlDiv(array('class' => 'dropzone_right'), ($thumbnailSmall ? : we_html_element::htmlImg(array('src' => $this->binDocProperties['icon'])))) . '
			</div>
			<div class="we_file_drag we_file_drag_binDoc" style="display:none;' . (!$this->isDragAndDrop ? 'border-color:rgb(243, 247, 255);' : '') . '" id="div_fileupload_fileDrag_state_1">
				<div id="div_upload_fileDrag_innerLeft" style="display:table-cell;width:178px;height:116px;padding-left:10px;vertical-align:middle;text-align:left;color:#333;font-weight: normal;font-size:12px">' .
			we_html_element::htmlSpan(array('id' => 'span_fileDrag_inner_filename')) . we_html_element::htmlBr() .
			we_html_element::htmlSpan(array('id' => 'span_fileDrag_inner_size')) . we_html_element::htmlBr() .
			we_html_element::htmlSpan(array('id' => 'span_fileDrag_inner_type')) . '
				</div>' .
			we_html_element::htmlDiv(array('id' => 'div_upload_fileDrag_innerRight', 'class' => 'dropzone_right'), '') . '
			</div>' .
			($this->isDragAndDrop ? '<div style="position:absolute;top:11px;background:none;" class="we_file_drag" id="div_we_File_fileDrag"></div>' : '')) : '';

		return (self::isFallback() || self::isLegacyMode() ? '' : $this->getJs() . $this->getCss()) . '
			<table id="table_form_upload" cellpadding="0" cellspacing="0" border="0" width="500">
			<tr style="vertical-align:top;">
				<td class="defaultfont" width="200px">' .
			$divBtnUploadLegacy .
			$divFileInfo .
			(self::isFallback() || self::isLegacyMode() ? '' :
				$divButtons .
				we_html_tools::hidden('we_doc_ct', $this->contentType) .
				we_html_tools::hidden('we_doc_ext', $this->extension) .
				we_html_tools::hidden('weFileNameTemp', '') .
				we_html_tools::hidden('weFileName', '') .
				we_html_tools::hidden('weFileCt', '') .
				we_html_tools::hidden('weIsFileInLegacy', 0)
			) . '
				</td>
				<td width="300px">' .
			(self::isFallback() || self::isLegacyMode() ? '' : '
						<div id="div_fileupload_right">' .
				$divDropzone .
				($this->binDocProperties['type'] === 'image' ? '<br />' . we_html_forms::checkbox(1, true, "import_metadata", g_l('metadata', '[import_metadata_at_upload]')) : '') . '
						</div>'
			) . '
					<div id="div_fileupload_right_legacy" style="text-align:right;display:' . (self::isFallback() || self::isLegacyMode() ? '' : 'none' ) . '">' .
			$thumbnailBig . '
					</div>
				</td>
			</tr>
			<tr><td colspan="2">' . we_html_tools::getPixel(4, 20) . '</td></tr>' .
			(self::isFallback() || self::isLegacyMode() ? '' : '<tr><td colspan="2" class="defaultfont">' . $this->getHtmlAlertBoxes() . '</td></tr>
							<tr><td colspan="2">' . we_html_tools::getPixel(4, 20) . '</td></tr>') . '

			<tr><td colspan="2" class="defaultfont">' . we_html_tools::htmlAlertAttentionBox(g_l('weClass', ($GLOBALS['we_doc']->getFilesize() ? "[upload_will_replace]" : "[upload_single_files]")), we_html_tools::TYPE_ALERT, 508) . '</td></tr>
			</table>';
	}

	public function getJsBtnCmd($btn = 'upload'){
		$call = 'window.we_FileUpload.' . ($btn === 'upload' ? 'startUpload()' : 'cancelUpload()');
		$callback = 'we_cmd(\'editor_uploadFile\', \'legacy\');';

		return 'if(typeof window.we_FileUpload === "undefined" || window.we_FileUpload.getIsLegacyMode()){' . $callback . ';}else{' . $call . ';}';
	}

	public static function getJsOnLeave($callback, $type = 'switch_tab'){
		if(self::isFallback() || self::isLegacyMode()){
			return $callback;
		}

		if($type === 'switch_tab'){
			$parentObj = 'top.weEditorFrameController';
			$frame = 'top.weEditorFrameController.getVisibleEditorFrame()';
		} else {
			$parentObj = '_EditorFrame';
			$frame = '_EditorFrame.getContentEditor()';
		}

		return "var fileupload; if(typeof " . $parentObj . " !== 'undefined' && typeof (fileUpload = " . $frame . ".we_FileUpload) !== 'undefined' && fileUpload.getType() === 'binDoc' && !fileUpload.getIsLegacyMode()){fileUpload.doUploadIfReady(function(){" . $callback . "})}else{" . $callback . "}";
	}

	public function processFileRequest(){
		$this->transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', '');
		$partNum = we_base_request::_(we_base_request::INT, 'wePartNum', 0);
		$partCount = we_base_request::_(we_base_request::INT, 'wePartCount', 0);
		$fileNameTemp = we_base_request::_(we_base_request::STRING, 'weFileNameTemp', '');
		$fileName = we_base_request::_(we_base_request::STRING, 'weFileName', '');
		$fileCt = we_base_request::_(we_base_request::STRING, 'weFileCt', '');

		$error = '';

		if(isset($_FILES[$this->name]) && $_FILES[$this->name]['tmp_name']){
			$tempName = $partNum == 1 ? we_base_file::getUniqueId() . $this->extension : we_base_file::getUniqueId();
			$tempPath = TEMP_PATH;

			$error = (!$tempName ?
					'no_filename_error' :
					($this->maxChunkCount && $partNum > $this->maxChunkCount ?
						'oversized_error' :
						(!@move_uploaded_file($_FILES[$this->name]["tmp_name"], TEMP_PATH . $tempName) ?
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
						$error = !$this->_checkFileType($mime, $fileName) ? 'mime_or extension_not_ok_error: ' . $mime . '/' . $fileName : '';
					}
				} else {
					//t_e("No Mime type could be determined by finfo_file or mime_content_type");
					//we ignore this and test against extension when file is completed
				}
			}

			if($error){
				echo json_encode(array('status' => 'failure', 'fileNameTemp' => $fileNameTemp, 'message' => $error, 'completed' => 0, 'finished' => ''));
				return;
			}
			$fileNameTemp = $partNum == 1 ? $tempName : $fileNameTemp;
			if($partCount > 1 && $partNum > 1){
				file_put_contents(TEMP_PATH . $fileNameTemp, file_get_contents(TEMP_PATH . $tempName), FILE_APPEND);
				unlink(TEMP_PATH . $tempName);
			}
			$response = array('status' => ($partNum == $partCount ? 'success' : 'continue'), 'fileNameTemp' => $fileNameTemp, 'mimePhp' => (isset($mime) && $mime ? $mime : $fileCt), 'message' => '', 'completed' => ($partNum == $partCount ? 1 : 0), 'finished' => '');
			if($partCount && $partCount != $partNum){
				echo json_encode($response);
				return;
			}
		}

		//no errors so far and all chunks are done
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
			echo json_encode($response);
			return;
		}

		$_FILES[$this->name] = array(
			'type' => $fileCt,
			'tmp_name' => 'notempty',
			'name' => $fileName,
			'size' => 1,
			'error' => UPLOAD_ERR_OK,
		);
		//FIXME: make some integrity test for the whole and for every chunk (md5)

		$saveDocument = $this->postProcess($fileNameTemp, $fileName, $fileCt);
		if($saveDocument[0]){
			$response['weDoc'] = array('path' => $saveDocument[1][0], 'text' => $saveDocument[1][1]);
		} else {
			$response = array('status' => 'failure', 'fileNameTemp' => $fileNameTemp, 'message' => $saveDocument[1], 'completed' => 0, 'finished' => '');
		}
		echo json_encode($response);
	}

	private function postProcess($fileNameTemp, $fileName = '', $fileCt = ''){
		if(!isset($_SESSION['weS']['we_data'][$this->transaction])){
			return array(false, 'transaction is not correct');
		}
		$we_dt = $_SESSION['weS']['we_data'][$this->transaction];
		include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

		if(!$this->typeCondition['accepted']['mime'] || in_array($fileCt, $this->typeCondition['accepted']['mime'])){
			$we_doc->Extension = strtolower((strpos($fileName, '.') > 0) ? preg_replace('/^.+(\..+)$/', '$1', $fileName) : ''); //strtolower for feature 3764
			$we_File = TEMP_PATH . $fileNameTemp;

			if((!$we_doc->Filename) || (!$we_doc->ID)){
				// Bug Fix #6284
				$we_doc->Filename = preg_replace('/[^A-Za-z0-9._-]/', '', $fileName);
				$we_doc->Filename = preg_replace('/^(.+)\..+$/', '$1', $we_doc->Filename);
			}

			$foo = explode('/', $fileCt);
			$we_doc->setElement('data', $we_File, $foo[0]);

			switch($we_doc->ContentType){
				case we_base_ContentTypes::IMAGE:
					if(!$we_doc->isSvg() && !in_array(we_base_imageEdit::detect_image_type($we_File), we_base_imageEdit::$GDIMAGE_TYPE)){
						$we_alerttext = g_l('alert', '[wrong_file][' . $we_doc->ContentType . ']');
						break;
					}
				//no break
				case we_base_ContentTypes::FLASH:
					$we_size = $we_doc->getimagesize($we_File);
					$we_doc->setElement('width', $we_size[0], 'attrib');
					$we_doc->setElement('height', $we_size[1], 'attrib');
					$we_doc->setElement('origwidth', $we_size[0], 'attrib');
					$we_doc->setElement('origheight', $we_size[1], 'attrib');
				//no break
				default:
					$we_doc->Text = $we_doc->Filename . $we_doc->Extension;
					$we_doc->Path = $we_doc->getPath();
					$we_doc->DocChanged = true;

					if($we_doc->Extension === '.pdf'){
						$we_doc->setMetaDataFromFile($we_File);
					}

					$_SESSION['weS']['we_data']['tmpName'] = $we_File;
					if(we_base_request::_(we_base_request::BOOL, 'import_metadata')){
						$we_doc->importMetaData();
					}
					$we_doc->saveInSession($_SESSION['weS']['we_data'][$this->transaction]); // save the changed object in session
			}
		} else if(isset($fileName) && !empty($fileName)){
			$we_alerttext = g_l('alert', '[wrong_file][' . $we_doc->ContentType . ']');
		} else if(isset($fileName) && empty($fileName)){
			$we_alerttext = g_l('alert', '[no_file_selected]');
		}


		if(isset($we_alerttext) && $we_alerttext){
			return array(false, $we_alerttext);
		}

		return array(true, array($we_doc->Path, $we_doc->Text));
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
