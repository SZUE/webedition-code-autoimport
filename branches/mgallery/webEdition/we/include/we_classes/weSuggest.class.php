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

/**
 * Klasse f�r Autocomleter
 *
 * $yuiSuggest =& weSuggest::getInstance();																								// Die Kalsse instanzieren.
 * echo $yuiSuggest->getYuiFiles																										// Die notwendigen YUI-JS-Dateien werden an einer passenden Stelle eingebunden
 * echo $yuiSuggest->createAutocompleter(																								// GUI-Element mit Input-Feld und Auswahl-Button
 * 			"Doc", 																														// AC-Id
 * 			we_button::create_button(we_html_button::SELECT, "javascript:select_seem_start()", true, 100, 22, "", "", false, false),					// Auswahl-Button
 * 			we_html_tools::htmlTextInput("seem_start_document_name", 11, $_document_path, "", " id='yuiAcInputDoc'", "text", 190, 0, "", false),		// Input-Feld
 * 			'yuiAcInputDoc',																											// Input-Feld-Id. Die Id besteht aus 'yuiAcInput' und AC-Id
 * 			rray("name" => "seem_start_document", "value" => $_document_id, "id"=>"yuiAcResultDoc")),		// Result-Field (hidden) für die Document-, Folder-, Object-,...ID
 * 			'yuiAcResultDoc', 																											// Result-Feld-Id. Die Id besteht aus 'yuiAcResult' und AC-Id
 * 			'',																															// Label: steht über dem Inputfeld
 * 			FILE_TABLE, 																												// Name der Tabele in für die Query
 * 			"folder,text/webedition,image/*,text/js,text/css,text/html,application/*,video/quicktime", 													// ContentTypen für die Query: sie entsprechende Tabele
 * 			"docSelector", 																												// docSelector | dirSelector : ob nach folder oder doc gesucht wird
 * 			20, 																														// Anzahl der Vorschläge
 * 			0, 																															// Verzögerung für das auslösen des AutoCompletion
 * 			true, 																														// Soll eine Ergebnisüberprüfung stattfinden
 * 			"190", 																														// Container-Breite
 * 			"true",																														// Feld darf leer bleiben
 * 			10																															// Abstand zwischen Input-Feld und Button
 * 		);
 * echo $yuiSuggest->getYuiJs																											// Generieter CSS- und JS-Code
 */
class weSuggest{
	const DocSelector = 'docSelector';
	const DirSelector = 'dirSelector';

	private $noautoinit = false;
	private $noAutoInits = array();
	var $inputfields = array();
	var $containerwidth = array();
	var $tables = array();
	var $rootDirs = array();
	var $contentTypes = array();
	var $weMaxResults = array();
	var $queryDelay = array();
	var $layer = array();
	var $setOnSelectFields = array();
	var $checkFieldsValues = array();
	var $selectors = array();
	var $ct = array();
	var $inputMayBeEmpty = array();
	var $_doOnItemSelect = array();
	var $_doOnTextfieldBlur = array();
	var $preCheck = "";
	/*	 * ************************************* */
	var $acId = '';
	var $checkFieldValue = true;
	var $containerWidth = '';
	var $containerWidthForAll = 0;
	var $contentType = "folder";
	var $inputAttribs = 0;
	var $inputDisabled = 0;
	var $inputId = '';
	var $inputName = '';
	var $inputValue = '';
	var $label = '';
	var $maxResults = 20;
	var $mayBeEmpty = 1;
	var $resultName = '';
	var $resultValue = '';
	var $resultId = '';
	var $rootDir = '';
	var $selectButton = '';
	var $selector = "Dir"; //FIXME: self::DirSelector???
	var $trashButton = '';
	var $openButton = '';
	var $additionalButton = '';
	var $createButton = '';
	var $table = FILE_TABLE;
	var $width = 280;
	var $doOnItemSelect = '';
	var $doOnTextfieldBlur = '';
	private static $giveStatic = true;

