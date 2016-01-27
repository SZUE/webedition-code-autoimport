<?php
/**
 * webEdition SDK
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * Utility class for path manipulation and creation
 *
 * @category   we
 * @package none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_util_Path{

	/**
	 * Converts a given id to a path
	 *
	 * @param integer $id  id to convert
	 * @param string $dbTable name of table
	 * @return string
	 */
	static function id2Path($id, $dbTable, $db = NULL){
		return f('SELECT Path FROM ' . addslashes($dbTable) . ' WHERE ID=' . intval($id), '', $db? : $GLOBALS['DB_WE']);
	}

	/**
	 * Converts a given path to an id
	 *
	 * @param string $path  path to convert
	 * @param string $dbTable name of table
	 * @return integer
	 */
	static function path2Id($path, $dbTable){
		$db = $GLOBALS['DB_WE'];
		return f('SELECT ID FROM ' . addslashes($dbTable) . ' WHERE Path="' . $db->escape($path) . '"', '', $db);
	}

	/**
	 * Checks if a given path exists
	 *
	 * @param string $path  path to convert
	 * @param string $dbTable name of table
	 * @return boolean
	 */
	static function pathExists($path, $dbTable){
		$id = we_util_Path::path2Id($path, $dbTable);
		return $id != 0;
	}

}
