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
we_html_tools::protect();
echo we_html_tools::getHtmlTop(g_l('global', '[question]')) .
 STYLESHEET .
 we_html_element::jsScript(JS_DIR . 'keyListener.js');

$_we_cmd6 = we_base_request::_(we_base_request::RAW, 'we_cmd', '', 6);

$alerttext = ($isTemplatesUsedByThisTemplate ?
		g_l('alert', '[template_save_warning2]') :
		sprintf((g_l('alert', ($nrDocsUsedByThisTemplate == 1) ? '[template_save_warning1]' : '[template_save_warning]')), $nrDocsUsedByThisTemplate)
	);
?>
<script><!--

	// functions for keyBoard Listener
	function applyOnEnter() {
		pressed_yes_button();

	}

	// functions for keyBoard Listener
	function closeOnEscape() {
		pressed_cancel_button();

	}

	function pressed_yes_button() {
		opener.top.we_cmd('save_document', '<?php echo $we_transaction; ?>', 0, 1, 1, '<?php echo str_replace("'", "\\'", $GLOBALS['we_responseJS']); ?>', "<?php echo $_we_cmd6; ?>");
		opener.top.toggleBusy(1);
		self.close();

	}

	function pressed_no_button() {
		opener.top.we_cmd('save_document', '<?php echo $we_transaction; ?>', 0, 1, 0, '<?php echo str_replace("'", "\\'", $GLOBALS['we_responseJS']) ?>', "<?php echo $_we_cmd6; ?>");
		opener.top.toggleBusy(1);
		self.close();

	}

	function pressed_cancel_button() {
		self.close();
		opener.top.toggleBusy(0);

	}
//-->
</script>
</head>
<body class="weEditorBody" onload="self.focus();" onblur="self.focus()">
	<?php echo we_html_tools::htmlYesNoCancelDialog($alerttext, IMAGE_DIR . 'alert.gif', true, true, true, 'pressed_yes_button()', 'pressed_no_button()', 'pressed_cancel_button()'); ?>
</body>

</html>