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
	private static $maxUploadSizeMB = -1;

	protected $name = 'weFileSelect';
	protected $isDragAndDrop = true;
	protected $maxUploadSizeBytes = 0;
	protected $isGdOk = true;
	protected $genericFileNameTemp = '';
	protected $predefinedConfig = '';
	protected $doCommitFile = true;
	protected $typeCondition = ['accepted' => ['cts' => '',
			'exts4cts' => '',
			'exts' => '',
			'all' => '',
		 ],
		'forbidden' => ['cts' => '',
			'exts4cts' => '',
			'exts' => '',
			'all' => '',
		 ]
	];
	protected $imageEditProps = ['parentID' => 0,
		'sameName' => 'rename',
		'importMetadata' => false,
		'isSearchable' => false,
		'thumbnails' => '',
		'imageWidth' => 0,
		'imageHeight' => 0,
		'widthSelect' => 'pixel',
		'heightSelect' => 'pixel',
		'keepRatio' => true,
		'quality' => 8,
		'degrees' => 0,
		'categories' => ''
	 ];

	const CHUNK_SIZE = 128;
	const ON_ERROR_RETURN = true; //obsolete?
	const ON_ERROR_DIE = true; //obsolete?
	const REPLACE_BY_UNIQUEID = '##REPLACE_BY_UNIQUEID##';
	const REPLACE_BY_FILENAME = '##REPLACE_BY_FILENAME##';
	const USE_FILENAME_FROM_UPLOAD = true;
	const EDIT_IMAGES_CLIENTSIDE = true;

	protected function __construct($name){
		$this->name = $name;
		$this->isDragAndDrop = !(we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11) || we_base_browserDetect::isOpera();
		$this->maxUploadSizeBytes = (self::getMaxUploadSizeMB()) * 1048576;
		$this->maxChunkCount = (self::getMaxUploadSizeMB()) * 1024 / self::CHUNK_SIZE;
	}

	public function setIsDragAndDrop($isDragAndDrop = true){
		$this->isDragAndDrop = !$this->isDragAndDrop ? false : $isDragAndDrop;
	}

	public function getCss(){
		return '';
	}

	public function getJs(){
		return '';
	}

	public function getName(){
		return $this->name;
	}

	public static function isDragAndDrop(){
		return true;//!((we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11) || we_base_browserDetect::isOpera());
	}

	public static function getMaxUploadSizeMB(){
		if(self::$maxUploadSizeMB === -1){
			self::$maxUploadSizeMB = intval(defined('FILE_UPLOAD_MAX_UPLOAD_SIZE') ? FILE_UPLOAD_MAX_UPLOAD_SIZE : 0);
		}

		return self::$maxUploadSizeMB;
	}

	public static function getMaxUploadSizeB(){
		return self::getMaxUploadSizeMB() * 1024 * 1024;
	}

	public function setTypeCondition($field = 'accepted', $weCts = [], $exts = []){
		// new vars for js
		$cts = $exts4cts = [];
		foreach($weCts as $ct){
			$ct = strtolower($ct);
			if(in_array($ct, we_base_ContentTypes::inst()->getContentTypes(FILE_TABLE, true))){
				$exts4cts = is_array(($tmp = we_base_ContentTypes::inst()->getExtension($ct))) ? array_merge($exts4cts, $tmp) :
					($tmp ? array_merge($exts4cts, explode(',', trim($tmp, ','))) : $exts4cts);
				$tmp = we_base_ContentTypes::inst()->getRealContentTypes($ct);
				$cts = empty($tmp) ? array_merge($cts, [$ct]) : array_merge($cts, $tmp);
			}
		}

		$ret = ['cts' => $cts ? ',' . implode(',', array_unique($cts)) . ',' : '',
			'exts4cts' => $exts4cts ? ',' . strtolower(implode(',', array_unique($exts4cts))) . ',' : '',
			'exts' => $exts ? ',' . strtolower(implode(',', array_unique($exts))) . ',' : '',
			];
		$ret['all'] = str_replace(',,', ',', $ret['cts'] . $ret['exts4cts'] . $ret['exts']);

		$this->typeCondition[$field] = $ret;
	}

	public function setPredefinedConfig($predefinedConfig = ''){
		$this->predefinedConfig = $predefinedConfig;
	}

	public function setImageEditProps($props){
		if(empty($props) || !is_array($props)){
			return;
		}

		$this->imageEditProps = array_merge($this->imageEditProps, $props);
	}

	public function loadImageEditPropsFromSession(){
		$this->imageEditProps = isset($_SESSION['weS']['we_fileupload']['imageEditProps']) ? $_SESSION['weS']['we_fileupload']['imageEditProps'] : $this->imageEditProps;
	}

	public function saveImageEditPropsInSession(){
		$_SESSION['weS']['we_fileupload']['imageEditProps'] = $this->imageEditProps;
	}

}
