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
 * General Definition of WebEdition Customer
 *
 */
class we_customer_customer extends weModelBase{
	const NOPWD_CHANGE = '__WE__PWD_NO_CHANGE';
	const ENCRYPTED_DATA = '**ENCRYPTED**';
	const ENCRYPT_NONE = 0;
	const ENCRYPT_SYMMETRIC = 1;
	const ENCRYPT_HASH = 2;
	const REMOVE_PASSWORD = 0;
	const STORE_PASSWORD = 1;
	const STORE_DBPASSWORD = 2;
	const PWD_ALL_OK = 0;
	const PWD_FIELD_NOT_SET = 1;
	const PWD_NOT_MATCH = 2;
	const PWD_NO_SUCH_USER = 3;
	const PWD_TOKEN_INVALID = 4;
	const PWD_USER_EMPTY = 5;
	const PWD_USER_EXISTS = 5;
	const PWD_NOT_SUFFICIENT = 6;

	//properties
	var $ID;
//	var $Text;
	//var $ParentID;
	//var $IsFolder;
//	var $Path;
	var $Username;
	var $Password;
	var $LoginDenied;
	var $Forename;
	var $Surname;
	var $MemberSince;
	var $LastLogin = 0;
	var $LastAccess = 0;
	var $ModifyDate;
	var $ModifiedBy;
	var $protected = array('ID', /* 'ParentID', 'IsFolder', 'Path', 'Text', */ 'ModifiedBy', 'ModifyDate');
	var $properties = array('Username', 'Password', 'Forename', 'Surname', 'LoginDenied', 'MemberSince', 'LastLogin', 'LastAccess', 'AutoLoginDenied', 'AutoLogin');
	var $udates = array('MemberSince', 'LastLogin', 'LastAccess');

	/**
	 * Default Constructor
	 * Can load or create new Customer depends of parameter
	 */
	function __construct($customerID = 0){
		parent::__construct(CUSTOMER_TABLE);

		$this->MemberSince = time();

		if($customerID){
			$this->ID = $customerID;
			$this->load($customerID);
		}
	}

	public function loadPresistents(){
		$this->persistent_slots = array();
		$tableInfo = $this->db->metadata($this->table);
		foreach($tableInfo as $t){
			$fname = $t["name"];
			$this->persistent_slots[] = $fname;
			if(!isset($this->$fname)){
				$this->$fname = '';
			}
		}
	}

	/**
	 * 	Overwrites an existing User, keeps customerfilters => User should be the same person
	 * @param int $id id to overwrite
	 */
	function overwrite($id){
		$tmp = new self($id);
		$tmp->delete(false);
		unset($tmp);
		$this->ID = $id;
		$this->save();
	}

	function save($force_new = false, $isAdvanced = false, $jsonSer = false){
		/* 		$this->IsFolder = 0;
		  $this->Text = $this->Username;
		  $this->Path = '/' . $this->Username;
		 */
		if($this->MemberSince == 0){
			$this->MemberSince = time();
		}
		$this->ModifyDate = time();
		$this->ModifiedBy = 'backend';
		if(isset($this->setModifiedBy)){
			$this->ModifiedBy = $this->setModifiedBy;
		}

		$hook = new weHook('customer_preSave', '', array('customer' => $this, 'from' => 'management', 'type' => ($this->ID ? 'existing' : 'new')));

		return $hook->executeHook() && weModelBase::save() && $this->registerMediaLinks();
	}

	function registerMediaLinks(){
		$this->unregisterMediaLinks();
		foreach(self::getImageFields() as $field){
			if($this->$field){
				$this->MediaLinks[] = $this->$field;
			}
		}

		parent::registerMediaLinks();
		return true;
	}

	/**
	 * delete entry from database
	 * @param recursive bool if true, customerfilter are deleted as well
	 */
	function delete($recursive = true){ //FIXME: what about documents/objects of customer?
		if(weModelBase::delete() && $recursive){
			we_customer_documentFilter::deleteWebUser($this->ID);
			return true;
		}
		return false;
	}

	function transFieldName($real_name, &$banche){
		if(strpos($real_name, g_l('modules_customer', '[other]') !== FALSE)){
			return $real_name;
		}
		$pre = explode('_', $real_name);
		if(($pre[0] != $real_name) && (!in_array($pre[0], $this->protected)) && (!in_array($pre[0], $this->properties))){
			$banche = $pre[0];
			$field = implode('_', array_slice($pre, 1));
			return $field;
		}
		return $real_name;
	}

