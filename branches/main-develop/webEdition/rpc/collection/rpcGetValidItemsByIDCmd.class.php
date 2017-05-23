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
class rpcGetValidItemsByIDCmd extends we_rpc_cmd{
	protected $resp = null;
	protected $collection = null;
	protected $collectionID = 0;
	protected $IDs = [];
	protected $transaction = '';
	protected $full = false;
	protected $recursive = true;
	protected $initSessDat = false;

	function __construct(){
		$this->resp = new we_rpc_response();
		$this->initByRequest();
	}

	protected function initByRequest(){
		if(!($this->IDs = we_base_request::_(we_base_request::INTLISTA, 'we_cmd', [], 'ids'))){
			$this->resp->setData("error", ["Missing field id"]);
			$this->resp->setStatus(false);
			return;
		}

		$this->resp->setData('index', we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'index'));
		$this->resp->setData('notReplace', we_base_request::_(we_base_request::BOOL, 'we_cmd', true, 'notReplace'));
		$this->resp->setData('message', we_base_request::_(we_base_request::STRING, 'we_cmd', true, 'message'));

		// TODO: only save as prop when really needed!
		$this->transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', '', 'transaction');
		$this->collectionID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'collection');
		$this->full = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 'full');
		$this->recursive = we_base_request::_(we_base_request::BOOL, 'we_cmd', true, 'recursive');

		$this->collection = new we_collection();
		if($this->transaction && (isset($_SESSION['weS']['we_data'][$this->transaction]))){
			$this->collection->we_initSessDat($_SESSION['weS']['we_data'][$this->transaction]);
			$this->initSessDat = true;
		} else if($this->collectionID){
			$this->collection->initByID($this->collectionID);
		} else {
			$this->resp->setData("error", ["no collection error"]);
			$this->resp->setStatus(false);

			return;
		}
	}

	protected function getValidItems(){
		return $this->collection->getValidItemsFromIDs($this->IDs, $this->full, $this->recursive);
	}

	function execute(){
		if(!$this->resp->getStatus()){
			return $this->resp;
		}

		$this->resp->setData('items', $this->getValidItems());

		return $this->resp;
	}

}
