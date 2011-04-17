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

include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_html_tools.inc.php");

protect();

$_cmd_string = '';

if (isset($_REQUEST['SEEM_edit_include']) && $_REQUEST['SEEM_edit_include']) {
	for ($i=1; $i<4; $i++) {
		$_cmd_string .= ",'" . $_REQUEST['we_cmd'][$i] . "'";

	}
	$_cmd_string .= ",'SEEM_edit_include'";
}

?><html>
<head>
<script type="text/javascript">
	function we_cmd(){
		var args = "";
		for(var i = 0; i < arguments.length; i++){
			args += 'arguments['+i+']' + ( (i < (arguments.length-1)) ? ',' : '');
		}
		eval('parent.we_cmd('+args+')');
	}

	function doSafariLoad() {
		window.frames["multiEditorDocumentControllerFrame"].document.location = "<?php print WEBEDITION_DIR ?>multiEditor/EditorFrameController.php";

	}

	function startMultiEditor() {
		we_cmd('start_multi_editor'<?php print $_cmd_string; ?>);

	}

</script>
</head>
<?php //switch($GLOBALS["BROWSER"]){ ?>
		<div style="position:fixed;width:100%;height:100%;top:0;left:0;border: 1px solid black;">
       <div style="height:22px;width:100%;">
				<iframe src="<?php print WEBEDITION_DIR ?>multiEditor/multiTabs.php" style="border:0;width: 100%;height:100%;" name="multiEditorDocumentTabsFrame" scrolling="no"></iframe>
			</div>
			<div style="width: 100%;height:100%;">
				<iframe src="<?php print WEBEDITION_DIR ?>multiEditor/multiEditorFrameset.php" name="multiEditorEditorFramesets" style="border:0;width:100%;height:100%;" scrolling="no"></iframe>
       </div>
       <div style="height:0">
				<iframe src="<?php print WEBEDITION_DIR ?>multiEditor/EditorFrameController.php" name="multiEditorDocumentControllerFrame" style="border:0;" scrolling="no" onload="startMultiEditor();"></iframe>
			</div>
     </div>
</html>