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

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/captcha/captchaImage.class.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/captcha/captchaMemory.class.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/captcha/captcha.class.php');

function we_tag_ifCaptcha($attribs, $content){
	$name = we_getTagAttribute('name', $attribs);
	$formname = we_getTagAttribute('formname', $attribs, '');

	if (!empty($formname) && isset($_REQUEST['we_ui_' . $formname][$name])) {
		return Captcha::check($_REQUEST['we_ui_' . $formname][$name]);
	} else
		if (empty($formname) && isset($_REQUEST['we_ui_we_global_form'][$name])) {
			return Captcha::check($_REQUEST['we_ui_we_global_form'][$name]);
		} else
			if (empty($formname) && isset($_REQUEST[$name])) {
				return Captcha::check($_REQUEST[$name]);
			} else {
				return false;
			}
}
