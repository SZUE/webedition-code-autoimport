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
/*
 * This is the template for tab update. It contains the information screen
 * before searching for an update
 *
 */
$alsoBeta = (defined('WE_VERSION_SUPP') && WE_VERSION_SUPP != 'release' ? '&setTestUpdate=1' : '');

require_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we.inc.php");
$searchButton = we_html_button::create_button(we_html_button::SEARCH, getScriptName() . '?section=update&update_cmd=update&detail=lookForUpdate' . $alsoBeta);
$clientSubVersion = (isset($GLOBALS['LU_Variables']['clientSubVersion']) && $GLOBALS['LU_Variables']['clientSubVersion'] != '0000') ?
	', SVN-Revision: ' . $GLOBALS['LU_Variables']['clientSubVersion'] : '';

$clientVersionName = (!empty($GLOBALS['LU_Variables']['clientVersionName'])) ?
	$GLOBALS['LU_Variables']['clientVersionName'] : $GLOBALS['LU_Variables']['clientVersion'];


$content = '
<table class="defaultfont" style="width:100%">
<tr>
	<td>' . g_l('liveUpdate', '[update][actualVersion]') . '</td>
	<td>' . $clientVersionName . ' (' . $GLOBALS['LU_Variables']['clientVersion'] . $clientSubVersion . ')</td>
</tr>
<tr>
	<td>' . g_l('liveUpdate', '[update][lastUpdate]') . '</td>
	<td>' . $this->Data['lastUpdate'] . '</td>
</tr>
<tr><td colspan="2"><br /><br /></td></tr>
<tr>
	<td>' . g_l('liveUpdate', '[update][lookForUpdate]') . '</td>
	<td>' . $searchButton . '</td>
</tr>
</table>';

return liveUpdateTemplates::getHtml(g_l('liveUpdate', '[update][headline]'), $content);
