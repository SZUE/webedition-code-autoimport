<?php
/**
 * webEdition CMS
 *
 * LICENSETEXT_CMS
 *
 *
 * @category   webEdition
 * @package    webEdition_base
 * @copyright  Copyright (c) 2008 living-e AG (http://www.living-e.com)
 * @license    http://www.living-e.de/licence     LICENSETEXT_CMS  TODO insert license type and url
 */


include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_html_tools.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/java_menu/weJavaMenu.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/java_menu/modules/module_menu_messaging.inc.php");
include_once( $_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/jsMessageConsole/messageConsole.inc.php" );

protect();

htmlTop();

print STYLESHEET;

$port = defined("HTTP_PORT") ? HTTP_PORT : "";
$protocol=getServerProtocol();
$jmenu = new weJavaMenu($we_menu_messaging, SERVER_NAME, 'top.opener.top.load', $protocol, $port,300);

?>
	<script language="JavaScript" type="text/javascript" src="<?php print JS_DIR; ?>images.js"></script>
	<script language="JavaScript" type="text/javascript">
        function menuaction(cmd){
				top.opener.top.load.location.replace("/webEdition/we_lcmd.php?we_cmd[0]="+cmd);
	    }
	</script>

	<body background="<?php print IMAGE_DIR ?>java_menu/background.gif" bgcolor="#bfbfbf" leftmargin=0 topmargin=0 marginheight=0 marginwidth=0>
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
