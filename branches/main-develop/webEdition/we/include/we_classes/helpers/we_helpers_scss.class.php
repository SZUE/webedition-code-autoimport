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
include_once(WE_LIB_PATH . 'additional/scssphp/scss.inc.php');

class we_helpers_scss extends \Leafo\ScssPhp\Compiler{
	public static $includedFiles = [];

	protected function importFile($path, $out){
		if(!is_numeric($path)){
			return parent::importFile($path, $out);
		}

		$fname = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($path));
		// see if tree is cached
		if(isset($this->importCache[$path])){
			$tree = $this->importCache[$path];
		} else {
			$code = f('SELECT c.Dat FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON l.CID=c.ID WHERE l.Type="txt" AND l.nHash=x\'' . md5('data') . '\' AND l.DocumentTable="tblFile" AND l.DID=' . intval($path));
			self::$includedFiles[] = $path;
			$parser = new \Leafo\ScssPhp\Parser($fname, false);
			$tree = $parser->parse($code);
			$this->parsedFiles[] = $fname;

			$this->importCache[$path] = $tree;
		}

		//append path to list of paths
		$pi = pathinfo($fname);
		array_unshift($this->importPaths, $pi['dirname']);
		$this->compileChildren($tree->children, $out);
		array_shift($this->importPaths);
	}

	// results the file path for an import url if it exists
	public function findImport($url){
		$matches = [];
		if(preg_match('|#WE:(\d+)#|', $url, $matches)){
			$url = intval($matches[1]);
			return (f('SELECT Extension FROM ' . FILE_TABLE . ' WHERE ID=' . $url) === '.scss' ? $url : null);
		}
		return parent::findImport($url);
	}

}
