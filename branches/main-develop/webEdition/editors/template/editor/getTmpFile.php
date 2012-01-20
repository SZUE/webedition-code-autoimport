 <?php

/**
 * webEdition CMS
 *
 * $Rev: 3840 $
 * $Author: mokraemer $
 * $Date: 2012-01-19 20:28:15 +0100 (Do, 19. Jan 2012) $
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

 //FIXME: remove by call of ./webEdition/showTempFile.php
 include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();
t_e($_REQUEST);
if(isset($_REQUEST['we_cmd'][0])){
	$file = $_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/tmp/' . basename($_REQUEST['we_cmd'][0]);
	readfile($file);
}