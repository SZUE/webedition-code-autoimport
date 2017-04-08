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
we_html_tools::protect(['BROWSE_SERVER', 'SITE_IMPORT', 'ADMINISTRATOR']);

$cmd = we_base_request::_(we_base_request::STRING, 'cmd');
$jsCmd = new we_base_jsCmd();
$js = '';

if($cmd === "save_last"){
	$_SESSION['user']["LastDir"] = $last;
}
if(!$cmd || $cmd != "save_last"){
	$selectInternal = we_base_request::_(we_base_request::BOOL, 'selectInternal', false);

	function delDir($dir){
		$d = dir($dir);
		while(false !== ($entry = $d->read())){
			if($entry != "." && $entry != ".."){
				if(is_dir($dir . "/" . $entry)){
					delDir($dir . "/" . $entry);
				} else if(is_file($dir . "/" . $entry)){
					if(!@unlink($dir . "/" . $entry)){
						$GLOBALS['jsCmd']->addMsg(sprintf(g_l('alert', '[delete_nok_file]'), $entry), we_base_util::WE_MESSAGE_ERROR);
					}
				} else {
					$GLOBALS['jsCmd']->addMsg(sprintf(g_l('alert', '[delete_nok_noexist]'), $entry), we_base_util::WE_MESSAGE_ERROR);
				}
			}
		}
		if(!rmdir($dir)){
			$GLOBALS['jsCmd']->addMsg(sprintf(g_l('alert', '[delete_nok_folder]'), $dir), we_base_util::WE_MESSAGE_ERROR);
		}
	}

	$txt = we_base_request::_(we_base_request::FILE, 'txt', '');
	switch(we_base_request::_(we_base_request::STRING, "cmd")){
		case "new_folder":
			$js.= 'drawDir(top.fileSelect.data.currentDir);';
			if($txt === ''){
				$jsCmd->addMsg(g_l('alert', '[we_filename_empty]'), we_base_util::WE_MESSAGE_ERROR);
				break;
			}
			if(preg_match('|[\'"<>/]|', $txt)){
				$jsCmd->addMsg(g_l('alert', '[name_nok]'), we_base_util::WE_MESSAGE_ERROR);
				break;
			}
			$path = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . we_base_request::_(we_base_request::FILE, 'pat') . '/' . $txt);
			if(!is_dir($path)){
				if(!we_base_file::createLocalFolderByPath($path)){
					$jsCmd->addMsg(g_l('alert', '[create_folder_nok]'), we_base_util::WE_MESSAGE_ERROR);
				} else {
					$js.= 'selectFile("' . $txt . '");top.fileSelect.data.currentID="' . $path . '";';
				}
			} else {
				$jsCmd->addMsg(sprintf(g_l('alert', '[path_exists]'), str_replace($_SERVER['DOCUMENT_ROOT'], '', $path)), we_base_util::WE_MESSAGE_ERROR);
			}
			break;
		case "rename_folder":
			if($txt === ''){
				$jsCmd->addMsg(g_l('alert', '[we_filename_empty]'), we_base_util::WE_MESSAGE_ERROR);
				$js.= "drawDir(top.fileSelect.data.currentDir);";
				break;
			}
			if(preg_match('|[\'"<>/]|', $txt)){
				$jsCmd->addMsg(g_l('alert', '[name_nok]'), we_base_util::WE_MESSAGE_ERROR);
				$js.= "drawDir(top.fileSelect.data.currentDir);";
				break;
			}
			$pat = we_base_request::_(we_base_request::FILE, 'pat');
			$old = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $pat . '/' . we_base_request::_(we_base_request::FILE, 'sid'));
			$new = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $pat . '/' . $txt);
			if($old != $new){
				if(!is_dir($new)){
					if(!rename($old, $new)){
						$jsCmd->addMsg(g_l('alert', '[rename_folder_nok]'), we_base_util::WE_MESSAGE_ERROR);
					} else {
						$js.= 'selectFile("' . $txt . '");';
					}
				} else {
					$we_responseText = sprintf(g_l('alert', '[path_exists]'), str_replace($_SERVER['DOCUMENT_ROOT'], '', $new));
					$jsCmd->addMsg($we_responseText, we_base_util::WE_MESSAGE_ERROR);
				}
			}
			$js.= 'drawDir(top.fileSelect.data.currentDir);';

			break;
		case "rename_file":
			if($txt === ''){
				$jsCmd->addMsg(g_l('alert', '[we_filename_empty]'), we_base_util::WE_MESSAGE_ERROR);
				$js.= "drawDir(top.fileSelect.data.currentDir);";
				break;
			}
			if(preg_match('|[\'"<>/]|', $txt)){
				$jsCmd->addMsg(g_l('alert', '[name_nok]'), we_base_util::WE_MESSAGE_ERROR);
				$js.= "drawDir(top.fileSelect.data.currentDir);";
				break;
			}
			$pat = we_base_request::_(we_base_request::FILE, 'pat');
			$txt = we_base_request::_(we_base_request::FILE, 'txt');
			$old = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $pat . '/' . we_base_request::_(we_base_request::FILE, "sid"));
			$new = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $pat . '/' . $txt);
			if($old != $new){
				if(!file_exists($new)){
					if(!rename($old, $new)){
						$jsCmd->addMsg(g_l('alert', '[rename_file_nok]'), we_base_util::WE_MESSAGE_ERROR);
					} else {
						$js.= 'selectFile("' . $txt . '");';
					}
				} else {
					$jsCmd->addMsg(sprintf(g_l('alert', '[path_exists]'), str_replace($_SERVER['DOCUMENT_ROOT'], '', $new)), we_base_util::WE_MESSAGE_ERROR);
				}
			}
			$js.= "drawDir(top.fileSelect.data.currentDir);selectFile(top.fileSelect.data.currentName);";
			break;
		case "delete_file":
			if(!($fid = we_base_request::_(we_base_request::FILE, "fid"))){
				break;
			}
			$foo = f('SELECT ID FROM ' . FILE_TABLE . ' WHERE Path="' . $DB_WE->escape($fid) . '"');
			if(preg_match('|' . WEBEDITION_PATH . '|', $fid) || ($fid == rtrim(WEBEDITION_PATH, '/')) || strpos("..", $fid) || $foo || $fid == $_SERVER['DOCUMENT_ROOT'] || $fid . "/" == $_SERVER['DOCUMENT_ROOT']){
				$jsCmd->addMsg(g_l('alert', '[access_denied]'), we_base_util::WE_MESSAGE_ERROR);
				break;
			}
			if(we_base_request::_(we_base_request::BOOL, "ask")){
				if(!is_link($fid) && is_dir($fid)){
					$js.= 'if (window.confirm("' . g_l('alert', '[delete_folder]') . '")){delFile(0);}';
				} else if(is_link($fid) || is_file($fid)){
					$js.= 'if (window.confirm("' . g_l('alert', '[delete]') . '")){delFile(0);}';
				}
			} else {
				if(!is_link($fid) && is_dir($fid)){
					delDir($fid);
				} else if(!@unlink($fid)){
					$jsCmd->addMsg(sprintf(g_l('alert', '[delete_nok_error]'), $fid), we_base_util::WE_MESSAGE_ERROR);
				}
			}
			$js.= "selectFile('');drawDir(top.fileSelect.data.currentDir);";
	}
}

echo we_html_tools::getHtmlTop('', '', '', (!$cmd || $cmd != "save_last" ?
		we_html_element::jsScript(JS_DIR . 'selectors/sselector_cmd.js', "top.fileSelect.data.filter='" . we_base_request::_(we_base_request::STRING, 'filter') . "';selectInternal=" . intval($selectInternal) . ";") .
		we_html_element::jsElement($js) : '') .
	$jsCmd->getCmds(), we_html_element::htmlBody()
);

