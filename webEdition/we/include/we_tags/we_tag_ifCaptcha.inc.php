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
function we_tag_ifCaptcha($attribs){
	$name = weTag_getAttribute('name', $attribs);
	$formname = weTag_getAttribute('formname', $attribs);

	if(($checkM = we_base_request::_(we_base_request::STRING, $name))){
		if($formname && ($check = we_base_request::_(we_base_request::STRING, 'we_ui_' . $formname, '', $name))){
			return we_captcha_captcha::check($check);
		}
		if(($check = we_base_request::_(we_base_request::STRING, 'we_ui_we_global_form', '', $name))){
			return we_captcha_captcha::check($check);
		}
		return we_captcha_captcha::check($checkM);
	}

	return false;
}
