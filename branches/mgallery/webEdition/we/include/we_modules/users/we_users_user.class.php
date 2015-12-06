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
class we_users_user{
	const TYPE_USER = 0;
	const TYPE_USER_GROUP = 1;
	const TYPE_ALIAS = 2;
	const INVALID_CREDENTIALS = 1;
	const MAX_LOGIN_COUNT_REACHED = 2;
	const ERR_USER_PATH_NOK = -5;
	const SALT_NONE = 0;
	const SALT_MD5 = 1;
	const SALT_CRYPT = 2;
	const TAB_DATA = 0;
	const TAB_PERMISSION = 1;
	const TAB_WORKSPACES = 2;
	const TAB_SETTINGS = 3;

	// Name of the class => important for reconstructing the class from outside the class
	var $ClassName = __CLASS__;
	// In this array are all storagable class variables
	public $persistent_slots = array(
		'ID' => we_base_request::INT,
		'Type' => we_base_request::INT,
		'ParentID' => we_base_request::INT,
		'Salutation' => we_base_request::STRING,
		'First' => we_base_request::STRING,
		'Second' => we_base_request::STRING,
		'Address' => we_base_request::STRING,
		'HouseNo' => we_base_request::STRING, //e.g. 2a
		'City' => we_base_request::STRING,
		'PLZ' => we_base_request::STRING, //leading 0
		'State' => we_base_request::STRING,
		'Country' => we_base_request::STRING,
		'Tel_preselection' => we_base_request::STRING,
		'Telephone' => we_base_request::STRING,
		'Fax' => we_base_request::STRING,
		'Fax_preselection' => we_base_request::STRING,
		'Handy' => we_base_request::STRING,
		'Email' => we_base_request::EMAIL,
		'username' => we_base_request::STRING,
		'passwd' => we_base_request::STRING,
		'clearpasswd' => we_base_request::STRING,
		'Text' => we_base_request::STRING,
		'Path' => we_base_request::FILE,
		'Permissions' => we_base_request::RAW,
		'ParentPerms' => we_base_request::BOOL,
		'Description' => we_base_request::STRING,
		'Alias' => we_base_request::STRING,
		'IsFolder' => we_base_request::INT,
		'CreatorID' => we_base_request::INT,
		'CreateDate' => we_base_request::INT,
		'ModifierID' => we_base_request::INT,
		'ModifyDate' => we_base_request::INT,
		'Ping' => we_base_request::INT,
		'workSpace' => we_base_request::INTLIST,
		//'workSpaceDef' => we_base_request::INTLIST,
		'workSpaceTmp' => we_base_request::INTLIST,
		'workSpaceNav' => we_base_request::INTLIST,
		'workSpaceNwl' => we_base_request::INTLIST,
		'workSpaceObj' => we_base_request::INTLIST,
		'workSpaceCust' => we_base_request::INTLIST,
		'ParentWs' => we_base_request::BOOL,
		'ParentWst' => we_base_request::BOOL,
		'ParentWsn' => we_base_request::BOOL,
		'ParentWso' => we_base_request::BOOL,
		'ParentWsnl' => we_base_request::BOOL,
		'ParentWsCust' => we_base_request::BOOL,
		'altID' => we_base_request::INT,
		'LoginDenied' => we_base_request::BOOL,
		'UseSalt' => we_base_request::INT
	);
	// Name of the Object that was createt from this class
	var $Name = '';
	// ID from the database record
	var $ID = 0;
	// database table in which the object is stored
	var $Table = USER_TABLE;
	// Database Object
	var $DB_WE;
	// Parent identificator
	var $ParentID = 0;
	// Flag which indicates which kind of user is 0-user;1-group;2-owner group;3 - alias
	var $Type = self::TYPE_USER;
	// Flag which indicates if user is group
	var $IsFolder = 0;
	// Salutation
	var $Salutation = '';
	// User first name
	var $First = '';
	// User second name
	var $Second = '';
	// Address
	var $Address = '';
	// House number
	var $HouseNo = '';
	// City
	var $City = '';
	var $State = '';
	// ZIP Code
	var $PLZ = '';
	// Country
	var $Country = '';
	// Telephone preselection
	var $Tel_preselection = '';
	// Telephone
	var $Telephone = '';
	// Fax preselection
	var $Fax_preselection = '';
	// Fax
	var $Fax = '';
	// Cell phone
	var $Handy = '';
	// Email
	var $Email = '';
	// Username
	var $username = '';
	// User password (md5 salted)
	var $passwd = '';
	// User password
	var $clearpasswd = '';
	// User permissions
	var $Permissions = '';
	// Flag which indicated if user inherits permissions from parent
	var $ParentPerms = 0;
	// Description
	var $Description = '';
	// User Prefrences
	var $Preferences = array();
	var $Text = '';
	var $Path = '';
	var $Alias = '';
	var $CreatorID = 0;
	var $CreateDate = 0;
	var $ModifierID = 0;
	var $ModifyDate = 0;
	// Ping flag
	var $Ping = 0;
	// Documents workspaces
	var $workSpace = '';
	/* // Default documents workspaces
	  var $workSpaceDef = '';
	 */
	// Templates workspaces
	var $workSpaceTmp = '';
	// Navigation workspaces
	var $workSpaceNav = '';
	// Objects workspaces
	var $workSpaceObj = '';
	// Newsletter workspaces
	var $workSpaceNwl = '';
	// Customer workspaces
	private $workSpaceCust = '';
	// Flag which indicated if user inherits files workspaces from parent
	var $ParentWs = 1;
	// Flag which indicated if user inherits templates workspaces from parent
	var $ParentWst = 1;
	// Flag which indicated if user inherits templates workspaces from parent
	var $ParentWsn = 1;
	// Flag which indicated if user inherits objetcs workspaces from parent
	var $ParentWso = 1;
	// Flag which indicated if user inherits newsletters workspaces from parent
	var $ParentWsnl = 1;
	// Flag which indicated if user inherits customer "workspaces" from parent
	private $ParentWsCust = 1;
	var $LoginDenied = 0;
	// Flag which indicated if user inherits templates workspaces from parent
	var $initExt = 0;

	/*
	 * ADDITIONAL
	 */
	// Workspace array
	var $workspaces = array(
		FILE_TABLE => array(),
		TEMPLATES_TABLE => array(),
		NAVIGATION_TABLE => array(),
	);
	// Permissions headers array
	var $permissions_main_titles = array();
	// Permissions values array
	var $permissions_slots = array();
	// Permissions titles
	var $permissions_titles = array();
	// Extensions array
	var $extensions_slots = array();
	private $permissions_defaults = array();
	// Preferences array
	private $preference_slots = array('sizeOpt', 'weWidth', 'weHeight', 'usePlugin', 'autostartPlugin', 'promptPlugin', 'Language', 'BackendCharset', 'seem_start_file', 'seem_start_type', 'seem_start_weapp', 'editorSizeOpt', 'editorWidth', 'editorHeight', 'editorFontname', 'editorFontsize', 'editorFont', 'default_tree_count', 'force_glossary_action', 'force_glossary_check', 'cockpit_amount_columns', 'cockpit_amount_last_documents', 'cockpit_rss_feed_url', 'editorMode');

	// Constructor
	public function __construct(){
		$GLOBALS['editor_reloaded'] = false;

		$this->Name = 'user_' . md5(uniqid(__FILE__, true));

		$this->DB_WE = new DB_WE();
		if(defined('OBJECT_TABLE')){
			$this->workspaces[OBJECT_FILES_TABLE] = array();
		}
		if(defined('NEWSLETTER_TABLE')){
			$this->workspaces[NEWSLETTER_TABLE] = array();
		}

		if(defined('CUSTOMER_TABLE')){
			$this->workspaces[CUSTOMER_TABLE] = array();
		}

		foreach($this->preference_slots as $val){
			$this->Preferences[$val] = null;
		}

		$this->initType(self::TYPE_USER);
	}

	function initType($typ, $ext = 0){
		$this->Type = $typ;

		$this->mapPermissions();
		if($ext){
			$this->initExt = $ext;
			foreach($this->extensions_slots as $k => &$v){
				$v->init($this);
			}
		}
	}

	// Intialize the class

	function initFromDB($id){
		if(!$id){
			return false;
		}

		if(f('SELECT 1 FROM ' . USER_TABLE . ' WHERE ID=' . intval($id), '', $this->DB_WE)){
			$this->ID = $id;
			$this->getPersistentSlotsFromDB();
			$this->getPreferenceSlotsFromDB();
			$ret = true;
		} else {
			$ret = false;
		}
		$this->loadWorkspaces();
		$this->mapPermissions();

		return $ret;
	}

	function savePersistentSlotsInDB(){
		$this->ModDate = time();
		$tableInfo = $this->DB_WE->metadata($this->Table);
		if($this->clearpasswd !== ''){
			$this->passwd = self::makeSaltedPassword($this->username, $this->clearpasswd);
		}

		$updt = array();

		foreach($tableInfo as $t){
			$fieldName = $t['name'];
			$val = ($fieldName === 'UseSalt' && $useSalt > 0 ?
					$useSalt :
					(isset($this->$fieldName) ? $this->$fieldName : 0)
				);
			if($fieldName != 'ID'){
				if($fieldName === 'editorFontname' && $this->Preferences['editorFont'] == '0'){
					$val = 'none';
				} elseif($fieldName === 'editorFontsize' && $this->Preferences['editorFont'] == '0'){
					$val = '-1';
				}
				if($fieldName !== 'passwd' || $val !== ''){
					$updt[$fieldName] = $val;
				}
			}
		}
		$this->DB_WE->query(($this->ID ? 'UPDATE ' : 'INSERT INTO ') . $this->DB_WE->escape($this->Table) . ' SET ' . we_database_base::arraySetter($updt) . ($this->ID ? ' WHERE ID=' . intval($this->ID) : ''));
		$this->ID = $this->ID ? : $this->DB_WE->getInsertId();
	}

	function createAccount(){
		if(defined('MESSAGING_SYSTEM')){
			we_messaging_messaging::createFolders($this->ID);
		}
	}

	function removeAccount(){
		if(defined('MESSAGING_SYSTEM')){
			$this->DB_WE->query('DELETE FROM ' . MSG_ADDRBOOK_TABLE . ' WHERE UserID=' . $this->ID);
			$this->DB_WE->query('DELETE FROM ' . MESSAGES_TABLE . ' WHERE UserID=' . $this->ID);
			$this->DB_WE->query('DELETE FROM ' . MSG_TODO_TABLE . ' WHERE UserID=' . $this->ID);
			$this->DB_WE->query('DELETE FROM ' . MSG_TODOHISTORY_TABLE . ' WHERE UserID=' . $this->ID);
			$this->DB_WE->query('DELETE FROM ' . MSG_FOLDERS_TABLE . ' WHERE UserID=' . $this->ID);
			$this->DB_WE->query('DELETE FROM ' . MSG_ACCOUNTS_TABLE . ' WHERE UserID=' . $this->ID);
		}
	}

	function getPersistentSlotsFromDB(){
		$slots = array();
		foreach(array_keys($this->persistent_slots) as $slot){
			switch($slot){
				case 'altID':
					break;
				case 'Ping':
					$slots[] = 'DATE_FORMAT(Ping,"' . g_l('weEditorInfo', '[mysql_date_format]') . '") AS Ping';
					break;
				case 'clearpasswd':
					break;
				default:
					$slots[] = $slot;
			}
		}

		$tmp = getHash('SELECT ' . implode(',', $slots) . ' FROM ' . USER_TABLE . ' WHERE ID=' . intval($this->ID), $this->DB_WE);
		foreach($tmp as $fieldName => $val){
			$this->$fieldName = $val;
		}
	}

	function saveToDB(){
		$db_tmp = new DB_WE();
		$isnew = $this->ID ? false : true;
		if($this->Type == self::TYPE_USER_GROUP && $this->ID != 0){
			$ppath = ($this->ParentID == 0 ? '/' : $this->getPath($this->ParentID));
			$dpath = $this->getPath($this->ID);
			if(preg_match('|' . $dpath . '|', $ppath)){
				return self::ERR_USER_PATH_NOK;
			}
		}
		if($this->Type == self::TYPE_ALIAS){
			$foo = getHash('SELECT ID,username FROM ' . USER_TABLE . ' WHERE ID=' . intval($this->Alias), $this->DB_WE);
			$uorginal = $foo['ID'];
			$search = true;
			$ount = 0;
			$try_name = '@' . $foo['username'];
			$try_text = $foo['username'];
			while($search){
				if(f('SELECT 1 FROM ' . USER_TABLE . ' WHERE ID!=' . intval($this->ID) . ' AND ID!=' . intval($uorginal) . ' AND username="' . $this->DB_WE->escape($try_name) . '" LIMIT 1', '', $this->DB_WE)){
					$try_name = $try_name . '_' . ++$ount;
				} else {
					$search = false;
				}
			}
			$this->username = $try_name;
			$this->Text = $try_text;
		} else {
			$this->Text = $this->username;
		}
		$this->IsFolder = ($this->Type == self::TYPE_USER_GROUP ? 1 : 0);
		$this->Path = $this->getPath($this->ID);
		$oldpath = $this->Path;
		$this->saveWorkspaces();
		$this->savePermissions();
		if($isnew){
			$this->CreatorID = $_SESSION['user']['ID'];
			$this->CreateDate = time();
		}
		$this->ModifierID = $_SESSION['user']['ID'];
		$this->ModifyDate = time();
		$this->savePersistentSlotsInDB();
		$this->createAccount();
		if($oldpath != '' && $oldpath != '/'){
			$this->DB_WE->query('SELECT ID,username FROM ' . USER_TABLE . ' WHERE Path LIKE "' . $this->DB_WE->escape($oldpath) . '%"');
			while($this->DB_WE->next_record()){
				$db_tmp->query('UPDATE ' . USER_TABLE . ' SET Path="' . $this->getPath($this->DB_WE->f('ID')) . '" WHERE ID=' . $this->DB_WE->f('ID'));
			}
		}
		$this->savePreferenceSlotsInDB($isnew);

		$_REQUEST['uid'] = $this->ID;

		return $this->saveToSession();
	}

