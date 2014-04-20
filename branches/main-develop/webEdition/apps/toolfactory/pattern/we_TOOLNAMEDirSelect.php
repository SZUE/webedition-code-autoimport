require_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we.inc.php');
include_once(WEBEDITION_PATH.'apps/<?php echo $TOOLNAME; ?>/we_<?php echo $TOOLNAME; ?>DirSelector.class.php');

we_html_tools::protect();
$_SERVER['SCRIPT_NAME'] = WEBEDITION_DIR.'apps/<?php print $TOOLNAME; ?>/we_<?php print $TOOLNAME; ?>DirSelect.php';
$fs = new we_<?php echo $TOOLNAME; ?>DirSelector(weRequest('int','id',weRequest('int','we_cmd',0,1)),
							isset($JSIDName) ? $JSIDName : weRequest('string','JSIDName',weRequest('string','we_cmd','',2)),
							isset($JSTextName) ? $JSTextName : weRequest('string','JSTextName',weRequest('string','we_cmd','',3)),
							isset($JSCommand) ? $JSCommand : weRequest('raw','JSCommand',weRequest('raw','we_cmd','',4)),
							isset($order) ? $order : weRequest('string','order',''),
							isset($we_editDirID) ? $we_editDirID : weRequest('int','we_editDirID',0),
							isset($we_FolderText) ? $we_FolderText : weRequest('raw','we_FolderText',''));

$fs->printHTML(weRequest('string','what',we_fileselector::FRAMESET);
