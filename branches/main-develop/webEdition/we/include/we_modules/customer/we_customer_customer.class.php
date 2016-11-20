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
class we_customer_customer extends we_base_model{
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
	const PWD_USER_EXISTS = 6;
	const PWD_NOT_SUFFICIENT = 7;

	//properties
	var $ID = 0;
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
	protected $protected = ['ID', 'Path', 'ModifiedBy', 'ModifyDate'];
	var $properties = ['Username', 'Password', 'Forename', 'Surname', 'LoginDenied', 'MemberSince', 'LastLogin', 'LastAccess', 'AutoLoginDenied', 'AutoLogin'];
	var $udates = ['MemberSince', 'LastLogin', 'LastAccess'];

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
		$this->persistent_slots = $this->db->metadata($this->table, we_database_base::META_NAME);
		foreach($this->persistent_slots as $fname){
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

		$hook = new weHook('customer_preSave', '', ['customer' => $this, 'from' => 'management', 'type' => ($this->ID ? 'existing' : 'new')]);

		return $hook->executeHook() && we_base_model::save() && $this->registerMediaLinks();
	}

	protected function registerMediaLinks(){
		$this->unregisterMediaLinks();
		foreach(self::getImageFields() as $field){
			if($this->$field){
				$this->MediaLinks[$field] = $this->$field;
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
		if(we_base_model::delete() && $recursive){
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

	function getBranches(array &$branches, array &$fixed, array &$other){
		$fixed['ID'] = $this->ID; // Bug Fix #8413 + #8520
		if(!isset($this->persistent_slots)){
			return;
		}

		$branch = '';
		foreach($this->persistent_slots as $per){
			$var_value = (isset($this->$per) ? $this->$per : null);
			$field = $this->transFieldName($per, $branch);

			if($field != $per){
				$branches[$branch][$field] = $var_value;
			} else if(in_array($per, $this->properties)){
				$fixed[$per] = $var_value;
			} else if(!in_array($per, $this->protected)){
				$other[$per] = $var_value;
			}
		}
		ksort($branches);
	}

	function getBranchesNames(){
		$branches = $common = $other = [];
		$this->getBranches($branches, $common, $other);
		return array_keys($branches);
	}

	function getFieldsNames($branch){
		$branches = $common = $other = [];

		$this->getBranches($branches, $common, $other);

		$arr = [];

		$branch = $branch ?: g_l('modules_customer', '[other]');


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

		$ret = [];
		$other = g_l('modules_customer', '[other]');
		foreach(array_keys($arr) as $b){
			if($branch === $other){
				$ret[$b] = $b;
			} else {
				$ret[$branch . "_" . $b] = $b;
			}
		}
		return $ret;
	}

	function getFieldDbProperties($field_name){
		$buff = $this->getFieldsDbProperties();
		return (isset($buff[$field_name]) ?
			$buff[$field_name] :
			[]);
	}

	function getFieldsDbProperties(){
		static $ret = [];
		if($ret){
			return $ret;
		}
		$this->db->query('SHOW COLUMNS FROM ' . CUSTOMER_TABLE);
		while($this->db->next_record(MYSQL_ASSOC)){
			$record = $this->db->Record;
			list($type) = explode('(', $record['Type']);
			switch($type){
				case 'tinyint':
				case 'smallint':
				case 'mediumint':
				case 'int':
				case 'bigint';
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
		$db = $db ?: new DB_WE();
		$name = trim($name);
		return ($name ? f('SELECT 1 FROM ' . CUSTOMER_TABLE . ' WHERE Username="' . $db->escape($name) . '" LIMIT 1', '', $db) : true);
	}

	/* function customerFieldValueExist($fieldname, $value, $condition = ''){
	  $db = new DB_WE();
	  return (f('SELECT 1 FROM ' . CUSTOMER_TABLE . ' WHERE ' . $db->escape($fieldname) . '="' . $db->escape($value) . '"' . ($condition ? ' AND ' . $condition : '') . ' LIMIT 1', '', $db));
	  } */

	function fieldExist($field){
		return in_array($field, $this->persistent_slots);
	}

	function getFieldset(){
		$result = [];
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
		$matches = [];
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
		$matches = [];
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
		$customerFields = we_unserialize(f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="webadmin" AND pref_name="FieldAdds"'));
		$fields = [];
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
		$customerFields = we_unserialize(f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="webadmin" AND pref_name="FieldAdds"'));
		$fields = [];
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

	public static function getJSLangConsts(){
		return 'WE().consts.g_l.customer={
	view:{
		save_changed_customer:"' . g_l('modules_customer', '[save_changed_customer]') . '",
		delete_alert:"' . g_l('modules_customer', '[delete_alert]') . '",
		nothing_to_delete:"' . we_message_reporting::prepareMsgForJS(g_l('modules_customer', '[nothing_to_delete]')) . '",
		nothing_to_save:"' . we_message_reporting::prepareMsgForJS(g_l('modules_customer', '[nothing_to_save]')) . '",
		reset_failed_login_successfully:"' . we_message_reporting::prepareMsgForJS(g_l('modules_customer', '[login_reset_ok]')) . '"
		},
	admin:{
		del_fild_question:"' . g_l('modules_customer', '[del_fild_question]') . '",
		other:"' . g_l('modules_customer', '[other]') . '",
		no_field: "' . we_message_reporting::prepareMsgForJS(g_l('modules_customer', '[no_field]')) . '",
		no_branch:"' . we_message_reporting::prepareMsgForJS(g_l('modules_customer', '[no_branch]')) . '",
		branch_no_edit:"' . we_message_reporting::prepareMsgForJS(g_l('modules_customer', '[branch_no_edit]')) . '",
		we_fieldname_notValid:"' . we_message_reporting::prepareMsgForJS(g_l('modules_customer', '[we_fieldname_notValid]')) . '"
	},
	sortAdmin:{
		default_soting_no_del: "' . we_message_reporting::prepareMsgForJS(g_l('modules_customer', '[default_soting_no_del]')) . '",
		sortname_empty: "' . we_message_reporting::prepareMsgForJS(g_l('modules_customer', '[sortname_empty]')) . '",
	}
};';
	}

}
