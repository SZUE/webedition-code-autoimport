<?php
/**
 * webEdition CMS
 *
 * $Rev: 3084 $
 * $Author: mokraemer $
 * $Date: 2011-07-27 21:57:15 +0200 (Mi, 27. Jul 2011) $
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

function we_parse_tag_Xcustomer($attribs, $content) {
	eval('$arr = ' . $attribs . ';');
	$name = we_getTagAttributeTagParser("name", $arr);
	if ($name && strpos($name, ' ') !== false) {
		return parseError(sprintf(g_l('parser','[name_with_space]'), 'customer'));
	}
	
	return '<?php echo we_tag(\'customer\',$attribs);?>';
}

//FIXME: customer tag
function we_tag_customer($attribs,$content){
	if (!defined("WE_CUSTOMER_MODULE_DIR")) {
		return modulFehltError('Customer','customer');
	}
	//TODO!
}