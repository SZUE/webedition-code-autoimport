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
 * @package    webEdition_class
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/*  a class for handling flashDocuments. */

class we_otherDocument extends we_binaryDocument{
	/* Name of the class => important for reconstructing the class from outside the class */

	var $ClassName = __CLASS__;

	/* ContentType of the Object  */
	var $ContentType = 'application/*';

	/* Constructor */

	function __construct(){
		//Begin: Do we use this?
		switch($this->Extension){
			case ".pdf":
				$this->Icon = "pdf.gif";
				break;
		}
		// End: Do we use this?
		parent::__construct();
		$this->EditPageNrs[] = WE_EDITPAGE_PREVIEW;
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor(){
		switch($this->EditPageNr){
			case WE_EDITPAGE_PREVIEW:
				return "we_templates/we_editor_other_preview.inc.php";
			default:
				return parent::editor();
		}
	}

	/* gets the HTML for including in HTML-Docs */

	function getHtml($dyn = false){
		$_data = $this->getElement("data");
		$this->html = ($this->ID || ($_data && !is_dir($_data) && is_readable($_data)) ?
				'<p class="defaultfont"><b>Datei</b>: ' . $this->Text . '</p>' :
				g_l('global', "[no_file_uploaded]"));

		return $this->html;
	}

	function formExtension2(){
		return $this->htmlFormElementTable($this->htmlTextInput("we_" . $this->Name . "_Extension", 5, $this->Extension, "", 'onChange="_EditorFrame.setEditorIsHot(true);" style="width:92px"'), g_l('weClass', "[extension]"));
	}

	function we_save($resave = 0){
		$ct = new we_base_ContentTypes();
		$this->Icon = $ct->getIcon($this->ContentType, '', $this->Extension);
		return parent::we_save($resave);
	}

	function insertAtIndex(){
		$text = '';
		$this->resetElements();
		while((list($k, $v) = $this->nextElement(''))) {
			$foo = (isset($v["dat"]) && substr($v["dat"], 0, 2) == "a:") ? unserialize($v["dat"]) : '';
			if(!is_array($foo)){
				if(isset($v["type"]) && $v["type"] == "txt" && isset($v["dat"])){
					$text .= ' ' . trim($v["dat"]);
				}
			}
		}
		$text = trim(strip_tags($text));

		switch($this->Extension){
			case '.doc':
			case '.xls':
			case '.pps':
			case '.ppt':
			case '.rtf':
				$content = $this->i_getDocument(1000000);
				break;
			case '.odt':
			case '.ods':
			case '.odf':
			case '.odp':
			case '.odg':
			case '.ott':
			case '.ots':
			case '.otf':
			case '.otp':
			case '.otg':
				if(class_exists('ZipArchive') && (isset($this->elements['data']['dat']) && file_exists($this->elements['data']['dat']))){
					$zip = new ZipArchive;
					if($zip->open($this->elements['data']['dat']) === TRUE){
						$content = CheckAndConvertISOfrontend(strip_tags(preg_replace(array('|</text[^>]*>|', '|<text[^/>]*/>|'), ' ', str_replace(array('&#x0d;', '&#x0a;'), ' ', $zip->getFromName('content.xml')))));
						$zip->close();
						break;
					}
				}
				$content = '';
				break;
			case '.pdf':
			default:
				$content = '';
		}

		/* if($this->Extension == ".pdf" && function_exists("gzuncompress")){
		  $content = $this->getPDFText($this->i_getDocument());
		  } */
		$content = preg_replace('/[\x00-\x1F]/', '', $content);
		$text.= ' '.trim($content);

		$maxDB = min(1000000, getMaxAllowedPacket($this->DB_WE) - 1024);
		$text = substr(preg_replace('/  +/', ' ', $text), 0, $maxDB);

		if($this->IsSearchable && $this->Published){
			$set = array(
				'DID' => intval($this->ID),
				'Text' => $text,
				'BText' => $text,
				'Workspace' => $this->ParentPath,
				'WorkspaceID' => intval($this->ParentID),
				'Category' => $this->Category,
				'Doctype' => '',
				'Title' => $this->getElement("Title"),
				'Description' => $this->getElement("Description"),
				'Path' => $this->Path);
			return $this->DB_WE->query('REPLACE INTO ' . INDEX_TABLE . ' SET ' . we_database_base::arraySetter($set));
		}
		$this->DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE DID=' . intval($this->ID));
		return true;
	}

	function i_descriptionMissing(){
		if($this->IsSearchable){
			$description = $this->getElement("Description");
			return strlen($description) ? false : true;
		}
		return false;
	}

	/*
	  function nextline(){
	  $pos = strpos($this->_buffer, "\r");
	  if($pos === false){
	  return false;
	  }
	  $line = substr($this->_buffer, 0, $pos);
	  $this->_buffer = substr($this->_buffer, $pos + 1);
	  if($line == "stream"){
	  $endpos = strpos($this->_buffer, "endstream");
	  $stream = substr($this->_buffer, 1, $endpos - 1);
	  $stream = gzuncompress($stream);
	  $this->_buffer = $stream . substr($this->_buffer, $endpos + 9);
	  }
	  return $line;
	  }

	  function txtline(){
	  $line = $this->nextline();
	  if($line === false){
	  return false;
	  }
	  $match = array();
	  if(preg_match('/[^\\\\]\\((.+)[^\\\\]\\)/', $line, $match)){
	  $line = preg_replace("/\\\\(\d+)/e", "chr(0\\1);", $match[1]);
	  return stripslashes($line);
	  }
	  return $this->txtline();
	  }

	  function getPDFText($str){
	  $out = '';
	  $this->_buffer = $str;
	  while(($line = $this->txtline()) !== false) {
	  $out .= $line;
	  }
	  return $out;
	  }
	 */

	static function checkAndPrepare($formname, $key = 'we_document'){
		// check to see if there is an image to create or to change
		if(isset($_FILES["we_ui_$formname"]) && is_array($_FILES["we_ui_$formname"])){

			$webuserId = isset($_SESSION["webuser"]["ID"]) ? $_SESSION["webuser"]["ID"] : 0;

			if(isset($_FILES["we_ui_$formname"]["name"]) && is_array($_FILES["we_ui_$formname"]["name"])){
				foreach($_FILES["we_ui_$formname"]["name"] as $binaryName => $filename){
					$_binaryDataId = isset($_REQUEST['WE_UI_BINARY_DATA_ID_' . $binaryName]) ? $_REQUEST['WE_UI_BINARY_DATA_ID_' . $binaryName] : false;

					if($_binaryDataId !== false && isset($_SESSION[$_binaryDataId])){
						$_SESSION[$_binaryDataId]['doDelete'] = false;

						if(isset($_REQUEST["WE_UI_DEL_CHECKBOX_" . $binaryName]) && $_REQUEST["WE_UI_DEL_CHECKBOX_" . $binaryName] == 1){
							$_SESSION[$_binaryDataId]['doDelete'] = true;
						} elseif($filename){
							// file is selected, check to see if it is an image
							$ct = getContentTypeFromFile($filename);
							if($ct == "application/*"){
								$binaryId = intval($GLOBALS[$key][$formname]->getElement($binaryName));

								// move document from upload location to tmp dir
								$_SESSION[$_binaryDataId]["serverPath"] = TEMP_PATH . "/" . weFile::getUniqueId();
								move_uploaded_file(
									$_FILES["we_ui_$formname"]["tmp_name"][$binaryName], $_SESSION[$_binaryDataId]["serverPath"]);



								$tmp_Filename = $binaryName . "_" . weFile::getUniqueId() . "_" . preg_replace(
										"/[^A-Za-z0-9._-]/", "", $_FILES["we_ui_$formname"]["name"][$binaryName]);

								if($binaryId){
									$_SESSION[$_binaryDataId]["id"] = $binaryId;
								}

								$_SESSION[$_binaryDataId]["fileName"] = preg_replace('#^(.+)\..+$#', '\\1', $tmp_Filename);
								$_SESSION[$_binaryDataId]["extension"] = (strpos($tmp_Filename, ".") > 0) ? preg_replace(
										'#^.+(\..+)$#', '\\1', $tmp_Filename) : "";
								$_SESSION[$_binaryDataId]["text"] = $_SESSION[$_binaryDataId]["fileName"] . $_SESSION[$_binaryDataId]["extension"];
								$_SESSION[$_binaryDataId]["type"] = $_FILES["we_ui_$formname"]["type"][$binaryName];
								$_SESSION[$_binaryDataId]["size"] = $_FILES["we_ui_$formname"]["size"][$binaryName];
							}
						}
					}
				}
			}
		}
	}

}

