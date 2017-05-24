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
/*  a class for handling binary-documents like images. */

class we_binaryDocument extends we_document{
	/* The HTML-Code which can be included in a HTML Document */
	protected $html = '';

	/**
	 * Flag which indicates that the doc has changed!
	 * @var boolean
	 */
	public $DocChanged = false;

	/**
	 * @var object instance of metadata reader for accessing metadata functionality
	 */
	private $metaDataReader = null;
	var $documentCustomerFilter = ''; // DON'T SET TO NULL !
	protected $IsProtected = false;

	/**
	 * @var array for metadata read via $metaDataReader
	 */
	var $metaData = [];

	/** Constructor
	 * @return we_binaryDocument
	 * @desc Constructor for we_binaryDocument
	 */
	function __construct(){
		parent::__construct();
		array_push($this->persistent_slots, 'html', 'DocChanged', 'IsProtected');
		if(isWE()){
			array_push($this->EditPageNrs, we_base_constants::WE_EDITPAGE_PROPERTIES, we_base_constants::WE_EDITPAGE_INFO, we_base_constants::WE_EDITPAGE_CONTENT, we_base_constants::WE_EDITPAGE_VERSIONS);
			if(defined('CUSTOMER_TABLE') && (we_base_permission::hasPerm(['CAN_EDIT_CUSTOMERFILTER', 'CAN_CHANGE_DOCS_CUSTOMER']))){
				$this->EditPageNrs[] = we_base_constants::WE_EDITPAGE_WEBUSER;
			}
		}
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor(){
		switch($this->EditPageNr){
			default:
				$_SESSION['weS']['EditPageNr'] = $this->EditPageNr = we_base_constants::WE_EDITPAGE_PROPERTIES;
			case we_base_constants::WE_EDITPAGE_PROPERTIES:
				return new we_editor_properties($this);
			case we_base_constants::WE_EDITPAGE_IMAGEEDIT:
				return new we_editor_imageedit($this);
			case we_base_constants::WE_EDITPAGE_INFO:
				return new we_editor_info($this);
			case we_base_constants::WE_EDITPAGE_CONTENT:
				return new we_editor_binaryContent($this);
			case we_base_constants::WE_EDITPAGE_WEBUSER:
				return new we_editor_weDocumentCustomerFilter($this);
			case we_base_constants::WE_EDITPAGE_VERSIONS:
				return new we_editor_versions($this);
		}
	}

	protected function i_getContentData(){
		parent::i_getContentData();
		$sitePath = $this->getSitePath();
		$realPath = $this->getRealPath();
		if(!file_exists($sitePath) && file_exists($realPath) && !is_dir($realPath)){
			we_base_file::makeHardLink($realPath, $sitePath);
		}
		if(file_exists($sitePath) && filesize($sitePath)){
			$this->setElement('data', $sitePath, 'image');
		}
	}

	public function we_save($resave = false, $skipHook = false){
		if(!$this->issetElement('data')){
			$this->i_getContentData();
		}
		if($this->getFilesize() == 0){
			$this->errMsg = g_l('metadata', '[file_size_0]');
			return false;
		}
		if(parent::we_save($resave, $skipHook)){
			$this->DocChanged = false;
			$this->setElement('data', $this->getSitePath());
			$this->i_writeMetaValues();
			return $this->insertAtIndex();
		}

		return false;
	}

	public function we_publish(){
		return $this->we_save();
	}

	function i_getDocument($size = -1){
		$file = $this->getElement('data');
		return ($file && file_exists($file) ?
			($size == -1 ?
			we_base_file::load($file) :
			we_base_file::loadPart($file, 0, $size)
			) :
			'');
	}

	protected function i_writeDocument(){
		$file = $this->getElement('data');
		if(!($file && file_exists($file)) ||
			$this->i_pathNotValid()
		){
			return false;
		}
		$sitePath = $this->getSitePath();
		$realPath = $this->getRealPath();
		if(!we_base_file::checkAndMakeFolder(dirname($sitePath), true) ||
			!we_base_file::checkAndMakeFolder(dirname($realPath), true) ||
			($file != $sitePath && !we_base_file::copyFile($file, $sitePath))){
			return false;
		}
		if(!we_base_file::makeHardLink($file, $realPath)){
			return false;
		}
		if($this->isMoved()){
			we_base_file::delete($this->getRealPath(true));
			we_base_file::delete($this->getSitePath(true));
			$this->rewriteNavigation();
		}
		$this->update_filehash();

		return true;
	}

	protected function i_writeSiteDir($doc){
		//do nothing - remove functionality added
	}

	protected function i_writeMainDir($doc){
		//do nothing - remove functionality added
	}

	/** gets the filesize of the document */
	function getFilesize(){
		$file = $this->getElement('data');
		return (file_exists($file) ? filesize($file) : 0);
	}

	function insertAtIndex(array $only = null, array $fieldTypes = null){
		if(!(!empty($this->IsSearchable) && $this->Published)){
			$this->DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE ClassID=0 AND ID=' . intval($this->ID));
			return true;
		}

		$text = "";
		$this->resetElements();
		while((list($k, $v) = $this->nextElement(''))){
			$foo = (isset($v["dat"]) && substr($v["dat"], 0, 2) === 'a:') ? we_unserialize($v["dat"]) : "";
			if(!is_array($foo)){
				if(isset($v["type"]) && $v["type"] === 'txt'){
					$text .= ' ' . (isset($v["dat"]) ? $v["dat"] : '');
				}
			}
		}
		$set = [
			'ID' => intval($this->ID),
			'DID' => intval($this->ID),
			'Text' => $text,
			'WorkspaceID' => intval($this->ParentID),
			'Category' => $this->Category,
			'Doctype' => '',
			'Title' => $this->getElement('Title'),
			'Description' => $this->getElement('Description'),
		];
		return $this->DB_WE->query('REPLACE INTO ' . INDEX_TABLE . ' SET ' . we_database_base::arraySetter($set));
	}

	public function we_new(){
		parent::we_new();
		$this->Filename = $this->i_getDefaultFilename();
	}

	/**
	 * create instance of weMetaData to access metadata functionality:
	 */
	protected function getMetaDataReader($force = false){
		if($force){
			if(!$this->metaDataReader){
				$source = $this->getElement('data');
				if(file_exists($source)){
					$this->metaDataReader = new we_metadata_metaData($source);
				}
			}
			return $this->metaDataReader;
		}
		return false;
	}

	/**
	 * @abstract tries to read ebmedded metadata from file
	 * @return bool false if either no metadata is available or something went wrong
	 */
	function getMetaData(){
		$reader = $this->getMetaDataReader();
		if($reader){
			$this->metaData = $reader->getMetaData();
			if(!is_array($this->metaData)){
				return false;
			}
		}
		return $this->metaData;
	}

	protected function i_setElementsFromHTTP(){
		// preventing fields from override
		if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === 'update_file'){
			return;
		}
		parent::i_setElementsFromHTTP();
	}

