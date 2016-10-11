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
class we_fileupload_resp_base extends we_fileupload{
	protected $name = 'we_File';
	protected $response = ['status' => '', 'fileNameTemp' => '', 'mimePhp' => 'none', 'message' => '', 'completed' => '', 'weDoc' => ''];
	protected $contentType = 'image/*'; // alloud target ct: obsolete => use typeCondition
	protected $controlVars = ['partNum' => 1,
		'partCount' => 1,
		'formnum' => 0,
		'formcount' => 0,
		'doCommitFile' => true
	 ];
	protected $fileVars = ['genericFileNameTemp' => '',
		'fileTemp' => '',
		'fileDef' => '',
		'parentID' => 0,
		'parentDir' => '',
		'weFileName' => '',
		'weFileSize' => 1,
		'weFileCt' => '',
		'sameName' => 'rename',
	 ];
	protected $docVars = ['transaction' => '',
		'importMetadata' => false,
		'categories' => '',
		'isSearchable' => true,
		'title' => '',
		'alt' => '',
		'thumbs' => '',
		'width' => 0,
		'widthSelect' => '',
		'height' => 0,
		'heightSelect' => '',
		'quality' => 8,
		'keepRatio' => 0,
		'degrees' => 0,
		'focusX' => 0,
		'focusY' => 0
	 ];
	protected $FILES = [];
	protected $maxChunkCount = 0;
	protected $uploadError = '';

	const GET_PATH_ONLY = 1;
	const GET_NAME_ONLY = 2;
	const FORCE_DOC_ROOT = true;
	const MISSING_DOC_ROOT = true;
	const USE_FILENAME_FROM_UPLOAD = true;

	public function __construct($name = '', $contentType = '', $FILE = [], $fileVars = [], $controlVars = [], $docVars = ''){
		$this->name = $name ? : $this->name;
		$this->contentType = $contentType ? : $this->contentType; // => this is alloud ct, not ct of uploaded file! loop from ui through js to rpc!!
		$this->initByHttp();
	}

	protected function initByHttp(){
		$this->FILES = $_FILES;
		$this->fileVars = array_merge($this->fileVars, array_filter([
			'genericFileNameTemp' => we_base_request::_(we_base_request::STRING, 'genericFilename', we_base_request::NOT_VALID),
			'fileTemp' => we_base_request::_(we_base_request::STRING, 'weFileNameTemp', we_base_request::NOT_VALID),
			'parentID' => we_base_request::_(we_base_request::URL, 'fu_file_parentID', we_base_request::NOT_VALID),
			'parentDir' => we_base_request::_(we_base_request::URL, 'fu_file_parentDir', we_base_request::NOT_VALID),
			'weFileName' => we_base_request::_(we_base_request::STRING, 'weFileName', we_base_request::NOT_VALID),
			'weFileSize' => we_base_request::_(we_base_request::INT, 'weFileSize', we_base_request::NOT_VALID),
			'weFileCt' => we_base_request::_(we_base_request::STRING, 'weFileCt', we_base_request::NOT_VALID),
			'sameName' => we_base_request::_(we_base_request::STRING, 'fu_file_sameName', we_base_request::NOT_VALID)
			], function($var){
				return $var !== we_base_request::NOT_VALID;
			})
		);
		$this->controlVars = array_merge($this->controlVars, array_filter([
			'doCommitFile' => we_base_request::_(we_base_request::BOOL, 'doCommitFile', true),
			'partNum' => we_base_request::_(we_base_request::INT, 'wePartNum', we_base_request::NOT_VALID),
			'partCount' => we_base_request::_(we_base_request::INT, 'wePartCount', we_base_request::NOT_VALID),
			'formnum' => we_base_request::_(we_base_request::INT, "weFormNum", we_base_request::NOT_VALID),
			'formcount' => we_base_request::_(we_base_request::INT, "weFormCount", we_base_request::NOT_VALID),
			], function($var){
				return $var !== we_base_request::NOT_VALID;
			})
		);
	}

	public function processRequest(){
		if(!(isset($this->FILES[$this->name]) && strlen($this->FILES[$this->name]["tmp_name"]))){
			return array_merge($this->response, ['status' => 'failure', 'message' => g_l('importFiles', '[php_error]')]);
		}

		// FIXME: this test ist too strong:
		// Only check permissions when we know, that the file is to be imported (important: e.g. html should be permitted on we_otherDocument even if the right new_html is missing!)
		$this->fileVars['weFileCt'] = getContentTypeFromFile($this->FILES[$this->name]['name']); //compare mime and ct by extension
		if(!permissionhandler::hasPerm(($perm = we_base_ContentTypes::inst()->getPermission($this->fileVars['weFileCt'])))){
			return array_merge($this->response, ['status' => 'failure', 'message' => 'no perms: ' . g_l('perms_workpermissions', '[' . $perm . ']')]);
		}

		if($this->controlVars['partNum'] > 1 && !$this->fileVars['fileTemp']){
			return array_merge($this->response, ['status' => 'failure', 'message' => 'inconsistent_params']);
		}

		$chunkName = $this->controlVars['partNum'] === 1 ? $this->makeFileTemp() : TEMP_DIR . we_base_file::getUniqueId();
		$chunkFile = $_SERVER['DOCUMENT_ROOT'] . $chunkName;
		if(!@move_uploaded_file($this->FILES[$this->name]["tmp_name"], $chunkFile)){
			return array_merge($this->response, ['status' => 'failure', 'message' => 'move_file_error']);
		}

		// if not first chunk append it to tempFile, else tmpFile = chunkFile
		if($this->controlVars['partNum'] === 1){
			$this->fileVars['fileTemp'] = $chunkName;
		} else {
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . $this->fileVars['fileTemp'], file_get_contents($chunkFile), FILE_APPEND);
			unlink($chunkFile);
		}

