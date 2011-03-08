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

include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_html_tools.inc.php");
include_once(WE_SHOP_MODULE_DIR . 'shopFunctions.inc.php');

protect();

htmlTop();

print STYLESHEET;

$da = ( $GLOBALS["WE_LANGUAGE"] == "Deutsch" )?"%d.%m.%y":"%m/%d/%y";
if(isset($_REQUEST["cid"]) ){

	$foo = getHash("SELECT Forename,Surname FROM ".CUSTOMER_TABLE." WHERE ID='" . abs($_REQUEST["cid"]) . "'",$DB_WE);
	if (is_array($foo)){
		$Kundenname = $foo["Forename"]." ".$foo["Surname"];
	}
	$orderList = getCustomersOrderList($_REQUEST["cid"]);
}
?>
</head>
<body class="weEditorBody" onUnload="doUnload()">
<?php print  htmlDialogLayout($orderList,g_l('modules_shop','[order_liste]')."&nbsp;".$Kundenname);?>
</body></html>