	function getBranches(array &$banches, array &$fixed, array &$other, $mysort = ''){
		$fixed['ID'] = $this->ID; // Bug Fix #8413 + #8520
		if(!isset($this->persistent_slots)){
			return;
		}
		$sortarray = ($mysort ? makeArrayFromCSV($mysort) : range(0, count($this->persistent_slots) - 1));

		if(count($sortarray) != count($this->persistent_slots)){
			if(count($sortarray) == count($this->persistent_slots) - 1){
				$sortarray[] = max($sortarray) + 1;
			} else {
				$sortarray = range(0, count($this->persistent_slots) - 1);
			}
		}
		$orderedarray = array_combine($sortarray, $this->persistent_slots);
		ksort($orderedarray);

		$branche = array();
		foreach($orderedarray as $per){
			$var_value = (isset($this->$per) ? $this->$per : null);
			$field = $this->transFieldName($per, $branche);

			if($field != $per){
				$banches[$branche][$field] = $var_value;
			} else if(in_array($per, $this->properties)){
				$fixed[$per] = $var_value;
			} else if(!in_array($per, $this->protected)){
				$other[$per] = $var_value;
			}
		}
	}

	function getBranchesNames(){
		$branches = $common = $other = array();
		$this->getBranches($branches, $common, $other);
		return array_keys($branches);
	}

	function getFieldsNames($branch, $mysort = ''){
		$branches = $common = $other = array();

		$this->getBranches($branches, $common, $other, $mysort);

		$arr = array();

		$branch = $branch? : g_l('modules_customer', '[other]');


		switch($branch){
			case g_l('modules_customer', '[common]'):
				if(is_array($common)){
					$arr = $common;
				}
				break;
			case g_l('modules_customer', '[other]'):
				if(is_array($common)){
					$arr = $other;
				}
				break;
			default:
				if(isset($branches[$branch]) && is_array($branches[$branch])){
					$arr = $branches[$branch];
				}
		}

		$ret = array();
		foreach(array_keys($arr) as $b){
			if($branch == g_l('modules_customer', '[other]')){
				$ret[$b] = $b;
			} else {
				$ret[$branch . "_" . $b] = $b;
			}
		}
		return $ret;
	}

	function getFieldDbProperties($field_name){
		$buff = $this->getFieldsDbProperties();

		foreach($buff as $b){
			if($b["Field"] == $field_name){
				return $b;
			}
		}

		return array();
	}

	function getFieldsDbProperties(){
		$ret = array();
		$this->db->query('SHOW COLUMNS FROM ' . $this->db->escape($this->table));
		while($this->db->next_record()){
			$record = $this->db->Record;
			switch($record['Type']){
				case 'int(11)':
					if(empty($record['Default'])){
						$record['Default'] = '0';
					}
					break;
				case 'bigint(20)':
					if(empty($record['Default'])){
						$record['Default'] = '0';
					}
					break;
				case 'date':
					if(empty($record['Default'])){
						$record['Default'] = '0000-00-00';
					}
					break;
				case 'datetime':
					if(empty($record['Default'])){
						$record['Default'] = '0000-00-00 00:00:00';
					}
					break;
			}
			$ret[$this->db->f('Field')] = $record;
		}

		return $ret;
	}

	function isInfoDate($field){
		return in_array($field, $this->udates);
	}

	function isProperty($field){
		return in_array($field, $this->properties);
	}

	function isProtected($field){
		return in_array($field, $this->protected);
	}

	function clearSessionVars(){
		if(isset($_SESSION['weS']['customer_session'])){
			unset($_SESSION['weS']['customer_session']);
		}
	}

	static function customerNameExist($name, we_database_base $db = null){
		$db = $db ? : new DB_WE();
		$name = trim($name);
		return ($name ? f('SELECT 1 FROM ' . CUSTOMER_TABLE . ' WHERE Username="' . $db->escape($name) . '" LIMIT 1', '', $db) : true);
	}

	function customerFieldValueExist($fieldname, $value, $condition = ''){
		$db = new DB_WE();
		return (f('SELECT 1 FROM ' . CUSTOMER_TABLE . ' WHERE ' . $db->escape($fieldname) . '="' . $db->escape($value) . '"' . ($condition ? ' AND ' . $condition : '') . ' LIMIT 1', '', $db));
	}