		// everything works fine but upload not finished yet: get next chunk
		if($this->controlVars['partCount'] !== $this->controlVars['partNum'] && $this->controlVars['partCount'] !== 1){
			return array_merge($this->response, ['status' => 'continue', 'fileNameTemp' => $this->fileVars['fileTemp']]);
		}

		// last chunk appended: start post process and get some response to send
		return $this->postProcess();
	}

	protected function postprocess(){
		if(!$this->controlVars['doCommitFile']){ // most simple variant: we just leave tempFile in tmp directory and inform GUI that it can go on and grab it
			return array_merge($this->response, ['status' => 'success', 'fileNameTemp' => $this->fileVars['fileTemp'], 'completed' => 1]);
		}

		if(!$this->checkSetFile()){
			return $this->response;
		}

		if(!copy($_SERVER['DOCUMENT_ROOT'] . $this->fileVars['fileTemp'], $_SERVER['DOCUMENT_ROOT'] . str_replace(['\\', '//'], '/', $this->fileVars['fileDef']))){
			return $this->response = array_merge($this->response, ['status' => 'failure', 'message' => 'error_copy_file', 'completed' => 1]);
		}

		return array_merge($this->response, ['status' => 'success', 'fileNameTemp' => $this->fileVars['fileTemp'], 'completed' => 1, 'weDoc' => ['id' => 0, 'path' => $this->fileVars['fileDef'],
				'text' => $this->fileVars['weFileName']], 'commited' => 1]);
	}

	public function commitUploadedFile($absolute = false){
		$file = $_SERVER['DOCUMENT_ROOT'] . $this->fileVars['fileTemp'];
		if(!file_exists($file) || !is_readable($file)){
			$this->uploadError = 'no valid file uploaded';

			return '';
		}
		if(!$this->checkFileType($file)){
			$this->uploadError = 'filetype not ok';

			return '';
		}

		return $absolute ? $file : $this->fileVars['fileTemp'];
	}

	protected function checkSetFile(){
		if($this->fileVars['parentDir'] && $this->fileVars['parentID'] == -1){
			$path = $this->fileVars['parentDir'];
		} else {
			$path = $this->fileVars['parentID'] ? id_to_path($this->fileVars['parentID']) : '';
		}
		$path = rtrim($path, '/') . '/';

		if(file_exists($_SERVER['DOCUMENT_ROOT'] . $path . $this->fileVars['weFileName'])){
			switch($this->fileVars['sameName']){
				case 'overwrite':
					if(path_to_id($path . $this->fileVars['weFileName'])){
						$this->response = array_merge($this->response, ['status' => 'failure', 'message' => g_l('fileselector', '[can_not_overwrite_we_file]'), 'completed' => 1]);
						return false;
					}
					break;
				case 'rename':
					$z = 0;
					$regs = [];
					if(preg_match('|^(.+)(\.[^\.]+)$|', $this->fileVars['weFileName'], $regs)){
						$ext = $regs[2];
						$name = $regs[1];
					} else {
						$ext = "";
						$name = $this->fileVars['weFileName'];
					}
					$tmp = $name . "_" . $z . $ext;
					while(file_exists($_SERVER['DOCUMENT_ROOT'] . $path . $tmp)){
						$tmp = $name . "_" . ++$z . $ext;
					}
					$this->fileVars['weFileName'] = $tmp;
					break;
				default:
					$this->response = array_merge($this->response, ['status' => 'failure', 'message' => g_l('importFiles', '[same_name]'), 'completed' => 1]);
					return false;
			}
		}
		$this->fileVars['fileDef'] = $path . "/" . $this->fileVars['weFileName'];

		return true;
	}

	protected function makeFileTemp(){
		return $this->fileVars['genericFileNameTemp'] ? strtr($this->fileVars['genericFileNameTemp'], [self::REPLACE_BY_UNIQUEID => we_base_file::getUniqueId(), self::REPLACE_BY_FILENAME => $this->fileVars['weFileName']]) : TEMP_DIR . we_base_file::getUniqueId();
	}

	private function checkFileType($mime = '', $fileName = '', $mode = ''){
		return true; // FIXME: make same test as in JS
	}

}
