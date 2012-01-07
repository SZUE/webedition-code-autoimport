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
	var $ContentType = "application/*";

	/* buffer for pdf text creation */
	var $_buffer = "";



	/* Constructor */

	function __construct(){
		/* Begin: Do we use this? */
		switch($this->Extension){
			case ".pdf":
				$this->Icon = "pdf.gif";
				break;
		}
		/* End: Do we use this? */
		parent::__construct();
		array_push($this->EditPageNrs, WE_EDITPAGE_PREVIEW);
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

	function getHtml($dyn=false){
		global $lngDir, $we_transaction;
		$_data = $this->getElement("data");
		if($this->ID || ($_data && !is_dir($_data) && is_readable($_data))){
			$this->html = '<p class="defaultfont"><b>Datei</b>: ' . $this->Text . '</p>';
		} else{
			$this->html = g_l('global', "[no_file_uploaded]");
		}
		return $this->html;
	}

	function formExtension2(){
		return $this->htmlFormElementTable($this->htmlTextInput("we_" . $this->Name . "_Extension", 5, $this->Extension, "", 'onChange="_EditorFrame.setEditorIsHot(true);" style="width:92px"'), g_l('weClass', "[extension]"));
	}

	function we_save($resave=0){
		$ct = new we_base_ContentTypes();
		$this->Icon = $ct->getIcon($this->ContentType, '', $this->Extension);
		return parent::we_save($resave);
	}

	function insertAtIndex(){
		$text = "";
		$this->resetElements();
		while(list($k, $v) = $this->nextElement("")) {
			$foo = (isset($v["dat"]) && substr($v["dat"], 0, 2) == "a:") ? unserialize($v["dat"]) : "";
			if(!is_array($foo)){
				if(isset($v["type"]) && $v["type"] == "txt"){
					$text .= " " . (isset($v["dat"]) ? $v["dat"] : "");
				}
			}
		}

		$content = ($this->Extension == ".doc" || $this->Extension == ".xls" || $this->Extension == ".pps" || $this->Extension == ".ppt" || $this->Extension == ".rtf") ? $this->i_getDocument() : "";

		/* if($this->Extension == ".pdf" && function_exists("gzuncompress")){
		  $content = $this->getPDFText($this->i_getDocument());
		  } */

		for($i = 0; $i < 48; $i++){
			$content = str_replace(chr($i), "", $content);
		}

		$text = trim(strip_tags($text) . $content);

		$maxDB = getMaxAllowedPacket($this->DB_WE) - 1024;
		$maxDB = min(1000000, $maxDB);
		if(strlen($text) > $maxDB){
			$text = substr($text, 0, $maxDB);
		}
		$text = addslashes($text);

		$this->DB_WE->query("DELETE FROM " . INDEX_TABLE . " WHERE DID=" . intval($this->ID));
		if($this->IsSearchable && $this->Published){
			return $this->DB_WE->query("INSERT INTO " . INDEX_TABLE . " (DID,Text,BText,Workspace,WorkspaceID,Category,Doctype,Title,Description,Path) VALUES(" . intval($this->ID) . ",'" . $this->DB_WE->escape($text) . "','" . $this->DB_WE->escape($text) . "','" . $this->DB_WE->escape($this->ParentPath) . "'," . intval($this->ParentID) . ",'" . $this->DB_WE->escape($this->Category) . "','','" . $this->DB_WE->escape($this->getElement("Title")) . "','" . $this->DB_WE->escape($this->getElement("Description")) . "','" . $this->DB_WE->escape($this->Path) . "')");
		}
		return true;
	}

	function i_descriptionMissing(){
		if($this->IsSearchable){
			$description = $this->getElement("Description");
			return strlen($description) ? false : true;
		}
		return false;
	}

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
		if(preg_match("/[^\\\\]\\((.+)[^\\\\]\\)/", $line, $match)){
			$line = preg_replace("/\\\\(\d+)/e", "chr(0\\1);", $match[1]);
			return stripslashes($line);
		}
		return $this->txtline();
	}

	function getPDFText($str){
		$out = "";
		$this->_buffer = $str;
		while(($line = $this->txtline()) !== false) {
			$out .= $line;
		}
		return $out;
	}

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
						} else
						if($filename){
							// file is selected, check to see if it is an image
							$ct = getContentTypeFromFile($filename);
							if($ct == "application/*"){
								$binaryId = intval($GLOBALS[$key][$formname]->getElement($binaryName));

								// move document from upload location to tmp dir
								$_SESSION[$_binaryDataId]["serverPath"] = TMP_DIR . "/" . md5(
										uniqid(rand(), 1));
								move_uploaded_file(
									$_FILES["we_ui_$formname"]["tmp_name"][$binaryName], $_SESSION[$_binaryDataId]["serverPath"]);



								$tmp_Filename = $binaryName . "_" . md5(uniqid(rand(), 1)) . "_" . preg_replace(
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
