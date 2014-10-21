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

class we_sdk_namespace implements ArrayAccess{

	private $container = null;

	public function __construct($name){
		if(!isset($_SESSION['weS']['apps'][$name])){
			$_SESSION['weS']['apps'][$name] = array();
		}
		$this->container = &$_SESSION['weS']['apps'][$name];
	}

	public function offsetExists($offset){
		return isset($this->container[$offset]);
	}

	public function offsetGet($offset){
		return isset($this->container[$offset]) ? $this->container[$offset] : null;
	}

	public function offsetSet($offset, $value){
		if(is_null($offset)){
			$this->container[] = $value;
		} else {
			$this->container[$offset] = $value;
		}
	}

	public function offsetUnset($offset){
		unset($this->container[$offset]);
	}

	public function __get($offset){
		return isset($this->container[$offset]) ? $this->container[$offset] : null;
	}

	public function __set($offset, $value){
		$this->container[$offset] = $value;
	}

	public function __isset($offset){
		return isset($this->container[$offset]);
	}

	public function __unset($offset){
		unset($this->container[$offset]);
	}

}