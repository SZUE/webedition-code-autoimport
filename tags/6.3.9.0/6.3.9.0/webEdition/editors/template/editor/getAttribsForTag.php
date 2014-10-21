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
//called by old javaeditor
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();
$tagName = we_base_request::_(we_base_request::STRING, 'tagName', '');

// Remove . / \ because of security reasons
$tagName = str_replace(array('.', '/', '\\'), '', $tagName);

$xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n<attributes>\n";

if($tagName){
	$tagData = weTagData::getTagData($tagName);
	foreach($tagData->getAllAttributes() as $attr){
		$xml .= "\t" . '<attribute name="' . $attr . '" />' . "\n";
	}
}

$xml .= "</attributes>\n";

header('Content-Type: text/xml');
echo $xml;
