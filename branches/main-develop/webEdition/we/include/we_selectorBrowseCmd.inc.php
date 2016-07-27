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

we_html_tools::protect(array('BROWSE_SERVER', 'SITE_IMPORT', 'ADMINISTRATOR'));

echo we_html_tools::getHtmlTop() . STYLESHEET;
$cmd = we_base_request::_(we_base_request::STRING, 'cmd');

if($cmd === "save_last"){
	$_SESSION['user']["LastDir"] = $last;
}
if(!$cmd || $cmd != "save_last"){
	$selectOwn = we_base_request::_(we_base_request::BOOL, 'selectOwn', false);

	echo we_html_element::jsScript(JS_DIR . 'selectors/sselector_cmd.js');
	?>
	<script><!--<?php
	echo '
filter="' . we_base_request::_(we_base_request::STRING, 'filter') . '";
selectOwn=' . intval($selectOwn) . ';
';

	function delDir($dir){
		$d = dir($dir);
		while(false !== ($entry = $d->read())){
			if($entry != "." && $entry != ".."){
				if(is_dir($dir . "/" . $entry)){
					delDir($dir . "/" . $entry);
				} else if(is_file($dir . "/" . $entry)){
					if(!@unlink($dir . "/" . $entry)){
						echo we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[delete_nok_file]'), $entry), we_message_reporting::WE_MESSAGE_ERROR);
					}
				} else {
					echo we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[delete_nok_noexist]'), $entry), we_message_reporting::WE_MESSAGE_ERROR);
				}
			}
		}
		if(!rmdir($dir)){
			echo we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[delete_nok_folder]'), $dir), we_message_reporting::WE_MESSAGE_ERROR);
		}
	}

	$txt = we_base_request::_(we_base_request::FILE, 'txt', '');
	switch(we_base_request::_(we_base_request::STRING, "cmd")){
		case "new_folder":
			echo 'drawDir(top.currentDir);';
			if($txt === ''){
				echo we_message_reporting::getShowMessageCall(g_l('alert', '[we_filename_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
				break;
			}
			if(preg_match('|[\'"<>/]|', $txt)){
				echo we_message_reporting::getShowMessageCall(g_l('alert', '[name_nok]'), we_message_reporting::WE_MESSAGE_ERROR);
				break;
			}
			$path = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . we_base_request::_(we_base_request::FILE, 'pat') . '/' . $txt);
			if(!is_dir($path)){
				echo (!we_base_file::createLocalFolderByPath($path) ?
					we_message_reporting::getShowMessageCall(g_l('alert', '[create_folder_nok]'), we_message_reporting::WE_MESSAGE_ERROR) :
					'selectFile("' . $txt . '");top.currentID="' . $path . '";');
			} else {
				echo we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[path_exists]'), str_replace($_SERVER['DOCUMENT_ROOT'], '', $path)), we_message_reporting::WE_MESSAGE_ERROR);
			}
			break;
		case "rename_folder":
			if($txt === ''){
				echo we_message_reporting::getShowMessageCall(g_l('alert', '[we_filename_empty]'), we_message_reporting::WE_MESSAGE_ERROR) .
				"drawDir(top.currentDir);";
				break;
			}
			if(preg_match('|[\'"<>/]|', $txt)){
				echo we_message_reporting::getShowMessageCall(g_l('alert', '[name_nok]'), we_message_reporting::WE_MESSAGE_ERROR) .
				"drawDir(top.currentDir);";
				break;
			}
			$pat = we_base_request::_(we_base_request::FILE, 'pat');
			$old = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $pat . '/' . we_base_request::_(we_base_request::FILE, 'sid'));
			$new = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $pat . '/' . $txt);
			if($old != $new){
				if(!is_dir($new)){
					echo (!rename($old, $new) ?
						we_message_reporting::getShowMessageCall(g_l('alert', '[rename_folder_nok]'), we_message_reporting::WE_MESSAGE_ERROR) :
						'selectFile("' . $txt . '");');
				} else {
					$we_responseText = sprintf(g_l('alert', '[path_exists]'), str_replace($_SERVER['DOCUMENT_ROOT'], '', $new));
					echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
				}
			}
			echo 'drawDir(top.currentDir);';

			break;
		case "rename_file":
			if($txt === ''){
				echo we_message_reporting::getShowMessageCall(g_l('alert', '[we_filename_empty]'), we_message_reporting::WE_MESSAGE_ERROR) .
				"drawDir(top.currentDir);";
				break;
			}
			if(preg_match('|[\'"<>/]|', $txt)){
				echo we_message_reporting::getShowMessageCall(g_l('alert', '[name_nok]'), we_message_reporting::WE_MESSAGE_ERROR) .
				"drawDir(top.currentDir);";
				break;
			}
			$pat = we_base_request::_(we_base_request::FILE, 'pat');
			$txt = we_base_request::_(we_base_request::FILE, 'txt');
			$old = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $pat . '/' . we_base_request::_(we_base_request::FILE, "sid"));
			$new = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $pat . '/' . $txt);
			if($old != $new){
				if(!file_exists($new)){
					echo (!rename($old, $new) ?
						we_message_reporting::getShowMessageCall(g_l('alert', '[rename_file_nok]'), we_message_reporting::WE_MESSAGE_ERROR) :
						'selectFile("' . $txt . '");');
				} else {
					$we_responseText = sprintf(g_l('alert', '[path_exists]'), str_replace($_SERVER['DOCUMENT_ROOT'], '', $new));
					echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
				}
			}
			echo "drawDir(top.currentDir);selectFile(top.currentName);";
			break;
		case "delete_file":
			if(!($fid = we_base_request::_(we_base_request::FILE, "fid"))){
				break;
			}
			$foo = f('SELECT ID FROM ' . FILE_TABLE . ' WHERE Path="' . $DB_WE->escape($fid) . '"');
			if(preg_match('|' . WEBEDITION_PATH . '|', $fid) || ($fid == rtrim(WEBEDITION_PATH, '/')) || strpos("..", $fid) || $foo || $fid == $_SERVER['DOCUMENT_ROOT'] || $fid . "/" == $_SERVER['DOCUMENT_ROOT']){
				echo we_message_reporting::getShowMessageCall(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR);
				break;
			}
			if(we_base_request::_(we_base_request::BOOL, "ask")){
				if(!is_link($fid) && is_dir($fid)){
					echo 'if (confirm("' . g_l('alert', '[delete_folder]') . '")){delFile(0);}';
				} else if(is_link($fid) || is_file($fid)){
					echo 'if (confirm("' . g_l('alert', '[delete]') . '")){delFile(0);}';
				}
			} else {
				if(!is_link($fid) && is_dir($fid)){
					delDir($fid);
				} else if(!@unlink($fid)){
					echo we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[delete_nok_error]'), $fid), we_message_reporting::WE_MESSAGE_ERROR);
				}
			}
			echo "selectFile('');drawDir(top.currentDir);";
	}
	?>
		//-->
	</script>
<?php } ?>
</head>

<body>
</body>
</html>