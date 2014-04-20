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
 * @package    webEdition_wysiwyg
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$appendJS = "";


if(!(isset($_REQUEST['we_dialog_args']) &&
	(
	weRequest('bool', 'we_dialog_args', false, 'outsideWE') ||
	weRequest('bool', 'we_dialog_args', false, 'isFrontend')
	))){
	we_html_tools::protect();
	$noInternals = false;
} else {
	$noInternals = true;
}
$noInternals = $noInternals || !isset($_SESSION['user']) || !isset($_SESSION['user']['Username']) || $_SESSION['user']['Username'] == '';

if(defined("GLOSSARY_TABLE") && weRequest('bool', 'weSaveToGlossary') && !$noInternals){
	$Glossary = new we_glossary_glossary();
	$Glossary->Language = weRequest('string', 'language');
	$Glossary->Type = we_glossary_glossary::TYPE_ABBREVATION;
	$Glossary->Text = trim(weRequest('raw', 'text'));
	$Glossary->Title = trim(weRequest('raw', 'we_dialog_args', '', 'title'));
	$Glossary->Published = time();
	$Glossary->setAttribute('lang', weRequest('string', 'we_dialog_args', '', 'lang'));
	$Glossary->setPath();

	if($Glossary->Title == ""){
		$appendJS = we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[title_empty]'), we_message_reporting::WE_MESSAGE_ERROR) . ';var elem = document.forms[0].elements["we_dialog_args[title]"];elem.focus();elem.select();');
	} else if($Glossary->getAttribute('lang') == ""){
		$appendJS = we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[lang_empty]'), we_message_reporting::WE_MESSAGE_ERROR) . 'var elem = document.forms[0].elements["we_dialog_args[lang]"];elem.focus();elem.select();');
	} else if($Glossary->Text == ""){
		$appendJS = we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[name_empty]'), we_message_reporting::WE_MESSAGE_ERROR));
	} else if($Glossary->pathExists($Glossary->Path)){
		$appendJS = we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[name_exists]'), we_message_reporting::WE_MESSAGE_ERROR));
	} else {
		$Glossary->save();

		$Cache = new we_glossary_cache(weRequest('string', 'language'));
		$Cache->write();
		unset($Cache);

		$appendJS = we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[entry_saved]'), we_message_reporting::WE_MESSAGE_NOTICE) . 'top.close();');
	}
}

$dialog = new we_dialog_abbr($noInternals);
$dialog->initByHttp();
$dialog->registerOkJsFN("weDoAbbrJS");
echo $dialog->getHTML() .
 $appendJS;

function weDoAbbrJS(){
	return '
if(typeof(isTinyMCE) != "undefined" && isTinyMCE === true){
	WeabbrDialog.insert();
	top.close();
} else{
	eval("var editorObj = top.opener.weWysiwygObject_"+document.we_form.elements["we_dialog_args[editname]"].value);
	var title = document.we_form.elements["we_dialog_args[title]"].value;
	var lang = document.we_form.elements["we_dialog_args[lang]"].value;
	editorObj.editAbbr(title,lang);
	top.close();
}
';
}
