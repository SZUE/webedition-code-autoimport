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
class we_object_tag{//FIXME: check why we use class/id instead of classID/ID => causes unneeded differentiation in e.g. we:form
	public $DB_WE; //FIXME: change this to private!
	var $class = '';
	var $id = 0;
	var $triggerID = 0;
	var $ClassName = __CLASS__;
	private $object;
	var $avail = false;
	var $hidedirindex = false;
	var $objectseourls = false;

	function __construct($class = '', $id = 0, $triggerID = 0, $searchable = true, $condition = '', $hidedirindex = false, $objectseourls = false){
		$this->id = $id;
		if(!$this->id && ($oid = we_base_request::_(we_base_request::INT, 'we_objectID'))){
			$this->id = $oid;
		}
		if(!$this->id){
			return;
		}
		$this->DB_WE = new DB_WE();
		$this->class = $class;
		$this->hidedirindex = $hidedirindex;
		$this->objectseourls = $objectseourls;

		$this->triggerID = $triggerID;
		$unique = md5(uniqid(__FUNCTION__, true));

		$foo = f('SELECT TableID FROM ' . OBJECT_FILES_TABLE . ' WHERE IsFolder=0 AND ID=' . intval($this->id), '', $this->DB_WE);
		if(!$foo){
			return;
		}
		$this->object = new we_object_listview($unique, 1, 0, '', 0, $foo, '', '', '(' . OBJECT_X_TABLE . $foo . '.OF_ID="' . intval($this->id) . '")' . ($condition ? ' AND ' . $condition : ''), $this->triggerID, '', '', $searchable, '', '', '', '', '', '', '', 0, '', '', '', '', $hidedirindex, $objectseourls);
		$this->avail = $this->object->next_record();
	}

	public function getID(){
		return $this->id;
	}

	public function getDBf($key){
		return ($this->id ? $this->object->getDBf($key) : '');
	}

	public function f($key){
		return ($this->id ? $this->object->f($key) : '');
	}

	public function getObject(){//FIXME: remove this
		return $this->object;
	}

	public function getDB(){
		return $this->DB_WE;
	}

	public function getDBRecord(){
		return ($this->id ? $this->object->getDBRecord() : array());
	}
}
