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

$msg_cmd = "javascript:top.opener.we_cmd('messaging_start', " . we_messaging_frames::TYPE_MESSAGE . ");";
$todo_cmd = "javascript:top.opener.we_cmd('messaging_start', " . we_messaging_frames::TYPE_TODO . ");";

$text = '';
$msg = we_base_request::_(we_base_request::INT, 'msg', 0) - we_base_request::_(we_base_request::INT, 'omsg', 0);
$todo = we_base_request::_(we_base_request::INT, 'todo', 0) - we_base_request::_(we_base_request::INT, 'otodo', 0);

$text = ($msg > 0 ? sprintf(g_l('modules_messaging', '[newHeaderMsg]'), '<a href="' . $msg_cmd . '">' . $msg, '</a>') . '<br/>' : '') .
	($todo > 0 ? sprintf(g_l('modules_messaging', '[newHeaderTodo]'), '<a href="' . $todo_cmd . '">' . $todo, '</a>') . '<br/>' : '');
$parts = array(
	array(
		"headline" => we_html_tools::htmlAlertAttentionBox($text, we_html_tools::TYPE_INFO, 500, false),
		"html" => '',
		"space" => 10,
		"noline" => 1),
);

echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET, we_html_element::htmlBody(array('class' => 'weDialogBody'), we_html_element::htmlCenter(
			we_html_multiIconBox::getHTML("", "100%", $parts, 30, '<div style="width:100%;text-align:right;">'.we_html_button::create_button(we_html_button::OK, "javascript:self.close();").'</div>')
		)
	)
);
