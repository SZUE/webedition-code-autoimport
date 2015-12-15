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
/*  a class for handling flashDocuments. */

class we_otherDocument extends we_binaryDocument{

	function __construct(){
		parent::__construct();
		if(isWE()){
			$this->EditPageNrs[] = we_base_constants::WE_EDITPAGE_PREVIEW;
		}
		$this->ContentType = we_base_ContentTypes::APPLICATION;
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor(){
		switch($this->EditPageNr){
			case we_base_constants::WE_EDITPAGE_PREVIEW:
				return 'we_editors/we_editor_other_preview.inc.php';
			default:
				return parent::editor();
		}
	}

	/* gets the HTML for including in HTML-Docs */

	function getHtml($dyn = false){
		$_data = $this->getElement('data');
		$this->html = ($this->ID || ($_data && !is_dir($_data) && is_readable($_data)) ?
						'<p class="defaultfont"><b>Datei</b>: ' . $this->Text . '</p>' :
						g_l('global', '[no_file_uploaded]'));

		return $this->html;
	}

	protected function formExtension2(){
		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('we_' . $this->Name . '_Extension', 5, $this->Extension, '', 'onchange="WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);" style="width:92px"'), g_l('weClass', '[extension]'));
	}

	public function we_save($resave = false, $skipHook = false){
		return parent::we_save($resave, $skipHook);
	}

	/**
	 * create instance of weMetaData to access metadata functionality:
	 */
	protected function getMetaDataReader($force = false){
		return ($this->Extension === '.pdf' ?
						parent::getMetaDataReader(true) :
						false);
	}

	public function insertAtIndex(array $only = null, array $fieldTypes = null){
		if(!($this->IsSearchable && $this->Published)){
			$this->DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE ClassID=0 AND ID=' . intval($this->ID));
			return true;
		}

		$text = '';
		$this->resetElements();
		while((list($k, $v) = $this->nextElement(''))){
			$foo = (isset($v['dat']) ? we_unserialize($v['dat']) : '');
			if(!is_array($foo)){
				if(isset($v['type']) && $v['type'] === 'txt' && isset($v['dat'])){
					$text .= ' ' . trim($v['dat']);
				}
			}
		}
		$text = trim(strip_tags($text));

		switch($this->Extension){
			case '.doc':
			case '.xls':
			case '.pps':
			case '.ppt':
				$content = '';
				break;
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
				$name = $this->getElement('data');
				if(class_exists('ZipArchive') && file_exists($name)){
					$zip = new ZipArchive;
					if($zip->open($name) === TRUE){
						$content = CheckAndConvertISOfrontend(strip_tags(preg_replace(array('|</text[^>]*>|', '|<text[^/>]*/>|'), ' ', str_replace(array('&#x0d;', '&#x0a;'), ' ', $zip->getFromName('content.xml')))));
						$zip->close();
						break;
					}
				}
				$content = '';
				break;
			case '.pdf':
				$name = $this->getElement('data');
				if(file_exists($name) && (filesize($name) * 2 < we_convertIniSizes(ini_get('memory_limit')))){
					$pdf = new we_helpers_pdf2text($name);
					$content = CheckAndConvertISOfrontend($pdf->processText());
					break;
				}
			default:
				$content = '';
		}

		$content = preg_replace('/[\x00-\x1F]/', '', $content);
		$text.= ' ' . trim($content);

		$maxDB = 65535;

		$set = array(
			'ID' => intval($this->ID),
			'DID' => intval($this->ID),
			'Text' => substr(preg_replace(array("/\n+/", '/  +/'), ' ', $text), 0, $maxDB),
			'WorkspaceID' => intval($this->ParentID),
			'Category' => $this->Category,
			'Doctype' => '',
			'Title' => $this->getElement('Title'),
			'Description' => $this->getElement('Description'),
			'Path' => $this->Path);
		return $this->DB_WE->query('REPLACE INTO ' . INDEX_TABLE . ' SET ' . we_database_base::arraySetter($set));
	}

	protected function i_descriptionMissing(){
		return ($this->IsSearchable ?
						($this->getElement('Description') === '') :
						false);
	}

	public function setMetaDataFromFile($file){
		if($this->Extension === '.pdf' && file_exists($file)){
			$pdf = new we_helpers_pdf2text($file);
			$metaData = $pdf->getInfo();
			if($metaData){
				if(isset($metaData['Title']) && !$this->getElement('Title')){
					$this->setElement('Title', $metaData['Title']);
				}
				if(isset($metaData['Keywords']) && !$this->getElement('Keywords')){
					$this->setElement('Keywords', $metaData['Keywords']);
				}
				if(isset($metaData['Subject']) && !$this->getElement('Description')){
					$this->setElement('Description', $metaData['Subject']);
				}
			}
		}
	}

	static function checkAndPrepare($formname, $key = 'we_document'){
		// check to see if there is an image to create or to change
		if(isset($_FILES["we_ui_$formname"]) && is_array($_FILES["we_ui_$formname"]) &&
				isset($_FILES["we_ui_$formname"]["name"]) && is_array($_FILES["we_ui_$formname"]["name"])){
			foreach($_FILES["we_ui_$formname"]["name"] as $binaryName => $filename){
				$_binaryDataId = we_base_request::_(we_base_request::STRING, 'WE_UI_BINARY_DATA_ID_' . $binaryName);

				if($_binaryDataId !== false && isset($_SESSION[$_binaryDataId])){
					$_SESSION[$_binaryDataId]['doDelete'] = false;

					if(we_base_request::_(we_base_request::BOOL, 'WE_UI_DEL_CHECKBOX_' . $binaryName)){
						$_SESSION[$_binaryDataId]['doDelete'] = true;
					} elseif($filename){
						// file is selected, check to see if it is an image
						$ct = getContentTypeFromFile($filename);
						if($ct == we_base_ContentTypes::APPLICATION){
							$binaryId = intval($GLOBALS[$key][$formname]->getElement($binaryName));

							// move document from upload location to tmp dir
							$_SESSION[$_binaryDataId]['serverPath'] = TEMP_PATH . we_base_file::getUniqueId();
							move_uploaded_file($_FILES["we_ui_$formname"]['tmp_name'][$binaryName], $_SESSION[$_binaryDataId]['serverPath']);

							$tmp_Filename = $binaryName . '_' . we_base_file::getUniqueId() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', $_FILES["we_ui_$formname"]['name'][$binaryName]);

							if($binaryId){
								$_SESSION[$_binaryDataId]['id'] = $binaryId;
							}

							$_SESSION[$_binaryDataId]['fileName'] = preg_replace('#^(.+)\..+$#', '$1', $tmp_Filename);
							$_SESSION[$_binaryDataId]['extension'] = (strpos($tmp_Filename, '.') > 0) ? preg_replace('#^.+(\..+)$#', '$1', $tmp_Filename) : '';
							$_SESSION[$_binaryDataId]['text'] = $_SESSION[$_binaryDataId]['fileName'] . $_SESSION[$_binaryDataId]['extension'];
							$_SESSION[$_binaryDataId]['type'] = $_FILES["we_ui_$formname"]['type'][$binaryName];
							$_SESSION[$_binaryDataId]['size'] = $_FILES["we_ui_$formname"]['size'][$binaryName];
						}
					}
				}
			}
		}
	}

}
