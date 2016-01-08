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

/** the parent class of storagable webEdition classes */
abstract class we_class{
	//constants for retrieving data from DB

	const LOAD_MAID_DB = 0;
	const LOAD_TEMP_DB = 1;
	const LOAD_REVERT_DB = 2; //we_temporaryDocument::revert gibst nicht mehr siehe #5789
	const LOAD_SCHEDULE_DB = 3;
	//constants define where to write document (guess)
	const SUB_DIR_NO = 0;
	const SUB_DIR_YEAR = 1;
	const SUB_DIR_YEAR_MONTH = 2;
	const SUB_DIR_YEAR_MONTH_DAY = 3;

	/* Name of the class => important for reconstructing the class from outside the class */
	var $ClassName = __CLASS__;
	/* In this array are all storagable class variables */
	var $persistent_slots = array('ClassName', 'Name', 'ID', 'Table', 'wasUpdate', 'InWebEdition');
	/* Name of the Object that was createt from this class */
	var $Name = '';

	/* ID from the database record */
	var $ID = 0;

	/* database table in which the object is stored */
	var $Table = '';
	protected $LangLinks = array();

	/* Database Object */
	protected $DB_WE;

	/* optional ID for being able to manipulate the ID for DB inserts, requires a getter and setter */
	protected $insertID = 0;

	/* Flag which is set when the file is not new */
	var $wasUpdate = 0;
	public $InWebEdition = false;
	var $PublWhenSave = 1;
	var $IsTextContentDoc = false;
	var $fileExists = 1;
	protected $errMsg = '';

	//Overwrite
	public function we_new(){

	}

	//Overwrite
	public function we_initSessDat($sessDat){

	}

	/* Constructor */

	function __construct(){
		$this->Name = md5(uniqid(__FILE__, true));
		$this->ClassName = get_class($this); //$this is different from self!
		$this->DB_WE = new DB_WE();
	}

	/* Intialize the class. If $sessDat (array) is set, the class will be initialized from this array */

	function init(){
		$this->we_new();
	}

	/* set the protected variable insertID */

	function setInsertID($insertID){
		$this->insertID = $insertID;
	}

	/* get the protected variable insertID */

	function getInsertID(){
		return $this->insertID;
	}

	/* returns the url $in with $we_transaction appended */

	public static function url($in){
		return $in . ( strpos($in, '?') !== FALSE ? '&' : '?') . 'we_transaction=' . $GLOBALS['we_transaction'];
	}

	/* returns the code for a hidden " we_transaction-field" */

	public static function hiddenTrans(){
		return we_html_element::htmlHidden("we_transaction", $GLOBALS['we_transaction']);
	}

	/* must be overwritten by child */

	function saveInSession(/* &$save */){

	}

	/* creates a text-input field for entering Data that will be stored at the $elements Array */

	function formInput($name, $size = 25, $type = 'txt'){
		return $this->formTextInput($type, $name, (g_l('weClass', '[' . $name . ']') ? : $name), $size);
	}

	/* creates a color field. when user clicks, a colorchooser opens. Data that will be stored at the $elements Array */

	function formColor($width, $name, $type = 'txt', $height = 18, $isTag = false){
		$value = $this->getElement($name);
		if(!$isTag){
			$width -= 4;
		}
		$formname = 'we_' . $this->Name . '_' . $type . '[' . $name . ']';
		$out = we_html_element::htmlHidden($formname, $this->getElement($name)) .
			'<table class="default" style="border:1px solid black"><tr><td' . ($value ? (' bgcolor="' . $value . '"') : '') . '><a href="javascript:setScrollTo();we_cmd(\'openColorChooser\',\'' . $formname . '\',document.we_form.elements[\'' . $formname . '\'].value);"><span style="width:' . $width . 'px;height:' . $height . 'px"/></a></td></tr></table>';
		return g_l('weClass', '[' . $name . ']', true) !== false ? we_html_tools::htmlFormElementTable($out, g_l('weClass', '[' . $name . ']')) : $out;
	}

