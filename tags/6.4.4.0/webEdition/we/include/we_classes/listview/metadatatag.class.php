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
class metadatatag{//FIXME: remove in 6.6
	private $DB_WE;
	var $ClassName = __CLASS__;
	private $object;
	var $avail = false;
	var $id = 0;

	function __construct($name){
		$this->DB_WE = new DB_WE();

		if($name){
			$unique = md5(uniqid(__FILE__, true));
			$_value = (isset($GLOBALS["lv"]) ?
					$GLOBALS["lv"]->f($name) :
					// determine the id of the element
					($GLOBALS['we_doc']->getElement($name, 'bdid')? :
						$GLOBALS['we_doc']->getElement($name)
					)
				);

			// it is an id
			$this->id = (is_numeric($_value) ? $_value : 0);
			if(!$this->id){
				return;
			}
			$this->object = new we_listview_document($unique, 1, 0, "", false, "", "", false, false, 0, "", "", false, "", "", "", "", "", "", "off", true, "", $this->id, '', false, false, 0);
			$this->avail = ($this->object->next_record());
		}
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
