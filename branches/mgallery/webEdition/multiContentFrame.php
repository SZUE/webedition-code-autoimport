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
 STYLESHEET .
 we_html_element::cssLink(CSS_DIR . 'multiEditor/multiEditor.css');

?>
<script><!--
	function startMultiEditor() {
		WE().layout.multiTabs = new TabView(this.document);
		we_cmd('start_multi_editor'<?php echo $_cmd_string; ?>);
	}

//-->
</script>
<?php
echo we_html_element::jsScript(JS_DIR . 'multiEditor/EditorFrameController.js') .
 we_html_element::jsScript(JS_DIR . 'multiEditor/multiTabs.js');
?>
</head>
<body onresize="WE().layout.multiTabs.setFrameSize()" onload="startMultiEditor();" style="overflow: hidden;">
	<div id="multiEditorDocumentTabsFrameDiv">
		<div id="weMultiTabs">
			<div id="tabContainer" name="tabContainer">
			</div>
			<div class="hidden" id="tabDummy" title="" name="" onclick="WE().layout.multiTabs.selectFrame(this)">
				<nobr>
					<span class="spacer status" id="###loadId###" title="" ></span>
					<span id="###tabTextId###" class="text"></span>
					<span class="spacer">
						<i class="fa fa-asterisk modified" id="###modId###"></i>
						<span class="fa-stack close" id="###closeId###" onclick="WE().layout.multiTabs.onCloseTab(this)">
							<i class="fa fa-circle-o fa-stack-2x"></i>
							<i class="fa fa-close fa-stack-1x "></i>
						</span>
				</nobr>
			</div>
		</div>
	</div>
	<div id="multiEditorEditorFramesetsDiv"><?php
		$count = (isset($_SESSION) && isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE) ? 1 : 32;

		for($i = 0; $i < $count; $i++){
			//'overflow:hidden;' removed to fix bug #6540
			echo '<iframe style="' . ($i == 0 ? '' : (we_base_browserDetect::isChrome() ? 'display:none;' : 'width:0px;height:0px;')) . '" src="' . HTML_DIR . 'blank_editor.html" name="multiEditFrame_' . $i . '" id="multiEditFrame_' . $i . '"  noresize ></iframe>';
		}
		?>
	</div>
</body>
</html>