	function fieldExist($field){
		return in_array($field, $this->persistent_slots);
	}

	function getFieldset(){
		$result = array();
		$fields = $this->getFieldsDbProperties();
		foreach(array_keys($fields) as $k){
			if(!$this->isProtected($k)){
				$result[] = $k;
			}
		}
		return $result;
	}

	function filenameNotValid(){
		return preg_match('|[/]|i', $this->Username);
	}

	public static function cryptPassword($pass){
		switch(SECURITY_ENCRYPTION_TYPE_PASSWORD){
			case self::ENCRYPT_NONE:
				return $pass;
			case self::ENCRYPT_SYMMETRIC:
				return self::cryptData($pass);
			case self::ENCRYPT_HASH:
				return we_users_user::makeSaltedPassword($pass, 10);
		}
	}

	public static function comparePassword($storedPassword, $clearPassword){
		if(!$storedPassword || !$clearPassword){
			return false;
		}
		$matches = array();
		if(!preg_match('|^\$([^$]{2,4})\$([^$]+)\$(.+)$|', $storedPassword, $matches)){
			return $storedPassword === $clearPassword;
		}
		switch($matches[1]){
			case '-1':
				return $clearPassword === self::decryptData($storedPassword);
			case '2y':
				return we_users_user::comparePasswords('', $storedPassword, $clearPassword);
		}
		return false;
	}

	public static function cryptGetIV($len = 8){
		if(!function_exists('mcrypt_create_iv')){
			return '';
		}
		return mcrypt_create_iv($len, (runAtWin() ? MCRYPT_RAND : MCRYPT_DEV_URANDOM));
	}

	public static function cryptData($data, $key = SECURITY_ENCRYPTION_KEY, $keepBin = false){//Note we need 4 Bytes prefix + 16 Byte IV + 1$ = 21 Bytes. The rest is avail for data, which is hex'ed, so "half" of length is available
		if($data && function_exists('mcrypt_module_open') && ($res = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_OFB, ''))){
			$iv = self::cryptGetIV();
			mcrypt_generic_init($res, hex2bin($key), $iv);
			$data = mcrypt_generic($res, $data);
			mcrypt_generic_deinit($res);
			mcrypt_module_close($res);
			return '$-1' . ($keepBin ? 'a' : '') . '$' . bin2hex($iv) . '$' . ($keepBin ? $data : bin2hex($data));
		}
		return $data;
	}

	public static function decryptData($data, $key = SECURITY_ENCRYPTION_KEY){
		$matches = array();
		if(!preg_match('|^\$([^$]{2,4})\$([a-f0-9]{16,32})\$|', $data, $matches)){
			return '';
		}
		$data = substr($data, strlen($matches[0]));
		switch($matches[1]){
			case '-1':
				$data = hex2bin($data);
			case '-1a':
				if(function_exists('mcrypt_module_open') && ($res = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_OFB, ''))){
					mcrypt_generic_init($res, hex2bin($key), hex2bin($matches[2]));
					$data = mdecrypt_generic($res, $data);
					mcrypt_generic_deinit($res);
					mcrypt_module_close($res);
					return $data;
				}
			default:
			case '2y'://can't be decoded
				return '';
		}
	}

	public static function getEncryptedFields(){
		static $fields = -1;
		if(is_array($fields)){
			return $fields;
		}
		$customerFields = we_unserialize(f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="webadmin" AND pref_name="FieldAdds"', '', $GLOBALS['DB_WE']));
		$fields = array();
		if(!$customerFields){
			return $fields;
		}

		foreach($customerFields as $key => $value){
			if(!empty($value['encryption'])){
				$fields[$key] = self::ENCRYPTED_DATA;
			}
		}

		return $fields;
	}

	public static function getImageFields(){
		static $fields = -1;
		if(is_array($fields)){
			return $fields;
		}
		$customerFields = we_unserialize(f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="webadmin" AND pref_name="FieldAdds"', '', $GLOBALS['DB_WE']));
		$fields = array();
		if(!$customerFields){
			return $fields;
		}

		foreach($customerFields as $key => $value){
			if(isset($value['type']) && $value['type'] === 'img'){
				$fields[] = $key;
			}
		}

		return $fields;
	}

}
