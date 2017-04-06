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
 * $weSuggest =& weSuggest::getInstance();																								// Die Kalsse instanzieren.
 * echo $weSuggest->createAutocompleter(																								// GUI-Element mit Input-Feld und Auswahl-Button
 * 			"Doc", 																														// AC-Id
 * 			we_button::create_button(we_html_button::SELECT, "javascript:select_seem_start()", , "", "", false, false),					// Auswahl-Button
 * 			we_html_tools::htmlTextInput("seem_start_document_name", 11, $document_path, "", " id='yuiAcInputDoc'", "text", 190, 0, "", false),		// Input-Feld
 * 			'yuiAcInputDoc',																											// Input-Feld-Id. Die Id besteht aus 'yuiAcInput' und AC-Id
 * 			rray("name" => "seem_start_document", "value" => $document_id, "id"=>"yuiAcResultDoc")),		// Result-Field (hidden) für die Document-, Folder-, Object-,...ID
 * 			'yuiAcResultDoc', 																											// Result-Feld-Id. Die Id besteht aus 'yuiAcResult' und AC-Id
 * 			'',																															// Label: steht über dem Inputfeld
 * 			FILE_TABLE, 																												// Name der Tabele in für die Query
 * 			"folder,text/webedition,image/*,text/js,text/css,text/html,application/*", 													// ContentTypen für die Query: sie entsprechende Tabele
 * 			"docSelector", 																												// docSelector | dirSelector : ob nach folder oder doc gesucht wird
 * 			20, 																														// Anzahl der Vorschläge
 * 			0, 																															// Verzögerung für das auslösen des AutoCompletion
 * 			true, 																														// Soll eine Ergebnisüberprüfung stattfinden
 * 			"190", 																														// Container-Breite
 * 			"true",																														// Feld darf leer bleiben
 * 			10																															// Abstand zwischen Input-Feld und Button
 * 		);
 */
class we_gui_suggest{
	const DocSelector = 'docSelector';
	const DirSelector = 'dirSelector';
	const BTN_EDIT = true;
	const USE_DRAG_AND_DROP = true;
	const BTN_SELECT = 0;
	const BTN_ADDITIONAL = 1;
	const BTN_TRASH = 2;
	const BTN_OPEN = 3;
	const BTN_CREATE = 4;

	private $noautoinit = false;
	private $acId = '';
	private $checkFieldValue = true;
	private $contentType = we_base_ContentTypes::FOLDER;
	private $inputAttribs = [];
	private $inputId = '';
	private $inputName = '';
	private $inputValue = '';
	private $label = '';
	private $maxResults = 20;
	private $required = false;
	private $resultName = '';
	private $resultValue = '';
	private $resultId = '';
	private $rootDir = '';
	private $selector = self::DirSelector;
	private $buttons = [
		self::BTN_SELECT => '',
		self::BTN_ADDITIONAL => '',
		self::BTN_TRASH => '',
		self::BTN_OPEN => '',
		self::BTN_CREATE => '',
	];
	private $table = FILE_TABLE;
	private $width = 280;
	private $jsCommandOnItemSelect = '';
	private $isDropFromTree = false;
	private $isDropFromExt = false;
	private $doOnDropFromTree = '';
	private $doOnDropFromExt = '';
	private static $giveStatic = true;

