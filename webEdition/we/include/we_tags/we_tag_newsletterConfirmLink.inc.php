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
function we_tag_newsletterConfirmLink($attribs, $content){

	$plain = weTag_getAttribute("plain", $attribs, false, true);

	$content = trim($content);
	$link = isset($GLOBALS["WE_CONFIRMLINK"]) ? $GLOBALS["WE_CONFIRMLINK"] : '';

	if($link){
		$attribs["href"] = $link;
		return ($plain ?
				$link :
				getHtmlTag('a', $attribs, $content ? : $link)
			);
	}
	return '';
}
