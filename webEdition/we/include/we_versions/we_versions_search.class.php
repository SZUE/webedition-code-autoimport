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
class we_versions_search{
	public $db;
	public $searchFields = array();
	public $location = array();
	public $search = array();
	public $mode = 0;
	public $order = "version DESC";
	public $anzahl = 25;
	public $searchstart = 0;
	public $height = 1;
	public $where;
	public $version;
	public $View;

	/**
	 *  Constructor for class 'weVersionsSearch'
	 */
	public function __construct($view = null){

		$this->db = new DB_WE();
		$this->version = new we_versions_version();
		$this->View = $view ? : new we_versions_view();
	}

	/**
	 * @abstract make WHERE-Statement for mysql-SELECT
	 */
	function getWhere($model){
		$where = "";
		$modConst = array();
		$currentSearch = $model->getProperty('currentSearch');
		$currentSearchFields = $model->getProperty('currentSearchFields');
		$currentLocation = $model->getProperty('currentLocation');

		if(($model->mode)){
			if(count($currentSearchFields)){
				foreach($currentSearchFields as $k => $v){
					switch($v){
						case "modifierID":
							if(isset($currentSearch[$k])){
								$where .= ' AND ' . $v . '="' . escape_sql_query($currentSearch[$k]) . '"';
							}
							break;
						case "status":
							if(isset($currentSearch[$k])){
								$where .= ' AND ' . $v . '="' . escape_sql_query($currentSearch[$k]) . '"';
							}
							break;
						case "timestamp":
							if(isset($currentLocation[$k]) && isset($currentSearch[$k]) && $currentSearch[$k]){

								$date = explode('.', $currentSearch[$k]);
								$day = $date[0];
								$month = $date[1];
								$year = $date[2];
								$timestampStart = mktime(0, 0, 0, $month, $day, $year);
								$timestampEnd = mktime(23, 59, 59, $month, $day, $year);

								switch($currentLocation[$k]){
									case "IS":
										$where .= ' AND ' . $v . ' BETWEEN ' . intval($timestampStart) . " AND " . intval($timestampEnd);
										break;
									case "LO":
										$where .= ' AND ' . $v . $currentLocation[$k] . ' ' . intval($timestampStart);
										break;
									case "LEQ":
										$where .= ' AND ' . $v . $currentLocation[$k] . ' ' . intval($timestampEnd);
										break;
									case "HI":
										$where .= ' AND ' . $v . $currentLocation[$k] . ' ' . intval($timestampEnd);
										break;
									case "HEQ":
										$where .= ' AND ' . $v . $currentLocation[$k] . ' ' . intval($timestampStart);
										break;
								}
							} else {
								$where .= ' AND 1 ';
							}
							break;
						case 'allModsIn':
							if(isset($currentSearch[$k]) && isset($this->version->modFields[$currentSearch[$k]])){
								$modConst[] = $this->version->modFields[$currentSearch[$k]];
							}
							break;
					}
				}
				if($modConst){
					$ids = array();
					$_ids = array();
					$this->db->query('SELECT ID, modifications FROM ' . VERSIONS_TABLE . ' WHERE modifications!=""');
					$modifications = array_map('makeArrayFromCSV', $this->db->getAllFirst(false));

					$m = 0;
					foreach($modConst as $k => $v){
						foreach($modifications as $key => $val){
							if(in_array($v, $modifications[$key])){
								$ids[$m][] = $key;
							}
						}$m++;
					}

					if(!empty($ids)){
						foreach($ids as $key => $val){
							$_ids[] = $val;
						}
						$arr = array();
						if(!empty($_ids[0])){
							//more then one field
							$mtof = false;
							foreach($_ids as $k => $v){
								if($k != 0){
									$mtof = true;
									foreach($v as $key => $val){
										if(!in_array($val, $_ids[0])){
											unset($_ids[0][$val]);
										} else {
											$arr[] = $val;
										}
									}
								}
							}
							if($mtof){
								$where .= ' AND ID IN (' . implode(',', $arr) . ') ';
							} elseif(!empty($_ids[0])){
								$where .= ' AND ID IN (' . implode(',', $_ids[0]) . ') ';
							} else {
								$where .= ' AND 0';
							}
						}
					} else {
						$where .= ' AND 0';
					}
				}
			}
		}

		return $where;
	}

	/**
	 * @abstract get modification-fields for filter-SELECT
	 * @return array of modification-fields
	 */
	function getModFields(){
		$modFields = array();

		foreach($this->version->modFields as $k => $v){
			if($k != "status"){
				$modFields[$k] = g_l('versions', '[' . $k . ']');
			}
		}

		return $modFields;
	}

	/**
	 * @abstract get filter categories for filter-SELECT
	 * @return array of filter categories
	 */
	function getFields(){

		$tableFields = array(
			'allModsIn' => g_l('versions', '[allModsIn]'),
			'timestamp' => g_l('versions', '[modTime]'),
			'modifierID' => g_l('versions', '[modUser]'),
			'status' => g_l('versions', '[status]')
		);

		return $tableFields;
	}

	/**
	 * @abstract get all user for filter-SELECT in category 'modifierID'
	 * @return array of users
	 */
	function getUsers(){
		$db = new DB_WE();

		$db->query('SELECT ID, Text FROM ' . USER_TABLE);
		return $db->getAllFirst(false);
	}

	/**
	 * @abstract get status
	 * @return array of stats
	 */
	function getStats(){
		return array(
			"published" => g_l('versions', '[published]'),
			"unpublished" => g_l('versions', '[unpublished]'),
			"saved" => g_l('versions', '[saved]'),
			"deleted" => g_l('versions', '[deleted]'),
		);
	}

}
