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
 * Class we_history
 *
 * Provides functions determined to handle a list of last modified files required by
 * the 'personalized desktop'.
 */
abstract class we_history{
	const MAX = 50;

	static function userHasPerms($creatorid, $owners, $restricted){
		return (permissionhandler::hasPerm('ADMINISTRATOR') || !$restricted || we_users_util::isOwner($owners) || we_users_util::isOwner($creatorid));
	}

	static function insertIntoHistory(&$object){
		$db = new DB_WE();
		$uid = intval(isset($GLOBALS['we']['Scheduler_active']) ? 0 : (isset($_SESSION['user']['ID']) ? $_SESSION['user']['ID'] : 0));
		$tab = stripTblPrefix($object->Table);
		$cnt = f('SELECT COUNT(1) FROM ' . HISTORY_TABLE . ' WHERE DocumentTable="' . $tab . '" AND UID=' . $uid, '', $db);
		if($cnt > self::MAX){
			$db->query('DELETE FROM ' . HISTORY_TABLE . ' WHERE DocumentTable="' . $tab . '" AND UID=' . $uid . ' ORDER BY ModDate ASC LIMIT ' . ($cnt - self::MAX));
		}

		$db->query('REPLACE INTO ' . HISTORY_TABLE . ' SET ' . we_database_base::arraySetter(['DID' => intval($object->ID),
				'DocumentTable' => $tab,
				'ContentType' => $object->ContentType,
				'UserName' => (isset($GLOBALS['we']['Scheduler_active']) ? 'Scheduler' : (isset($_SESSION['user']['Username']) ? $_SESSION['user']['Username'] : (isset($_SESSION['webuser']['Username']) ? $_SESSION['webuser']['Username'] : 'Unknown'))),
				'UID' => $uid,
				]));
	}

	/**
	 * Deletes a model from navigation History
	 *
	 * @param array $modelIds
	 * @param string $table
	 */
	static function deleteFromHistory($modelIds, $table){
		$db = new DB_WE();
		$db->query('DELETE FROM ' . HISTORY_TABLE . ' WHERE DID IN (' . implode(', ', $modelIds) . ') AND DocumentTable="' . stripTblPrefix($table) . '"');
	}

	public static function deleteByUserID($uid){
		if($uid){
			$db = new DB_WE();
			$db->query('DELETE FROM ' . HISTORY_TABLE . ' WHERE UID=' . intval($uid));
		}
	}

}
