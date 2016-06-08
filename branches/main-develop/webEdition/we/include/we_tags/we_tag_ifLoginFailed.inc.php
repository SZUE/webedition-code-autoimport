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
function we_tag_ifLoginFailed(array $attribs){
	switch(weTag_getAttribute('type', $attribs, 'all', we_base_request::STRING)){
		default:
		case 'all':
			return isset($_SESSION['webuser']) && isset($_SESSION['webuser']['loginfailed']) ? ($_SESSION['webuser']['loginfailed'] !== false) : false;
		case 'credentials':
			return isset($_SESSION['webuser']) && isset($_SESSION['webuser']['loginfailed']) ? ($_SESSION['webuser']['loginfailed'] === we_users_user::INVALID_CREDENTIALS) : false;
		case 'retrylimit':
			return isset($_SESSION['webuser']) && isset($_SESSION['webuser']['loginfailed']) ? ($_SESSION['webuser']['loginfailed'] === we_users_user::MAX_LOGIN_COUNT_REACHED) : false;
	}
}
