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
function we_parse_tag_select($attribs, $content){
	return '<?php if($GLOBALS[\'we_editmode\']){ ob_start();?>' . $content . '<?php $we_select_content=ob_get_contents();ob_end_clean();}else{$we_select_content=\'\';}'
		. 'printElement(' . we_tag_tagParser::printTag('select', $attribs, '$we_select_content') . ');?>';
}

function we_tag_select($attribs, $content){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}
	$name = weTag_getAttribute('name', $attribs);
	$val = $GLOBALS['we_doc']->getElement($name);
	if($GLOBALS['we_editmode']){
		$onchange = weTag_getAttribute('onchange', $attribs);
		$reload = weTag_getAttribute('reload', $attribs, false, true);
		$attr = we_make_attribs($attribs, 'name,value,onchange,_name_orig');
		if(weTag_getAttribute('type', $attribs) == 'csv'){
			$vals = explode(',', weTag_getAttribute('values', $attribs, $content));
			$content = '';
			foreach($vals as $cur){
				$content.=($cur == $val ?
						getHtmlTag('option', array('value' => $cur, 'selected' => 'selected'), $cur, true) :
						getHtmlTag('option', array('value' => $cur), $cur, true)
					);
			}
		} else {
			$content = preg_replace('|<(option[^>]*) selected( *=? *"selected")?([^>]*)>|i', "<\\1\\3>", $content);
			if(stripos($content, '<option>') !== false){
				$content = preg_replace('|<option>' . preg_quote($val) . '( ?[<\n\r\t])|i', '<option selected="selected">' . $val . '\\1', $content);
			}
			if(preg_match('|<option[^>]*value=[\'"]?.*[\'"]?>|i', $content)){
				$content = preg_replace('|<option([^>]*)value *= *"' . preg_quote($val) . '"([^>]*)>|i', '<option value="' . $val . '" selected="selected">', $content);
			}
		}
		return '<select onchange="_EditorFrame.setEditorIsHot(true);' . ($onchange ? $onchange : "") . ';' . ($reload ? (';setScrollTo();top.we_cmd(\'reload_editpage\');') : '') . '" class="defaultfont" name="we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']" ' . ($attr ? " $attr" : "") . '>' . $content . '</select>';
	} else {
		return $val;
	}
}
