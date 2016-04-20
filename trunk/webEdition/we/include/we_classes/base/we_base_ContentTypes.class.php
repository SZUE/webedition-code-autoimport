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
class we_base_ContentTypes{
	const IMAGE = 'image/*';
	const TEMPLATE = 'text/weTmpl';
	const XML = 'text/xml';
	const HTML = 'text/html';
	const WEDOCUMENT = 'text/webedition';
	const JS = 'text/js';
	const CSS = 'text/css';
	const HTACESS = 'text/htaccess';
	const TEXT = 'text/plain';
	const FLASH = 'application/x-shockwave-flash';
	const QUICKTIME = 'video/quicktime';
	const VIDEO = 'video/*';
	const AUDIO = 'audio/*';
	const APPLICATION = 'application/*';
	const FOLDER = 'folder';
	const CLASS_FOLDER = 'class_folder';
	const OBJECT = 'object';
	const OBJECT_FILE = 'objectFile';
	const COLLECTION = 'text/weCollection';

	private $ct;

	public function __construct(){
		$charset = defined('WE_BACKENDCHARSET') ? WE_BACKENDCHARSET : 'UTF-8';
		$this->ct = array(
// Content Type for Images
			self::IMAGE => array(
				'Extension' => array('.gif', '.jpg', '.jpeg', '.png', '.svg', '.svgz', '.ico'),
				'ContentTypes' => array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png', 'image/svg+xml', 'image/svg-xml', 'image/x-citrix-pjpeg', 'image/x-icon'),
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_GRAFIK',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Table' => array(FILE_TABLE),
				'Class' => 'we_imageDocument'
			),
			self::XML => array(//this entry must stay before text/html, text/we because fileextensions are not distinct
				'Extension' => '.xml',
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_TEXT',
				'DefaultCode' => '<?xml version="1.0" encoding="' . $charset . '" ?>',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Table' => array(FILE_TABLE),
				'Class' => 'we_textDocument'
			),
			self::HTML => array(
				'Extension' => array('.html', '.htm', '.shtm', '.shtml', '.stm', '.php', '.jsp', '.asp', '.pl', '.cgi', '.xml', '.xsl'),
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_HTML',
				'DefaultCode' => '<!doctype html>
<html>
	<head>
		<title></title>
		<meta charset="' . $charset . '">
	</head>
	<body>
	</body>
</html>',
				'IsWebEditionFile' => true,
				'IsRealFile' => true,
				'Table' => array(FILE_TABLE),
				'Class' => 'we_htmlDocument'
			),
			self::WEDOCUMENT => array(
				'Extension' => array('.html', '.htm', '.shtm', '.shtml', '.stm', '.php', '.jsp', '.asp', '.pl', '.cgi', '.xml'),
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_WEBEDITIONSITE',
				'DefaultCode' => '',
				'IsWebEditionFile' => true,
				'IsRealFile' => false,
				'Table' => array(FILE_TABLE),
				'Class' => 'we_webEditionDocument'
			),
			self::TEMPLATE => array(
				'Extension' => '.tmpl',
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_TEMPLATE',
				'DefaultCode' => '<!DOCTYPE HTML>
<html dir="ltr" lang="<we:pageLanguage type="language" doc="top" />">
<head>
	<we:title></we:title>
	<we:description></we:description>
	<we:keywords></we:keywords>
	<we:charset defined="UTF-8">UTF-8</we:charset>
</head>
<body>
	<article style="width:400px">
		<h1><we:input type="text" name="Headline" style="width:60em"/></h1>
		<p><b><we:input type="date" name="Date" format="d.m.Y"/></b></p>
		<we:ifNotEmpty match="Image">
			<p><we:img name="Image" showthumbcontrol="true"/></p>
		</we:ifNotEmpty>
		<we:textarea name="Content" width="400" height="400" autobr="true" wysiwyg="true" removefirstparagraph="false" inlineedit="true"/>
	</article>
</body>
</html>',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Table' => array(TEMPLATES_TABLE),
				'Class' => 'we_template'
			),
			self::JS => array(
				'Extension' => '.js',
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_JS',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Table' => array(FILE_TABLE),
				'Class' => 'we_textDocument'
			),
			self::CSS => array(
				'Extension' => array('.css', '.less', '.scss', '.sass'),
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_CSS',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Table' => array(FILE_TABLE),
				'Class' => 'we_textDocument'
			),
			self::HTACESS => array(
				'Extension' => array('.htaccess', '.htpasswd'),
				'ExtensionIsFilename' => true,
				'Permission' => 'NEW_HTACCESS',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Table' => array(FILE_TABLE),
				'Class' => 'we_textDocument'
			),
			self::TEXT => array(
				'Extension' => array('.txt', '.csv'),
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_TEXT',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Table' => array(FILE_TABLE),
				'Class' => 'we_textDocument'
			),
			self::FOLDER => array(
				'Extension' => '',
				'ExtensionIsFilename' => false,
				'Permission' => '',
				'DefaultCode' => '',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Table' => array_filter(array(FILE_TABLE, TEMPLATES_TABLE, defined('OBJECT_TABLE') ? OBJECT_TABLE : '', defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : '', defined('VFILE_TABLE') ? VFILE_TABLE : '')),
				'Class' => 'we_folder'
			),
			self::CLASS_FOLDER => array(
				'Extension' => '',
				'ExtensionIsFilename' => false,
				'Permission' => '',
				'DefaultCode' => '',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Table' => array(defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : ''),
				'Class' => 'we_class_folder'
			),
			self::FLASH => array(
				'Extension' => array('.swf'/* ,'.mp4','.m4v' */),
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_FLASH',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Table' => array(FILE_TABLE),
				'Class' => 'we_flashDocument'
			),
			self::QUICKTIME => array(
				'Extension' => array('.mov', '.moov', '.qt'),
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_QUICKTIME',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Table' => array(FILE_TABLE),
				'Class' => 'we_quicktimeDocument'
			),
			self::VIDEO => array(
				'Extension' => array('.mp4', '.m4v', '.ogg', '.webm'),
				'ContentTypes' => array('video/mp4', 'video/webm', 'application/ogg', 'video/ogg',),
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_FLASH',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Table' => array(FILE_TABLE),
				'Class' => 'we_document_video'
			),
			self::AUDIO => array(
				'Extension' => array('.mp3', '.wav', '.ogg'),
				'ContentTypes' => array('audio/mp3', 'audio/ogg', 'audio/wav'),
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_SONSTIGE',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Table' => array(FILE_TABLE),
				'Class' => 'we_document_audio'
			),
			self::APPLICATION => array(
				'Extension' => array('.doc', '.xls', '.ppt', '.zip', '.sit', '.bin', '.hqx', '.exe', '.pdf'),
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_SONSTIGE',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Table' => array(FILE_TABLE),
				'Class' => 'we_otherDocument'
			),
			self::OBJECT => array(
				'Extension' => '',
				'ExtensionIsFilename' => false,
				'Permission' => '',
				'DefaultCode' => '',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Table' => array(defined('OBJECT_TABLE') ? OBJECT_TABLE : ''),
				'Class' => 'we_object'
			),
			self::OBJECT_FILE => array(
				'Extension' => '',
				'ExtensionIsFilename' => false,
				'Permission' => '',
				'DefaultCode' => '',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Table' => array(defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : ''),
				'Class' => 'we_objectFile'
			),
			self::COLLECTION => array(
				'Extension' => '',
				'ExtensionIsFilename' => false,
				'Permission' => '',
				'DefaultCode' => '',
				'IsRealFile' => false, //TODO: use this when saving
				'IsWebEditionFile' => false,
				'Table' => array(defined('VFILE_TABLE') ? VFILE_TABLE : ''),
				'Class' => 'we_collection'
			)
		);
	}

