<?php

/**
 * webEdition CMS
 *
 * $Rev: 10461 $
 * $Author: lukasimhof $
 * $Date: 2015-09-18 15:20:39 +0200 (Fr, 18 Sep 2015) $
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
class we_fileupload_resp_import extends we_fileupload_resp_base{
	//protected $writeToID = 0;
	//protected $importToID = 0;//Parent

	public function __construct($name = '', $contentType = '', $FILE = array(), $fileVars = array(), $controlVars = array(), $docVars = array()){
		parent::__construct($name, $contentType, $FILE, $fileVars, $controlVars, $docVars);
	}

	protected function initByHttp(){
		parent::initByHttp();

		$this->docVars = array_filter(
			array(
				'transaction' => we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', we_base_request::NOT_VALID),
				'importMetadata' => we_base_request::_(we_base_request::INT, "importMetadata", we_base_request::NOT_VALID),
				'imgsSearchable' => we_base_request::_(we_base_request::INT, "imgsSearchable", we_base_request::NOT_VALID),
				'title' => we_base_request::_(we_base_request::STRING, 'img_title', we_base_request::NOT_VALID),
				'alt' => we_base_request::_(we_base_request::STRING, 'img_alt', we_base_request::NOT_VALID),
				'thumbs' => we_base_request::_(we_base_request::INTLIST, 'thumbs', we_base_request::NOT_VALID),
				'width' => we_base_request::_(we_base_request::INT, "width", we_base_request::NOT_VALID),
				'widthSelect' => we_base_request::_(we_base_request::STRING, "widthSelect", we_base_request::NOT_VALID),
				'height' => we_base_request::_(we_base_request::INT, "height", we_base_request::NOT_VALID),
				'heihgtSelect' => we_base_request::_(we_base_request::STRING, "heightSelect", we_base_request::NOT_VALID),
				'quality' => we_base_request::_(we_base_request::INT, "quality", we_base_request::NOT_VALID),
				'keepRatio' => we_base_request::_(we_base_request::BOOL, "keepRatio", we_base_request::NOT_VALID),
				'degrees' => we_base_request::_(we_base_request::INT, "degrees", we_base_request::NOT_VALID),
			), function($var){return $var !== we_base_request::NOT_VALID;}
		);
	}

	protected function postprocess(){
		$we_doc = $this->getWebeditionDocument();
		//TODO: make concise mime and extension test here, taking uploader's typecondition from session
		/*
		if($this->typeCondition['accepted']['mime'] && !in_array($this->fileVars['weFileCt'], $this->typeCondition['accepted']['mime'])){
			if(!empty($this->fileVars['weFileName'])){
				$we_alerttext = g_l('alert', '[wrong_file][' . $we_doc->ContentType . ']');
			} else {
				$we_alerttext = g_l('alert', '[no_file_selected]');
			}
		}
		 * 
		 */

		$response = $this->writeWebeditionDocument($we_doc);

		if($response['success']){
			return array_merge($this->response, array('status' => 'success', 'completed' => 1, 'weDoc' => $response['weDoc']));
		} else {
			return array_merge($this->response, array('status' => 'failure', 'message' => $response['error']));
		}
	}

	protected function getWebeditionDocument(){ // TODO: avoid some more redundancy in this fn
		if($this->docVars['transaction']){ // import ne binary for existing wedoc
			if(!isset($_SESSION['weS']['we_data'][$this->docVars['transaction']])){
				return array(
					'error' => 'transaction is not correct',
					'success' => false,
					'weDoc' => array('id' => 0, 'path' => '')
				);
			}
			$we_dt = $_SESSION['weS']['we_data'][$this->docVars['transaction']];
			include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

			$we_doc->Extension = strtolower((strpos($this->fileVars['weFileName'], '.') > 0) ? preg_replace('/^.+(\..+)$/', '$1', $this->fileVars['weFileName']) : ''); //strtolower for feature 3764
			if((!$we_doc->Filename) || (!$we_doc->ID)){
				// Bug Fix #6284
				$we_doc->Filename = preg_replace('/[^A-Za-z0-9._-]/', '', $this->fileVars['weFileName']);
				$we_doc->Filename = preg_replace('/^(.+)\..+$/', '$1', $we_doc->Filename);
			}
			$we_doc->Text = $we_doc->Filename . $we_doc->Extension;
			$we_doc->Path = $we_doc->getPath();

			return $we_doc;
		}

		// make new we_doc
		$we_ContentType = getContentTypeFromFile($this->fileVars['weFileName']);
		include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

		// set filename, ext and path
		$filename = we_import_functions::correctFilename($this->fileVars['weFileName']);
		$matches = array();
		preg_match('#^(.*)(\..+)$#', $filename, $matches);
		if(!$matches){
			return array(
				'error' => g_l('importFiles', '[save_error]'),
				'success' => false,
				'weDoc' => ''
			);
		}
		$we_doc->Filename = $matches[1];
		$we_doc->Extension = strtolower($matches[2]);
		if(!$we_doc->Filename){ // .htaccess
			$we_doc->Filename = $matches[2];//.htaccess
			$we_doc->Extension = '';
		}
		$we_doc->Text = $we_doc->Filename . $we_doc->Extension;
		$we_doc->setParentID($this->fileVars['saveToID']);
		$we_doc->Path = $we_doc->getParentPath() . (($we_doc->getParentPath() != '/') ? '/' : '') . $we_doc->Text;

		// if file exists we have to see if we should create a new one or overwrite it!
		if(($file_id = f('SELECT ID FROM ' . FILE_TABLE . ' WHERE Path="' . $GLOBALS['DB_WE']->escape($we_doc->Path) . '"'))){
			switch($this->fileVars['sameName']){
				case 'overwrite':
					$tmp = $we_doc->ClassName;
					$we_doc = new $tmp();
					$we_doc->initByID($file_id, FILE_TABLE);
					break;
				case "rename":
					$z = 0;
					$footext = $we_doc->Filename . '_' . $z . $we_doc->Extension;
					while(f('SELECT ID FROM ' . FILE_TABLE . " WHERE Text='" . $GLOBALS['DB_WE']->escape($footext) . "' AND ParentID=" . intval($this->fileVars['saveToID']))){
						$z++;
						$footext = $we_doc->Filename . '_' . $z . $we_doc->Extension;
					}
					$we_doc->Text = $footext;
					$we_doc->Filename = $we_doc->Filename . "_" . $z;
					$we_doc->Path = $we_doc->getParentPath() . (($we_doc->getParentPath() != '/') ? '/' : '') . $we_doc->Text;
					break;
				default:
					return array(
						'error' => g_l('importFiles', '[same_name]'),
						'success' => false,
						'weDoc' => ''
					);
			}
		}

		return $we_doc;
	}

	protected function writeWebeditionDocument($we_doc){
		$tempFile =  $_SERVER['DOCUMENT_ROOT'] . $this->fileVars['fileTemp'];
		// TODO: there are more bad combinations to consider
		if($we_doc->ContentType === we_base_ContentTypes::IMAGE){
			if(!$we_doc->isSvg() && !in_array(we_base_imageEdit::detect_image_type($tempFile), we_base_imageEdit::$GDIMAGE_TYPE)){
				return array('filename' => $this->fileVars['weFileName'], 'error' => g_l('alert', '[wrong_file][' . $we_doc->ContentType . ']'));
			}
		}

		$we_doc->setElement('type', $we_doc->ContentType, "attrib");
		//$we_doc->Published = time(); // nok for parked existing docs

		if(($fh = @fopen($tempFile, 'rb'))){
			if($we_doc->isBinary()){
				$we_doc->setElement("data", $tempFile);
			} else {
				$mime = explode('/', $this->fileVars['weFileCt']);
				$we_doc->setElement("data", $tempFile, $mime[0]);
			}
			fclose($fh);
		} else {
			//FIXME: fopen uses less memory then gd: gd can fail (and returns 500) even if $fh = true! // ?
			//return array('filename' => $_FILES['we_File']['name'], 'error' => g_l('importFiles', '[read_file_error]'));
			return array(
				'error' => g_l('importFiles', '[read_file_error]'),
				'success' => false,
				'weDoc' => ''
			);
		}

		switch($we_doc->ContentType){
			case we_base_ContentTypes::IMAGE:
				if(isset($this->docVars['importMetadata']) && $this->docVars['importMetadata']){
					$we_doc->importMetaData($tempFile);
				}
				// no break
			case we_base_ContentTypes::FLASH:
				$we_size = $we_doc->getimagesize($tempFile);
				if(is_array($we_size) && count($we_size) >= 2){
					$we_doc->setElement("width", $we_size[0], "attrib");
					$we_doc->setElement("height", $we_size[1], "attrib");
					$we_doc->setElement("origwidth", $we_size[0], 'attrib');
					$we_doc->setElement("origheight", $we_size[1], 'attrib');
				}
				// no break
			default: 
				$we_doc->Table = FILE_TABLE;
				$this->fileVars['weFileSize'] = $this->fileVars['weFileSize'] < 1 ? 1 : $this->fileVars['weFileSize'];
				$we_doc->setElement('filesize', $this->fileVars['weFileSize'], 'attrib');
				/*
				now change the category
					$we_doc->Category = isset($this->docVars['categories']) && $this->docVars['categories'] = $this->docVars['categories'] : $we_doc->Category;
				*/
				if(isset($this->docVars['importMetadata']) && $this->docVars['importMetadata']){
					if($we_doc->Extension === '.pdf'){
						$we_doc->setMetaDataFromFile($tempFile);
					}
					if(we_base_request::_(we_base_request::BOOL, 'import_metadata')){
						$we_doc->importMetaData($tempFile);
					}
				}
				$we_doc->DocChanged = true;
		}

		if($we_doc->ContentType == we_base_ContentTypes::IMAGE){
			$newWidth = 0;
			$newHeight = 0;
			if(isset($this->docVars['width'])){
				$newWidth = ($this->docVars['widthSelect'] === 'percent' ?
						round(($we_doc->getElement("origwidth") / 100) * $this->docVars['width']) :
						$this->docVars['width']);
			}
			if(isset($this->docVars['height'])){
				$newHeight = ($this->docVars['heightSelect'] === 'percent' ?
						round(($we_doc->getElement("origheight") / 100) * $this->docVars['height']) :
						$this->docVars['height']);
			}
			if(($newWidth && ($newWidth != $we_doc->getElement("origwidth"))) || ($newHeight && ($newHeight != $we_doc->getElement("origheight")))){
				if($we_doc->resizeImage($newWidth, $newHeight, $this->docVars['quality'], $this->docVars['keepRatio'])){
					$this->docVars['width'] = $newWidth;
					$this->docVars['height'] = $newHeight;
				}
			}
			if($this->docVars['degrees']){
				$we_doc->rotateImage(
					($this->docVars['degrees'] % 180 == 0) ?
						$we_doc->getElement('origwidth') :
						$we_doc->getElement("origheight"), ($this->docVars['degrees'] % 180 == 0 ?
						$we_doc->getElement("origheight") :
						$we_doc->getElement("origwidth")), $this->docVars['degrees'], $this->docVars['quality']);
			}
		}

		if($this->docVars['transaction']){
			$_SESSION['weS']['we_data']['tmpName'] = $tempFile;// what's this?
			$we_doc->saveInSession($_SESSION['weS']['we_data'][$this->docVars['transaction']]); // save the changed object in session
		} else {
			$we_doc->setElement('title', (isset($this->docVars['title']) ? $this->docVars['title'] : $we_doc->title), 'attrib');
			$we_doc->setElement('alt', (isset($this->docVars['alt']) ? $this->docVars['alt'] : $we_doc->alt), 'attrib');
			$we_doc->setElement('Thumbs', (isset($this->docVars['thumbs']) ? $this->docVars['thumbs'] : $we_doc->alt), 'attrib');
			$we_doc->Thumbs = isset($this->docVars['thumbs']) ? $this->docVars['thumbs'] : $we_doc->thumbs;
			$we_doc->IsSearchable = isset($this->docVars['imgsSearchable']) ? $this->docVars['imgsSearchable'] : $we_doc->thumbs;

			if(!$we_doc->we_save()){
				return array(
					'error' => g_l('importFiles', '[save_error]'),
					'success' => false,
					'weDoc' => ''
				);
			}
			if(!$we_doc->we_publish()){
				return array(
					'error' => "publish_error",
					'success' => false,
					'weDoc' => ''
				);
			}
		}

		return array(
			'error' => array(),
			'success' => true,
			'weDoc' => array('id' => $we_doc->ID, 'path' => $we_doc->Path, 'text' => $we_doc->Text)
		);
	}
}