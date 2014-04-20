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

$frames = new we_export_wizard(WE_EXPORT_MODULE_DIR . "export_frameset.php");
we_html_tools::protect();
//	Starting output .

$what = weRequest('string', "pnt", "frameset");
$step = weRequest('int', "step", 0);


switch($what){

	case "frameset" :
		print $frames->getHTMLFrameset();
		break;

	case "header" :
		print $frames->getHTMLHeader($step);
		break;

	case "body" :
		print $frames->getHTMLStep($step);
		break;

	case "footer" :
		print $frames->getHTMLFooter($step);
		break;

	case "load" :
		print $frames->getHTMLCmd();
		break;

	default :
		die("Unknown command: " . $what . "\n");
		break;
}
