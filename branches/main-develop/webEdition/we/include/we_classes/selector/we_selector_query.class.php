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
 * @name we_selectorQuery
 */
class we_selector_query{
	/*	 * ***********************************************************************
	 * VARIABLES
	 * *********************************************************************** */
	private $db;
	var $result = array();
	var $fields;
	var $condition = array();

	/*	 * ***********************************************************************
	 * CONSTRUCTOR
	 * *********************************************************************** */

	/**
	 * Constructor of class
	 *
	 * @return we_selector_query
	 */
	public function __construct(){
		$this->db = new DB_WE();
		$this->fields = array('ID', 'Path');
	}

	/**
	 * query
	 * Die Funktion 'query' f�hrt die Abfrage f�r den �bergebenen Selektor-Typ durch.
	 * Mit dem Parameter 'limit' kann man die Anzahl der Suchergebnisse begrenzen.
	 *
	 * @param search
	 * @param table
	 * @param types
	 * @param limit
	 *
	 * @return void
	 */
	function queryTable($search, $table, array $types, $limit = null){
		$userExtraSQL = $this->getUserExtraQuery($table);

		switch($table){
			case USER_TABLE:
				$this->addQueryField("IsFolder");
				$typeField = "Type";
				break;
			case (defined('CUSTOMER_TABLE') ? CUSTOMER_TABLE : 'CUSTOMER_TABLE'):
				$typeField = "ContentType";
				$userExtraSQL = '';
				break;
			case VFILE_TABLE:
			case CATEGORY_TABLE:
			case (defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : ""):
				break;
			default:
				$typeField = "ContentType";
		}

		$where = "Path='" . $this->db->escape($search) . "'";
		$isFolder = 1;
		$addCT = 0;

		$types = array_unique(array_filter($types));

		$q = array();
		foreach($types as $type){
			$type = str_replace(" ", "", $type);
			if($type == we_base_ContentTypes::FOLDER){
				$q[] = 'IsFolder=1';
			} elseif(!empty($typeField)){
				$q[] = $typeField . '="' . $this->db->escape($type) . '"';
				$isFolder = 0;
				$addCT = 1;
			}
		}
		$where.=($q ? ' AND (' . implode(' OR ', $q) . ')' : '');
		if($addCT){
			$this->addQueryField($typeField);
		}
		$where .= ($userExtraSQL ? : '');

		if($this->condition){
			foreach($this->condition as $val){
				$where .= ' ' . $val['queryOperator'] . " " . $val['field'] . $val['conditionOperator'] . "'" . $val['value'] . "'";
			}
		}

		$order = 'ORDER BY ' . ($isFolder ? "Path" : "isFolder  ASC, Path") . ' ASC ';
		$fields = implode(', ', $this->fields);
		$this->db->query("SELECT $fields FROM " . $this->db->escape($table) . " WHERE $where $order" . ($limit ? " LIMIT $limit" : ""));
	}

