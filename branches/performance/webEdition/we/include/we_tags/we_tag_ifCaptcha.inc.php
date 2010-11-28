<?php
include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/captcha/captchaImage.class.php");
include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/captcha/captchaMemory.class.php");
include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/captcha/captcha.class.php");

function we_tag_ifCaptcha($attribs, $content){
	$name = we_getTagAttribute("name", $attribs);
	$formname = we_getTagAttribute("formname", $attribs, "");

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
}?>
