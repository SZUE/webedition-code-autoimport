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
function we_tag_input(array $attribs, $content){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}

	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);
	$value = weTag_getAttribute('value', $attribs, '', we_base_request::RAW);
	$values = weTag_getAttribute('values', $attribs, '', we_base_request::RAW);
	$mode = weTag_getAttribute('mode', $attribs, '', we_base_request::STRING);
	$type = weTag_getAttribute('type', $attribs, '', we_base_request::STRING);
	$format = weTag_getAttribute('format', $attribs, '', we_base_request::STRING);

	$seperator = weTag_getAttribute('seperator', $attribs, '|', we_base_request::RAW);
	$reload = weTag_getAttribute('reload', $attribs, false, we_base_request::BOOL);

	$spellcheck = weTag_getAttribute('spellcheck', $attribs, false, we_base_request::BOOL);

	$val = oldHtmlspecialchars($GLOBALS['we_doc']->issetElement($name) ? $GLOBALS['we_doc']->getElement($name) : $value);

	if($GLOBALS['we_editmode']){
		//all edit-specific things
		switch($type){
			case 'date':
				$currentdate = weTag_getAttribute('currentdate', $attribs, false, we_base_request::BOOL);
				$d = abs($GLOBALS['we_doc']->getElement($name));
				return we_html_tools::getDateInput('we_' . $GLOBALS['we_doc']->Name . '_date[' . $name . ']', $d? : ($currentdate ? time() : 0), true, $format);
			case 'checkbox':
				$attribs = removeAttribs($attribs, array('name', 'value', 'type', '_name_orig', 'reload'));
				$attribs['type'] = 'checkbox';
				$attribs['name'] = 'we_' . $GLOBALS['we_doc']->Name . '_attrib_' . $name;
				$attribs['value'] = 1;
				$attribs['onclick'] = '_EditorFrame.setEditorIsHot(true);this.form.elements[\'we_' . $GLOBALS['we_doc']->Name . '_checkbox[' . $name . ']\'].value=(this.checked ? 1 : 0);' . ($reload ? (';setScrollTo();top.we_cmd(\'reload_editpage\');') : '');
				if($val){
					$attribs['checked'] = 'checked';
				}

				return we_html_element::htmlHidden('we_' . $GLOBALS['we_doc']->Name . '_checkbox[' . $name . ']', $val) .
					getHtmlTag('input', $attribs);

			case 'country':
				$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'maxlength', 'wysiwyg'));
				$newAtts['name'] = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']';
				$newAtts['onclick'] = '_EditorFrame.setEditorIsHot(true);';
				$docAttr = weTag_getAttribute('doc', $attribs, 'self', we_base_request::STRING);
				$doc = we_getDocForTag($docAttr);
				$lang = $doc->Language;
				$langcode = ($lang ? substr($lang, 0, 2) : array_search($GLOBALS['WE_LANGUAGE'], getWELangs()));

				$orgVal = $GLOBALS['we_doc']->getElement($name);
				$topCountries = array_flip(explode(',', WE_COUNTRIES_TOP));
				foreach($topCountries as $countrykey => &$countryvalue){
					$countryvalue = we_base_country::getTranslation($countrykey, we_base_country::TERRITORY, $langcode);
				}
				unset($countryvalue);
				$shownCountries = array_flip(explode(',', WE_COUNTRIES_SHOWN));
				foreach($shownCountries as $countrykey => &$countryvalue){
					$countryvalue = we_base_country::getTranslation($countrykey, we_base_country::TERRITORY, $langcode);
				}
				unset($countryvalue);
				$oldLocale = setlocale(LC_ALL, NULL);
				setlocale(LC_ALL, $lang . '.UTF-8');
				asort($topCountries, SORT_LOCALE_STRING);
				asort($shownCountries, SORT_LOCALE_STRING);
				setlocale(LC_ALL, $oldLocale);
				$orgVal = $GLOBALS['we_doc']->getElement($name);
				$content = '';
				if(WE_COUNTRIES_DEFAULT != ''){
					$content.='<option value="--" ' . ($orgVal === '--' ? ' selected="selected">' : '>') . WE_COUNTRIES_DEFAULT . '</option>';
				}
				foreach($topCountries as $countrykey => &$countryvalue){
					$content.='<option value="' . $countrykey . '" ' . ($orgVal == $countrykey ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($countryvalue) . '</option>';
				}
				unset($countryvalue);
				$content.='<option value="-" disabled="disabled">----</option>';
				foreach($shownCountries as $countrykey2 => &$countryvalue2){
					$content.='<option value="' . $countrykey2 . '" ' . ($orgVal == $countrykey2 ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($countryvalue2) . '</option>';
				}
				unset($countryvalue2);

				return getHtmlTag('select', $newAtts, $content, true);
			case 'language':
				$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'maxlength', 'wysiwyg'));
				$newAtts['name'] = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']';
				$newAtts['onclick'] = '_EditorFrame.setEditorIsHot(true);';
				$docAttr = weTag_getAttribute('doc', $attribs, 'self', we_base_request::STRING);
				$doc = we_getDocForTag($docAttr);
				$lang = $doc->Language;
				$langcode = ($lang ? substr($lang, 0, 2) : array_search($GLOBALS['WE_LANGUAGE'], getWELangs()));

				$frontendL = $GLOBALS['weFrontendLanguages'];
				foreach($frontendL as &$lcvalue){
					$lccode = explode('_', $lcvalue);
					$lcvalue = $lccode[0];
				}
				$frontendLL = [];
				foreach($frontendL as &$lcvalue){
					$frontendLL[$lcvalue] = we_base_country::getTranslation($lcvalue, we_base_country::LANGUAGE, $langcode);
				}

				$oldLocale = setlocale(LC_ALL, NULL);
				setlocale(LC_ALL, $lang . '.UTF-8');
				asort($frontendLL, SORT_LOCALE_STRING);
				setlocale(LC_ALL, $oldLocale);
				$content = '';
				$orgVal = $GLOBALS['we_doc']->getElement($name);
				foreach($frontendLL as $langkey => &$langvalue){
					$content.='<option value="' . $langkey . '" ' . ($orgVal == $langkey ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($langvalue) . '</option>';
				}
				unset($langvalue);
				return getHtmlTag('select', $newAtts, $content, true);
			case 'choice':
				if($values){
					$tagname = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']';
					$vals = explode($seperator, $values);

					$onChange = ($mode === 'add' ?
							"this.form.elements['" . $tagname . "'].value += ((this.form.elements['" . $tagname . "'].value ? ' ' : '')+this.options[this.selectedIndex].text);" :
							"this.form.elements['" . $tagname . "'].value = this.options[this.selectedIndex].text;") .
						($reload ? 'setScrollTo();top.we_cmd(\'reload_editpage\');' : '');

					$sel = getHtmlTag('select', array(
						'class' => "defaultfont",
						'name' => 'we_choice_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']',
						'onchange' => $onChange . ';this.selectedIndex=0;_EditorFrame.setEditorIsHot(true);'
						), ($vals ? '<option>' . implode('</option><option>', $vals) . '</option>' : ''), true);
				}

				$attribs['onchange'] = '_EditorFrame.setEditorIsHot(true);';
				$attribs['type'] = "text";
				$attribs['name'] = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']';
				$attribs['value'] = $val;

				return getHtmlTag('input', removeAttribs($attribs, array('mode', 'values', '_name_orig'))) . "&nbsp;" . (isset($sel) ? $sel : '');
			case 'select':
				//NOTE: this tag is for objects only
				return $GLOBALS['we_doc']->getField($attribs, 'select');
			case 'print':
				return $val;
			case 'text':
			default:
				$attribs['onchange'] = '_EditorFrame.setEditorIsHot(true);';
				$attribs['class'] = "wetextinput";
				$attribs['type'] = "text";
				$attribs['name'] = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']';
				$attribs['value'] = $val;
				$input = getHtmlTag('input', removeAttribs($attribs, array('mode', 'values', '_name_orig')));
				return (defined('SPELLCHECKER') && $spellcheck ?
						'<table class="weEditTable padding0 spacing0 border0">
	<tr>
			<td class="weEditmodeStyle">' . $input . '</td>
			<td class="weEditmodeStyle">' . we_html_button::create_button('fa:spellcheck,fa-lg fa-font,fa-lg fa-check fa-ok', 'javascript:we_cmd("spellcheck","we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']")') . '</td>
	</tr>
</table>' :
						$input
					);
		}
	} else {
		//not-editmode
		switch($type){
			case 'date':
				return $GLOBALS['we_doc']->getField($attribs, 'date');
			case 'checkbox':
				return $GLOBALS['we_doc']->getElement($name);
			case 'country':
				$lang = weTag_getAttribute('outputlanguage', $attribs, '', we_base_request::STRING);
				if(!$lang){
					$docAttr = weTag_getAttribute('doc', $attribs, 'self', we_base_request::STRING);
					$doc = we_getDocForTag($docAttr);
					$lang = $doc->Language;
				}
				$langcode = substr($lang, 0, 2);
				if(!$lang){
					$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
					$langcode = array_search($lang[0], getWELangs());
				}
				if($GLOBALS['we_doc']->getElement($name) === '--'){
					return '';
				}
				return CheckAndConvertISOfrontend(we_base_country::getTranslation($GLOBALS['we_doc']->getElement($name), we_base_country::TERRITORY, $langcode));
			case 'language':
				$lang = weTag_getAttribute('outputlanguage', $attribs, '', we_base_request::STRING);
				if(!$lang){
					$docAttr = weTag_getAttribute('doc', $attribs, 'self', we_base_request::STRING);
					$doc = we_getDocForTag($docAttr);
					$lang = $doc->Language;
				}
				$langcode = substr($lang, 0, 2);
				if(!$lang){
					$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
					$langcode = array_search($lang[0], getWELangs());
				}
				return CheckAndConvertISOfrontend(we_base_country::getTranslation($GLOBALS['we_doc']->getElement($name), we_base_country::LANGUAGE, $langcode));
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
