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
function we_tag_unsubscribe($attribs){

	$attribs['type'] = 'text';
	$attribs['name'] = 'we_unsubscribe_email__';

	if(isset($_REQUEST["we_unsubscribe_email__"])){
		$attribs['value'] = oldHtmlspecialchars($_REQUEST["we_unsubscribe_email__"]);
	} else{
		$attribs['value'] = "";
	}

	return getHtmlTag('input', $attribs);
}