	static function &getInstance(){
		static $inst = null;
		if(!is_object($inst)){
			$inst = new self();
		}
		if(self::$giveStatic){
			return $inst;
		}
		$void = new self();
		return $void;
	}

	static function getYuiFiles(){ //FIXME: make sure all pages include this in head-element
		return
			we_html_element::cssLink(CSS_DIR . 'weSuggest.css') .
			YAHOO_FILES .
			we_html_element::jsScript(LIB_DIR . 'additional/yui/dom-min.js') .
			we_html_element::jsScript(LIB_DIR . 'additional/yui/datasource-min.js') .
			we_html_element::jsScript(LIB_DIR . 'additional/yui/animation-min.js') .
			we_html_element::jsScript(LIB_DIR . 'additional/yui/autocomplete-min.js') .
			we_html_element::jsScript(JS_DIR . 'utils/we_cmd_encode.js') .
			we_html_element::jsScript(JS_DIR . 'weSuggest.js');
	}

	//use this, if you need to add fields dynamically
	public function getyuiAcFields(){
		$fildsObj = array();

		// loop fields
		for($i = 0; $i < count($this->inputfields); $i++){
			if(!$this->noAutoInits[$i]){
				//$weErrorMarkId = str_replace("Input", "ErrorMark", $this->inputfields[$i]);
				$weWorkspacePathArray = id_to_path(get_ws($this->tables[$i]), $this->tables[$i], null, false, true);
				//$weFieldWS[] = '[' . ($weWorkspacePathArray ? '"' . implode('","', $weWorkspacePathArray) . '"' : '') . ']';

				$current = "{
				'id' : '" . $this->inputfields[$i] . "',
				'container': '" . $this->containerfields[$i] . "',
				'old': document.getElementById('" . $this->inputfields[$i] . "').value,
				'selector': '" . $this->selectors[$i] . "',
				'sel': '',
				'newval': null,
				'run': false,
				'found': 0,
				'cType': '',
				'valid': true,
				'countMark': 0,
				'changed': false,
				'maxResults':" . $this->weMaxResults[$i] . ",
				'table': '" . $this->tables[$i] . "',
				'rootDir': '" . $this->rootDirs[$i] . "',
				'cTypes': '" . $this->contentTypes[$i] . "',
				'workspace': [" . ($weWorkspacePathArray ? '"' . implode('","', $weWorkspacePathArray) . '"' : '') . "],
				'mayBeEmpty': " . ($this->inputMayBeEmpty[$i] ? "true" : "false") . ",
				'checkField': " . intval(!empty($this->checkFieldsValues[$i]));

				if(isset($this->setOnSelectFields[$i]) && is_array($this->setOnSelectFields[$i])){
					if($this->setOnSelectFields[$i]){
						$current .=",
	'fields_id': ['" . implode('\',\'', $this->setOnSelectFields[$i]) . '\']' . ",
	'fields_val': [document.getElementById('" . implode("').value,document.getElementById('", $this->setOnSelectFields[$i]) . "').value]";
					}
				}
				if($this->_doOnItemSelect[$i]){
					$current .=',itemSelect:function(param1,param2,param,params){' . $this->_doOnItemSelect[$i] . '}';
				}
				if($this->_doOnTextfieldBlur[$i]){
					$current .=',blur:function(){' . $this->_doOnTextfieldBlur[$i] . '}';
				}
				if(!empty($this->checkFieldsValues[$i])){
					$additionalFields = "";
					if(isset($this->setOnSelectFields[$i]) && is_array($this->setOnSelectFields[$i])){
						for($j = 0; $j < count($this->setOnSelectFields[$i]); $j++){
							$additionalFields .= ($j > 0 ? "," : "") . str_replace('-', '_', $this->setOnSelectFields[$i][$j]);
						}
						$current .=",
							'checkValues':'" . $additionalFields . "'";
					}
				}
				// EOF loop fields

				$current .= "}";
				$fildsObj[$this->inputfields[$i]] = $current;
			}
		}
		return $fildsObj;
	}

