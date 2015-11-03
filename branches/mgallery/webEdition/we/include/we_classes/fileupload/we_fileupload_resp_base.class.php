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
class we_fileupload_resp_base extends we_fileupload{

	protected $name = 'we_File';
	protected $response = array('status' => '', 'fileNameTemp' => '', 'mimePhp' => 'none', 'message' => '', 'completed' => '', 'weDoc' => '');
	protected $typeCondition = array(
		'accepted' => array(
			'mime' => array(),
			'extensions' => array(),
			'all' => array()
		),
		'forbidden' => array(
			'mime' => array(),
			'extensions' => array(),
			'all' => array()
		)
	);
	protected $contentType = 'image/*'; // alloud target ct: obsolete => use typeCondition
	protected $controlVars = array(
		'partNum' => 1,
		'partCount' => 1,
		'formnum' => 0,
		'formcount' => 0,
		'doCommitFile' => true
	);
	protected $fileVars = array(
		'genericFileNameTemp' => '',
		'fileTemp' => '',
		'fileDef' => '',
		'parentID' => 0,
		'parentDir' => '',
		'weFileName' => '',
		'weFileSize' => 1,
		'weFileCt' => '',
		'sameName' => 'rename',
	);
	protected $docVars = array(
		'transaction' => '',
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
	);
	protected $FILES = array();
	protected $maxChunkCount = 0;

	const GET_PATH_ONLY = 1;
	const GET_NAME_ONLY = 2;
	const FORCE_DOC_ROOT = true;
	const MISSING_DOC_ROOT = true;
	const USE_FILENAME_FROM_UPLOAD = true;

	public function __construct($name = '', $contentType = '', $FILE = array(), $fileVars = array(), $controlVars = array(), $docVars = ''){
		$this->name = $name ? : $this->name;
		$this->contentType = $contentType ? : $this->contentType; // => this is alloud ct, not ct of uploaded file! loop from ui through js to rpc!!
		$this->initByHttp();
	}

	protected function initByHttp(){
		$this->FILES = $_FILES;
		$this->fileVars = array_merge($this->fileVars, array_filter(array(
				'genericFileNameTemp' => we_base_request::_(we_base_request::STRING, 'genericFilename', we_base_request::NOT_VALID),
				'fileTemp' => we_base_request::_(we_base_request::STRING, 'weFileNameTemp', we_base_request::NOT_VALID),
				'parentID' => we_base_request::_(we_base_request::URL, 'fu_file_parentID', we_base_request::NOT_VALID),
				'weFileName' => we_base_request::_(we_base_request::STRING, 'weFileName', we_base_request::NOT_VALID),
				'weFileSize' => we_base_request::_(we_base_request::INT, 'weFileSize', we_base_request::NOT_VALID),
				'weFileCt' => we_base_request::_(we_base_request::STRING, 'weFileCt', we_base_request::NOT_VALID),
				'sameName' => we_base_request::_(we_base_request::STRING, 'fu_file_sameName', we_base_request::NOT_VALID)
			), function($var){return $var !== we_base_request::NOT_VALID;})
		);
		$this->controlVars = array_merge($this->controlVars, array_filter(array(
				'doCommitFile' => we_base_request::_(we_base_request::BOOL, 'doCommitFile', true),
				'partNum' => we_base_request::_(we_base_request::INT, 'wePartNum', we_base_request::NOT_VALID),
				'partCount' => we_base_request::_(we_base_request::INT, 'wePartCount', we_base_request::NOT_VALID),
				'formnum' => we_base_request::_(we_base_request::INT, "weFormNum", we_base_request::NOT_VALID),
				'formcount' => we_base_request::_(we_base_request::INT, "weFormCount", we_base_request::NOT_VALID),
			), function($var){return $var !== we_base_request::NOT_VALID;})
		);
	}

