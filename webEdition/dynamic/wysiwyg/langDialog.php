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
	$Glossary->Text = trim(we_base_request::_(we_base_request::STRING, 'text'));
	$Glossary->Published = time();
	$Glossary->setAttribute('lang', we_base_request::_(we_base_request::STRING, 'we_dialog_args', '', 'lang'));
	$Glossary->setPath();

	$cmd = new we_base_jsCmd();
	if($Glossary->Text === "" || $Glossary->getAttribute('lang') === ""){
		$cmd->addCmd('msg', ['msg' => g_l('modules_glossary', '[name_empty]'), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
	} else if($Glossary->pathExists($Glossary->Path)){
		$cmd->addCmd('msg', ['msg' => g_l('modules_glossary', '[name_exists]'), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
	} else {
		$Glossary->save();
		$cmd->addCmd('msg', ['msg' => g_l('modules_glossary', '[entry_saved]'), 'prio' => we_message_reporting::WE_MESSAGE_NOTICE]);
		$cmd->addCmd('close');
	}
}

$dialog = new we_dialog_lang($noInternals);
$dialog->initByHttp();
echo $dialog->getHTML() . $cmd->getCmds();
