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
$cmd0 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);
switch($cmd0){//FIMXE most of the stuff can be handled via session! transfer is a bit complicated due to elements inside documents
	case 'openCatselector' :
		$_REQUEST['noChoose'] = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 8);
		$_REQUEST['id'] = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
		$table = $_REQUEST['table'] = we_base_request::_(we_base_request::TABLE, 'we_cmd', FILE_TABLE, 2);
		$_REQUEST['JSIDName'] = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
		$_REQUEST['JSTextName'] = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
		$_REQUEST['JSCommand'] = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
		$_REQUEST['rootDirID'] = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
		break;
	case 'openDirselector':
	case 'openSelector' :
	case 'openDelSelector' :
		$_REQUEST['id'] = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
		$table = $_REQUEST['table'] = we_base_request::_(we_base_request::TABLE, 'we_cmd', FILE_TABLE, 2);
		$_REQUEST['JSIDName'] = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
		$_REQUEST['JSTextName'] = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
		$_REQUEST['JSCommand'] = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
		$_REQUEST['rootDirID'] = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
		$_REQUEST['filter'] = we_base_request::_(we_base_request::BOOL, 'we_cmd', '', 8);
		$_REQUEST['multiple'] = we_base_request::_(we_base_request::BOOL, 'we_cmd', '', 9);
		break;
	case 'openDocselector':
	case 'openImgselector':
		$_REQUEST['id'] = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
		$_REQUEST['table'] = we_base_request::_(we_base_request::TABLE, 'we_cmd', FILE_TABLE, 2);
		$_REQUEST['JSIDName'] = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
		$_REQUEST['JSTextName'] = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
		$_REQUEST['JSCommand'] = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
		$_REQUEST['rootDirID'] = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
		$_REQUEST['filter'] = we_base_request::_(we_base_request::STRINGC, 'we_cmd', '', 8);
		$_REQUEST['open_doc'] = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 9);
		$_REQUEST['multiple'] = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 10);
		$_REQUEST['canSelectDir'] = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 11);
		if($cmd0 === 'openDocselector' && $_REQUEST['filter'] === 'image/*'){
			$cmd0 = 'openImgselector';
			//t_e('notice', 'called incorrect selector');
		}
		break;
}

switch($cmd0){
	case 'openDirselector':
		require_once (WEBEDITION_PATH . 'we_dirSelect.php');
		break;
	case 'openSelector':
		require_once (WEBEDITION_PATH . ($table == CUSTOMER_TABLE ? 'we_customerSelect.php' : 'we_fs.php'));
		break;
	case 'openDocselector':
		require_once (WEBEDITION_PATH . 'we_docSelect.php');
		break;
	case 'openImgselector':
		require_once (WEBEDITION_PATH . 'we_imgSelect.php');
		break;
	case 'openCatselector':
		require_once (WEBEDITION_PATH . 'we_catSelect.php');
		break;
	case 'openDelSelector':
		require_once (WEBEDITION_PATH . 'we_delSelect.php');
		break;
}