	function saveToSession(){
		if($this->ID != $_SESSION['user']['ID']){
			return '';
		}

		$save_javascript = 'var _multiEditorreload = false;' .
			$this->rememberPreference(isset($this->Preferences['Language']) ? $this->Preferences['Language'] : null, 'Language') .
			$this->rememberPreference(isset($this->Preferences['BackendCharset']) ? $this->Preferences['BackendCharset'] : null, 'BackendCharset') .
			$this->rememberPreference(isset($this->Preferences['default_tree_count']) ? $this->Preferences['default_tree_count'] : null, 'default_tree_count');

		if(isset($this->Preferences['seem_start_type'])){
			switch($this->Preferences['seem_start_type']){
				case 'cockpit':
					$save_javascript .=
						$this->rememberPreference(0, 'seem_start_file') .
						$this->rememberPreference("cockpit", 'seem_start_type') .
						$this->rememberPreference('', 'seem_start_weapp');
					break;
				case 'object':
					$save_javascript .=
						$this->rememberPreference(isset($this->Preferences['seem_start_object']) ? $this->Preferences['seem_start_object'] : 0, 'seem_start_file') .
						$this->rememberPreference('object', 'seem_start_type') .
						$this->rememberPreference('', 'seem_start_weapp');
					break;
				case 'weapp':
					$save_javascript .=
						$this->rememberPreference((isset($this->Preferences['seem_start_weapp']) ? $this->Preferences['seem_start_weapp'] : ''), 'seem_start_weapp') .
						$this->rememberPreference('weapp', 'seem_start_type') .
						$this->rememberPreference(0, 'seem_start_file');
					break;
				case 'document':
					$save_javascript .=
						$this->rememberPreference(isset($this->Preferences['seem_start_document']) ? $this->Preferences['seem_start_document'] : 0, 'seem_start_file') .
						$this->rememberPreference('document', 'seem_start_type') .
						$this->rememberPreference('', 'seem_start_weapp');
					break;
				default:
					$save_javascript .=
						$this->rememberPreference('0', 'seem_start_type') .
						$this->rememberPreference('', 'seem_start_weapp') .
						$this->rememberPreference(0, 'seem_start_file');
			}
		}

		$save_javascript .=
			$this->rememberPreference(isset($this->Preferences['sizeOpt']) ? $this->Preferences['sizeOpt'] : null, 'sizeOpt') .
			$this->rememberPreference(isset($this->Preferences['weWidth']) ? $this->Preferences['weWidth'] : null, 'weWidth') .
			$this->rememberPreference(isset($this->Preferences['weHeight']) ? $this->Preferences['weHeight'] : null, 'weHeight') .
			$this->rememberPreference(isset($this->Preferences['editorMode']) ? $this->Preferences['editorMode'] : null, 'editorMode') .
			$this->rememberPreference(isset($this->Preferences['editorFont']) ? $this->Preferences['editorFont'] : null, 'editorFont') .
			$this->rememberPreference(isset($this->Preferences['editorFontname']) ? $this->Preferences['editorFontname'] : null, 'editorFontname') .
			$this->rememberPreference(isset($this->Preferences['editorFontsize']) ? $this->Preferences['editorFontsize'] : null, 'editorFontsize') .
			$this->rememberPreference(isset($this->Preferences['editorSizeOpt']) ? $this->Preferences['editorSizeOpt'] : null, 'editorSizeOpt') .
			$this->rememberPreference(isset($this->Preferences['editorWidth']) ? $this->Preferences['editorWidth'] : null, 'editorWidth') .
			$this->rememberPreference(isset($this->Preferences['editorHeight']) ? $this->Preferences['editorHeight'] : null, 'editorHeight') .
			$this->rememberPreference(isset($this->Preferences['force_glossary_action']) ? $this->Preferences['force_glossary_action'] : null, 'force_glossary_action') .
			$this->rememberPreference(isset($this->Preferences['force_glossary_check']) ? $this->Preferences['force_glossary_check'] : null, 'force_glossary_check');

		return $save_javascript;
	}

	function mapPermissions(){
		$this->permissions_main_titles = array();
		$this->permissions_slots = array();
		$this->permissions_titles = array();
		$this->permissions_defaults = array();
		$permissions = we_unserialize($this->Permissions);

		$entries = we_tool_lookup::getPermissionIncludes();

		$d = dir(WE_USERS_MODULE_PATH . 'perms');
		while(($file = $d->read())){
			if(substr($file, 0, 9) === 'we_perms_'){
				$entries[] = WE_USERS_MODULE_PATH . 'perms/' . $file;
			}
		}
		$d->close();

		foreach($entries as $entry){

			$perm_group_name = '';
			$perm_values = $perm_titles = $perm_group_title = array();

			include($entry);
			if(!($perm_group_name === 'administrator' && $this->Type != self::TYPE_USER) && $perm_group_name){
				if(!isset($this->permissions_main_titles[$perm_group_name])){
					$this->permissions_main_titles[$perm_group_name] = '';
				}
				if(!isset($this->permissions_slots[$perm_group_name])){
					$this->permissions_slots[$perm_group_name] = array();
				}
				if(!isset($this->permissions_titles[$perm_group_name])){
					$this->permissions_titles[$perm_group_name] = '';
				}
				if(is_array($perm_values[$perm_group_name])){
					$this->permissions_defaults[$perm_group_name] = is_array($perm_defaults[$perm_group_name]) ? $perm_defaults[$perm_group_name] : array();
					foreach($perm_values[$perm_group_name] as $v){
						$this->permissions_slots[$perm_group_name][$v] = (is_array($permissions) && isset($permissions[$v]) ?
								$permissions[$v] :
								0); //always set unknown fields to 0//(is_array($perm_defaults[$perm_group_name]) ? $perm_defaults[$perm_group_name][$v] : 0);
					}
				}

				$this->permissions_main_titles[$perm_group_name] = $perm_group_title[$perm_group_name];

				if(is_array($perm_titles[$perm_group_name])){
					foreach($perm_titles[$perm_group_name] as $key => $val){
						$this->permissions_titles[$perm_group_name][$key] = $val;
					}
				}
			}
		}
	}

	function setPermissions(){
		foreach($this->perm_branches as $val){
			foreach($val as $k => $v){
				$this->Permissions[$k] = $this->permissions_slots[$v];
			}
		}
	}

	function setPermission($perm_name, $perm_value){
		foreach($this->permissions_slots as $key => $val){
			foreach($val as $k => $v){
				if($perm_name == $k){
					$this->permissions_slots[$key][$k] = $perm_value;
				}
			}
		}
	}

	function savePermissions(){
		$permissions = array();
		foreach($this->permissions_slots as $val){
			foreach($val as $k => $v){
				$permissions[$k] = $v;
			}
		}
		$this->Permissions = we_serialize($permissions, 'json');
	}

	function loadWorkspaces(){
		if($this->workSpace){
			$this->workspaces[FILE_TABLE] = makeArrayFromCSV($this->workSpace);
		}
		if($this->workSpaceTmp){
			$this->workspaces[TEMPLATES_TABLE] = makeArrayFromCSV($this->workSpaceTmp);
		}
		if($this->workSpaceNav){
			$this->workspaces[NAVIGATION_TABLE] = makeArrayFromCSV($this->workSpaceNav);
		}
		if(defined('OBJECT_TABLE') && $this->workSpaceObj){
			$this->workspaces[OBJECT_FILES_TABLE] = makeArrayFromCSV($this->workSpaceObj);
		}
		if(defined('NEWSLETTER_TABLE') && $this->workSpaceNwl){
			$this->workspaces[NEWSLETTER_TABLE] = makeArrayFromCSV($this->workSpaceNwl);
		}
		if(defined('CUSTOMER_TABLE')){
			$this->workspaces[CUSTOMER_TABLE] = we_unserialize($this->workSpaceCust);
		}
	}

	function saveWorkspaces(){
		foreach($this->workspaces as $k => $v){
			if(defined('CUSTOMER_TABLE') && $k == CUSTOMER_TABLE){
				continue;
			}
			$new_array = array();

			foreach($v as $key => $val){
				if($val != 0){
					$new_array[] = $this->workspaces[$k][$key];
				}
			}
			$this->workspaces[$k] = $new_array;
		}

		$this->workSpace = implode(',', $this->workspaces[FILE_TABLE]);
		$this->workSpaceTmp = implode(',', $this->workspaces[TEMPLATES_TABLE]);
		$this->workSpaceNav = implode(',', $this->workspaces[NAVIGATION_TABLE]);
		if(defined('OBJECT_TABLE')){
			$this->workSpaceObj = implode(',', $this->workspaces[OBJECT_FILES_TABLE]);
		}
		if(defined('NEWSLETTER_TABLE')){
			$this->workSpaceNwl = implode(',', $this->workspaces[NEWSLETTER_TABLE]);
		}
		if(defined('CUSTOMER_TABLE')){
			$this->workSpaceCust = $this->workspaces[CUSTOMER_TABLE] ? we_serialize($this->workspaces[CUSTOMER_TABLE]) : '';
		}

		// if no workspaces are set, take workspaces from creator
		if(empty($this->workSpace)){
			$_uws = get_ws(FILE_TABLE, true);
			if(!empty($_uws)){
				$this->workSpace = implode(',', $_uws);
				$this->workspaces[FILE_TABLE] = $_uws;
			}
		}
		if(empty($this->workSpaceTmp)){
			$_uws = get_ws(TEMPLATES_TABLE, true);
			if(!empty($_uws)){
				$this->workSpaceTmp = implode(',', $_uws);
				$this->workspaces[TEMPLATES_TABLE] = $_uws;
			}
		}
		if(empty($this->workSpaceNav)){
			$_uws = get_ws(NAVIGATION_TABLE, true);
			if(!empty($_uws)){
				$this->workSpaceNav = implode(',', $_uws);
				$this->workspaces[NAVIGATION_TABLE] = $_uws;
			}
		}

		if(defined('OBJECT_FILES_TABLE') && empty($this->workSpaceObj)){
			$_uws = get_ws(OBJECT_FILES_TABLE, true);
			if(!empty($_uws)){
				$this->workSpaceObj = implode(',', $_uws);
				$this->workspaces[OBJECT_FILES_TABLE] = $_uws;
			}
		}
		if(defined('NEWSLETTER_TABLE') && empty($this->workSpaceNwl)){
			$_uws = get_ws(NEWSLETTER_TABLE, true);
			if(!empty($_uws)){
				$this->workSpaceNwl = implode(',', $_uws);
				$this->workspaces[NEWSLETTER_TABLE] = $_uws;
			}
		}
	}

	function getPreferenceSlotsFromDB(){
		$tmp = self::readPrefs($this->ID, $this->DB_WE);
		$this->Preferences = array_intersect_key($tmp, array_flip($this->preference_slots));
	}

	function setPreference($name, $value){
		if(in_array($name, $this->preference_slots)){
			$this->Preferences[$name] = $value;
		}
	}

	function savePreferenceSlotsInDB($isnew = false){
		if($this->Type != self::TYPE_USER){
			return;
		}

		$this->ModDate = time();
		$updt = array('userID' => intval($this->ID));
		foreach($this->preference_slots as $fieldName){
			switch($fieldName){
				case 'editorFontsize':
				case 'editorFontname':
					if($this->Preferences['editorFont'] != '1'){
						$this->Preferences[$fieldName] = '-1';
					}
				default:
					$updt[$fieldName] = $this->Preferences[$fieldName];
			}
		}
		if($isnew){
			$updt['userID'] = intval($this->ID);
			//$updt['FileFilter'] = '0';
			$updt['openFolders_tblFile'] = '';
			$updt['openFolders_tblTemplates'] = '';
			$updt['DefaultTemplateID'] = '0';
		}

		self::writePrefs(intval($this->ID), $this->DB_WE, $updt);
		$_SESSION["prefs"] = ($_SESSION["prefs"]["userID"] == intval($this->ID) ? self::readPrefs(intval($this->ID), $this->DB_WE) : $_SESSION["prefs"]);
	}

