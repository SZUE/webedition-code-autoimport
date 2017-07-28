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

$searchButton = we_html_button::create_button(we_html_button::SEARCH, getScriptName() . '?section=upgrade&update_cmd=upgrade&detail=lookForUpgrade');

$content = '
<table class="defaultfont" style="width:100%">
<tr>
	<td>' . g_l('liveUpdate', '[upgrade][actualVersion]') . '</td>
	<td>' . $GLOBALS['LU_Variables']['clientVersion'] . '</td>
</tr>
<tr>
	<td>
		<br />
		<br />
	</td>
</tr>
<tr>
	<td>' . g_l('liveUpdate', '[upgrade][lookForUpdate]') . '</td>
	<td>' . $searchButton . '</td>
</tr>
</table>';

echo liveUpdateTemplates::getHtml(g_l('liveUpdate', '[upgrade][headline]'), $content);
