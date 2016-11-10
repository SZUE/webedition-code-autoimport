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
 * This is the template for tab languages. It contains the information screen
 * before deleting or installing languages
 */
require_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we.inc.php");

$nextButton = we_html_button::create_button(we_html_button::NEXT, $_SERVER['SCRIPT_NAME'] . '?section=languages&update_cmd=languages&detail=selectLanguages');
$deleteButton = we_html_button::create_button(we_html_button::DELETE, 'javascript:document.we_form.submit()');

$languages = liveUpdateFunctions::getInstalledLanguages();

$languagesStr = '';
foreach($languages as $lng){

	if(WE_LANGUAGE == $lng){
		$lngBox = we_html_forms::checkbox($lng, false, 'deleteLanguages[]', "<i>$lng (" . g_l('liveUpdate', '[languages][systemLanguage]') . ")</i>", false, 'defaultfont', '', true);
	} else if($GLOBALS['WE_LANGUAGE'] == $lng){
		$lngBox = we_html_forms::checkbox($lng, false, 'deleteLanguages[]', "<i>$lng (" . g_l('liveUpdate', '[languages][usedLanguage]') . ")</i>", false, 'defaultfont', '', true);
	} else {
		$lngBox = we_html_forms::checkbox($lng, false, 'deleteLanguages[]', $lng, true);
	}
	$languagesStr .= '<div>' . $lngBox . '</div>';
}

$deletedLngs = $this->getData('deletedLngs');
$notDeletedLngs = $this->getData('notDeletedLngs');
$jsAlert = '';
if(!empty($deletedLngs)){
	$jsAlert .= g_l('liveUpdate', '[languages][languagesDeleted]') . '\n';
	foreach($deletedLngs as $lng){
		$jsAlert .= $lng . '\n';
	}
}
if(!empty($notDeletedLngs)){
	$jsAlert .= g_l('liveUpdate', '[languages][languagesNotDeleted]') . '\n';
	foreach($notDeletedLngs as $lng){
		$jsAlert .= $lng . '\n';
	}
}

echo liveUpdateTemplates::getHtml(g_l('liveUpdate', '[languages][headline]'), '
<div>
<form name="we_form">
' . we_html_element::htmlHidden('section', 'languages') . '
' . g_l('liveUpdate', '[languages][installedLngs]') . '
<br />
' . $languagesStr . '
<br />
<table class="defaultfont" style="width:100%">
<tr>
	<td>' . g_l('liveUpdate', '[languages][showLanguages]') . '</td>
	<td>' . $nextButton . '</td>
</tr>
<tr>
	<td><br /></td>
</tr>
<tr>
	<td>' . g_l('liveUpdate', '[languages][deleteSelectedLanguages]') . '</td>
	<td>' . $deleteButton . '</td>
</tr>
</table>
</form>
' . ($jsAlert ? we_message_reporting::jsMessagePush($jsAlert, we_message_reporting::WE_MESSAGE_FRONTEND) : ''));