	/**
	 * search
	 * Die Funktion 'search' f�hrt die Suche nach den Anfangszeichen f�r den �bergebenen Selektor-Typ durch.
	 * Mit dem Parameter 'limit' kann man die Anzahl der Suchergebnisse begrenzen.
	 *
	 * @param search
	 * @param table
	 * @param types
	 * @param limit
	 *
	 * @return void
	 */
	function search($search, $table, array $types, $limit = null, $rootDir = ""){
		$search = strtr($search, array("[" => "\\\[", "]" => "\\\]"));
		$userExtraSQL = $this->getUserExtraQuery($table);
		switch($table){
			case USER_TABLE:
				$this->addQueryField("IsFolder");
				$typeField = "Type";
				break;
			case (defined('CUSTOMER_TABLE') ? CUSTOMER_TABLE : 'CUSTOMER_TABLE'):
				$typeField = "ContentType";
				$userExtraSQL = '';
				break;
			case VFILE_TABLE:
			case CATEGORY_TABLE:
			case (defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : ""):
				break;
			default:
				$typeField = "ContentType";
		}

		$rootOnly = $rootDir && ($search === "/" || strpos($rootDir, $search) === 0);
		$where = ($rootOnly ?
				"Path LIKE '" . $rootDir . "'" :
				"Path REGEXP '^" . preg_quote(preg_quote($search)) . "[^/]*$'" . (
				($rootDir) ?
					" AND (Path LIKE '" . $this->db->escape($rootDir) . "' OR Path LIKE '" . $this->db->escape($rootDir) . "%')" :
					''));

		$isFolder = 0;
		$addCT = 0;

		$types = array_unique(array_filter($types));

		$q = array();
		foreach($types as $type){
			$type = str_replace(" ", "", $type);
			if($type == we_base_ContentTypes::FOLDER){
				$q[] = 'IsFolder=1';
			} elseif(!empty($typeField)){
				$q[] = $typeField . '="' . $this->db->escape($type) . '"';
				$isFolder = 0;
				$addCT = 1;
			}
		}
		$where.=($q ? ' AND (' . implode(' OR ', $q) . ')' : '');
		if(!$q){
			$isFolder = 1;
		}
		if($addCT){
			$this->addQueryField($typeField);
		}
		$where .= $userExtraSQL;

		if(!empty($this->condition)){
			foreach($this->condition as $val){
				$where .= ' ' . $val['queryOperator'] . ' ' . $val['field'] . $val['conditionOperator'] . '"' . $this->db->escape($val['value']) . '"';
			}
		}
		if(defined('NAVIGATION_TABLE') && $table == NAVIGATION_TABLE){
			$where.=we_navigation_navigation::getWSQuery();
		}
		//FIXME: what about other workspacequeries?!
		$this->db->query('SELECT ' . implode(', ', $this->fields) . ' FROM ' . $this->db->escape($table) . ' WHERE ' . $where . ' ORDER BY ' . ($isFolder ? 'isFolder DESC, Path' : 'Path') . ' ASC ' . ($limit ? ' LIMIT ' . $limit : ''));
	}

	/**
	 * Returns all entries of a folder, depending on the contenttype.
	 *
	 * @param integer $id
	 * @param string $table
	 * @param array $types
	 * @param integer $limit
	 */
	function queryFolderContents($id, $table, $types = null, $limit = null){
		$userExtraSQL = $this->getUserExtraQuery($table);
		if(is_array($types) && $table != CATEGORY_TABLE){
			$this->addQueryField('ContentType');
		}

		$this->addQueryField("Text");
		$this->addQueryField("ParentID");

		// deal with contenttypes
		$ctntQuery = ' OR ( 0 ';
		if($types){
			foreach($types as $type){
				$ctntQuery .= ' OR ContentType= "' . $type . '"';
			}
		}
		$ctntQuery .= ' ) ';
		if($table == CATEGORY_TABLE){
			$ctntQuery = '';
		}

		$this->db->query('SELECT ' . implode(',', $this->fields) . ' FROM ' . $this->db->escape($table) . ' WHERE ParentID=' . intval($id) . ' AND (IsFolder=1 ' . $ctntQuery . ' ) ' .
			$userExtraSQL . ' ORDER BY IsFolder DESC, Path ');
	}

	/**
	 * returns single item by id
	 *
	 * @param integer $id
	 * @param string $table
	 */
	function getItemById($id, $table, $fields = '', $useExtraSQL = true){
		$_votingTable = defined('VOTING_TABLE') ? VOTING_TABLE : "";
		switch($table){
			case $_votingTable:
				$useCreatorID = false;
				break;
			default:
				$useCreatorID = true;
		}

		$userExtraSQL = (!defined('BANNER_TABLE') || $table != BANNER_TABLE ?
				($useExtraSQL ? $this->getUserExtraQuery($table, $useCreatorID) : '') : '');

		$this->addQueryField("Text");
		$this->addQueryField("ParentID");
		if(is_array($fields)){
			foreach($fields as $val){
				$this->addQueryField($val);
			}
		}
		$this->db->query('SELECT ' . implode(',', $this->fields) . ' FROM ' . $this->db->escape($table) . ' WHERE ID=' . intval($id) . ' ' . $userExtraSQL);
		return $this->getResult();
	}

