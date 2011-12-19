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
include($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$row = getHash("SELECT Path,Published FROM " . FILE_TABLE . " WHERE ID=" . intval($_REQUEST["id"]), $DB_WE);
srand((double) microtime() * 1000000);
$randval = rand();

if($row["Published"]){
	header("Location: " . WE_SERVER_URL . $row["Path"] . "?r=$randval");
	exit;
}
header("Location: " . WE_SERVER_URL . WEBEDITION_DIR . "notPublished.php");
