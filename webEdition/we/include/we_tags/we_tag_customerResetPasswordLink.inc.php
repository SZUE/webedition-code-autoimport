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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function we_tag_customerResetPasswordLink(array $attribs, $content){
	if(($foo = attributFehltError($attribs, 'id', __FUNCTION__))){
		return $foo;
	}


	if(isset($GLOBALS['ERROR']['customerResetPassword']) && $GLOBALS['ERROR']['customerResetPassword'] != we_customer_customer::PWD_ALL_OK || !isset($_SESSION['webuser'])){
		return '';
	}
	$id = weTag_getAttribute('id', $attribs);
	$host = weTag_getAttribute('host', $attribs, getServerUrl());

	$attribs["href"] = $host . we_tag('a', array('hrefonly' => true, 'id' => $id)) . '?user=' . $_SESSION['webuser']['ID'] . '&token=' . $_SESSION['webuser']['WE_token'];

	return (weTag_getAttribute("plain", $attribs, false, true) ?
			$attribs["href"] :
			getHtmlTag('a', removeAttribs($attribs, array('id', 'plain', 'host')), $content ? : $attribs["href"])
		);
}
