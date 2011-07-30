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


include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_html_tools.inc.php");

protect();
htmlTop();

$table = isset($table) ? $table : FILE_TABLE;
?>
<script type="text/javascript">
function we_cmd(){
	var args = "";
	for(var i = 0; i < arguments.length; i++){
		args += 'arguments['+i+']' + ( (i < (arguments.length-1)) ? ',' : '');
	}
	eval('parent.we_cmd('+args+')');
}

</script>
</head>
<body>
<div style="position:absolute;top:0;bottom:0;left:0;right:0;">
  <div style="position:absolute;top:0;bottom:0;left:0;width:24px;">
		<iframe src="<?php print WEBEDITION_DIR ?>we_vtabs.php" style="border:0;width:100%;height:100%;overflow: hidden;" name="bm_vtabs"></iframe>
	</div>
	<div style="position:absolute;top:0;bottom:0;left:24px;right:0;border:0;" id="treeFrameDiv">
		<div style="position:absolute;top:0;height:1px;left:0;right:0;" id="bm_vtabsDiv">
			<iframe src="<?php print HTML_DIR ?>frameheader.html" name="treeheader" style="border:0;width:100%;height:100%;overflow: hidden;"></iframe>
		</div>
		<div style="position:absolute;bottom:0;height:40px;left:0;right:0;">
			<iframe src="treeInfo.php" name="infoFrame" style="border:0;width:100%;height:100%;overflow: hidden;"></iframe>
		</div>
		<div style="position:absolute;top:1px;bottom:40px;left:0;right:0;overflow: auto;" id="bm_mainDiv">
			<iframe src="treeMain.php" name="bm_main" onload="top.start()" style="border:0;width:100%;height:100%;"></iframe>
		</div>
	</div>
</div>
</body>
</html>