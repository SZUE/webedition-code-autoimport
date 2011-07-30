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
function we_parse_tag_repeatShopItem($attribs, $content) {
	eval('$attribs = ' . $attribs . ';');
	if (($foo = attributFehltError($attribs, "shopname", "repeatShopItem"))) {
		return $foo;
	}

	$attribs['_type'] = 'start';
	return '<?php we_tag(\'repeatShopItem\',' . we_tagParserPrintArray($attribs) . '); while($GLOBALS[\'lv\']->next_record()) {' . $content . '} we_tag(\'repeatShopItem\',array(\'_type\'=>\'stop\'));?>';
}

function we_tag_repeatShopItem($attribs, $content) {
	if (!defined("SHOP_TABLE")) {
		print modulFehltError('Shop', '"repeatShopitem"');
		return;
	}
	$shopname = we_getTagAttribute("shopname", $attribs);

	//internal Attribute
	$_type = we_getTagAttribute('_type', $attribs);
	switch ($_type) {
		case 'start':
			if (($foo = attributFehltError($attribs, "shopname", "repeatShopItem"))) {
				print $foo;
				return;
			}
			include_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_modules/shop/we_conf_shop.inc.php");
			$_SESSION["we_shopname"] = "' . $shopname . '";

			if (!isset($GLOBALS[$shopname]) || empty($GLOBALS[$shopname])) {
				echo parseError(sprintf(g_l('parser', '[missing_createShop]'), 'repeatShopItem'));
			}
			$GLOBALS["lv"] = new shop($GLOBALS[$shopname]);
			break;
		case 'stop':
			unset($GLOBALS["lv"]);
			break;
	}
}
