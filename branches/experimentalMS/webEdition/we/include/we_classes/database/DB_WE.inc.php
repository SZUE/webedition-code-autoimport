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

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_db_tools.inc.php');

// Database wrapper class of webEdition
if (!defined('DB_CONNECT')){
	define('DB_CONNECT','undefined in we_conf');
}
switch(DB_CONNECT){
	case 'connect':
	case 'pconnect':
		include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/database/we_database_mysql.class.php');
		break;
	case 'mysqli_connect':
	case 'mysqli_pconnect':
		include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/database/we_database_mysqli.class.php');
		if(!defined('MYSQL_BOTH')) define('MYSQL_BOTH',MYSQLI_BOTH);
		if(!defined('MYSQL_ASSOC')) define('MYSQL_ASSOC',MYSQLI_ASSOC);
		if(!defined('MYSQL_NUM')) define('MYSQL_NUM',MYSQLI_NUM);
		break;
	case 'msconnect':
		include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/database/we_database_mssql.class.php');
		if(!defined('MYSQL_BOTH')) define('MYSQL_BOTH',MSSQL_BOTH);
		if(!defined('MYSQL_ASSOC')) define('MYSQL_ASSOC',MSSQL_ASSOC);
		if(!defined('MYSQL_NUM')) define('MYSQL_NUM',MSSQL_NUM);
		break;
	
	default:
		echo 'unknown DB connection type "'.DB_CONNECT."\"\n";
		die('unknown DB connection type');
}