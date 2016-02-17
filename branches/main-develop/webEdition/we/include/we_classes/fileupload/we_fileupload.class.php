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
abstract class we_fileupload{
	protected $name = 'weFileSelect';
	protected $isDragAndDrop = true;
	protected $maxUploadSizeMBytes = 16;
	protected $maxUploadSizeBytes = 0;
	protected $isGdOk = true;
	protected $genericFileNameTemp = '';
	protected $predefinedConfig = '';
	protected $doCommitFile = true;

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

	public function setTypeCondition($field = 'accepted', $weCts = array(), $exts = array()){

		// new vars for js
		$cts = '';
		$exts4cts = '';
		foreach($weCts as $ct){
			$ct = strtolower($ct);
			if(in_array($ct, we_base_ContentTypes::inst()->getContentTypes(FILE_TABLE, true))){
				$tmp = we_base_ContentTypes::inst()->getExtension($ct);
				if(is_array($tmp)){
					$tmp = array_map(function($e){return(substr($e, 1));}, $tmp);
					$exts4cts .= ',' . implode(',', $tmp) . ',';
				}
				$cts .= $ct . ',';
			}
		}

		$exts = array_map(function($e){return(strtolower(trim($e, ' ,')));}, $exts);

		$ret = array(
			// new vars: used in js
			'cts' => $cts ? ',' . $cts : '',
			'exts4cts' => $exts4cts,
			'exts' => $exts ? ',' . implode(',', $exts) . ',' : '',
			// old vars: used in php // FIXME: throw away when sure that we do not need them anymore
			'mime' => $weCts,
			'ext' => $exts
		);
		$ret['all'] = array_merge($ret['mime'], $ret['ext']);

		$this->typeCondition[$field] = $ret;
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

	public function setPredefinedConfig($predefinedConfig = ''){
		$this->predefinedConfig = $predefinedConfig;
	}

}
