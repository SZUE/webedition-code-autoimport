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

we_html_tools::protect();

$what = (isset($_REQUEST["pnt"]) ? $_REQUEST["pnt"] : 'frameset');
$mode = (isset($_REQUEST["art"]) ? $_REQUEST["art"] : 0);

$newsletterFrame = new weNewsletterFrames();
$newsletterFrame->getHTMLDocumentHeader($what, $mode);

if(isset($_REQUEST["inid"])){
	$newsletterFrame->View->newsletter = new weNewsletter($_REQUEST["inid"]);
} else{
	switch($what){
		case "export_csv_mes":
		case "newsletter_settings":
		case "qsend":
		case "eedit":
		case "black_list":
		case "upload_csv":
			break;
		default:
			$newsletterFrame->View->processVariables();
	}
}

switch($what){
	case 'export_csv_mes':
	case 'preview':
	case 'domain_check':
	case 'newsletter_settings':
	case 'show_log':
	case 'print_lists':
	case 'qsend':
	case 'eedit':
	case 'black_list':
		break;
	default:
		$newsletterFrame->View->processCommands();
}

echo $newsletterFrame->getHTML($what, $mode);