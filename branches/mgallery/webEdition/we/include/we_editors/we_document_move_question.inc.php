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
we_html_tools::protect();
echo we_html_tools::getHtmlTop(g_l('global', '[question]')) .
 STYLESHEET;
?>
</head>
<body class="weEditorBody" onload="self.focus();" onblur="self.focus();">
	<?php
	$yesCmd = "url = '" . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=rebuild&step=2&btype=rebuild_filter';new (WE().util.jsWindow)(window, url,'templateMoveQuestion',-1,-1,600,135,true,false,true);opener.top.toggleBusy(1);self.close();";
	$noCmd = "self.close();opener.top.toggleBusy(0);";
	$cancelCmd = "self.close();opener.top.toggleBusy(0);";

	echo we_html_tools::htmlYesNoCancelDialog(g_l('alert', '[document_move_warning]'), IMAGE_DIR . 'alert.gif', true, true, true, $yesCmd, $noCmd, $cancelCmd);
	?>
</body>
</html>