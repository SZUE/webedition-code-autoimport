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


$id = $_REQUEST["we_cmd"][4];
$table = USER_TABLE;
if (isset($_REQUEST["we_cmd"][1]) && strpos($_REQUEST["we_cmd"][1],'WECMDENC_')!==false){$_REQUEST["we_cmd"][1]=base64_decode( substr($_REQUEST["we_cmd"][1],9));}
if (isset($_REQUEST["we_cmd"][2]) && strpos($_REQUEST["we_cmd"][2],'WECMDENC_')!==false){$_REQUEST["we_cmd"][2]=base64_decode( substr($_REQUEST["we_cmd"][2],9));}
if (isset($_REQUEST["we_cmd"][5]) && strpos($_REQUEST["we_cmd"][5],'WECMDENC_')!==false){$_REQUEST["we_cmd"][5]=base64_decode( substr($_REQUEST["we_cmd"][5],9));}

$JSIDName = $_REQUEST["we_cmd"][1];
$JSTextName = $_REQUEST["we_cmd"][2];
$JSCommand = isset($_REQUEST["we_cmd"][5]) ? $_REQUEST["we_cmd"][5] : "";
$sessionID = isset($_REQUEST["we_cmd"][6]) ? $_REQUEST["we_cmd"][6] : 0;
$rootDirID = isset($_REQUEST["we_cmd"][7]) ? $_REQUEST["we_cmd"][7] : 0;
$filter = $_REQUEST["we_cmd"][3];
$multiple = isset($_REQUEST["we_cmd"][8]) ? $_REQUEST["we_cmd"][8] : 0;

include_once(WE_USERS_MODULE_DIR . "we_usersSelect.php");

?>