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
	var $ID = 0;
	var $DID = 0;
	var $Name = '';
	var $nHash = '';
	var $Type = '';
	var $BDID = 0;
	var $Dat = '';
	var $LangugeID = 0;
	var $Len = 0;
	var $persistent_slots = [];
	var $Content;
	static $db = '';

	public function __construct($options = []){
		if(!is_object(self::$db)){
			self::$db = new DB_WE();
		}

		$this->Content = new we_base_model(CONTENT_TABLE, self::$db);
		if(is_array($options)){
			$this->fetchOptions($options);
		}
		$this->nHash = md5($this->Name);
		$this->persistent_slots = array_keys($options);
	}

	private function fetchOptions($options = []){
		foreach($options as $k => $v){
			if(property_exists($this, $k)){
				$this->$k = $v;
			}
		}
	}

	function save(){
		$this->Content->save();
	}

	function load($DID, $Name, $Table){
		t_e('FIXME: called, why, where, fix this');
		if($this->Content->load($this->ID)){
			return true;
		}
		return false;
	}

	function getElement(){
		return [
			$this->Name => [
				'dat' => $this->Dat,
				'type' => $this->Type,
				'len' => $this->Len
			]
		];
	}

}
