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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/modules/weModelBase.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/customer/weDocumentCustomerFilter.class.php');
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_hook/class/weHook.class.php");

/**
 * General Definition of WebEdition Customer
 *
 */
class weCustomer extends weModelBase {

	//properties
	var $ID;
	var $Text;
	var $ParentID;
	var $Icon;
	var $IsFolder;
	var $Path;
	var $Username;
	var $Password;
	var $LoginDenied;
	var $Forename;
	var $Surname;
	var $MemberSince;
	var $LastLogin;
	var $LastAccess;
	var $protected = array('ID', 'ParentID', 'Icon', 'IsFolder', 'Path', 'Text');
	var $properties = array('Username', 'Password', 'Forename', 'Surname', 'LoginDenied', 'MemberSince', 'LastLogin', 'LastAccess', 'AutoLoginDenied', 'AutoLogin');
	var $udates = array('MemberSince', 'LastLogin', 'LastAccess');

	/**
	 * Default Constructor
	 * Can load or create new Customer depends of parameter
	 */
	function weCustomer($customerID = 0) {

		$this->table = CUSTOMER_TABLE;

		weModelBase::weModelBase(CUSTOMER_TABLE);

		$this->MemberSince = time();
		$this->LastLogin = 0;
		$this->LastAccess = 0;

		if ($customerID) {
			$this->ID = $customerID;
			$this->load($customerID);
		}
	}

	function loadPresistents() {
		$this->persistent_slots = array();
		$tableInfo = $this->db->metadata($this->table);
		for ($i = 0; $i < sizeof($tableInfo); $i++) {
			$fname = $tableInfo[$i]["name"];
			$this->persistent_slots[] = $fname;
			if (!isset($this->$fname))
				$this->$fname = '';
		}
	}

	function save() {
		$this->Icon = "customer.gif";
		$this->IsFolder = 0;
		$this->Text = $this->Username;
		$this->Path = "/" . $this->Username;

		if ($this->MemberSince == 0)
			$this->MemberSince = time();

		$s = array();
		foreach ($this->persistent_slots as $key => $val) {
			$s[$key] = $val;
		}

		//FIXME: @deprecated!
		// Start Schnittstelle fuer change-Funktion
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/WE_CUSTOMER_EXTERNAL_FN.php')) {
			include_once($_SERVER['DOCUMENT_ROOT'] . '/WE_CUSTOMER_EXTERNAL_FN.php');
			we_customer_saveFN($s);
		}
		// Ende Schnittstelle fuer change-Funktion
		$hook = new weHook('customer_preSave', '', array('customer'=>$s,'from'=>'management','type'=>($s['ID']?'existing':'new')));
		$ret=$hook->executeHook();

		weModelBase::save();
	}

	/**
	 * delete entry from database
	 */
	function delete() {
		if (weModelBase::delete()) {
			weDocumentCustomerFilter::deleteWebUser($this);
			return true;
		}
		return false;
	}

	function transFieldName($real_name, &$banche) {
		if (ereg(g_l('modules_customer','[other]'), $real_name)) {
			return $real_name;
		}
		$pre = explode("_", $real_name);
		if (($pre[0] != $real_name) && (!in_array($pre[0], $this->protected)) && (!in_array($pre[0], $this->properties))) {
			$banche = $pre[0];
			$field = implode("_", array_slice($pre, 1));
			return $field;
		}
		return $real_name;
	}

	function getBranches(&$banches, &$fixed, &$other, $mysort='') {

		$fixed["ID"] = $this->ID; // Bug Fix #8413 + #8520
		if (isset($this->persistent_slots)) {
			$orderedarray = $this->persistent_slots;
			$sortarray = ($mysort != '' ? makeArrayFromCSV($mysort) : range(0, count($orderedarray) - 1));

			if (count($sortarray) != count($orderedarray)) {

				if (count($sortarray) == count($orderedarray) - 1) {
					$sortarray[] = max($sortarray) + 1;
				} else {
					$sortarray = range(0, count($orderedarray) - 1);
				}
			}
			$orderedarray = array_combine($sortarray, $orderedarray);
			ksort($orderedarray);

			foreach ($orderedarray as $per) {
				$var_value = ((!$this->isnew && isset($this->$per)) ? $var_value=$this->$per : null);

				$field = $this->transFieldName($per, $branche);

				if ($field != $per) {
					$banches[$branche][$field] = $var_value;
				} else if (in_array($per, $this->properties)) {
					$fixed[$per] = $var_value;
				} else if (!in_array($per, $this->protected)) {
					$other[$per] = $var_value;
				}
			}
		}
	}

	function getBranchesNames() {
		$branches = array();
		$common = array();
		$other = array();

		$this->getBranches($branches, $common, $other);

		return array_keys($branches);
	}

	function getFieldsNames($branch, $mysort='') {
		$branches = array();
		$common = array();
		$other = array();

		$this->getBranches($branches, $common, $other, $mysort);

		$arr = array();

		if ($branch == '')
			$branch = g_l('modules_customer','[other]');

		if ($branch == g_l('modules_customer','[common]')) {
			if (is_array($common)) {
				$arr = $common;
			}
		} else if ($branch == g_l('modules_customer','[other]')) {
			if (is_array($common)) {
				$arr = $other;
			}
		} else {
			if (isset($branches[$branch]) && is_array($branches[$branch]))
				$arr = $branches[$branch];
		}

		$ret = array();
		foreach (array_keys($arr) as $b) {
			if ($branch == g_l('modules_customer','[other]'))
				$ret[$b] = $b;
			else
				$ret[$branch . "_" . $b] = $b;
		}
		return $ret;
	}

	function getFieldDbProperties($field_name, $buff=array()) {

		if (empty($buff)) {
			$buff = $this->getFieldsDbProperties();
		}

		foreach ($buff as $b)
			if ($b["Field"] == $field_name)
				return $b;

		return array();
	}

	function getFieldsDbProperties() {
		$ret = array();
		$this->db->query("SHOW COLUMNS FROM " . $this->db->escape($this->table));
		while ($this->db->next_record()) {
			$ret[$this->db->f("Field")] = $this->db->Record;
		}

		return $ret;
	}

	function isInfoDate($field) {
		return in_array($field, $this->udates);
	}

	function isProperty($field) {
		return in_array($field, $this->properties);
	}

	function isProtected($field) {
		return in_array($field, $this->protected);
	}

	function clearSessionVars() {
		if (isset($_SESSION["customer_session"]))
			unset($_SESSION["customer_session"]);
	}

	function customerNameExist($name) {
		$db = new DB_WE();
		return (f('SELECT 1 AS a FROM ' . CUSTOMER_TABLE . ' WHERE Username="' . $db->escape($name) . '"', 'a', $db)==1);
	}

	function fieldExist($field) {
		return in_array($field, $this->persistent_slots);
	}

	function getFieldset() {
		$result = array();
		$fields = $this->getFieldsDbProperties();
		foreach ($fields as $k => $v) {
			if (!$this->isProtected($k))
				$result[] = $k;
		}
		return $result;
	}

	function filenameNotValid() {
		return preg_match('|[/]|i', $this->Username);
	}

}