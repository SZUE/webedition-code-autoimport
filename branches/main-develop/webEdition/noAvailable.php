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
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_classes/html/we_button.inc.php");

htmlTop();

print STYLESHEET;

$content='<table cellpadding="0" cellspacing="0" border="0">
        <tr><td class="defaultfont">'.$g_l('help','[help_not_available_text]').'</td></tr>
        <tr><td class="defaultfont">'.$g_l('help','[help_not_available_again]').'</td></tr>
    </table>';

?>
</head>
<body class="weDialogBody">
 <?php print htmlDialogLayout($content,$g_l('help','[help_not_available_title]'),we_button::create_button("close", "javascript:self.close();")); ?>
</body>
</html>