	function rememberPreference($settingvalue, $settingname){
		$save_javascript = '';
		if(isset($settingvalue) && ($settingvalue != null)){
			switch($settingname){
				case 'Language'://FIXME: reload corrupt. fix when needless frames are eliminated
					$_SESSION['prefs']['Language'] = $settingvalue;

					if($settingvalue != $GLOBALS['WE_LANGUAGE']){
						$save_javascript .= "
if (top.frames[0]) {
	top.frames[0].location.reload();
}

if (parent.frames[0]) {
	parent.frames[0].location.reload();
}

// Tabs Module User
if (top.content.editor.edheader) {
	top.content.editor.edheader.location = top.content.editor.edheader.location +'?tab='+top.content.editor.edheader.activeTab;
}

// Editor Module User
if (top.content.editor.edbody) {
	top.content.editor.edbody.location = top.content.editor.edbody.location +'?tab=" . we_base_request::_(we_base_request::INT, 'tab', 0) . "&perm_branch='+top.content.editor.edbody.opened_group;
}

// Save Module User
if (top.content.editor.edfooter) {
	top.content.editor.edfooter.location.reload();
}
if (top.opener.top.header) {
	top.opener.top.header.location.reload();
}

// reload all frames of an editor
// reload current document => reload all open Editors on demand
var _usedEditors =  WE().layout.weEditorFrameController.getEditorsInUse();
for (frameId in _usedEditors) {

	if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
		_usedEditors[frameId].setEditorReloadAllNeeded(true);
		_usedEditors[frameId].setEditorIsActive(true);

	} else {
		_usedEditors[frameId].setEditorReloadAllNeeded(true);
	}
}
_multiEditorreload = true;
";
					}
					break;
				case 'BackendCharset'://FIXME: reload corrupt. fix when needless frames are eliminated
					$_SESSION['prefs']['BackendCharset'] = $settingvalue;

					if($settingvalue != $GLOBALS['WE_BACKENDCHARSET']){
						$save_javascript .= "
if (top.frames[0]) {
	top.frames[0].location.reload();
}

if (parent.frames[0]) {
	parent.frames[0].location.reload();
}

// Tabs Module User
if (top.content.editor.edheader) {
	top.content.editor.edheader.location = top.content.editor.edheader.location +'?tab='+top.content.editor.edheader.activeTab;
}

// Editor Module User
if (top.content.editor.edbody) {
	top.content.editor.edbody.location = top.content.editor.edbody.location +'?tab=" . we_base_request::_(we_base_request::INT, 'tab', 0) . "&perm_branch='+top.content.editor.edbody.opened_group;
}

// Save Module User
if (top.content.editor.edfooter) {
	top.content.editor.edfooter.location.reload();
}
if (top.opener.top.header) {
	top.opener.top.header.location.reload();
}

// reload all frames of an editor
// reload current document => reload all open Editors on demand
var _usedEditors =  WE().layout.weEditorFrameController.getEditorsInUse();
for (frameId in _usedEditors) {

	if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
		_usedEditors[frameId].setEditorReloadAllNeeded(true);
		_usedEditors[frameId].setEditorIsActive(true);

	} else {
		_usedEditors[frameId].setEditorReloadAllNeeded(true);
	}
}
_multiEditorreload = true;";
					}
					break;

				case 'seem_start_type':
					switch($settingvalue){
						case 'cockpit':
							$_SESSION['prefs']['seem_start_file'] = 0;
							$_SESSION['prefs']['seem_start_type'] = 'cockpit';
							break;
						case 'object':
							$_SESSION['prefs']['seem_start_file'] = we_base_request::_(we_base_request::INT, 'seem_start_object', 0);
							$_SESSION['prefs']['seem_start_type'] = 'object';
							break;
						case 'weapp':
							$_SESSION['prefs']['seem_start_weapp'] = we_base_request::_(we_base_request::STRING, 'seem_start_weapp', '');
							$_SESSION['prefs']['seem_start_type'] = 'weapp';
							break;
						case 'document':
							$_SESSION['prefs']['seem_start_file'] = we_base_request::_(we_base_request::INT, 'seem_start_document', 0);
							$_SESSION['prefs']['seem_start_type'] = 'document';
							break;
						default:
							$_SESSION['prefs']['seem_start_file'] = 0;
							$_SESSION['prefs']['seem_start_type'] = '0';
					}
					break;

				case 'sizeOpt':
					if($settingvalue == 0){
						$_SESSION['prefs']['weWidth'] = 0;
						$_SESSION['prefs']['weHeight'] = 0;
						$_SESSION['prefs']['sizeOpt'] = 0;
					} else if(($settingvalue == 1) && (we_base_request::_(we_base_request::INT, 'weWidth')) && we_base_request::_(we_base_request::INT, 'weHeight')){
						$_SESSION['prefs']['sizeOpt'] = 1;
					}
					break;

				case 'weWidth':
					if($_SESSION['prefs']['sizeOpt'] == 1){
						$_generate_java_script = false;

						if($_SESSION['prefs']['weWidth'] != $settingvalue){
							$_generate_java_script = true;
						}

						$_SESSION['prefs']['weWidth'] = $settingvalue;

						if($_generate_java_script){
							$height = we_base_request::_(we_base_request::INT, 'weHeight');
							$save_javascript .= '
								top.opener.top.resizeTo(' . $settingvalue . ', ' . $height . ');
								top.opener.top.moveTo((screen.width / 2) - ' . ($settingvalue / 2) . ', (screen.height / 2) - ' . ($height / 2) . ');';
						}
					}
					break;

				case 'weHeight':
					if($_SESSION['prefs']['sizeOpt'] == 1){
						$_SESSION['prefs']['weHeight'] = $settingvalue;
					}
					break;


				case 'editorMode':
					$_SESSION['prefs']['editorMode'] = $settingvalue;
					break;


				case 'editorFont':
					if($settingvalue == 0){
						$_SESSION['prefs']['editorFontname'] = 'none';
						$_SESSION['prefs']['editorFontsize'] = -1;
						$_SESSION['prefs']['editorFont'] = 0;
					} else if(($settingvalue == 1) && we_base_request::_(we_base_request::STRING, 'editorFontname') && we_base_request::_(we_base_request::INT, 'editorFontsize')){
						$_SESSION['prefs']['editorFont'] = 1;
					}

					$save_javascript .= '
if ( !_multiEditorreload ) {
	var _usedEditors =  WE().layout.weEditorFrameController.getEditorsInUse();

		for (frameId in _usedEditors) {

			if ( (_usedEditors[frameId].getEditorEditorTable() == "' . TEMPLATES_TABLE . '" || ' . (defined('OBJECT_TABLE') ? ' _usedEditors[frameId].getEditorEditorTable() == "' . OBJECT_FILES_TABLE . '" || ' : '') . ' _usedEditors[frameId].getEditorEditorTable() == "' . FILE_TABLE . '") &&
				_usedEditors[frameId].getEditorEditPageNr() == ' . we_base_constants::WE_EDITPAGE_CONTENT . ' ) {

				if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
					_usedEditors[frameId].setEditorReloadNeeded(true);
					_usedEditors[frameId].setEditorIsActive(true);
				} else {
					_usedEditors[frameId].setEditorReloadNeeded(true);
				}
			}
		}
}
_multiEditorreload = true;';
					break;

				case 'editorFontname':
					if($_SESSION['prefs']['editorFont'] == 1){
						$_SESSION['prefs']['editorFontname'] = $settingvalue;
					}
					break;

				case 'editorFontsize':
					if($_SESSION['prefs']['editorFont'] == 1){
						$_SESSION['prefs']['editorFontsize'] = $settingvalue;
					}
					break;

				case 'editorSizeOpt':
					if($settingvalue == 0){
						$_SESSION['prefs']['editorWidth'] = 0;
						$_SESSION['prefs']['editorHeight'] = 0;
						$_SESSION['prefs']['editorSizeOpt'] = 0;
					} else if(($settingvalue == 1) && we_base_request::_(we_base_request::INT, 'editorWidth') && we_base_request::_(we_base_request::INT, 'editorHeight')){
						$_SESSION['prefs']['editorSizeOpt'] = 1;
					}

