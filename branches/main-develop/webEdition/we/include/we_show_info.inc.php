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
echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET .
	we_html_element::cssLink(CSS_DIR . 'loginScreen.css').
	we_html_element::cssLink(CSS_DIR . 'infoScreen.css'));
?>
<body id="infoScreen" onload="self.focus();">
	<?php
	echo include (WE_INCLUDES_PATH . 'we_editors/we_info.inc.php');
	?>
</body>
</html>
