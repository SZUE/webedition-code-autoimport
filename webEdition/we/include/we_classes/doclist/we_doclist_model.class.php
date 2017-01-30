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
class we_doclist_model extends we_search_modelBase{
	//public $ModelClassName = __CLASS__;
	public $whichSearch;
	public $height = 0;
	public $transaction = '';
	public $predefined;

	/**
	 * @var integer: position to start the search
	 */
	protected $searchstartDoclistSearch = 0;

	/**
	 * @var array: includes the text to search for
	 */
	protected $searchDoclistSearch = [];

	/**
	 * @var array: includes the operators
	 */
	protected $locationDoclistSearch = [];

	/**
	 * @var integer: folder-ids of the docsearch and the tmplsearch
	 */
	protected $folderIDDoclist;

	/**
	 * @var tinyint: flag that shows what view is set in each search
	 */
	protected $setViewDoclistSearch = 'list';

	/**
	 * @var int: gives the number of entries in each search for one page
	 */
	protected $anzahlDoclistSearch = 10;

	/**
	 * @var string: gives the order
	 */
	protected $OrderDoclistSearch = 'Text';
	protected $searchTablesDoclistSearch = [];

	/**
	 * @var array: includes the searchfiels which you are searching in
	 */
	protected $searchFieldsDoclistSearch = [];

	/**
	 * Default Constructor
	 * Can load or create new searchtool object depends of parameter
	 */
	function __construct($transaction, $searchTable, $folderID = 0, $setView = 'list'){
		//as we do actually not save this model to db nor session we do call parent
		//parent::__construct(SEARCH_TABLE);

		$this->transaction = $transaction;
		$this->searchTablesDoclistSearch = [$searchTable];
		$this->folderIDDoclist = $folderID;
		$this->setViewDoclistSearch = $setView;
		$this->whichSearch = we_search_view::SEARCH_DOCLIST;

		/*
		  if($weSearchID){
		  $this->ID = $weSearchID;
		  $this->load($weSearchID);
		  } else {
		  $this->ID = 0;
		  }
		 * */
	}

	public function initByHttp($whichSearch = '', $isWeCmd = true){
		// IMPORTANT: this is the ONLY place where model vars are set!
		$request = we_base_request::_(we_base_request::STRING, 'we_cmd');

		if(isset($_REQUEST['searchstart' . $this->whichSearch]) || isset($request['searchstart' . $this->whichSearch])){
			if(isset($_REQUEST['searchFields' . $this->whichSearch])){
				$_REQUEST['we_cmd']['searchFields' . $this->whichSearch] = $_REQUEST['searchFields' . $this->whichSearch];
				$_REQUEST['we_cmd']['location' . $this->whichSearch] = isset($_REQUEST['location' . $this->whichSearch]) ? $_REQUEST['location' . $this->whichSearch] : [];
				$_REQUEST['we_cmd']['search' . $this->whichSearch] = $_REQUEST['search' . $this->whichSearch];
			} else {
				foreach($request as $k => $v){
					if(stristr($k, 'searchFields' . $this->whichSearch . '[') && !stristr($k, 'hidden_')){
						$_REQUEST['we_cmd']['searchFields' . $this->whichSearch][] = $v;
						continue;
					}
					if(stristr($k, 'location' . $this->whichSearch . '[')){
						$_REQUEST['we_cmd']['location' . $this->whichSearch][] = $v;
						continue;
					}
					if(stristr($k, 'search' . $this->whichSearch . '[')){
						$_REQUEST['we_cmd']['search' . $this->whichSearch][] = $v;
					}
				}
			}

			// FIXME: unify the different ways these params are committed
			if(isset($_REQUEST['searchstart' . $this->whichSearch])){
				$_REQUEST['we_cmd']['Order' . $this->whichSearch] = $_REQUEST['Order' . $this->whichSearch];
				$_REQUEST['we_cmd']['setView' . $this->whichSearch] = $_REQUEST['setView' . $this->whichSearch];
				$_REQUEST['we_cmd']['searchstart' . $this->whichSearch] = $_REQUEST['searchstart' . $this->whichSearch];
				$_REQUEST['we_cmd']['anzahl' . $this->whichSearch] = $_REQUEST['anzahl' . $this->whichSearch];
				$_REQUEST['we_cmd']['mode'] = $_REQUEST['mode'];
			}

			$this->mode = we_base_request::_(we_base_request::INT, 'we_cmd', $this->mode, 'mode');

			// searchfield's default is an empty 'Content' dearch (not having any impact on the result)
			$this->searchFieldsDoclistSearch = !$this->mode ? ['Content'] : we_base_request::_(we_base_request::STRING, 'we_cmd', ['Content'], 'searchFields' . $this->whichSearch);
			$this->searchDoclistSearch = !$this->mode ? [''] : array_map('trim', we_base_request::_(we_base_request::STRING, 'we_cmd', [''], 'search' . $this->whichSearch));
			$this->locationDoclistSearch = !$this->mode ? [''] : we_base_request::_(we_base_request::STRING, 'we_cmd', [''], 'location' . $this->whichSearch);

			$this->OrderDoclistSearch = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->OrderDoclistSearch, 'Order' . $this->whichSearch);
			$this->setViewDoclistSearch = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->setViewDoclistSearch, 'setView' . $this->whichSearch);
			$this->searchstartDoclistSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->searchstartDoclistSearch, 'searchstart' . $this->whichSearch);
			$this->anzahlDoclistSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->anzahlDoclistSearch, 'anzahl' . $this->whichSearch);
			$this->height = count($this->searchFieldsDoclistSearch);

			// reindex search arrays
			$this->searchDoclistSearch = array_merge($this->searchDoclistSearch);
			$this->searchFieldsDoclistSearch = array_merge($this->searchFieldsDoclistSearch);
			$this->locationDoclistSearch = array_merge($this->locationDoclistSearch);
		}

		$this->prepareModelForSearch();
	}

	public function prepareModelForSearch(){
		$this->currentSearchTables = $this->searchTablesDoclistSearch;
		$this->currentSearchFields = $this->searchFieldsDoclistSearch;
		$this->currentLocation = $this->locationDoclistSearch;
		$this->currentSearch = $this->searchDoclistSearch;
		$this->currentFolderID = $this->folderIDDoclist;
		$this->currentOrder = $this->OrderDoclistSearch;
		$this->currentSetView = $this->setViewDoclistSearch;
		$this->currentSearchstart = $this->searchstartDoclistSearch;
		$this->currentAnzahl = $this->anzahlDoclistSearch;
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
