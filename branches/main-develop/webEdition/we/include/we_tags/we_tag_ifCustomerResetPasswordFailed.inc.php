<?php

/**
 * webEdition CMS
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
function we_tag_ifCustomerResetPasswordFailed(array $attribs){
	$type = weTag_getAttribute('type', $attribs, 'all');
	switch($type){
		case 'all':
			return isset($GLOBALS['ERROR']['customerResetPassword']) && $GLOBALS['ERROR']['customerResetPassword'];
		case 'passwordMismatch':
			return isset($GLOBALS['ERROR']['customerResetPassword']) && $GLOBALS['ERROR']['customerResetPassword'] == we_customer_customer::PWD_NOT_MATCH;
		case 'required':
			return isset($GLOBALS['ERROR']['customerResetPassword']) && ($GLOBALS['ERROR']['customerResetPassword'] == we_customer_customer::PWD_FIELD_NOT_SET || $GLOBALS['ERROR']['customerResetPassword'] == we_customer_customer::PWD_NO_SUCH_USER);
		case 'token':
			return isset($GLOBALS['ERROR']['customerResetPassword']) && ($GLOBALS['ERROR']['customerResetPassword'] == we_customer_customer::PWD_TOKEN_INVALID);
	}
	//all|required|passwordMismatch|mailEmpty|illegalToken|tokenTooOld
}
