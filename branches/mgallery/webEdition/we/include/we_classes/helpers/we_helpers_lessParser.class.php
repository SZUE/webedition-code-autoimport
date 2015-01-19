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

	public function __construct($env = null){
		if(!is_object($env)){
			$env['import_callback'] = array('we_helpers_lessParser', 'importCallBack');
		}
		parent::__construct($env);
	}

	public static function importCallBack($env){
		$matches = array();
		if($env->path->value && preg_match('|#WE:(\d+)#|', $env->path->value, $matches)){
			$hash = getHash('SELECT Path,ParentID FROM ' . FILE_TABLE . ' WHERE ID=' . intval($matches[1]), null, MYSQL_NUM);
			list($path, $parentid) = ($hash? : array(0, 0));
			return array($path ? $env->path->value : $path, $parentid ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . $parentid) : '/');
		}
		return null;
	}

	public function SetInput($file_path){
		if(is_numeric($file_path)){
			$this->input = f('SELECT c.Dat FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON l.CID=c.ID WHERE l.Type="txt" AND l.Name="data" AND l.DocumentTable="tblFile" AND l.DID=' . intval($file_path));
			$file_path = '';
		}
		parent::SetInput($file_path);
	}

}
