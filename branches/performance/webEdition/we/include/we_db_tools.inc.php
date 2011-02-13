<?php

/**
 * webEdition CMS
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
function getHash($query, $DB_WE){
	static $cache=array();
	if($query==''){
		$cache=array();
		return '';
	}
	if (isset($cache[$query])) {
		return $cache[$query];
	} else {
		$DB_WE->query($query);
		if ($DB_WE->next_record()) {
			$cache[$query] = $DB_WE->Record;
		} else {
			$cache[$query] = array();
		}
	}
	return $cache[$query];
}

function f($query, $field, $DB_WE) {
	$h = getHash($query, $DB_WE);
	return isset($h[$field]) ? $h[$field] : "";
}

function doUpdateQuery($DB_WE, $table, $hash, $where) {
	$tableInfo = $DB_WE->metadata($table);
	$sql = "UPDATE $table SET ";
for ($i = 0; $i < sizeof($tableInfo); $i++) {
		$fieldName = $tableInfo[$i]["name"];
		if ($fieldName != "ID") {
			$sql .= $fieldName . "='" . (isset($hash[$fieldName]) ? mysql_real_escape_string($hash[$fieldName]) : "") . "',";
		}
	}
	$sql = rtrim($sql, ',') . ' ' . $where;
	return $DB_WE->query($sql);
}

function doUpdateQuery2($DB_WE, $table, $hash, $where) {
	$tableInfo = $DB_WE->metadata($table);
	$sql = "UPDATE $table SET ";
	for ($i = 0; $i < sizeof($tableInfo); $i++) {
		$fieldName = $tableInfo[$i]["name"];
		if ($fieldName != "ID" && isset($hash[$fieldName])) {
			$sql .= $fieldName . "='" . mysql_real_escape_string($hash[$fieldName]) . "',";
		}
	}
	$sql = rtrim($sql, ',') . ' ' . $where;

	return $DB_WE->query($sql);
}

function doInsetQuery($DB_WE, $table, $hash) {

	$tableInfo = $DB_WE->metadata($table);
	$fn = array();
	$values = "";
	for ($i = 0; $i < sizeof($tableInfo); $i++) {
		$fieldName = $tableInfo[$i]["name"];
		array_push($fn, $fieldName);
		$values .= "'" . mysql_real_escape_string(isset($hash[$fieldName . "_autobr"]) ? nl2br($hash[$fieldName]) : $hash[$fieldName]) . "',";
	}
	$ti_s = implode(",", $fn);
	$values = rtrim($values, ',');
	$sql = "INSERT INTO $table ($ti_s) VALUES ($values)";

	return $DB_WE->query($sql);
}
