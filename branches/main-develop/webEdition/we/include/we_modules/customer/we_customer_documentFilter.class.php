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
 * Customer filter (model) for document (or object) filters
 *
 */
class we_customer_documentFilter extends we_customer_abstractFilter{

	const ACCESS = 'f_1';
	const CONTROLONTEMPLATE = 'f_2';
	const NO_ACCESS = 'f_3';
	const NO_LOGIN = 'f_4';

	/**
	 * Id of model (document or object)
	 *
	 * @var integer
	 */
	private $_modelId = 0;

	/**
	 * DocumentType of model (eg. text/webEdition)
	 *
	 * @var string
	 */
	private $_modelType = '';

	/**
	 * Table where model is stored in db (eg. FILE_TABLE)
	 *
	 * @var string
	 */
	private $_modelTable = '';

	/**
	 * Flag if access control is made by template or not
	 *
	 * @var boolean
	 */
	private $_accessControlOnTemplate = false;

	/**
	 * Id of Document which is shown when customer is not logged in
	 *
	 * @var boolean
	 */
	private $_errorDocNoLogin = 0;

	/**
	 * Id of Document which is shown when customer has no acces
	 *
	 * @var boolean
	 */
	private $_errorDocNoAccess = 0;

	/**
	 * Constructor for PHP 5
	 *
	 * @param integer $id
	 * @param integer $modelId
	 * @param string $modelType
	 * @param string $modelTable
	 * @param boolean $accessControlOnTemplate
	 * @param integer $errorDocNoLogin
	 * @param integer $errorDocNoAccess
	 * @param integer $mode
	 * @param array $specificCustomers
	 * @param array $filter
	 * @param array $whiteList
	 * @param array $blackList
	 * @return we_customer_documentFilter
	 */
	public function __construct($modelId = 0, $modelType = '', $modelTable = '', $accessControlOnTemplate = true, $errorDocNoLogin = 0, $errorDocNoAccess = 0, $mode = we_customer_abstractFilter::OFF, array $specificCustomers = array(), array $filter = array(), array $whiteList = array(), array $blackList = array()){
		parent::__construct($mode, $specificCustomers, $blackList, $whiteList, $filter);

		$this->setModelId($modelId);
		$this->setModelType($modelType);
		$this->setModelTable($modelTable);
		$this->setAccessControlOnTemplate($accessControlOnTemplate);
		$this->setErrorDocNoLogin($errorDocNoLogin);
		$this->setErrorDocNoAccess($errorDocNoAccess);
	}

	/**
	 * initializes and returns filter object from db object. Called after $db->query();
	 *
	 * @param we_db $db
	 * @return we_customer_documentFilter
	 */
	private static function getFilterByDbHash($hash){
		$f = we_unserialize($hash['filter']);
		return new self(intval($hash['modelId']), $hash['modelType'], $hash['modelTable'], intval($hash['accessControlOnTemplate']), intval($hash['errorDocNoLogin']), intval($hash['errorDocNoAccess']), intval($hash['mode']), makeArrayFromCSV($hash['specificCustomers']), is_array($f) ? $f : array(), makeArrayFromCSV($hash['whiteList']), makeArrayFromCSV($hash['blackList']));
	}

	/**
	 * initializes and returns filter object from request
	 *
	 * param webeditionDocument or objectFile
	 * @param mixed $model
	 * @return we_customer_documentFilter
	 */
	static function getCustomerFilterFromRequest($id, $ct, $table){
		return
				(we_base_request::_(we_base_request::INT, 'wecf_mode') === we_customer_abstractFilter::OFF ?
						self::getEmptyDocumentCustomerFilter() :
						new self(intval($id), $ct, $table, ((we_base_request::_(we_base_request::STRING, 'wecf_accessControlOnTemplate') === "onTemplate") ? 1 : 0), we_base_request::_(we_base_request::INT, 'wecf_noLoginId', 0), we_base_request::_(we_base_request::INT, 'wecf_noAccessId', 0), we_base_request::_(we_base_request::INT, 'wecf_mode', 0), self::getSpecificCustomersFromRequest(), self::getFilterFromRequest(), self::getWhiteListFromRequest(), self::getBlackListFromRequest()
						)
				);
	}

