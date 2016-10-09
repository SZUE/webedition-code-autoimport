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
 * This is the template for tab updatelog. It does not connect to the
 * updateserver
 *
 * This template need the following variables -> set in liveUpdateFunctions
 *
 * allEntries -> amount of all entries, not regarding the filter
 * amountEntries -> amount of all entries regarding the filter
 * amountPerPage -> amount of entries to be shwon on page
 * logEntries -> array of entries for this page
 * amountMessages -> amount of entries of type message
 * amountNotices -> amount of entries of type notice
 * amountErrors -> amount of entries of type notice
 *
 * and in addition the following request variables
 *
 * 'start'
 * 'messages'
 * 'notices'
 * 'errors'
 *
 */

$content = g_l('liveUpdate', '[updatelog][logIsEmpty]');

/*
 * items found - show them in table
 */
$buttons = '';

if($this->Data['allEntries']){ // entries exist
	// there are entries available -> show them in table
	$start = $this->Data['start'];
	$content = '
<form name="we_form">' . we_html_element::htmlHiddens([
			"section" => we_base_request::_(we_base_request::STRING, 'section'),
			"log_cmd" => "dummy",
			"start" => $start
		]) . '
<table class="defaultfont" style="width:100%">
<tr>
	<td>' . g_l('liveUpdate', '[updatelog][entriesTotal]') . ': ' . $this->Data['amountEntries'] . '</td>
	<td class="alignRight">' . g_l('liveUpdate', '[updatelog][page]') . ' ' . (($start / $this->Data['amountPerPage']) + 1) . '/ ' . ((ceil($this->Data['amountEntries'] / $this->Data['amountPerPage'])) ? ceil($this->Data['amountEntries'] / $this->Data['amountPerPage']) : 1) . '</td>
</tr>
</table>
<div class="defaultfont">' .
		we_html_forms::checkbox(1, we_base_request::_(we_base_request::BOOL, 'messages'), 'messages', "<span class=\"logMessage\">" . g_l('liveUpdate', '[updatelog][legendMessages]') . " (" . $this->Data['amountMessages'] . ")</span>", false, "small", "document.we_form.submit();") .
		we_html_forms::checkbox(1, we_base_request::_(we_base_request::BOOL, 'notices'), 'notices', "<span class=\"logNotice\">" . g_l('liveUpdate', '[updatelog][legendNotices]') . " (" . $this->Data['amountNotices'] . ")</span>", false, "small", "document.we_form.submit();") .
		we_html_forms::checkbox(1, we_base_request::_(we_base_request::BOOL, 'errors'), 'errors', "<span class=\"logError\">" . g_l('liveUpdate', '[updatelog][legendErrors]') . " (" . $this->Data['amountErrors'] . ")</span>", false, "small", "document.we_form.submit();") . '
</div>
<br />';

	if(($this->Data['logEntries'])){ // entries match filter
		$content .= '
<table style="width:100%" class="defaultfont updateContent" id="updateLogTable">
<tr>
	<th>' . g_l('liveUpdate', '[updatelog][date]') . '</th>
	<th>' . g_l('liveUpdate', '[updatelog][action]') . '</th>
	<th>' . g_l('liveUpdate', '[updatelog][version]') . '</th>
</tr>';

		foreach($this->Data['logEntries'] as $logEntry){
			switch($logEntry['state']){
				case 1:
					$classStr = ' class="logError"';
					break;
				case 2:
					$classStr = ' class="logNotice"';
					break;
				default:
					$classStr = ' class="logMessage"';
			}

			$content .= '<tr' . $classStr . '>
		<td style="vertical-align:top">' . $logEntry['date'] . '</td>
		<td>' . $logEntry['action'] . '</td>
		<td style="vertical-align:top">' . $logEntry['version'] . '</td>
	</tr>';
		}

		/*
		 * Add buttons for next, back and delete
		 */

		$buttons = we_html_button::create_button(we_html_button::DELETE, "javascript:confirmDelete();") .
			($start > 0 ? //	backbutton
				we_html_button::create_button(we_html_button::BACK, "javascript:lastEntries();") :
				we_html_button::create_button(we_html_button::BACK, "#", '', 0, 0, "", "", true)) .
			($this->Data['amountEntries'] <= $start + $this->Data['amountPerPage'] ? //	next_button
				we_html_button::create_button(we_html_button::NEXT, "#", '', 0, 0, "", "", true) :
				we_html_button::create_button(we_html_button::NEXT, "javascript:nextEntries();"));

		$content .= '
</table>';
	} else {
		$content .=g_l('liveUpdate', '[updatelog][noEntriesMatchFilter]');
	}
	$content .= '
</form>';
}

echo liveUpdateTemplates::getHtml(g_l('liveUpdate', '[updatelog][headline]'), $content, we_html_element::jsScript(JS_DIR . 'update/updatelog.js') .
	we_html_element::jsElement('
function confirmDelete() {
	if (confirm("' . g_l('liveUpdate', '[updatelog][confirmDelete]') . '")) {
		deleteEntries();
	}
}
'), $buttons);
