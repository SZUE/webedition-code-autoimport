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
//$_SESSION["prefs"]["FileFilter"] = we_base_request::_(we_base_request::RAW, 'we_cmd', $_SESSION["prefs"]["FileFilter"], 5);

$topFrame = we_base_request::_(we_base_request::STRING, 'we_cmd', "top", 4);
//added for export module.
$treeFrame = we_base_request::_(we_base_request::STRING, 'we_cmd', $topFrame . '.body', 5);
$cmdFrame = we_base_request::_(we_base_request::STRING, 'we_cmd', $topFrame . '.cmd', 6);
$tree = new we_export_tree("export_frameset.php", $topFrame, $treeFrame, $cmdFrame);

$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', FILE_TABLE, 1);

if($table === FILE_TABLE && !permissionhandler::hasPerm("CAN_SEE_DOCUMENTS")){
	if(permissionhandler::hasPerm("CAN_SEE_TEMPLATES")){
		$table = TEMPLATES_TABLE;
	} else if(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTFILES")){
		$table = OBJECT_FILES_TABLE;
	} else if(defined('OBJECT_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTS")){
		$table = OBJECT_TABLE;
	}
}

$parentFolder = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2);
$openFolders = array_filter(we_base_request::_(we_base_request::INTLISTA, 'we_cmd', array(), 3));

$tree->loadHTML($table, $parentFolder, $openFolders);
