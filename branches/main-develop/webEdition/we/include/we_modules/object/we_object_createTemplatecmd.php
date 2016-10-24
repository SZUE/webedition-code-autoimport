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

we_html_tools::protect();

$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', 0, 3);

$nr = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2);
$sid = we_base_request::_(we_base_request::STRING, 'SID');

$GLOBALS['we_doc'] = new we_template();
$GLOBALS['we_doc']->Table = TEMPLATES_TABLE;
$GLOBALS['we_doc']->we_new();

$filename = we_base_request::_(we_base_request::FILE, 'we_' . $sid . '_Filename');

$GLOBALS['we_doc']->Filename = $filename;
$GLOBALS['we_doc']->Extension = '.tmpl';
$GLOBALS['we_doc']->setParentID(we_base_request::_(we_base_request::INT, 'we_' . $sid . '_ParentID'));
$GLOBALS['we_doc']->Path = $GLOBALS['we_doc']->ParentPath . (($GLOBALS['we_doc']->ParentPath != '/') ? '/' : '') . $filename . '.tmpl';
$GLOBALS['we_doc']->ContentType = we_base_ContentTypes::TEMPLATE;

$GLOBALS['we_doc']->Table = TEMPLATES_TABLE;


//$GLOBALS['we_doc']->ID = 61;
//  $_SESSION['weS']['content'] is only used for generating a default template, it is
//  set in WE_OBJECT_MODULE_PATH\we_object_createTemplate.inc.php
$GLOBALS['we_doc']->elements['data']['dat'] = $_SESSION['weS']['content'];
$GLOBALS['we_doc']->elements['data']['type'] = 'txt';
unset($_SESSION['weS']['content']);

$jscmd = new we_base_jsCmd();

if(($we_responseText = $GLOBALS['we_doc']->checkFieldsOnSave())){
	$jscmd->addCmd($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
	echo $jscmd->getCmds();
	require_once(WE_MODULES_PATH . 'object/we_object_createTemplate.inc.php');
} else {
	if($GLOBALS['we_doc']->we_save()){
		$jscmd->addMsg(sprintf(g_l('weEditor', '[' . $GLOBALS['we_doc']->ContentType . '][response_save_ok]'), $GLOBALS['we_doc']->Path), we_message_reporting::WE_MESSAGE_NOTICE);
		$jscmd->addCmd('we_cmd', ["object_changeTempl_ob", $nr, $GLOBALS['we_doc']->ID]);
		$jscmd->addCmd('close');
		echo $jscmd->getCmds();
	} else {
		$we_responseText = sprintf(g_l('weEditor', '[' . $GLOBALS['we_doc']->ContentType . '][response_save_notok]'), $GLOBALS['we_doc']->Path);
		$jscmd->addCmd($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
		echo $jscmd->getCmds();
		require_once(WE_MODULES_PATH . 'object/we_object_createTemplate.inc.php');
	}
}
