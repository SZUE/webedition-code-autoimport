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
we_html_tools::protect();

// prepare the queries, 4 as maximum.
$ids = we_base_request::_(we_base_request::INTLISTA, 'we_cmd', [], 1); // we_cmd[1] is commaseperated list of ids
//FIXME: make tblList???
$tables = explode(',', we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3)); // we_cmd[3] is commaseparated list of tables
$transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', null, 4); // we_cmd[4] is a single transaction, to delete data from session

$queries = [];

if($transaction){ // clean session
	if(isset($_SESSION['weS']['we_data'][$transaction])){
		$doc = $_SESSION['weS']['we_data'][$transaction][0];
		if(isset($_SESSION['weS']['versions']['versionToCompare'][$doc['Table']][$doc['ID']])){
			unset($_SESSION['weS']['versions']['versionToCompare'][$doc['Table']][$doc['ID']]);
		}

		unset($_SESSION['weS']['we_data'][$transaction]); // we_transaction is resetted here
	}
}

for($i = 0; $i < count($ids); $i++){
	if($tables[$i] && !empty($ids[$i])){
		$queries[$tables[$i]][] = $ids[$i];
	}
}
$uid = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2);
foreach($queries as $table => $ids){
	//don't clean all locks! - is this really a needed statement???
	$ids = implode(', ', array_filter($ids));
	if($ids){
		$DB_WE->query('UPDATE ' . LOCK_TABLE . ' SET sessionID=x\'00\',UserID=releaseRequestID WHERE releaseRequestID IS NOT NULL AND releaseRequestID!=UserID AND tbl="' . $DB_WE->escape(stripTblPrefix($table)) . '" AND ID IN (' . $ids . ') AND UserID=' . intval($_SESSION['user']['ID']) . ' AND sessionID="' . session_id() . '"');

		$DB_WE->query('DELETE FROM ' . LOCK_TABLE . ' WHERE tbl="' . $DB_WE->escape(stripTblPrefix($table)) . '" AND ID IN (' . $ids . ') AND sessionID=x\'' . session_id() . '\' AND UserID=' . $uid);
	}
}
?>{"UNLOCKED":true}
