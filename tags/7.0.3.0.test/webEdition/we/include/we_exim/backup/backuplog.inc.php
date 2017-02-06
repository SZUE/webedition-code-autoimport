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
	$parts = array(
		array(
			'headline' => g_l('backup', '[view_log]'),
			'html' => '',
			'space' => we_html_multiIconBox::SPACE_SMALL
		),
		array(
			'headline' => '',
			'html' => (file_exists(BACKUP_PATH . we_backup_util::logFile) ?
				'<pre>' . file_get_contents(BACKUP_PATH . we_backup_util::logFile) . '</pre>' :
				'<p>' . g_l('backup', '[view_log_not_found]') . '</p>'),
			'space' => we_html_multiIconBox::SPACE_SMALL
		)
	);
} else {
	$parts = array(
		array(
			'headline' => '',
			'html' => '<p>' . g_l('backup', '[view_log_no_perm]') . '</p>',
			'space' => we_html_multiIconBox::SPACE_SMALL
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
<body class="weDialogBody" onload="self.focus();">
	<div id="info"><?php
		$buttons = we_html_button::formatButtons(we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close()"));

		echo we_html_multiIconBox::getJS() .
		we_html_multiIconBox::getHTML('', $parts, 30, $buttons);
		?>
	</div>

</body>
</html>