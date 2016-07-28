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
function we_tag_formToken(array $attribs){
	// No token needen in editmode
	//FIXME: check if Dynamic is true on seo's
	if(!empty($GLOBALS['we_doc']->InWebEdition) || empty($GLOBALS['WE_MAIN_DOC']->IsDynamic)){
		return;
	}
	//generate one token per page
	static $token = '';

	if(!$token){
		//generate a unique token - it will be invalidated if the session is stopped or started
		$token = md5(uniqid($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['we_doc']->Published . session_id(), false));

		// Default lifetime of the token is set to 1/2 hour
		$lifetime = max(30, weTag_getAttribute('lifetime', $attribs, 1800, we_base_request::INT));

		// Set the current token and it's creation timestamp + lifetime
		we_captcha_captcha::save($token, 'token', $lifetime);
	}

	// Return the hidden input element
	return we_html_element::htmlHidden('securityToken', $token);
}
