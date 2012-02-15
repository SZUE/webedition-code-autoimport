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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class we_base_ContentTypes{
	private $ct;

	public function __construct(){
		$charset=defined('WE_BACKENDCHARSET') ? WE_BACKENDCHARSET : 'UTF-8';
		$this->ct = array(
// Content Type for Images
			'image/*' => array(
				'Extension' => array('.gif','.jpg','.jpeg','.png'),
				'Permission' => 'NEW_GRAFIK',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'image.gif',
			),
			'text/html' => array(
				'Extension' => array('.html','.htm','.shtm','.shtml','.stm','.php','.jsp','.asp','.pl','.cgi','.xml','.xsl'),
				'Permission' => 'NEW_HTML',
				'DefaultCode' => '<html>' . "\n\t" .
				'<head>' . "\n\t\t" .
				'<title></title>' . "\n\t\t" .
				'<meta http-equiv="Content-Type" content="text/html; charset="' . $charset . '">' . "\n\t" .
				'</head>' . "\n\t" .
				'<body>' . "\n\t" .
				'</body>' . "\n" .
				'</html>',
				'IsWebEditionFile' => true,
				'IsRealFile' => true,
				'Icon' => 'html.gif',
			),
			'text/webedition' => array(
				'Extension' => array('.html','.htm','.shtm','.shtml','.stm','.php','.jsp','.asp','.pl','.cgi','.xml'),
				'Permission' => 'NEW_WEBEDITIONSITE',
				'DefaultCode' => '',
				'IsWebEditionFile' => true,
				'IsRealFile' => false,
				'Icon' => 'we_dokument.gif',
			),
			'text/weTmpl' => array(
				'Extension' => '.tmpl',
				'Permission' => 'NEW_TEMPLATE',
				'DefaultCode' => '<!DOCTYPE HTML>
<html dir="ltr" lang="<we:pageLanguage type="complete" doc="top" />">
<head>
	<we:title></we:title>
	<we:description></we:description>
	<we:keywords></we:keywords>
	<we:charset defined="UTF-8">UTF-8</we:charset>
</head>
<body>
	<table cellpadding="0" cellspacing="0"  width="400">
		<tr><td>
				<p><b><we:input type="date" name="Date" format="d.m.Y"/></b></p>
				<h2><we:input type="text" name="Headline" size="60"/></h2>
				<p>
					<we:ifNotEmpty match="Image">
						<we:img name="Image" showthumbcontrol="true"/>
						<we:ifEditmode>
							<br/><br/>
						</we:ifEditmode>
					</we:ifNotEmpty></p>

					<we:textarea name="Content" width="400" height="200" autobr="true" wysiwyg="true" removefirstparagraph="false" inlineedit="true"/>

		</td></tr>
	</table>
</body>
</html>',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Icon' => 'we_template.gif',
			),
			'text/js' => array(
				'Extension' => '.js',
				'Permission' => 'NEW_JS',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'javascript.gif',
			),
			'text/css' => array(
				'Extension' => '.css',
				'Permission' => 'NEW_CSS',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'css.gif',
			),
			'text/htaccess' => array(
				'Extension' => '',
				'Permission' => 'NEW_HTACCESS',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'htaccess.gif'
			),
			'text/plain' => array(
				'Extension' => '.txt',
				'Permission' => 'NEW_TEXT',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'link.gif',
			),
			'folder' => array(
				'Extension' => '',
				'Permission' => '',
				'DefaultCode' => '',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Icon' => 'folder.gif',
			),
			'class_folder' => array(
				'Extension' => '',
				'Permission' => '',
				'DefaultCode' => '',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Icon' => 'class_folder.gif',
			),
			'application/x-shockwave-flash' => array(
				'Extension' => '.swf',
				'Permission' => 'NEW_FLASH',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'flashmovie.gif',
			),
			'video/quicktime' => array(
				'Extension' => array('.mov','.moov','.qt'),
				'Permission' => 'NEW_QUICKTIME',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'quicktime.gif',
			),
			'application/*' => array(
				'Extension' => array('.doc','.xls','.ppt','.zip','.sit','.bin','.hqx','.exe'),
				'Permission' => 'NEW_SONSTIGE',
				'DefaultCode' => '',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'link.gif',
			),
			'text/xml' => array(
				'Extension' => '.xml',
				'Permission' => 'NEW_TEXT',
				'DefaultCode' => '<?xml version="1.0" encoding="' . $charset . '" ?>',
				'IsRealFile' => true,
				'IsWebEditionFile' => true,
				'Icon' => 'link.gif',
			),
			'object' => array(
				'Extension' => '',
				'Permission' => '',
				'DefaultCode' => '',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Icon' => 'object.gif',
			),
			'objectFile' => array(
				'Extension' => '',
				'Permission' => '',
				'DefaultCode' => '',
				'IsRealFile' => false,
				'IsWebEditionFile' => false,
				'Icon' => 'objectFile.gif',
			),
		);
	}

	public function hasContentType($name){
		return isset($this->ct[$name]);
	}

	public function getContentTypes(){
		return array_keys($this->ct);
	}

	public function getIcon($name, $default='', $extension=''){
		if($name == 'application/*'){
			switch($extension){
				case '.pdf' :
					return 'pdf.gif';
				case '.zip' :
				case '.sit' :
				case '.hqx' :
				case '.bin' :
					return 'zip.gif';
				case '.doc' :
					return 'word.gif';
				case '.xls' :
					return 'excel.gif';
				case '.ppt' :
					return 'powerpoint.gif';
			}
			return 'prog.gif';
		} else{
			return isset($this->ct[$name]) ? $this->ct[$name]['Icon'] : $default;
		}
	}

	public function getExtension($name){
		return isset($this->ct[$name])?$this->ct[$name]['Extension']:'';
	}

	public function isWEFile($name){
		return isset($this->ct[$name])?$this->ct[$name]['IsWebEditionFile']:false;
	}

	public function getWETypes(){
		$ret=array();
		foreach($this->ct as $name=>$type){
			if($type['IsWebEditionFile']){
				$ret[]=$name;
			}
		}
		return $ret;
	}

	public function getDefaultCode($name){
		return isset($this->ct[$name])?$this->ct[$name]['DefaultCode']:'';
	}

	public function getPermission($name){
		return isset($this->ct[$name])?$this->ct[$name]['Permission']:'';
	}

	public function getTypeForExtension($extension){
		foreach($this->ct as $type=>$val){
			$ext=$val['Extension'];
			if((is_array($ext)&&in_array($extension, $ext))||$ext==$extension){
				return $type;
			}
		}
		return '';
	}

	public function getFiles(){
		$ret=array();
		foreach($this->ct as $type=>$val){
			if($val['IsRealFile']){
				$ret[]=$type;
			}
		}
		return $ret;
	}
}