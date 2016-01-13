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
we_html_tools::protect();

/**
 * Checks if the start-document is a valid document. Content Type text/webedition or text/html
 * @return bool
 * @param int $id
 */
function checkIfValidStartdocument($id, $type = 'document'){

	return ($type === 'object' ?
			(f('SELECT ContentType FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($id)) === we_base_ContentTypes::OBJECT_FILE) :
			(f('SELECT ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id)) === we_base_ContentTypes::WEDOCUMENT));
}

//	Here begins the code for showing the correct frameset.
//	To improve readability the different cases are outsourced
//	in several functions, for SEEM, normal or edit_include-Mode.


function _buildJsCommand($cmdArray = array('', '', 'cockpit', 'open_cockpit', '', '', '', '', '')){
	return 'if(WE().layout.weEditorFrameController){
		WE().layout.weEditorFrameController.openDocument("' . implode('", "', $cmdArray) . '");
}';
}

$jsCommand = '';
if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 4) === 'SEEM_edit_include'){ // Edit-Include-Mode
// in multiEditorFrameset we_cmd[1] can be set to reach this
	$directCmd = array();
	for($i = 1; $i < 4; $i++){
		$directCmd[] = we_base_request::_(we_base_request::STRING, 'we_cmd', '', $i);
	}
	$jsCommand = _buildJsCommand($directCmd);
} else { // check preferences for which document to open at startup
// <we:linkToSeeMode> !
	if(isset($_SESSION['weS']['SEEM']) && isset($_SESSION['weS']['SEEM']['open_selected'])){
		switch($_SESSION['weS']['SEEM']['startType']){
			case 'document':
				if(checkIfValidStartdocument($_SESSION['weS']['SEEM']['startId'])){
					$directCmd = array(
						FILE_TABLE,
						$_SESSION['weS']['SEEM']['startId'],
						we_base_ContentTypes::WEDOCUMENT,
					);
					$jsCommand = _buildJsCommand($directCmd);
				} else {
					t_e('invalid start doc ' . $_SESSION['weS']['SEEM']['startId']);
					$jsCommand = _buildJsCommand();
				}
				break;
			case 'object':
				if(checkIfValidStartdocument($_SESSION['weS']['SEEM']['startId'])){
					$directCmd = array(
						OBJECT_FILES_TABLE,
						$_SESSION['weS']['SEEM']['startId'],
						we_base_ContentTypes::OBJECT_FILE
					);
					$jsCommand = _buildJsCommand($directCmd);
				} else {
					t_e('invalid start doc ' . $_SESSION['weS']['SEEM']['startId']);
					$jsCommand = _buildJsCommand();
				}
				break;
			default:
				$jsCommand = _buildJsCommand();
		}
		unset($_SESSION['weS']['SEEM']['open_selected']);
	} else {// normal mode, start document depends on settings
		switch($_SESSION['prefs']['seem_start_type']){
			default:
			case 'cockpit':
				$jsCommand = _buildJsCommand();
				break;
			case 'object':
				if($_SESSION['prefs']['seem_start_file'] != 0 && checkIfValidStartdocument($_SESSION['prefs']['seem_start_file'], 'object')){ //	if a stardocument is already selected - show this
					$jsCommand = _buildJsCommand(array(
						OBJECT_FILES_TABLE,
						$_SESSION['prefs']['seem_start_file'],
						we_base_ContentTypes::OBJECT_FILE,
					));
				} else {
					t_e('start doc not valid', $_SESSION['prefs']['seem_start_file']);
					$jsCommand = _buildJsCommand();
				}
				break;
			case '0':
				$jsCommand = 'WE().layout.weEditorFrameController.toggleFrames();';
				break;
			case 'document':
				if($_SESSION['prefs']['seem_start_file'] != 0 && checkIfValidStartdocument($_SESSION['prefs']['seem_start_file'])){ //	if a stardocument is already selected - show this
					$jsCommand = _buildJsCommand(array(
						FILE_TABLE,
						$_SESSION['prefs']['seem_start_file'],
						we_base_ContentTypes::WEDOCUMENT,
					));
				} elseif($_SESSION['prefs']['seem_start_file'] != 0){
					t_e('start doc not valid', $_SESSION['prefs']['seem_start_file']);
					$jsCommand = _buildJsCommand();
				}
				break;
			case 'weapp':
				if($_SESSION['prefs']['seem_start_weapp'] != ''){ //	if a we-app is choosen
					$jsCommand = _buildJsCommand() .
						_buildJsCommand(array('', '', '', 'tool_' . $_SESSION['prefs']['seem_start_weapp'] . '_edit'));
				}
				break;
		}
	}
}
echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') . we_html_element::jsElement($jsCommand), we_html_element::htmlBody());
