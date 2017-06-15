<?php
/**
 * webEdition CMS
 *
 * $Rev: 13684 $
 * $Author: mokraemer $
 * $Date: 2017-04-04 23:48:16 +0200 (Di, 04. Apr 2017) $
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

switch($cmd){
	case 'weSearch_edit':
		$_REQUEST['mod'] = 'weSearch';
		$_REQUEST['pnt'] = 'show_frameset';

		unset($_SESSION['weS'][$tool . '_session']);

		$_REQUEST['keyword'] = '';
		if(($cmd1 = we_base_request::_(we_base_request::STRING, 'we_cmd', false, 1))){
			$_SESSION['weS']['weSearch']['keyword'] = $cmd1;
			$_REQUEST['keyword'] = $cmd1;
		}

		switch(($cmd2 = we_base_request::_(we_base_request::TABLE, 'we_cmd', "", 2))){//FIXME: bad to have different types at one query
			case FILE_TABLE:
				$_SESSION['weS']['weSearch']["checkWhich"] = 1;
				$cmd = 'weSearch_new_forDocuments';
				break;
			case TEMPLATES_TABLE:
				$_SESSION['weS']['weSearch']["checkWhich"] = 2;
				$cmd = 'weSearch_new_forTemplates';
				break;
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$_SESSION['weS']['weSearch']["checkWhich"] = 3;
				$cmd = 'weSearch_new_forObjects';
				break;
			case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
				$_SESSION['weS']['weSearch']["checkWhich"] = 4;
				$cmd = 'weSearch_new_forClasses';
				break;
			default:
				$cmd = 'weSearch_new';
		}
		$_REQUEST['cmd'] = $cmd;

		return '../../we_showMod.php';
}
