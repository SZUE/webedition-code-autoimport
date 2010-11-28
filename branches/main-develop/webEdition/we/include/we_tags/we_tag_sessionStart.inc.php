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

function we_tag_sessionStart($attribs, $content){
	$GLOBALS["WE_SESSION_START"] = true;
	if (defined("CUSTOMER_TABLE")) {
		if (isset($_REQUEST["we_webUser_logout"]) && $_REQUEST["we_webUser_logout"]) {

			if (!isset($_SESSION))
				@session_start();
			unset($_SESSION["webuser"]);
			unset($_SESSION["s"]);
			unset($_REQUEST["s"]);
			$_SESSION["webuser"] = array(
				"registered" => false
			);

		} else {
			if (!isset($_SESSION))
				@session_start();
			if (isset($_REQUEST["we_set_registeredUser"]) && $GLOBALS["we_doc"]->InWebEdition) {
				$_SESSION["we_set_registered"] = $_REQUEST["we_set_registeredUser"];
			}
			if (!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]) {
				if (!isset($_SESSION["webuser"])) {
					$_SESSION["webuser"] = array(
						"registered" => false
					);
				}
				if (isset($_REQUEST["s"]["Username"]) && isset($_REQUEST["s"]["Password"]) && !(isset(
						$_REQUEST["s"]["ID"]))) {
					if($_REQUEST["s"]["Username"] != ''){
						$u = getHash(
								'SELECT * from ' . CUSTOMER_TABLE . ' WHERE Username="' . mysql_real_escape_string($_REQUEST['s']["Username"]) . '"',
								$GLOBALS["DB_WE"]);
						if (isset($u["Password"]) && $u["LoginDenied"] != 1) {
							if ($_REQUEST['s']["Username"] == $u["Username"] && $_REQUEST['s']["Password"] == $u["Password"]) {
								$_SESSION["webuser"] = $u;
								$_SESSION["webuser"]["registered"] = true;
								$GLOBALS["DB_WE"]->query(
										"UPDATE " . CUSTOMER_TABLE . " SET LastLogin='" . time() . "' WHERE ID='" . abs($_SESSION["webuser"]["ID"]) . "'");
							} else {
								$_SESSION["webuser"] = array(
									"registered" => false, "loginfailed" => true
								);
							}

						} else {
							$_SESSION["webuser"] = array(
								"registered" => false, "loginfailed" => true
							);
						}
					} else {
						$_SESSION["webuser"] = array(
							"registered" => false, "loginfailed" => true
						);
					}
				}

				if (isset($_SESSION["webuser"]["registered"]) && isset($_SESSION["webuser"]["ID"]) && isset($_SESSION["webuser"]["Username"]) && $_SESSION["webuser"]["registered"] && $_SESSION["webuser"]["ID"] && $_SESSION["webuser"]["Username"]!='') {
					$lastAccessExists = false;
					$foo = $GLOBALS["DB_WE"]->metadata(CUSTOMER_TABLE);
					for ($i = 0; $i < sizeof($foo); $i++) {
						if ($foo[$i]["name"] == "LastAccess") {
							$lastAccessExists = true;
							break;
						}
					}
					if ($lastAccessExists) {
						$GLOBALS["DB_WE"]->query(
								"UPDATE " . CUSTOMER_TABLE . " SET LastAccess='" . time() . "' WHERE ID='" . mysql_real_escape_string($_SESSION["webuser"]["ID"]) . "'");
					}
				}

			}
		}
		return "";

	} else {
		if (!isset($_SESSION))
			@session_start();
	}
	return "";
}
