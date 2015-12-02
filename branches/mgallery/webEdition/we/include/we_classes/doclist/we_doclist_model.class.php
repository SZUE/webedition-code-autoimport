<?php
/**
 * webEdition CMS
 *
 * $Rev: 10822 $
 * $Author: lukasimhof $
 * $Date: 2015-11-26 22:27:47 +0100 (Do, 26 Nov 2015) $
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

class we_doclist_model{// extends weModelBase{
	/**
	 * @var string: classname
	 */
	public $ModelClassName = __CLASS__;

	public $searchTable;

	protected $whichSearch;

	/**
	 * @var tinyint: flag if the search ist predefined or not
	 */
	public $predefined;

	/**
	 * @var integer: position to start the search
	 */
	public $searchstart = 0;

	/**
	 * @var array: includes the text to search for
	 */
	public $search = array();

	/**
	 * @var array: includes the operators
	 */
	public $location = array();

	/**
	 * @var integer: folder-ids of the docsearch and the tmplsearch
	 */
	public $folderID;

	/**
	 * @var tinyint: flag that shows what view is set in each search
	 */
	public $setView = 0;

	/**
	 * @var int: gives the number of entries in each search for one page
	 */
	public $anzahl = 10;

	/**
	 * @var string: gives the order
	 */
	public $order = 'Text';

	/**
	 * @var array: includes the searchfiels which you are searching in
	 */
	public $searchFields = array();

	/**
	 * Default Constructor
	 * Can load or create new searchtool object depends of parameter
	 */
	function __construct($searchTable, $folderID = 0, $setView = 'list'){
		//as we do actually not save this model to db nor session we do call parent
		//parent::__construct(SUCHE_TABLE);

		$this->searchTable = $searchTable;
		$this->folderID = $folderID;
		$this->setView = $setView;
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

	public function processRequest(){
		$DB_WE = new DB_WE();
		$request = we_base_request::_(we_base_request::STRING, 'we_cmd');

		if(isset($_REQUEST['searchstart']) || isset($request['searchstart'])){
			if(isset($_REQUEST['searchstart'])){
				$_REQUEST['we_cmd']['searchFields' . $this->whichSearch] = $_REQUEST['searchFields' . $this->whichSearch];
				$_REQUEST['we_cmd']['location' . $this->whichSearch] =  $_REQUEST['location' . $this->whichSearch];
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

			$this->searchFields = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->searchFields, 'searchFields' . $this->whichSearch);
			$this->search = array_map('trim', we_base_request::_(we_base_request::STRING, 'we_cmd', $this->search, 'search' . $this->whichSearch));
			$this->location = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->location, 'location' . $this->whichSearch);
			$this->order = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->order, 'order' . $this->whichSearch);
			$this->setView = we_base_request::_(we_base_request::INT, 'we_cmd', $this->setView, 'setView' . $this->whichSearch);
			$this->searchstart = we_base_request::_(we_base_request::INT, 'we_cmd', $this->searchstart, 'searchstart' . $this->whichSearch);
			$this->anzahl = we_base_request::_(we_base_request::INT, 'we_cmd', $this->anzahl, 'anzahl' . $this->whichSearch);
			$this->mode = we_base_request::_(we_base_request::BOOL, 'we_cmd', $this->mode, 'mode');
			$this->mode = true;
			$this->height = count($this->searchFields);
			
			// reindex search arrays
			$this->search = array_merge($this->search);
			$this->searchFields = array_merge($this->searchFields);
			$this->location = array_merge($this->location);
		}

		

	}

	public function getWhichSearch(){
		return $this->whichSearch;
	}

	function load($id = 0){
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