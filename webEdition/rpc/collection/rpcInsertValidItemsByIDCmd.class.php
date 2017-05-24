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

include_once(dirname(__FILE__) . '/rpcGetValidItemsByIDCmd.class.php');

class rpcInsertValidItemsByIDCmd extends rpcGetValidItemsByIDCmd{
	protected $position = -1;

	function __construct(){
		$this->position = we_base_request::_(we_base_request::INT, 'we_cmd', -1, 'position');
		parent::__construct();
	}

	protected function initByRequest(){
		parent::initByRequest();
	}

	function execute(){
		$validItems = $this->getValidItems();

		if(($validItems = $this->getValidItems())){
			$result = $this->collection->addItemsToCollection($validItems, $this->position);
			if($this->initSessDat){
				$this->collection->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);
			} else {
				$this->collection->save();
			}

			$this->resp->setData('items', $result);
		} else { }

		return $this->resp;
	}

}