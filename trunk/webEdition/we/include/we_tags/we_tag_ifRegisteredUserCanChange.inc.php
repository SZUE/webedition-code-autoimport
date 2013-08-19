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
function we_tag_ifRegisteredUserCanChange($attribs){
	$admin = weTag_getAttribute('admin', $attribs);
	$userid = weTag_getAttribute('userid', $attribs); // deprecated  use protected=true instead
	$protected = weTag_getAttribute('protected', $attribs, false, true);
	if(!(isset($_SESSION['webuser']) && isset($_SESSION['webuser']['ID']))){
		return false;
	}
	if($admin && (isset($_SESSION['webuser'][$admin]) && $_SESSION['webuser'][$admin])){
		return true;
	}

	if(isset($GLOBALS['lv'])){
		return ($protected ? $GLOBALS['lv']->f('wedoc_WebUserID') : $GLOBALS['lv']->f($userid)) == $_SESSION['webuser']['ID'];
	} else{
		return ($protected ? $GLOBALS['we_doc']->WebUserID : $GLOBALS['we_doc']->getElement($userid)) == $_SESSION['webuser']['ID'];
	}
}
