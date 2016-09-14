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
we_html_tools::protect(['administrator']);

if(we_base_request::_(we_base_request::BOOL, 'clearlog')){
	$GLOBALS['DB_WE']->query('DELETE FROM ' . FORMMAIL_LOG_TABLE);
}

$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();");
$refresh = we_html_button::create_button(we_html_button::REFRESH, "javascript:location.reload();");
$deleteLogBut = we_html_button::create_button('clear_log', "javascript:clearLog()");


$headline = [['dat' => we_html_element::htmlB("IP Adresse")],
	['dat' => we_html_element::htmlB("Datum/Uhrzeit")]
];

$content = [];

$count = 15;
$start = max(we_base_request::_(we_base_request::INT, 'start', 0), 0);

$nextprev = "";

$num_all = f('SELECT COUNT(1) FROM ' . FORMMAIL_LOG_TABLE);

$GLOBALS['DB_WE']->query('SELECT ip,DATE_FORMAT(unixTime,"' . g_l('weEditorInfo', '[mysql_date_format]') . '") AS unixTime FROM ' . FORMMAIL_LOG_TABLE . ' ORDER BY unixTime DESC LIMIT ' . abs($start) . ',' . abs($count));
$num_rows = $GLOBALS['DB_WE']->num_rows();
if($num_rows > 0){
	while($GLOBALS['DB_WE']->next_record()){
		$content[] = [['dat' => $GLOBALS['DB_WE']->f("ip")],
			['dat' => $GLOBALS['DB_WE']->f("unixTime")]
		];
	}

	$next = $start + $count;

	$nextprev = '<table style="margin-top: 10px;" class="default"><tr><td style="padding-right:20px;">' .
		($start > 0 ?
			we_html_button::create_button(we_html_button::BACK, WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=show_formmail_log&start=" . ($start - $count)) : //bt_back
			we_html_button::create_button(we_html_button::BACK, "", false, 100, 22, "", "", true)
		) .
		'</td><td class="defaultfont bold" style="width:120px;text-align:center;padding-right:20px;">' . ($start + 1) . "&nbsp;-&nbsp;" .
		min($num_all, $start + $count) .
		"&nbsp;" . g_l('global', '[from]') . " " . ($num_all) . '</td><td>' .
		($next < $num_all ?
			we_html_button::create_button(we_html_button::NEXT, WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=show_formmail_log&start=" . $next) : //bt_next
			we_html_button::create_button(we_html_button::NEXT, "", "", 100, 22, "", "", true)
		) .
		"</td></tr></table>";

	$parts = [['headline' => '',
		'html' => we_html_tools::htmlDialogBorder3(730, $content, $headline) . $nextprev,
		'noline' => 1
		]
	];
} else {
	$parts = [['headline' => '',
		'html' => we_html_element::htmlSpan(['class' => 'middlefont lowContrast'], g_l('prefs', '[log_is_empty]')) .
		we_html_element::htmlBr() .
		we_html_element::htmlBr(),
		'noline' => 1
		]
	];
}

echo we_html_tools::getHtmlTop(g_l('prefs', '[formmail_log]'), '', '', we_html_element::jsScript(JS_DIR . 'formmaillog.js'), we_html_element::htmlBody(['class' => "weDialogBody", 'onload' => 'self.focus();'], we_html_multiIconBox::getHTML("show_log_data", $parts, 30, we_html_button::formatButtons($refresh . $close . $deleteLogBut), -1, '', '', false, g_l('prefs', '[formmail_log]'))
));