					if(empty($GLOBALS['editor_reloaded'])){
						$GLOBALS['editor_reloaded'] = true;

						$save_javascript .= '
if ( !_multiEditorreload ) {
	var _usedEditors =  WE().layout.weEditorFrameController.getEditorsInUse();

		for (frameId in _usedEditors) {

			if ( (_usedEditors[frameId].getEditorEditorTable() == "' . TEMPLATES_TABLE . '" || ' . (defined('OBJECT_TABLE') ? ' _usedEditors[frameId].getEditorEditorTable() == "' . OBJECT_FILES_TABLE . '" || ' : '') . ' _usedEditors[frameId].getEditorEditorTable() == "' . FILE_TABLE . '") &&
				_usedEditors[frameId].getEditorEditPageNr() == ' . we_base_constants::WE_EDITPAGE_CONTENT . ' ) {

				if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
					_usedEditors[frameId].setEditorReloadNeeded(true);
					_usedEditors[frameId].setEditorIsActive(true);
				} else {
					_usedEditors[frameId].setEditorReloadNeeded(true);
				}
			}
		}
}
_multiEditorreload = true;';
					}
					break;

				case 'editorWidth':
					if($_SESSION['prefs']['editorSizeOpt'] == 1){
						$_SESSION['prefs']['editorWidth'] = $settingvalue;
					}
					break;

				case 'editorHeight':
					if($_SESSION['prefs']['editorSizeOpt'] == 1){
						$_SESSION['prefs']['editorHeight'] = $settingvalue;
					}
					break;

				case 'default_tree_count':
					$_SESSION['prefs']['default_tree_count'] = $settingvalue;
					break;

				case 'force_glossary_check':
					$_SESSION['prefs']['force_glossary_check'] = $settingvalue;
					break;

				case 'force_glossary_action':
					$_SESSION['prefs']['force_glossary_action'] = $settingvalue;
					break;

				case 'cockpit_amount_columns':
					$_SESSION['prefs']['cockpit_amount_columns'] = $settingvalue;
					break;

				default:
					break;
			}
		} else {

			switch($settingname){

				case 'editorFont':
					$_SESSION['prefs']['editorFontname'] = 'none';
					$_SESSION['prefs']['editorFontsize'] = -1;
					$_SESSION['prefs']['editorFont'] = 0;

					$save_javascript .= '
if ( !_multiEditorreload ) {
	var _usedEditors =  WE().layout.weEditorFrameController.getEditorsInUse();

		for (frameId in _usedEditors) {

			if ( (_usedEditors[frameId].getEditorEditorTable() == "' . TEMPLATES_TABLE . '" || ' . (defined('OBJECT_TABLE') ? ' _usedEditors[frameId].getEditorEditorTable() == "' . OBJECT_FILES_TABLE . '" || ' : '') . ' _usedEditors[frameId].getEditorEditorTable() == "' . FILE_TABLE . '") &&
				_usedEditors[frameId].getEditorEditPageNr() == ' . we_base_constants::WE_EDITPAGE_CONTENT . ' ) {

				if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
					_usedEditors[frameId].setEditorReloadNeeded(true);
					_usedEditors[frameId].setEditorIsActive(true);
				} else {
					_usedEditors[frameId].setEditorReloadNeeded(true);
				}
			}
		}
}
_multiEditorreload = true;';
					break;

				case 'force_glossary_check':
					$_SESSION['prefs']['force_glossary_check'] = 0;
					break;

				case 'force_glossary_action':
					$_SESSION['prefs']['force_glossary_action'] = 0;
					break;

				default:
					break;
			}
		}

		return $save_javascript;
	}

	function preserveState($tab, $sub_tab){
		switch($tab){
			case self::TAB_DATA:
				foreach($this->persistent_slots as $name => $type){
					if(($val = we_base_request::_($type, $this->Name . '_' . $name, '__no')) !== '__no'){
						$this->$name = $val;
					}
				}

				if($this->Type == self::TYPE_ALIAS){
					$this->ParentPerms = we_base_request::_(we_base_request::BOOL, $this->Name . '_ParentPerms');
					$this->ParentWs = we_base_request::_(we_base_request::BOOL, $this->Name . '_ParentWs');
					$this->ParentWst = we_base_request::_(we_base_request::BOOL, $this->Name . '_ParentWst');
					$this->ParentWso = we_base_request::_(we_base_request::BOOL, $this->Name . '_ParentWso');
					$this->ParentWsn = we_base_request::_(we_base_request::BOOL, $this->Name . '_ParentWsn');
					$this->ParentWsnl = we_base_request::_(we_base_request::BOOL, $this->Name . '_ParentWsnl');
					$this->ParentWsCust = we_base_request::_(we_base_request::BOOL, $this->Name . '_ParentWsCust');
				}
				break;
			case self::TAB_PERMISSION:
				foreach($this->permissions_slots as $pval){
					foreach($pval as $k => $v){
						$this->setPermission($k, we_base_request::_(we_base_request::BOOL, $this->Name . '_Permission_' . $k));
					}
				}
				$this->ParentPerms = we_base_request::_(we_base_request::BOOL, $this->Name . '_ParentPerms');
				break;
			case self::TAB_WORKSPACES:
				foreach($this->workspaces as $k => $v){
					$obj = $this->Name . '_Workspace_' . $k;
					if(($val = we_base_request::_(we_base_request::RAW, $obj, false, 'id')) !== false){
						$this->workspaces[$k] = $val;
					}
					$obj = $this->Name . '_Workspace_' . $k . '_AddDel';
					if(($val = we_base_request::_(we_base_request::RAW, $obj, '')) != ''){
						if($val === 'new'){//add
							$this->workspaces[$k][] = 0;
						} else {
							unset($this->workspaces[$k][$val]);
						}
					}
				}
				if(defined('CUSTOMER_TABLE')){
					$this->workspaces[CUSTOMER_TABLE] = we_customer_abstractFilter::getFilterFromRequest();
				}

				$this->ParentWs = we_base_request::_(we_base_request::BOOL, $this->Name . '_ParentWs');
				$this->ParentWst = we_base_request::_(we_base_request::BOOL, $this->Name . '_ParentWst');
				$this->ParentWso = we_base_request::_(we_base_request::BOOL, $this->Name . '_ParentWso');
				$this->ParentWsn = we_base_request::_(we_base_request::BOOL, $this->Name . '_ParentWsn');
				$this->ParentWsnl = we_base_request::_(we_base_request::BOOL, $this->Name . '_ParentWsnl');
				$this->ParentWsCust = we_base_request::_(we_base_request::BOOL, $this->Name . '_ParentWsCust');
				break;
			case self::TAB_SETTINGS:
				foreach($this->preference_slots as $val){
					switch($val){
						case 'seem_start_file':
						case 'seem_start_type':
						case 'seem_start_weapp':
							$obj = '';
							break;
						default:
							$obj = $this->Name . '_Preference_' . $val;
					}
					$this->setPreference($val, we_base_request::_(we_base_request::RAW_CHECKED, $obj, 0));
				}
				switch(we_base_request::_(we_base_request::STRING, 'seem_start_type')){
					case 'cockpit':
						$this->setPreference('seem_start_file', 0);
						$this->setPreference('seem_start_type', 'cockpit');
						break;
					case 'object':
						$this->setPreference('seem_start_file', we_base_request::_(we_base_request::INT, 'seem_start_object'));
						$this->setPreference('seem_start_type', 'object');
						break;
					case 'weapp':
						$this->setPreference('seem_start_weapp', we_base_request::_(we_base_request::STRING, 'seem_start_weapp'));
						$this->setPreference('seem_start_type', 'weapp');
						break;
					case 'document':
						$this->setPreference('seem_start_file', we_base_request::_(we_base_request::INT, 'seem_start_document'));
						$this->setPreference('seem_start_type', 'document');
						break;
					default:
						$this->setPreference('seem_start_file', 0);
						$this->setPreference('seem_start_type', '0');
				}
				break;
		}
		foreach($this->extensions_slots as $k => $v){
			$this->extensions_slots[$k]->perserve($tab, $sub_tab);
		}
	}

	function checkPermission($perm){
		foreach($this->permissions_slots as $key => $val){
			foreach($val as $key => $val){
				if($key == $perm){
					return ($val ? true : false);
				}
			}
		}
		return false;
	}

	function resetOwnersCreatorModifier(){
		$newID = intval($_SESSION['user']['ID']);
		$this->ID = intval($this->ID);
		$this->DB_WE->query('UPDATE ' . FILE_TABLE . " SET Owners=REPLACE(Owners,'," . $this->ID . ",',',')");
		$this->DB_WE->query('UPDATE ' . FILE_TABLE . " SET Owners='' WHERE Owners=','");
		$this->DB_WE->query('UPDATE ' . TEMPLATES_TABLE . " SET Owners=REPLACE(Owners,'," . $this->ID . ",',',')");
		$this->DB_WE->query('UPDATE ' . TEMPLATES_TABLE . " SET Owners='' WHERE Owners=','");
		$this->DB_WE->query('UPDATE ' . FILE_TABLE . " SET CreatorID=$newID WHERE CreatorID=" . $this->ID);
		$this->DB_WE->query('UPDATE ' . TEMPLATES_TABLE . " SET CreatorID=$newID WHERE CreatorID=" . $this->ID);
		$this->DB_WE->query('UPDATE ' . FILE_TABLE . " SET ModifierID=$newID WHERE ModifierID=" . $this->ID);
		$this->DB_WE->query('UPDATE ' . TEMPLATES_TABLE . " SET ModifierID=$newID WHERE ModifierID=" . $this->ID);
		$this->DB_WE->query('UPDATE ' . USER_TABLE . " SET CreatorID=$newID WHERE CreatorID=" . $this->ID);
		$this->DB_WE->query('UPDATE ' . USER_TABLE . ' SET ModifierID=' . $newID . ' WHERE ModifierID=' . $this->ID);

		if(defined('OBJECT_TABLE')){
			$this->DB_WE->query('UPDATE ' . OBJECT_TABLE . " SET Owners=REPLACE(Owners,'," . $this->ID . ",',',')");
			$this->DB_WE->query('UPDATE ' . OBJECT_TABLE . " SET Owners='' WHERE Owners=','");
			$this->DB_WE->query('UPDATE ' . OBJECT_FILES_TABLE . " SET Owners=REPLACE(Owners,'," . $this->ID . ",',',')");
			$this->DB_WE->query('UPDATE ' . OBJECT_FILES_TABLE . " SET Owners='' WHERE Owners=','");
			$this->DB_WE->query('UPDATE ' . OBJECT_TABLE . " SET CreatorID=$newID WHERE CreatorID=" . $this->ID);
			$this->DB_WE->query('UPDATE ' . OBJECT_FILES_TABLE . " SET CreatorID=$newID WHERE CreatorID=" . $this->ID);
			$this->DB_WE->query('UPDATE ' . OBJECT_TABLE . " SET ModifierID=$newID WHERE ModifierID=" . $this->ID);
			$this->DB_WE->query('UPDATE ' . OBJECT_FILES_TABLE . " SET ModifierID=$newID WHERE ModifierID=" . $this->ID);
		}
	}

	function deleteMe(){
		foreach(array_keys($this->extensions_slots) as $k){
			$this->extensions_slots[$k]->delete();
		}
		$this->ID = intval($this->ID);
		switch($this->Type){
			case self::TYPE_USER:
				$this->DB_WE->query('DELETE FROM ' . USER_TABLE . ' WHERE ID=' . $this->ID);
				$this->DB_WE->query('DELETE FROM ' . PREFS_TABLE . ' WHERE userID=' . $this->ID);
				$this->resetOwnersCreatorModifier();
				$this->removeAccount();
				return true;
			case self::TYPE_USER_GROUP:
				$this->DB_WE->query('SELECT ID FROM ' . USER_TABLE . ' WHERE ParentID=' . $this->ID);
				while($this->DB_WE->next_record()){
					$tmpobj = new we_users_user();
					$tmpobj->initFromDB($this->DB_WE->f('ID'));
					$tmpobj->deleteMe();
				}
				$this->DB_WE->query('DELETE FROM ' . USER_TABLE . ' WHERE ID=' . $this->ID);
				$this->resetOwnersCreatorModifier();
				return true;
			case self::TYPE_ALIAS:
				$this->DB_WE->query('DELETE FROM ' . USER_TABLE . ' WHERE ID=' . $this->ID);
				return true;
		}
		return false;
	}

	function isLastAdmin(){
		$this->ID = intval($this->ID);

		if(f('SELECT 1 FROM ' . USER_TABLE . " WHERE Permissions LIKE ('%\"ADMINISTRATOR\";%') AND ID!=" . $this->ID . ' LIMIT 1', '', $this->DB_WE)){
			return false;
		}
		if(($id = intval(f('SELECT ID FROM ' . USER_TABLE . " WHERE Permissions LIKE ('%\"ADMINISTRATOR\";s:1:\"1\";%') AND ID!=" . $this->ID, '', $this->DB_WE)))){
			echo $id . we_html_element::htmlBr();
			return false;
		}

		return true;
	}

	function getPath($id = 0){
		$db_tmp = new DB_WE();
		$path = '';
		if($id == 0){
			$id = intval($this->ParentID);
			$path = $db_tmp->escape($this->username);
		}
		$foo = getHash('SELECT username,ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($id), $db_tmp);
		$path = '/' . (isset($foo['username']) ? $foo['username'] : '') . $path;
		$pid = isset($foo['ParentID']) ? $foo['ParentID'] : '';
		while($pid > 0){
			$db_tmp->query('SELECT username,ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($pid));
			while($db_tmp->next_record()){
				$path = '/' . $db_tmp->f('username') . $path;
				$pid = $db_tmp->f('ParentID');
			}
		}
		return $path;
	}

	public static function getAllPermissions($uid, $onlyParent = false){
		$user_permissions = array();

		$db = $GLOBALS['DB_WE'];
		$db_tmp = new DB_WE();
		$db->query('SELECT ParentID,' . ($onlyParent ? '1 AS ' : '') . ' ParentPerms,' . ($onlyParent ? '"a:0:{}" AS ' : '') . 'Permissions,Alias FROM ' . USER_TABLE . ' WHERE ID=' . intval($uid) . ($onlyParent ? '' : ' OR Alias=' . intval($uid)));
		while($db->next_record(MYSQL_ASSOC)){
			if($db->f('Alias') != $uid){
				$group_permissions = we_unserialize($db->f('Permissions'));
				foreach($group_permissions as $key => $val){
					$user_permissions[$key] = (isset($user_permissions[$key]) ? $user_permissions[$key] : 0) | $group_permissions[$key];
				}
			}
			$lpid = $db->f('ParentID');
			if($db->f('ParentPerms')){
				while($lpid){
					$db_tmp->query('SELECT ParentID,ParentPerms,Permissions FROM ' . USER_TABLE . ' WHERE ID=' . intval($lpid));
					if($db_tmp->next_record(MYSQL_ASSOC)){
						$group_permissions = we_unserialize($db_tmp->f('Permissions'));
						foreach($group_permissions as $key => $val){
							$user_permissions[$key] = (isset($user_permissions[$key]) ? $user_permissions[$key] : 0) | $group_permissions[$key];
						}
						$lpid = ($db_tmp->f('ParentPerms') ? $db_tmp->f('ParentID') : 0);
						continue;
					}
					$lpid = 0;
				}
			}
		}
		/* 		if(!$onlyParent && !array_filter($user_permissions)){
		  t_e('error reading user permissions! Check parent permissions & resave parent folders! UID: ' . $uid, $user_permissions);
		  } */
		return (!empty($user_permissions['ADMINISTRATOR']) ? array('ADMINISTRATOR' => 1) : array_filter($user_permissions));
	}

	/**
	 * LAYOUT FUNCTIONS
	 */
	function formDefinition($tab, $perm_branch){
		$yuiSuggest = & weSuggest::getInstance();
		switch($tab){
			case self::TAB_DATA:
				return weSuggest::getYuiFiles() .
					$this->formGeneralData();
			case self::TAB_PERMISSION:
				return $this->formPermissions($perm_branch);
			case self::TAB_WORKSPACES:
				return weSuggest::getYuiFiles() .
					$this->formWorkspace();
			case self::TAB_SETTINGS:
				return $this->formPreferences($perm_branch);
		}
		foreach(array_keys($this->extensions_slots) as $k){
			return $this->extensions_slots[$k]->formDefinition($tab, $perm_branch);
		}
		return $this->formGeneralData();
	}

	function formGeneralData(){
		switch($this->Type){
			case self::TYPE_USER:
				return $this->formUserData();
			case self::TYPE_USER_GROUP:
				return $this->formGroupData();
			case self::TYPE_ALIAS:
				return $this->formAliasData();
		}
	}

	function formGroupData(){
		$_tableObj = new we_html_table(array(), 3, 1);

		$_username = $this->getUserfield("username", "group_name", "text", 255, false, 'id="yuiAcInputPathName" onblur="parent.frames[0].weTabs.setTitlePath(this.value);"');
		$_description = '<textarea name="' . $this->Name . '_Description" cols="25" rows="5" style="width:560px" class="defaultfont" onchange="top.content.setHot();">' . $this->Description . '</textarea>';
		$parent_name = f('SELECT Path FROM ' . USER_TABLE . ' WHERE ID=' . intval($this->ParentID), 'Path', $this->DB_WE);

		$parent_name = ($parent_name ? : '/');

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId('PathGroup');
		$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER);
		$yuiSuggest->setInput($this->Name . '_ParentID_Text', $parent_name, array('onchange' => 'top.content.setHot()'));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(false);
		$yuiSuggest->setResult($this->Name . '_ParentID', $this->ParentID);
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setTable(USER_TABLE);
		$yuiSuggest->setWidth(450);
		$yuiSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_users_selector','document.we_form." . $this->Name . "_ParentID.value','document.we_form." . $this->Name . "_ParentID_Text.value','group',document.we_form." . $this->Name . "_ParentID.value);"));

		$weAcSelector = $yuiSuggest->getHTML();

		$_tableObj->setCol(0, 0, null, $_username);
		$_tableObj->setCol(1, 0, array('style' => 'padding-top:5px;'), we_html_tools::htmlFormElementTable($_description, g_l('modules_users', '[description]')));
		$_tableObj->setCol(2, 0, array('style' => 'padding-top:10px;'), we_html_tools::htmlFormElementTable($weAcSelector, g_l('modules_users', '[group]')));

		$content = '<select name="' . $this->Name . '_Users" size="8" style="width:560px" onchange="if(this.selectedIndex > -1){WE().layout.button.switch_button_state(document, \'edit\', \'enabled\');}else{WE().layout.button.switch_button_state(document, \'edit\', \'disabled\');}" ondblclick="top.content.we_cmd(\'display_user\',document.we_form.' . $this->Name . '_Users.value)">';
		if($this->ID){
			$this->DB_WE->query('SELECT ID,username,Text,Type FROM ' . USER_TABLE . ' WHERE Type IN (0,2) AND ParentID=' . intval($this->ID));
			while($this->DB_WE->next_record()){
				$content.='<option value="' . $this->DB_WE->f('ID') . '">' . (($this->DB_WE->f("Type") == 2) ? "[" : "") . $this->DB_WE->f("Text") . (($this->DB_WE->f("Type") == 2) ? "]" : "");
			}
		}

		$content.='</select><br/><br/>' . we_html_button::create_button(we_html_button::EDIT, "javascript:we_cmd('display_user',document.we_form." . $this->Name . "_Users.value)", true, 0, 0, "", "", true, false);

		$parts = array(
			array(
				'headline' => g_l('modules_users', '[group_data]'),
				'html' => $_tableObj->getHtml(),
				'space' => 120
			),
			array(
				'headline' => g_l('modules_users', '[user]'),
				'html' => $content,
				'space' => 120
			)
		);

		return we_html_multiIconBox::getHTML('', $parts, 30);
	}

	function getUserfield($name, $lngkey, $type = 'text', $maxlen = 255, $noNull = false, $attribs = ''){
		$val = $this->$name;
		if($noNull && !$val){
			$val = '';
		}
		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($this->Name . '_' . $name, 20, $val, $maxlen, 'onchange="top.content.setHot()" ' . $attribs, $type, 240), g_l('modules_users', '[' . $lngkey . ']'));
	}

	function formUserData(){
		$_description = '<textarea name="' . $this->Name . '_Description" cols="25" rows="5" style="width:520px" class="defaultfont" onchange="top.content.setHot();">' . $this->Description . '</textarea>';

		$_tableObj = new we_html_table(array('class' => 'withBigSpace'), 12, 2, array(
			array(array(null, $this->getUserfield('Salutation', 'salutation'))),
			array(
				array(null, $this->getUserfield('First', 'first_name')),
				array(null, $this->getUserfield('Second', 'second_name'))
			),
			array(
				array(null, $this->getUserfield('Address', 'address')),
				array(null, $this->getUserfield('HouseNo', 'houseno'))
			),
			array(
				array(null, $this->getUserfield('PLZ', 'PLZ', 'text', 16, true)),
				array(null, $this->getUserfield('City', 'city'))
			),
			array(
				array(null, $this->getUserfield('State', 'state')),
				array(null, $this->getUserfield('Country', 'country'))
			),
			array(
				array(null, $this->getUserfield('Tel_preselection', 'tel_pre')),
				array(null, $this->getUserfield('Telephone', 'telephone'))
			),
			array(
				array(null, $this->getUserfield('Fax_preselection', 'fax_pre')),
				array(null, $this->getUserfield('Fax', 'fax'))
			),
			array(
				array(null, $this->getUserfield('Handy', 'mobile')),
				array(null, $this->getUserfield('Email', 'email'))
			),
			array(
				array(array('colspan' => 2), we_html_tools::htmlFormElementTable($_description, g_l('modules_users', '[description]')))
			)
		));

		$parts = array(
			array(
				'headline' => g_l('modules_users', '[general_data]'),
				'html' => $_tableObj->getHtml(),
				'space' => 120
			)
		);


		$_username = $this->getUserfield('username', 'username', 'text', 255, false, 'id="yuiAcInputPathName" onblur="parent.frames[0].weTabs.setTitlePath(this.value);"');

		$_password = (!empty($_SESSION['user']['ID']) && $_SESSION['user']['ID'] == $this->ID && !permissionhandler::hasPerm('EDIT_PASSWD') ?
				'****************' :
				'<input type="hidden" name="' . $this->Name . '_clearpasswd" value="' . $this->clearpasswd . '" />' . we_html_tools::htmlTextInput('input_pass', 20, "", 255, 'onchange="top.content.setHot()" autocomplete="off"', 'password', 240));

		$parent_name = f('SELECT Path FROM ' . USER_TABLE . ' WHERE ID=' . intval($this->ParentID), '', $this->DB_WE)? : '/';

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId('PathGroup');
		$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER);
		$yuiSuggest->setInput($this->Name . '_ParentID_Text', $parent_name, array('onchange' => 'top.content.setHot()'));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($this->Name . '_ParentID', $this->ParentID);
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setTable(USER_TABLE);
		$yuiSuggest->setWidth(403);
		$yuiSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_users_selector','document.we_form." . $this->Name . "_ParentID.value','document.we_form." . $this->Name . "_ParentID_Text.value','group',document.we_form." . $this->Name . "_ParentID.value);"));


		$CreatorIDtext = ($this->CreatorID ?
				(($hash = getHash('SELECT username,First,Second FROM ' . USER_TABLE . ' WHERE ID=' . intval($this->CreatorID), $this->DB_WE)) ?
					$hash['username'] . ' (' . $hash['First'] . ' ' . $hash['Second'] . ')' :
					g_l('modules_users', '[lostID]') . $this->CreatorID . g_l('modules_users', '[lostID2]')) :
				'-');

		$ModifierIDtext = ($this->ModifierID ?
				($this->ModifierID == $this->ID ?
					$this->username . ' (' . $this->First . ' ' . $this->Second . ')' :
					(($hash = getHash('SELECT username,First,Second FROM ' . USER_TABLE . ' WHERE ID=' . intval($this->ModifierID), $this->DB_WE)) ?
						$hash['username'] . ' (' . $hash['First'] . ' ' . $hash['Second'] . ')' :
						g_l('modules_users', '[lostID]') . $this->ModifierID . g_l('modules_users', '[lostID2]'))
				) :
				'-');
		$_tableObj = new we_html_table(array(), 5, 2, array(
			array(
				array(array('style' => 'padding-bottom:10px;width:280px;'), $_username),
				array(array('style' => 'width:280px;'), we_html_tools::htmlFormElementTable($_password, g_l('modules_users', '[password]')))
			),
			array(
				array(array('style' => 'padding-bottom:10px;'), we_html_forms::checkboxWithHidden($this->LoginDenied, $this->Name . '_LoginDenied', g_l('modules_users', '[login_denied]'), false, "defaultfont", "top.content.setHot();", ($_SESSION["user"]["ID"] == $this->ID || !permissionhandler::hasPerm("ADMINISTRATOR")))),
				array(array("class" => "defaultfont"), g_l('modules_users', '[lastPing]') . ' ' . ($this->Ping ? : '-'))
			),
			array(
				array(array("colspan" => 2, 'style' => 'padding-bottom:10px;'), we_html_tools::htmlFormElementTable($yuiSuggest->getHTML(), g_l('modules_users', '[group]')))
			),
			array(
				array(array('class' => 'defaultfont'), g_l('modules_users', '[CreatorID]') . ' ' . $CreatorIDtext),
				array(array('class' => 'defaultfont'), g_l('modules_users', '[CreateDate]') . ' ' . (($this->CreateDate) ? date('d.m.Y H:i:s', $this->CreateDate) : '-'))
			),
			array(
				array(array('class' => 'defaultfont'), g_l('modules_users', '[ModifierID]') . ' ' . $ModifierIDtext),
				array(array('class' => 'defaultfont'), g_l('modules_users', '[ModifyDate]') . ' ' . (($this->ModifyDate) ? date('d.m.Y H:i:s', $this->ModifyDate) : '-'))
			),
		));

		$parts[] = array(
			'headline' => g_l('modules_users', '[user_data]'),
			'html' => $_tableObj->getHtml(),
			'space' => 120
		);

		return we_html_multiIconBox::getHTML('', $parts, 30);
	}

	/**
	 * This function outputs the group of selectable user permissions
	 *
	 * @param      $branch                                 string
	 *
	 * @return     string
	 */
	function formPermissions($branch){
		// Set output text
		// Create a object of the class dynamicControls
		//FIXME: change we_html_dynamicControls
		$dynamic_controls = new we_html_dynamicControls();
		// Now we create the overview of the user rights
		$parentPerm = $this->ParentID ? self::getAllPermissions($this->ID, true) : false;
		$content = $dynamic_controls->fold_checkbox_groups($this->permissions_slots, $parentPerm, $this->permissions_main_titles, $this->permissions_titles, $this->Name, $branch, array('administrator'), true, true, 'we_form', 'perm_branch', true, true);

		$javascript = '
function rebuildCheckboxClicked() {
	toggleRebuildPerm(false);
}

function toggleRebuildPerm(disabledOnly) {';
		if(isset($this->permissions_slots['rebuildpermissions']) && is_array($this->permissions_slots['rebuildpermissions'])){

			foreach($this->permissions_slots['rebuildpermissions'] as $pname => $pvalue){
				if($pname != 'REBUILD'){
					$javascript .= '
	if (document.we_form.' . $this->Name . '_Permission_REBUILD && document.we_form.' . $this->Name . '_Permission_' . $pname . ') {
		if(document.we_form.' . $this->Name . '_Permission_REBUILD.checked) {
			document.we_form.' . $this->Name . '_Permission_' . $pname . '.disabled = false;
			if (!disabledOnly) {
				document.we_form.' . $this->Name . '_Permission_' . $pname . '.checked = true;
			}
		} else {
			document.we_form.' . $this->Name . '_Permission_' . $pname . '.disabled = true;
			if (!disabledOnly) {
				document.we_form.' . $this->Name . '_Permission_' . $pname . '.checked = false;
			}
		}
	}';
				} else {
					$handler = '
	if (document.we_form.' . $this->Name . '_Permission_' . $pname . ') {
		document.we_form.' . $this->Name . '_Permission_' . $pname . ".onclick = rebuildCheckboxClicked;
	} else {
		document.we_form." . $this->Name . "_Permission_" . $pname . ".onclick = top.content.setHot();
	}
	toggleRebuildPerm(true);";
				}
			}
		}
		$javascript .= '}';
		if(isset($handler)){
			$javascript .= $handler;
		}

		$parts = array(
			array(
				'headline' => '',
				'html' => $content,
				'space' => 0,
				'noline' => 1
			)
		);

		// js to uncheck all permissions
		$uncheckjs = $checkjs = $defaultjs = '';
		foreach($this->permissions_slots as $gname => $group){
			foreach($group as $pname => $pvalue){
				if($pname != 'ADMINISTRATOR'){
					$uncheckjs .= 'document.we_form.' . $this->Name . '_Permission_' . $pname . '.checked = false;top.content.setHot();';
					$checkjs .= 'document.we_form.' . $this->Name . '_Permission_' . $pname . '.checked = true;top.content.setHot();';
					$defaultjs.='document.we_form.' . $this->Name . '_Permission_' . $pname . '.checked = ' . (!empty($this->permissions_defaults[$gname][$pname]) ? 'true' : 'false') . ';top.content.setHot();';
				}
			}
		}

		$button_uncheckall = we_html_button::create_button('uncheckall', 'javascript:' . $uncheckjs);
		$button_checkall = we_html_button::create_button('checkall', 'javascript:' . $checkjs);
		$button_default = we_html_button::create_button('default', 'javascript:' . $defaultjs);
		$parts[] = array(
			'headline' => '',
			'html' => $button_default . $button_uncheckall . $button_checkall,
			'space' => 0
		);

		// Check if user has right to decide to give administrative rights
		if(permissionhandler::hasPerm('ADMINISTRATOR') && $this->Type == self::TYPE_USER && is_array($this->permissions_slots['administrator'])){
			foreach($this->permissions_slots['administrator'] as $k => $v){
				$content = '
<table class="default" width="500" style="margin-top:5px;">
	<tr><td>' . we_html_forms::checkbox(1, $v, $this->Name . "_Permission_" . $k, $this->permissions_titles['administrator'][$k], false, 'defaultfont', ($k === 'REBUILD' ? 'setRebuidPerms();top.content.setHot();' : 'top.content.setHot();')) . '</td></tr>
</table>';
			}
			$parts[] = array(
				'headline' => '',
				'html' => $content,
				'space' => 0
			);
		}
		if($this->ParentID){
			$parts[] = array(
				'headline' => '',
				'html' => we_html_element::jsElement('
function showParentPerms(show) {
	tmp=document.getElementsByClassName("showParentPerms");
	for( var k=0; k<tmp .length; k++ ) {
		tmp[k].style.display=(show?"inline":"none");
	}
}
showParentPerms(' . ($this->ParentPerms ? 1 : 0) . ');') .
				$this->formInherits('_ParentPerms', $this->ParentPerms, g_l('modules_users', '[inherit]'), 'showParentPerms(this.checked);'),
				'space' => 0
			);
		}

		return we_html_multiIconBox::getHTML('', $parts, 30) . we_html_element::jsElement($javascript);
	}

	function formWorkspace(){
		$parentWsp = self::setEffectiveWorkspaces($this->ID, $this->DB_WE, true);
		$parts = array();
		$content = we_html_element::jsElement('
function addElement(elvalues) {
	elvalues.value="new";
	switchPage(' . self::TAB_WORKSPACES . ');
}

function delElement(elvalues,elem) {
	elvalues.value=elem;
	top.content.setHot();
}
');
		$yuiSuggest = & weSuggest::getInstance();

		foreach($this->workspaces as $k => $v){
			switch($k){
				case TEMPLATES_TABLE:
					$title = g_l('modules_users', '[workspace_templates]');
					$setValue = 'TEMPLATES_TABLE';
					$showParent = $this->ParentWst;
					$content1 = $this->ParentID ? $this->formInherits('_ParentWst', $this->ParentWst, g_l('modules_users', '[inherit_wst]'), 'document.getElementById(\'info' . $setValue . '\').style.display=(this.checked?\'inline\':\'none\');') : '';
					break;
				case NAVIGATION_TABLE:
					$title = g_l('modules_users', '[workspace_navigations]');
					$setValue = 'NAVIGATION_TABLE';
					$showParent = $this->ParentWsn;
					$content1 = $this->ParentID ? $this->formInherits('_ParentWsn', $this->ParentWsn, g_l('modules_users', '[inherit_wsn]'), 'document.getElementById(\'info' . $setValue . '\').style.display=(this.checked?\'inline\':\'none\');') : '';
					break;
				case (defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : 'NEWSLETTER_TABLE'):
					$title = g_l('modules_users', '[workspace_newsletter]');
					$setValue = 'NEWSLETTER_TABLE';
					$showParent = $this->ParentWsnl;
					$content1 = $this->ParentID ? $this->formInherits('_ParentWsnl', $this->ParentWsnl, g_l('modules_users', '[inherit_wsnl]'), 'document.getElementById(\'info' . $setValue . '\').style.display=(this.checked?\'inline\':\'none\');') : '';
					break;
				case (defined('OBJECT_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
					$title = g_l('modules_users', '[workspace_objects]');
					$setValue = 'OBJECT_TABLE';
					$showParent = $this->ParentWso;
					$content1 = $this->ParentID ? $this->formInherits('_ParentWso', $this->ParentWso, g_l('modules_users', '[inherit_wso]'), 'document.getElementById(\'info' . $setValue . '\').style.display=(this.checked?\'inline\':\'none\');') : '';
					break;
				case FILE_TABLE:
					$title = g_l('modules_users', '[workspace_documents]');
					$setValue = 'FILE_TABLE';
					$showParent = $this->ParentWs;
					$content1 = $this->ParentID ? $this->formInherits('_ParentWs', $this->ParentWs, g_l('modules_users', '[inherit_ws]'), 'document.getElementById(\'info' . $setValue . '\').style.display=(this.checked?\'inline\':\'none\');') : '';
					break;
				default:
					continue 2;
			}
			$obj_values = $this->Name . '_Workspace_' . $k . '_AddDel';
			$obj_names = $this->Name . '_Workspace_' . $k;
			//$obj_def_names = $this->Name . '_defWorkspace_' . $k;
			//$content .= '<p>';

			$content1.='<input type="hidden" name="' . $obj_values . '" value="" /><table width="520">';
			foreach($v as $key => $val){
				$value = $val;
				$path = f('SELECT Path FROM ' . $k . ' WHERE ' . $k . '.ID=' . $value, '', $this->DB_WE);
				if(!$path){
					$foo = get_def_ws($k);
					$fooA = makeArrayFromCSV($foo);
					$value = (count($fooA) ? $fooA[0] : 0);
					$path = id_to_path($value);
				}

				$wecmdenc1 = we_base_request::encCmd("document.getElementsByName('" . $obj_names . '[id][' . $key . "]')[0].value");
				$wecmdenc2 = we_base_request::encCmd("document.getElementsByName('" . $obj_names . '[Text][' . $key . "]')[0].value");

				switch($k){
					case (defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : 'NEWSLETTER_TABLE'):

						$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('openNewsletterDirselector',document.getElementsByName('" . $obj_names . "[id][" . $key . "]')[0].value,'" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','" . we_base_request::_(we_base_request::INT, "rootDirID", 0) . "' )");
						break;
					case NAVIGATION_TABLE:

						$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('openNavigationDirselector',document.getElementsByName('" . $obj_names . "[id][" . $key . "]')[0].value,'" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','" . we_base_request::_(we_base_request::INT, "rootDirID", 0) . "' )");
						break;
					default:

						$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.getElementsByName('" . $obj_names . "[id][" . $key . "]')[0].value,'" . $k . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','" . we_base_request::_(we_base_request::INT, "rootDirID", 0) . "' )");
				}

				$yuiSuggest->setAcId('WS' . $k . $key);
				$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER);
				$yuiSuggest->setInput($obj_names . '[Text][' . $key . ']', $path);
				$yuiSuggest->setMaxResults(10);
				$yuiSuggest->setMayBeEmpty(true);
				$yuiSuggest->setResult($obj_names . '[id][' . $key . ']', $value);
				$yuiSuggest->setSelector(weSuggest::DirSelector);
				$yuiSuggest->setTable($k);
				$yuiSuggest->setWidth(290);
				$yuiSuggest->setSelectButton($button, 10);

				$weAcSelector = $yuiSuggest->getHTML();

				$content1.='
<tr><td colspan="2">' . $weAcSelector . '</td>
	<td><div style="position:relative; top:-1px">' . we_html_button::create_button(we_html_button::TRASH, "javascript:delElement(document.we_form." . $obj_values . "," . $key . ");switchPage(" . self::TAB_WORKSPACES . ");", true) . '</td></div>' . '
</tr>';
			}

			$content1.= '<colgroup><col style="width:300px"/><col style="width:110px"/><col style="width:40px"/><col style="width:90px"/></colgroup>
	<tr><td colspan="4">' . we_html_button::create_button(we_html_button::PLUS, "javascript:top.content.setHot();addElement(document.we_form." . $obj_values . ");", true) . '</td></tr>
</table>';

			if($parentWsp[$k]){
				$this->DB_WE->query('SELECT Path FROM ' . $k . ' WHERE ID IN(' . implode(',', $parentWsp[$k]) . ')');
				$parent = implode("<br/>", $this->DB_WE->getAll(true));
			} else {
				$parent = ' - ';
			}

			$parts[] = array(
				'headline' => $title,
				'html' => ($this->ParentID ? '<div id="info' . $setValue . '" style="' . ($showParent ? '' : 'display:none;') . '">' . we_html_tools::htmlAlertAttentionBox($parent, we_html_tools::TYPE_INFO, 600, false) . '</div>' : '') . $content1,
				'space' => 200
			);
		}

		if(defined('CUSTOMER_TABLE')){
			$filter = new we_navigation_customerFilter(we_customer_abstractFilter::FILTER, array(), array(), array(), $this->workspaces[CUSTOMER_TABLE]);
			$view = new we_customer_filterView($filter, 'top.content.setHot();', 520);
			if($parentWsp[CUSTOMER_TABLE]){

				$parent = '';
				foreach($parentWsp[CUSTOMER_TABLE] as $setting){
					$setting = we_unserialize($setting);
					foreach($setting as $cur){
						$parent.=($parent ? $cur['logic'] . ' ' : '') . we_customer_abstractFilter::evalSingleFilterQuery($cur['operation'], $cur['field'], $cur['value']) . "\n";
					}
				}
			} else {
				$parent = ' - ';
			}
			$parts[] = array(
				'headline' => g_l('modules_users', '[workspace_customer]'),
				'html' => ($this->ParentID ?
					'<div id="infoCUSTOMER" style="' . ($this->ParentWsCust ? '' : 'display:none;') . '">' . we_html_tools::htmlAlertAttentionBox($parent, we_html_tools::TYPE_INFO, 600) . '</div>' .
					$this->formInherits('_ParentWsCust', $this->ParentWsCust, g_l('modules_users', '[inherit_cust]'), 'document.getElementById(\'infoCUSTOMER\').style.display=(this.checked?\'inline\':\'none\');') : '') . $view->getFilterCustomers(),
				'space' => 200
			);
		}

		return $content .
			we_html_multiIconBox::getHTML('', $parts, 30);
	}

	function formPreferences($branch = ''){
		//FIXME: change we_html_dynamicControls
		$dynamic_controls = new we_html_dynamicControls();
		$groups = array(
			'glossary' => g_l('prefs', '[tab_glossary]'),
			'ui' => g_l('prefs', '[tab][ui]'),
			//'editor' => g_l('prefs', '[tab][editor]'),
		);

		$titles = $groups;

		$multiboxes = array(
			'glossary' => $this->formPreferencesGlossary(),
			'ui' => $this->formPreferencesUI(),
			//'editor' => $this->formPreferencesEditor(),
		);

		return we_html_multiIconBox::getHTML('', array(
				array(
					'headline' => '',
					'html' => $dynamic_controls->fold_multibox_groups($groups, $titles, $multiboxes, $branch),
					'space' => 0
				)
				), 30);
	}

	function formPreferencesGlossary(){
		$_settings = array();

		// Create checkboxes
		$_table = new we_html_table(array('class' => 'default withSpace'), 2, 1);
//FIXME: where is the difference between force_glossary_check + force_glossary_action?!

		$_table->setCol(0, 0, null, we_html_forms::checkbox(1, $this->Preferences['force_glossary_check'], $this->Name . '_Preference_force_glossary_check', g_l('prefs', '[force_glossary_check]'), 'false', 'defaultfont', "top.content.setHot()"));
		$_table->setCol(1, 0, null, we_html_forms::checkbox(1, $this->Preferences['force_glossary_action'], $this->Name . "_Preference_force_glossary_action", g_l('prefs', '[force_glossary_action]'), "false", "defaultfont", "top.content.setHot()"));

		// Build dialog if user has permission
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			$_settings[] = array('headline' => g_l('prefs', '[glossary_publishing]'), 'html' => $_table->getHtml(), 'space' => 200, 'noline' => 1);
		}

		return $_settings;
	}

	function formPreferencesUI(){
		$_settings = array();
		// LANGUAGE
		//	Look which languages are installed ...
		$_language_directory = dir(WE_INCLUDES_PATH . 'we_language');

		while(false !== ($entry = $_language_directory->read())){
			if($entry != '.' && $entry != '..'){
				if(is_dir(WE_INCLUDES_PATH . 'we_language/' . $entry)){
					$_language[$entry] = $entry;
				}
			}
		}
		global $_languages;

		if(!empty($_language)){ // Build language select box
			$_languages = new we_html_select(array('name' => $this->Name . '_Preference_Language', 'class' => 'weSelect'));
			$myCompLang = (!empty($this->Preferences['Language']) ? $this->Preferences['Language'] : $GLOBALS['WE_LANGUAGE']);

			foreach($_language as $key => $value){
				$_languages->addOption($key, $value);

				// Set selected extension
				if($key == $myCompLang){
					$_languages->selectOption($key);
				} else {
					// do nothing
				}
			}

			// Build dialog
			$_settings[] = array('headline' => g_l('prefs', '[choose_language]'), 'html' => $_languages->getHtml(), 'space' => 200, 'noline' => 1);
		}


		$_charset = new we_html_select(array('name' => $this->Name . '_Preference_BackendCharset', 'class' => 'weSelect', 'onchange' => 'top.content.setHot();'));
		$c = we_base_charsetHandler::getAvailCharsets();
		foreach($c as $char){
			$_charset->addOption($char, $char);
		}
		$myCompChar = (!empty($this->Preferences['BackendCharset']) ? $this->Preferences['BackendCharset'] : $GLOBALS['WE_BACKENDCHARSET']);
		$_charset->selectOption($myCompChar);
		$_settings[] = array(
			'headline' => g_l('prefs', '[choose_backendcharset]'),
			'html' => $_charset->getHtml(),
			'space' => 200
		);

		//AMOUNT Number of Columns
		$_amount = new we_html_select(array('name' => $this->Name . '_Preference_cockpit_amount_columns', 'class' => 'weSelect', 'onchange' => "top.content.setHot();"));
		if(!$this->Preferences['cockpit_amount_columns']){
			$this->Preferences['cockpit_amount_columns'] = 3;
		}
		for($i = 1; $i <= 10; $i++){
			$_amount->addOption($i, $i);
			if($i == $this->Preferences['cockpit_amount_columns']){
				$_amount->selectOption($i);
			}
		}

		$_settings[] = array(
			'headline' => g_l('prefs', '[cockpit_amount_columns]'),
			'html' => $_amount->getHtml(),
			'space' => 200
		);

		//SEEM
		// Generate needed JS
		$js = we_html_element::jsElement("
function select_seem_start() {
	myWindStr=\"WE().util.jsWindow.prototype.find('preferences').wind\";

	if(document.getElementById('seem_start_type').value == 'object') {
		top.opener.top.we_cmd('we_selector_document', document.forms[0].elements.seem_start_object.value, '" . (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : "") . "', myWindStr + '.document.forms[0].elements.seem_start_object.value', myWindStr + '.document.forms[0].elements.seem_start_object_name.value', '', '', '', 'objectFile','objectFile'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ");
	} else {
		top.opener.top.we_cmd('we_selector_document', document.forms[0].elements.seem_start_document.value, '" . FILE_TABLE . "', myWindStr + '.document.forms[0].elements.seem_start_document.value', myWindStr + '.document.forms[0].elements.seem_start_document_name.value', '', '', '', '" . we_base_ContentTypes::WEDOCUMENT . "','objectFile'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");
	}
}

function show_seem_chooser(val) {
	switch(val){
	case 'document':
		if(document.getElementById('seem_start_object')) {
			document.getElementById('seem_start_object').style.display = 'none';
		}
		document.getElementById('seem_start_document').style.display = 'block';
		document.getElementById('seem_start_weapp').style.display = 'none';
		break;
" . (defined('OBJECT_FILES_TABLE') ? "
	case 'object':
		document.getElementById('seem_start_document').style.display = 'none';
		document.getElementById('seem_start_weapp').style.display = 'none';
		document.getElementById('seem_start_object').style.display = 'block';
		break;
" : '') . "
	case 'weapp':
		document.getElementById('seem_start_document').style.display = 'none';
		document.getElementById('seem_start_object').style.display = 'none';
		document.getElementById('seem_start_weapp').style.display = 'block';
		break;
	default:
		document.getElementById('seem_start_document').style.display = 'none';
		document.getElementById('seem_start_weapp').style.display = 'none';
		if(document.getElementById('seem_start_object')) {
			document.getElementById('seem_start_object').style.display = 'none';
		}
		break;
	}
}");

		// Cockpit
		$_document_path = $_object_path = '';
		$_document_id = $_object_id = 0;

		switch($this->Preferences['seem_start_type']){
			default:
				$_seem_start_type = '0';
				break;
			case 'cockpit':
				$_SESSION['prefs']['seem_start_file'] = 0;
				$_seem_start_type = 'cockpit';
				break;
			case 'object':
				$_seem_start_type = 'object';
				if($this->Preferences['seem_start_file'] != 0){
					$_object_id = $this->Preferences['seem_start_file'];
					$_get_object_paths = getPathsFromTable(OBJECT_FILES_TABLE, $GLOBALS['DB_WE'], we_base_constants::FILE_ONLY, $_object_id);

					if(isset($_get_object_paths[$_object_id])){ //	seeMode start file exists
						$_object_path = $_get_object_paths[$_object_id];
					}
				}
				break;
			case 'weapp':
				$_seem_start_type = 'weapp';
				break;
			// Document
			case 'document':
				$_seem_start_type = 'document';
				if($this->Preferences['seem_start_file'] != 0){
					$_document_id = $this->Preferences['seem_start_file'];
					$_get_document_paths = getPathsFromTable(FILE_TABLE, $GLOBALS['DB_WE'], we_base_constants::FILE_ONLY, $_document_id);

					if(isset($_get_document_paths[$_document_id])){ //	seeMode start file exists
						$_document_path = $_get_document_paths[$_document_id];
					}
				}
		}

		$_start_type = new we_html_select(array('name' => 'seem_start_type', 'class' => 'weSelect', 'id' => 'seem_start_type', 'onchange' => "show_seem_chooser(this.value); top.content.setHot();"));
		$_start_type->addOption(0, '-');
		$_start_type->addOption('cockpit', g_l('prefs', '[seem_start_type_cockpit]'));
		$_start_type->addOption('document', g_l('prefs', '[seem_start_type_document]'));
		if(defined('OBJECT_FILES_TABLE')){
			$_start_type->addOption('object', g_l('prefs', '[seem_start_type_object]'));
		}

		//weapp

		$_start_weapp = new we_html_select(array('name' => 'seem_start_weapp', 'class' => 'weSelect', 'id' => 'seem_start_weapp', 'onchange' => 'top.content.setHot();'));
		$_tools = we_tool_lookup::getAllTools(true, false);
		foreach($_tools as $_tool){
			if(!$_tool['appdisabled'] && permissionhandler::hasPerm($_tool['startpermission'])){
				$_start_weapp->addOption($_tool['name'], $_tool['text']);
			}
		}
		if($_start_weapp->getOptionNum()){
			$_start_type->addOption('weapp', g_l('prefs', '[seem_start_type_weapp]'));
		}

		$_start_type->selectOption($_seem_start_type);
		$_start_weapp->selectOption($this->Preferences['seem_start_weapp']);

		$_seem_weapp_chooser = we_html_element::htmlSpan(array('id' => 'seem_start_weapp', 'style' => 'display:none'), $_start_weapp->getHtml());


		// Build SEEM select start document chooser
		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId('Doc');
		$yuiSuggest->setContentType(implode(',', array(we_base_ContentTypes::FOLDER, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML, we_base_ContentTypes::JS, we_base_ContentTypes::CSS, we_base_ContentTypes::APPLICATION, we_base_ContentTypes::QUICKTIME)));
		$yuiSuggest->setInput('seem_start_document_name', $_document_path);
		$yuiSuggest->setMaxResults(20);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('seem_start_document', $_document_id);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setWidth(191);
		$yuiSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, 'javascript:select_seem_start()', true, 100, 22, '', '', false, false), 10);
		$yuiSuggest->setContainerWidth(299);

		$_seem_document_chooser = we_html_element::htmlSpan(array('id' => 'seem_start_document', 'style' => 'display:none'), $yuiSuggest->getHTML());

		// Build SEEM select start object chooser
		$yuiSuggest->setAcId('Obj');
		$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::OBJECT_FILE);
		$yuiSuggest->setInput('seem_start_object_name', $_object_path);
		$yuiSuggest->setMaxResults(20);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('seem_start_object', $_object_id);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		if(defined('OBJECT_FILES_TABLE')){
			$yuiSuggest->setTable(OBJECT_FILES_TABLE);
		}
		$yuiSuggest->setWidth(191);
		$yuiSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, 'javascript:select_seem_start()', true, 100, 22, '', '', false, false), 10);
		$yuiSuggest->setContainerWidth(299);

		$_seem_object_chooser = we_html_element::htmlSpan(array('id' => 'seem_start_object', 'style' => 'display:none'), $yuiSuggest->getHTML());

		// Build final HTML code
		$_seem_html = new we_html_table(array('class' => 'default'), 2, 1);
		$_seem_html->setCol(0, 0, array('class' => 'defaultfont'), $_start_type->getHtml());
		$_seem_html->setCol(1, 0, null, $_seem_document_chooser . $_seem_object_chooser . $_seem_weapp_chooser);

		if(permissionhandler::hasPerm('CHANGE_START_DOCUMENT')){
			$_settings[] = array(
				'headline' => g_l('prefs', '[seem_startdocument]'),
				'html' => $js . $_seem_html->getHtml() . we_html_element::jsElement('show_seem_chooser("' . $_seem_start_type . '");'),
				'space' => 200
			);
		}

		// TREE

		$_value_selected = false;
		$_tree_count = $this->Preferences['default_tree_count'];

		$_file_tree_count = new we_html_select(array('name' => $this->Name . '_Preference_default_tree_count', 'class' => 'weSelect', 'onchange' => 'top.content.setHot();'));

		$_file_tree_count->addOption(0, g_l('prefs', '[all]'));
		if(0 == $_tree_count){
			$_file_tree_count->selectOption(0);
			$_value_selected = true;
		}

		for($i = 10; $i < 51; $i+=10){
			$_file_tree_count->addOption($i, $i);

			// Set selected extension
			if($i == $_tree_count){
				$_file_tree_count->selectOption($i);
				$_value_selected = true;
			}
		}

		for($i = 100; $i < 501; $i+=100){
			$_file_tree_count->addOption($i, $i);

			// Set selected extension
			if($i == $_tree_count){
				$_file_tree_count->selectOption($i);
				$_value_selected = true;
			}
		}

		if(!$_value_selected){
			$_file_tree_count->addOption($_tree_count, $_tree_count);
			// Set selected extension
			$_file_tree_count->selectOption($_tree_count);
		}

		$_settings[] = array('headline' => g_l('prefs', '[tree_title]'), 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[tree_count_description]'), we_html_tools::TYPE_INFO) . '<br/>' . $_file_tree_count->getHtml(), 'space' => 200);

		// WINDOW DIMENSIONS

		$_window_max = $_window_specify = false;

		if($this->Preferences['sizeOpt'] == 0){
			$_window_max = true;
		} elseif($this->Preferences['sizeOpt'] == 1){
			$_window_specify = true;
		}

		// Build maximize window
		$_window_max_code = we_html_forms::radiobutton(0, $this->Preferences['sizeOpt'] == 0, $this->Name . '_Preference_sizeOpt', g_l('prefs', '[maximize]'), true, 'defaultfont', "document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].disabled = true;document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].disabled = true;top.content.setHot();");

		// Build specify window dimension
		$_window_specify_code = we_html_forms::radiobutton(1, !($this->Preferences['sizeOpt'] == 0), $this->Name . '_Preference_sizeOpt', g_l('prefs', '[specify]'), true, 'defaultfont', "document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].disabled = false;top.content.setHot();");

		// Create specify window dimension input
		$_window_specify_table = new we_html_table(array('class' => 'default', 'style' => 'margin:5px 0px;'), 2, 2);

		$_window_specify_table->setCol(0, 0, array('class' => 'defaultfont', 'style' => 'padding-left:40px;padding-right:10px;'), g_l('prefs', '[width]') . ':');
		$_window_specify_table->setCol(0, 1, null, we_html_tools::htmlTextInput($this->Name . '_Preference_weWidth', 6, ($this->Preferences['weWidth'] != '' && $this->Preferences['weWidth'] != '0' ? $this->Preferences['weWidth'] : 800), 4, ($this->Preferences['sizeOpt'] == 0 ? "disabled=\"disabled\"" : "") . "onchange='top.content.setHot();'", "text", 60));
		$_window_specify_table->setCol(1, 0, array('class' => 'defaultfont', 'style' => 'padding-left:40px;padding-right:10px;'), g_l('prefs', '[height]') . ':');
		$_window_specify_table->setCol(1, 1, null, we_html_tools::htmlTextInput($this->Name . "_Preference_weHeight", 6, ( ($this->Preferences['weHeight'] != '' && $this->Preferences['weHeight'] != '0') ? $this->Preferences['weHeight'] : 600), 4, ($this->Preferences['sizeOpt'] == 0 ? "disabled=\"disabled\"" : "") . "onchange='top.content.setHot();'", "text", 60));

		// Build apply current window dimension
		$_window_current_dimension_table = '<div style="padding-left:90px;">' .
			we_html_button::create_button("apply_current_dimension", "javascript:top.content.setHot();document.getElementsByName('" . $this->Name . "_Preference_sizeOpt')[1].checked = true;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].value = top.opener.top.window.outerWidth;document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].value =top.opener.top.window.outerHeight;", true, 210) . '</div>';

		// Build final HTML code
		$_window_html = new we_html_table(array('class' => 'default withBigSpace'), 3, 1);
		$_window_html->setCol(0, 0, null, $_window_max_code);
		$_window_html->setCol(1, 0, null, $_window_specify_code . $_window_specify_table->getHtml());
		$_window_html->setCol(2, 0, null, $_window_current_dimension_table);

		// Build dialog
		$_settings[] = array("headline" => g_l('prefs', '[dimension]'), "html" => $_window_html->getHtml(), "space" => 200);

		// Create predefined window dimension buttons
		$_window_predefined_table = new we_html_table(array('class' => 'withBigSpace', 'style' => 'text-align:right'), 2, 1);

		$_window_predefined_table->setCol(0, 0, null, we_html_button::create_button("res_800", "javascript:top.content.setHot();document.getElementsByName('" . $this->Name . "_Preference_sizeOpt')[1].checked = true;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].value = '800';document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].value = '600';", true) .
			we_html_button::create_button("res_1024", "javascript:top.content.setHot();document.getElementsByName('" . $this->Name . "_Preference_sizeOpt')[1].checked = true;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].value = '1024';document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].value = '768';", true));
		$_window_predefined_table->setCol(1, 0, null, we_html_button::create_button("res_1280", "javascript:top.content.setHot();document.getElementsByName('" . $this->Name . "_Preference_sizeOpt')[1].checked = true;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].value = '1280';document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].value = '960';", true) . we_html_button::create_button("res_1600", "javascript:top.content.setHot();document.getElementsByName('" . $this->Name . "_Preference_sizeOpt')[1].checked = true;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_weWidth')[0].value = '1600';document.getElementsByName('" . $this->Name . "_Preference_weHeight')[0].value = '1200';", true));

		// Build dialog
		$_settings[] = array("headline" => g_l('prefs', '[predefined]'), "html" => $_window_predefined_table->getHtml(), "space" => 200);

		return $_settings;
	}

	function formPreferencesEditor(){
		return array();
		//FIXME: this is not correct!
		//Editor Mode
		$_template_editor_mode = new we_html_select(array("class" => "weSelect", "name" => $this->Name . "_Preference_editorMode", "size" => 1, "onchange" => "displayEditorOptions(this.options[this.options.selectedIndex].value);"));
		$_template_editor_mode->addOption('textarea', g_l('prefs', '[editor_plaintext]'));
		$_template_editor_mode->addOption('codemirror2', g_l('prefs', '[editor_javascript2]'));
		$_template_editor_mode->selectOption($this->Preferences['editorMode']);
		$_settings = array(
			array("headline" => g_l('prefs', '[editor_mode]'), "html" => $_template_editor_mode->getHtml(), "space" => 150)
		);
//FIXME: use code from preferences for font-selection
		$_template_fonts = array('Arial', 'Courier', 'Courier New', 'Helvetica', 'Monaco', 'Mono', 'Tahoma', 'Verdana', 'serif', 'sans-serif', 'none');
		$_template_font_sizes = array(8, 9, 10, 11, 12, 14, 16, 18, 24, 32, 48, 72, -1);

		$_template_editor_font_specify = false;
		$_template_editor_font_size_specify = false;

		if($this->Preferences['editorFontname'] != "" && $this->Preferences['editorFontname'] != "none"){
			$_template_editor_font_specify = true;
		}

		if($this->Preferences['editorFontsize'] != "" && $this->Preferences['editorFontsize'] != -1){
			$_template_editor_font_size_specify = true;
		}

		// Build specify font
		$_template_editor_font_specify_code = we_html_forms::checkbox(1, $_template_editor_font_specify, $this->Name . "_Preference_editorFont", g_l('prefs', '[specify]'), true, "defaultfont", "top.content.setHot(); if (document.getElementsByName('" . $this->Name . "_Preference_editorFont')[0].checked) { document.getElementsByName('" . $this->Name . "_Preference_editorFontname')[0].disabled = false;document.getElementsByName('" . $this->Name . "_Preference_editorFontsize')[0].disabled = false; } else { document.getElementsByName('" . $this->Name . "_Preference_editorFontname')[0].disabled = true;document.getElementsByName('" . $this->Name . "_Preference_editorFontsize')[0].disabled = true; }");

		$_template_editor_font_select_box = new we_html_select(array("class" => "weSelect", "name" => $this->Name . "_Preference_editorFontname", "size" => 1, "style" => "width: 90px;", ($_template_editor_font_specify ? "enabled" : "disabled") => ($_template_editor_font_specify ? "enabled" : "disabled"), "onchange" => "top.content.setHot();"));

		foreach($_template_fonts as $tf){
			$_template_editor_font_select_box->addOption($tf, $tf);

			if(!$_template_editor_font_specify){
				if($tf === "Courier New"){
					$_template_editor_font_select_box->selectOption($tf);
				}
			} else {
				if($tf == $this->Preferences['editorFontname']){
					$_template_editor_font_select_box->selectOption($tf);
				}
			}
		}

		$_template_editor_font_sizes_select_box = new we_html_select(array('class' => 'weSelect', 'name' => $this->Name . '_Preference_editorFontsize', "size" => 1, "style" => "width: 90px;", ($_template_editor_font_size_specify ? "enabled" : "disabled") => ($_template_editor_font_size_specify ? "enabled" : "disabled"), "onchange" => "top.content.setHot();"));

		foreach($_template_font_sizes as $tf){
			$_template_editor_font_sizes_select_box->addOption($tf, $tf);

			if(!$_template_editor_font_specify){
				if($tf == 11){
					$_template_editor_font_sizes_select_box->selectOption($tf);
				}
			} else {
				if($tf == $this->Preferences['editorFontsize']){
					$_template_editor_font_sizes_select_box->selectOption($tf);
				}
			}
		}
		// Create specify window dimension input
		$_template_editor_font_specify_table = new we_html_table(array('class' => 'default withSpace', 'style' => 'margin:5px 0px 0px 50px;'), 2, 2);

		$_template_editor_font_specify_table->setCol(0, 0, array("class" => "defaultfont", 'style' => 'padding-right:10px;'), g_l('prefs', '[editor_fontname]') . ":");
		$_template_editor_font_specify_table->setCol(0, 1, null, $_template_editor_font_select_box->getHtml());
		$_template_editor_font_specify_table->setCol(1, 0, array("class" => "defaultfont", 'style' => 'padding-right:10px;'), g_l('prefs', '[editor_fontsize]') . ":");
		$_template_editor_font_specify_table->setCol(1, 1, null, $_template_editor_font_sizes_select_box->getHtml());

		// Build dialog
		$_settings[] = array(
			'headline' => g_l('prefs', '[editor_font]'),
			'html' => $_template_editor_font_specify_code . $_template_editor_font_specify_table->getHtml(),
			'space' => 200
		);

		return $_settings;
	}

	function formAliasData(){
		$alias_text = ($this->ID ? f('SELECT Path FROM ' . USER_TABLE . ' WHERE ID=' . intval($this->Alias), 'Path', $this->DB_WE) : '');
		$parent_text = ($this->ParentID == 0 ? '/' : f('SELECT Path FROM ' . USER_TABLE . ' WHERE ID=' . intval($this->ParentID), 'Path', $this->DB_WE));

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId('PathName');
		$yuiSuggest->setContentType(self::TYPE_USER . ',' . self::TYPE_USER_GROUP); // in USER_TABLE is Type 0 folder, Type 1 user and Type 2 alias. Field ContentType is not setted so in weSelectorQuery is a workaroun for USER_TABLE
		$yuiSuggest->setInput($this->Name . '_Alias_Text', $alias_text, array('onchange' => 'top.content.setHot();'));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(false);
		$yuiSuggest->setResult($this->Name . '_Alias', $this->Alias);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setTable(USER_TABLE);
		$yuiSuggest->setWidth(200);
		$yuiSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_users_selector','document.we_form." . $this->Name . "_Alias.value','document.we_form." . $this->Name . "_Alias_Text.value','noalias',document.we_form." . $this->Name . "_Alias.value)"));

		$weAcSelectorName = $yuiSuggest->getHTML();

		$yuiSuggest->setAcId("PathGroup");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput($this->Name . '_ParentID_Text', $parent_text, array("onchange" => "top.content.setHot();"));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($this->Name . '_ParentID', $this->ParentID);
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setTable(USER_TABLE);
		$yuiSuggest->setWidth(200);
		$yuiSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_users_selector','document.we_form." . $this->Name . "_ParentID.value','document.we_form." . $this->Name . "_ParentID_Text.value','group',document.we_form." . $this->Name . "_ParentID.value)"));

		$weAcSelectorGroup = $yuiSuggest->getHTML();

		$content = '
<table class="default" width="530">
<colgroup><col style="width:170px;"/><col style="width:330px;"/></colgroup>
	<tr>
		<td class="defaultfont">' . g_l('modules_users', '[user]') . ':</td>
		<td>' . $weAcSelectorName . '</td>
	</tr>
	<tr>
		<td colspan="2" style="height:5px;"></td>
	</tr>
	<tr>
		<td class="defaultfont">' . g_l('modules_users', '[group_member]') . ':</td>
		<td>' . $weAcSelectorGroup . '</td>
	</tr>
	<tr>
		<td colspan="2" style="height:1px;"></td>
	</tr>
</table>';

		$parts = array(
			array(
				"headline" => g_l('modules_users', '[alias_data]'),
				"html" => $content,
				"space" => 120
			), array(
				"headline" => g_l('modules_users', '[rights_and_workspaces]'),
				"html" =>
				$this->formInherits("_ParentPerms", $this->ParentPerms, g_l('modules_users', '[inherit]')) .
				$this->formInherits("_ParentWs", $this->ParentWs, g_l('modules_users', '[inherit_ws]')) .
				$this->formInherits("_ParentWst", $this->ParentWst, g_l('modules_users', '[inherit_wst]')),
				"space" => 120
			)
		);

		return we_html_multiIconBox::getHTML('', $parts, 30);
	}

	function formInherits($name, $value, $title, $onClick = ''){
		return '
<table class="default" width="500">
	<tr>
		<td class="defaultfont">' .
			we_html_forms::checkbox(1, ($value ? true : false), $this->Name . $name, $title, '', 'defaultfont', 'top.content.setHot();' . $onClick) . '
	</tr>
</table>';
	}

	function formHeader($tab = self::TAB_DATA){
		switch($this->Type){
			case self::TYPE_USER_GROUP:
				$headline1 = g_l('modules_users', '[group]') . ': ';
				$tabs = array(self::TAB_DATA => 'data', self::TAB_PERMISSION => 'permissions', self::TAB_WORKSPACES => 'workspace');
				break;
			case self::TYPE_ALIAS:
				$tabs = array(self::TAB_DATA => 'data');
				$headline1 = g_l('javaMenu_users', '[menu_alias]') . ': ';
				break;
			case self::TYPE_USER:
			default:
				$tabs = array(self::TAB_DATA => 'data', self::TAB_PERMISSION => 'permissions', self::TAB_WORKSPACES => 'workspace', self::TAB_SETTINGS => 'preferences');
				$headline1 = g_l('javaMenu_users', '[menu_user]') . ': ';
		}

		$we_tabs = new we_tabs();
		foreach($tabs as $key => $val){
			$we_tabs->addTab(new we_tab(g_l('tabs', '[module][' . $val . ']'), ($tab == $key ? we_tab::ACTIVE : we_tab::NORMAL), 'self.setTab(' . $key . ');'));
		}

		$tab_header = we_tabs::getHeader();

		return we_html_element::jsElement('
var activeTab = ' . self::TAB_DATA . ';
function setTab(tab) {
	switch(tab) {
		case ' . self::TAB_DATA . ':
			top.content.editor.edbody.switchPage(' . self::TAB_DATA . ');
			activeTab = ' . self::TAB_DATA . ';
			break;
		case ' . self::TAB_PERMISSION . ':
			if(top.content.editor.edbody.switchPage(' . self::TAB_PERMISSION . ')==false){
				setTimeout(resetTabs,50);
			}
			activeTab = ' . self::TAB_PERMISSION . ';
			break;
		case ' . self::TAB_WORKSPACES . ':
			if(top.content.editor.edbody.switchPage(' . self::TAB_WORKSPACES . ')==false) {
				setTimeout(resetTabs,50);
			}
			activeTab = ' . self::TAB_WORKSPACES . ';
			break;
		case ' . self::TAB_SETTINGS . ':
			if(top.content.editor.edbody.switchPage(' . self::TAB_SETTINGS . ')==false) {
				setTimeout(resetTabs,50);
			}
			activeTab = ' . self::TAB_SETTINGS . ';
			break;
	}
}

function resetTabs(){
		top.content.editor.edbody.document.we_form.tab.value = ' . self::TAB_DATA . ';
		top.content.editor.edheader.tabCtrl.weTabs.setActiveTab(' . self::TAB_DATA . ');
}') .
			$tab_header .
			'<div id="main"><div id="headrow"><b>' . str_replace(" ", "&nbsp;", $headline1) . '&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . str_replace(" ", "&nbsp;", ($this->Path ? : $this->getPath($this->ParentID))) . '</b></span></div>' . $we_tabs->getHTML() . '</div>';
	}

	public static function getUsername($id, we_database_base $db = null){
		$db = $db ? : new DB_WE();
		$user = f('SELECT username FROM ' . USER_TABLE . ' WHERE ID=' . intval($id), '', $db);
		return $user ? : g_l('modules_messaging', '[userid_not_found]');
	}

	public static function getUserID($username, we_database_base $db){
		$uid = f('SELECT ID FROM ' . USER_TABLE . ' WHERE username="' . $db->escape(trim($username)) . '"', '', $db);
		return $uid ? : -1;
	}

	static function filenameNotValid($username){
		return preg_match('|^[A-Z0-9._\-][A-Z0-9.,_\-@]+$|i', $username);
	}

	static function setEffectiveWorkspaces($user, we_database_base $db, $onlyParent = false){
		$workspaces = array(
			FILE_TABLE => array('key' => 'workSpace', 'value' => array(), 'parent' => 0, 'parentKey' => 'ParentWs', 'explodeValue' => true),
			TEMPLATES_TABLE => array('key' => 'workSpaceTmp', 'value' => array(), 'parent' => 0, 'parentKey' => 'ParentWst', 'explodeValue' => true),
			NAVIGATION_TABLE => array('key' => 'workSpaceNav', 'value' => array(), 'parent' => 0, 'parentKey' => 'ParentWsn', 'explodeValue' => true),
		);

		if(defined('OBJECT_FILES_TABLE')){
			$workspaces[OBJECT_FILES_TABLE] = array('key' => 'workSpaceObj', 'value' => array(), 'parent' => 0, 'parentKey' => 'ParentWso', 'explodeValue' => true);
		}

		if(defined('NEWSLETTER_TABLE')){
			$workspaces[NEWSLETTER_TABLE] = array('key' => 'workSpaceNwl', 'value' => array(), 'parent' => 0, 'parentKey' => 'ParentWsnl', 'explodeValue' => true);
		}

		if(defined('CUSTOMER_TABLE')){
			$workspaces[CUSTOMER_TABLE] = array('key' => 'workSpaceCust', 'value' => array(), 'parent' => 0, 'parentKey' => 'ParentWsCust', 'explodeValue' => false);
		}

//FIXME: onlyParent doesn't work correctly
		$fields = array('ParentID');
		foreach($workspaces as $cur){
			$fields[] = $cur['key'];
			$fields[] = $cur['parentKey'];
		}
		$fields = implode(',', $fields);

		$userGroups = array(); //	Get Groups user belongs to.

		$pids = array();
		$db->query('SELECT ' . $fields . ' FROM ' . USER_TABLE . ' WHERE ID=' . intval($user) . ($onlyParent ? '' : ' OR Alias=' . intval($user)));
		while($db->next_record()){
			$pids[] = $db->f('ParentID');
			foreach($workspaces as &$cur){
				$cur['parent'] = $db->f($cur['parentKey']);
				if($onlyParent){//we only need parentID
					continue;
				}
				if($cur['explodeValue']){
					// get workspaces
					$a = explode(',', trim($db->f($cur['key']), ','));
					foreach($a as $v){
						$cur['value'][] = $v;
					}
				} else {
					$cur['value'][] = $db->f($cur['key']);
				}
			}
			unset($cur);
		}

		foreach($pids as $pid){
			while($pid){ //	For each group
				$userGroups[] = $pid;

				if(($row = getHash('SELECT ' . $fields . ' FROM ' . USER_TABLE . ' WHERE ID=' . intval($pid), $db))){
					$pid = $row['ParentID'];
					foreach($workspaces as &$cur){
						if($cur['parent']){
							// get workspaces
							$a = explode(',', trim($row[$cur['key']], ','));
							foreach($a as $v){
								$cur['value'][] = $v;
							}
						}
						$cur['parent'] = $row[$cur['parentKey']];
					}
					unset($cur);
				} else {
					$pid = 0;
				}
			}
		}

		foreach($workspaces as &$cur){
			$cur = array_unique(array_filter($cur['value']));
		}
		unset($cur);
		if($onlyParent){
			return $workspaces;
		}
		$_SESSION['user']['workSpace'] = $workspaces;
		$_SESSION['user']['groups'] = $userGroups; //	order: first is folder with user himself (deepest in tree)

		if(defined('CUSTOMER_TABLE') && $_SESSION['user']['workSpace'][CUSTOMER_TABLE]){
			//setup customer
			$filter = array();
			foreach($_SESSION['user']['workSpace'][CUSTOMER_TABLE] as $cur){
				$filter[] = we_customer_abstractFilter::getQueryFromFilter(we_unserialize($cur));
			}

			//FIXME: this won't hold for alias users
			$_SESSION['user']['workSpace'][CUSTOMER_TABLE] = implode(' AND ', array_filter($filter));
		}
	}

	/**
	 *
	 * @param type $useSalt DB-field
	 * @param type $username DB-field
	 * @param type $storedPassword DB-field!!! //needs to be cause of salt!
	 * @param type $clearPassword //posted password
	 */
	static function comparePasswords($username, $storedPassword, $clearPassword){
		$matches = array();
		$useSalt = (!preg_match('|^\$([^$]{2,4})\$([^$]+)\$(.+)$|', $storedPassword, $matches) ?
				//old md5
				self::SALT_MD5 :
				$matches[1]);

		switch($useSalt){
			default:
			/* unsupported
			 * 			case self::SALT_NONE:
			  $passwd = md5($clearPassword);
			  break; */
			case self::SALT_MD5:
				$passwd = md5($clearPassword . md5($username));
				break;
			case '2y':
				$passwd = crypt($passwd = substr($clearPassword, 0, 64), $storedPassword);
				break;
		}
		return ($passwd === $storedPassword);
	}

	public static function getHashIV($len){
		static $WE_SALTCHARS = './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$salt = '';
		for($i = 0; $i < $len; $i++){
			$tmp_str = str_shuffle($WE_SALTCHARS);
			$salt .= $tmp_str[0];
		}
		return $salt;
	}

	public static function makeSaltedPassword($username, $passwd, $strength = 15){
		$passwd = substr($passwd, 0, 64);
		return crypt($passwd, '$2y$' . sprintf('%02d', $strength) . '$' . self::getHashIV(22));
	}

	static function readPrefs($id, $db, $login = false){
//set defaults
		$ret = array('userID' => $id);
		require_once(WE_INCLUDES_PATH . 'we_editors/we_preferences_config.inc.php');
		foreach($GLOBALS['configs']['user'] as $key => $vals){
			$ret[$key] = $vals[1];
		}
		if($login){
			$db->query('DELETE FROM ' . PREFS_TABLE . ' WHERE `key` NOT IN("' . implode('","', array_keys($GLOBALS['configs']['user'])) . '")');
		}
		$db->query('SELECT `key`,`value` FROM ' . PREFS_TABLE . ' WHERE userID=' . intval($id));
		//read db
		while($db->next_record(MYSQL_ASSOC)){
			$ret[$db->f('key')] = $db->f('value');
		}
		if($login){
			$_SESSION['prefs'] = $ret;
			self::writePrefs($id, $db);
		}
		return $ret;
	}

	/** write settings for a user, all default values are applied before data is written
	 * @id int user id to write settings for
	 * @db socket database connection
	 * @data array optional if empty settings of current session are used.
	 */
	static function writePrefs($id, $db, array $data = array()){
		$id = intval($id);
		if($data){
			$old = array('userID' => $id);
			require_once(WE_INCLUDES_PATH . 'we_editors/we_preferences_config.inc.php');
			foreach($GLOBALS['configs']['user'] as $key => $vals){
				//only write config data, if data is read! otherwise we overwrite some settings
				if(isset($data[$key])){
					$old[$key] = $vals[1];
				}
			}
		} else {
			$old = self::readPrefs($id, $db);
			$data = $_SESSION['prefs'];
		}
		$upd = array();
		foreach($old as $key => $val){
			if($key != 'userID' && (!isset($data[$key]) || $data[$key] != $val)){
				$upd[] = '(' . $id . ',"' . $db->escape($key) . '","' . $db->escape((isset($data[$key]) ? $data[$key] : $val)) . '")';
			}
		}
		if(!empty($upd)){
			$db->query('REPLACE INTO ' . PREFS_TABLE . ' (`userID`,`key`,`value`) VALUES ' . implode(',', $upd));
		}
	}

	/**
	 * @internal
	 * @param type $table
	 * @param type $user
	 */
	public static function logLoginFailed($table, $user){
		$db = $GLOBALS['DB_WE'];
		$db->query('INSERT INTO ' . FAILED_LOGINS_TABLE . ' SET ' . we_database_base::arraySetter(array(
				'UserTable' => $table,
				'Username' => $user,
				'IP' => $_SERVER['REMOTE_ADDR'],
				'Servername' => $_SERVER['SERVER_NAME'],
				'Port' => $_SERVER['SERVER_PORT'],
				'Script' => $_SERVER['SCRIPT_NAME']
		)));
	}

	public static function updateActiveUser(){
		if($_SESSION["user"]["ID"]){
			$GLOBALS['DB_WE']->query("UPDATE " . USER_TABLE . " SET Ping=NOW() WHERE ID=" . $_SESSION["user"]["ID"]);
			$GLOBALS['DB_WE']->query('UPDATE ' . LOCK_TABLE . ' SET lockTime=NOW() + INTERVAL ' . (we_base_constants::PING_TIME + we_base_constants::PING_TOLERANZ) . ' SECOND WHERE UserID=' . intval($_SESSION["user"]["ID"]) . ' AND sessionID="' . session_id() . '"');
		}
	}

}
