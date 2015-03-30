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
echo we_html_tools::getHtmlTop('command-bridge', '', 5);
?>
<script type="text/javascript"><!--
	// bugfix WE-356
	self.focus();
	var g_l = {
		"nothing_to_save": "<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[nothing_to_save]')); ?>",
		"nothing_to_publish": "<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[nothing_to_publish]')); ?>",
	};
	var tables = {
		"FILE_TABLE": "<?php echo defined('FILE_TABLE') ? FILE_TABLE : 'f'; ?> ",
		"TEMPLATES_TABLE": "<?php echo defined('TEMPLATES_TABLE') ? TEMPLATES_TABLE : 't'; ?> ",
		"VFILE_TABLE": "<?php echo defined('VFILE_TABLE') ? VFILE_TABLE : 'v'; ?> ",
		"OBJECT_FILES_TABLE": "<?php echo defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'o'; ?> "
	};
	var contentTypes = {
		'TEMPLATE': '<?php echo we_base_ContentTypes::TEMPLATE; ?>',
		'WEDOCUMENT': '<?php echo we_base_ContentTypes::WEDOCUMENT; ?>',
		'OBJECT_FILE': '<?php echo we_base_ContentTypes::OBJECT_FILE; ?>',
		'IMAGE': '<?php echo we_base_ContentTypes::IMAGE; ?>',
		'HTML': '<?php echo we_base_ContentTypes::HTML; ?>',
		'FLASH': '<?php echo we_base_ContentTypes::FLASH; ?>',
		'QUICKTIME': '<?php echo we_base_ContentTypes::QUICKTIME; ?>',
		'VIDEO': '<?php echo we_base_ContentTypes::VIDEO; ?>',
		'AUDIO': '<?php echo we_base_ContentTypes::AUDIO; ?>',
		'JS': '<?php echo we_base_ContentTypes::JS; ?>',
		'TEXT': '<?php echo we_base_ContentTypes::TEXT; ?>',
		'XML': '<?php echo we_base_ContentTypes::XML; ?>',
		'HTACESS': '<?php echo we_base_ContentTypes::HTACESS; ?>',
		'CSS': '<?php echo we_base_ContentTypes::CSS; ?>',
		'APPLICATION': '<?php echo we_base_ContentTypes::APPLICATION; ?>',
		'COLLECTION': '<?php echo we_base_ContentTypes::COLLECTION; ?>'
	};
	var openTable = '<?php echo (isset($_SESSION['weS']['seemForOpenDelSelector']['Table']) ? $_SESSION['weS']['seemForOpenDelSelector']['Table'] : FILE_TABLE); ?>';

	//-->
</script><?php
if(($cmd0 = we_base_request::_(we_base_request::STRING, 'wecmd0')) !== false){ // when calling from applet (we can not call directly we_cmd[0] with the applet =>  Safari OSX doesn't support live connect)
	$_REQUEST['we_cmd'][0] = $cmd0;
}
$reqArr = we_base_request::_(we_base_request::STRING, 'we_cmd');

echo we_html_element::jsScript(JS_DIR . 'we_lcmd.js') .
 we_html_element::jsElement('
we_lcmd("' . implode('","', $reqArr) . '");');
?>
</head><body></body></html>