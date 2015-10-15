<?php

/**
 * webEdition CMS
 *
 * $Rev: 10502 $
 * $Author: lukasimhof $
 * $Date: 2015-09-24 22:05:28 +0200 (Do, 24 Sep 2015) $
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
abstract class we_fileupload{
	protected $name = 'weFileSelect';
	protected $isDragAndDrop = true;
	protected $maxUploadSizeMBytes = 16;
	protected $maxUploadSizeBytes = 0;
	protected $isGdOk = true;
	protected $genericFileNameTemp = '';
	protected $predefinedConfig = '';

	const CHUNK_SIZE = 128;
	const ON_ERROR_RETURN = true;//obsolete?
	const ON_ERROR_DIE = true;//obsolete?

	const REPLACE_BY_UNIQUEID = '##REPLACE_BY_UNIQUEID##';
	const REPLACE_BY_FILENAME = '##REPLACE_BY_FILENAME##';
	const USE_FILENAME_FROM_UPLOAD = true;

	protected function __construct($name){
		$this->name = $name;
		$this->isDragAndDrop = !(we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11) || we_base_browserDetect::isOpera();
		$this->maxUploadSizeMBytes = 128;intval(defined('FILE_UPLOAD_MAX_UPLOAD_SIZE') ? FILE_UPLOAD_MAX_UPLOAD_SIZE : -1);
		$this->maxUploadSizeBytes = $this->maxUploadSizeMBytes * 1048576;
		$this->maxChunkCount = $this->maxUploadSizeMBytes * 1024 / self::CHUNK_SIZE;
	}

	public function setMaxUploadSize($sizeMB){
		$this->maxUploadSizeMBytes = $sizeMB;
		$this->maxUploadSizeBytes = $this->maxUploadSizeMBytes * 1048576;
	}

	public function setIsDragAndDrop($isDragAndDrop = true){
		$this->isDragAndDrop = !$this->isDragAndDrop ? false : $isDragAndDrop;
	}

	public function setIsFallback($isFallback = false){
		self::$isFallback = $isFallback;
	}

	public function getName(){
		return $this->name;
	}

	public static function isDragAndDrop(){
		return !((we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11) || we_base_browserDetect::isOpera());
	}

	public function getMaxUploadSize(){
		return $this->maxUploadSizeBytes;
	}

	public function setTypeCondition($field = 'accepted', $mime = array(), $ext = array(), $cts = array()){ // move to ui_base or base
		foreach($cts as $ct){
			switch($ct){
				case we_base_ContentTypes::IMAGE;
					$mime = array_merge($mime, we_base_ContentTypes::inst()->getRealContentTypes($this->contentType));
					$ext = array_merge($ext, explode(',', we_base_imageEdit::IMAGE_EXTENSIONS));
					break;
				case we_base_ContentTypes::VIDEO:
					$mime = array_merge($mime, we_base_util::mimegroup2mimes(we_base_ContentTypes::VIDEO));
					$ext = we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::VIDEO);
				case we_base_ContentTypes::AUDIO:
					$mime = array_merge($mime, we_base_util::mimegroup2mimes(we_base_ContentTypes::AUDIO));
					$ext = we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::AUDIO);
				case we_base_ContentTypes::APPLICATION;
					$mime = array();
					$ext = array();
					break;
				default:
					$mime = array($this->contentType);
					$ext = array();
			}
		}

		$mime = array_filter(array_map(function($e){return(strtolower(trim($e, ' ,')));}, $mime), function($var){return !$var ? false : true;});
		$ext = array_map(function($e){return(strtolower(trim($e, ' ,')));}, $ext);

		$tmp = array(
			'mime' => $mime,
			'ext' => $ext,
		);
		$tmp['all'] = array_merge($tmp['mime'], $tmp['ext']);

		$this->typeCondition[$field] = $tmp;
	}

	public static function commitFile($fileInputName = '', $typecondition = array()){// FIXME: implement typecondition, move to resp_base?
		if($fileInputName && 
				($filenametemp = we_base_request::_(we_base_request::STRING, 'weFileNameTemp', '')) &&
				($filename = we_base_request::_(we_base_request::STRING, 'weFileName', ''))){

			$_FILES[$fileInputName] = array(
				'type' => we_base_request::_(we_base_request::STRING, 'weFileCt', ''),
				'tmp_name' => 'notempty',
				'name' => $filename,
				'size' => 1,//we_base_request::_(we_base_request::STRING, 'weFileCt', ''),
				'error' => UPLOAD_ERR_OK,
			);

			// make some integrity tests

			return $filenametemp;
		}

		return false;
	}

	protected function _isFileExtensionOk($fileName){
		return $this->_checkFileType('', $fileName, 'ext');
	}

	protected function _isFileMimeOk($mime){//FIXME:unused!
		return $this->_checkFileType($mime, '', 'mime');
	}
	
	public function setPredefinedConfig($predefinedConfig = ''){
		$this->predefinedConfig = $predefinedConfig;
	}

}
