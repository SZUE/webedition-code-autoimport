<?php
/**
 * webEdition CMS
 *
 * $Rev: 2788 $
 * $Author: mokraemer $
 * $Date: 2011-04-21 02:20:09 +0200 (Do, 21. Apr 2011) $
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

function we_tag_conditionOr($attribs, $content){
	if (isset($GLOBALS["we_lv_conditionName"]) && isset($GLOBALS[$GLOBALS["we_lv_conditionName"]])) {
		$GLOBALS[$GLOBALS["we_lv_conditionName"]] .= " OR ";
	}
	return "";
}
