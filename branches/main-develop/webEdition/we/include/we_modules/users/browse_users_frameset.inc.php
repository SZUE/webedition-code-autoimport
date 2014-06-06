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
$_REQUEST['id'] = $_REQUEST['we_cmd'][4];
$_REQUEST['table'] = USER_TABLE;

$_REQUEST['JSIDName'] = we_cmd_dec(1);
$_REQUEST['JSTextName'] = we_cmd_dec(2);
$_REQUEST['JSCommand'] = we_cmd_dec(5);
$_REQUEST['rootDirID'] = weRequest('int', 'we_cmd', 0, 7);
$_REQUEST['filter'] = $_REQUEST['we_cmd'][3];
$_REQUEST['multiple'] = weRequest('bool', 'we_cmd', false, 8);

require_once(WE_USERS_MODULE_PATH . "we_usersSelect.php");
