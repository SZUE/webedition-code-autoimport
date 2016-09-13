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
function we_tag_customerResetPasswordLink(array $attribs, $content, $internal = false){
	if(!$internal && ($foo = attributFehltError($attribs, 'id', __FUNCTION__))){
		return $foo;
	}

	if(isset($GLOBALS['ERROR']['customerResetPassword']) && $GLOBALS['ERROR']['customerResetPassword'] !== we_customer_customer::PWD_ALL_OK || !isset($_SESSION['webuser'])){
		return '';
	}

	if($internal){
		$docPath = WEBEDITION_DIR . 'resetpwd.php';
		$cnt = 0;
	} else {
		$id = weTag_getAttribute('id', $attribs, 0, we_base_request::INT);
		$docPath = we_tag('a', ['hrefonly' => true, 'id' => $id]);
		$cnt = 0;
		//Fix #10071
		$urlReplace = we_folder::getUrlReplacements($GLOBALS['DB_WE'], true);
		$url = preg_replace($urlReplace, array_keys($urlReplace), $docPath, -1, $cnt);
	}
	$host = weTag_getAttribute('host', $attribs, getServerUrl(), we_base_request::URL);

	$attribs['href'] = $host . ($cnt ? $url : $docPath) . '?user=' . $_SESSION['webuser']['ID'] . '&token=' . $_SESSION['webuser']['WE_token'];

	return (weTag_getAttribute('plain', $attribs, false, we_base_request::BOOL) ?
			$attribs['href'] :
			getHtmlTag('a', removeAttribs($attribs, ['id', 'plain', 'host']), $content ? : $attribs['href'])
		);
}
