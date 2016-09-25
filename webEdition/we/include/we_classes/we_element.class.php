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
class we_element{

	var $ClassName = __CLASS__;
	var $DID = 0;
	var $Name = '';
	var $Type = '';
	var $CID = 0;
	var $BDID = 0;
	var $Dat = '';
	var $LangugeID = 0;
	var $Len = 0;
	var $link_attribs = ['DID', 'Name', 'Type'];
	var $content_attribs = ['CID', 'BDID', 'Dat', 'LanguageID'];
	var $persistent_slots = ['ClassName', 'Name', 'Type', 'BDID', 'Dat', 'LanguageID'];
	var $Link;
	var $Content;
	var $linked = false;
	static $db = '';

	function __construct($link_props = true, $options = []){
		if(!is_object(self::$db)){
			self::$db = new DB_WE();
		}
		$this->DID = 0;
		$this->Link = new we_base_model(LINK_TABLE, self::$db);
		$this->Link->setKeys(['DID', 'CID']);
		$this->Content = new we_base_model(CONTENT_TABLE, self::$db);
		if(is_array($options)){
			if($link_props){
				$this->fetchLinkedOptions($options);
			} else {
				$this->fetchOptions($options);
			}
		}

		if($link_props){
			$this->linked = true;
			$this->linkProps();
		} else {
			$this->persistent_slots = array_keys($options);
		}
	}

	function fetchOptions($options = []){
		foreach($options as $k => $v){
			if(!is_numeric($k) && property_exists($this, $k)){
				$this->$k = $options[$k];
			}
		}
	}

	function fetchLinkedOptions($options = []){
		if(is_array($options)){
			foreach($options as $k => $v){
				foreach($this->link_attribs as $k => $v){
					if(isset($options[$k]) && isset($this->Link->$k)){
						$this->Link->$k = $options[$k];
					}
				}
				foreach($this->content_attribs as $k => $v){
					if(isset($options[$k]) && isset($this->Content->$k)){
						$this->Content->$k = $options[$k];
					}
				}
			}
		}
	}

	function save(){
		$this->Content->save();
		$this->Link->CID = $this->Content->ID;
		$this->Link->save();
	}

	function load($DID, $Name, $Table){
		$this->Link->setKeys(["DID", "Name", "DocumentTable"]);
		if($this->Link->load("$DID,$Name,$Table")){
			$this->Content->load($this->Link->CID);
			return true;
		}
		return false;
	}

	function linkProps(){

		$this->DID = &$this->Link->DID;
		$this->Name = &$this->Link->Name;
		$this->Type = &$this->Link->Type;

		$this->CID = &$this->Content->CID;
		$this->BDID = &$this->Content->BDID;
		$this->Dat = &$this->Content->Dat;
		$this->LanguageID = &$this->Content->LanguageID;
	}

	function getElement(){
		return ($this->linked ?
			[$this->Name => ["id" => $this->CID,
				"bdid" => $this->BDID,
					"languageid" => $this->LanguageID,
					"cid" => $this->CID,
					"type" => $this->Type,
					"dat" => $this->Dat
					]
			] :
			[$this->Name => ["dat" => $this->Dat,
				"type" => $this->Type,
					"len" => $this->Len
					]
			]
			);
	}

	function getObjectElement(){
		return [$this->Name => ["dat" => base64_decode($this->Dat),
				"type" => $this->Type,
				"len" => $this->Len
				]
		];
	}

}
