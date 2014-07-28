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
require_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we.inc.php");
we_html_tools::protect();
echo we_html_tools::getHtmlTop();
?>
</head>
<body>
	<?php
	// build url from REQUEST ...
	$paraStr = we_SEEM::arrayToParameters($_REQUEST, "", array("we_cmd", "original_action"));
	$action = $_REQUEST['original_action'] . "?1" . $paraStr;

	//	The following will translate a given URL to a we_cmd.
	//	When pressing a link in edit-mode this functionality
	//	is needed to reopen the document (if possible) with webEdition

	echo we_html_element::jsElement(we_SEEM::getJavaScriptCommandForOneLink("<a href=\"" . str_replace(" ", "+", $action) . "\">"));
	?>
</body>
</html>