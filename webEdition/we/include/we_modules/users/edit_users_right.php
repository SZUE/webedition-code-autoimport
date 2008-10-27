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
  include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/" . $GLOBALS["WE_LANGUAGE"] . "/modules/users.inc.php");
  
  htmlTop();
?>
	</head>

<?php if ($GLOBALS["BROWSER"] == "NN6")	{ ?>
	<frameset cols="*" framespacing="0" border="0" frameborder="NO">
        <frame src="<?php print WE_USERS_MODULE_PATH; ?>edit_users_editor.php" scrolling="no" noresize name="user_editor">
	</frameset>
<?php } else if($GLOBALS["BROWSER"] == "SAFARI") { ?>
	<frameset cols="1,*" framespacing="0" border="0" frameborder="NO">
        <frame src="<?php print HTML_DIR; ?>safariResize.html" name="user_separator" noresize scrolling="no">
        <frame src="<?php print WE_USERS_MODULE_PATH; ?>edit_users_editor.php" noresize name="user_editor" scrolling="no">
	</frameset>
<?php } else { ?>
	<frameset cols="2,*" framespacing="0" border="0" frameborder="NO">
        <frame src="<?php print HTML_DIR; ?>ieResize.html" name="user_separator" noresize scrolling="no">
        <frame src="<?php print WE_USERS_MODULE_PATH; ?>edit_users_editor.php" noresize name="user_editor" scrolling="no">
	</frameset>
<?php } ?>
	<noframes>
    <body bgcolor="#ffffff">
		<p></p>
	</body>
	</noframes>
</html>