	/**
	 * This function generates the individual js code for the autocomletion
	 *
	 * @return String
	 */
	function getYuiJs(){
		/**
		 * @todo 	1. value
		 * 			2. table
		 * 			3. contenttype
		 * 			4. ?
		 * 			5. id
		 */
		$weSelfContentType = (isset($GLOBALS['we_doc']) && isset($GLOBALS['we_doc']->ContentType)) ? $GLOBALS['we_doc']->ContentType : '';
		$weSelfID = (isset($GLOBALS['we_doc']) && isset($GLOBALS['we_doc']->ID)) ? $GLOBALS['we_doc']->ID : '';

		if(empty($this->inputfields)){
			return;
		}

		// WORKSPACES
		//$weFieldWS = array();
		// AC-FIEDS
		$fildsObj = $this->getyuiAcFields();
		// AC-FIEDS BY ID
		$fildsById = array();
		foreach(array_keys($fildsObj) as $i => $key){
			$fildsById[] = '"' . $key . '":' . $i;
		}

		return we_html_element::jsElement('
YAHOO.autocoml.width= ' . intval($this->width) . ';
YAHOO.autocoml.ajaxURL = WE().consts.dirs.WEBEDITION_DIR+"rpc/rpc.php";
YAHOO.autocoml.selfType="' . $weSelfContentType . '";
YAHOO.autocoml.selfID="' . $weSelfID . '";
YAHOO.autocoml.yuiAcFieldsById = {' . implode(',', $fildsById) . '};
YAHOO.autocoml.yuiAcFields = [' . implode(',', $fildsObj) . '];
YAHOO.util.Event.addListener(this, "load", YAHOO.autocoml.init);' .
				$this->preCheck);
	}

	function getHTML($reset = true){
		$inputId = $this->inputId ? : 'yuiAcInput' . $this->acId;
		$resultId = $this->resultId ? : 'yuiAcResult' . $this->acId;
		$containerWidth = $this->containerWidth ? : $this->width;

		$this->setAutocompleteField($inputId, "yuiAcContainer" . $this->acId, $this->table, $this->contentType, $this->selector, $this->maxResults, 0, "yuiAcLayer" . $this->acId, array($resultId), $this->checkFieldValue, (we_base_browserDetect::isIE() ? $containerWidth : ($containerWidth - 8)), $this->mayBeEmpty, $this->rootDir, $this->noautoinit);
		$inputField = $this->_htmlTextInput($this->inputName, $this->inputValue, "", 'id="' . $inputId . '" ' . $this->inputAttribs, "text", $this->width, 0, "", $this->inputDisabled);
		$resultField = we_html_tools::hidden($this->resultName, $this->resultValue, array('id' => $resultId));
		$autoSuggest = '<div id="yuiAcLayer' . $this->acId . '" class="yuiAcLayer">' . $inputField . '<div id="yuiAcContainer' . $this->acId . '"></div></div>';


		$html = we_html_tools::htmlFormElementTable(
				array(
				"text" => $resultField . $autoSuggest), $this->label, 'left', 'defaultfont', (
				$this->selectButton ? : ''
				), (
				$this->additionalButton ? : ''
				), (
				$this->trashButton ? : ''
				), (
				$this->openButton ? : ''
				), (
				$this->createButton ? : '')
		);

		if($reset){
			$this->contentType = we_base_ContentTypes::FOLDER;
			$this->mayBeEmpty = 1;
			$this->label = '';
			$this->containerWidth = '';
			$this->containerWidthForAll = 0;
			$this->selector = self::DirSelector; //FIXME:self::Dirselector??
			$this->table = FILE_TABLE;
			$this->width = 280;
			$this->doOnItemSelect = '';
			$this->doOnTextfieldBlur = '';
		}
		$this->acId = '';
		$this->maxResults = 20;
		$this->resultName = '';
		$this->resultValue = '';
		$this->resultId = '';
		$this->selectButton = '';
		$this->trashButton = '';
		$this->openButton = '';
		$this->createButton = '';
		return $html;
	}

