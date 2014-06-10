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
$ok = false;

if(permissionhandler::hasPerm('ADMINISTRATOR')){
	$we_transaction = weRequest('transaction', 'we_cmd', 0,1);
	// init document
	$we_dt = $_SESSION['weS']['we_data'][$we_transaction];

	include (WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

	$ok = $we_doc->changeLanguageRecursive();
}

echo we_html_tools::getHtmlTop() .
 ($ok ? we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('weClass', "[grant_language_ok]"), we_message_reporting::WE_MESSAGE_NOTICE)) :
		we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('weClass', "[grant_language_notok]"), we_message_reporting::WE_MESSAGE_ERROR)));
?>
</head>

<body>
</body>

</html>