	public static function inst(){
		static $inst = 0;
		return ($inst = ($inst ? : new self()));
	}

	public function hasContentType($name){
		return isset($this->ct[$name]);
	}

	public function getContentTypes($filter = '', $filterOmitFolder = false){
		if($filter){
			$ret = array();
			foreach($this->ct as $k => $v){
				if(in_array($filter, $v['Table']) && !($filterOmitFolder && ($k === self::FOLDER || $k === self::CLASS_FOLDER))){
					$ret[] = $k;
				}
			}

			return $ret;
		}
		return array_keys($this->ct);
	}

	public function getExtension($name, $ignoreIsFilename = false){
		return isset($this->ct[$name]) && ($ignoreIsFilename || !$this->ct[$name]['ExtensionIsFilename']) ? $this->ct[$name]['Extension'] : '';
	}

	public function isWEFile($name){
		return isset($this->ct[$name]) ? $this->ct[$name]['IsWebEditionFile'] : false;
	}

	public function getWETypes(){
		$ret = array();
		foreach($this->ct as $name => $type){
			if($type['IsWebEditionFile']){
				$ret[] = $name;
			}
		}
		return $ret;
	}

	public function getObject($type = ''){
		if(!$type){
			return false;
		}

		if(isset($this->ct[$type]['Class']) && $this->ct[$type]['Class'] && class_exists($this->ct[$type]['Class'])){
			return new $this->ct[$type]['Class'];
		} else {
			$classname = 'we_' . $type;
			if(class_exists($classname)){
				return new $classname();
			} else {
				t_e('Can NOT initialize document of type -' . $type . '- ' . 'we_' . $type . '.inc.php');
				return false;
			}
		}
	}

	public function getDefaultCode($name){
		return isset($this->ct[$name]) ? $this->ct[$name]['DefaultCode'] : '';
	}

	public function getPermission($name){
		return isset($this->ct[$name]) ? $this->ct[$name]['Permission'] : '';
	}

	public function getTypeForExtension($extension){
		foreach($this->ct as $type => $val){
			$ext = $val['Extension'];
			if((is_array($ext) && in_array($extension, $ext)) || $ext == $extension){
				return $type;
			}
		}
		return '';
	}

	public function getRealContentTypes($type){
		return (isset($this->ct[$type]['ContentTypes'])) ? $this->ct[$type]['ContentTypes'] : array();
	}

	public function getFiles(){
		$ret = array();
		foreach($this->ct as $type => $val){
			if($val['IsRealFile']){
				$ret[] = $type;
			}
		}
		return $ret;
	}

}
