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
class we_helpers_lessParser extends Less_Parser{
	public static $includedFiles = [];

	public function __construct($env = null){
		if(!is_object($env)){
			$env['import_callback'] = ['we_helpers_lessParser', 'importCallBack'];
		}
		parent::__construct($env);
	}

	public static function importCallBack($env){
		$matches = [];
		if($env->path->value && preg_match('|#WE:(\d+)#|', $env->path->value, $matches)){
			$hash = getHash('SELECT Path,ParentID FROM ' . FILE_TABLE . ' WHERE ID=' . intval($matches[1]), null, MYSQL_NUM);
			list($path, $parentid) = ($hash? : [0, 0]);
			if($hash){
				self::$includedFiles[] = intval($matches[1]);
			}
			return [$path ? $env->path->value : $path, $parentid ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . $parentid) : '/'];
		}
		return null;
	}

	public function SetInput($file_path){
		if(is_numeric($file_path)){
			self::$includedFiles[] = $file_path;
			$this->input = f('SELECT c.Dat FROM ' . CONTENT_TABLE . ' c WHERE c.Type="txt" AND c.nHash=x\'' . md5("data") . '\' AND c.DocumentTable="tblFile" AND c.DID=' . intval($file_path));
			$file_path = '';
		}
		parent::SetInput($file_path);
	}

}
