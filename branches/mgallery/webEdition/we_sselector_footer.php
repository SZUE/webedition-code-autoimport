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

echo we_html_tools::getHtmlTop() .
 STYLESHEET .
 we_html_element::jsScript(JS_DIR . 'windows.js') .
 we_html_element::jsElement('
var g_l={
	"edit_file_nok":"' . we_message_reporting::prepareMsgForJS(g_l('fileselector', '[edit_file_nok]')) . '",
	"edit_file_is_folder":"' . we_message_reporting::prepareMsgForJS(g_l('fileselector', '[edit_file_is_folder]')) . '"
};
') . we_html_element::jsScript(JS_DIR . 'selectors/we_sselector_footer.js') .
 we_html_element::cssLink(CSS_DIR . 'selectors.css');
?>

</head>
<body class="selectorFooter" onunload="doUnload();">
	<form name="we_form" target="fscmd">
		<table id="footer">
			<?php
			if(we_base_request::_(we_base_request::BOOL, "ret")){
				$cancel_button = we_html_button::create_button("cancel", "javascript:top.close();");
				$yes_button = we_html_button::create_button("ok", "javascript:top.exit_close();");
			} else {
				$cancel_button = we_html_button::create_button("close", "javascript:top.exit_close();");
				$yes_button = we_html_button::create_button("edit", "javascript:editFile();");
			}
			if(we_base_request::_(we_base_request::STRING, "filter") === "all_Types"){
				?>
				<tr>
					<td class="defaultfont description"><?php echo g_l('fileselector', '[type]'); ?></td>
					<td class="defaultfont">
						<select name="filter" class="weSelect" size="1" onchange="top.fscmd.setFilter(document.we_form.elements.filter.options[document.we_form.elements.filter.selectedIndex].value)" style="width:100%">
							<option value="<?php echo str_replace(' ', '%20', g_l('contentTypes', '[all_Types]')); ?>"><?php echo g_l('contentTypes', '[all_Types]'); ?></option>
							<?php
							$ct = we_base_ContentTypes::inst();
							foreach($ct->getFiles() as $key){
								echo '<option value="' . rawurlencode(g_l('contentTypes', '[' . $key . ']')) . '">' . g_l('contentTypes', '[' . $key . ']') . '</option>';
							}
							?>
						</select></td>
				</tr>
			<?php } ?>
			<tr>
				<td class="defaultfont description"><?php echo g_l('fileselector', '[name]');?></td>
				<td class="defaultfont" align="left"><?php echo we_html_tools::htmlTextInput("fname", 24, we_base_request::_(we_base_request::FILE, "currentName"), "", "style=\"width:100%\" readonly=\"readonly\""); ?>				</td>
			</tr>
		</table>
		<div id="footerButtons"><?php echo we_html_button::position_yes_no_cancel($yes_button, null, $cancel_button); ?></div>
	</form>
</body>
</html>
