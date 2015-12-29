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
function we_tag_ifFormToken(){
	// Tokens in edit are always valid
	if(!empty($GLOBALS['we_doc']->InWebEdition)){
		return true;
	}

	if(($token = we_base_request::_(we_base_request::STRING, 'securityToken', ''))){
		return we_captcha_captcha::check($token, 'token');
	}
	return false;
}
