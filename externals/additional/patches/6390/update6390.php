<?php
/*
Attempt to update
*/
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");

$all = file('./del.files', FILE_IGNORE_NEW_LINES);
if(!$all){
	t_e('file not found');
}
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

t_e('deleted Files',$delFiles);

return true;
