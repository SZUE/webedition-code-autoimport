<?php
function we_tag_select($attribs, $content){
	global $we_editmode, $l_global;
	$foo = attributFehltError($attribs, "name", "select");
	if ($foo)
		return $foo;
	$name = we_getTagAttribute("name", $attribs);
	$onchange = we_getTagAttribute("onchange", $attribs);
	$reload = we_getTagAttribute("reload", $attribs, "", true);

	if ($we_editmode) {
		$val = $GLOBALS["we_doc"]->getElement($name);
		$attr = we_make_attribs($attribs, "name,value,onchange");
		if ($val) {
			$content = eregi_replace("<(option[^>]*) selected>", "<\\1>", $content);
			if (eregi("<option>", $content))
				$content = eregi_replace(
						'<option>' . quotemeta($val) . "( ?[<\n\r\t])",
						'<option selected>' . $val . '\1',
						$content);
			if (eregi('<option value=[\'"]?.*[\'"]?>', $content))
				$content = eregi_replace(
						'<option value=[\'"]?' . quotemeta($val) . '[\'"]?>',
						'<option value="' . $val . '" selected>',
						$content);
		}
		return '<select onchange="_EditorFrame.setEditorIsHot(true);' . ($onchange ? $onchange : "") . ';' . ($reload ? (';setScrollTo();top.we_cmd(\'reload_editpage\');') : '') . '" class="defaultfont" name="we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $name . ']" ' . ($attr ? " $attr" : "") . '>' . $content . '</select>';
	} else {
		return ($GLOBALS["we_doc"]->getElement($name));
	}
}?>
