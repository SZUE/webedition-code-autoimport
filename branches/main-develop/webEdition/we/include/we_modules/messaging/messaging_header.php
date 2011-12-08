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
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/java_menu/modules/module_menu_messaging.inc.php");
include_once( $_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/jsMessageConsole/messageConsole.inc.php" );

protect();

we_html_tools::htmlTop();

print STYLESHEET;

$port = defined("HTTP_PORT") ? HTTP_PORT : "";
$protocol=getServerProtocol();
$jmenu = new weJavaMenu($we_menu_messaging, SERVER_NAME, 'top.opener.top.load', $protocol, $port,300);
echo we_htmlElement::jsScript(JS_DIR.'images.js');
?>
	<script type="text/javascript"><!--
        function menuaction(cmd){
				top.opener.top.load.location.replace("/webEdition/we_lcmd.php?we_cmd[0]="+cmd);
	    }
			//-->
	</script>

	<body background="<?php print IMAGE_DIR ?>java_menu/background.gif" style="background-color:#bfbfbf; background-repeat:repeat;margin:0px 0px 0px 0px">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td align=left valign=top>
					<?php $jmenu->printMenu(); ?>
				</td>
				<td align="right">
				<?php
					include_once( $_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/jsMessageConsole/messageConsole.inc.php" );
					print createMessageConsole("moduleFrame");
				?>
				</td>
			</tr>
		</table>
	</body>

</html>
