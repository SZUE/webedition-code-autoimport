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
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */


include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_html_tools.inc.php");

htmlTop();

print STYLESHEET;

if (!eregi("^([a-f0-9]){32}$",$_REQUEST['we_transaction'])) {
	exit();
}

?>
  </head>

<?php if ($GLOBALS["BROWSER"] == "NN6")	{ ?>
<frameset cols="*" framespacing="0" border="0" frameborder="NO">
	<frame src="<?php print WE_MESSAGING_MODULE_PATH ?>messaging_work.php?we_transaction=<?php echo $_REQUEST['we_transaction']?>" name="msg_work" scrolling="no" noresize>
</frameset>
<?php } else if($GLOBALS["BROWSER"] == "SAFARI") { ?>
<frameset cols="1,*" framespacing="0" border="0" frameborder="NO">
	<frame src="<?php print HTML_DIR ?>safariResize.html" name="bm_resize" scrolling="no" noresize>
	<frame src="<?php print WE_MESSAGING_MODULE_PATH ?>messaging_work.php?we_transaction=<?php echo $_REQUEST['we_transaction']?>" name="msg_work" scrolling="no" noresize>
</frameset>
<?php } else { ?>
<frameset cols="2,*" framespacing="0" border="0" frameborder="NO">
	<frame src="<?php print HTML_DIR ?>ieResize.html" name="bm_resize" scrolling="no" noresize>
	<frame src="<?php print WE_MESSAGING_MODULE_PATH ?>messaging_work.php?we_transaction=<?php echo $_REQUEST['we_transaction']?>" name="msg_work" scrolling="no" noresize>
</frameset>
<?php } ?>

  <body>
  </body>
</body>
