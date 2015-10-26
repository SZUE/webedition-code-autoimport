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

if(permissionhandler::hasPerm("BACKUPLOG")){
	$_parts = array(
		array(
			'headline' => g_l('backup', '[view_log]'),
			'html' => '',
			'space' => 10
		),
		array(
			'headline' => '',
			'html' => (file_exists(BACKUP_PATH . we_backup_backup::logFile) ?
				'<pre>' . file_get_contents(BACKUP_PATH . we_backup_backup::logFile) . '</pre>' :
				'<p>' . g_l('backup', '[view_log_not_found]') . '</p>'),
			'space' => 10
		)
	);
} else {
	$_parts = array(
		array(
			'headline' => '',
			'html' => '<p>' . g_l('backup', '[view_log_no_perm]') . '</p>',
			'space' => 10
		)
	);
}
echo we_html_tools::getHtmlTop(g_l('backup', '[view_log]')) .
 we_html_element::jsElement('
	function closeOnEscape() {
		return true;
	}
') .
 STYLESHEET;
?>
</head>
<body class="weDialogBody" style="overflow:hidden;" onload="self.focus();">
	<div id="info"><?php
		$buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close()"), '', '');

		echo we_html_multiIconBox::getJS() .
		we_html_multiIconBox::getHTML('', $_parts, 30, $buttons, -1, '', '', false, "", "", 0, "auto");
		?>
	</div>

</body>
</html>