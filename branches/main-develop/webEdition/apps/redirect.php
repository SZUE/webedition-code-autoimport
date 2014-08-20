<?php
/**
 * webEdition CMS
 *
 * $Rev: 7705 $
 * $Author: mokraemer $
 * $Date: 2014-06-10 21:46:56 +0200 (Di, 10. Jun 2014) $
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

we_html_tools::protect();
$matches = array();
//[REQUEST_URI] => /webEdition/apps/redirect.php/wephpmyadmin/index.php/frameset/index
preg_match('|' . WE_APPS_DIR . 'redirect.php/([^/]+)/([^/]+)(/.*)?$|', $_SERVER['REQUEST_URI'], $matches);
include_once($_SERVER['DOCUMENT_ROOT'] . WE_APPS_DIR . $matches[1] . '/' . ($matches[2] == 'index.php' ? 'index.php' : (isset($matches[3]) ? $matches[3] : '')));
/*p_r($matches);
p_r($_SERVER);

t_e($_REQUEST, $_SERVER);
 */