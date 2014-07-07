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
 * General Definition of WebEdition Glossary
 *
 */
class we_glossary_glossary extends weModelBase{

	const TYPE_LINK = 'link';
	const TYPE_ACRONYM = 'acronym';
	const TYPE_ABBREVATION = 'abbreviation';
	const TYPE_FOREIGNWORD = 'foreignword';
	const TYPE_TEXTREPLACE = 'textreplacement';

	/**
	 * Identifier of the glossayry item
	 *
	 * @var integer
	 */
	var $ID = 0;

	/**
	 * Path of the glossary item, composed by the language
	 * type and text
	 *
	 * @var string
	 */
	var $Path = "";

	/**
	 * is the item a folder
	 *
	 * @var boolean
	 */
	var $IsFolder = false;

	/**
	 * Icon of the item
	 *
	 * @var string
	 */
	var $Icon = "";

	/**
	 * Text of the item
	 *
	 * @var string
	 */
	var $Text = "";

	/**
	 * Type of the item, could be abbreviation, acronym, foreignword or link or textreplacement
	 *
	 * @var string
	 */
	var $Type = "";

	/**
	 * Language of the item
	 *
	 * @var string
	 */
	var $Language = "";

	/**
	 * title attribute
	 *
	 * @var string
	 */
	var $Title = "";

	/**
	 * all valid other attributes needed for later replacement
	 *
	 * @var array
	 */
	var $Attributes = array();

	/**
	 * should the item be linked to a detailed description
	 *
	 * @var boolean
	 */
	var $Linked = false;

	/**
	 * detailed description of the item
	 *
	 * @var string
	 */
	var $Description = '';

	/**
	 * timestamp of creation
	 *
	 * @var string
	 */
	var $CreationDate = 0;

	/**
	 * timestamp of last modification
	 *
	 * @var string
	 */
	var $ModDate = 0;

	/**
	 * timestamp of publishing
	 *
	 * @var string
	 */
	var $Published = 0;

	/**
	 * id of creator
	 *
	 * @var string
	 */
	var $CreatorID = 0;

	/**
	 * id of modifier
	 *
	 * @var string
	 */
	var $ModifierID = 0;

	/** determines if the whole word is replaced */
	public $Fullword = 1;

	/**
	 * internal list with al serialized fields in the database
	 *
	 * @var array
	 */
	var $_Serialized = array();

	/**
	 * @param integer $GlossaryId
	 * @desc Could load a glossary item if $GlossaryId is not 0
	 */
	function __construct($GlossaryId = 0){
		parent::__construct(GLOSSARY_TABLE);
		$this->_Serialized = array('Attributes');

		if($GlossaryId){
			$this->ID = $GlossaryId;
			$this->load($GlossaryId);
		} else {
			switch(we_base_request::_(we_base_request::STRING, 'cmd')){
				case 'new_glossary_abbreviation':
					$this->Type = self::TYPE_ABBREVATION;
					break;
				case 'new_glossary_acronym':
					$this->Type = self::TYPE_ACRONYM;
					break;
				case 'new_glossary_foreignword':
					$this->Type = self::TYPE_FOREIGNWORD;
					break;
				case 'new_glossary_link':
					$this->Type = self::TYPE_LINK;
					break;
				case 'new_glossary_textreplacement':
					$this->Type = self::TYPE_TEXTREPLACE;
					break;

					$cmdid = we_base_request::_(we_base_request::STRING, 'cmdid');
					if($cmdid && !preg_match('|^[0-9]|', $cmdid)){
						$this->View->Glossary->Language = substr($cmdid, 0, 5);
					}
			}
		}
	}

	function getEntries($Language, $Mode = 'all', $Type = 'all'){
		$Query = 'SELECT Type,Text,Title,Attributes FROM ' . GLOSSARY_TABLE . " WHERE Language='" . $GLOBALS['DB_WE']->escape($Language) . "' " .
			($Type != 'all' ? "AND Type='" . $GLOBALS['DB_WE']->escape($Type) . "' " : '');

		switch($Mode){
			case 'published':
				$Query .= 'AND Published>0 ';
				break;
			case 'unpublished':
				$Query .= 'AND Published=0 ';
				break;
		}

		$GLOBALS['DB_WE']->query($Query);

		$ReturnValue = array();
		while($GLOBALS['DB_WE']->next_record()){
			$Item = array(
				'Type' => $GLOBALS['DB_WE']->f("Type"),
				'Text' => $GLOBALS['DB_WE']->f("Text"),
				'Title' => $GLOBALS['DB_WE']->f("Title"),
			);

			if($GLOBALS['DB_WE']->f("Type") != self::TYPE_FOREIGNWORD){
				$temp = unserialize($GLOBALS['DB_WE']->f("Attributes"));
				$Item['Lang'] = (isset($temp['lang']) ? $temp['lang'] : '');
			} else {
				$Item['Lang'] = '';
			}
			$ReturnValue[] = $Item;
		}
		return $ReturnValue;
	}

	function publishItem($Language, $Text){
		return $GLOBALS['DB_WE']->query('UPDATE ' . GLOSSARY_TABLE . ' SET Published=UNIX_TIMESTAMP()'
				. " WHERE Language='" . $GLOBALS['DB_WE']->escape($Language) . "' AND Text='" . $GLOBALS['DB_WE']->escape($Text) . "'");
	}

	/**
	 * method to load data from database
	 *
	 * @param integer $id
	 */
	function load($id = 0){
		parent::load(strval($id));

		// serialize all needed attributes
		foreach($this->_Serialized as $Attribute){
			$this->$Attribute = unserialize($this->$Attribute);
		}
	}