	/**
	 * returns HTML code for embedded metadata of current image with custom form fields
	 */
	function formMetaData(){
		// first we fetch all defined metadata fields from tblMetadata:
		$defined_fields = we_metadata_metaData::getDefinedMetaDataFields();

		// show an alert if there are none
		if(empty($defined_fields)){
			return '';
		}

		// second we build all input fields for them and take
		// the elements of this imageDocument as values:
		$fieldcount = count($defined_fields);
		$content = new we_html_table(['class' => 'default', 'style' => "margin-top:4px;"], $fieldcount, 5);
		$mdcontent = '';
		for($i = 0; $i < $fieldcount; $i++){
			$tagName = $defined_fields[$i]["tag"];
			if($tagName != 'Title' && $tagName != 'Description' && $tagName != 'Keywords'){
				$type = $defined_fields[$i]['type'];
				//$mode = $defined_fields[$i]['mode'];
				//$csv = boolval($defined_fields[$i]['csv']);
				//$closed = boolval($defined_fields[$i]['closed']);

				switch($type){
					case 'textarea':
						$inp = $this->formTextArea('txt', $tagName, $tagName, 10, 30, ['onchange' => '_EditorFrame.setEditorIsHot(true);', 'style' => 'width:500px;height:150px;border: #AAAAAA solid 1px']);
						break;
					case 'wysiwyg':
						$inp = $this->formTextArea('txt', $tagName, $tagName, 10, 30, ['onchange' => '_EditorFrame.setEditorIsHot(true);', 'style' => 'width:500px;height:150px;border: #AAAAAA solid 1px']);
						break;
					case 'date':
						$inp = we_html_tools::htmlFormElementTable(we_html_tools::getDateInput('we_' . $this->Name . '_date[' . $tagName . ']', abs($this->getElement($tagName)), true), $tagName);
						break;
					case 'textfield':
					default:
						$inp = $this->formMetaField($tagName);
				}

				$content->setCol($i, 0, ["colspan" => 5, 'style' => 'padding-bottom:5px;'], $inp);
			}
		}

		$mdcontent .= $content->getHtml();

		// Return HTML
		return $mdcontent;
	}

