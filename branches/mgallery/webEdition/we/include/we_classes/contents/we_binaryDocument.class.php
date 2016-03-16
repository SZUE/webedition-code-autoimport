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
	var $metaData = array();

	/** Constructor
	 * @return we_binaryDocument
	 * @desc Constructor for we_binaryDocument
	 */
	function __construct(){
		parent::__construct();
		array_push($this->persistent_slots, 'html', 'DocChanged', 'IsProtected');
		if(isWE()){
			array_push($this->EditPageNrs, we_base_constants::WE_EDITPAGE_PROPERTIES, we_base_constants::WE_EDITPAGE_INFO, we_base_constants::WE_EDITPAGE_CONTENT, we_base_constants::WE_EDITPAGE_VERSIONS);
			if(defined('CUSTOMER_TABLE') && (permissionhandler::hasPerm('CAN_EDIT_CUSTOMERFILTER') || permissionhandler::hasPerm('CAN_CHANGE_DOCS_CUSTOMER'))){
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
				return 'we_editors/we_editor_properties.inc.php';
			case we_base_constants::WE_EDITPAGE_IMAGEEDIT:
				return 'we_editors/we_image_imageedit.inc.php';
			case we_base_constants::WE_EDITPAGE_INFO:
				return 'we_editors/we_editor_info.inc.php';
			case we_base_constants::WE_EDITPAGE_CONTENT:
				return 'we_editors/we_editor_binaryContent.inc.php';
			case we_base_constants::WE_EDITPAGE_WEBUSER:
				return 'we_editors/editor_weDocumentCustomerFilter.inc.php';
			case we_base_constants::WE_EDITPAGE_VERSIONS:
				return 'we_editors/we_editor_versions.inc.php';
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
			echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('metadata', '[file_size_0]'), we_message_reporting::WE_MESSAGE_ERROR));
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
		if(!($file && file_exists($file))){
			return false;
		}
		if($file != $this->getSitePath()){
			if(!we_base_file::copyFile($file, $this->getSitePath())){
				return false;
			}
		}
		if(!we_base_file::makeHardLink($file, $this->getRealPath())){
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
		$set = array(
			'ID' => intval($this->ID),
			'DID' => intval($this->ID),
			'Text' => $text,
			'WorkspaceID' => intval($this->ParentID),
			'Category' => $this->Category,
			'Doctype' => '',
			'Title' => $this->getElement('Title'),
			'Description' => $this->getElement('Description'),
			'Path' => $this->Path);
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
		$_reader = $this->getMetaDataReader();
		if($_reader){
			$this->metaData = $_reader->getMetaData();
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
		$_defined_fields = we_metadata_metaData::getDefinedMetaDataFields();

		// show an alert if there are none
		if(empty($_defined_fields)){
			return '';
		}

		// second we build all input fields for them and take
		// the elements of this imageDocument as values:
		$_fieldcount = count($_defined_fields);
		$_content = new we_html_table(array("class" => 'default', "style" => "margin-top:4px;"), $_fieldcount, 5);
		$_mdcontent = '';
		for($i = 0; $i < $_fieldcount; $i++){
			$_tagName = $_defined_fields[$i]["tag"];
			if($_tagName != 'Title' && $_tagName != 'Description' && $_tagName != 'Keywords'){
				$_type = $_defined_fields[$i]['type'];
				//$_mode = $_defined_fields[$i]['mode'];
				//$_csv = boolval($_defined_fields[$i]['csv']);
				//$_closed = boolval($_defined_fields[$i]['closed']);

				switch($_type){
					case 'textarea':
						$_inp = $this->formTextArea('txt', $_tagName, $_tagName, 10, 30, array('onchange' => 'WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);', 'style' => 'width:508px;height:150px;border: #AAAAAA solid 1px'));
						break;
					case 'wysiwyg':
						$_inp = $this->formTextArea('txt', $_tagName, $_tagName, 10, 30, array('onchange' => 'WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);', 'style' => 'width:508px;height:150px;border: #AAAAAA solid 1px'));
						break;
					case 'date':
						$_inp = we_html_tools::htmlFormElementTable(we_html_tools::getDateInput('we_' . $this->Name . '_date[' . $_tagName . ']', abs($this->getElement($_tagName)), true), $_tagName);
						break;
					case 'textfield':
					default:
						$_inp = $this->formMetaField($_tagName);
				}

				$_content->setCol($i, 0, array("colspan" => 5, 'style' => 'padding-bottom:5px;'), $_inp);
			}
		}

		$_mdcontent.=$_content->getHtml();

		// Return HTML
		return $_mdcontent;
	}

	/**
	 * Returns HTML code for Upload Button and infotext
	 */
	function formUpload(){
		$fs = $GLOBALS['we_doc']->getFilesize();
		$fs = g_l('metadata', '[filesize]') . ": " . round(($fs / 1024), 2) . "&nbsp;KB";
		$_metaData = $this->getMetaData();
		$_mdtypes = array();

		if($_metaData){
			if(!empty($_metaData["exif"])){
				$_mdtypes[] = "Exif";
			}
			if(!empty($_metaData["iptc"])){
				$_mdtypes[] = "IPTC";
			}
			if(!empty($_metaData["pdf"])){
				$_mdtypes[] = "PDF";
			}
		}

		$ft = g_l('metadata', '[filetype]') . ': ' . ($this->Extension ? substr($this->Extension, 1) : '');

		$md = ($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE ?
				'' :
				g_l('metadata', '[supported_types]') . ': ' .
				'<a href="javascript:parent.frames.editHeader.weTabs.setActiveTab(\'tab_2\');we_cmd(\'switch_edit_page\',2,\'' . $GLOBALS['we_transaction'] . '\');">' .
				(count($_mdtypes) > 0 ? implode(', ', $_mdtypes) : g_l('metadata', '[none]')) .
				'</a>');

		$fileUpload = new we_fileupload_ui_wedoc($this->ContentType);

		return $fileUpload->getHTML($fs, $ft, $md, $this->getThumbnail(100, 100), $this->getThumbnail());
	}

	protected function getThumbnail($width = 150, $height = 100){
		return '';
	}

	function savebinarydata(){
		$_data = $this->getElement('data');
		if($_data && (strlen($_data) > 512 || !@file_exists($_data))){ //assume data>512 = binary data
			$_path = we_base_file::saveTemp($_data);
			$this->setElement('data', $_path);
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

	function formReferences(){
		$search = new we_search_search();
		$search->searchMediaLinks(0, true, $this->ID);
		$ml = $search->getUsedMediaLinks();
		$accessibles = isset($ml['accessible']['mediaID_' . $this->ID]) ? $ml['accessible']['mediaID_' . $this->ID] : array(); //IMPORTANT: we hava a nested structure: make optgroups
		$notaccessibles = isset($ml['notaccessible']['mediaID_' . $this->ID]) ? $ml['notaccessible']['mediaID_' . $this->ID] : array();
		$groups = isset($ml['groups']['mediaID_' . $this->ID]) ? $ml['groups']['mediaID_' . $this->ID] : array();

		if(empty($groups)){
			return g_l('weClass', '[notReferenced]');
		}

		$js = "";
		$values = array();
		$c = 0;
		$limit=10;
		foreach($groups as $group){
			$cna = count($notaccessibles[$group]);
			$values[(count($accessibles[$group]) + $cna) . ' '. $group . ($cna ? ' (' . $cna . ' ' . g_l('weClass', '[medialinks_unaccessible]') . ')' : '')] = we_html_tools::OPTGROUP;
			$cc = 0;
			foreach($accessibles[$group] as $v){
				if($cc++ >= $limit){
					$values[-1] = '+ ' . (count($accessibles[$group]) - $limit) . ' ' . g_l('weClass', '[medialinks_more]');
					break;
				}
				$values[++$c] = $v['path'];
				$js .= "id_" . $c . ": {type: '" . $v['type'] . "', id: " . $v['id'] . ", table: '" . $v['table'] . "', ct: '" . $v['ct'] . "', mod: '" . $v['mod'] . "', referencedIn: '" . $v['referencedIn'] . "', isTempPossible: " . ($v['isTempPossible'] ? 1 : 0) . ", isModified: " . ($v['isModified'] ? 1 : 0) . "},";
			}
		}
		$button = we_html_button::create_button(we_html_button::EDIT, "javascript:top.we_openMediaReference(document.getElementById('MediaReferences').value);");

		return we_html_element::jsElement("top.we_mediaReferences = {" . $js . "};") . we_html_tools::htmlFormElementTable($this->htmlSelect('MediaReferences', $values, 1, '', false, array(), 'value', 388), '', 'left', 'defaultfont', '', $button);
	}

	public function getPropertyPage(){
		return we_html_multiIconBox::getHTML('PropertyPage', array(
			array('icon' => 'path.gif', 'headline' => g_l('weClass', '[path]'), 'html' => $this->formPath(), 'space' => 140),
			array('icon' => 'doc.gif', 'headline' => g_l('weClass', '[document]'), 'html' => $this->formIsSearchable() . $this->formIsProtected(), 'space' => 140),
			//array('icon' => 'meta.gif', 'headline' => g_l('weClass', '[metainfo]'), 'html' => $this->formMetaInfos(), 'space' => 140),
			array('icon' => 'cat.gif', 'headline' => g_l('weClass', '[category]'), 'html' => $this->formCategory(), 'space' => 140),
			array('icon' => 'user.gif', 'headline' => g_l('weClass', '[owners]'), 'html' => $this->formCreatorOwners(), 'space' => 140))
		);
	}

}
