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
	const CLASS_FOLDER_ICON = 'class_folder.gif';
	const FOLDER_ICON = 'folder.gif';
	const IMAGE_ICON = 'image.gif';
	const FILE_ICON = 'file.gif';
	const LINK_ICON = 'link.gif';
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
	const APPLICATION = 'application/*';
	const FOLDER = 'folder';

	private $ct;

	public function __construct(){
		$charset = defined('WE_BACKENDCHARSET') ? WE_BACKENDCHARSET : 'UTF-8';
		$this->ct = array(
// Content Type for Images
			self::IMAGE => array(
				'Extension' => array('.gif', '.jpg', '.jpeg', '.png', '.svg', '.svgz'),
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_GRAFIK',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => self::IMAGE_ICON,
			),
			self::XML => array(//this entry must stay before text/html, text/we because filetypes are not distinct
				'Extension' => '.xml',
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_TEXT',
				'DefaultCode' => '<?xml version="1.0" encoding="' . $charset . '" ?>',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => self::FILE_ICON,
			),
			self::HTML => array(
				'Extension' => array('.html', '.htm', '.shtm', '.shtml', '.stm', '.php', '.jsp', '.asp', '.pl', '.cgi', '.xml', '.xsl'),
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_HTML',
				'DefaultCode' => '<html>
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset="' . $charset . '">
	</head>
	<body>
	</body>
</html>',
				'IsWebEditionFile' => true,
				'IsRealFile' => true,
				'Icon' => 'html.gif',
			),
			self::WEDOCUMENT => array(
				'Extension' => array('.html', '.htm', '.shtm', '.shtml', '.stm', '.php', '.jsp', '.asp', '.pl', '.cgi', '.xml'),
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_WEBEDITIONSITE',
				'DefaultCode' => '',
				'IsWebEditionFile' => true,
				'IsRealFile' => false,
				'Icon' => 'we_dokument.gif',
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
		<h1><we:input type="text" name="Headline" size="60"/></h1>
		<p><b><we:input type="date" name="Date" format="d.m.Y"/></b></p>
		<we:ifNotEmpty match="Image">
			<p><we:img name="Image" showthumbcontrol="true"/></p>
		</we:ifNotEmpty>
		<we:textarea name="Content" width="400" height="200" autobr="true" wysiwyg="true" removefirstparagraph="false" inlineedit="true"/>
	</article>
</body>
</html>',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Icon' => 'we_template.gif',
			),
			self::JS => array(
				'Extension' => '.js',
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_JS',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'javascript.gif',
			),
			self::CSS => array(
				'Extension' => array('.css', '.less', '.scss', '.sass'),
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_CSS',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'css.gif',
			),
			self::HTACESS => array(
				'Extension' => '.htaccess',
				'ExtensionIsFilename' => true,
				'Permission' => 'NEW_HTACCESS',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'htaccess.gif'
			),
			self::TEXT => array(
				'Extension' => '.txt',
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_TEXT',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => self::FILE_ICON,
			),
			'folder' => array(
				'Extension' => '',
				'ExtensionIsFilename' => false,
				'Permission' => '',
				'DefaultCode' => '',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Icon' => self::FOLDER_ICON,
			),
			'class_folder' => array(
				'Extension' => '',
				'ExtensionIsFilename' => false,
				'Permission' => '',
				'DefaultCode' => '',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Icon' => self::CLASS_FOLDER_ICON,
			),
			self::FLASH => array(
				'Extension' => '.swf',
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_FLASH',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'flashmovie.gif',
			),
			self::QUICKTIME => array(
				'Extension' => array('.mov', '.moov', '.qt'),
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_QUICKTIME',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'quicktime.gif',
			),
			self::APPLICATION => array(
				'Extension' => array('.doc', '.xls', '.ppt', '.zip', '.sit', '.bin', '.hqx', '.exe', '.pdf'),
				'ExtensionIsFilename' => false,
				'Permission' => 'NEW_SONSTIGE',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => self::FILE_ICON,
			),
			'object' => array(
				'Extension' => '',
				'ExtensionIsFilename' => false,
				'Permission' => '',
				'DefaultCode' => '',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Icon' => 'object.gif',
			),
			'objectFile' => array(
				'Extension' => '',
				'ExtensionIsFilename' => false,
				'Permission' => '',
				'DefaultCode' => '',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Icon' => 'objectFile.gif',
			),
		);
	}

	public static function inst(){
		static $inst = 0;
		$inst = ($inst ? $inst : new self());
		return $inst;
	}

	public function hasContentType($name){
		return isset($this->ct[$name]);
	}

	public function getContentTypes(){
		return array_keys($this->ct);
	}

	public function getIcon($name, $default = '', $extension = ''){
		if($name == self::APPLICATION){
			switch(strtolower($extension)){
				case '.pdf' :
					return 'pdf.gif';
				case '.zip' :
				case '.sit' :
				case '.hqx' :
				case '.bin' :
					return 'zip.gif';
				case '.odt':
				case '.ott':
				case '.dot' :
				case '.doc' :
					return 'word.gif';
				case '.ods':
				case '.ots':
				case '.xlt' :
				case '.xls' :
					return 'excel.gif';
				case '.odp':
				case '.otp':
				case '.ppt' :
					return 'powerpoint.gif';
				case '.odg':
				case '.otg':
					return 'odg.gif';
				default:
					return 'prog.gif';
			}
		} else {
			return isset($this->ct[$name]) ? $this->ct[$name]['Icon'] : $default;
		}
	}

	public function getExtension($name){
		return isset($this->ct[$name]) && !$this->ct[$name]['ExtensionIsFilename'] ? $this->ct[$name]['Extension'] : '';
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
