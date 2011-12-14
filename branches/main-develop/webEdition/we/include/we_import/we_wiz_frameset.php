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

include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_tagParser.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_import/we_wizard.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_import/we_wiz_import.inc.php");

$wizard = new we_wizard_import();

we_html_tools::protect();

if (isset($_REQUEST["pnt"])) $what = $_REQUEST["pnt"];
else $what = "wizframeset";

if (isset($_REQUEST["type"])) $type = $_REQUEST["type"];
else $type = "";

if (isset($_REQUEST["step"])) $step = $_REQUEST["step"];
else $step = 0;

if (isset($_REQUEST["mode"])) $mode = $_REQUEST["mode"];
else $mode = 0;

switch ($what) {
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
