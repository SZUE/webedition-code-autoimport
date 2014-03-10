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
define("NO_SESS", 1);

require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : 0;
$did = isset($_REQUEST["did"]) ? intval($_REQUEST["did"]) : 0;
$page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : 0;
$referer = isset($_REQUEST["referer"]) ? $_REQUEST["referer"] : 0;
$nocount = isset($_REQUEST["nocount"]) ? $_REQUEST["nocount"] : "";
$db = $GLOBALS['DB_WE'];

if(!$id){
	$bannername = $_REQUEST["bannername"];

	if($bannername && isset($_COOKIE['webid_' . $bannername])){
		$id = $_COOKIE["webid_" . $bannername];
	}
	if(!$id){
		$id = f('SELECT pref_value FROM ' . BANNER_PREFS_TABLE . ' WHERE pref_name="DefaultBannerID"');
	}
}

if($id && is_numeric($id) && $did > 0){
	$url = we_banner_banner::getBannerURL($id);
	if(!$nocount){
		$db->query('INSERT INTO ' . BANNER_CLICKS_TABLE . ' SET ' . we_database_base::arraySetter(array(
				'ID' => intval($id),
				'Timestamp' => sql_function('UNIX_TIMESTAMP()'),
				'IP' => $_SERVER['REMOTE_ADDR'],
				'Referer' => ($referer ? $referer : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '')),
				'DID' => intval($did),
				'Page' => $page
		)));

		$db->query('UPDATE ' . BANNER_TABLE . ' SET clicks=clicks+1 WHERE ID=' . intval($id));
	}
	header("Location: $url");
}
