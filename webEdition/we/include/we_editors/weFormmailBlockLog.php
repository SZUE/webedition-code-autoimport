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
if(permissionhandler::hasPerm("administrator")){

	if(we_base_request::_(we_base_request::BOOL, 'clearlog')){
		$GLOBALS['DB_WE']->query('TRUNCATE ' . FORMMAIL_BLOCK_TABLE);
	} else if(($id = we_base_request::_(we_base_request::BOOL, 'clearEntry'))){
		$GLOBALS['DB_WE']->query('DELETE FROM ' . FORMMAIL_BLOCK_TABLE . ' WHERE id=' . $id);
	}

	$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();");
	$refresh = we_html_button::create_button(we_html_button::REFRESH, "javascript:location.reload();");
	$deleteLogBut = we_html_button::create_button("clear_log", "javascript:clearLog()");


	$headline = array(
		array('dat' => we_html_element::htmlB(g_l('prefs', '[ip_address]'))),
		array('dat' => we_html_element::htmlB(g_l('prefs', '[blocked_until]'))),
		array('dat' => "")
	);


	$content = array();

	$count = 15;
	$start = max(we_base_request::_(we_base_request::INT, 'start', 0), 0);

	$num_all = f('SELECT COUNT(1) FROM ' . FORMMAIL_BLOCK_TABLE);

	$GLOBALS['DB_WE']->query('SELECT * FROM ' . FORMMAIL_BLOCK_TABLE . ' ORDER BY blockedUntil DESC LIMIT ' . abs($start) . ',' . abs($count));
	$num_rows = $GLOBALS['DB_WE']->num_rows();
	if($num_rows > 0){
		$ind = 0;
		while($GLOBALS['DB_WE']->next_record()){

			$content[$ind] = array();
			$content[$ind][0]['dat'] = $GLOBALS['DB_WE']->f("ip");
			$content[$ind][1]['dat'] = ($GLOBALS['DB_WE']->f("blockedUntil") == -1 ?
							oldHtmlspecialchars(g_l('prefs', '[forever]')) :
							date(g_l('weEditorInfo', '[date_format]'), $GLOBALS['DB_WE']->f("blockedUntil")));

			$content[$ind][2]['dat'] = '<a href="javascript:clearEntry(' . $GLOBALS['DB_WE']->f("id") . ',\'' . $GLOBALS['DB_WE']->f("ip") . '\')">' . g_l('prefs', '[unblock]') . '</a>';

			$ind++;
		}

		$next = $start + $count;

		$nextprev = '<table style="margin-top: 10px;" class="default"><tr><td style="padding-right:20px;">' .
				($start > 0 ?
						we_html_button::create_button(we_html_button::BACK, $_SERVER['SCRIPT_NAME'] . "?start=" . ($start - $count)) : //bt_back
						we_html_button::create_button(we_html_button::BACK, "", false, 100, 22, "", "", true)) .
				"</td><td style='text-align:center' class='defaultfont' width='120'><b>" . ($start + 1) . "&nbsp;-&nbsp;" .
				min($num_all, $start + $count) .
				"&nbsp;" . g_l('global', '[from]') . " " . ($num_all) . '</b></td><td style="padding-right:20px;">' .
				($next < $num_all ?
						we_html_button::create_button(we_html_button::NEXT, $_SERVER['SCRIPT_NAME'] . "?start=" . $next) : //bt_next
						we_html_button::create_button(we_html_button::NEXT, "", "", 100, 22, "", "", true)) .
				'</td></tr></table>';

		$parts = array(
			array(
				'headline' => '',
				'html' => we_html_tools::htmlDialogBorder3(730, 300, $content, $headline) . $nextprev,
				'space' => 0,
				'noline' => 1
			)
		);
	} else {
		$parts[] = array(
			'headline' => '',
			'html' => we_html_element::htmlSpan(array('class' => 'middlefontgray'), g_l('prefs', '[log_is_empty]')) .
			we_html_element::htmlBr() .
			we_html_element::htmlBr(),
			'space' => 0,
			'noline' => 1
		);
	}

	$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_multiIconBox::getHTML("show_log_data", $parts, 30, we_html_button::formatButtons($refresh . $close . $deleteLogBut), -1, '', '', false, g_l('prefs', '[formmail_log]'), "", 558) .
					we_html_element::jsElement("self.focus();")
	);


	$script = we_html_element::jsElement('
function clearLog() {
	if (confirm("' . addslashes(g_l('prefs', '[clear_log_question]')) . '")) {
		document.location="' . $_SERVER['SCRIPT_NAME'] . '?clearlog=1";
	}
}
function clearEntry(id,ip) {
	var txt = "' . addslashes(g_l('prefs', '[clear_block_entry_question]')) . '";


	if (confirm(txt.replace(/%s/,ip))) {
		document.location="' . $_SERVER['SCRIPT_NAME'] . '?clearEntry="+id;
	}
}');

	echo getHTMLDocument($body, $script);
}

function getHTMLDocument($body, $head = ""){
	return we_html_tools::getHtmlTop(g_l('prefs', '[formmail_log]'), '', '', STYLESHEET . $head, $body);
}
