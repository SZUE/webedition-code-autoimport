<?php
/**
 * webEdition CMS
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
 * @copyright  Copyright (c) 2008 living-e AG (http://www.living-e.com)
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_html_tools.inc.php");

protect();

$cols = array();
$frames = "";

for ($i=0;$i<MULTIEDITOR_AMOUNT;$i++) {
	
	$cols[] = "*";
	$frames .= '	<frame src="about:blank" name="multiEditFrame_' . $i . '" id="multiEditFrame_' . $i . '"  noresize />'."\n";
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
</script>

</head>
<frameset id="multiEditorFrameset" cols="<?php print implode(",", $cols); ?>" border="0" frameborder="no" framespacing="0" noresize>
<?php

print $frames;

?>
</frameset>
</html>