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
 * Base class for data base
 *
 * @category   we
 * @deprecated since version 6.4.0
 * @package none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_io_DB{

	/**
	 * dbInstance attribute
	 *
	 * @var NULL
	 */
	private static $dbInstance = NULL;

	/**
	 * create new adapter
 * @deprecated since version 6.4.0
	 *
	 * @return object
	 */
	static function newAdapter(){
		t_e('deprecated','this class is deprecated');
		$DBpar = array('username' => DB_USER, 'password' => DB_PASSWORD, 'dbname' => DB_DATABASE);
		if(stripos(DB_HOST, ':') !== false){
			list($host, $port) = explode(':', DB_HOST);
			$DBpar['host'] = $host;
			$DBpar['port'] = $port;
		} else {
			$DBpar['host'] = DB_HOST;
		}
		$charset = we_database_base::getCharset();

		$DBpar['charset'] = (!$charset || strpos(strtolower($charset), 'utf') !== false ? // es gibt alte sites, da steht UTF-8 drin, was aber falsch ist
				'utf8' : $charset);


		$db = new DB_WE();
		return $db;
	}

	/**
	 * shared adapter
 * @deprecated since version 6.4.0
	 *
	 * @return object
	 */
	static function sharedAdapter(){
		if(self::$dbInstance === NULL){
			self::$dbInstance = self::newAdapter();
		}
		return self::$dbInstance;
	}

	/**
	 * checks if table exists in $tab
 * @deprecated since version 6.4.0
	 *
	 * @param string $tab
	 * @return boolean
	 */
	static function tableExists($tab){
		$_db = new DB_WE();
		return ($_db->isTabExist($tab) ? true : false);
	}

}
