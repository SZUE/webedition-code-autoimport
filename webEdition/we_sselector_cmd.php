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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();

echo we_html_tools::getHtmlTop() . STYLESHEET;

if(isset($_REQUEST["cmd"]) && $_REQUEST["cmd"] == "save_last"){
	$_SESSION["user"]["LastDir"] = $last;
}
if(!isset($_REQUEST["cmd"]) || (isset($_REQUEST["cmd"]) && $_REQUEST["cmd"] != "save_last")){
	?>
	<script type="text/javascript"><!--

		function drawNewFolder() {
			for (var i = 0; i < top.allentries.length; i++) {
				if (elem = top.fsbody.document.getElementById(top.allentries[i])) {
					elem.style.backgroundColor = 'white';
				}
			}
			drawDir(top.currentDir, "new_folder");
		}

		function setFilter(filter) {
			top.currentFilter = filter;
			drawDir(top.currentDir);
		}

		function setDir(dir) {
			var a = top.fsheader.document.forms["we_form"].elements["lookin"].options;
			if (a.length - 2 > -1) {
				for (j = 0; j < a.length; j++) {
					if (a[j].value === dir) {
						a.length = j + 1;
						a[j].selected = true;
					}
				}
	<?php if(isset($_REQUEST["filter"]) && ($_REQUEST["filter"] == "folder" || $_REQUEST["filter"] == "filefolder")){ ?>
					selectFile(dir);
	<?php } ?>
				top.currentDir = dir;
				selectDir();
			}
			else {
	<?php print we_message_reporting::getShowMessageCall(g_l('fileselector', "[already_root]"), we_message_reporting::WE_MESSAGE_ERROR); ?>
			}
		}

		function goUp() {
			var a = top.fsheader.document.forms["we_form"].elements["lookin"].options;
			if (a.length - 2 > -1) {
				setDir(a[a.length - 2].value);
			} else {
	<?php print we_message_reporting::getShowMessageCall(g_l('fileselector', '[already_root]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
			}
		}

		function selectFile(fid) {
			if (fid !== "/") {
				top.currentID = top.sitepath + top.rootDir + top.currentDir + ((top.currentDir != "/") ? "/" : "") + fid;
				top.currentName = fid;
				top.fsfooter.document.forms["we_form"].elements["fname"].value = fid;
				if (top.fsbody.document.getElementById(fid)) {
					for (var i = 0; i < top.allentries.length; i++) {
						if (top.fsbody.document.getElementById(top.allentries[i]))
							top.fsbody.document.getElementById(top.allentries[i]).style.backgroundColor = 'white';
					}
					top.fsbody.document.getElementById(fid).style.backgroundColor = '#DFE9F5';
				}
			} else {
				top.currentID = top.sitepath;
				top.currentName = fid;
				top.fsfooter.document.forms["we_form"].elements["fname"].value = fid;
				if (top.fsbody.document.getElementById(fid)) {
					for (var i = 0; i < top.allentries.length; i++) {
						if (top.fsbody.document.getElementById(top.allentries[i]))
							top.fsbody.document.getElementById(top.allentries[i]).style.backgroundColor = 'white';
					}
					top.fsbody.document.getElementById(fid).style.backgroundColor = '#DFE9F5';
				}
			}
		}

		function selectDir() {
			if (arguments[0]) {
	<?php if(isset($_REQUEST["filter"]) && $_REQUEST["filter"] == "folder"){ ?>
					//selectFile(arguments[0],true);
	<?php } ?>
				top.currentDir = top.currentDir + (top.currentDir === "/" ? "" : "/") + arguments[0];
				top.fsheader.addOption(arguments[0], top.currentDir);
			}

			if (top.currentDir.substring(0, 12) === "<?php echo WEBEDITION_DIR; ?>" || top.currentDir === "<?php echo rtrim(WEBEDITION_DIR, '/'); ?>") {
				top.fsheader.weButton.disable("btn_new_dir_ss");
				top.fsheader.weButton.disable("btn_add_file_ss");
				top.fsheader.weButton.disable("btn_function_trash_ss");
			} else {
				top.fsheader.weButton.enable("btn_new_dir_ss");
				top.fsheader.weButton.enable("btn_add_file_ss");
				top.fsheader.weButton.enable("btn_function_trash_ss");
			}

			drawDir(top.currentDir);

		}

		function reorderDir(dir, order) {
			setTimeout('top.fsbody.location="we_sselector_body.php?dir=' + dir + '&ord=' + order + '&file=' + top.currentFilter + '&curID=' + escape(top.currentID) + '"', 100);
		}

		function drawDir(dir) {
			switch (arguments[1]) {
				case "new_folder":
					top.fsbody.location = "we_sselector_body.php?dir=" + escape(top.rootDir + dir) + "&nf=new_folder&file=" + top.currentFilter + "&curID=" + escape(top.currentID);
					break;
				case "rename_folder":
					if (arguments[2]) {
						top.fsbody.location = "we_sselector_body.php?dir=" + escape(top.rootDir + dir) + "&nf=rename_folder&sid=" + escape(arguments[2]) + "&file=" + top.currentFilter + "&curID=" + escape(top.currentID);
					}
					break;
				case "rename_file":
					if (arguments[2]) {
						top.fsbody.location = "we_sselector_body.php?dir=" + escape(top.rootDir + dir) + "&nf=rename_file&sid=" + escape(arguments[2]) + "&file=" + top.currentFilter + "&curID=" + escape(top.currentID);
					}
					break;
				default:
					setTimeout('top.fsbody.location="we_sselector_body.php?dir=' + escape(top.rootDir + dir) + '&file=' + top.currentFilter + '&curID=' + escape(top.currentID) + '"', 100);
			}
		}

		function delFile() {
			if ((top.currentID !== "") && (top.fsfooter.document.forms["we_form"].elements["fname"].value !== "")) {
				top.fscmd.location = "we_sselector_cmd.php?cmd=delete_file&fid=" + top.currentID + "&ask=" + arguments[0];
			} else {
	<?php print we_message_reporting::getShowMessageCall(g_l('fileselector', "[edit_file_nok]"), we_message_reporting::WE_MESSAGE_ERROR); ?>
			}
		}

	<?php

	function delDir($dir){
		$d = dir($dir);
		while(false !== ($entry = $d->read())){
			if($entry != "." && $entry != ".."){
				if(is_dir($dir . "/" . $entry)){
					delDir($dir . "/" . $entry);
				} else if(is_file($dir . "/" . $entry)){
					if(!@unlink($dir . "/" . $entry)){
						echo we_message_reporting::getShowMessageCall(sprintf(g_l('alert', "[delete_nok_file]"), $entry), we_message_reporting::WE_MESSAGE_ERROR);
					}
				} else {
					echo we_message_reporting::getShowMessageCall(sprintf(g_l('alert', "[delete_nok_noexist]"), $entry), we_message_reporting::WE_MESSAGE_ERROR);
				}
			}
		}
		if(!@rmdir($dir)){
			echo we_message_reporting::getShowMessageCall(sprintf(g_l('alert', "[delete_nok_folder]"), $dir), we_message_reporting::WE_MESSAGE_ERROR);
		}
	}

	switch(weRequest('string', "cmd")){
		case "new_folder":
			echo 'drawDir(top.currentDir);';
			if(!($_REQUEST["txt"])){
				echo we_message_reporting::getShowMessageCall(g_l('alert', "[we_filename_empty]"), we_message_reporting::WE_MESSAGE_ERROR);
			} else if(preg_match('|[\'"<>/]|', $_REQUEST["txt"])){
				echo we_message_reporting::getShowMessageCall(g_l('alert', "[name_nok]"), we_message_reporting::WE_MESSAGE_ERROR);
			} else {
				$path = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['pat'] . '/' . $_REQUEST['txt']);
				if(!@is_dir($path)){
					echo (!we_util_File::createLocalFolder($path) ?
						we_message_reporting::getShowMessageCall(g_l('alert', "[create_folder_nok]"), we_message_reporting::WE_MESSAGE_ERROR) :
						'selectFile("' . $_REQUEST['txt'] . '");top.currentID="' . $path . '";');
				} else {
					echo we_message_reporting::getShowMessageCall(sprintf(g_l('alert', "[path_exists]"), str_replace($_SERVER['DOCUMENT_ROOT'], '', $path)), we_message_reporting::WE_MESSAGE_ERROR);
				}
			}
			break;

		case "rename_folder":
			if($_REQUEST["txt"] == ''){
				echo we_message_reporting::getShowMessageCall(g_l('alert', "[we_filename_empty]"), we_message_reporting::WE_MESSAGE_ERROR) .
				"drawDir(top.currentDir);\n";
			} else if(preg_match('|[\'"<>/]|', $_REQUEST["txt"])){
				echo we_message_reporting::getShowMessageCall(g_l('alert', "[name_nok]"), we_message_reporting::WE_MESSAGE_ERROR) .
				"drawDir(top.currentDir);\n";
			} else {
				$old = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['pat'] . '/' . $_REQUEST['sid']);
				$new = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['pat'] . '/' . $_REQUEST['txt']);
				if($old != $new){
					if(!@is_dir($new)){
						echo (!rename($old, $new) ?
							we_message_reporting::getShowMessageCall(g_l('alert', "[rename_folder_nok]"), we_message_reporting::WE_MESSAGE_ERROR) :
							'selectFile("' . $_REQUEST["txt"] . '");');
					} else {
						$we_responseText = sprintf(g_l('alert', "[path_exists]"), str_replace($_SERVER['DOCUMENT_ROOT'], '', $new));
						echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
					}
				}
				echo 'drawDir(top.currentDir);';
			}
			break;
		case "rename_file":
			if($_REQUEST["txt"] == ""){
				echo we_message_reporting::getShowMessageCall(g_l('alert', "[we_filename_empty]"), we_message_reporting::WE_MESSAGE_ERROR) .
				"drawDir(top.currentDir);\n";
			} else if(preg_match('|[\'"<>/]|', $_REQUEST["txt"])){
				echo we_message_reporting::getShowMessageCall(g_l('alert', "[name_nok]"), we_message_reporting::WE_MESSAGE_ERROR) .
				"drawDir(top.currentDir);\n";
			} else {
				$old = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $_REQUEST["pat"] . '/' . $_REQUEST["sid"]);
				$new = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $_REQUEST["pat"] . '/' . $_REQUEST["txt"]);
				if($old != $new){
					if(!file_exists($new)){
						echo (!rename($old, $new) ?
							we_message_reporting::getShowMessageCall(g_l('alert', "[rename_file_nok]"), we_message_reporting::WE_MESSAGE_ERROR) :
							'selectFile("' . $_REQUEST["txt"] . '");' . "\n");
					} else {
						$we_responseText = sprintf(g_l('alert', "[path_exists]"), str_replace($_SERVER['DOCUMENT_ROOT'], '', $new));
						echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
					}
				}
				echo "drawDir(top.currentDir);selectFile(top.currentName);";
			}
			break;
		case "delete_file":
			if(isset($_REQUEST["fid"])){
				$foo = f("SELECT ID FROM " . FILE_TABLE . " WHERE Path='" . $DB_WE->escape($_REQUEST["fid"]) . "'");
				if(preg_match('|' . WEBEDITION_PATH . '|', $_REQUEST["fid"]) || ($_REQUEST["fid"] == rtrim(WEBEDITION_PATH, '/')) || strpos("..", $_REQUEST["fid"]) || $foo || $_REQUEST["fid"] == $_SERVER['DOCUMENT_ROOT'] || $_REQUEST["fid"] . "/" == $_SERVER['DOCUMENT_ROOT']){
					echo we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR);
					break;
				}
				if($_REQUEST["ask"]){
					if(!is_link($_REQUEST["fid"]) && is_dir($_REQUEST["fid"])){
						echo "if (confirm(\"" . g_l('alert', "[delete_folder]") . "\")){delFile(0);}";
					} else if(is_link($_REQUEST["fid"]) || is_file($_REQUEST["fid"])){
						echo "if (confirm(\"" . g_l('alert', "[delete]") . "\")){delFile(0);}";
					}
				} else {
					if(!is_link($_REQUEST["fid"]) && is_dir($_REQUEST["fid"])){
						delDir($_REQUEST["fid"]);
					} else if(!@unlink($_REQUEST["fid"])){
						echo we_message_reporting::getShowMessageCall(sprintf(g_l('alert', "[delete_nok_error]"), $_REQUEST["fid"]), we_message_reporting::WE_MESSAGE_ERROR);
					}
				}
				echo "selectFile('');drawDir(top.currentDir);";
			}
	}
	?>
	//-->
	</script>
<?php } ?>
</head>

<body>
</body>
</html>