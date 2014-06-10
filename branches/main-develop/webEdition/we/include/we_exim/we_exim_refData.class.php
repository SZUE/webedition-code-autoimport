<?php

/**
 * webEdition CMS
 *
 * $Rev: 7121 $
 * $Author: mokraemer $
 * $Date: 2013-12-10 22:47:43 +0100 (Di, 10. Dez 2013) $
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
class we_exim_refData{

	var $ID;
	var $ParentID;
	var $TemplateID;
	var $Table;
	var $Path;
	var $ContentType;
	var $DocType;
	var $Category;
	var $OldID;
	var $OldParentID;
	var $OldPath;
	var $OldTemplatePath;
	public $OldDocTypeName;
	var $Examined = 0;
	var $elements = 0;
	var $slots = array("ID", "ParentID", "Path", "Table", "ContentType", "TemplateID", "DocType", "Category");

	function init($object, $extra = array()){
		foreach($this->slots as $slot){
			if(isset($object->$slot)){
				$this->$slot = $object->$slot;
			}
		}
		foreach($extra as $ek => $ev){
			$this->$ek = $ev;
		}
	}

	function match($param){
		foreach($param as $k => $v){
			if($k != 'level' && $this->$k != $v){
				return false;
			}
		}
		return true;
	}

}
