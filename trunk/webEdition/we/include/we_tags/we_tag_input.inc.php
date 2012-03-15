<?php

/**
 * webEdition CMS
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
function we_tag_input($attribs, $content) {
	$foo = attributFehltError($attribs, 'name', 'input');
	if ($foo)
		return $foo;

	$name = we_getTagAttribute('name', $attribs);
	$value = we_getTagAttribute('value', $attribs);
	$values = we_getTagAttribute('values', $attribs);
	$mode = we_getTagAttribute('mode', $attribs);
	$type = we_getTagAttribute('type', $attribs);
	$format = we_getTagAttribute('format', $attribs);

	$seperator = we_getTagAttribute('seperator', $attribs, '|');
	$reload = we_getTagAttribute('reload', $attribs, '', true);

	$spellcheck = we_getTagAttribute('spellcheck', $attribs, 'false');

	$val = htmlspecialchars($GLOBALS['we_doc']->issetElement($name) ? $GLOBALS['we_doc']->getElement($name) : $value);

	if ($GLOBALS['we_editmode']) {
		//all edit-specific things
		switch ($type) {
			case 'date':
				$d = abs($GLOBALS['we_doc']->getElement($name));
				return getDateInput2(
								'we_' . $GLOBALS['we_doc']->Name . '_date[' . $name . ']',
								($d ? $d : time()),
								true,
								$format);
			case 'checkbox':
				$attr = we_make_attribs($attribs, 'name,value,type');
				return '<input onclick="_EditorFrame.setEditorIsHot(true);this.form.elements[\'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']\'].value=(this.checked ? 1 : \'\');' . ($reload ? (';setScrollTo();top.we_cmd(\'reload_editpage\');') : '') . '" type="checkbox" name="we_' . $GLOBALS['we_doc']->Name . '_attrib_' . $name . '" value="1"' . ($attr ? " $attr" : "") . ($val ? " checked" : "") . ' /><input type="hidden" name="we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']" value="' . $val . '" />';
			case 'country':
				$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'maxlength', 'wysiwyg'));
				$newAtts['name'] = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']';
				$newAtts['onclick'] = '_EditorFrame.setEditorIsHot(true);';
				$docAttr = we_getTagAttribute('doc', $attribs, 'self');
				$doc = we_getDocForTag($docAttr);
				$lang = $doc->Language;
				$langcode = substr($lang, 0, 2);
				if ($lang == '') {
					$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
					$langcode = array_search($lang[0], $GLOBALS['WE_LANGS']);
				}
				$orgVal = $GLOBALS['we_doc']->getElement($name);
				if(isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'],'IIS') !==false ){
					Zend_Locale::disableCache(true);
				}
				$zendsupported = Zend_Locale::getTranslationList('territory', $langcode, 2);
				if (defined('WE_COUNTRIES_TOP')) {
					$topCountries = explode(',', WE_COUNTRIES_TOP);
				} else {
					$topCountries = explode(',', 'DE,AT,CH');
				}
				$topCountries = array_flip($topCountries);
				foreach ($topCountries as $countrykey => &$countryvalue) {
					$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
				}
				if (defined('WE_COUNTRIES_SHOWN')) {
					$shownCountries = explode(',', WE_COUNTRIES_SHOWN);
				} else {
					$shownCountries = explode(',', 'BE,DK,FI,FR,GR,IE,IT,LU,NL,PT,SE,ES,GB,EE,LT,MT,PL,SK,SI,CZ,HU,CY');
				}
				$shownCountries = array_flip($shownCountries);
				foreach ($shownCountries as $countrykey => &$countryvalue) {
					$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
				}
				$oldLocale = setlocale(LC_ALL, NULL);
				setlocale(LC_ALL, $lang . '.UTF-8');
				asort($topCountries, SORT_LOCALE_STRING);
				asort($shownCountries, SORT_LOCALE_STRING);
				setlocale(LC_ALL, $oldLocale);
				$orgVal = $GLOBALS['we_doc']->getElement($name);
				$content = '';
				if(defined('WE_COUNTRIES_DEFAULT') && WE_COUNTRIES_DEFAULT !=''){
					$content.='<option value="--" ' . ($orgVal == '--' ? ' selected="selected">' : '>') .WE_COUNTRIES_DEFAULT . '</option>' . "\n";
				}
				foreach ($topCountries as $countrykey => &$countryvalue) {
					$content.='<option value="' . $countrykey . '" ' . ($orgVal == $countrykey ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($countryvalue) . '</option>' . "\n";
				}
				$content.='<option value="-" disabled="disabled">----</option>' . "\n";
				foreach ($shownCountries as $countrykey2 => &$countryvalue2) {
					$content.='<option value="' . $countrykey2 . '" ' . ($orgVal == $countrykey2 ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($countryvalue2) . '</option>' . "\n";
				}

				return getHtmlTag('select', $newAtts, $content, true);
			//return '<input onclick="_EditorFrame.setEditorIsHot(true);this.form.elements[\'we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $name . ']\'].value=(this.checked ? 1 : \'\');' . ($reload ? (';setScrollTo();top.we_cmd(\'reload_editpage\');') : '') . '" type="checkbox" name="we_' . $GLOBALS["we_doc"]->Name . '_attrib_' . $name . '" value="1"' . ($attr ? " $attr" : "") . ($val ? " checked" : "") . ' /><input type="hidden" name="we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $name . ']" value="' . $val . '" />';
			case 'language':
				$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'maxlength', 'wysiwyg'));
				$newAtts['name'] = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']';
				$newAtts['onclick'] = '_EditorFrame.setEditorIsHot(true);';
				$docAttr = we_getTagAttribute('doc', $attribs, 'self');
				$doc = we_getDocForTag($docAttr);
				$lang = $doc->Language;
				$langcode = substr($lang, 0, 2);
				if ($lang == '') {
					$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
					$langcode = array_search($lang[0], $GLOBALS['WE_LANGS']);
				}
				$frontendL = array_keys($GLOBALS['weFrontendLanguages']);
				foreach ($frontendL as $lc => &$lcvalue) {
					$lccode = explode('_', $lcvalue);
					$lcvalue = $lccode[0];
				}
				if(isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'],'IIS') !==false ){
					Zend_Locale::disableCache(true);
				}
				foreach ($frontendL as &$lcvalue) {
					$frontendLL[$lcvalue] = Zend_Locale::getTranslation($lcvalue, 'language', $langcode);
				}

				$oldLocale = setlocale(LC_ALL, NULL);
				setlocale(LC_ALL, $lang . '.UTF-8');
				asort($frontendLL, SORT_LOCALE_STRING);
				setlocale(LC_ALL, $oldLocale);
				$content = '';
				$orgVal = $GLOBALS['we_doc']->getElement($name);
				foreach ($frontendLL as $langkey => &$langvalue) {
					$content.='<option value="' . $langkey . '" ' . ($orgVal == $langkey ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($langvalue) . '</option>' . "\n";
				}
				return getHtmlTag('select', $newAtts, $content, true);
			case 'choice':
				if ($values) {
					$tagname = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']';
					$vals = explode($seperator, $values);

					if ($mode == 'add') {
						$onChange = "this.form.elements['$tagname'].value += ((this.form.elements['$tagname'].value ? ' ' : '')+this.options[this.selectedIndex].text);";
					} else {
						$onChange = "this.form.elements['$tagname'].value = this.options[this.selectedIndex].text;";
					}
					if ($reload) {
						$onChange .= 'setScrollTo();top.we_cmd(\'reload_editpage\');';
					}
					$sel = '<select  class="defaultfont" name="we_choice_' . $GLOBALS["we_doc"]->Name . '_txt[' . $name . ']" size="1" onchange="' . $onChange . ';this.selectedIndex=0;_EditorFrame.setEditorIsHot(true);"><option></option>';

					$sel.=(sizeof($vals)?'<option>'.implode("</option>\n<option>", $vals)."</option>\n":'');
					$sel .= "</select>\n";
				}
				$attr = we_make_attribs($attribs, 'name,value,type,onchange,mode,values');

				return '<input onchange="_EditorFrame.setEditorIsHot(true);" type="text" name="we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $name . ']" value="' . $val . '"' . ($attr ? " $attr" : "") . ' />' . "&nbsp;" . (isset(
								$sel) ? $sel : '');
			case 'select':
				//NOTE: this tag is for objects only
				return $GLOBALS['we_doc']->getField($attribs, 'select');
			case 'print':
				return $val;
			case 'text':
			default:
				$we_button = new we_button();
				$attr = we_make_attribs($attribs, 'name,value,type,html');

				if (defined('SPELLCHECKER') && $spellcheck == 'true') {
					return '<table border="0" cellpadding="0" cellspacing="0" background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif">
	<tr>
			<td class="weEditmodeStyle"><input onchange="_EditorFrame.setEditorIsHot(true);" class="wetextinput" type="text" name="we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $name . ']" value="' . $val . '"' . ($attr ? " $attr" : "") . ' /></td>
			<td class="weEditmodeStyle">' . getPixel(6, 4) . '</td>
			<td class="weEditmodeStyle">' . $we_button->create_button(
									'image:spellcheck',
									'javascript:we_cmd("spellcheck","we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']")') . '</td>
	</tr>
</table>';
				} else {
					return '<input onchange="_EditorFrame.setEditorIsHot(true);" class="wetextinput" type="text" name="we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $name . ']" value="' . $val . '"' . ($attr ? " $attr" : "") . ' />';
				}
		}
	} else {
		//not-editmode
		switch ($type) {
			case 'date':
				return $GLOBALS['we_doc']->getField($attribs, 'date');
			case 'checkbox':
				return $GLOBALS['we_doc']->getElement($name);
			case 'country':
				$lang = we_getTagAttribute('outputlanguage', $attribs, '');
				if ($lang == '') {
					$docAttr = we_getTagAttribute('doc', $attribs, 'self');
					$doc = we_getDocForTag($docAttr);
					$lang = $doc->Language;
				}
				$langcode = substr($lang, 0, 2);
				if ($lang == '') {
					$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
					$langcode = array_search($lang[0], $GLOBALS['WE_LANGS']);
				}
				if ($GLOBALS['we_doc']->getElement($name)=='--') {
					return '';
				} else {
					if(isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'],'IIS') !==false ){
						Zend_Locale::disableCache(true);
					}
					return CheckAndConvertISOfrontend(Zend_Locale::getTranslation($GLOBALS['we_doc']->getElement($name), 'territory', $langcode));
				}
			case 'language':
				$lang = we_getTagAttribute('outputlanguage', $attribs, '');
				if ($lang == '') {
					$docAttr = we_getTagAttribute('doc', $attribs, 'self');
					$doc = we_getDocForTag($docAttr);
					$lang = $doc->Language;
				}
				$langcode = substr($lang, 0, 2);
				if ($lang == '') {
					$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
					$langcode = array_search($lang[0], $GLOBALS['WE_LANGS']);
				}
				if(isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'],'IIS') !==false ){
					Zend_Locale::disableCache(true);
				}
				return CheckAndConvertISOfrontend(Zend_Locale::getTranslation($GLOBALS['we_doc']->getElement($name), 'language', $langcode));
			case 'choice':
				return $GLOBALS['we_doc']->getElement($name);
			case 'select':
				return $GLOBALS['we_doc']->getField($attribs, 'select');
			case 'text':
			default:
				return $GLOBALS['we_doc']->getField($attribs);
		}
	}
}
