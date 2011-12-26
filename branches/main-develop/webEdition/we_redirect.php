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

$path = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE Published>0 AND ID=' . intval($_REQUEST["id"]), 'Path', $DB_WE);
srand((double) microtime() * 1000000);
$randval = rand();

if($path){
	header('Location: ' . getServerUrl() . $path . '?r='.$randval);
	exit;
}
header('Location: ' . getServerUrl() . WEBEDITION_DIR . 'notPublished.php');
