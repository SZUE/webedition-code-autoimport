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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function we_tag_ifNotCustomerResetPassword(array $attribs){
	switch(weTag_getAttribute('type', $attribs, 'all', we_base_request::STRING)){
		default:
		case 'all':
			return $GLOBALS['ERROR']['customerResetPassword'] !== we_customer_customer::PWD_ALL_OK;
		case 'passwordMismatch':
			return $GLOBALS['ERROR']['customerResetPassword'] === we_customer_customer::PWD_NOT_MATCH;
		case 'passwordRule':
			return $GLOBALS['ERROR']['customerResetPassword'] === we_customer_customer::PWD_NOT_SUFFICIENT;
		case 'required':
			return ($GLOBALS['ERROR']['customerResetPassword'] === we_customer_customer::PWD_FIELD_NOT_SET);
		case 'userNotExists': //FR #9823
			return ($GLOBALS['ERROR']['customerResetPassword'] === we_customer_customer::PWD_NO_SUCH_USER);
		case 'token':
			return ($GLOBALS['ERROR']['customerResetPassword'] === we_customer_customer::PWD_TOKEN_INVALID);
	}
	//all|required|passwordMismatch|mailEmpty|illegalToken|tokenTooOld
}
