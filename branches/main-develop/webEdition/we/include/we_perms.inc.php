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
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_html_tools.inc.php');

###### protect #################################################################
### protect()
### protects a page. Guests can not see this page

function protect(){
	if (!isset($_SESSION["user"]) || !isset($_SESSION["user"]["Username"]) ||  $_SESSION["user"]["Username"] == '') {
		print htmlTop();
		print
						we_htmlElement::jsElement(
										we_message_reporting::getShowMessageCall(
														g_l('alert','[perms_no_permissions]'), WE_MESSAGE_ERROR) . 'top.close();');
		print '</body></html>';
		exit();
	}
}

###### login ###################################################################
### login()
### the same as protect but with an othe error message. It is used after the login

function login() {
	if ($_SESSION['user']['Username'] == '') {

		print htmlTop();
		print
						we_htmlElement::jsElement(
										we_message_reporting::getShowMessageCall(g_l('alert','[login_failed]'), WE_MESSAGE_ERROR) . 'history.back();');
		print '</body></html>';
		exit();
	}
}
