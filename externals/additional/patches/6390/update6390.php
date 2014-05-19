<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");

$all = array();
if($all = file(WEBEDITION_PATH . 'liveUpdate/includes/del.files', FILE_IGNORE_NEW_LINES)){
	$delFiles=array();
	foreach($all as $cur){
		if(file_exists(WEBEDITION_PATH . $cur)){
			if(is_file(WEBEDITION_PATH . $cur)){
				$delFiles[]=$cur;
				unlink(WEBEDITION_PATH . $cur);
			} elseif(is_dir(WEBEDITION_PATH . $cur)){
				$delFiles[]='Folder: '. $cur;
				we_util_File::deleteLocalFolder(WEBEDITION_PATH . $cur, false);
			}
		}
	}
}
file_put_contents(WEBEDITION_PATH . 'liveUpdate/includes/del.files', ($all ? "Deleted Files: " . count($delFiles) . "\n\n" . implode("\n", $delFiles) : "File del.files not found or empty"));

return true;