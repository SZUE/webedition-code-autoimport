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

	public $whichSearch;

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
	public $setView = 'list';

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
	
	public $mode = 0;
	public $height = 0;
	public $transaction = '';

	/**
	 * Default Constructor
	 * Can load or create new searchtool object depends of parameter
	 */
	function __construct($transaction, $searchTable, $folderID = 0, $setView = 'list'){
		//as we do actually not save this model to db nor session we do call parent
		//parent::__construct(SUCHE_TABLE);

		$this->transaction = $transaction;
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
		// IMPORTANT: this is the ONLY place where model vars are set!
		$DB_WE = new DB_WE();
		$request = we_base_request::_(we_base_request::STRING, 'we_cmd');

		if(isset($_REQUEST['searchstart' . $this->whichSearch]) || isset($request['searchstart' . $this->whichSearch])){
			if(isset($_REQUEST['searchFields' . $this->whichSearch])){
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
			$this->searchFields = !$this->mode ? array('Content') : we_base_request::_(we_base_request::STRING, 'we_cmd', array('Content'), 'searchFields' . $this->whichSearch);
			$this->search = !$this->mode ? array('') : array_map('trim', we_base_request::_(we_base_request::STRING, 'we_cmd', array(''), 'search' . $this->whichSearch));
			$this->location = !$this->mode ? array('') : we_base_request::_(we_base_request::STRING, 'we_cmd', array(''), 'location' . $this->whichSearch);

			$this->order = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->order, 'Order' . $this->whichSearch);
			$this->setView = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->setView, 'setView' . $this->whichSearch);
			$this->searchstart = we_base_request::_(we_base_request::INT, 'we_cmd', $this->searchstart, 'searchstart' . $this->whichSearch);
			$this->anzahl = we_base_request::_(we_base_request::INT, 'we_cmd', $this->anzahl, 'anzahl' . $this->whichSearch);
			$this->height = count($this->searchFields);

			// reindex search arrays
			$this->search = array_merge($this->search);
			$this->searchFields = array_merge($this->searchFields);
			$this->location = array_merge($this->location);
			//t_e('model updatet', $this);
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