	/**
	 * initializes and returns filter object from model
	 *
	 * @param mixed $model
	 * @return we_customer_documentFilter
	 */
	public static function getFilterOfDocument($model, we_database_base $db = null){
		return self::getFilterByIdAndTable($model->ID, $model->Table, $db);
	}

	/**
	 * initializes and returns filter object
	 *
	 * @param integer $id
	 * @param string $contentType
	 * @return we_customer_documentFilter
	 */
	public static function getFilterByIdAndTable($id, $table, we_database_base $db = null){
		$db = ($db ? : new DB_WE());
		$hash = getHash('SELECT * FROM ' . CUSTOMER_FILTER_TABLE . ' WHERE modelTable="' . $db->escape(stripTblPrefix($table)) . '" AND modelId=' . intval($id), $db);
		return ($hash ?
						self::getFilterByDbHash($hash) :
						''); // important do NOT return null
	}

	/**
	 * get additional condition for listviews
	 *
	 * @return string
	 */
	function getConditionForListviewQuery($filter, we_listview_base $obj, $classID = 0, $ids = ''){
		if($filter === 'off' || $filter === 'false' || $filter === false || $filter === 'all' || $filter === ''){
			return '';
		}
		if(!self::customerIsLogedIn()){ //we don't show any documents with an customerfilter
			if($obj instanceof we_listview_search){ // search
				//FIXME: changed tblINDEX!
				return ' AND ( (i.ClassID=0 AND i.ID NOT IN(SELECT modelId FROM ' . CUSTOMER_FILTER_TABLE . ' WHERE modelTable="' . stripTblPrefix(FILE_TABLE) . '"))' .
						' OR ( i.ClassID>0 AND i.ID NOT IN(SELECT modelId FROM ' . CUSTOMER_FILTER_TABLE . ' WHERE modelTable="' . stripTblPrefix(OBJECT_FILES_TABLE) . '")) )';
			}
			return ($classID ?
							' AND ' . OBJECT_X_TABLE . $classID . '.OF_ID NOT IN(SELECT modelId FROM ' . CUSTOMER_FILTER_TABLE . ' WHERE modelTable="' . stripTblPrefix(OBJECT_FILES_TABLE) . '")' :
							' AND ' . FILE_TABLE . '.ID NOT IN(SELECT modelId FROM ' . CUSTOMER_FILTER_TABLE . ' WHERE modelTable="' . stripTblPrefix(FILE_TABLE) . '")'
					);
		}

		// if customer is not logged in, all documents/objects with filters must be hidden
		$_restrictedFilesForCustomer = self::_getFilesWithRestrictionsOfCustomer($obj, $filter, $classID, $ids);

		if($obj instanceof we_listview_search){ // search
			$_queryTail = array();
			// build query from restricted files, regard search and normal listview
			foreach($_restrictedFilesForCustomer as $tab => $_fileArray){
				if($_fileArray){
					$_queryTail [] = '(' . ($tab === 'tblObjectFiles' ? ' ClassID>0' : 'ClassID=0') . ' AND  ID NOT IN(' . implode(', ', $_fileArray) . '))';
				}
			}
			return ' AND ' . implode(' OR ', $_queryTail);
		}
		$_queryTail = '';
		$_fileArray = array();
		// build query from restricted files, regard search and normal listview
		foreach($_restrictedFilesForCustomer as $tab => $_fileArray){
			if($_fileArray){
				$_idField = ($classID && $tab === 'tblObjectFiles' ?
								OBJECT_X_TABLE . $classID . '.OF_ID' :
								FILE_TABLE . '.ID');
				$_queryTail .= ' AND ' . $_idField . ' NOT IN(' . implode(', ', $_fileArray) . ')';
			}
		}

		return $_queryTail;
	}

	/**
	 * returns empty filter object
	 *
	 * @return we_customer_documentFilter
	 */
	static function getEmptyDocumentCustomerFilter(){
		return new self();
	}

