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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
$protect = we_base_moduleInfo::isActive(we_base_moduleInfo::SHOP) && we_users_util::canEditModule(we_base_moduleInfo::SHOP) ? null : array(false);
we_html_tools::protect($protect);

echo we_html_tools::getHtmlTop() .
 STYLESHEET;

$Kundenname = '';

if(($cid = we_base_request::_(we_base_request::INT, 'cid'))){
	$Kundenname = f('SELECT CONCAT(Forename," ",Surname) AS Name FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . $cid);
	$orderList = we_shop_functions::getCustomersOrderList($cid);
}else{
	$Kundenname = $orderList='';
}
?>
</head>
<body class="weEditorBody">
	<?= we_html_tools::htmlDialogLayout($orderList, g_l('modules_shop', '[order_liste]') . "&nbsp;" . $Kundenname); ?>
</body></html>
