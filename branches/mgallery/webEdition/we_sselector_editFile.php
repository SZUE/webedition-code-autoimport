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

we_html_tools::protect(array("BROWSE_SERVER"));

echo we_html_tools::getHtmlTop() . STYLESHEET;

$we_fileData = "";

$id = we_base_request::_(we_base_request::FILE, 'id');
if(we_base_request::_(we_base_request::STRING, "cmd") === "save"){
	if(($data = we_base_request::_(we_base_request::RAW_CHECKED, "editFile")) !== false){
		we_base_file::save($id, $data);
	}
	$we_fileData = stripslashes(we_base_request::_(we_base_request::RAW_CHECKED, "editFile"));
} else if($id){
	$id = str_replace('//', '/', $id);
	$we_fileData = we_base_file::load($id);
	if($we_fileData === false){
		$we_alerttext = sprintf(g_l('alert', '[can_not_open_file]'), str_replace(str_replace("\\", "/", dirname($id)) . "/", "", $id), 1);
	}
}

$buttons = we_html_button::position_yes_no_cancel(
		we_html_button::create_button(we_html_button::SAVE, "javascript:document.forms[0].submit();"), null, we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();")
);
$content = '<textarea name="editFile" id="editFile" style="width:540px;height:380px;overflow: auto;">' . oldHtmlspecialchars($we_fileData) . '</textarea>';
?>
<script><!--
	function setSize() {
		var ta = document.getElementById("editFile");
		ta.style.width = (document.body.offsetWidth - 60) + "px";
		ta.style.height = (document.body.offsetHeight - 118) + "px";
	}
<?php
if(isset($we_alerttext)){
	echo we_message_reporting::getShowMessageCall($we_alerttext, we_message_reporting::WE_MESSAGE_ERROR);
	?>
		self.close();
<?php } ?>
	self.focus();
<?php if(isset($data) && (!isset($we_alerttext))){ ?>
		opener.top.fscmd.selectDir();
		self.close();
<?php } ?>
//-->
</script>
</head>
<body class="weDialogBody" onresize="setSize()" style="width:100%; height:100%"><div style="text-align:center">
		<form method="post">
			<input type="hidden" name="cmd" value="save" />
			<?php echo we_html_tools::htmlDialogLayout($content, g_l('global', '[edit_file]') . ": <span class=\"weMultiIconBoxHeadline\">" . str_replace(str_replace("\\", "/", dirname($id)) . "/", "", $id), $buttons, 1) . "</span>"; ?>
		</form></div>
</body>
</html>