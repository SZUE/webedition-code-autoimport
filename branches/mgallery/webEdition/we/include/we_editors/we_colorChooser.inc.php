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
echo we_html_tools::getHtmlTop(g_l('global', '[select_color]')) .
 STYLESHEET .
 we_html_element::jsScript(JS_DIR . 'we_colors2.js');
$isA = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 0);
?>
<script><!--

	function setColor() {
<?php if($isA){ ?>
			opener.document.we_form.elements["<?php echo we_base_request::_(we_base_request::RAW, 'we_cmd', 0, 1); ?>"].value = document.we_form.colorvalue.value;

	<?php
	if(($js = we_base_request::_(we_base_request::JS, 'we_cmd', '', 3))){
		echo $js;
	} else {
		?>
				opener._EditorFrame.setEditorIsHot(true);
				opener.we_cmd("reload_editpage");

		<?php
	}
} else {
	?>
			window.returnValue = document.we_form.colorvalue.value;
<?php } ?>
		window.close();
	}
	//-->
</script>
</head>

<body class="weDialogBody"<?php echo 'onload="init(' . ($isA ? '"' . we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2) . '"' : 'window.dialogArguments["bgcolor"]') . ')"'; ?>>
	<form name="we_form" action="" onsubmit="<?php if(!$isA){ ?>setColor();<?php } ?>return
			false;">
					<?php
					$buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, ($isA ? "javascript:setColor();" : "form:submit:we_form")), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:window.close()"));
					echo we_html_tools::htmlDialogLayout('<table class="default">
	<tr>
		<td><table class="colorTable" class="default">
<script><!--
var z=0;
for ( col in we_color2 ){
	if(z == 0){
		document.writeln(\'<tr>\');
	}

document.writeln(\'<td style="background-color:\'+col+\'" onclick="selectColor(\\\'\'+col+\'\\\');" title="\'+we_color2[col]+\'" >&nbsp;</td>\');

if(z==17){
		document.writeln(\'</tr>\');
		z = 0;
	}else{
		z++;
	}
}
if(z != 0){
	for(var i=z;i<18;i++){
		document.writeln(\'<td></td>\');
	}
	document.writeln(\'</tr>\');
}
//->
</script>
		</table></td>
	</tr>
	<tr><td style="padding-top:10px;">' . we_html_tools::htmlFormElementTable('<input type="text" size="20" name="colorvalue" class="defaultfont" style="width:150px" />', g_l('wysiwyg', '[color]')) . '</td></tr>
</table>
', g_l('global', '[select_color]'), $buttons);
					?>
	</form>
</body>

</html>