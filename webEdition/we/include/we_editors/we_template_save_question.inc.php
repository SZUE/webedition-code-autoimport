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
$we_cmd6 = we_base_request::_(we_base_request::JS, 'we_cmd', '', 6);

$alerttext = ($isTemplatesUsedByThisTemplate ?
		g_l('alert', '[template_save_warning2]') :
		sprintf((g_l('alert', ($nrDocsUsedByThisTemplate == 1) ? '[template_save_warning1]' : '[template_save_warning]')), $nrDocsUsedByThisTemplate)
	);

echo we_html_tools::getHtmlTop(g_l('global', '[question]')) .
 we_html_element::jsScript(JS_DIR . 'template_save_question.js', '', ['id' => 'loadVarTemplate_save_question', 'data-editorSave' => setDynamicVar([
		'we_transaction' => $we_transaction,
		'we_responseJS' => $GLOBALS['we_responseJS'],
		'we_cmd6' => $we_cmd6
	])]
);
?>
</head>
<body class="weEditorBody" onload="self.focus();" onblur="self.focus()">
	<?= we_html_tools::htmlYesNoCancelDialog($alerttext, '<span class="fa-stack fa-lg" style="color:#F2F200;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>', true, true, true, 'pressed_yes_button()', 'pressed_no_button()', 'pressed_cancel_button()'); ?>
</body>

</html>