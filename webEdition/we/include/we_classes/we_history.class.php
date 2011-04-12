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
 * Class we_history
 *
 * Provides functions determined to handle a list of last modified files required by
 * the 'personalized desktop'.
 */

class we_history
{

	function userHasPerms($creatorid,$owners,$restricted){
		if(!defined('BIG_USER_MODULE') || !in_array('busers',$GLOBALS['_pro_modules'])){
			return true;
		}
		if($_SESSION['perms']['ADMINISTRATOR']){
			return true;
		}
		if(!$restricted){
			return true;
		}
		if(we_isOwner($owners) || we_isOwner($creatorid)) {
			return true;
		}
		return false;
	}


	function insertIntoHistory(&$object){
		$_db = new DB_WE();
		//print $object->Table;
		$_username = isset($_SESSION['user']['Username']) ? $_SESSION['user']['Username'] : '';
		$_query = "SELECT * FROM " . HISTORY_TABLE . " WHERE " . HISTORY_TABLE . ".DID='".abs($object->ID).
							"' AND " . HISTORY_TABLE . ".DocumentTable='".$_db->escape(str_replace(TBL_PREFIX,'',$object->Table))."';";
		$object->DB_WE->query($_query);
		while($object->DB_WE->next_record()){
			$_row = "DELETE FROM " . HISTORY_TABLE . " WHERE " . HISTORY_TABLE . ".ID = '" . $_db->escape($object->DB_WE->f("ID")) . "';";
			$_db->query($_row);
		}

		$_query = 'INSERT INTO ' . HISTORY_TABLE . ' (DID,DocumentTable,ContentType,ModDate,Act,UserName) VALUES("'.abs($object->ID).'","'.$_db->escape(str_replace(TBL_PREFIX,'',$object->Table)).'","'.$_db->escape($object->ContentType).'","'.$_db->escape($object->ModDate).'","save","'.$_db->escape($_username).'");';
		$object->DB_WE->query($_query);

	}

	/**
	 * Deletes a model from navigation History
	 *
	 * @param array $modelIds
	 * @param string $table
	 */
	function deleteFromHistory( $modelIds, $table ) {

		$_db = new DB_WE();

		$query = "
			DELETE FROM " . HISTORY_TABLE . "
			WHERE DID in (" . implode(", ", $modelIds) . ")
			AND DocumentTable = \"" . substr($table, strlen(TBL_PREFIX)) . "\"
		";
		$_db->query( $query );

	}

}

