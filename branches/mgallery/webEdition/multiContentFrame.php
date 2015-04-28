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

// generate ContentType JS-String
$_contentTypes = array();
$_contentTypes[] = '"cockpit": "icon_cockpit.gif"';
$ct = we_base_ContentTypes::inst();
foreach($ct->getContentTypes() as $ctype){
	$_contentTypes[] = '"' . $ctype . '":"' . $ct->getIcon($ctype) . '"';
}

/* FIXME: check if browser dependencies are really needed anymore!
 * Browser dependencies
 */

$browser = we_base_browserDetect::inst();
switch($browser->getBrowser()){
	case we_base_browserDetect::SAFARI:
		$heightPlus = 0;
		break;
	default:
		$heightPlus = 1;
}
?>
<script type="text/javascript"><!--
	function we_cmd() {
	var args = [];
					for (var i = 0; i < arguments.length; i++) {
	args.push(arguments[i]);
	}
	parent.we_cmd.apply(this, args);
	}

	function startMultiEditor() {
	we_cmd('start_multi_editor'<?php echo $_cmd_string; ?>);
	}

	var isChrome =<?php echo intval(we_base_browserDetect::isChrome()); ?>;
					var curUserID =<?php echo intval($_SESSION["user"]["ID"]); ?>;
					var g_l = {
					eplugin_exit_doc: "<?php echo g_l('multiEditor', '[eplugin_exit_doc]'); ?>",
									no_editor_left: "<?php echo we_message_reporting::prepareMsgForJS(g_l('multiEditor', '[no_editor_left]')); ?>"
					};
					var contentTypeApp = "<?php echo we_base_ContentTypes::APPLICATION; ?>";
					var heightPlus =<?php echo $heightPlus; ?>;
					var _Contentypes = {<?php echo implode(',', $_contentTypes); ?>};
//-->
</script>
<?php
echo we_html_element::jsScript(JS_DIR . 'multiEditor/EditorFrameController.js') .
 we_html_element::jsScript(JS_DIR . 'multiEditor/multiTabs.js');
?>
</head>
<body onresize="setFrameSize()" onload="init();
									startMultiEditor();" style="overflow: hidden;">
	<div id="multiEditorDocumentTabsFrameDiv">
		<div id="weMultiTabs">
			<div id="tabContainer" name="tabContainer">
			</div>
			<div class="hidden" id="tabDummy" title="" name="" onclick="top.weMultiTabs.selectFrame(this)">
				<nobr>
					<span class="spacer">&nbsp;<img src="<?php echo IMAGE_DIR ?>pixel.gif" id="###loadId###" title="" class="status"/>&nbsp;</span>
					<span id="###tabTextId###" class="text"></span>
					<span class="spacer"><img id="###modId###" class="status modified"/>
						<img src="<?php echo IMAGE_DIR ?>multiTabs/close.gif" id="###closeId###" border="0" vspace="0" hspace="0" onclick="top.weMultiTabs.onCloseTab(this)" onmouseover="this.src = '<?php echo IMAGE_DIR ?>multiTabs/closeOver.gif'" onmouseout="this.src = '<?php echo IMAGE_DIR ?>multiTabs/close.gif'" class="close" />&nbsp;</span>
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