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
 * This is the template for tab connect. It trys to connect to the server in
 * different ways.
 */

$checkButton = we_html_button::create_button(we_html_button::NEXT, getScriptName() . '?section=connect&update_cmd=checkConnection');

if($errorMessage){ // servers response is error string
	$content = '
<div class="defaultfont">
	' . g_l('liveUpdate', '[connect][connectionSuccessError]') . '
	<div class="errorDiv">
		<code>' . $errorMessage . '</code>
	</div>
</div>
';
} else {
	$content = '
<div class="defaultfont">
	' . g_l('liveUpdate', '[connect][connectionSuccess]') . '
</div>
';
}

echo liveUpdateTemplates::getHtml(g_l('liveUpdate', '[connect][headline]'), $content);
