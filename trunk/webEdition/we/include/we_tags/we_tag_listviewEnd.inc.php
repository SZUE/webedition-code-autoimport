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

function we_tag_listviewEnd($attribs, $content){
	$cols= $GLOBALS['lv']->cols ? $GLOBALS['lv']->cols:1;
	return $GLOBALS["lv"]->rows ? min(
			($GLOBALS["lv"]->start - abs($GLOBALS["lv"]->offset)) + ($GLOBALS["lv"]->rows *$cols),
			($GLOBALS["lv"]->anz_all - abs($GLOBALS["lv"]->offset))) : ($GLOBALS["lv"]->anz_all - abs(
			$GLOBALS["lv"]->offset));
}