	/**
	 * Returns HTML code for Upload Button and infotext
	 */
	function formUpload(){
		$isempty = empty(($size = $GLOBALS['we_doc']->getFilesize()));
		$size = g_l('metadata', '[filesize]') . ": " . round(($size / 1024), 2) . "&nbsp;KB";
		$ext = $isempty ? '' : $this->Extension;
		$typetext = g_l('metadata', '[filetype]') . ': ' . ($ext ? substr($ext, 1) : '');

		$metaData = $this->getMetaData();
		$mdtypes = [];
		if($metaData){
			if(!empty($metaData["exif"])){
				$mdtypes[] = "Exif";
			}
			if(!empty($metaData["iptc"])){
				$mdtypes[] = "IPTC";
			}
			if(!empty($metaData["pdf"])){
				$mdtypes[] = "PDF";
			}
		}
		$metatext = ($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE ? '' :
			g_l('metadata', '[supported_types]') . ': ' .
			'<a href="javascript:parent.frames.editHeader.weTabs.setActiveTab(\'tab_2\');we_cmd(\'switch_edit_page\',2,\'' . $GLOBALS['we_transaction'] . '\');">' .
			(count($mdtypes) > 0 ? implode(', ', $mdtypes) : g_l('metadata', '[none]')) .
			'</a>');

		$fileinfo = ['size' => $size,
			'ct' => $this->ContentType,
			'ext' => $ext,
			'typetext' => $typetext,
			'metatext' => $metatext,
			'thumbnail' => $this->getThumbnail(100, 100),
			'isempty' => $isempty
		];

		$fileUpload = new we_fileupload_ui_wedoc($this->ContentType);
		$fileUpload->setEditOptsHidden($this->getElement('data'));

		return $fileUpload->getHTML($fileinfo);
	}

	protected function getThumbnail($width = 150, $height = 100){
		return '';
	}

	function savebinarydata(){
		$data = $this->getElement('data');
		if($data && (strlen($data) > 512 || !@file_exists($data))){ //assume data>512 = binary data
			$path = we_base_file::saveTemp($data);
			$this->setElement('data', $path);
		}
	}

	public function isBinary(){
		return true;
	}

	public function formProperties(){

	}

	function formIsProtected(){
		return we_html_forms::checkboxWithHidden((bool) $this->IsProtected, 'we_' . $this->Name . '_IsProtected', g_l('weClass', '[protected]'), false, 'defaultfont', '_EditorFrame.setEditorIsHot(true);');
	}

	function formReferences(we_base_jsCmd $jsCmd){
		$search = new we_search_search();
		$search->searchMediaLinks(0, true, $this->ID);
		$ml = $search->getUsedMediaLinks();
		$accessibles = isset($ml['accessible']['mediaID_' . $this->ID]) ? $ml['accessible']['mediaID_' . $this->ID] : [];
		$notaccessibles = isset($ml['notaccessible']['mediaID_' . $this->ID]) ? $ml['notaccessible']['mediaID_' . $this->ID] : [];
		$groups = isset($ml['groups']['mediaID_' . $this->ID]) ? $ml['groups']['mediaID_' . $this->ID] : [];

		if(empty($groups)){
			return ['form' => g_l('weClass', '[notReferenced]'), 'num' => 0];
		}

		$js = "";
		$values = [];
		$c = $num = 0;
		$limit = 20;
		$ref = [];
		foreach($groups as $group){
			$cna = isset($notaccessibles[$group]) && is_array($notaccessibles[$group]) ? count($notaccessibles[$group]) : 0;
			$ca = (isset($accessibles[$group]) && is_array($accessibles[$group]) ? count($accessibles[$group]) : 0) + $cna;
			$num += $ca;

			$values[$group . ' (' . ($ca) . ($cna ? ', davon ' . $cna . ' ' . g_l('weClass', '[medialinks_unaccessible]') . '' : '') . ')'] = we_html_tools::OPTGROUP;
			$cc = 0;
			foreach((isset($accessibles[$group]) && is_array($accessibles[$group]) ? $accessibles[$group] : []) as $v){
				if($cc++ >= $limit){
					$values[-1] = '[ + ' . (count($accessibles[$group]) - $limit) . ' ' . g_l('weClass', '[medialinks_more]') . ' ]';
					break;
				}
				$element = preg_replace('|NN[0-9]\]+$|', 'NN]', $v['element']);
				$values[++$c] = $v['path'] . ($element ? ', in: ' . $element : '');
				$ref['id_' . $c] = $v;
			}
		}
		$button = we_html_button::create_button(we_html_button::EDIT, "javascript:top.we_openMediaReference(document.getElementById('MediaReferences').value);");
		$jsCmd->addCmd('setMediaReferences', $ref);
		$form = we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect('MediaReferences', $values, 1, '', false, [], 'value', 388), '', 'left', 'defaultfont', '', $button);