	function formTextInput($elementtype, $name, $text, $size = 24, $maxlength = '', $attribs = '', $textalign = 'left', $textclass = 'defaultfont'){
		if(!$elementtype){
			$ps = $this->$name;
		}
		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput(($elementtype ? ('we_' . $this->Name . '_' . $elementtype . '[' . $name . ']') : ('we_' . $this->Name . '_' . $name)), $size, ($elementtype ? $this->getElement($name) : $ps), $maxlength, $attribs), $text, $textalign, $textclass);
	}

	function formInputField($elementtype, $name, $text, $size, $width, $maxlength = '', $attribs = '', $textalign = 'left', $textclass = 'defaultfont'){
		if(!$elementtype){
			$ps = $this->$name;
		}
		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('we_' . $this->Name . '_' . ($elementtype ? $elementtype . '[' . $name . ']' : $name), $size, ($elementtype && ($elVal = $this->getElement($name)) ? $elVal : (isset($GLOBALS['meta'][$name]) ? $GLOBALS['meta'][$name]['default'] : (isset($ps) ? $ps : '') )), $maxlength, $attribs, 'text', $width), $text, $textalign, $textclass);
	}

	function formTextArea($elementtype, $name, $text, $rows = 10, $cols = 30, array $attribs = array(), $textalign = 'left', $textclass = 'defaultfont'){
		return we_html_tools::htmlFormElementTable(self::htmlTextArea(($elementtype ? ('we_' . $this->Name . '_' . $elementtype . "[$name]") : ('we_' . $this->Name . '_' . $name)), $rows, $cols, $this->getElement($name), $attribs), $text, $textalign, $textclass);
	}

	function formSelectFromArray($elementtype, $name, array $vals, $text, $size = 1, $multiple = false, array $attribs = array()){
		$pop = $this->htmlSelect('we_' . $this->Name . '_' . ($elementtype ? $elementtype . '[' . $name . ']' : $name), $vals, $size, ($elementtype ? $this->getElement($name) : $this->$name), $multiple, $attribs);
		return we_html_tools::htmlFormElementTable($pop, $text, 'left', 'defaultfont');
	}

	//FIXME: remove
	function htmlTextInput($name, $size = 0, $value = '', $maxlength = '', $attribs = '', $type = 'text', $width = 0, $height = 0){
		return we_html_tools::htmlTextInput($name, 0, $value, $maxlength, $attribs, $type, $width, $height);
	}

	static function htmlTextArea($name, $rows = 10, $cols = 30, $value = '', array $attribs = array()){
		return we_html_element::htmlTextArea(array_merge(array(
				'name' => trim($name),
				'class' => 'defaultfont wetextarea',
				'rows' => abs($rows),
				'cols' => abs($cols),
					), $attribs
				), ($value ? (oldHtmlspecialchars($value)) : ''));
	}

	//fixme: add auto-grouping, add format
	function htmlSelect($name, array $values, $size = 1, $selectedIndex = '', $multiple = false, array $attribs = array(), $compare = 'value', $width = 0, $classes = array()){
		$optgroup = false;
		$selIndex = $multiple ? explode(',', $selectedIndex) : array($selectedIndex);
		$ret = '';
		foreach($values as $value => $text){
			if($text === we_html_tools::OPTGROUP){
				$ret .= ($optgroup ? '</optgroup>' : '') . '<optgroup label="' . oldHtmlspecialchars($value) . '">';
				$optgroup = true;
				continue;
			}

			$ret .= '<option ' . (isset($classes[$value]) ? 'class="' . $classes[$value] . '" ' : '') . ' value="' . oldHtmlspecialchars($value) . '"' . (in_array((($compare === 'value') ? $value : $text), $selIndex) ? ' selected="selected"' : '') . '>' . $text . '</option>';
		}
		if($optgroup){
			$ret .= '</optgroup>';
		}

		return we_html_element::htmlSelect(array_merge($attribs, array(
				'id' => trim($name),
				'class' => "weSelect defaultfont",
				'name' => trim($name),
				'size' => abs($size),
				($multiple ? 'multiple' : null) => 'multiple',
				($width ? 'width' : null) => $width
				)), $ret);
	}

	############## new fns
	/* creates a select field for entering Data that will be stored at the $elements Array */

	function formSelectElement($width, $name, $values, $type = 'txt', $size = 1, array $attribs = array()){
		return we_html_tools::htmlFormElementTable(
				we_html_tools::html_select('we_' . $this->Name . '_' . $type . '[' . $name . ']', $size, $values, $this->getElement($name), array_merge(array(
					'class' => 'defaultfont',
					'width' => $width,
						), $attribs))
				, g_l('weClass', '[' . $name . ']'));
	}

	function formInput2($width, $name, $size = 25, $type = 'txt', $attribs = ''){
		return $this->formInputField($type, $name, (g_l('weClass', '[' . $name . ']', true)? : $name), $size, $width, '', $attribs);
	}

	/* creates a text-input field for entering Data that will be stored at the $elements Array and shows information from another Element */

	function formInputInfo2($width, $name, $size, $type = 'txt', $attribs = '', $infoname = ''){
		$infotext = ' (' . (g_l('weClass', '[' . $infoname . ']', true) ? : $infoname) . ': ' . $this->getElement($infoname) . ')';
		return $this->formInputField($type, $name, (g_l('weClass', '[' . $name . ']', true) ? : $name) . $infotext, $size, $width, '', $attribs);
	}

	function formSelect2($width, $name, $table, $val, $txt, $text, $sqlFrom = '', $sqlTail = '', $size = 1, $selectedIndex = '', $multiple = false, $onChange = '', array $attribs = array(), $textalign = 'left', $textclass = 'defaultfont', $precode = '', $postcode = '', $firstEntry = '', $gap = 20){
		$vals = array();
		if($firstEntry){
			$vals[$firstEntry[0]] = $firstEntry[1];
		}
		$this->DB_WE->query('SELECT ' . ($sqlFrom ? : $this->DB_WE->escape($val) . ',' . $this->DB_WE->escape($txt)) . ' FROM ' . $this->DB_WE->escape($table) . ' WHERE ' . $sqlTail);
		while($this->DB_WE->next_record(MYSQL_ASSOC)){
			$v = $this->DB_WE->f($val);
			$t = $this->DB_WE->f($txt);
			$vals[$v] = $t;
		}
		$vals = we_html_tools::groupArray($vals, false, 1);
		$myname = 'we_' . $this->Name . '_' . $name;

		$ps = $this->$name;

		$pop = $this->htmlSelect($myname . ($multiple ? 'Tmp' : ''), $vals, $size, $ps, $multiple, array_merge(array(
			'onchange' => $onChange . ($multiple ? ";var we_sel='';for(i=0;i<this.options.length;i++){if(this.options[i].selected){we_sel += (this.options[i].value + ',');};};if(we_sel){we_sel=we_sel.substring(0,we_sel.length-1)};this.form.elements['" . $myname . "'].value=we_sel;" : '')
				), $attribs), 'value', $width);

		if($precode || $postcode){
			$pop = '<table class="default"><tr>' . ($precode ? ('<td style="padding-right:' . $gap . 'px;">' . $precode . '</td>') : '') . '<td>' . $pop . '</td>' . ($postcode ? ('<td>' . $postcode . '</td>') : '') . '</tr></table>';
		}
		return ($multiple ? we_html_element::htmlHidden($myname, $selectedIndex) : '') . we_html_tools::htmlFormElementTable($pop, $text, $textalign, $textclass);
	}

	function formSelect4($width, $name, $table, $val, $txt, $text, $sqlTail = '', $size = 1, $selectedIndex = '', $multiple = false, $onChange = '', array $attribs = array(), $textalign = 'left', $textclass = 'defaultfont', $precode = '', $postcode = '', $firstEntry = '', $gap = 20){
		$vals = array();
		if($firstEntry){
			$vals[$firstEntry[0]] = $firstEntry[1];
		}
		$this->DB_WE->query('SELECT ' . $this->DB_WE->escape($val) . ',' . $this->DB_WE->escape($txt) . ' FROM ' . $this->DB_WE->escape($table) . ' ' . $sqlTail);
		while($this->DB_WE->next_record(MYSQL_ASSOC)){
			$v = $this->DB_WE->f($val);
			$t = $this->DB_WE->f($txt);
			$vals[$v] = $t;
		}
		$myname = 'we_' . $this->Name . '_' . $name;

		$pop = $this->htmlSelect($myname, $vals, $size, $selectedIndex, $multiple, array_merge(array('onchange' => $onChange), $attribs), 'value', $width);
		if($precode || $postcode){
			$pop = '<table class="default"><tr>' . ($precode ? ('<td style="padding-right:' . $gap . 'px;">' . $precode . '</td>') : '') . '<td>' . $pop . '</td>' . ($postcode ? ('<td>' . $postcode . '</td>') : '') . '</tr></table>';
		}
		return we_html_tools::htmlFormElementTable($pop, $text, $textalign, $textclass);
	}

