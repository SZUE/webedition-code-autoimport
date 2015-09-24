<?php

/**
 * webEdition CMS
 *
 * $Rev: 10461 $
 * $Author: lukasimhof $
 * $Date: 2015-09-18 15:20:39 +0200 (Fr, 18 Sep 2015) $
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
class we_fileupload_resp_base {
	private $name = '';
	private $fileNameTemp = '';
	private $contenType = 'text/*';
	private $controlVars = array(
			'partNum' => 0,
			'partCount' => 0,
			'weIsUploadComplete' => false,//FIXME: do we really need so much vars for execution control?
			'weIsUploading' => false
		);
	private $fileVars = array(
			'fileNameTemp' => '',
			'weFileName' => '',
			'weFileSize' => 1,
			'weFileCt' => ''
		);
	private $fileupload = array();

	const GET_PATH_ONLY = 1;
	const GET_NAME_ONLY = 2;
	const FORCE_DOC_ROOT = true;
	const MISSING_DOC_ROOT = true;
	const USE_FILENAME_FROM_UPLOAD = true;
	const USE_LEGACY_FOR_BACKUP = false;
	const USE_LEGACY_FOR_WEIMPORT = false;

	public function __construct($name = '', $contentType = '', $fileupload = array(), $fileVars = array(), $controlVars = array()){
		$this->name = $name;
		$this->contenType = $contentType ? : $this->contenType;
		$this->fileVars = array_merge($this->fileVars, $fileVars);
		$this->controlVars = array_merge($this->controlVars, $controlVars);
		$this->fileupload = is_array($fileupload) ? $fileupload : array();
	}

	public function processRequest($retFalseOnFinalError = false){

		$partNum = $this->controlVars['partNum'] ? : 0;
		$partCount = $this->controlVars['partCount'] ? : 0;
		$fileNameTemp = $this->file['fileNameTemp'] ? : '';
		$fileName = $this->file['weFileName'] ? : '';
		$fileSize = $this->file['weFileSize'] ? : 1;

		//FIXME: do we really need so much vars for execution control?
		$isUploadComplete = $this->controlVars['weIsUploadComplete'] ? : false;
		$isUploading = $this->controlVars['weIsUploadComplete'] ? : false;
		$error = '';

		if($isUploading){//FIXME: change to $isHtmlInputFile
			if($partCount){
				if(isset($this->fileupload[$this->name]) && $this->fileupload[$this->name]['tmp_name']){

					$tempName = $partNum == 1 ? $this->_makeFileNameTemp(self::GET_NAME_ONLY) : we_base_file::getUniqueId();
					$tempPath = $this->_makeFileNameTemp(self::GET_PATH_ONLY, self::FORCE_DOC_ROOT);

					$error = (!$tempName ?
							'no_filename_error' :
							($this->maxChunkCount && $partNum > $this->maxChunkCount ?
								'oversized_error' :
								(!@move_uploaded_file($this->fileupload[$this->name]["tmp_name"], $tempPath . $tempName) ?
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

	protected function writeDocument(){
		
	}

	protected function getDocument(){
		$we_doc = we_base_ContentTypes::inst()->getObject($this->contenType);

		return $we_doc ? : new we_otherDocument();
	}

	protected function getAllowedContentTypes(){
		switch($this->contenType){
			case we_base_ContentTypes::IMAGE;
			case we_base_ContentTypes::VIDEO:
			case we_base_ContentTypes::AUDIO:
				$allowedContentTypes = implode(',', we_base_ContentTypes::inst()->getRealContentTypes($we_ContentType));
				break;
			case we_base_ContentTypes::APPLICATION;
				$allowedContentTypes = '';
				break;
			default:
				$allowedContentTypes = $we_ContentType;
		}
	}
}
