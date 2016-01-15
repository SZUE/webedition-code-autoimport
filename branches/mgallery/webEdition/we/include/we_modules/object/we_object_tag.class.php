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
 * @deprecated since version 6.4.1
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class we_object_tag{//FIXME: remove in 6.6
//FIXME: check why we use class/id instead of classID/ID => causes unneeded differentiation in e.g. we:form
	public $DB_WE; //FIXME: change this to private in 6.5 Alpha 1!
	var $class = '';
	var $id = 0;
	var $triggerID = 0;
	var $ClassName = __CLASS__;
	public $object; //FIXME: change this to private in 6.5 Alpha 1!
	public $classID = 0;
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

		$this->classID = f('SELECT TableID FROM ' . OBJECT_FILES_TABLE . ' WHERE IsFolder=0 AND ID=' . intval($this->id), '', $this->DB_WE);
		if(!$this->classID){
			return;
		}
		$this->object = new we_listview_object($unique, 1, 0, '', 0, $this->classID, '', '', '(' . OBJECT_X_TABLE . $this->classID . '.OF_ID="' . intval($this->id) . '")' . ($condition ? ' AND ' . $condition : ''), $this->triggerID, '', '', $searchable, '', '', '', '', '', '', '', 0, '', '', '', '', $hidedirindex, $objectseourls);
		$this->avail = $this->object->next_record();
	}

	/**
	 *
	 * @deprecate since 6.4.0
	 */
	public function getID(){
		return $this->id;
	}

	/**
	 *
	 * @deprecate since 6.4.0
	 */
	public function getDBf($key){
		return ($this->id ? $this->object->getDBf($key) : '');
	}

	public function f($key){
		return ($this->id ? $this->object->f($key) : '');
	}

	/**
	 *
	 * @deprecate since 6.4.0
	 */
	public function getDB(){
		return $this->DB_WE;
	}

	/**
	 *
	 * @deprecate since 6.4.0
	 */
	public function getDBRecord(){
		return ($this->id ? $this->object->getDBRecord() : array());
	}

}
