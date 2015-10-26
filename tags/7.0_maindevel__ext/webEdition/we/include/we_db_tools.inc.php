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
  @param $query: SQL query; an empty query resets the cache
 */
function getHash($query = '', we_database_base $DB_WE = NULL, $resultType = MYSQL_ASSOC){
	$DB_WE = $DB_WE ? : $GLOBALS['DB_WE'];
	return $DB_WE->getHash($query, $resultType);
}

function f($query, $field = '', we_database_base $DB_WE = NULL, $emptyValue = ''){
	$DB_WE = $DB_WE ? : $GLOBALS['DB_WE'];
	$h = $DB_WE->getHash($query, MYSQL_ASSOC);
	return
		($field ?
			($h && isset($h[$field]) ? $h[$field] : $emptyValue) :
			($h ? current($h) : $emptyValue)
		);
}

function escape_sql_query($inp){
	if(is_array($inp)){
		return array_map(__METHOD__, $inp);
	}

	return ($inp && is_string($inp) ?
			strtr($inp, array(
				'\\' => '\\\\',
				"\0" => '\\0',
				"\n" => '\\n',
				"\r" => '\\r',
				"'" => "\\'",
				'"' => '\\"',
				"\x1a" => '\\Z'
			)) :
			$inp);
}

function sql_function($name){
	static $data = 0;
	if(!$data){
		$data = md5(uniqid(__FUNCTION__, true));
	}
	return (is_array($name) ? isset($name['sqlFunction']) && $name['sqlFunction'] === $data :
			array('sqlFunction' => $data, 'val' => $name));
}

//unused
function doUpdateQuery(we_database_base $DB_WE, $table, $hash, $where){
	if(!$hash){
		return;
	}
	$tableInfo = $DB_WE->metadata($table);
	$fn = array();
	foreach($tableInfo as $f){
		$fieldName = $f['name'];
		if($fieldName != 'ID' && isset($hash[$fieldName])){
			$fn[$fieldName] = $hash[$fieldName];
		}
	}
	return $DB_WE->query('UPDATE `' . $DB_WE->escape($table) . '` SET ' . we_database_base::arraySetter($fn) . ' ' . $where);
}

//unused
function doInsertQuery(we_database_base $DB_WE, $table, $hash){
	$tableInfo = $DB_WE->metadata($table);
	$fn = array();
	foreach($tableInfo as $t){
		$fieldName = $t['name'];
		$fn[$fieldName] = isset($hash[$fieldName . '_autobr']) ? nl2br($hash[$fieldName]) : $hash[$fieldName];
	}

	return $DB_WE->query('INSERT INTO `' . $DB_WE->escape($table) . '` SET ' . we_database_base::arraySetter($fn));
}
