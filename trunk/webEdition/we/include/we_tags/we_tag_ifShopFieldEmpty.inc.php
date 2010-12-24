<?php
/**
 * webEdition CMS
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

function we_tag_ifShopFieldEmpty($attribs,$content) {
	$foo = attributFehltError($attribs, "name", "ifShopFieldEmpty");if($foo) return $foo;
	$foo = attributFehltError($attribs, "reference", "ifShopFieldEmpty");if($foo) return $foo;
	$foo = attributFehltError($attribs, "shopname", "ifShopFieldEmpty");if($foo) return $foo;

	$name      = we_getTagAttribute("name", $attribs);
	$reference = we_getTagAttribute("reference", $attribs);
	$shopname  = we_getTagAttribute("shopname", $attribs);
	$attribs['type']='print';//Bug #4895

	return (we_tag('shopField',$attribs, "") == '');
}
