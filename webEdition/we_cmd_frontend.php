<?php

/**
 * webEdition CMS
 *
 * $Rev: 5080 $
 * $Author: mokraemer $
 * $Date: 2012-11-06 18:45:46 +0100 (Di, 06 Nov 2012) $
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
if(!isset($_REQUEST['we_cmd'])){
	exit();
}

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_defines.inc.php');

//start autoloader!
include_once ($_SERVER['DOCUMENT_ROOT'] . LIB_DIR . 'we/core/autoload.php');

$INCLUDE = '';
//	In we.inc.php all names of the active modules have already been searched
//	so we only have to use the array $GLOBALS['_we_active_integrated_modules']

if(!$INCLUDE){
	switch($_REQUEST['we_cmd'][0]){
		case 'open_wysiwyg_window':
			$INCLUDE = 'wysiwygWindow.inc.php';
			break;

		default:
			exit();
	}
}

if($INCLUDE){
	//  When pressing a link in edit-mode, the page is being reloaded from
	//  webedition. If a webedition link was pressed this page shall not be
	//  reloaded. All entries in this array represent values for we_cmd[0]
	//  when the javascript command shall NOT be inserted (p.ex while saving the file.)
	//	This is ONLY used in the edit-mode of the documents.

	require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
	include(WE_INCLUDES_PATH . $INCLUDE);

	//This statement prevents the page from being reloaded
	if(!in_array($_REQUEST['we_cmd'][0], $cmds_no_js)){
		print we_html_element::jsElement('parent.openedWithWE = 1;');
	}

	if($_REQUEST['we_cmd'][0] == 'edit_document' || $_REQUEST['we_cmd'][0] == 'switch_edit_page' || $_REQUEST['we_cmd'][0] == 'load_editor'){

		print we_html_element::jsScript(JS_DIR . 'attachKeyListener.js');
	}
	exit;
}