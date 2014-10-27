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
$protect = we_base_moduleInfo::isActive('customer') && we_users_util::canEditModule('customer') ? null : array(false);
we_html_tools::protect($protect);

$what = we_base_request::_(we_base_request::STRING, 'pnt', 'frameset');
$mode = we_base_request::_(we_base_request::STRING, 'art');

$ExImport = $weFrame = null;

switch($what){
	case 'export':
	case 'eibody':
	case 'eifooter':
	case 'eiload':
	case 'import':
	case 'eiupload':
		$ExImport = new we_customer_EIWizard();
		$step = we_base_request::_(we_base_request::INT, 'step', 0);
		echo $ExImport->getHTML($what, $mode, $step);
		break;
	default:
		$weFrame = new we_customer_frames();
		echo $weFrame->getHTMLDocumentHeader($what, $mode);
		$weFrame->process();
		echo $weFrame->getHTML($what, $mode);
}