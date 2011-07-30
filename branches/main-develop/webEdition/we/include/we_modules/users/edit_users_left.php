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
?>
<script type="text/javascript" src="<?php print JS_DIR; ?>images.js"></script>
<?php print STYLESHEET; ?>
    </head>
    <frameset rows="1,*,40" framespacing="0" border="0" frameborder="NO">
    <frame src="<?php print HTML_DIR?>whiteWithTopLine.html" scrolling="no" noresize>
    <frame src="<?php print HTML_DIR?>white.html" name="user_tree" scrolling="auto" noresize>
    <frame src="<?php print WE_USERS_MODULE_PATH; ?>edit_users_search.php" name="user_search" scrolling="no" noresize>
   </frameset>
   <noframes>
   <body background="<?php print IMAGE_DIR ?>backgrounds/aquaBackground.gif" bgcolor="#bfbfbf" leftmargin=0 topmargin=0 marginheight=0 marginwidth=0>
   </body>
   </noframes>
</html>
