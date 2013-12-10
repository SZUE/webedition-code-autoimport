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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();
we_html_tools::htmlTop();

print STYLESHEET;

$Kundenname = '';

if(isset($_REQUEST["cid"])){
	$Kundenname = f('SELECT CONCAT(Forename," ",Surname) AS Name FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . intval($_REQUEST["cid"]), 'Name', $DB_WE);
	$orderList = we_shop_functions::getCustomersOrderList($_REQUEST["cid"]);
}
?>
</head>
<body class="weEditorBody" onunload="doUnload()">
	<?php print we_html_tools::htmlDialogLayout($orderList, g_l('modules_shop', '[order_liste]') . "&nbsp;" . $Kundenname); ?>
</body></html>
