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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

function we_tag_select($attribs, $content){
	$foo = attributFehltError($attribs, "name", "select");
	if ($foo)
		return $foo;
	$name = weTag_getAttribute("name", $attribs);
	$onchange = weTag_getAttribute("onchange", $attribs);
	$reload = weTag_getAttribute("reload", $attribs, false, true);	
	if ($GLOBALS['we_editmode']) {
		$val = $GLOBALS['we_doc']->getElement($name);
		$attr = we_make_attribs($attribs, "name,value,onchange");
		if ($val) {
			$content = eregi_replace("<(option[^>]*) selected>", "<\\1>", $content);
			if (stripos($content,"<option>")!==false)
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
		return '<select onchange="_EditorFrame.setEditorIsHot(true);' . ($onchange ? $onchange : "") . ';' . ($reload ? (';setScrollTo();top.we_cmd(\'reload_editpage\');') : '') . '" class="defaultfont" name="we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']" ' . ($attr ? " $attr" : "") . '>' . $content . '</select>';
	} else {
		return  $GLOBALS['we_doc']->getElement($name);
	}
}
