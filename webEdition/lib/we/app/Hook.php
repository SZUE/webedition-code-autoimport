<?php

/**
 * webEdition SDK
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * class for loading and executing code execution hooks for webEdition applications
 *
 * @category   we
 * @package none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_app_Hook{

	/**
	 * @var array for storing all application code hooks that have already been read.
	 */
	protected $_hooks = [];

	/**
	 * @var array for storing all reader objects that are used by this hook object instance
	 */
	protected $_readers = [];

	/**
	 * constructor method for hook parser
	 * @param we_app_Reader_* reference to reader object, i.e. we_app_Reader_Xml
	 */
	public function __construct(&$reader = null){
		t_e('notice',"creating hook object");
		if(!is_null($reader)){
			$this->addReader($reader);
			t_e('notice',"adding hook reader object of type " . get_class($reader));
		}
	}

	/**
	 * getter method
	 * @example $myHook->preInstallHook->code
	 * @return requested hook properties of object $this->_hooks or false on failure
	 */
	public function __get($property = ""){
		return (empty($property) || empty($this->_hooks) ?
				false :
				(isset($this->_hooks->$property) ?
					false : $this->_hooks->$property));
	}

	/**
	 * adds a reader for fetching hooks (i.e. we_app_Reader_Xml)
	 * @return bool true (success) or false (failure, i.e. no or invalid reader)
	 */
	public function addReader(&$reader = null){
		if(is_null($reader)){
			return false;
		}
		error_log("adding hook reader object of type " . get_class($reader));
	}

	public function run($hook = ""){

	}

	/**
	 * loads all hooks from all registered readers
	 */
	public function loadAll(){
		// not implemented yet
	}

}
