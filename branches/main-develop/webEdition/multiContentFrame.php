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

$_cmd_string = '';

if(weRequest('bool', 'SEEM_edit_include')){
	for($i = 1; $i < 4; $i++){
		$_cmd_string .= ",'" . weRequest('raw', 'we_cmd', '', $i) . "'";
	}
	$_cmd_string .= ",'SEEM_edit_include'";
}

echo we_html_tools::getHtmlTop();
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
		we_cmd('start_multi_editor'<?php print $_cmd_string; ?>);
	}
//-->
</script>
<?php
include(WEBEDITION_PATH . 'multiEditor/EditorFrameController.inc.php');
?>
</head>
<?php //WEEXT: registerWeIframe ?>
<body onLoad="if(typeof top.WE !== 'undefined'){top.WE.app.getController('Bridge').registerWeIframe(this, true);}" onresize="setFrameSize()">
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
	<?php
	echo we_html_element::jsElement('startMultiEditor()');
	?>
</body>
</html>