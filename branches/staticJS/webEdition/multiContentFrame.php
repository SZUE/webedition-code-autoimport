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

$_cmd_string = '';

if(we_base_request::_(we_base_request::BOOL, 'SEEM_edit_include')){
	for($i = 1; $i < 4; $i++){
		$_cmd_string .= ",'" . we_base_request::_(we_base_request::RAW, 'we_cmd', '', $i) . "'";
	}
	$_cmd_string .= ",'SEEM_edit_include'";
}

echo we_html_tools::getHtmlTop() .
 we_html_element::cssLink(CSS_DIR . 'multiEditor/multiEditor.css') .
 we_html_element::jsScript(JS_DIR . 'we_showMessage.js');
?>
<script type="text/javascript"><!--
	function we_cmd() {
		var args = "";
		for (var i = 0; i < arguments.length; i++) {
			args += 'arguments[' + i + ']' + ((i < (arguments.length - 1)) ? ',' : '');
		}
		eval('parent.we_cmd(' + args + ')');
	}

	function startMultiEditor() {
		we_cmd('start_multi_editor'<?php echo $_cmd_string; ?>);
	}

	var isChrome =<?php echo intval(we_base_browserDetect::isChrome()); ?>;
	var curUserID =<?php echo intval($_SESSION["user"]["ID"]); ?>;
	var g_l = {
		'eplugin_exit_doc': "<?php echo g_l('multiEditor', '[eplugin_exit_doc]'); ?>",
		'no_editor_left': "<?php echo we_message_reporting::prepareMsgForJS(g_l('multiEditor', '[no_editor_left]')); ?>"
	}
//-->
</script>
<?php
echo we_html_element::jsScript(JS_DIR . 'multiEditor/EditorFrameController.js');
?>
</head>
<body onresize="setFrameSize()" onload="init();
		startMultiEditor();">
	<div style="position:absolute;top:0px;bottom:0px;right:0px;left:0px;overflow: hidden;background-color: white;">
		<div style="position:absolute;top:0px;height:22px;width:100%;background-color: Silver; border-top: 1px solid #000000;" id="multiEditorDocumentTabsFrameDiv">
<?php include(WEBEDITION_PATH . 'multiEditor/multiTabs.inc.php'); ?>
		</div>
		<div style="position:absolute;top:22px;bottom:0px;left:0px;right:0px;overflow: hidden;" id="multiEditorEditorFramesetsDiv"><?php
			$count = (isset($_SESSION) && isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE) ? 1 : 32;

			for($i = 0; $i < $count; $i++){
				//'overflow:hidden;' removed to fix bug #6540
				echo '<iframe frameBorder="0" style="' . ($i == 0 ? 'width:100%;height:100%;' : (we_base_browserDetect::isChrome() ? 'width:100%;height:100%;display:none;' : 'width:0px;height:0px;')) . 'margin:0px;border:0px;" src="' . HTML_DIR . 'blank_editor.html" name="multiEditFrame_' . $i . '" id="multiEditFrame_' . $i . '"  noresize ></iframe>';
			}
			?>
		</div>
	</div>
</body>
</html>