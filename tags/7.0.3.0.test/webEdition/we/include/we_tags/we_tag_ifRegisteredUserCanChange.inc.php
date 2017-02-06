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
function we_tag_ifRegisteredUserCanChange(array $attribs){
	$admin = weTag_getAttribute('admin', $attribs, '', we_base_request::STRING);
	$userid = weTag_getAttribute('userid', $attribs, '', we_base_request::STRING); // deprecated  use protected=true instead
	$protected = weTag_getAttribute('protected', $attribs, false, we_base_request::BOOL);
	if(!(isset($_SESSION['webuser']) && isset($_SESSION['webuser']['ID']) && !empty($_SESSION['webuser']['registered']))){
		return false;
	}
	if($admin && (!empty($_SESSION['webuser'][$admin]))){
		return true;
	}

	if(isset($GLOBALS['lv'])){
		return ($protected ? $GLOBALS['lv']->f('wedoc_WebUserID') : $GLOBALS['lv']->f($userid)) == $_SESSION['webuser']['ID'];
	}
	return ($protected ? $GLOBALS['we_doc']->WebUserID : $GLOBALS['we_doc']->getElement($userid)) == $_SESSION['webuser']['ID'];
}
