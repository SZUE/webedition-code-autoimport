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
function we_tag_sessionField($attribs, $content){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}

	$name = weTag_getAttribute('_name_orig', $attribs);
	$xml = weTag_getAttribute('xml', $attribs, false, true);
	$removeFirstParagraph = weTag_getAttribute("removefirstparagraph", $attribs, defined('REMOVEFIRSTPARAGRAPH_DEFAULT') ? REMOVEFIRSTPARAGRAPH_DEFAULT : true, true);
	$autobrAttr = weTag_getAttribute('autobr', $attribs, false, true);
	$checked = weTag_getAttribute('checked', $attribs, false, true);
	$values = weTag_getAttribute('values', $attribs);
	$type = weTag_getAttribute('type', $attribs);
	$size = weTag_getAttribute('size', $attribs);
	$dateformat = weTag_getAttribute('dateformat', $attribs);
	$value = weTag_getAttribute('value', $attribs);
	$orgVal = (isset($_SESSION['webuser'][$name]) && (strlen($_SESSION['webuser'][$name]) > 0)) ? $_SESSION['webuser'][$name] : (($type == 'radio') ? '' : $value);


	$autofill = weTag_getAttribute('autofill', $attribs, false, true);
	if($autofill){
		$condition = ($name == 'Username' ?
				array('caps' => 4, 'small' => 4, 'nums' => 4, 'specs' => 0) :
				array('caps' => 3, 'small' => 4, 'nums' => 3, 'specs' => 2));

		$pass = new we_customer_generatePassword(7, $condition);
		$orgVal = $pass->PassGen();
		//echo $tmppass;
	}

	switch($type){
		case "date" :
			$currentdate = weTag_getAttribute("currentdate", $attribs, false, true);
			$minyear = weTag_getAttribute("minyear", $attribs);
			$maxyear = weTag_getAttribute("maxyear", $attribs);
			$format = weTag_getAttribute("dateformat", $attribs);
			if($currentdate){
				$orgVal = time();
			}
			try{
				$date = ($orgVal ? new DateTime((is_numeric($orgVal) ? '@' : '') . $orgVal) : 0);
			} catch (Exception $e){
				$date = new DateTime('now');
			}
			return we_html_tools::getDateInput2(
					"s[we_date_" . $name . "]", $date, false, $format, '', '', $xml, $minyear, $maxyear);

		case 'country':
			$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'maxlength', 'wysiwyg'));
			$newAtts['name'] = 's[' . $name . ']';
			$docAttr = weTag_getAttribute('doc', $attribs, 'self');
			$doc = we_getDocForTag($docAttr);
			$lang = $doc->Language;
			$langcode = ($lang ?
					substr($lang, 0, 2) :
					array_search($GLOBALS['WE_LANGUAGE'], getWELangs()));

			if(!Zend_Locale::hasCache()){
				Zend_Locale::setCache(getWEZendCache());
			}

			//$zendsupported = Zend_Locale::getTranslationList('territory', $langcode, 2);
			$topCountries = array_flip(explode(',', WE_COUNTRIES_TOP));
			foreach($topCountries as $countrykey => &$countryvalue){
				$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
			}
			unset($countryvalue);

			$shownCountries = array_flip(explode(',', WE_COUNTRIES_SHOWN));
			foreach($shownCountries as $countrykey => &$countryvalue){
				$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
			}
			unset($countryvalue);
			$oldLocale = setlocale(LC_ALL, NULL);
			setlocale(LC_ALL, $lang . '.UTF-8');
			asort($topCountries, SORT_LOCALE_STRING);
			asort($shownCountries, SORT_LOCALE_STRING);
			setlocale(LC_ALL, $oldLocale);

			$content = '';
			if(WE_COUNTRIES_DEFAULT != ''){
				$content.='<option value="--" ' . ($orgVal == '--' ? ' selected="selected">' : '>') . WE_COUNTRIES_DEFAULT . '</option>';
			}
			foreach($topCountries as $countrykey => &$countryvalue){
				$content.='<option value="' . $countrykey . '" ' . ($orgVal == $countrykey ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($countryvalue) . '</option>';
			}
			unset($countryvalue);

			if($topCountries && $shownCountries){
				$content.='<option value="-" disabled="disabled">----</option>';
			}
			foreach($shownCountries as $countrykey2 => &$countryvalue2){
				$content.='<option value="' . $countrykey2 . '" ' . ($orgVal == $countrykey2 ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($countryvalue2) . '</option>';
			}
			unset($countryvalue2);

			return getHtmlTag('select', $newAtts, $content, true);

		case 'language':
			$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'maxlength', 'wysiwyg'));
			$newAtts['name'] = 's[' . $name . ']';
			$docAttr = weTag_getAttribute('doc', $attribs, 'self');
			$doc = we_getDocForTag($docAttr);
			$lang = $doc->Language;
			$langcode = ($lang ? substr($lang, 0, 2) : array_search($GLOBALS['WE_LANGUAGE'], getWELangs()));

			$frontendL = $GLOBALS['weFrontendLanguages'];
			foreach($frontendL as &$lcvalue){
				$lccode = explode('_', $lcvalue);
				$lcvalue = $lccode[0];
			}
			if(!Zend_Locale::hasCache()){
				Zend_Locale::setCache(getWEZendCache());
			}

			$frontendLL = array();
			foreach($frontendL as &$lcvalue){
				$frontendLL[$lcvalue] = Zend_Locale::getTranslation($lcvalue, 'language', $langcode);
			}

			$oldLocale = setlocale(LC_ALL, NULL);
			setlocale(LC_ALL, $lang . '.UTF-8');
			asort($frontendLL, SORT_LOCALE_STRING);
			setlocale(LC_ALL, $oldLocale);
			$content = '';
			foreach($frontendLL as $langkey => &$langvalue){
				$content.='<option value="' . $langkey . '" ' . ($orgVal == $langkey ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($langvalue) . '</option>' . "\n";
			}
			unset($langvalue);
			return getHtmlTag('select', $newAtts, $content, true);

		case 'select':
			$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'maxlength', 'wysiwyg'));
			return we_getSelectField('s[' . $name . ']', $orgVal, $values, $newAtts, true);

		case 'choice':
			$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'maxlength', 'rows', 'cols', 'wysiwyg'));
			$mode = weTag_getAttribute('mode', $attribs);
			return we_html_tools::htmlInputChoiceField('s_' . $name . '', $orgVal, $values, $newAtts, $mode);

		case 'textinput':
			$choice = weTag_getAttribute('choice', $attribs, false, true);
			$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'wysiwyg', 'rows', 'cols'));
			//FIXME: can't this be done by calling the 'choice' switch?
			if($choice){ // because of backwards compatibility
				$newAtts = removeAttribs($newAtts, array('maxlength'));
				$newAtts['name'] = 's[' . $name . ']';

				$optionsAr = makeArrayFromCSV(weTag_getAttribute('options', $attribs));
				$isin = 0;
				$options = '';
				for($i = 0; $i < count($optionsAr); $i++){
					if($optionsAr[$i] == $orgVal){
						$options .= getHtmlTag('option', array('value' => oldHtmlspecialchars($optionsAr[$i]), 'selected' => 'selected'), $optionsAr[$i], true);
						$isin = 1;
					} else {
						$options .= getHtmlTag('option', array('value' => oldHtmlspecialchars($optionsAr[$i])), $optionsAr[$i], true);
					}
				}
				if(!$isin){
					$options .= getHtmlTag('option', array('value' => oldHtmlspecialchars($orgVal), 'selected' => 'selected'), oldHtmlspecialchars($orgVal), true);
				}
				return getHtmlTag('select', $newAtts, $options, true);
			}
			return we_getInputTextInputField('s[' . $name . ']', $orgVal, $newAtts);

		case 'textarea':
			//old Attribute
			$pure = weTag_getAttribute('pure', $attribs, false, true);
			if($pure){
				$attribs = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'size', 'wysiwyg'));
				return we_getTextareaField('s[' . $name . ']', $orgVal, $attribs);
			}
			echo we_html_element::jsElement('weFrontpageEdit=true;') .
			we_html_element::jsScript(JS_DIR . 'we_textarea.js') .
			we_html_element::jsScript(JS_DIR . 'windows.js');
			$autobr = $autobrAttr ? 'on' : 'off';
			$showAutobr = isset($attribs['autobr']);
			return we_html_forms::weTextarea('s[' . $name . ']', $orgVal, $attribs, $autobr, 'autobr', $showAutobr, $GLOBALS['we_doc']->getHttpPath(), false, false, $xml, $removeFirstParagraph, '');

		case 'radio':
			if((!isset($_SESSION['webuser'][$name])) && $checked){
				$orgVal = $value;
			}
			$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'wysiwyg'));

			return we_getInputRadioField('s[' . $name . ']', $orgVal, $value, $newAtts);
		case 'checkbox':
			$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'wysiwyg'));

			if((!isset($_SESSION['webuser'][$name])) && $checked){
				$orgVal = 1;
			}
			return we_getInputCheckboxField('s[' . $name . ']', $orgVal, $newAtts);
		case 'password':
			$newAtts = removeAttribs($attribs, array('checked', 'options', 'selected', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'wysiwyg'));
			$newAtts['name'] = 's[' . $name . ']';
			$newAtts['value'] = isset($_SESSION['webuser']['registered']) && $_SESSION['webuser']['registered'] ? we_customer_customer::NOPWD_CHANGE : ''; //oldHtmlspecialchars($_SESSION['webuser']['_Password']);
			return getHtmlTag('input', $newAtts);
		case 'print':
			$ascountry = weTag_getAttribute('ascountry', $attribs, false, true);
			$aslanguage = weTag_getAttribute('aslanguage', $attribs, false, true);
			if($ascountry || $aslanguage){
				if(!Zend_Locale::hasCache()){
					Zend_Locale::setCache(getWEZendCache());
				}

				$lang = weTag_getAttribute('outputlanguage', $attribs);
				if($lang == ''){
					$doc = we_getDocForTag(weTag_getAttribute('doc', $attribs, 'self'));
					$lang = $doc->Language;
				}
				$langcode = substr($lang, 0, 2);
				if($lang == ''){
					$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
					$langcode = array_search($lang[0], getWELangs());
				}
				return ($ascountry && $orgVal == '--' ? '' : CheckAndConvertISOfrontend(Zend_Locale::getTranslation($orgVal, ($ascountry ? 'territory' : 'language'), $langcode)));
			}
			if($dateformat){
				try{
					$date = new DateTime((is_numeric($orgVal) ? '@' : '') . $orgVal);
					return $date->format($dateformat);
				} catch (Exception $e){
					//fallback to default return
				}
			}
			return weTag_getAttribute('htmlspecialchars', $attribs, false, true) ? oldHtmlspecialchars($orgVal) : $orgVal;
		case 'hidden':
			$usevalue = weTag_getAttribute('usevalue', $attribs, false, true);
			$languageautofill = weTag_getAttribute('languageautofill', $attribs, false, true);
			$v = ($usevalue ? $value : $orgVal);
			$_hidden = array(
				'type' => 'hidden',
				'name' => 's[' . $name . ']',
				'value' => weTag_getAttribute('htmlspecialchars', $attribs, false, true) ? oldHtmlspecialchars($v) : $v,
				'xml' => $xml);
			if($languageautofill){
				$doc = we_getDocForTag(weTag_getAttribute('doc', $attribs, 'self'));
				$lang = $doc->Language;
				$langcode = substr($lang, 0, 2);
				$_hidden['value'] = $langcode;
			}
			return getHtmlTag('input', $_hidden);
		case 'img':
			if(!isset($_SESSION['webuser']['imgtmp'])){
				$_SESSION['webuser']['imgtmp'] = array();
			}
			if(!isset($_SESSION['webuser']['imgtmp'][$name])){
				$_SESSION['webuser']['imgtmp'][$name] = array();
			}

			$_SESSION['webuser']['imgtmp'][$name]['parentid'] = weTag_getAttribute('parentid', $attribs, 0);
			$_SESSION['webuser']['imgtmp'][$name]['width'] = weTag_getAttribute('width', $attribs, 0);
			$_SESSION['webuser']['imgtmp'][$name]['height'] = weTag_getAttribute('height', $attribs, 0);
			$_SESSION['webuser']['imgtmp'][$name]['quality'] = weTag_getAttribute('quality', $attribs, 8);
			$_SESSION['webuser']['imgtmp'][$name]['keepratio'] = weTag_getAttribute('keepratio', $attribs, true, true);
			$_SESSION['webuser']['imgtmp'][$name]['maximize'] = weTag_getAttribute('maximize', $attribs, false, true);
			$_SESSION['webuser']['imgtmp'][$name]['id'] = $orgVal ? $orgVal : '';

			$_foo = id_to_path($_SESSION['webuser']['imgtmp'][$name]['id']);
			if(!$_foo){
				$_SESSION['webuser']['imgtmp'][$name]['id'] = 0;
			}

			$bordercolor = weTag_getAttribute('bordercolor', $attribs, '#006DB8');
			$checkboxstyle = weTag_getAttribute('checkboxstyle', $attribs);
			$inputstyle = weTag_getAttribute('inputstyle', $attribs);
			$checkboxclass = weTag_getAttribute('checkboxclass', $attribs);
			$inputclass = weTag_getAttribute('inputclass', $attribs);
			$checkboxtext = weTag_getAttribute('checkboxtext', $attribs, g_l('parser', '[delete]'));

			if($_SESSION['webuser']['imgtmp'][$name]['id']){
				$attribs['id'] = $_SESSION['webuser']['imgtmp'][$name];
			}

			unset($attribs['width']);
			unset($attribs['height']);

			$showcontrol = weTag_getAttribute('showcontrol', $attribs, true, true);
			if($showcontrol){

				if(($foo = attributFehltError($attribs, 'parentid', __FUNCTION__))){
					return $foo;
				}
			}

			$imgId = $_SESSION['webuser']['imgtmp'][$name]['id'];

			$thumbnail = weTag_getAttribute('thumbnail', $attribs);
			if($thumbnail != ''){
				$attr['thumbnail'] = $thumbnail;
				$imgTag = $GLOBALS['we_doc']->getFieldByVal($imgId, 'img', $attr);
			} else {
				$imgTag = $GLOBALS['we_doc']->getFieldByVal($imgId, 'img');
			}

			if($showcontrol){
				$checked = '';

				return '<table class="weEditTable padding2 spacing2" style="border: solid ' . $bordercolor . ' 1px;">
	<tr>
		<td class="weEditmodeStyle" colspan="2" align="center">' .
					$imgTag . '
			<input type="hidden" name="s[' . $name . ']" value="' . $_SESSION['webuser']['imgtmp'][$name]["id"] . '" /></td>
	</tr>
	<tr>
		<td class="weEditmodeStyle" colspan="2" align="left">
			<input' . ($size ? ' size="' . $size . '"' : '') . ' name="WE_SF_IMG_DATA[' . $name . ']" type="file" accept="' . we_base_imageEdit::IMAGE_CONTENT_TYPES . '"' . ($inputstyle ? (' style="' . $inputstyle . '"') : '') . ($inputclass ? (' class="' . $inputclass . '"') : '') . ' />
		</td>
	</tr>
	<tr>
		<td class="weEditmodeStyle" colspan="2" align="left">
			<table class="weEditTable padding0 spacing0 border0">
				<tr>
					<td style="padding-right: 5px;">
						<input style="border:0px solid black;" type="checkbox" id="WE_SF_DEL_CHECKBOX_' . $name . '" name="WE_SF_DEL_CHECKBOX_' . $name . '" value="1" ' . $checked . '/>
					</td>
					<td>
						<label for="WE_SF_DEL_CHECKBOX_' . $name . '"' . ($checkboxstyle ? (' style="' . $checkboxstyle . '"') : '') . ($checkboxclass ? (' class="' . $checkboxclass . '"') : '') . '>' . $checkboxtext . '</label>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>';
			} else {
				return ($imgId ? $imgTag : '');
			}
	}
}
