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

function we_tag_processDateSelect($attribs, $content){
	$foo = attributFehltError($attribs, "name", "dateSelect");
	if ($foo)
		return $foo;
	$name = we_getTagAttribute("name", $attribs);
	$endofday = we_getTagAttribute("endofday", $attribs, "", true);
	$GLOBALS[$name] = $_REQUEST[$name] = mktime(
			$endofday ? 23 : 0,
			$endofday ? 59 : 0,
			$endofday ? 59 : 0,
			isset($_REQUEST[$name . "_month"]) ? $_REQUEST[$name . "_month"] : 0,
			isset($_REQUEST[$name . "_day"]) ? $_REQUEST[$name . "_day"] : 0,
			isset($_REQUEST[$name . "_year"]) ? $_REQUEST[$name . "_year"] : 0);
}