	public static function &getInstance(){
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

	public function getHTML($reset = true){
		$inputId = $this->inputId ?: 'yuiAcInput' . $this->acId;
		$resultId = $this->resultId ?: 'yuiAcResult' . $this->acId;

		if($this->buttons[self::BTN_OPEN] === self::BTN_EDIT){
			$this->buttons[self::BTN_OPEN] = we_html_button::create_button(we_html_button::EDIT, "javascript:WE().layout.weSuggest.openSelectionToEdit(window,'" . $inputId . "')");
		}

		if(!$this->noautoinit){
			$this->inputAttribs['class'] .= ' weSuggest';
		}
		$this->inputAttribs['data-table'] = $this->table;
		$this->inputAttribs['data-contenttype'] = $this->contentType;
		$this->inputAttribs['data-basedir'] = ($this->rootDir ?: '/');
		$this->inputAttribs['data-result'] = $resultId;
		$this->inputAttribs['data-max'] = $this->maxResults;
		$this->inputAttribs['data-selector'] = $this->selector;
		$this->inputAttribs['data-currentDocumentType'] = (isset($GLOBALS['we_doc']) && isset($GLOBALS['we_doc']->ContentType)) ? $GLOBALS['we_doc']->ContentType : '';
		$this->inputAttribs['data-currentDocumentID'] = (isset($GLOBALS['we_doc']) && isset($GLOBALS['we_doc']->ID)) ? $GLOBALS['we_doc']->ID : '';
		$this->inputAttribs['data-onSelect'] = $this->jsCommandOnItemSelect;

		if($this->required){
			$this->inputAttribs['required'] = 'required';
		}

		$html = we_html_tools::htmlFormElementTable([
				'text' => $this->htmlTextInput($this->inputName, $this->inputValue, $this->inputAttribs, 'text', $this->width) .
				we_html_element::htmlHidden($this->resultName, $this->resultValue, $resultId, [
					//FIXME: we need to know this
					'data-contenttype' => '',
				])
				], $this->label, 'left', 'defaultfont', (
				$this->buttons[self::BTN_SELECT]
				), (
				$this->buttons[self::BTN_ADDITIONAL] ?: ''
				), (
				$this->buttons[self::BTN_TRASH] ?: ''
				), (
				$this->buttons[self::BTN_OPEN] ?: ''
				), (
				$this->buttons[self::BTN_CREATE] ?: '')
		);

		if(self::USE_DRAG_AND_DROP && ($this->isDropFromTree || $this->isDropFromExt)){
			$this->isDropFromExt = $this->table === FILE_TABLE ? $this->isDropFromExt : false;

			$dropzoneContent = g_l('global', '[dragndrop][selection]') . '<br>' . g_l('global', '[dragndrop][dnd_text][' . (($this->isDropFromTree ? 1 : 0) + ($this->isDropFromExt ? 2 : 0)) . ']');
			$dropzoneStyle = 'width:auto;padding-top:14px;height:60px;';

			$img = '';
			$eventAttribs = ['ondragover' => 'handleDragOver(event, \'' . $this->acId . '\');', 'ondragleave' => 'handleDragLeave(event, \'' . $this->acId . '\');'];

			if(true && $this->contentType === we_base_ContentTypes::IMAGE){ // FIXME: add code for icons so we can have preview for all cts
				if($this->resultValue){
					$DE_WE = new DB_WE;
					$file = $DE_WE->getHash('SELECT Path,Extension,ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . $this->resultValue);

					if($file['ContentType'] === we_base_ContentTypes::IMAGE){
						$url = WEBEDITION_DIR . 'thumbnail.php?id=' . $this->resultValue . "&size[width]=100&size[heihjt]=100&path=" . urlencode($file['Path']) . "&extension=" . $file['Extension'];
						$img = we_html_element::htmlImg(['src' => $url, 'style' => 'vertical-align:middle;']);
					}
				}

				$imgDiv = we_html_element::htmlDiv(array_merge($eventAttribs, ['style' => 'float:left;height:100%;']), we_html_element::htmlSpan(['style' => 'display:inline-block;height: 100%;vertical-align: middle;']) .
						we_html_element::htmlSpan(['id' => 'preview_' . $this->acId], $img)
				);
				$dropzoneContent = $imgDiv . we_html_element::htmlDiv(array_merge($eventAttribs, ['style' => 'display:inline-block;padding-top:30px;']), $dropzoneContent);
				$dropzoneStyle = 'width:auto;padding:0px 0 0 12px;';
			}
			$dropzone = we_fileupload_ui_base::getExternalDropZone($this->acId, $dropzoneContent, $dropzoneStyle, $this->isDropFromTree, $this->isDropFromExt, 'suggest_writeBack,' . $this->acId, 'suggest_writeBack,' . $this->acId, explode(',', $this->contentType), $this->table);

			$html = we_html_element::htmlDiv([], we_html_element::htmlDiv([], $html) .
					we_html_element::htmlDiv(['style' => 'margin-top:-4px;'], $dropzone)
			);
			$this->isDropFromTree = $this->isDropFromExt = false; //reset default for other instances on the same site
		}

		$html .= we_html_element::htmlHidden('yuiAcContentType' . $this->acId, isset($file['ContentType']) ? $file['ContentType'] : '', 'yuiAcContentType' . $this->acId);

		if($reset){
			$this->contentType = we_base_ContentTypes::FOLDER;
			$this->required = false;
			$this->label = '';
			$this->selector = self::DirSelector;
			$this->table = FILE_TABLE;
			$this->width = 280;
			$this->jsCommandOnItemSelect = '';
		}
		$this->acId = '';
		$this->maxResults = 20;
		$this->resultName = '';
		$this->resultValue = '';
		$this->resultId = '';
		$this->buttons = [
			self::BTN_SELECT => '',
			self::BTN_ADDITIONAL => '',
			self::BTN_TRASH => '',
			self::BTN_OPEN => '',
			self::BTN_CREATE => '',
		];
		return $html;
	}

	function getInputId(){
		return $this->inputId;
	}

	/**
	 * Set id and value for the input field
	 *
	 * @param String $name
	 * @param String $value
	 * @param Array $attribs
	 * @param Boolean $disabled
	 */
	public function setInput($name, $value = '', array $attribs = [], $disabled = false, $markHot = false){
		$this->inputId = '';
		$this->inputName = $name;
		$this->inputValue = $value;
		$class = $onchange = 0;
		$this->inputAttribs = [
			'class' => 'wetextinput',
			'onchange' => ($markHot ? 'hot=true;we_cmd(\'setHot\')' : ''),
		];
		if($disabled){
			$this->inputAttribs['disabled'] = 'disabled';
		}
		foreach($attribs as $key => $val){
			$key = strtolower($key);
			switch($key){
				case 'id':
					$this->inputId = $key;
					$this->inputAttribs[$key] = $val;
					break;
				case 'onchange':
					$onchange = 1;
					$this->inputAttribs[$key] .= $val;
					break;
				case 'class':
					$class = 1;
					$this->inputAttribs[$key] .= ' ' . $val;
//				case 'onfocus':
				default:
					$this->inputAttribs[$key] = $val;
			}
		}

		if(!$this->inputId){
			$this->setInputId();
		}
	}

	private function htmlTextInput($name, $value = '', array $attribs = [], $type = 'text', $width = 0){
		$attribs['type'] = $type;
		$attribs['name'] = trim($name);
		$attribs['value'] = oldHtmlspecialchars($value);
		$attribs['style'] = (empty($attribs['style']) ? '' : $attribs['style']) .
			($width ? ('width: ' . $width . ((strpos($width, 'px') || strpos($width, '%')) ? '' : 'px') . ';') : '');

		return we_html_element::htmlInput($attribs);
	}

	//setter

	public function setAcId($val, $rootDir = ""){
		$this->acId = str_replace('-', '_', $val);
		$this->rootDir = $rootDir;
	}

	public function setNoAutoInit($noautoinit = false){
		$this->noautoinit = $noautoinit;
	}

	/**
	 * Set the content tye to filter result
	 *
	 * @param unknown_type $val
	 */
	public function setContentType($val){
		$this->contentType = is_array($val) ? implode(',', $val) : $val;
	}

	/*if some other item is selected this we-cmd is called*/
	public function setjsCommandOnItemSelect($val){
		$this->jsCommandOnItemSelect = $val;
	}

	public function setDoOnDropFromExt($val = ''){
		$this->doOnDropFromExt = $val;
	}

	public function setDoOnDropFromTree($val = ''){
		$this->doOnDropFromTree = $val;
	}

	public function setIsDropFromExt($val = false){
		$this->isDropFromExt = $val;
	}

	public function setIsDropFromTree($val = false){
		$this->isDropFromTree = $val;
	}

	private function setInputId($val = ''){
		$this->inputId = ($val ?: "yuiAcInput" . $this->acId);
		$this->inputAttribs['id'] = $this->inputId;
	}

	public function setMaxResults($val){
		$this->maxResults = $val;
	}

	public function setCheckFieldValue($val){
		$this->checkFieldValue = $val;
	}

	/**
	 * Flag if the autocompleter my be empty
	 *
	 * @param unknown_type $val
	 */
	public function setRequired($val){
		$this->required = $val;
	}

	public function setLabel($val){
		$this->label = $val;
	}

	/**
	 * Set name, value and id for the result field
	 *
	 * @param unknown_type $resultID
	 * @param unknown_type $resultValue
	 */
	public function setResult($resultName, $resultValue = "", $resultID = ""){
		$this->resultName = $resultName;
		$this->resultId = $resultID;
		$this->resultValue = $resultValue;
	}

	/**
	 * Set the selector
	 *
	 * @param String $val
	 */
	public function setSelector($val){
		$this->selector = $val;
	}

	/**
	 * Set the table for query result
	 *
	 * @param unknown_type $val
	 */
	public function setTable($val){
		$this->table = $val;
	}

	public function setSelectButton($val){
		$this->buttons[self::BTN_SELECT] = $val;
	}

	public function setTrashButton($val){
		$this->buttons[self::BTN_TRASH] = $val;
	}

	public function setOpenButton($val){
		$this->buttons[self::BTN_OPEN] = $val;
	}

	public function setAdditionalButton($val){
		$this->buttons[self::BTN_ADDITIONAL] = $val;
	}

	public function setCreateButton($val){
		$this->buttons[self::BTN_CREATE] = $val;
	}

	public function setWidth($var){
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

	/**
	 * needed to suppress giving the same instance
	 * If sth. is included & the main instance should not be modified, set this to false
	 * @param bool $staticInstance false, if the results should be omitted; don't forget to reset
	 */
	public static function setStaticInstance($staticInstance){
		self::$giveStatic = $staticInstance;
	}

}
