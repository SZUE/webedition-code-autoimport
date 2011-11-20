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
  
  we_html_tools::htmlTop();
?>
</head>

<?php if ($GLOBALS["BROWSER"] == "NN6"){ ?>
	<frameset cols="170,*" border="1" id="resizeframeid">
		<frame src="<?php print WE_USERS_MODULE_PATH; ?>edit_users_left.php" name="user_left" scrolling="no">
		<frame src="<?php print WE_USERS_MODULE_PATH; ?>edit_users_right.php" name="user_right">
	</frameset>
<?php } else if($GLOBALS["BROWSER"] == "SAFARI") { ?>
	<frameset cols="170,*" framespacing="0" border="0" frameborder="0" id="resizeframeid">
		<frame src="<?php print WE_USERS_MODULE_PATH; ?>edit_users_left.php" name="user_left" scrolling="no">
		<frame src="<?php print WE_USERS_MODULE_PATH; ?>edit_users_right.php" name="user_right">
	</frameset>
<?php } else { //IE ?>
	<frameset cols="170,*" framespacing="0" border="0" frameborder="0" id="resizeframeid">
		<frame src="<?php print WE_USERS_MODULE_PATH; ?>edit_users_left.php" name="user_left" scrolling="no" frameborder="0">
		<frame src="<?php print WE_USERS_MODULE_PATH; ?>edit_users_right.php" name="user_right">
	</frameset>
<?php } ?>
<noframes>
 <body background="<?php print IMAGE_DIR ?>backgrounds/aquaBackground.gif" bgcolor="#bfbfbf" leftmargin=0 topmargin=0 marginheight=0 marginwidth=0>
 </body>
</noframes>
</html>
