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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();
echo we_html_tools::getHtmlTop('command-bridge', '', 5);
?>
<script  type="text/javascript"><!--
	// bugfix WE-356
	self.focus();
<?php
if(isset($_REQUEST['wecmd0'])){ // when calling from applet (we can not call directly we_cmd[0] with the applet =>  Safari OSX doesn't support live connect)
	$_REQUEST['we_cmd'][0] = $_REQUEST['wecmd0'];
}
foreach($_REQUEST['we_cmd'] as &$cmdvalue){
	$cmdvalue = preg_replace('/[^a-z0-9_-]/i', '', strip_tags($cmdvalue));
}

switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)){
	case "trigger_save_document":
		echo 'if(top.weEditorFrameController.getActiveDocumentReference() && top.weEditorFrameController.getActiveDocumentReference().frames[3] && top.weEditorFrameController.getActiveDocumentReference().frames[3].weCanSave){
	top.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(false);
	top.weEditorFrameController.getActiveDocumentReference().frames[3].we_save_document();
}else{
	' . we_message_reporting::getShowMessageCall(g_l('alert', "[nothing_to_save]"), we_message_reporting::WE_MESSAGE_ERROR) . '
}
';
		break;

	case "trigger_publish_document":
		echo 'if(top.weEditorFrameController.getActiveDocumentReference() && top.weEditorFrameController.getActiveDocumentReference().frames[3] && top.weEditorFrameController.getActiveDocumentReference().frames[3].weCanSave){
	top.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(true);
	top.weEditorFrameController.getActiveDocumentReference().frames[3].we_save_document();
}else{
	' . we_message_reporting::getShowMessageCall(g_l('alert', "[nothing_to_publish]"), we_message_reporting::WE_MESSAGE_ERROR) . '
}
';
		break;
	case "new_webEditionPage":
		echo 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::WEDOCUMENT . '");';
		break;
	case "new_image":
		echo 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::IMAGE . '");';
		break;
	case "new_html_page":
		echo 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::HTML . '");';
		break;
	case "new_flash_movie":
		echo 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::FLASH . '");';
		break;
	case "new_quicktime_movie":
		echo 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::QUICKTIME . '");';
		break;
	case "new_javascript":
		echo 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::JS . '");';
		break;
	case "new_text_plain":
		echo 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::TEXT . '");';
		break;
	case "new_text_xml":
		echo 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::XML . '");';
		break;
	case "new_text_htaccess":
		echo 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::HTACESS . '");';
		break;
	case "new_css_stylesheet":
		echo 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::CSS . '");';
		break;
	case "new_binary_document":
		echo 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::APPLICATION . '");';
		break;
	case "new_template":
		echo 'top.we_cmd("new","' . TEMPLATES_TABLE . '","","' . we_base_ContentTypes::TEMPLATE . '");';
		break;
	case "new_document_folder":
		echo 'top.we_cmd("new","' . FILE_TABLE . '","","folder");';
		break;
	case "new_template_folder":
		echo 'top.we_cmd("new","' . TEMPLATES_TABLE . '","","folder");';
		break;
	case "delete_documents":
		echo 'top.we_cmd("del",1,"' . FILE_TABLE . '");';
		break;
	case "delete_templates":
		echo 'top.we_cmd("del",1,"' . TEMPLATES_TABLE . '");';
		break;
	case "move_documents":
		echo 'top.we_cmd("mv",1,"' . FILE_TABLE . '");';
		break;
	case "move_templates":
		echo 'top.we_cmd("mv",1,"' . TEMPLATES_TABLE . '");';
		break;

	case "openDelSelector":
		$openTable = FILE_TABLE;
		if(isset($_SESSION['weS']['seemForOpenDelSelector']['Table'])){
			$openTable = $_SESSION['weS']['seemForOpenDelSelector']['Table'];
			unset($_SESSION['weS']['seemForOpenDelSelector']['Table']);
		}
		$_cmd = 'top.we_cmd("openDelSelector","","' . $openTable . '","","","","","","",1);';
		echo "setTimeout('" . $_cmd . "',50)";
		break;

	case "export_documents":
		$_tbl = FILE_TABLE;
	case "export_templates":
		$_tbl = (!isset($_tbl) ? $_tbl : TEMPLATES_TABLE);
	case "export_objects":
		$_tbl = (!isset($_tbl) ? $_tbl : OBJECT_FILES_TABLE);



	default:
		$regs = array();
		if(preg_match('/^new_dtPage(.+)$/', $_REQUEST['we_cmd'][0], $regs)){
			$dt = $regs[1];
			echo 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::WEDOCUMENT . '","' . $dt . '");';
			break;
		} else if(preg_match('/^new_ClObjectFile(.+)$/', $_REQUEST['we_cmd'][0], $regs)){
			$clID = $regs[1];
			echo 'top.we_cmd("new","' . OBJECT_FILES_TABLE . '","","objectFile","' . $clID . '");';
			break;
		}
		$arr = array();
		foreach($_REQUEST['we_cmd'] as $cur){
			$arr[] = '\'' . str_replace(array('\'', '"'), array('\\\'', '\\"'), $cur) . '\'';
		}
		echo 'setTimeout("top.we_cmd(' . implode(',', $arr) . ')",50);';
}
?>
	//-->
</script>
</head><body></body></html>