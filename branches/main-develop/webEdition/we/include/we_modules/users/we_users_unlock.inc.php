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
$_ids = we_base_request::_(we_base_request::INTLISTA, 'we_cmd', array(), 1); // we_cmd[1] is commaseperated list of ids
//FIXME: make tblList???
$_tables = explode(',', we_base_request::_(we_base_request::STRINGC, 'we_cmd', '', 3)); // we_cmd[3] is commaseparated list of tables
$_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', null, 4); // we_cmd[4] is a single transaction, to delete data from session

$queries = array();

if($_transaction){ // clean session
	if(isset($_SESSION['weS']['we_data'][$_transaction])){
		$doc = $_SESSION['weS']['we_data'][$_transaction][0];
		if(isset($_SESSION['weS']['versions']['versionToCompare'][$doc['Table']][$doc['ID']])){
			unset($_SESSION['weS']['versions']['versionToCompare'][$doc['Table']][$doc['ID']]);
		}

		unset($_SESSION['weS']['we_data'][$_transaction]); // we_transaction is resetted here
	}
}

for($i = 0; $i < count($_ids); $i++){
	if($_tables[$i] && isset($_ids[$i]) && $_ids[$i]){
		$queries[$_tables[$i]][] = $_ids[$i];
	}
}
$uid = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2);
foreach($queries as $table => $ids){
	//don't clean all locks! - is this really a needed statement???
	$ids = implode(', ', array_filter($ids));
	if($ids){
		$DB_WE->query('DELETE FROM ' . LOCK_TABLE . ' WHERE tbl="' . $DB_WE->escape(stripTblPrefix($table)) . '" AND ID IN (' . $ids . ') AND sessionID="' . session_id() . '" AND UserID=' . $uid);
	}
}
?>UNLOCKED