	public function processRequest(){
		if(!(isset($this->FILES[$this->name]) && strlen($this->FILES[$this->name]["tmp_name"]))){
			return array_merge($this->response, array('status' => 'failure', 'message' => g_l('importFiles', '[php_error]')));
		}

		$this->fileVars['weFileCt'] = getContentTypeFromFile($this->FILES[$this->name]["name"]); //compare mime and ct by extension
		if(!permissionhandler::hasPerm(we_base_ContentTypes::inst()->getPermission($this->fileVars['weFileCt']))){
			return array_merge($this->response, array('status' => 'failure', 'message' => 'no_perms'));
		}

		if($this->controlVars['partNum'] > 1 && !$this->fileVars['fileTemp']){
			return array_merge($this->response, array('status' => 'failure', 'message' => 'inconsistent_params'));
		}

		$chunkName = $this->controlVars['partNum'] === 1 ? $this->makeFileTemp() : TEMP_DIR . we_base_file::getUniqueId();
		$chunkFile = $_SERVER['DOCUMENT_ROOT'] . $chunkName;
		if(!@move_uploaded_file($this->FILES[$this->name]["tmp_name"], $chunkFile)){
			return array_merge($this->response, array('status' => 'failure', 'message' => 'move_file_error'));
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
			return array_merge($this->response, array('status' => 'continue', 'fileNameTemp' => $this->fileVars['fileTemp']));
		}

		// last chunk appended: start post process and get some response to send
		return $this->postProcess();
	}

	protected function postprocess(){
		if(!$this->controlVars['doCommitFile']){ // most simple variant: we just leave tempFile in tmp directory and inform GUI that it can go on and grab it
			return array_merge($this->response, array('status' => 'success', 'fileNameTemp' => $this->fileVars['fileTemp'], 'completed' => 1));
		}

		if(!$this->checkSetFile()){
			return $this->response;
		}

		if(!copy($_SERVER['DOCUMENT_ROOT'] . $this->fileVars['fileTemp'], $_SERVER['DOCUMENT_ROOT'] . str_replace(array('\\', '//'), '/', $this->fileVars['fileDef']))){
			return $this->response = array_merge($this->response, array('status' => 'failure', 'message' => 'error_copy_file', 'completed' => 1));
		};

		return array_merge($this->response, array('status' => 'success', 'fileNameTemp' => $this->fileVars['fileTemp'], 'completed' => 1, 'weDoc' => array('id' => 0, 'path' => $this->fileVars['fileDef'], 'text' => $this->fileVars['weFileName']), 'commited' => 1));
	}

	protected function checkSetFile(){
		$path = rtrim(($this->fileVars['parentID'] ? id_to_path($this->fileVars['parentID']) : ''), '/') . '/';

		if(file_exists($_SERVER['DOCUMENT_ROOT'] . $path . $this->fileVars['weFileName'])){
			switch($this->fileVars['sameName']){
				case 'overwrite':
					if(path_to_id($path . $this->fileVars['weFileName'])){
						$this->response = array_merge($this->response, array('status' => 'failure', 'message' => g_l('fileselector', '[can_not_overwrite_we_file]'), 'completed' => 1));
						return false;
					}
					break;
				case 'rename':
					$z = 0;
					$regs = array();
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
					$this->response = array_merge($this->response, array('status' => 'failure', 'message' => g_l('importFiles', '[same_name]'), 'completed' => 1));
					return false;
			}
		}
		$this->fileVars['fileDef'] = $path . "/" . $this->fileVars['weFileName'];

		return true;
	}

	protected function makeFileTemp(){
		return $this->fileVars['genericFileNameTemp'] ? str_replace(array(self::REPLACE_BY_UNIQUEID, self::REPLACE_BY_FILENAME), array(we_base_file::getUniqueId(), $this->fileVars['weFileName']), $this->fileVars['genericFileNameTemp']) : TEMP_DIR . we_base_file::getUniqueId();
	}

	protected function checkIntegrity(){
		return true;
	}

	protected function getAllowedContentTypes(){
		switch($this->contentType){
			case we_base_ContentTypes::IMAGE;
			case we_base_ContentTypes::VIDEO:
			case we_base_ContentTypes::AUDIO:
				$allowedContentTypes = implode(',', we_base_ContentTypes::inst()->getRealContentTypes($this->contentType));
				break;
			case we_base_ContentTypes::APPLICATION;
				$allowedContentTypes = '';
				break;
			default:
				$allowedContentTypes = $this->contentType;
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
			// t_e($alert);
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

}
