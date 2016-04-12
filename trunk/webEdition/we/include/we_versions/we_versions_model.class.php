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
 * class representing the model data of the search
 */
require_once (WE_INCLUDES_PATH . 'we_tools/weSearch/conf/define.conf.php');

class we_versions_model extends we_search_modelBase{
	//public $ModelClassName = __CLASS__;
	public $whichSearch;
	public $height = 0;
	public $transaction = '';
	public $predefined;

	/**
	 * @var integer: position to start the search
	 */
	protected $searchstartVersionSearch = 0;

	/**
	 * @var array: includes the text to search for
	 */
	protected $searchVersionSearch = array();

	/**
	 * @var array: includes the operators
	 */
	protected $locationVersionSearch = array();

	/**
	 * @var integer: folder-ids of the docsearch and the tmplsearch
	 */
	protected $folderIDVersion = 0;

	/**
	 * @var tinyint: flag that shows what view is set in each search
	 */
	protected $setViewVersionSearch = 'list';

	/**
	 * @var int: gives the number of entries in each search for one page
	 */
	protected $anzahlVersionSearch = 10;

	/**
	 * @var string: gives the order
	 */
	protected $OrderVersionSearch = 'ID';
	protected $searchTablesVersionSearch = array();

	/**
	 * @var array: includes the searchfiels which you are searching in
	 */
	protected $searchFieldsVersionSearch = array();

	/**
	 * Default Constructor
	 * Can load or create new searchtool object depends of parameter
	 */
	function __construct($transaction, $setView = 'list'){
		//as we do actually not save this model to db nor session we do call parent
		//parent::__construct(SUCHE_TABLE);

		$this->transaction = $transaction;
		$this->searchTablesVersionSearch = array(VERSIONS_TABLE);
		$this->whichSearch = we_search_view::SEARCH_VERSION;
	}

	public function initByHttp($whichSearch = '', $isWeCmd = true){
		// IMPORTANT: this is the ONLY place where model vars are set!
		if(isset($_REQUEST['searchstart'])){
			$this->mode = we_base_request::_(we_base_request::INT, "mode", $this->mode);
			$this->OrderVersionSearch = we_base_request::_(we_base_request::STRING, "order", $this->OrderVersionSearch);
			$this->anzahlVersionSearch = we_base_request::_(we_base_request::INT, "anzahl", $this->anzahlVersionSearch);
			$this->searchstartVersionSearch = we_base_request::_(we_base_request::STRING, "searchstart", $this->searchstartVersionSearch);
			$this->searchFieldsVersionSearch = we_base_request::_(we_base_request::STRING, "searchFields");
			$this->height = count($this->searchstartVersionSearch);
			$this->locationVersionSearch = we_base_request::_(we_base_request::STRING, "location", $this->locationVersionSearch);
			$this->searchVersionSearch = we_base_request::_(we_base_request::STRING, "search", $this->searchVersionSearch);
		} else {
			if(isset($_REQUEST['we_cmd']['searchstart'])){
				$request = we_base_request::_(we_base_request::STRING, 'we_cmd');
				foreach($request as $k => $v){
					if(stristr($k, 'searchFields[') && !stristr($k, 'hidden_')){
						$_REQUEST['we_cmd']['searchFields'][] = $v;
						continue;
					}
					if(stristr($k, 'location[')){
						$_REQUEST['we_cmd']['location'][] = $v;
						continue;
					}
					if(stristr($k, 'search[')){
						$_REQUEST['we_cmd']['search'][] = $v;
					}
				}

				$this->mode = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->mode, 'mode');
				$this->OrderVersionSearch = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->OrderVersionSearch, 'order');
				$this->searchstartVersionSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->searchstartVersionSearch, 'searchstart');
				$this->anzahlVersionSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->anzahlVersionSearch, 'anzahl');
				$this->searchFieldsVersionSearch = we_base_request::_(we_base_request::STRING, 'we_cmd', array(), 'searchFields');
				$this->height = count($this->searchFieldsVersionSearch);
				$this->searchVersionSearch = array_map('trim', we_base_request::_(we_base_request::STRING, 'we_cmd', array(), 'search'));
				$this->locationVersionSearch = we_base_request::_(we_base_request::STRING, 'we_cmd', array(), 'location');
			}
		}

		$this->searchFieldsVersionSearch = ($this->searchFieldsVersionSearch);
		$this->locationVersionSearch = ($this->locationVersionSearch);
		$this->searchVersionSearch = ($this->searchVersionSearch);

		$this->prepareModelForSearch();
	}

	public function prepareModelForSearch(){
		$this->currentSearchTables = $this->searchTablesVersionSearch;
		$this->currentSearchFields = $this->searchFieldsVersionSearch;
		$this->currentLocation = $this->locationVersionSearch;
		$this->currentSearch = $this->searchVersionSearch;
		$this->currentFolderID = $this->folderIDVersion;
		$this->currentOrder = $this->OrderVersionSearch;
		$this->currentSetView = $this->setViewVersionSearch;
		$this->currentSearchstart = $this->searchstartVersionSearch;
		$this->currentAnzahl = $this->anzahlVersionSearch;
	}

	public function getWhichSearch(){
		return $this->whichSearch;
	}

	function load($id = 0, $isAdvanced = false){
		parent::load($id);
		// we could save doclist searches to some new table
	}

	function saveInSession(){
		// doclist models are actually saved to session as property of folders
		//$_SESSION['weS'][$this->toolName . '_session'] = $this;
	}

	function clearSessionVars(){
		/*
		  if(!empty($this->toolName) && isset($_SESSION['weS'][$this->toolName . '_session'])){
		  unset($_SESSION['weS'][$this->toolName . '_session']);
		  }
		 *
		 */
	}

}
