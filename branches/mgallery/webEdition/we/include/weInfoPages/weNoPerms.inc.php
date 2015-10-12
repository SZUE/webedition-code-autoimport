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
echo we_html_tools::getHtmlTop(g_l('alert', '[no_perms_title]'), '', '', STYLESHEET .
	we_html_element::jsElement('
	top.toggleBusy(0);
	WE().layout.weEditorFrameController.getEditorFrame(window.name).setEditorIsLoading(false);'));
?>
<body class="weDialogBody"><?php
	echo we_html_tools::htmlDialogLayout('<p class="defaultfont">' . g_l('alert', '[no_perms_action]') . '</p>', g_l('alert', '[no_perms_title]'));
	?>
</body>
</html>