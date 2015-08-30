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
function we_parse_tag_ifNotWritten($attribs, $content){
	return '<?php if(!' . we_tag_tagParser::printTag('ifWritten', $attribs) . '){ ?>' . $content . '<?php } ?>';
}

function we_tag_ifNotWritten($attribs){
	$type = (weTag_getAttribute('type', $attribs, '', we_base_request::STRING)? : weTag_getAttribute('var', $attribs, 'document', we_base_request::STRING))? : weTag_getAttribute('doc', $attribs, 'document', we_base_request::STRING);
	switch($type){
		case 'customer':
			switch(weTag_getAttribute('onerror', $attribs, 'all', we_base_request::STRING)){
				default:
				case 'all':
					return !empty($GLOBALS['ERROR']['saveRegisteredUser']);
				case 'nousername':
					return isset($GLOBALS['ERROR']['saveRegisteredUser']) && $GLOBALS['ERROR']['customerResetPassword'] == we_customer_customer::PWD_USER_EMPTY;
				case 'nopassword':
					return isset($GLOBALS['ERROR']['saveRegisteredUser']) && $GLOBALS['ERROR']['customerResetPassword'] == we_customer_customer::PWD_FIELD_NOT_SET;
				case 'userexists':
					return isset($GLOBALS['ERROR']['saveRegisteredUser']) && $GLOBALS['ERROR']['customerResetPassword'] == we_customer_customer::PWD_USER_EXISTS;
				case 'passwordRule':
					return isset($GLOBALS['ERROR']['saveRegisteredUser']) && $GLOBALS['ERROR']['customerResetPassword'] == we_customer_customer::PWD_NOT_SUFFICIENT;
			}

			break;
		default:
			return !we_tag('ifWritten', $attribs);
	}
}