	/**
	 * returns single item by id
	 *
	 * @param integer $id
	 * @param string $table
	 */
	function getItemByPath($path, $table, $fields = ""){
		$userExtraSQL = $this->getUserExtraQuery($table);

		$this->addQueryField("Text");
		$this->addQueryField("ParentID");
		if(is_array($fields)){
			foreach($fields as $val){
				$this->addQueryField($val);
			}
		}
		$this->db->query('SELECT ' . implode(',', $this->fields) . ' FROM ' . $this->db->escape($table) . ' WHERE	Path="' . $this->db->escape($path) . '" ' . $userExtraSQL);
		return $this->getResult();
	}

	/**
	 * getResult
	 * Liefert das komplette Erg�bnis der Abfrage als Hash mit den Feldnamen als Spalten.
	 * @return Array
	 */
	function getResult(){
		$i = 0;
		$result = array();
		while($this->db->next_record()){
			foreach($this->fields as $val){
				$result[$i][$val] = htmlspecialchars_decode($this->db->f($val));
			}
			$i++;
		}
		return $result;
	}

	/**
	 * addQueryField
	 * F�gt den �bergebenen String zur Liste der gesuchten Felder hinzu.
	 * @param field
	 * @return void
	 */
	function addQueryField($field){
		$this->fields[] = $field;
	}

	/**
	 * delQueryField
	 * Entfernt den �bergebenen String von der Liste der gesuchten Felder.
	 * @param field
	 * @return void
	 */
	function delQueryField($field){
		foreach($this->fields as $key => $val){
			if($val == $field){
				unset($this->fields[$key]);
			}
		}
	}

	/**
	 * addCondition
	 * F�gt die �bergeben Abfragebedingung hinzu.
	 * @param array $condition
	 */
	function addCondition($condition){
		if(is_array($condition)){
			$arrayIndex = count($this->condition);
			$this->condition[$arrayIndex]['queryOperator'] = $condition[0];
			$this->condition[$arrayIndex]['conditionOperator'] = $condition[1];
			$this->condition[$arrayIndex]['field'] = $condition[2];
			$this->condition[$arrayIndex]['value'] = $condition[3];
		}
	}

	/**
	 * getUserExtraQuery
	 * Erzeugt ein Bedingungen zur Filterung der Arbeitsbereiche
	 * @param string $table
	 * @return string
	 */
	function getUserExtraQuery($table){
		if((defined('NAVIGATION_TABLE') && $table == NAVIGATION_TABLE) || (defined('BANNER_TABLE') && $table == BANNER_TABLE) || $table == CATEGORY_TABLE){
			return '';
		}
		$userExtraSQL = ' AND((1 ' . we_users_util::makeOwnersSql(false) . ') ';

		if(get_ws($table)){
			$userExtraSQL .= getWsQueryForSelector($table);
		} else if(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE && (!permissionhandler::hasPerm('ADMINISTRATOR'))){
			$wsQuery = array();
			$ac = we_users_util::getAllowedClasses($this->db);
			foreach($ac as $cid){
				$path = id_to_path($cid, OBJECT_TABLE);
				$wsQuery[] = ' Path LIKE "' . $this->db->escape($path) . '/%" OR Path="' . $this->db->escape($path) . '"';
			}
			if($wsQuery){
				$userExtraSQL .= ' AND (' . implode(' OR ', $wsQuery) . ')';
			}
		} else {
			switch($table){
				case (we_base_moduleInfo::isActive(we_base_moduleInfo::NEWSLETTER) ? NEWSLETTER_TABLE : 'NEWSLETTER_TABLE'):
				case USER_TABLE:
					break;
				default:
					$userExtraSQL.=' OR RestrictOwners=0 ';
			}
		}
		return $userExtraSQL . ')';
	}

}
