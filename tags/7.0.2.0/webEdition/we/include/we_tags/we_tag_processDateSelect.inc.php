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
function we_tag_processDateSelect(array $attribs){
	if(($foo = attributFehltError($attribs, "name", __FUNCTION__))){
		return $foo;
	}
	$name = weTag_getAttribute("name", $attribs, '', we_base_request::STRING);
	$endofday = weTag_getAttribute("endofday", $attribs, false, we_base_request::BOOL);
	$GLOBALS[$name] = $_REQUEST[$name] = mktime(
			$endofday ? 23 : 0, $endofday ? 59 : 0, $endofday ? 59 : 0, we_base_request::_(we_base_request::INT, $name . "_month", 0), we_base_request::_(we_base_request::INT, $name . "_day", 0), we_base_request::_(we_base_request::INT, $name . "_year", 0));
}
