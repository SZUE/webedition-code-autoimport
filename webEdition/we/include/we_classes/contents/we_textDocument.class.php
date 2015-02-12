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
		$this->setElement('Charset', DEFAULT_CHARSET, 'attrib');
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
				}
				$GLOBALS['we_editmode'] = false;
				return 'we_templates/we_srcTmpl.inc.php';
			case we_base_constants::WE_EDITPAGE_VALIDATION:
				return 'we_templates/validateDocument.inc.php';
			case we_base_constants::WE_EDITPAGE_VERSIONS:
				return 'we_editors/we_editor_versions.inc.php';
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
			$paths = id_to_path($matches, FILE_TABLE, $GLOBALS['DB_WE'], false, true);
			foreach($paths as $match => $path){
				$doc = str_replace('#WE:' . $match . '#', ($urlReplace ? preg_replace($urlReplace, array_keys($urlReplace), $path) : $path), $doc);
			}
		}
		return $doc;
	}

	public function we_save($resave = 0, $skipHook = 0){
		if($this->ContentType === we_base_ContentTypes::HTACESS && $this->ParentID == 0){
			//pretest new htaccess file
			$doc = parent::i_getDocumentToSave();
			$oldDoc = ($this->ID ? f('SELECT Dat FROM ' . LINK_TABLE . ' JOIN ' . CONTENT_TABLE . ' ON CID=ID WHERE DID=' . $this->ID . ' AND Name="data"', '', $this->DB_WE) : '');
			$ok = we_base_file::save($_SERVER['DOCUMENT_ROOT'] . $this->Path, $doc);
			$data = getHTTP(getServerUrl(true), WEBEDITION_DIR . 'triggerWEtasks.php');
			$data2 = getHTTP(getServerUrl(true), WEBEDITION_DIR . 'index.html');
			if($data != 'OK' || strlen($data2) != filesize(WEBEDITION_PATH . 'index.html')){//generated error codes; since fopen is not capable of returning proper codes
				//restore old htaccess
				if($this->ID){
					we_base_file::save($_SERVER['DOCUMENT_ROOT'] . $this->Path, $oldDoc);
				} else {
					we_base_file::delete($_SERVER['DOCUMENT_ROOT'] . $this->Path);
				}
				$this->errMsg = 'Error 500';
				return false;
			}
		}
		return parent::we_save($resave, $skipHook);
	}

	protected function i_writeSiteDir($doc){
		if($this->ContentType === we_base_ContentTypes::HTACESS){
			return true;
		}
		return parent::i_writeSiteDir($doc);
	}

	function getPath(){
		if($this->parseFile && $this->ContentType == we_base_ContentTypes::CSS && ($this->Extension === '.less' || $this->Extension === '.scss')){
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
							$less = new lessc();
							we_helpers_lessParser::$includedFiles = array();
							$less->setImportDir(array(
								$_SERVER['DOCUMENT_ROOT'],
								$_SERVER['DOCUMENT_ROOT'] . $this->getParentPath(),
							));
							$less->setFormatter('classic');
							try{
								//we prepend an extra / before #WE, to make parser believe this is an absolute path
								$doc = str_replace('/#WE:', '#WE:', $less->compile(preg_replace('|(#WE:\d+#)|', '/$1', $doc)));
							} catch (exception $e){
								$this->errMsg = str_replace(array('\n', "\n"), ' ', $e->getMessage());
								return false;
							}
						}
						break;
					case '.scss':
						if($this->parseFile){
							$scss = new we_helpers_scss();
							we_helpers_scss::$includedFiles = array(); //due to rebuild!
							$scss->setImportPaths(array_unique(array('', $_SERVER['DOCUMENT_ROOT'] . $this->getParentPath(), $_SERVER['DOCUMENT_ROOT'] . '/')));
							try{
								$doc = $scss->compile($doc);
							} catch (exception $e){
								$this->errMsg = str_replace(array('\n', "\n"), ' ', $e->getMessage());
								return false;
							}
						}
				}
			//no break;
			case we_base_ContentTypes::JS:
				$doc = self::replaceWEIDs($doc);
				//FIXME: write all dependend files to database link, this should be the same table, as used for media queries in 6.5
				break;
			default:
		}
		return $doc;
	}

	function formParseFile(){
		return we_html_forms::checkboxWithHidden((bool) $this->parseFile, 'we_' . $this->Name . '_parseFile', g_l('weClass', '[parseFile]'), false, 'defaultfont', '_EditorFrame.setEditorIsHot(true);');
	}

}
