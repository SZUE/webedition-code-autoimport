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
echo we_html_tools::getHtmlTop(g_l('modules_banner', '[bannercode]'));

$ok = we_base_request::_(we_base_request::BOOL, "ok");
$type = we_base_request::_(we_base_request::STRING, "type");
$tagname = we_base_request::_(we_base_request::STRING, "tagname");
$page = we_base_request::_(we_base_request::STRING, "page", "");
$target = we_base_request::_(we_base_request::STRING, "target", "");
$width = we_base_request::_(we_base_request::INT, "width", 468);
$height = we_base_request::_(we_base_request::INT, "height", 60);
$paths = we_base_request::_(we_base_request::STRING, "paths", "");
$getscript = we_base_request::_(we_base_request::URL, "getscript", WEBEDITION_DIR . "getBanner.php");
$clickscript = we_base_request::_(we_base_request::URL, "clickscript", WEBEDITION_DIR . "bannerclick.php");

if($ok){
//FIXME: replace by call of jsScript
	if($type === "js"){
		$code = we_html_element::jsElement('
var r = Math.random();
document.write ("<" + "script src=\"' . $getscript . '?r="+r+"&amp;bannername=' . rawurlencode($tagname) . '&amp;paths=' . rawurlencode($paths) . '&amp;type=js&amp;target=' . rawurlencode($target) . '&amp;bannerclick=' . rawurlencode($clickscript) . '&amp;height=' . rawurlencode($height) . '&amp;width=' . rawurlencode($width) . '&amp;page=' . rawurlencode($page) . '"+(document.referer ? ("&amp;referer="+encodeURI(document.referer)) : "")+"\"><" + "/script>");

') . '<noscript><a href="' . $clickscript . '?u=' . md5(uniqid('', true)) . '&amp;bannername=' . rawurlencode($tagname) . '&amp;page=' . rawurlencode($page) . '" target="' . $target . '"><img src="' . $getscript . '?bannername=' . rawurlencode($tagname) . '&amp;paths=' . rawurlencode($paths) . '&amp;page=' . rawurlencode($page) . '&amp;bannerclick=' . rawurlencode($clickscript) . '&amp;c=1" alt="" style="width:' . $width . 'px;height:' . $height . 'px;" /></a></noscript>';
	} else {
		$code = '<iframe
	src="' . $getscript . '?bannername=' . rawurlencode($tagname) . '&amp;type=iframe&amp;target=' . rawurlencode($target) . '&amp;bannerclick=' . rawurlencode($clickscript) . '&amp;width=' . rawurlencode($width) . '&amp;height=' . rawurlencode($height) . '&amp;page=' . rawurlencode($page) . '"
	width="' . $width . '"
	height="' . $height . '"
	vspace=0
	align=center
><ilayer
	src="' . $getscript . '?bannername=' . rawurlencode($tagname) . '&amp;type=iframe&amp;target=' . rawurlencode($target) . '&amp;bannerclick=' . rawurlencode($clickscript) . '&amp;width=' . rawurlencode($width) . '&amp;height=' . rawurlencode($height) . '&amp;page=' . rawurlencode($page) . '"
	width="' . $width . '"
	height="' . $height . '"
></ilayer><nolayer><a href="' . $clickscript . '?u=' . md5(uniqid('', true)) . '&amp;bannername=' . rawurlencode($tagname) . '&amp;page=' . rawurlencode($page) . '" target="' . $target . '"><img src="' . $getscript . '?bannername=' . rawurlencode($tagname) . '&amp;paths=' . rawurlencode($paths) . '&amp;page=' . rawurlencode($page) . '&amp;bannerclick=' . rawurlencode($clickscript) . '" alt="" style="width:' . $width . 'px;height:' . $height . 'px" /></a>
</nolayer>
</iframe>';
	}
}
echo we_html_element::jsScript(WE_JS_MODULES_DIR . 'banner/banner_code.js');
?>
	<style>
		td.right{
			padding-left:10px;
		}
	</style>
</head>
<body class="weDialogBody"<?php if($ok){ ?> onload="self.focus();
			document.we_form.code.focus();
			document.we_form.code.select();"<?php } ?>>
	<form onsubmit="return checkForm(this);" name="we_form" action="<?= $_SERVER["SCRIPT_NAME"]; ?>" method="get"><input type="hidden" name="ok" value="1" /><input type="hidden" name="we_cmd[0]" value="<?= we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0); ?>" />
		<?php
		$typeselect = '<select name="type">
<option' . (($type === "js") ? " selected" : "") . '>js</option>
<option' . (($type === "iframe") ? " selected" : "") . '>iframe</option>
</select>';

		$content = '<table class="default withSpace">
';
		if(!$ok){
			$content .= '	<tr><td class="defaultfont">' . g_l('modules_banner', '[type]') . '</td><td class="defaultfont right">' . $typeselect . '</td></tr>
	<tr><td class="defaultfont">' . g_l('modules_banner', '[tagname]') . '*</td><td class="defaultfont right">' . we_html_tools::htmlTextInput("tagname", 40, $tagname, "", "", "text", 300) . '</td>	</tr>
	<tr><td class="defaultfont">' . g_l('modules_banner', '[pageurl]') . '*</td><td class="defaultfont right">' . we_html_tools::htmlTextInput("page", 40, $page, "", "", "text", 300) . '</td>	</tr>
	<tr><td class="defaultfont">' . g_l('modules_banner', '[target]') . '</td><td class="defaultfont right">' . we_html_tools::htmlTextInput("target", 40, $target, "", "", "text", 300) . '</td>	</tr>
	<tr><td class="defaultfont">' . g_l('modules_banner', '[width]') . '*</td><td class="defaultfont right">' . we_html_tools::htmlTextInput("width", 40, $width, "", "", "text", 300) . '</td>	</tr>
	<tr><td class="defaultfont">' . g_l('modules_banner', '[height]') . '*</td><td class="defaultfont right">' . we_html_tools::htmlTextInput("height", 40, $height, "", "", "text", 300) . '</td></tr>
	<tr><td class="defaultfont">' . g_l('modules_banner', '[paths]') . '</td><td class="defaultfont right">' . we_html_tools::htmlTextInput("paths", 40, $paths, "", "", "text", 300) . '</td></tr>
	<tr><td class="defaultfont">' . g_l('modules_banner', '[getscript]') . '*</td><td class="defaultfont right">' . we_html_tools::htmlTextInput("getscript", 40, $getscript, "", "", "text", 300) . '</td></tr>
	<tr><td class="defaultfont">' . g_l('modules_banner', '[clickscript]') . '*</td><td class="defaultfont right">' . we_html_tools::htmlTextInput("clickscript", 40, $clickscript, "", "", "text", 300) . '</td>	</tr>
';
		}
		if($ok){
			$content .= '<tr>
		<td colspan="3" class="defaultfont"><textarea name="code" rows="8" cols="40" style="width:430px;height:300px">' . oldHtmlspecialchars($code) . '</textarea></td>
	</tr>
';
		}
		$content .= '</table>' . (($ok) ? "" : '<p class="defaultfont">*' . g_l('modules_banner', '[required]')) . '</p>';
		$cancel_button = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();");
		$ok_button = we_html_button::create_button(we_html_button::OK, we_html_button::WE_FORM . ":we_form");
		$back_button = we_html_button::create_button(we_html_button::BACK, "javascript:history.back();");
		$close_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:top.close();");

		$buttons = $ok ? we_html_button::formatButtons($close_button . $back_button) : we_html_button::position_yes_no_cancel($ok_button, null, $cancel_button);

		echo we_html_tools::htmlDialogLayout($content, g_l('modules_banner', $ok ? '[bannercode_copy]' : '[bannercode_ext]'), $buttons);
		?>
	</form>
</body>
</html>