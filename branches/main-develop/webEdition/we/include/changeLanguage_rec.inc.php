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

include_once ($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we.inc.php");

protect();
$ok = false;

if ($_SESSION["perms"]["ADMINISTRATOR"]) {
	$we_transaction = (preg_match('|^([a-f0-9]){32}$|i',$_REQUEST['we_cmd'][1])?$_REQUEST['we_cmd'][1]:0);
	// init document
	$we_dt = $_SESSION["we_data"][$we_transaction];

	include ($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_editors/we_init_doc.inc.php");

	$ok = $we_doc->changeLanguageRecursive();

}

we_html_tools::htmlTop();

	if ($ok) {
		print we_htmlElement::jsElement(we_message_reporting::getShowMessageCall(g_l('weClass',"[grant_language_ok]"), WE_MESSAGE_NOTICE));
	} else {
		print we_htmlElement::jsElement(we_message_reporting::getShowMessageCall(g_l('weClass',"[grant_language_notok]"), WE_MESSAGE_ERROR));
	}
	?>
</head>

<body>
</body>

</html>