	/**
	 * compares two filters and returns true if they have equal data
	 *
	 * @param we_customer_documentFilter $filter1
	 * @param we_customer_documentFilter $filter2
	 * @param boolean $applyCheck if also model data should be compared
	 * @static
	 * @return boolean
	 */
	function filterAreQual($filter1 = '', $filter2 = '', $applyCheck = false){
		$filter1 = $filter1? : self::getEmptyDocumentCustomerFilter();
		$filter2 = $filter2? : self::getEmptyDocumentCustomerFilter();

		$checkFields = array('modelTable', 'accessControlOnTemplate', 'errorDocNoLogin', 'errorDocNoAccess', 'mode', 'specificCustomers', 'filter', 'whiteList', 'blackList');
		if(!$applyCheck){
			$checkFields[] = 'modelId';
			$checkFields[] = 'modelType';
		}

		for($i = 0; $i < count($checkFields); $i++){
			$_fn = 'get' . ucfirst($checkFields[$i]);
			if($filter1->$_fn($i) != $filter2->$_fn($i)){
				return false;
			}
		}
		return true;
	}

	/**
	 * gets the right error document id
	 *
	 * @param String $errorConstant
	 * @return integer
	 */
	function getErrorDoc($errorConstant){

		$_ret = 0;
		switch($errorConstant){

			case self::NO_LOGIN:
				$_ret = ($this->_errorDocNoLogin ? : $this->_errorDocNoAccess);
				break;

			case self::NO_ACCESS:
				$_ret = ($this->_errorDocNoAccess ? : $this->_errorDocNoLogin);
				break;
			default:
				break;
		}
		return $_ret;
	}

	/**
	 * saves the filter data in db. Call this on save method of model
	 *
	 * param webeditionDocument or objectFile
	 * @param mixed $model
	 */
	function saveForModel(&$model){
		$_db = new DB_WE();

		// check if there were any changes?
		$_docCustomerFilter = $model->documentCustomerFilter; // filter of document
		$_tmp = self::getFilterByIdAndTable($model->ID, $model->Table, $_db); // filter stored in Database

		if(self::filterAreQual($_docCustomerFilter, $_tmp)){
			return;
		}// the filter changed
		if($_docCustomerFilter->getMode() == we_customer_abstractFilter::OFF){
			self::deleteForModel($model, $_db);
			return;
		}
		if($model->ID){ // only save if its is active
			$_filter = $_docCustomerFilter->getFilter();
			$_specificCustomers = $_docCustomerFilter->getSpecificCustomers();
			$_blackList = $_docCustomerFilter->getBlackList();
			$_whiteList = $_docCustomerFilter->getWhiteList();

			$_db->query('REPLACE INTO ' . CUSTOMER_FILTER_TABLE . ' SET ' . we_database_base::arraySetter(array(
						'modelId' => $model->ID,
						'modelType' => $model->ContentType,
						'modelTable' => stripTblPrefix($model->Table),
						'accessControlOnTemplate' => $_docCustomerFilter->getAccessControlOnTemplate(),
						'errorDocNoLogin' => $_docCustomerFilter->getErrorDocNoLogin(),
						'errorDocNoAccess' => $_docCustomerFilter->getErrorDocNoAccess(),
						'mode' => $_docCustomerFilter->getMode(),
						'specificCustomers' => ($_specificCustomers ? implode(',', $_specificCustomers) : ''),
						'filter' => ($_filter ? we_serialize($_filter, 'json') : ''),
						'whiteList' => ($_whiteList ? implode(',', $_whiteList) : ''),
						'blackList' => ($_blackList ? implode(',', $_blackList) : ''),
					))
			);
		}
	}

	/**
	 * Call this function, when model is deleted !
	 * this function is called, when model with filter is saved (filters are resaved)
	 *
	 * param webeditionDocument or objectFile
	 * @param mixed $model
	 */
	function deleteForModel(&$model, we_database_base $db = null){
		if($model->ID){
			$_db = ($db ? : new DB_WE());
			$_db->query('DELETE FROM ' . CUSTOMER_FILTER_TABLE . ' WHERE modelId=' . intval($model->ID) . ' AND modelTable="' . stripTblPrefix($model->Table) . '"');
		}
	}

