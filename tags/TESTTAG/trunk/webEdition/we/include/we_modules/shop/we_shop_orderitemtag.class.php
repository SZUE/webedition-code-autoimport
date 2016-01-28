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
class we_shop_orderitemtag{//FIXME: remove in 6.6
	private $DB_WE;
	var $id = 0;
	var $ClassName = __CLASS__;
	private $object;
	var $avail = false;
	var $hidedirindex = false;

	function __construct($id = 0, $condition = "", $hidedirindex = false){
		$this->id = $id;
		if(!$this->id){
			return;
		}

		$this->DB_WE = new DB_WE();
		$this->hidedirindex = $hidedirindex;

		$this->object = new we_shop_listviewOrderitem(0, 1, 0, "", 0, '(IntID=' . intval($this->id) . ')' . ($condition ? ' AND ' . $condition : ''), '', 0, 0, $hidedirindex);
		$this->avail = ($this->object->next_record());
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

}
