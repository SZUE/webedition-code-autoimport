<?php

require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
require_once ($_SERVER['DOCUMENT_ROOT'] .'/webEdition/we/include/weTagWizard/classes/weTagData.class.php');
require_once ($_SERVER['DOCUMENT_ROOT'] .'/webEdition/we/include/weTagWizard/classes/weTagWizard.class.php');

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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
 
include_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we.inc.php');
protect();//s1

$xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
$xml .= "<tags>\n";
$allWeTags = weTagWizard::getExistingWeTags();
foreach($allWeTags as $tag){
	$GLOBALS['TagRefURLName'] = strtolower($tag);
	$tagData = weTagData::getTagData($tag);
	$xml .= "\t". '<tag needsEndtag="'.($tagData->needsEndTag()? "true" : "false").'" name="' . $tagData->getName() . '" />'."\n";
}
$xml .= "</tags>\n";

header('Content-Type: text/xml');
print $xml;
