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
 * Definition of WebEdition Newsletter Base
 *
 */
class we_newsletter_base{
	const STATUS_ERROR = -1;
	const STATUS_SUCCESS = 0;
	const STATUS_EMAIL_EXISTS = 1;
	const STATUS_EMAIL_INVALID = 2;
	const STATUS_CONFIRM_FAILED = 3;
	const FEMALE_SALUTATION_FIELD = 'female_salutation';
	const MALE_SALUTATION_FIELD = 'male_salutation';
	const EMAIL_REPLACE_TEXT = '###EMAIL###';

	var $db;
	var $table;
	var $persistents = array();

	/**
	 * Default Constructor
	 */
	function __construct(){
		$this->db = new DB_WE();
	}

	/**
	 * Load entry from database
	 *
	 * @param Int $id
	 * @return Boolean
	 */
	function load($id = 0){
		if($id){
			$this->ID = intval($id);
		}
		if(!$this->ID){
			return false;
		}
		$tableInfo = $this->db->metadata($this->table);
		$hash = getHash('SELECT * FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->ID), $this->db);
		if($hash){
			foreach($tableInfo as $cur){
				$fieldName = $cur['name'];
				if(isset($this->persistents[$fieldName])){
					$this->$fieldName = $hash[$fieldName];
				}
			}
		}

		return true;
	}

	/**
	 * save entry in database
	 */
	function save(){
		$sets = $wheres = array();
		foreach(array_keys($this->persistents) as $val){
			$sets[$val] = $this->$val;
		}

		unset($sets['ID']);
		$set = we_database_base::arraySetter($sets);

		if($this->ID){
			$this->db->query('UPDATE ' . $this->db->escape($this->table) . ' SET ' . $set . ' WHERE ID=' . intval($this->ID));
		} else {
			$this->db->query('INSERT INTO ' . $this->db->escape($this->table) . ' SET ' . $set);
			# get ID #
			$this->ID = $this->db->getInsertId();
		}
	}

	/**
	 * delete entry from database
	 */
	function delete(){
		if(!$this->ID){
			return false;
		}
		$this->db->query('DELETE FROM ' . $this->db->escape($this->table) . ' WHERE ID="' . intval($this->ID) . '"');
		return true;
	}

	/**
	 * check email syntax
	 */
	function check_email($email){
		return we_check_email($email);
	}

	/**
	 * check domain
	 */
	function check_domain($email, &$domain){
		$mxhosts = "";

		//$exp="/[[:space:]\<_\.0-9A-Za-z-]+@([0-9a-zA-Z][0-9a-zA-Z-\.]+)(\>)?/";
		//if(preg_match_all($exp,$email,$out,PREG_PATTERN_ORDER)){
		$domain = self::get_domain($email);
		if(!$domain){
			return false;
		}
		if(stripos($_SERVER["SERVER_SOFTWARE"], "IIS") !== false || stripos($_SERVER["SERVER_SOFTWARE"], "Microsoft") !== false || stripos($_SERVER["SERVER_SOFTWARE"], "Windows") !== false || stripos($_SERVER["SERVER_SOFTWARE"], "Win32") !== false){
			return(gethostbyname(trim($domain)) == $domain);
		} else {
			return (getmxrr(trim($domain), $mxhosts));
		}
		//}
	}

	static function get_domain($email){
		$exp = "/[[:space:]\<_\.0-9A-Za-z-]+@([0-9a-zA-Z][0-9a-zA-Z-\.]+)(\>)?/";
		$out = array();
		if(preg_match_all($exp, $email, $out, PREG_PATTERN_ORDER)){
			return $out[1][0];
		}
		return false;
	}

	function getEmailsFromList($emails, $emails_only = 0, $group = 0, array $blocks = array()){
		$arr = explode("\n", $emails);
		if(!$arr){
			return array();
		}
		$ret = array();
		$_default_html = f('SELECT pref_value FROM ' . NEWSLETTER_PREFS_TABLE . ' WHERE pref_name="default_htmlmail";', 'pref_value', new DB_WE());

		foreach($arr as $row){
			if($row != ""){
				$arr2 = explode(",", $row);
				if(count($arr2)){
					$ret[] = ($emails_only ?
							$arr2[0] :
							array($arr2[0], (isset($arr2[1]) && trim($arr2[1]) != '') ? trim($arr2[1]) : $_default_html, isset($arr2[2]) ? trim($arr2[2]) : "", isset($arr2[3]) ? $arr2[3] : "", isset($arr2[4]) ? $arr2[4] : "", isset($arr2[5]) ? $arr2[5] : "", $group, $blocks));
				}
			}
		}

		return $ret;
	}

	function getEmailsFromExtern($files, $emails_only = 0, $group = 0, array $blocks = array()){
		$arr = makeArrayFromCSV($files);
		if(!$arr){
			return array();
		}
		$_default_html = f('SELECT pref_value FROM ' . NEWSLETTER_PREFS_TABLE . ' WHERE pref_name="default_htmlmail"');
		$ret = array();
		foreach($arr as $file){
			if(strpos($file, '..') === false){
				$data = str_replace("\r\n", "\n", we_base_file::load($_SERVER['DOCUMENT_ROOT'] . $file));
				$dataArr = explode("\n", $data);
				if(!empty($dataArr)){
					foreach($dataArr as $value){
						$dat = makeArrayFromCSV($value);
						$_alldat = implode("", $dat);
						if(str_replace(" ", "", $_alldat) === ''){
							continue;
						}
						switch($emails_only){
							case 1:
								$ret[] = $dat[0];
								break;
							case 2:
								$ret[] = array(trim($dat[0]), (isset($dat[1]) && trim($dat[1]) != '') ? trim($dat[1]) : $_default_html, isset($dat[2]) ? trim($dat[2]) : "", isset($dat[3]) ? $dat[3] : "", isset($dat[4]) ? $dat[4] : "", isset($dat[5]) ? $dat[5] : "");
								break;
							default:
								$ret[] = array(trim($dat[0]), (isset($dat[1]) && trim($dat[1]) != '') ? trim($dat[1]) : $_default_html, isset($dat[2]) ? trim($dat[2]) : "", isset($dat[3]) ? $dat[3] : "", isset($dat[4]) ? $dat[4] : "", isset($dat[5]) ? $dat[5] : "", $group, $blocks);
						}
					}
				}
			}
		}

		return $ret;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $files
	 * @param unknown_type $emails_only
	 * @param unknown_type $group
	 * @param unknown_type $blocks
	 * @param int $status (0=all; 1=invalid; 2=valid )
	 * @return unknown
	 */
	function getEmailsFromExtern2($files, $emails_only, $group, array $blocks, $status, &$emailkey){
		$arr = makeArrayFromCSV($files);
		if(!$arr){
			return array();
		}
		$ret = array();
		$countEMails = 0;
		$_default_html = f('SELECT pref_value FROM ' . NEWSLETTER_PREFS_TABLE . ' WHERE pref_name="default_htmlmail"');
		foreach($arr as $file){
			if(strpos($file, '..') === false){
				$data = str_replace("\r\n", "\n", we_base_file::load($_SERVER['DOCUMENT_ROOT'] . $file));
				$dataArr = explode("\n", $data);
				if(!empty($dataArr)){
					foreach($dataArr as $value){
						$dat = makeArrayFromCSV($value);
						$_alldat = implode("", $dat);
						if(str_replace(" ", "", $_alldat) === ''){
							continue;
						}
						$countEMails++;
						if($status == 1 && we_check_email($dat[0])){
							continue;
						} elseif($status == 2 && !we_check_email($dat[0])){
							continue;
						}
						$emailkey[] = $countEMails - 1;
						switch($emails_only){
							case 1:
								$ret[] = $dat[0];
								break;
							case 2:
								$ret[] = array(trim($dat[0]), (isset($dat[1]) && trim($dat[1]) != '') ? trim($dat[1]) : $_default_html, isset($dat[2]) ? trim($dat[2]) : "", isset($dat[3]) ? $dat[3] : "", isset($dat[4]) ? $dat[4] : "", isset($dat[5]) ? $dat[5] : "");
								break;
							default:
								$ret[] = array(trim($dat[0]), (isset($dat[1]) && trim($dat[1]) != '') ? trim($dat[1]) : $_default_html, isset($dat[2]) ? trim($dat[2]) : "", isset($dat[3]) ? $dat[3] : "", isset($dat[4]) ? $dat[4] : "", isset($dat[5]) ? $dat[5] : "", $group, $blocks);
						}
					}
				}
			}
		}

		return $ret;
	}

	function htmlSelectEmailList($name, $values, $size = 1, $selectedIndex = "", $multiple = false, $attribs = "", $compare = "value", $width = "", $cls = "defaultfont"){
		reset($values);
		$ret = '<select class="' . $cls . '" name="' . trim($name) . '" size=' . abs($size) . ' ' . ($multiple ? " multiple" : "") . ($attribs ? " $attribs" : "") . ($width ? ' style="width: ' . $width . 'px"' : '') . '>';
		$selIndex = makeArrayFromCSV($selectedIndex);
		foreach($values as $value => $text){
			$ret .= '<option value="' . oldHtmlspecialchars($value) . '"' . (in_array((($compare === "value") ? $value : $text), $selIndex) ? " selected" : "") . (we_check_email($text) ? ' class="markValid"' : ' class="markNotValid"') . '>' . $text . "</option>";
		}
		$ret .= '</select>';
		return $ret;
	}

}