	/**
	 * save the item to the database
	 *
	 */
	function save(){
		$this->Icon = ($this->IsFolder == 1 ? we_base_ContentTypes::FOLDER_ICON : 'prog.gif');

		$this->setPath();

		// serialize all needed attributes
		foreach($this->_Serialized as $Attribute){
			$this->$Attribute = serialize($this->$Attribute);
		}

		if(!$this->ID){
			$this->CreatorID = $_SESSION['user']['ID'];
			$this->CreationDate = time();
		}
		$this->ModifierID = $_SESSION['user']['ID'];
		$this->ModDate = time();


		$retVal = (parent::save());

		if(!$this->ID){
			$this->ID = $this->db->getInsertId();
		}

		// unserialize all needed attributes
		foreach($this->_Serialized as $Attribute){
			$this->$Attribute = unserialize($this->$Attribute);
		}

		return $retVal;
	}

	/**
	 * delete a glossary item from database
	 *
	 * @return boolean
	 */
	function delete(){
		if((!$this->ID) || ($this->IsFolder && !$this->_deleteChilds())){
			return false;
		}

		return parent::delete();
	}

	/**
	 * delete all childs of a item
	 *
	 * @return boolean
	 */
	function _deleteChilds(){
		return $this->db->query('DELETE FROM ' . $this->db->escape($this->table) . ' WHERE Path LIKE = "' . $this->db->escape($this->Path) . '/%"');
	}

	/**
	 * set the path of the item
	 *
	 */
	function setPath(){
		$this->Path = '/' . $this->Language . '/' . $this->Type . '/' . $this->Text;
	}

	/**
	 * set an Attribute
	 *
	 * @param string $Name
	 * @param string $Value
	 */
	function setAttribute($Name, $Value){
		$this->Attributes[$Name] = $Value;
	}

	/**
	 * get a attribute
	 *
	 * @param string $Name
	 * @return mixed
	 */
	function getAttribute($Name){
		if(!is_array($this->Attributes) || !array_key_exists($Name, $this->Attributes)){
			return null;
		}

		return $this->Attributes[$Name];
	}

	/**
	 * checks if a path already exists
	 *
	 * @param string $Path
	 * @return boolean
	 */
	function pathExists($Path){
		return (f('SELECT 1 FROM ' . $this->db->escape($this->table) . " WHERE Path Like Binary '" . $this->db->escape($Path) . "'" . ($this->ID ? ' AND ID != ' . intval($this->ID) : '') . ' LIMIT 1', '', $this->db));
	}

	function getIDByPath($Path){
		return intval(f('SELECT ID FROM ' . $this->db->escape($this->table) . ' WHERE Path = "' . $this->db->escape($Path) . '"', 'ID', $this->db));
	}

	/**
	 * check if the item is self (?!)
	 *
	 * @return boolean
	 */
	function isSelf(){
		$Text = self::escapeChars($this->Text);
		return strpos(htmlentities(we_base_file::clearPath(dirname($this->Path)) . '/'), '/' . $Text . '/') !== false;
	}

	//FIXME: some signs are broken due to utf-8
	function escapeChars($Text){
		$Text = quotemeta($Text); // escape . \ + * ? [ ^ ] ( $ )

		$escape = array('{', '&', '/', '\'', '"', '%');

		foreach($escape as $k){
			$before = $k;
			$after = "\\" . $k;
			if($k != ''){
				$Text = str_replace($before, $after, $Text);
			}
		}
		return $Text;
	}

	/**
	 * save a field to the database
	 *
	 * @param string $Name
	 * @return boolean
	 */
	function saveField($Name){
		$value = (in_array($Name, $this->_Serialized) ? unserialize($this->$Name) : $this->$Name);
		$this->db->query('UPDATE ' . $this->db->escape($this->table) . ' SET ' . $this->db->escape($Name) . "='" . $this->db->escape($value) . "' WHERE ID=" . intval($this->ID));

		return $this->db->affected_rows();
	}

	/**
	 * Clear all data from session
	 *
	 */
	function clearSessionVars(){
		if(isset($_SESSION['weS']['weGlossarySession'])){
			unset($_SESSION['weS']['weGlossarySession']);
		}
	}

	function addToException($language, $entry = ""){
		if(trim($entry) == ''){
			return true;
		}

		$items = self::getException($language);
		$items[] = $entry;
		$items = implode("\n", $items);

		return self::editException($language, $items);
	}

	function editException($language, $entries){
		$fileName = self::getExceptionFilename($language);

		$content = '';
		$items = explode("\n", $entries);
		sort($items);
		foreach($items as $item){
			$item = trim($item);
			if($item != ''){
				$content .= $item . "\n";
			}
		}

		return (file_put_contents($fileName, $content) !== FALSE);
	}

	function getException($language){
		$fileName = self::getExceptionFilename($language);

		if(file_exists($fileName) && is_file($fileName)){
			return file($fileName);
		}

		return array();
	}

	function getExceptionFilename($language){
		$fileDir = WE_GLOSSARY_MODULE_PATH . 'dict/';
		if(!is_dir($fileDir) && !we_base_file::createLocalFolder($fileDir)){
			return false;
		}

		return $fileDir . $language . '@' . $_SERVER['SERVER_NAME'] . '.dict';
	}

	function checkFieldText($text){
		$check = array('\\', '$', '|');

		foreach($check as $k){
			if(stristr(trim($text), $k)){
				return true;
			}
		}
		return false;
	}

}
