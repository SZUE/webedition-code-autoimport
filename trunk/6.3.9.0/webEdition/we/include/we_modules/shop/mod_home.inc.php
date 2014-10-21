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

/// config
$feldnamen = explode('|', f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . " WHERE strDateiname='shop_pref'"));
for($i = 0; $i <= 3; $i++){
	$feldnamen[$i] = isset($feldnamen[$i]) ? $feldnamen[$i] : '';
}
$fe = explode(',', $feldnamen[3]);
$classid = $fe[0];

$resultO = array_shift($fe);

$resultD = f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE Name="' . WE_SHOP_TITLE_FIELD_NAME . '" LIMIT 1');


$prefshop = we_html_button::create_button("pref_shop", "javascript:top.opener.top.we_cmd('pref_shop');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER"));

$prefshop1 = we_html_button::create_button("payment_val", "javascript:top.opener.top.we_cmd('payment_val');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER"));
if(($resultD) && $resultO){ //docs and objects
	$prefshop2 = we_html_button::create_button("quick_rev", "javascript:top.content.editor.location='" . WE_MODULES_DIR . "shop/edit_shop_frameset.php?pnt=editor&top=1&typ=document '", true);
} elseif((!$resultD) && $resultO){ // no docs but objects
	$prefshop2 = we_html_button::create_button("quick_rev", "javascript:top.content.editor.location='" . WE_MODULES_DIR . "shop/edit_shop_frameset.php?pnt=editor&top=1&typ=object&ViewClass=$classid '", true);
} elseif(($resultD) && !$resultO){ // docs but no objects
	$prefshop2 = we_html_button::create_button("quick_rev", "javascript:top.content.editor.location='" . WE_MODULES_DIR . "shop/edit_shop_frameset.php?pnt=editor&top=1&typ=document '", true);
}

$content = $prefshop . we_html_tools::getPixel(2, 14) .
	$prefshop1 . we_html_tools::getPixel(2, 14) .
	(isset($prefshop2) ? $prefshop2 . we_html_tools::getPixel(2, 14) : '');


$modimage = "shop.gif";
