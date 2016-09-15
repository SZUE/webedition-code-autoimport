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
$isA = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 0);
echo we_html_tools::getHtmlTop(g_l('global', '[select_color]'), '', '', we_html_element::jsScript(JS_DIR . 'we_colors2.js', '', ['id' => 'loadVarSelectorColor', 'data-selector' => setDynamicVar([
			'isA' => $isA,
			'cmd1' => we_base_request::_(we_base_request::RAW, 'we_cmd', 0, 1),
			'cmd3' => we_base_request::_(we_base_request::JS, 'we_cmd', '', 3),
	])])
);
?>
<body class="weDialogBody"<?= 'onload="init(' . ($isA ? '"' . we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2) . '"' : 'window.dialogArguments["bgcolor"]') . ')"'; ?>>
	<form name="we_form" action="" onsubmit="<?php if(!$isA){ ?>setColor();<?php } ?>return
			false;">
<?php
$buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, ($isA ? "javascript:setColor();" : we_html_button::WE_FORM . ":we_form")), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:window.close()"));
echo we_html_tools::htmlDialogLayout('<table class="default">
	<tr>
		<td id="colorTableCont"></td>
	</tr>
	<tr><td style="padding-top:10px;">' . we_html_tools::htmlFormElementTable('<input type="text" name="colorvalue" class="defaultfont" style="width:150px" />', g_l('wysiwyg', '[color]')) . '</td></tr>
</table>
', g_l('global', '[select_color]'), $buttons);
?>
	</form>
</body>

</html>