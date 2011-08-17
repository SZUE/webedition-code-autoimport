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
if (str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) == '/we_logout.inc.php') {
	exit();
}
include_once ($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we.inc.php");

$DB_WE->query('DELETE FROM ' . LOCK_TABLE . ' WHERE UserID="' . abs($_SESSION["user"]["ID"]) . '" AND sessionID="' . session_id() . '"');
//FIXME: table is set to false value, if 2 sessions are open; but this is updated shortly - so ignore it now
//TODO: update to time if still locked files open
$DB_WE->query("UPDATE " . USER_TABLE . " SET Ping=0 WHERE ID='" . abs($_SESSION["user"]["ID"]) . "'");

cleanTempFiles(true);

if (isset($_SESSION["prefs"]["userID"])) { //	bugfix 2585, only update prefs, when userId is available
	doUpdateQuery($DB_WE, PREFS_TABLE, $_SESSION["prefs"], " WHERE userID=" . abs($_SESSION["prefs"]["userID"]));
}

//	getJSCommand
if (isset($_SESSION["SEEM"]["startId"])) { // logout from webEdition opened with tag:linkToSuperEasyEditMode
	$_path = $_SESSION["SEEM"]["startPath"];

	$jsCommand = "top.location.replace('" . $_path . "');";

	while (list($name, $val) = each($_SESSION)) {
		if ($name != "webuser") {
			unset($_SESSION[$name]);
		}
	}
} else { //	normal logout from webEdition.
	$jsCommand = "top.location.replace('" . WEBEDITION_DIR . "');\n";
}
?>
<script  type="text/javascript">
	<!--
<?php
print $jsCommand;
?>
	//-->
</script>