##### NEWSTUFF ####
# public ##################

	public function initByID($ID, $Table = FILE_TABLE, $from = we_class::LOAD_MAID_DB){
		$this->ID = intval($ID);
		$this->Table = ($Table ? : FILE_TABLE);
		$this->we_load($from);
		$GLOBALS['we_ID'] = $ID; //FIXME: check if we need this !
		$GLOBALS['we_Table'] = $this->Table;
		// init Customer Filter !
		if(isset($this->documentCustomerFilter) && defined('CUSTOMER_TABLE')){
			$this->initWeDocumentCustomerFilterFromDB();
		}
	}

	/**
	 * inits weDocumentCustomerFilter from db regarding the modelId
	 * is called from "we_textContentDocument::we_load"
	 * @see we_textContentDocument::we_load
	 */
	protected function initWeDocumentCustomerFilterFromDB(){
		$this->documentCustomerFilter = we_customer_documentFilter::getFilterOfDocument($this);
	}

	public function we_load(/* $from = self::LOAD_MAID_DB */){
		$this->i_getPersistentSlotsFromDB();
	}

	public function we_save(/* $resave = 0 */){
		$this->wasUpdate = $this->ID > 0;
		return $this->i_savePersistentSlotsToDB();
	}

	public function we_delete(){
		we_base_delete::deleteEntry($this->ID, $this->Table, true, false, $this->DB_WE);
	}

	public function we_publish(/* $DoNotMark = false, $saveinMainDB = true */){
		return true; // overwrite
	}

	public function we_unpublish(/* $DoNotMark = false */){
		return true; // overwrite
	}

	public function we_republish(){
		return true;
	}

	protected function i_setElementsFromHTTP(){
		if($_REQUEST){
			// do not set REQUEST VARS into the document
			$cmd0 = we_base_request::_(we_base_request::STRING, 'cmd', '', 0);

			if(($cmd0 === 'switch_edit_page' && we_base_request::_(we_base_request::STRING, 'we_cmd', false, 3)) || ($cmd0 === 'save_document' && we_base_request::_(we_base_request::STRING, 'we_cmd', '', 7) === 'save_document')){
				return true;
			}
			$regs = array();
			foreach($_REQUEST as $n => $v){
				if(preg_match('#^we_' . preg_quote($this->Name, '#') . '_([^\[]+)$#', $n, $regs) && in_array($regs[1], $this->persistent_slots)){
					$this->$regs[1] = $v;
				}
			}
		}
	}

	protected function i_getPersistentSlotsFromDB($felder = '*'){
		$fields = getHash('SELECT ' . $felder . ' FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID=' . intval($this->ID), $this->DB_WE);
		if($fields){
			foreach($fields as $k => $v){
				if($k && in_array($k, $this->persistent_slots)){
					$this->{$k} = $v;
				}
			}
		} else {
			$this->fileExists = 0;
		}
	}

	protected function i_savePersistentSlotsToDB($felder = ''){
		$tableInfo = $this->DB_WE->metadata($this->Table);
		$feldArr = $felder ? makeArrayFromCSV($felder) : $this->persistent_slots;
		$fields = array();
		if(!$this->wasUpdate && $this->insertID && f('SELECT 1 FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID=' . intval($this->insertID) . ' LIMIT 1', '', $this->DB_WE)){
			return false;
		}
		foreach($tableInfo as $info){
			$fieldName = $info['name'];
			if(in_array($fieldName, $feldArr)){
				$val = isset($this->$fieldName) ? $this->$fieldName : '';

				if($fieldName != 'ID'){
					$fields[$fieldName] = $val;
				}
				if(!$this->wasUpdate && $this->insertID && $fieldName === 'ID'){//for Apps to be able to manipulate Insert-ID
					$fields['ID'] = $this->insertID;
					$this->insertID = 0;
				}
			}
		}
		if($fields){
			$where = ($this->wasUpdate ? ' WHERE ID=' . intval($this->ID) : '');
			$ret = (bool) ($this->DB_WE->query(($this->wasUpdate ? 'UPDATE ' : 'INSERT INTO ') . $this->DB_WE->escape($this->Table) . ' SET ' . we_database_base::arraySetter($fields) . $where));
			$this->ID = ($this->wasUpdate ? $this->ID : $this->DB_WE->getInsertId());
			return $ret;
		}
		return false;
	}

	protected function i_descriptionMissing(){
		return false;
	}

	function setDocumentControlElements(){
		//	function is overwritten in we_webEditionDocument
	}

	function executeDocumentControlElements(){
		//	function is overwritten in we_webEditionDocument
	}

	function isValidEditPage($editPageNr){
		return (is_array($this->EditPageNrs) ?
				in_array($editPageNr, $this->EditPageNrs) :
				false);
	}

	protected function updateRemoteLang($db, $id, $lang, $type){
		//overwrite if needed <= diese verwenden!
	}

	/**
	 * If documents, objects, folders and docTypes are saved and there is no LANGLINK_SUPPORT we must check, whether there is a change of language:
	 * if so, we must delete eventual entries in tblLangLink (entered before LANGLINK_SUPPORT wa stopped. <= TODO: Merge this with next method!
	 */
	protected function checkRemoteLanguage($table, $isfolder = false){
		if(($newLang = we_base_request::_(we_base_request::STRING, 'we_' . $this->Name . '_Language'))){
			$isobject = ($table === OBJECT_FILES_TABLE) ? 1 : 0;
			$type = stripTblPrefix($table);
			$type = ($isfolder && $isobject) ? 'tblFile' : ($isobject ? 'tblObjectFiles' : $type);

			$delete = f('SELECT 1 FROM ' . LANGLINK_TABLE . ' WHERE DLocale!="' . $this->DB_WE->escape($newLang) . '" AND DocumentTable="' . $this->DB_WE->escape($type) . '" AND IsObject=' . intval($isobject) . ' AND IsFolder=' . intval($isfolder) . ' AND DID=' . intval($this->ID) . ' LIMIT 1', '', $this->DB_WE);

			if($delete){
				$this->DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DID=' . intval($this->ID) . ' AND DocumentTable="' . $this->DB_WE->escape($type) . '" AND IsFolder=' . intval($isfolder) . ' AND IsObject=' . intval($isobject));
				if(!$isfolder){
					$this->DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE LDID=' . intval($this->ID) . ' AND DocumentTable="' . $this->DB_WE->escape($type) . '" AND IsFolder=0 AND IsObject=' . intval($isobject));
				}
			}
		}
	}

	/**
	 * Before writing LangLinks to the db, we must check the Document-Locale: if it has changed, we must update or clear
	 * existing LangLinks from and to this document.
	 */
	protected function setLanguageLink(array $LinkArray, $type, $isfolder = false, $isobject = false){
		if(!(LANGLINK_SUPPORT)){
			return true;
		}
		$newLang = $this->Language;
		if(!$newLang){
			return false;
		}
		$LangLinkArray = array();
		if($type !== 'tblDocTypes'){
			$LangLinkArray = array();
			foreach($LinkArray as $lang => $link){
				$LangLinkArray[$lang] = $link['id'];
			}
		} else {
			$LangLinkArray = $LinkArray;
		}

		$db = new DB_WE();
		$documentTable = ($type === 'tblObjectFile') ? 'tblObjectFiles' : $type;
		$ownDocumentTable = ($isfolder && $isobject) ? FILE_TABLE : addTblPrefix($documentTable);
		$origLinks = array();

		if(!$isfolder){
			$oldLang = f('SELECT Language FROM ' . $db->escape($ownDocumentTable) . ' WHERE ID=' . intval($this->ID), '', $db);
			if($newLang != $oldLang){// language changed
				// what langs where linked before document-language changed?
				$this->DB_WE->query('SELECT * FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $this->DB_WE->escape($type) . '" AND DID=' . intval($this->ID) . ' AND IsObject = ' . ($isobject ? 1 : 0) . ' AND IsFolder = ' . intval($isfolder));
				$origLinks = $this->DB_WE->getAllFirst(false);
				$origLangs = array_keys($origLinks);
				// because of UNIQUE-Indexes we do first delete obsolete entries in tblLangLink
				// => after optimizing executeSetLanguageLink() this will be obsolete!
				$this->DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE (DID=' . intval($this->ID) . ' OR LDID=' . intval($this->ID) . ') AND IsFolder=0 AND IsObject=' . ($isobject ? 1 : 0) . ' AND DocumentTable="' . $this->DB_WE->escape($type) . '"');

				// links FROM folders to the actual we_document/object must be updated right here.
				// if updating leads to conflict, we must delete a link
				$DB_WE2 = new DB_WE();
				$this->DB_WE->query('SELECT DID,(DLocale="' . $this->DB_WE->escape($newLang) . '"||(SELECT 1 FROM ' . LANGLINK_TABLE . ' WHERE DID=l.DID AND IsFolder=1 AND Locale="' . $this->DB_WE->escape($newLang) . '" LIMIT 1)) as `del` FROM ' . LANGLINK_TABLE . ' l WHERE LDID=' . intval($this->ID) . ' AND IsFolder=1');
				while($this->DB_WE->next_record()){
					if($this->DB_WE->f('del')){
						$DB_WE2->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE LDID=' . intval($this->ID) . ' AND DID=' . $this->DB_WE->f('DID') . ' AND IsFolder=1');
					} else {
						$DB_WE2->query('UPDATE ' . LANGLINK_TABLE . ' SET Locale="' . $this->DB_WE->escape($newLang) . '" WHERE LDID=' . intval($this->ID) . ' AND DID=' . $this->DB_WE->f('DID') . ' AND IsFolder=1');
					}
				}
				// if there is no conflict we can set new links and call prepareSetLanguageLinks()
				if(!in_array($newLang, $origLangs)){
					return ($this->prepareSetLanguageLink($LangLinkArray, $origLinks, true, $newLang, $type, $isfolder, $isobject, $ownDocumentTable)) ? true : false;
				}

				echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('weClass', '[languageLinksLocaleChanged]'), we_message_reporting::WE_MESSAGE_NOTICE)));
				return true;
			}
			//default case: there was now change of page language. Loop method call to another method, preparing LangLinks
			return ($this->prepareSetLanguageLink($LangLinkArray, $origLinks, false, $oldLang, $type, $isfolder, $isobject, $ownDocumentTable)) ? true : false;
		}//isfolder
		if(f('SELECT DLocale FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $this->DB_WE->escape($type) . '" AND IsObject=' . ($isobject ? 1 : 0) . ' AND IsFolder=1 AND DID=' . intval($this->ID), '', $this->DB_WE) != $newLang){
			$this->DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DID=' . intval($this->ID) . ' AND DocumentTable="tblFile" AND IsFolder=1 AND IsObject=' . ($isobject ? 1 : 0));
		}
		return ($this->prepareSetLanguageLink($LangLinkArray, $origLinks, false, $newLang, $type, $isfolder, $isobject, $ownDocumentTable)) ? true : false;
	}

	/**
	 * In this method the links of $LangLinkArray are testet twice:
	 * 1) We only write new or changed LangLinks to db, if LangLink-Locale and Locale of the targe-document/object fit together.
	 * 2) In recursive-mode we only one document/object to another, if their respective link-chains are not in conflict.
	 */
	private function prepareSetLanguageLink(array $LangLinkArray, $origLinks, $langChange, $ownLocale, $type, $isfolder, $isobject, $ownDocumentTable){
		$documentTable = ($type === 'tblObjectFile') ? 'tblObjectFiles' : $type; // we could take these  from setLanguageLink()...
		$ownDocumentTable = ($isfolder && $isobject) ? FILE_TABLE : addTblPrefix($documentTable);

		if(in_array(0, $LangLinkArray) || in_array('', $LangLinkArray)){
			if(!$langChange){
				$origLinks = array();
				$this->DB_WE->query('SELECT Locale,LDID FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $this->DB_WE->escape($type) . '" AND DID=' . intval($this->ID) . ' AND IsObject=' . ($isobject ? 1 : 0) . ' AND IsFolder=' . ($isfolder ? 1 : 0));
				while($this->DB_WE->next_record()){
					$origLinks[$this->DB_WE->Record['Locale']] = $this->DB_WE->Record['LDID'];
				}
			}
			$tmpLangLinkArray = array();
			foreach($LangLinkArray as $locale => $LDID){

				if(!($LDID == '' || $LDID == 0 || $LDID == -1)){
					$tmpLangLinkArray[$locale] = $LDID;
				} elseif(array_key_exists($locale, $origLinks)){
					$tmpLangLinkArray[$locale] = -1;
				}
			}
			$LangLinkArray = $tmpLangLinkArray;
		}

		$k = 0;
		foreach($LangLinkArray as $locale => $LDID){
			$k = ($locale == $ownLocale) ? $k : $k + 1;
			$newOrChanged = false;
			if(($actualLDID = f('SELECT LDID FROM ' . LANGLINK_TABLE . ' WHERE Locale="' . $this->DB_WE->escape($locale) . '" AND DID=' . intval($this->ID) . ' AND DocumentTable="' . $this->DB_WE->escape($type) . '" AND IsObject=' . ($isobject ? 1 : 0) . ' AND IsFolder=' . ($isfolder ? 1 : 0), '', $this->DB_WE))){
				if($actualLDID != $LDID){
					$newOrChanged = true; //changed
				}
			} else {
				$newOrChanged = true; //new
			}
			if(($newOrChanged || $langChange) && !($LDID == -1)){

				// from Folders, links lead only to documents, never to objects
				//$fileTable = $isfolder ? FILE_TABLE : ($isobject ? OBJECT_FILES_TABLE : FILE_TABLE);

				if(($fileLang = f('SELECT Language FROM ' . $this->DB_WE->escape(addTblPrefix($documentTable)) . ' WHERE ID=' . intval($LDID), '', $this->DB_WE))){
					if($fileLang != $locale){
						echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsElement(we_message_reporting::getShowMessageCall(sprintf(g_l('weClass', '[languageLinksLangNotok]'), $locale, $fileLang, $locale), we_message_reporting::WE_MESSAGE_NOTICE)));
						return true;
					}
					if(!$isfolder){
						$setThisLink = true;
						$this->DB_WE->query('SELECT Locale,LDID FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $this->DB_WE->escape($type) . '" AND DID=' . intval($this->ID) . ' AND IsObject=' . ($isobject ? 1 : 0) . ' AND IsFolder=' . ($isfolder ? 1 : 0));
						$actualLinks = $this->DB_WE->getAllFirst(false);
						$actualLangs = array_keys($actualLinks);
						$actualLangs[] = $ownLocale;

						$this->DB_WE->query('SELECT Locale,LDID FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $this->DB_WE->escape($type) . '" AND DID=' . intval($LDID) . ' AND IsObject=' . ($isobject ? 1 : 0) . ' AND IsFolder=' . ($isfolder ? 1 : 0));
						$targetLinks = $this->DB_WE->getAllFirst(false);
						$targetLangs = array_keys($targetLinks);

						if(count($actualLangs) > 1 || $targetLangs){
							$intersect = array_intersect($actualLangs, $targetLangs);
							$setThisLink = $intersect ? false : true;
						}

						if(!$newOrChanged){
							$setThisLink = true;
						}

						if(!$setThisLink){
							echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsElement(we_message_reporting::getShowMessageCall(sprintf(g_l('weClass', '[languageLinksConflicts]'), $locale), we_message_reporting::WE_MESSAGE_NOTICE)));
							return true;
						}
						// instead of modifying db-Enries, we delete them and create new ones
						if(!empty($actualLinks[$locale])){
							$deleteObsoleteArray = $actualLinks;
							$deleteObsoleteArray[$locale] = -1;
							$this->executeSetLanguageLink($deleteObsoleteArray, $type, $isfolder, $isobject);
						}

						$preparedLinkArray = $actualLinks;
						$preparedLinkArray[$locale] = $LDID;
						foreach($targetLinks as $targetLocale => $targetLDID){
							$preparedLinkArray[$targetLocale] = $targetLDID;
						}
						$this->executeSetLanguageLink($preparedLinkArray, $type, $isfolder, $isobject);
					} else {//!isfolder
						if(f('SELECT 1 FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $this->DB_WE->escape($type) . '" AND DLocale="' . $this->DB_WE->escape($ownLocale) . '" AND Locale="' . $this->DB_WE->escape($locale) . '" AND LDID=' . intval($LDID) . ' AND IsObject=' . ($isobject ? 1 : 0) . ' AND IsFolder=1 LIMIT 1', '', $this->DB_WE)){//conflict
							echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsElement(we_message_reporting::getShowMessageCall(sprintf(g_l('weClass', '[languageLinksConflicts]'), $locale), we_message_reporting::WE_MESSAGE_NOTICE)));
							return true;
						}
						$actualLinks = array();
						$this->DB_WE->query('SELECT Locale,LDID FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $this->DB_WE->escape($type) . '" AND DID=' . intval($this->ID) . ' AND IsObject=' . ($isobject ? 1 : 0) . ' AND IsFolder=' . ($isfolder ? 1 : 0));
						while($this->DB_WE->next_record()){
							$actualLinks[$this->DB_WE->Record['Locale']] = $this->DB_WE->Record['LDID'];
						}
						$actualLinks[$locale] = $LDID;
						$this->executeSetLanguageLink($actualLinks, $type, $isfolder, $isobject);
					}
				}
			}// end of new or changed link
			else {//delete links
				$actualLinks = array();
				$this->DB_WE->query('SELECT Locale,LDID FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $this->DB_WE->escape($type) . '" AND DID=' . intval($this->ID) . " AND IsObject = " . intval($isobject) . " AND IsFolder = " . intval($isfolder));
				while($this->DB_WE->next_record()){
					$actualLinks[$this->DB_WE->Record['Locale']] = $this->DB_WE->Record['LDID'];
				}
				$preparedLinkArray = $actualLinks;
				$preparedLinkArray[$locale] = $LDID;
				$this->executeSetLanguageLink($preparedLinkArray, $type, $isfolder, $isobject);
			}
		}//foreach
		return true;
	}

	//FIXME: in this method tblLangLink.ID is used and a lot of things are obsolete => to be cleaned in 6.3.1
	private function executeSetLanguageLink($LangLinkArray, $type, $isfolder = false, $isobject = false){
		if(!is_array($LangLinkArray)){
			return;
		}
		$this->DB_WE->query('SELECT * FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $this->DB_WE->escape($type) . '" AND DID=' . intval($this->ID) . ' AND IsFolder=' . ($isfolder ? 1 : 0) . ' AND IsObject=' . ($isobject ? 1 : 0));
		$orig = $this->DB_WE->getAll();
		if(!$isfolder){//folders never have backlinks BUT the document linked to the folder CAN have them if linked to another document
			$lids = array();
			foreach($orig as $cur){
				$lids[] = $cur['LDID'];
			}
			if($lids){
				$this->DB_WE->query('SELECT * FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $this->DB_WE->escape($type) . '" AND DID IN (' . implode(',', $lids) . ')');
			}
			$orig = array_merge($orig, $this->DB_WE->getAll());
		}

		foreach($LangLinkArray as $locale => $LDID){ //obsolete if we call executeSetLanguageLink with only the link to bechanged (instead of whole $LangLinkArray)
			if(($ID = f('SELECT ID FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $this->DB_WE->escape($type) . '" AND DID=' . intval($this->ID) . ' AND Locale="' . $this->DB_WE->escape($locale) . '" AND isFolder=' . intval($isfolder) . ' AND IsObject=' . intval($isobject), 'ID', $this->DB_WE))){
				$this->DB_WE->query('UPDATE ' . LANGLINK_TABLE . ' SET ' . we_database_base::arraySetter(array(
						'LDID' => $LDID,
						'DLocale' => $this->Language
					)) . ' WHERE ID=' . intval($ID) . ' AND DocumentTable="' . $this->DB_WE->escape($type) . '"');
			} elseif($locale != $this->Language && $LDID > 0){
				$this->DB_WE->query('INSERT INTO ' . LANGLINK_TABLE . ' SET ' . we_database_base::arraySetter(array(
						'DID' => $this->ID,
						'DLocale' => $this->Language,
						'IsFolder' => $isfolder,
						'IsObject' => $isobject,
						'LDID' => $LDID,
						'Locale' => $locale,
						'DocumentTable' => $type
				)));
			}

			if(!$isfolder && $LDID && $LDID != $this->ID){
				if(($ID = f('SELECT ID FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $this->DB_WE->escape($type) . '" AND DID=' . intval($LDID) . ' AND Locale="' . $this->DB_WE->escape($this->Language) . '" AND IsObject=' . ($isobject ? 1 : 0), '', $this->DB_WE))){
					if($LDID > 0){
						$this->DB_WE->query('UPDATE ' . LANGLINK_TABLE . ' SET ' . we_database_base::arraySetter(array(
								'DID' => $LDID,
								'DLocale' => $locale,
								'LDID' => $this->ID,
								'Locale' => $this->Language
							)) . ' WHERE ID=' . intval($ID) . ' AND DocumentTable="' . $this->DB_WE->escape($type) . '"');
					} elseif($LDID < 0){// here we could delete istead of update (and then delete later...)
						$this->DB_WE->query('UPDATE ' . LANGLINK_TABLE . ' SET ' . we_database_base::arraySetter(array(
								'DID' => $LDID, //FIXME: DID is unsigned => result=0!
								'DLocale' => $locale,
								'LDID' => 0,
								'Locale' => $this->Language,
							)) . ' WHERE ID=' . intval($ID) . ' AND DocumentTable="' . $this->DB_WE->escape($type) . '"');
					}
				} elseif($LDID > 0){
					$this->DB_WE->query('INSERT INTO ' . LANGLINK_TABLE . ' SET ' . we_database_base::arraySetter(array(
							'DID' => $LDID,
							'DLocale' => $locale,
							'LDID' => $this->ID,
							'Locale' => $this->Language,
							'IsObject' => ($isobject ? 1 : 0),
							'DocumentTable' => $type
					)));
				}
			}

			if(!$isfolder && $LDID < 0 && $LDID != $this->ID){
				if($LDID > 0){// never happens!
					$this->DB_WE->query('REPLACE INTO ' . LANGLINK_TABLE . ' SET DID=' . intval($LDID) . ', DLocale="' . $this->DB_WE->escape($locale) . '", LDID=' . intval($this->ID) . ', Locale="' . $this->DB_WE->escape($this->Language) . '",IsObject=' . ($isobject ? 1 : 0) . ', DocumentTable="' . $this->DB_WE->escape($type) . '"');
				}
			}
		}//foreach

		if(!$isfolder){
			foreach($LangLinkArray as $locale => $LDID){
				if($LDID > 0){
					$rows = $this->DB_WE->getAllq('SELECT * FROM ' . LANGLINK_TABLE . ' WHERE DID=' . intval($this->ID) . ' AND DocumentTable="' . $this->DB_WE->escape($type) . '" AND IsObject=' . ($isobject ? 1 : 0));
					if(count($rows) > 1){
						foreach($rows as $i => $row){
							$j = ($i + 1) % count($rows);
							if($rows[$i]['LDID'] && $rows[$j]['LDID']){
								$this->DB_WE->query('REPLACE INTO ' . LANGLINK_TABLE . ' SET ' . we_database_base::arraySetter(array(
										'DID' => $rows[$i]['LDID'],
										'DLocale' => $rows[$i]['Locale'],
										'LDID' => $rows[$j]['LDID'],
										'Locale' => $rows[$j]['Locale'],
										'IsObject' => ($isobject ? 1 : 0),
										'DocumentTable' => $type
								)));
							}
						}
					}
				} elseif($LDID < 0){
					foreach($orig as $origrow){
						if($origrow['DLocale'] == $locale){
							$this->DB_WE->query('SELECT ID FROM ' . LANGLINK_TABLE . ' WHERE DID=' . intval($origrow['DID']) . ' AND DLocale="' . $this->DB_WE->escape($locale) . '" AND DocumentTable="' . $this->DB_WE->escape($type) . '" AND IsObject=' . ($isobject ? 1 : 0));
							if(($ids = $this->DB_WE->getAll(true))){
								$this->DB_WE->query('UPDATE ' . LANGLINK_TABLE . ' SET LDID=0 WHERE ID IN(' . implode(',', $ids) . ') AND DocumentTable="' . $this->DB_WE->escape($type) . '"');
							}
						}
						if($origrow['Locale'] == $locale){
							$this->DB_WE->query('SELECT ID FROM ' . LANGLINK_TABLE . ' WHERE LDID=' . intval($origrow['LDID']) . ' AND Locale="' . $this->DB_WE->escape($locale) . '" AND DocumentTable="' . $this->DB_WE->escape($type) . '" AND IsObject=' . ($isobject ? 1 : 0));
							if(($ids = $this->DB_WE->getAll(true))){
								$this->DB_WE->query('UPDATE ' . LANGLINK_TABLE . ' SET LDID=0 WHERE ID IN(' . implode(',', $ids) . ') AND DocumentTable="' . $this->DB_WE->escape($type) . '"');
							}
						}
					}
				}
			}
		}
		$this->DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DID=0 OR LDID=0');
	}

	/*	 * returns error-messages recorded during an operation, currently only save is used */

	public function getErrMsg(){
		return ($this->errMsg ? '\n' . str_replace("\n", '\n', $this->errMsg) : '');
	}

	//FIXME: this is temporary
	public function getDBf($field){
		return $this->DB_WE->f($field);
	}

}