	function getInputId(){
		return $this->inputId;
	}

	private function _htmlTextInput($name, $value = "", $maxlength = "", $attribs = "", $type = "text", $width = 0, $height = 0, $markHot = "", $disabled = false){
		$style = ($width || $height) ? (' style="' . ($width ? ('width: ' . $width . ((strpos($width, "px") || strpos($width, "%")) ? "" : "px") . ';
						') : '') . ($height ? ('height: ' . $height . ((strpos($height, "px") || strpos($height, "%")) ? "" : "px") . ';
						') : '') . '"') : '';
		return '<input type="' . trim($type) . '" name="' . trim($name) . '" value="' . oldHtmlspecialchars($value) . '" ' . ($maxlength ? (' maxlength="' . abs($maxlength) . '"') : '') . $attribs . $style . ' />';
	}

	//setter

	function setAcId($val, $rootDir = ""){
		$this->acId = str_replace('-', '_', $val);
		$this->rootDir = $rootDir;
	}

	function setNoAutoInit($noautoinit = false){
		$this->noautoinit = $noautoinit;
	}

	/**
	 * Setts the width of the suggest container. Default is input field width
	 *
	 * @param Int $containerWidth
	 * @param Boolean $containerWidthforAll
	 */
	function setContainerWidth($containerWidth){
		$this->containerWidth = $containerWidth;
	}

	/**
	 * Set the content tye to filter result
	 *
	 * @param unknown_type $val
	 */
	function setContentType($val){
		$this->contentType = $val;
	}

	function setDoOnItemSelect($val){
		$this->doOnItemSelect = $val;
	}

	function setDoOnTextfieldBlur($val){
		$this->doOnTextfieldBlur = $val;
	}

	/**
	 * Set id and value for the input field
	 *
	 * @param String $name
	 * @param String $value
	 * @param Array $attribs
	 * @param Boolean $disabled
	 */
	function setInput($name, $value = "", $attribs = "", $disabled = false, $markHot = ""){
		$this->inputId = '';
		$this->inputName = $name;
		$this->inputValue = $value;
		$this->inputAttribs = "";
		if(isset($attribs) && is_array($attribs)){
			foreach($attribs as $key => $val){
				$key = strtolower($key);
				switch($key){
					case "id":
						$this->inputId = $key;
						break;
					case "onchange":
						$_onchange = 1;
						$this->inputAttribs .= $key . '="' . ($markHot ? 'WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);
						hot = 1}' : '') . $val . '" ';
						break;
					case "class":
						$_class = 1;
						$val.=' wetextinput';
					case "onblur":
					case "onfocus":
					default:
						$this->inputAttribs .= $key . '="' . $val . '" ';
				}
			}
			if(!isset($_class)){
				$this->inputAttribs .= 'class="wetextinput" ';
			}
			if(!isset($_onchange)){
				$this->inputAttribs .= ' onchange="' . ($markHot ? 'WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);
						hot = 1};
						' : '') . '" ';
			}
		} else {
			$this->inputAttribs = 'class="wetextinput" onchange="' . ($markHot ? 'WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);
						hot = 1;
						}' : '') . '" ';
		}
		if(!$this->inputId){
			$this->setInputId();
		}
		$this->inputDisabled = $disabled;
	}

	function setInputId($val = ''){
		$this->inputId = ($val ? : "yuiAcInput" . $this->acId);
	}

	function setInputName($val){
		$this->inputName = $val;
	}

	function setInputValue($val){
		$this->inputValue = $val;
	}

	function setMaxResults($val){
		$this->maxResults = $val;
	}

	function setCheckFieldValue($val){
		$this->checkFieldValue = $val;
	}

	/**
	 * Flag if the autocompleter my be empty
	 *
	 * @param unknown_type $val
	 */
	function setMayBeEmpty($val){
		$this->mayBeEmpty = $val;
	}

	function setLabel($val){
		$this->label = $val;
	}

	/**
	 * Set name, value and id for the result field
	 *
	 * @param unknown_type $resultID
	 * @param unknown_type $resultValue
	 */
	function setResult($resultName, $resultValue = "", $resultID = ""){
		$this->resultName = $resultName;
		$this->resultId = $resultID;
		$this->resultValue = $resultValue;
	}

	function setResultId($val){
		$this->resultId = $val;
	}

	function setResultName($val){
		$this->resultValue = $val;
	}

	function setResultValue($val){
		$this->resultValue = $val;
	}

	function setSelectButton($val){
		$this->selectButton = $val;
	}

	/**
	 * Set the selector
	 *
	 * @param String $val
	 */
	function setSelector($val){
		$this->selector = $val;
	}

	/**
	 * Set the table for query result
	 *
	 * @param unknown_type $val
	 */
	function setTable($val){
		$this->table = $val;
	}

	function setTrashButton($val){
		$this->trashButton = $val;
	}

	function setOpenButton($val){
		$this->openButton = $val;
	}

	function setAdditionalButton($val){
		$this->additionalButton = $val;
	}

	function setCreateButton($val){
		$this->createButton = $val;
	}

	function setWidth($var){
		$this->width = $var;
	}

	/**
	 * This function sets the values for the autocompletion fields
	 *
	 * @param unknown_type $inputFieldId
	 * @param unknown_type $containerFieldId
	 * @param unknown_type $table
	 * @param unknown_type $contentType
	 * @param unknown_type $maxResults
	 * @param unknown_type $queryDelay
	 * @param unknown_type $layerId
	 * @param unknown_type $setOnSelectFields
	 * @param unknown_type $checkFieldsValue
	 * @param unknown_type $containerwidth
	 */
	function setAutocompleteField($inputFieldId, $containerFieldId, $table, $contentType = '', $selector = '', $maxResults = 10, $queryDelay = 0, $layerId = null, $setOnSelectFields = null, $checkFieldsValue = true, $containerwidth = "100%", $inputMayBeEmpty = 'true', $rootDir = '', $noautoinit = false){
		$this->inputfields[] = $inputFieldId;
		$this->noAutoInits[] = $noautoinit;
		$this->containerfields[] = $containerFieldId;
		$this->tables[] = $table;
		$this->rootDirs[] = $rootDir;
		$this->contentTypes[] = $contentType;
		$this->selectors[] = $selector;
		$this->weMaxResults[] = $maxResults;
		$this->queryDelay[] = $queryDelay;
		$layerId ? array_push($this->layer, $layerId) : "";
		$this->setOnSelectFields[] = $setOnSelectFields;
		$this->checkFieldsValues[] = $checkFieldsValue;
		$this->containerwidth[] = $containerwidth;
		$this->inputMayBeEmpty[] = $inputMayBeEmpty;
		switch($contentType){
			case self::DirSelector:
				array($this->ct, "folder");
				break;
			case self::DocSelector:
				array($this->ct, "doc");
				break;
		}
		$this->_doOnItemSelect[] = $this->doOnItemSelect;
		$this->doOnItemSelect = '';
		$this->_doOnTextfieldBlur[] = $this->doOnTextfieldBlur;
		$this->doOnTextfieldBlur = '';
	}

	/**
	 * needed to suppress giving the same instance
	 * If sth. is included & the main instance should not be modified, set this to false
	 * @param bool $staticInstance false, if the results should be omitted; don't forget to reset
	 */
	public static function setStaticInstance($staticInstance){
		self::$giveStatic = $staticInstance;
	}

}
