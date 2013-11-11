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

/**
  @param $query: SQL query; an empty query resets the cache
 */
function getHash($query, we_database_base $DB_WE = NULL, $resultType = MYSQL_BOTH){
	static $cache = array();
	if($query == ''){
		$cache = array();
		return $cache;
	}
	if(!isset($cache[$query])){
		$DB_WE = $DB_WE ? $DB_WE : $GLOBALS['DB_WE'];
		$DB_WE->query($query);
		$cache[$query] = ($DB_WE->next_record($resultType) ? $DB_WE->Record : array());
	}
	return $cache[$query];
}

function f($query, $field = -1, we_database_base $DB_WE = NULL, $emptyValue = ''){
	$h = getHash($query, ($DB_WE ? $DB_WE : $GLOBALS['DB_WE']), MYSQL_ASSOC);
	return ($field == -1 ? current($h) : (isset($h[$field]) ? $h[$field] : $emptyValue));
}

function doUpdateQuery(we_database_base $DB_WE, $table, $hash, $where){
	if(empty($hash)){
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
	return $DB_WE->query('UPDATE `' . $table . '` SET ' . we_database_base::arraySetter($fn) . ' ' . $where);
}

function escape_sql_query($inp){
	if(is_array($inp)){
		return array_map(__METHOD__, $inp);
	}

	if(!empty($inp) && is_string($inp)){
		return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
	}
	return $inp;
}

function sql_function($name){
	return array('sqlFunction' => true, 'val' => $name);
}

function doInsertQuery(we_database_base $DB_WE, $table, $hash){
	$tableInfo = $DB_WE->metadata($table);
	$fn = array();
	foreach($tableInfo as $t){
		$fieldName = $t['name'];
		$fn[$fieldName] = isset($hash[$fieldName . '_autobr']) ? nl2br($hash[$fieldName]) : $hash[$fieldName];
	}

	return $DB_WE->query('INSERT INTO `' . $table . '` SET ' . we_database_base::arraySetter($fn));
}
