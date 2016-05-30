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
define("NO_SESS", 1);

require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$id = we_base_request::_(we_base_request::INT, 'id', 0);
$did = we_base_request::_(we_base_request::INT, 'did', 0);
$page = we_base_request::_(we_base_request::STRING, 'page', 0);
$referer = we_base_request::_(we_base_request::URL, 'referer',0);
$nocount = we_base_request::_(we_base_request::BOOL, 'nocount', false);
$db = $GLOBALS['DB_WE'];

if(!$id){
	$bannername = we_base_request::_(we_base_request::STRING, 'bannername');

	if($bannername && isset($_COOKIE['webid_' . $bannername])){
		$id = $_COOKIE["webid_" . $bannername];
	}
	if(!$id){
		$id = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="banner" AND pref_name="DefaultBannerID"');
	}
}

if($id && is_numeric($id) && $did > 0){
	$url = we_banner_banner::getBannerURL($id);
	if(!$nocount){
		$db->query('INSERT INTO ' . BANNER_CLICKS_TABLE . ' SET ' . we_database_base::arraySetter(array(
				'ID' => $id,
				'Timestamp' => sql_function('UNIX_TIMESTAMP()'),
				'IP' => $_SERVER['REMOTE_ADDR'],
				'Referer' => ($referer ? : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '')),
				'DID' => $did,
				'Page' => $page
		)));

		$db->query('UPDATE ' . BANNER_TABLE . ' SET clicks=clicks+1 WHERE ID=' . intval($id));
	}
	header("Location: $url");
}
