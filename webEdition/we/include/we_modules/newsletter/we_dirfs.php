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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$id = weRequest('int', 'we_cmd', 0, 1);

$JSIDName = stripslashes(weRequest('cmd', 'we_cmd', '', 2));
$JSTextName = stripslashes(weRequest('cmd', 'we_cmd', '', 3));
$JSCommand = weRequest('cmd', 'we_cmd', '', 4);
$sessionID = 0;
$rootDirID = weRequest('int', 'we_cmd', 0, 6);
$filter = weRequest('raw', 'we_cmd', 7, '');
$multiple = weRequest('bool', 'we_cmd', false, 8);

require_once(WE_MODULES_PATH . 'newsletter/we_newsletterDirSelector.php');
