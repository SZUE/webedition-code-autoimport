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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();

if(($id = we_base_request::_(we_base_request::INT, 'url'))){
	srand((double) microtime() * 1000000);
	$path = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE Published>0 AND ID=' . $id);
	if($path){
		$urlReplace = we_folder::getUrlReplacements($GLOBALS['DB_WE'], true);
		$loc = ($urlReplace ?
				(($http = preg_replace($urlReplace, array_keys($urlReplace), $path, -1, $cnt)) && $cnt ? '' : getServerUrl()) . $http :
				getServerUrl() . $path
			) . '?r=' . rand();
	} else {
		$loc = WEBEDITION_DIR . 'notPublished.php';
	}
} else {
	$loc = we_base_request::_(we_base_request::URL, 'url', '');
}
header('Location: ' . $loc);
echo we_html_tools::getHtmlTop('', '', '', '<meta http-equiv="refresh" content="1; url=' . $loc . '">', we_html_element::htmlBody());
