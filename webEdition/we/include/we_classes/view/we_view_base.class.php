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
 * Baseclass for all UI relevant functions
 * all functions not found in extended classes will be redirected to their corresponding document which holds the data
 */
abstract class we_view_base{
	protected $doc;

	public function __call($method_name, $params){
		return call_user_func_array([$this->doc, $method_name], $params);
	}

	public function __get($name){
		return $this->doc->$name;
	}

	public function __set($name, $value){
		$this->doc->$name = $value;
	}

	public function __isset($name){
		return isset($this->doc->$name);
	}

}
