<?php

/**
 * webEdition CMS
 *
 * $Rev: 6643 $
 * $Author: mokraemer $
 * $Date: 2013-09-16 01:47:39 +0200 (Mo, 16. Sep 2013) $
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
include_once(WE_LIB_PATH . 'additional/lessphp/lessc.inc.php');

class we_helpers_lessc extends lessc{

	private $path = '';

	public function setCurrentPath($path){
		$this->path = $path;
		$this->importDir = array(
			$_SERVER['DOCUMENT_ROOT'],
			$_SERVER['DOCUMENT_ROOT'] . $path,
		);
	}

	private static function isLess(&$url){
		$matches = array();
		if(preg_match('|#WE:(\d+)#|', $url, $matches)){
			$url = intval($matches[1]);
			return f('SELECT Extension FROM ' . FILE_TABLE . ' WHERE ID=' . intval($matches[1]), 'Extension', $GLOBALS['DB_WE']) == '.less';
		} else {
			if(substr_compare($url, '.css', -4, 4) === 0){
				return false;
			}
		}
		return true;
	}

	protected function tryImport($importPath, $parentBlock, $out){
		if($importPath[0] == 'function' && $importPath[1] == 'url'){
			$importPath = $this->flattenList($importPath[2]);
		}

		$str = $this->coerceString($importPath);
		if($str === null)
			return false;
		$url = $this->compileValue($this->lib_e($str));


		// don't import if it ends in css
		if(!self::isLess($url)){
			if(is_numeric($url)){
				return array(false,'@import "'.id_to_path($url, FILE_TABLE, $GLOBALS['DB_WE']).'";');
			}
			return false;
		}

		$realPath = $this->findImport($url);

		if($realPath === null)
			return false;

		if($this->importDisabled){
			return array(false, "/* import disabled */");
		}

		if(isset($this->allParsedFiles[$realPath])){
			return array(false, null);
		}

		$this->addParsedFile($realPath);
		$parser = $this->makeParser($realPath);
		$root = $parser->parse(self::getContent($realPath));

		// set the parents of all the block props
		foreach($root->props as $prop){
			if($prop[0] == 'block'){
				$prop[1]->parent = $parentBlock;
			}
		}

		// copy mixins into scope, set their parents
		// bring blocks from import into current block
		// TODO: need to mark the source parser	these came from this file
		foreach($root->children as $childName => $child){
			if(isset($parentBlock->children[$childName])){
				$parentBlock->children[$childName] = array_merge(
						$parentBlock->children[$childName], $child);
			} else {
				$parentBlock->children[$childName] = $child;
			}
		}

		$pi = pathinfo($realPath);
		$dir = $pi['dirname'];

		list($top, $bottom) = $this->sortProps($root->props, true);
		$this->compileImportedProps($top, $parentBlock, $out, $parser, $dir);

		return array(true, $bottom, $parser, $dir);
	}

	protected function findImport($url){
		if(is_numeric($url)){
			return $url;
		}
		//remove /
		$tmpurl = ltrim($url, '/');
		//in DB at path information the file name is .css
		if(strpos($tmpurl, '.less') === false){
			$tmpurl.='.css';
		} else {
			$tmpurl = str_replace('.less', '.css', $tmpurl);
		}

		$id = path_to_id($this->path . '/' . $tmpurl, FILE_TABLE, $GLOBALS['DB_WE']);
		$id = ($id ? $id : path_to_id('/' . $tmpurl, FILE_TABLE, $GLOBALS['DB_WE']));
		if($id){
			return $id;
		}
		return realpath(parent::findImport($url));
	}

	protected function addParsedFile($file){
		if(is_numeric($file)){
			$this->allParsedFiles[$file] = 1;
		} else {
			parent::addParsedFile($file);
		}
	}

	private static function getContent($file){
		if(is_numeric($file)){
			return f('SELECT c.Dat FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON l.CID=c.ID WHERE l.Type="txt" AND l.Name="data" AND l.DocumentTable="tblFile" AND l.DID=' . intval($file), 'Dat', $GLOBALS['DB_WE']);
		} else {
			return file_get_contents($file);
		}
	}

}