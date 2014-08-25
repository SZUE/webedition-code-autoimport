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
/*  a class for handling text-documents */
class we_textDocument extends we_document{
	/* Constructor */

	function __construct(){
		parent::__construct();
		if(isWE()){
			array_push($this->EditPageNrs, we_base_constants::WE_EDITPAGE_PROPERTIES, we_base_constants::WE_EDITPAGE_INFO, we_base_constants::WE_EDITPAGE_CONTENT, we_base_constants::WE_EDITPAGE_VALIDATION, we_base_constants::WE_EDITPAGE_VERSIONS);
		}
		$this->elements['Charset']['dat'] = DEFAULT_CHARSET;
		$this->Icon = we_base_ContentTypes::FILE_ICON;
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor(){

		switch($this->EditPageNr){
			case we_base_constants::WE_EDITPAGE_PROPERTIES:
				return 'we_templates/we_editor_properties.inc.php';
			case we_base_constants::WE_EDITPAGE_INFO:
				return 'we_templates/we_editor_info.inc.php';
			case we_base_constants::WE_EDITPAGE_CONTENT:
				$GLOBALS['we_editmode'] = true;
				return 'we_templates/we_srcTmpl.inc.php';
			case we_base_constants::WE_EDITPAGE_PREVIEW:
				if($GLOBALS['we_EDITOR']){
					$GLOBALS['we_file_to_delete_after_include'] = TEMP_PATH . we_base_file::getUniqueId() . $this->Extension;
					we_base_file::save($GLOBALS['we_file_to_delete_after_include'], $this->i_getDocument());
					return $GLOBALS['we_file_to_delete_after_include'];
				} else {
					$GLOBALS['we_editmode'] = false;
					return 'we_templates/we_srcTmpl.inc.php';
				}
			case we_base_constants::WE_EDITPAGE_VALIDATION:
				return 'we_templates/validateDocument.inc.php';
			case we_base_constants::WE_EDITPAGE_VERSIONS:
				return 'we_versions/we_editor_versions.inc.php';
			default:
				$this->EditPageNr = we_base_constants::WE_EDITPAGE_PROPERTIES;
				$_SESSION['weS']['EditPageNr'] = we_base_constants::WE_EDITPAGE_PROPERTIES;
				return 'we_templates/we_editor_properties.inc.php';
		}
		return $this->TemplatePath;
	}

	public function we_new(){
		parent::we_new();
		$this->Filename = $this->i_getDefaultFilename();
	}

	function isValidEditPage($editPageNr){
		if($editPageNr == we_base_constants::WE_EDITPAGE_VALIDATION){
			return ($this->ContentType == we_base_ContentTypes::CSS);
		}

		return parent::isValidEditPage($editPageNr);
	}

	private static function replaceWEIDs($doc){
		$matches = array();
		if(preg_match_all('|#WE:(\d+)#|', $doc, $matches)){
			$matches = array_unique($matches[1], SORT_NUMERIC);
			if(!$matches){
				return $doc;
			}
			$urlReplace = we_folder::getUrlReplacements($GLOBALS['DB_WE'], true);
			foreach($matches as $match){
				$path = id_to_path($match, FILE_TABLE, $GLOBALS['DB_WE']);
				if($urlReplace){
					$http = preg_replace($urlReplace, array_keys($urlReplace), $path, -1, $cnt);
					$path = ($cnt ? 'http:' : getServerUrl()) . $http;
				}
				$doc = str_replace('#WE:' . $match . '#', $path, $doc);
			}
		}
		return $doc;
	}

	function getPath(){
		if($this->parseFile && $this->ContentType == we_base_ContentTypes::CSS && ($this->Extension == '.less' || $this->Extension == '.scss')){
			return rtrim($this->getParentPath(), '/') . '/' . ( isset($this->Filename) ? $this->Filename : '' ) . '.css';
		}
		return parent::getPath();
	}

	protected function i_getDocumentToSave(){
		$doc = parent::i_getDocumentToSave();
		switch($this->ContentType){
			case we_base_ContentTypes::CSS:
				switch($this->Extension){
					case '.sass':
					case '.css':
						break;
					case '.less':
						if($this->parseFile){
							$less = new we_helpers_lessc();
							$less->setCurrentPath($this->getParentPath());
							$less->setFormatter('classic');
							try{
								$doc = $less->compile($doc);
							} catch (exception $e){
								$this->errMsg = $e->getMessage();
								return false;
							}
						}
						break;
					case '.scss':
						if($this->parseFile){
							$scss = new we_helpers_scss();
							$scss->setImportPaths(array_unique(array('', $_SERVER['DOCUMENT_ROOT'] . $this->getParentPath(), $_SERVER['DOCUMENT_ROOT'] . '/')));
							try{
								$doc = $scss->compile($doc);
							} catch (exception $e){
								$this->errMsg = $e->getMessage();
								return false;
							}
						}
				}
			//no break;
			case we_base_ContentTypes::JS:
				$doc = self::replaceWEIDs($doc);
				break;
			default:
		}
		return $doc;
	}

	function formParseFile(){
		return we_html_forms::checkboxWithHidden((bool) $this->parseFile, 'we_' . $this->Name . '_parseFile', g_l('weClass', '[parseFile]'), false, 'defaultfont', '_EditorFrame.setEditorIsHot(true);');
	}

}
