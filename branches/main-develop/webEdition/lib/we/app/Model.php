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
 * Base class for app models
 *
 * @category   we
 * @package none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_app_Model extends we_core_AbstractModel{
	/**
	 * id attribute
	 *
	 * @var integer
	 */
	public $ID = 0;

	/**
	 * Text attribute
	 *
	 * @var string
	 */
	public $Text = '';

	/**
	 * ParentID attribute
	 *
	 * @var integer
	 */
	public $ParentID = 0;

	/**
	 * Path attribute
	 *
	 * @var string
	 */
	public $Path = '';

	/**
	 * ContentType attribute
	 *
	 * @var string
	 */
	public $ContentType = '';

	/**
	 * IsFolder attribute
	 *
	 * @var boolean
	 */
	public $IsFolder;

	/**
	 * Published attribute
	 *
	 * @var int
	 */
	public $Published = 0;

	/**
	 * _appName attribute
	 *
	 * @var string
	 */
	protected $_appName = '';

	/**
	 * _requiredFields attribute
	 *
	 * @var array
	 */
	protected $_requiredFields = array();

	/**
	 * Constructor
	 *
	 * Set table and load persistents
	 *
	 * @param string $table
	 * @return void
	 */
	function __construct($table){
		parent::__construct($table);
	}

	/**
	 * validates the Text
	 *
	 * @return boolean
	 */
	public function textNotValid(){
		if(stripos($this->Text, '/') === false){
			return false;
		} else {
			return true;
		}
	}

	/**
	 * check if field is required
	 *
	 * @param string $fieldname
	 * @return boolean
	 */
	public function isRequiredField($fieldname){
		return in_array($fieldname, $this->_requiredFields);
	}

	/**
	 * check if required fields are available
	 *
	 * @param string (reference) $failed
	 * @return boolean
	 */
	public function hasRequiredFields(&$failed){
		foreach($this->_requiredFields as $req){
			if(empty($this->$req)){
				$failed[] = $req;
			}
		}
		return empty($failed);
	}

	/**
	 * set path
	 *
	 * @param string $path
	 * @return void
	 */
	public function setPath($path = ''){
		if($path === ''){
			$path = we_util_Path::id2Path($this->ParentID, $this->_table) . '/' . $this->Text;
		}
		$this->Path = $path;
	}

	/**
	 * check if path exists
	 *
	 * @param string $path
	 * @return boolean
	 */
	public function pathExists($path){
		return we_util_Path::pathExists($path, $this->_table);
	}

	/**
	 * check if ParentId is equal to Id
	 *
	 * @return boolean
	 */
	public function isSelf(){
		$db = new DB_WE();

		if($this->ID){
			$count = 0;
			$parentid = $this->ParentID;
			while($parentid != 0){
				if($parentid == $this->ID){
					return true;
				}
				$parentid = f('SELECT ParentID FROM ' . escape_sql_query($this->_table) . ' WHERE ID=' . intval($parentid), '', $db);
				$count++;
				if($count == 9999){
					return false;
				}
			}
			return false;
		} else {
			return false;
		}
	}

	/**
	 * check permissions of user
	 *
	 * @return boolean
	 */
	public function isAllowedForUser(){
		return true;
	}

	/**
	 * returns the path of given id
	 *
	 * @param integer $id
	 * @return string
	 */
	protected function _evalPath($id = 0){
		$db = new DB_WE();
		$path = '';
		if($id == 0){
			$id = $this->ParentID;
			$path = $this->Text;
		}

		$result = getHash('SELECT Text,ParentID FROM ' . $this->_table . ' WHERE ' . $this->_primaryKey . '=' . intval($id), $db);
		$path = '/' . (isset($result['Text']) ? $result['Text'] : '') . $path;
		$pid = isset($result['ParentID']) ? intval($result['ParentID']) : 0;
		while($pid > 0){
			$result = getHash('SELECT Text,ParentID FROM ' . $this->_table . ' WHERE ' . $this->_primaryKey . '=' . $pid, $db);
			$path = '/' . $result['Text'] . $path;
			$pid = intval($result['ParentID']);
		}
		return $path;
	}

	/**
	 * update the child paths
	 *
	 * @param string $oldpath
	 * @return void
	 */
	public function updateChildPaths($oldpath){
		$db = new DB_WE();
		if($this->IsFolder && $oldpath != '' && $oldpath != '/' && $oldpath != $this->Path){
			$result = $db->getAllq('SELECT ' . $this->_primaryKey . ' FROM ' . $this->_table . '  WHERE Path like "' . $db->escape($oldpath . '%') . '" AND ' . $this->_primaryKey . ' !=' . intval($this->{$this->_primaryKey}));
			foreach($result as $row){
				$updateFields = array('Path' => $this->_evalPath($row[$this->_primaryKey]));
				$cond = $this->_primaryKey . '=' . intval($row[$this->_primaryKey]);
				$db->update($this->_table, $updateFields, $cond);
			}
		}
	}

	/**
	 * set IsFolder
	 *
	 * @param boolean $value
	 * @return void
	 */
	public function setIsFolder($value){
		$this->IsFolder = $value;
	}

	/**
	 * delete childs
	 *
	 * @return void
	 */
	public function deleteChilds(){
		$db = new DB_WE();
		$db->query('SELECT ' . $this->_primaryKey . ' FROM ' . $this->_table . ' WHERE ParentID=' . intval($this->{$this->_primaryKey}));
		while($db->next_record()){
			$id = $db->f($this->_primaryKey);
			$class = get_class($this);
			$child = new $class($id);
			$child->delete();
		}
	}

	/**
	 * deletes entry
	 *
	 * @return void
	 */
	public function delete($skipHook = 0){
		$translate = we_core_Local::addTranslation('apps.xml');
		$message = $translate->_('This entry cannot be deleted. Probably there is no appropriate data record in the data base or the data base does not exist. In this case you must implement the data retention.');

		if(!$this->{$this->_primaryKey}){
			throw new we_core_ModelException($message, we_service_ErrorCodes::kModelNoPrimaryKeySet);
		}

		if($this->IsFolder){
			$this->deleteChilds();
		}

		parent::delete($skipHook);
	}

	/**
	 * set Fields
	 *
	 * @param array $fields
	 * @return void
	 */
	public function setFields($fields){
		parent::setFields($fields);
	}

}
