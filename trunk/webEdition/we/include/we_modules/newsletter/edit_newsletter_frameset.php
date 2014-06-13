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
$protect = we_base_moduleInfo::isActive('newsletter') && we_users_util::canEditModule('newsletter') ? null : array(false);
we_html_tools::protect($protect);

$what = we_base_request::_(we_base_request::STRING, 'pnt', 'frameset');
$mode = we_base_request::_(we_base_request::INT, 'art', 0);

$newsletterFrame = new we_newsletter_frames();
echo $newsletterFrame->getHTMLDocumentHeader($what, $mode);

if(isset($_REQUEST['inid'])){
	$newsletterFrame->View->newsletter = new we_newsletter_newsletter($_REQUEST['inid']);
} else {
	switch($what){
		case 'export_csv_mes':
		case 'newsletter_settings':
		case 'qsend':
		case 'eedit':
		case 'black_list':
		case 'upload_csv':
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
