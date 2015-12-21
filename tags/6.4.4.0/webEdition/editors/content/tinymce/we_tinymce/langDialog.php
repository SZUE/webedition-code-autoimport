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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$noInternals = false;
if(!(
	we_base_request::_(we_base_request::BOOL, 'we_dialog_args', false, 'outsideWE') ||
	we_base_request::_(we_base_request::BOOL, 'we_dialog_args', false, 'isFrontend')
	)){
	we_html_tools::protect();
} else {
	$noInternals = true;
}
$noInternals = $noInternals || !isset($_SESSION['user']) || !isset($_SESSION['user']['Username']) || $_SESSION['user']['Username'] == '';

$appendJS = "";
if(defined('GLOSSARY_TABLE') && we_base_request::_(we_base_request::BOOL, 'weSaveToGlossary') && !$noInternals){
	$Glossary = new we_glossary_glossary();
	$Glossary->Language = we_base_request::_(we_base_request::STRING, 'language', '');
	$Glossary->Type = we_glossary_glossary::TYPE_FOREIGNWORD;
	$Glossary->Text = trim(we_base_request::_(we_base_request::RAW, 'text'));
	$Glossary->Published = time();
	$Glossary->setAttribute('lang', we_base_request::_(we_base_request::STRING, 'we_dialog_args', '', 'lang'));
	$Glossary->setPath();

	if($Glossary->Text === "" || $Glossary->getAttribute('lang') === ""){
		$appendJS = we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[name_empty]'), we_message_reporting::WE_MESSAGE_ERROR));
	} else if($Glossary->pathExists($Glossary->Path)){
		$appendJS = we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[name_exists]'), we_message_reporting::WE_MESSAGE_ERROR));
	} else {
		$Glossary->save();
		$appendJS = we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[entry_saved]'), we_message_reporting::WE_MESSAGE_NOTICE) . 'top.close();');
	}
}

$dialog = new we_dialog_lang($noInternals);
$dialog->initByHttp();
$dialog->registerOkJsFN("weDoLangJS");
echo $dialog->getHTML().
		$appendJS;

function weDoLangJS(){
	return '
if(typeof(isTinyMCE) != "undefined" && isTinyMCE === true){
	WelangDialog.insert();
	top.close();
} else{
	eval("var editorObj = top.opener.weWysiwygObject_"+document.we_form.elements["we_dialog_args[editname]"].value);
	var lang = document.we_form.elements["we_dialog_args[lang]"].value;
	editorObj.editLang(lang);
	top.close();
}';
}
