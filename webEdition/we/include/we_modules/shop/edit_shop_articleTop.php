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
$protect = we_base_moduleInfo::isActive('shop') && we_users_util::canEditModule('shop') ? null : array(false);
we_html_tools::protect($protect);

echo we_html_tools::getHtmlTop() . STYLESHEET;

$da = ( $GLOBALS["WE_LANGUAGE"] == "Deutsch" ) ? "%d.%m.%y" : "%m/%d/%y";
if(isset($_REQUEST["cid"])){
	$Kundenname = f('SELECT CONCAT(Forename," ",Surname) AS Name FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . intval($_REQUEST["cid"]));

	$Bestelldaten = '
<table border="0" cellpadding="2" cellspacing="6" width="300">
		<tr><td class="defaultfont" colspan="2"><b>' . g_l('modules_shop', '[bestellung]') . '</b></td>
		<td class="defaultfont"><b>' . g_l('modules_shop', '[datum]') . '</b></td>
		</tr>
		<tr><td colspan=3></tr>';

	$DB_WE->query("SELECT IntOrderID,DateShipping, DATE_FORMAT(DateOrder,'" . $da . "') as orddate, DATE_FORMAT(DateOrder,'%c%Y') as mdate FROM " . SHOP_TABLE . " WHERE IntCustomerID=" . intval($_REQUEST["cid"]) . " GROUP BY IntOrderID ORDER BY IntID DESC");
	while($DB_WE->next_record()){
		$Bestelldaten .= "<tr><td class='defaultfont'><a href='" . WE_SHOP_MODULE_DIR . "edit_shop_frameset.php?pnt=edbody&bid=" . $DB_WE->f("IntOrderID") . "' class=\"defaultfont\"><b>" . $DB_WE->f("IntOrderID") . ".</b></a></td>
			<td class='defaultgray'>" . g_l('modules_shop', '[bestellungvom]') . "</td>
			<td class='defaultfont'><a href='" . WE_SHOP_MODULE_DIR . "edit_shop_frameset.php?pnt=editor&bid=" . $DB_WE->f("IntOrderID") . "' class=\"defaultfont\" target=\"editor\"><b>" . $DB_WE->f("orddate") . "</b></a></td></tr>";
	}
	$Bestelldaten .= "</table>";
} else {
	$Bestelldaten = g_l('modules_shop', '[keinedaten]');
	$Kundenname = '';
}
?>
</head>
<body class="weEditorBody" onunload="doUnload()">
	<?php echo we_html_tools::htmlDialogLayout($Bestelldaten, g_l('modules_shop', '[order_liste]') . "&nbsp;" . $Kundenname); ?>
</body></html>