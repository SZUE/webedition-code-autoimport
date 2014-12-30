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
<script type="text/javascript"><!--
	// bugfix WE-356
	self.focus();
<?php

function getJSCommand($cmd0){
	switch($cmd0){
		case 'trigger_save_document':
			return'if(top.weEditorFrameController.getActiveDocumentReference() && top.weEditorFrameController.getActiveDocumentReference().frames[3] && top.weEditorFrameController.getActiveDocumentReference().frames[3].weCanSave){
	top.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(false);
	top.weEditorFrameController.getActiveDocumentReference().frames[3].we_save_document();
}else{
	' . we_message_reporting::getShowMessageCall(g_l('alert', '[nothing_to_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
}
';
		case 'trigger_publish_document':
			return 'if(top.weEditorFrameController.getActiveDocumentReference() && top.weEditorFrameController.getActiveDocumentReference().frames[3] && top.weEditorFrameController.getActiveDocumentReference().frames[3].weCanSave){
	top.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(true);
	top.weEditorFrameController.getActiveDocumentReference().frames[3].we_save_document();
}else{
	' . we_message_reporting::getShowMessageCall(g_l('alert', '[nothing_to_publish]'), we_message_reporting::WE_MESSAGE_ERROR) . '
}
';
		case 'new_webEditionPage':
			return 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::WEDOCUMENT . '");';
		case 'new_image':
			return 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::IMAGE . '");';
		case 'new_html_page':
			return 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::HTML . '");';
		case 'new_flash_movie':
			return 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::FLASH . '");';
		case 'new_quicktime_movie':
			return 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::QUICKTIME . '");';
		case 'new_video_movie':
			return 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::VIDEO . '");';
		case 'new_audio_audio':
			return 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::AUDIO . '");';
		case 'new_javascript':
			return 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::JS . '");';
		case 'new_text_plain':
			return 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::TEXT . '");';
		case 'new_text_xml':
			return 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::XML . '");';
		case 'new_text_htaccess':
			return 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::HTACESS . '");';
		case 'new_css_stylesheet':
			return 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::CSS . '");';
		case 'new_binary_document':
			return 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::APPLICATION . '");';
		case 'new_template':
			return 'top.we_cmd("new","' . TEMPLATES_TABLE . '","","' . we_base_ContentTypes::TEMPLATE . '");';
		case 'new_document_folder':
			return 'top.we_cmd("new","' . FILE_TABLE . '","","folder");';
		case 'new_template_folder':
			return 'top.we_cmd("new","' . TEMPLATES_TABLE . '","","folder");';
		case 'delete_documents':
			return 'top.we_cmd("del",1,"' . FILE_TABLE . '");';
		case 'delete_templates':
			return 'top.we_cmd("del",1,"' . TEMPLATES_TABLE . '");';
		case 'move_documents':
			return 'top.we_cmd("mv",1,"' . FILE_TABLE . '");';
		case 'move_templates':
			return 'top.we_cmd("mv",1,"' . TEMPLATES_TABLE . '");';
		case 'openDelSelector':
			$openTable = FILE_TABLE;
			if(isset($_SESSION['weS']['seemForOpenDelSelector']['Table'])){
				$openTable = $_SESSION['weS']['seemForOpenDelSelector']['Table'];
				unset($_SESSION['weS']['seemForOpenDelSelector']['Table']);
			}
			$_cmd = 'top.we_cmd("openDelSelector","","' . $openTable . '","","","","","","",1);';
			return "setTimeout('" . $_cmd . "',50)";
		/* case "export_documents":
		  $_tbl = FILE_TABLE;
		  case "export_templates":
		  $_tbl = (isset($_tbl) ? $_tbl : TEMPLATES_TABLE);
		  case "export_objects":
		  $_tbl = (isset($_tbl) ? $_tbl : OBJECT_FILES_TABLE);
		 */
		default:
			$regs = array();
			if(preg_match('/^new_dtPage(.+)$/', $cmd0, $regs)){
				$dt = $regs[1];
				return 'top.we_cmd("new","' . FILE_TABLE . '","","' . we_base_ContentTypes::WEDOCUMENT . '","' . $dt . '");';
			}
			if(preg_match('/^new_ClObjectFile(.+)$/', $cmd0, $regs)){
				$clID = $regs[1];
				return 'top.we_cmd("new","' . OBJECT_FILES_TABLE . '","","objectFile","' . $clID . '");';
			}
			$arr = array();
			foreach($_REQUEST['we_cmd'] as $cur){
				$arr[] = '\'' . str_replace(array('\'', '"'), array('\\\'', '\\"'), preg_replace('/[^a-z0-9_-]/i', '', strip_tags($cur))) . '\'';
			}
			return 'setTimeout("top.we_cmd(' . implode(',', $arr) . ')",50);';
	}
}

if(($cmd0 = we_base_request::_(we_base_request::STRING, 'wecmd0')) !== false){ // when calling from applet (we can not call directly we_cmd[0] with the applet =>  Safari OSX doesn't support live connect)
	$_REQUEST['we_cmd'][0] = $cmd0;
}
echo getJSCommand(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0));
?>
	//-->
</script>
</head><body></body></html>