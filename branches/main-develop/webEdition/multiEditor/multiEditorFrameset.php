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
?><html>
	<head>

		<script type="text/javascript"><!--
			function we_cmd(){
				var args = "";
				for(var i = 0; i < arguments.length; i++){
					args += 'arguments['+i+']' + ( (i < (arguments.length-1)) ? ',' : '');
				}
				eval('parent.we_cmd('+args+')');
			}
			//-->
		</script>

	</head>
	<body style="margin:0px;"><?php
$MULTIEDITOR_AMOUNT = (isset($_SESSION) && isset($_SESSION['we_mode']) && $_SESSION['we_mode'] == 'seem') ? 1 : 16;

for($i = 0; $i < $MULTIEDITOR_AMOUNT; $i++){
	echo '	<iframe frameBorder="0" style="' . ($i == 0 ? '' : 'display:none;') . 'margin:0px;border:0px;width:100%;height:100%;overflow: scroll;" src="' . HTML_DIR . 'blank_editor.html" name="multiEditFrame_' . $i . '" id="multiEditFrame_' . $i . '"  noresize ></iframe>';
}
?>
	</body>
</html>