	/**
	 * Call this function, if customer is deleted
	 *
	 * @param we_customer_customer $webUser
	 */
	public static function deleteWebUser($webUser){
		if(!$webUser){
			return;
		}
		$_db = new DB_WE();
		$_db->query('UPDATE ' . CUSTOMER_FILTER_TABLE . ' SET specificCustomers=REPLACE(specificCustomers,",' . intval($webUser) . ',",",") WHERE FIND_IN_SET(' . intval($webUser) . ',specificCustomers)');
		$_db->query('UPDATE ' . CUSTOMER_FILTER_TABLE . ' SET specificCustomers="" WHERE specificCustomers=","');
		$_db->query('UPDATE ' . CUSTOMER_FILTER_TABLE . ' SET whiteList=REPLACE(whiteList,",' . intval($webUser) . ',",",") WHERE FIND_IN_SET(' . intval($webUser) . ',whiteList)');
		$_db->query('UPDATE ' . CUSTOMER_FILTER_TABLE . ' SET whiteList="" WHERE whiteList=","');
		$_db->query('UPDATE ' . CUSTOMER_FILTER_TABLE . ' SET blackList=REPLACE(blackList,",' . intval($webUser) . ',",",") WHERE FIND_IN_SET(' . intval($webUser) . ',blackList)');
		$_db->query('UPDATE ' . CUSTOMER_FILTER_TABLE . ' SET blackList="" WHERE blackList=","');
	}

	/**
	 * Deletes all filters for given modelIds of table
	 * call this, when several models are deleted
	 */
	public static function deleteModel(array $modelIds, $table){
		if(!$modelIds){
			return;
		}
		$_db = new DB_WE();
		$_db->query('DELETE FROM ' . CUSTOMER_FILTER_TABLE . ' WHERE modelId IN (' . implode(', ', $modelIds) . ') AND modelTable="' . $_db->escape(stripTblPrefix($table)) . '"');
	}

	/**
	 * private function. gets all file ids which customer can not accesss
	 *
	 * @param we_listview_document $listview
	 * @return array
	 */
	private static function _getFilesWithRestrictionsOfCustomer(we_listview_base $obj, $filter, $classID, $ids){
		//FIXME: this will query ALL documents with restrictions - this is definately not what we want!
		$_cid = !empty($_SESSION['webuser']['registered']) && $_SESSION['webuser']['ID'] ? $_SESSION['webuser']['ID'] : 0;
		//cache result
		static $_filesWithRestrictionsForCustomer = array();

		$listQuery = ' (mode=' . we_customer_abstractFilter::FILTER . ' AND !FIND_IN_SET(' . $_cid . ',whiteList) ) '; //FIND_IN_SET($_cid,blackList) AND
		$specificCustomersQuery = ' (mode=' . we_customer_abstractFilter::SPECIFIC . " AND !FIND_IN_SET($_cid,specificCustomers)) ";
		$mfilter = 'mode IN(' . we_customer_abstractFilter::FILTER . ',' . we_customer_abstractFilter::SPECIFIC . ')';

		// detect all files/objects with restrictions
		$_queryForIds = $obj->getCustomerRestrictionQuery($specificCustomersQuery, $classID, $mfilter, $listQuery);


// if customer is not logged in=> return NO_LOGIN
		// else return correct filter
		// execute the query (get all existing filters)
		$query = 'SELECT f.* ' . $_queryForIds . ($ids ? ' AND modelId IN (' . implode(',', (array_map('intval', explode(',', $ids)))) . ')' : '');
		$key = md5($query);
		if(isset($_filesWithRestrictionsForCustomer[$key])){
			return $_filesWithRestrictionsForCustomer[$key];
		}

		$_db = new DB_WE();
		$_db->query($query);
// visitor is logged in
		$_filesWithRestrictionsForCustomer[$key] = $filters = array();
		while($_db->next_record()){
			$filters[] = self::getFilterByDbHash($_db->getRecord());
		}

		foreach($filters as $filter){
			$_perm = $filter->accessForVisitor($filter->getModelId(), $filter->getModelType(), false, true);
			switch($_perm){
				case self::NO_ACCESS:
				case self::NO_LOGIN:
					$_filesWithRestrictionsForCustomer[$key][$filter->getModelTable()][] = $filter->getModelId();
					break;
				case self::CONTROLONTEMPLATE:
					if($filter === 'all' || $filter === 'true' || $filter === true){
						$_filesWithRestrictionsForCustomer[$key][$filter->getModelTable()][] = $filter->getModelId();
					}
					break;
				case self::ACCESS:
					break;
			}
		}
		return $_filesWithRestrictionsForCustomer[$key];
	}

