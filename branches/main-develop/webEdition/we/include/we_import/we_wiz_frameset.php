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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$wizard = new we_import_wizard();

we_html_tools::protect();

$what = (isset($_REQUEST["pnt"]) ? $_REQUEST["pnt"] : 'wizframeset');
$type = (isset($_REQUEST["type"]) ? $_REQUEST["type"] : '');
$step = intval(isset($_REQUEST["step"]) ? $_REQUEST["step"] : 0);
$mode = intval(isset($_REQUEST["mode"]) ? $_REQUEST["mode"] : 0);

switch($what){
	case "wizframeset":
		print $wizard->getWizFrameset();
		break;
	case "wizbody":
		print $wizard->getWizBody($type, $step, $mode);
		break;
	case "wizbusy":
		print $wizard->getWizBusy();
		break;
	case "wizcmd":
		print $wizard->getWizCmd();
		break;
}
