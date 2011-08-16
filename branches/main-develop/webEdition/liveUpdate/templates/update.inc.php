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
 * @package    webEdition_update
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/*
 * This is the template for tab update. It contains the information screen
 * before searching for an update
 *
 */

include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_classes/html/we_button.inc.php");
$searchButton = we_button::create_button('search', $_SERVER['SCRIPT_NAME'] . '?section=update&update_cmd=update&detail=lookForUpdate');
if (isset($GLOBALS['LU_Variables']['clientSubVersion']) &&  $GLOBALS['LU_Variables']['clientSubVersion'] !='0000'){
	$clientSubVersion = ' (SVN-Revision: '.$GLOBALS['LU_Variables']['clientSubVersion'].')';
} else {
	$clientSubVersion = '';
}

$content = '
<table class="defaultfont" width="100%">
<tr>
	<td>' . $GLOBALS['l_liveUpdate']['update']['actualVersion'] . '</td>
	<td>' . $GLOBALS['LU_Variables']['clientVersion'] . $clientSubVersion . '</td>
</tr>
<tr>
	<td>' . $GLOBALS['l_liveUpdate']['update']['lastUpdate'] . '</td>
	<td>' . $this->Data['lastUpdate'] . '</td>
</tr>
<tr>
	<td>
		<br />
		<br />
	</td>
</tr>
<tr>
	<td>' . $GLOBALS['l_liveUpdate']['update']['lookForUpdate'] . '</td>
	<td>' . $searchButton . '</td>
</tr>
</table>
';

print liveUpdateTemplates::getHtml($GLOBALS['l_liveUpdate']['update']['headline'], $content);