	/**
	 * checks if visitor has acces to see the document or object
	 *
	 * @param mixed $model
	 * @param array $modelHash
	 * @param boolean $_fromIfRegisteredUser
	 * @return string
	 */
	function accessForVisitor($id, $ct, $_fromIfRegisteredUser = false, $_fromListviewCheck = false){
		if($id == $this->getModelId() && $ct == $this->getModelType()){ // model is correct
			if(!$_fromListviewCheck && $this->getAccessControlOnTemplate() && !$_fromIfRegisteredUser){
				// access control is on template (for we:ifregisteredUser)
				return self::CONTROLONTEMPLATE;
			}

			if(!self::customerIsLogedIn()){ // no customer logged in
				// visitor is NOT logged in
				return self::NO_LOGIN;
			}

			if(!$this->customerHasAccess()){
				return self::NO_ACCESS;
			}
		}
		return self::ACCESS;
	}

	/* ############################ Accessors and Mutators ################################### */

	/**
	 * Accessor method for $this->_id
	 *
	 * @return integer
	 */

	/**
	 * Mutator method for $this->_modelId
	 *
	 * @param integer $modelId
	 */
	function setModelId($modelId){
		$this->_modelId = $modelId;
	}

	/**
	 * Accessor method for $this->_modelId
	 *
	 * @return integer
	 */
	function getModelId(){
		return $this->_modelId;
	}

	/**
	 * Mutator method for $this->_modelType
	 *
	 * @param string $modelType
	 */
	function setModelType($modelType){
		$this->_modelType = $modelType;
	}

	/**
	 * Accessor method for $this->_modelType
	 *
	 * @return string
	 */
	function getModelType(){
		return $this->_modelType;
	}

	/**
	 * Mutator method for $this->_modelTable
	 *
	 * @param string $modelTable
	 */
	function setModelTable($modelTable){
		$this->_modelTable = $modelTable;
	}

	/**
	 * Accessor method for $this->_modelTable
	 *
	 * @return string
	 */
	function getModelTable(){
		return $this->_modelTable;
	}

	/**
	 * Mutator method for $this->_accessControlOnTemplate
	 *
	 * @param boolean $accessControlOnTemplate
	 */
	function setAccessControlOnTemplate($accessControlOnTemplate){
		$this->_accessControlOnTemplate = $accessControlOnTemplate;
	}

	/**
	 * Accessor method for $this->_accessControlOnTemplate
	 *
	 * @return boolean
	 */
	function getAccessControlOnTemplate(){
		return $this->_accessControlOnTemplate;
	}

	/**
	 * Mutator method for $this->_errorDocNoLogin
	 *
	 * @param integer $errorDocNoLogin
	 */
	function setErrorDocNoLogin($errorDocNoLogin){
		$this->_errorDocNoLogin = $errorDocNoLogin;
	}

	/**
	 * Accessor method for $this->_errorDocNoLogin
	 *
	 * @return integer
	 */
	function getErrorDocNoLogin(){
		return $this->_errorDocNoLogin;
	}

	/**
	 * Mutator method for $this->_errorDocNoAccess
	 *
	 * @param integer $errorDocNoAccess
	 */
	function setErrorDocNoAccess($errorDocNoAccess){
		$this->_errorDocNoAccess = $errorDocNoAccess;
	}

	/**
	 * Accessor method for $this->_errorDocNoAccess
	 *
	 * @return integer
	 */
	function getErrorDocNoAccess(){
		return $this->_errorDocNoAccess;
	}

}
