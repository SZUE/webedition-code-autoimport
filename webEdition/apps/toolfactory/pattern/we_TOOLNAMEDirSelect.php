require_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we.inc.php');
include_once(WEBEDITION_PATH.'apps/<?php echo $TOOLNAME; ?>/we_<?php echo $TOOLNAME; ?>DirSelector.class.php');

we_html_tools::protect();
$_SERVER['SCRIPT_NAME'] = WEBEDITION_DIR.'apps/<?php echo $TOOLNAME; ?>/we_<?php echo $TOOLNAME; ?>DirSelect.php';
$fs = new we_<?php echo $TOOLNAME; ?>DirSelector(we_base_request::_(we_base_request::INT,'id',we_base_request::_(we_base_request::INT,'we_cmd',0,1)),
							isset($JSIDName) ? $JSIDName : we_base_request::_(we_base_request::STRING,'JSIDName',we_base_request::_(we_base_request::STRING,'we_cmd','',2)),
							isset($JSTextName) ? $JSTextName : we_base_request::_(we_base_request::STRING,'JSTextName',we_base_request::_(we_base_request::STRING,'we_cmd','',3)),
							isset($JSCommand) ? $JSCommand : we_base_request::_(we_base_request::RAW,'JSCommand',we_base_request::_(we_base_request::RAW,'we_cmd','',4)),
							isset($order) ? $order : we_base_request::_(we_base_request::STRING,'order',''),
							isset($we_editDirID) ? $we_editDirID : we_base_request::_(we_base_request::INT,'we_editDirID',0),
							isset($we_FolderText) ? $we_FolderText : we_base_request::_(we_base_request::RAW,'we_FolderText',''));

$fs->printHTML(we_base_request::_(we_base_request::STRING,'what',we_fileselector::FRAMESET);
