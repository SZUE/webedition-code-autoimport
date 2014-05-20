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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
switch(weRequest('string', 'we_cmd', '', 0)){
	case 'openCatselector' :
		$noChoose = weRequest('bool', 'we_cmd', false, 8);
	case 'openDirselector' :
	case 'openSelector' :
	case 'openCatselector' :
	case 'openDelSelector' :
		$id = weRequest('int', 'we_cmd', 0, 1);
		$table = weRequest('table', 'we_cmd', FILE_TABLE, 2);
		$JSIDName = we_cmd_dec(3);
		$JSTextName = we_cmd_dec(4);
		$JSCommand = we_cmd_dec(5);
		$sessionID = 0;
		$rootDirID = weRequest('int', 'we_cmd', 0, 7);
		$filter = weRequest('raw', 'we_cmd', '', 8);
		$multiple = weRequest('raw', 'we_cmd', '', 9);
		break;
	case 'openDocselector':
		$id = weRequest('int', 'we_cmd', 0, 1);
		$table = weRequest('table', 'we_cmd', FILE_TABLE, 2);
		$JSIDName = we_cmd_dec(3);
		$JSTextName = we_cmd_dec(4);
		$JSCommand = we_cmd_dec(5);
		$sessionID = 0;
		$rootDirID = weRequest('int', 'we_cmd', 0, 7);
		$filter = weRequest('raw', 'we_cmd', '', 8);
		$open_doc = weRequest('bool', 'we_cmd', false, 9);
		$multiple = weRequest('bool', 'we_cmd', false, 10);
		$canSelectDir = weRequest('bool', 'we_cmd', false, 11);
		break;
}

switch(weRequest('string', 'we_cmd', '', 0)){
	case 'openDirselector' :
		require_once (WEBEDITION_PATH . 'we_dirSelect.php');
		break;
	case 'openSelector' :
		require_once (WEBEDITION_PATH . ($table == CUSTOMER_TABLE ? 'we_customerSelect.php' : 'we_fs.php'));
		break;
	case 'openDocselector' :
		require_once (WEBEDITION_PATH . 'we_docSelect.php');
		break;
	case 'openCatselector' :
		require_once (WEBEDITION_PATH . 'we_catSelect.php');
		break;
	case 'openDelSelector' :
		require_once (WEBEDITION_PATH . 'we_delSelect.php');
		break;
}