		return ['form' => $form, 'num' => $num];
	}

	public function getPropertyPage(we_base_jsCmd $jsCmd){
		return we_html_multiIconBox::getHTML('PropertyPage', [
				['icon' => we_html_multiIconBox::PROP_PATH, 'headline' => g_l('weClass', '[path]'), 'html' => $this->formPath(), 'space' => we_html_multiIconBox::SPACE_ICON],
				['icon' => we_html_multiIconBox::PROP_DOC, 'headline' => g_l('weClass', '[document]'), 'html' => $this->formIsSearchable() . $this->formIsProtected(), 'space' => we_html_multiIconBox::SPACE_ICON],
				['icon' => we_html_multiIconBox::PROP_CATEGORIES, 'headline' => g_l('weClass', '[category]'), 'html' => $this->formCategory($jsCmd), 'space' => we_html_multiIconBox::SPACE_ICON],
				['icon' => we_html_multiIconBox::PROP_USER, 'headline' => g_l('weClass', '[owners]'), 'html' => $this->formCreatorOwners($jsCmd), 'space' => we_html_multiIconBox::SPACE_ICON]]
		);
	}

	public static function showBinaryDoc(){
		we_html_tools::protect();
		$cmd2 = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', '', 2);
		switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1)){
			case we_base_ContentTypes::IMAGE:
				$we_doc = new we_imageDocument();
				$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$cmd2]);
				$contenttype = $we_doc->getElement('type');
				break;
			case we_base_ContentTypes::VIDEO:
				$we_doc = new we_document_video();
				$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$cmd2]);
				$contenttype = 'video/' . str_replace('.', '', $we_doc->Extension);
				break;
			case we_base_ContentTypes::AUDIO:
				$we_doc = new we_document_audio();
				$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$cmd2]);
				$contenttype = 'audio/' . str_replace('.', '', $we_doc->Extension);
				break;
			case we_base_ContentTypes::FLASH:
				$we_doc = new we_flashDocument();
				$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$cmd2]);
				$contenttype = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1);
				break;
			case we_base_ContentTypes::APPLICATION:
				$we_doc = new we_otherDocument();
				$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$cmd2]);
				switch($we_doc->Extension){
					case ".zip":
						$contenttype = "application/zip";
						break;
					case ".doc":
						$contenttype = "application/msword";
						break;
					case ".xls":
						$contenttype = "application/vnd.ms-excel";
						break;
					case ".ppt":
						$contenttype = "application/vnd.ms-powerpoint";
						break;
					case ".pdf":
						$contenttype = "application/pdf";
						break;
					case ".sit":
						$contenttype = "application/x-stuffit";
						break;
					case ".hqx":
						$contenttype = "application/mac-binhex40";
						break;
					default:
						$contenttype = "application/octet-stream";
				}
				break;
			default:
				die('unsupported request');
		}
		header("Content-disposition: filename=" . $we_doc->Text);
		header("Content-Type: $contenttype");
		header("Pragma: no-cache");
		header("Expires: 0");

		$dataPath = $we_doc->getElement('data');
		if(($tid = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 3))){ // create thumbnail
			if(we_base_imageEdit::gd_version()){
				$thumbObj = new we_thumbnail();
				$thumbObj->initByThumbID($tid, $we_doc->ID, $we_doc->Filename, $we_doc->Path, $we_doc->Extension, $we_doc->getElement('origwidth', 'bdid'), $we_doc->getElement('origheight', 'bdid'), $we_doc->getDocument());
				$thumbObj->getThumb($out);
				unset($thumbObj);
				echo $out;
				exit();
			}
		}
		session_write_close();
		readfile($dataPath);
		exit();
	}

}
