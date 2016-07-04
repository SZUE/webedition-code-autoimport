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
function we_tag_shopField(array $attribs){
	if(($foo = attributFehltError($attribs, array("name" => false, "reference" => false, "shopname" => false), __FUNCTION__))){
		return $foo;
	}

	$name = weTag_getAttribute("_name_orig", $attribs, '', we_base_request::STRING);
	$reference = weTag_getAttribute("reference", $attribs, '', we_base_request::STRING);
	$shopname = weTag_getAttribute("shopname", $attribs, '', we_base_request::STRING);

	$type = weTag_getAttribute("type", $attribs, '', we_base_request::STRING);

	if($type === 'checkbox' && ($missingAttrib = attributFehltError($attribs, 'value', __FUNCTION__))){
		echo $missingAttrib;
	}

	$values = weTag_getAttribute("values", $attribs, '', we_base_request::RAW); // select, choice
	$value = oldHtmlspecialchars(weTag_getAttribute('value', $attribs, '', we_base_request::RAW), -1, 'ISO-8859-1', false); // checkbox
	$checked = weTag_getAttribute("checked", $attribs, false, we_base_request::BOOL); // checkbox

	if($checked && ($foo = attributFehltError($attribs, "value", __FUNCTION__))){
		return $foo;
	}
	$mode = weTag_getAttribute('mode', $attribs, '', we_base_request::STRING);

	$fieldname = ($reference === 'article' ? WE_SHOP_ARTICLE_CUSTOM_FIELD : WE_SHOP_CART_CUSTOM_FIELD) . '[' . $name . ']';
	
	$isFieldForCheckBox = false;

	if($reference === 'article'){ // name depends on value
		$savedVal = (!$shopname) ? we_base_request::_(we_base_request::HTML, WE_SHOP_ARTICLE_CUSTOM_FIELD, '', $name) : '';
		// does not exist here - we are only in article - custom fields are not stored on documents

		if(isset($GLOBALS['lv']) && ($tmpVal = we_tag('field', array('name' => $name)))){
			$savedVal = $tmpVal;
			unset($tmpVal);
		}
	} else {
		$savedVal = isset($GLOBALS[$shopname]) ? $GLOBALS[$shopname]->getCartField($name) : '';
		$isFieldForCheckBox = isset($GLOBALS[$shopname]) ? $GLOBALS[$shopname]->hasCartField($name) : false;
	}

	$atts = removeAttribs($attribs, array('name', 'reference', 'shopname', 'type', 'values', 'value', 'checked', 'mode'));

	if($type != 'checkbox' && $type != 'choice' && $type != 'radio' && $value){
		// value is compared to saved value in some cases
		// be careful with different behaviour when using value and values
		if(!$savedVal){
			$savedVal = $value;
		}
	}

	switch($type){
		case "checkbox":
			$atts = removeAttribs($atts, array('size'));
			//$atts['name'] = $fieldname; changed to $tnpname because of new hidden field #6544
			//we_getInputCheckboxField() not possible because sessionField type="checkbox" has a mandatory value
			$tmpname = md5(uniqid(__FUNCTION__, true)); // #6590, changed from: uniqid(time())
			$atts['name'] = $tmpname;
			$atts['type'] = 'checkbox';
			$atts['value'] = $value;
			$atts['onclick'] = 'this.form.elements[\'' . $fieldname . '\'].value=(this.checked) ? \'' . oldHtmlspecialchars($value) . '\' : \'\''; //#6544
			if(($savedVal == $value) || (!$isFieldForCheckBox) && $checked){
				$atts['checked'] = 'checked';
			}

			// added we_html_element::htmlHidden #6544
			return getHtmlTag('input', $atts) . we_html_element::htmlHidden($fieldname, $savedVal);

		case 'choice':
			return we_html_tools::htmlInputChoiceField($fieldname, $savedVal, $values, $atts, $mode);

		case 'hidden':
			$atts = removeAttribs($atts, array('reference'));
			$atts['name'] = $fieldname;
			$atts['value'] = $savedVal;
			return getHtmlTag('hidden', $atts);

		case 'print':
			$ascountry = weTag_getAttribute('ascountry', $attribs, false, we_base_request::BOOL);
			$aslanguage = weTag_getAttribute('aslanguage', $attribs, false, we_base_request::BOOL);
			if($ascountry || $aslanguage){
				$lang = weTag_getAttribute('outputlanguage', $attribs, '', we_base_request::STRING);
				if(!$lang){
					$doc = we_getDocForTag(weTag_getAttribute('doc', $attribs, 'self', we_base_request::STRING));
					$lang = $doc->Language;
				}
				$langcode = substr($lang, 0, 2);
				if(!$lang){
					$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
					$langcode = array_search($lang[0], getWELangs());
				}
				return ($ascountry && $savedVal === '--' ? '' : CheckAndConvertISOfrontend(we_base_country::getTranslation($savedVal, ($ascountry ? we_base_country::TERRITORY : we_base_country::LANGUAGE), $langcode)));
			}
			return $savedVal;

		case 'select':
			return we_getSelectField($fieldname, $savedVal, $values, $atts, false);

		case 'country':
			$newAtts = removeAttribs($attribs, array('name', 'type', 'value', 'values', 'checked', 'mode'));
			$newAtts['name'] = 'we_sscf[' . $name . ']';
			$docAttr = weTag_getAttribute('doc', $attribs, 'self', we_base_request::STRING);
			$doc = we_getDocForTag($docAttr);
			$lang = $doc->Language;
			$langcode = ($lang ?
					substr($lang, 0, 2) :
					array_search($GLOBALS['WE_LANGUAGE'], getWELangs()));

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

			$content = '';
			if(WE_COUNTRIES_DEFAULT != ''){
				$content.='<option value="--" ' . ($savedVal === '--' ? ' selected="selected">' : '>') . WE_COUNTRIES_DEFAULT . '</option>';
			}
			foreach($topCountries as $countrykey => &$countryvalue){
				$content.='<option value="' . $countrykey . '" ' . ($savedVal == $countrykey ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($countryvalue) . '</option>';
			}
			unset($countryvalue);

			if(!empty($topCountries) && !empty($shownCountries)){
				$content.='<option value="-" disabled="disabled">----</option>';
			}
			foreach($shownCountries as $countrykey2 => &$countryvalue2){
				$content.='<option value="' . $countrykey2 . '" ' . ($savedVal == $countrykey2 ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($countryvalue2) . '</option>';
			}
			unset($countryvalue2);

			return getHtmlTag('select', $newAtts, $content, true);

		case 'language':
			$newAtts = removeAttribs($attribs, array('name', 'type', 'value', 'values', 'checked', 'mode'));
			$newAtts['name'] = 'we_sscf[' . $name . ']';
			$docAttr = weTag_getAttribute('doc', $attribs, 'self', we_base_request::STRING);
			$doc = we_getDocForTag($docAttr);
			$lang = $doc->Language;
			$langcode = ($lang ?
					substr($lang, 0, 2) :
					array_search($GLOBALS['WE_LANGUAGE'], getWELangs()));

			$frontendL = $GLOBALS['weFrontendLanguages'];
			foreach($frontendL as &$lcvalue){
				$lccode = explode('_', $lcvalue);
				$lcvalue = $lccode[0];
			}
			$frontendLL = array();
			foreach($frontendL as &$lcvalue){
				$frontendLL[$lcvalue] = we_base_country::getTranslation($lcvalue, we_base_country::LANGUAGE, $langcode);
			}

			$oldLocale = setlocale(LC_ALL, NULL);
			setlocale(LC_ALL, $lang . '.UTF-8');
			asort($frontendLL, SORT_LOCALE_STRING);
			setlocale(LC_ALL, $oldLocale);
			$content = '';
			foreach($frontendLL as $langkey => &$langvalue){
				$content.='<option value="' . $langkey . '" ' . ($savedVal == $langkey ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($langvalue) . '</option>' . "\n";
			}
			unset($langvalue);
			return getHtmlTag('select', $newAtts, $content, true);

		case 'textarea':
			return we_getTextareaField($fieldname, $savedVal, $atts);

		case 'radio':
			if($checked && $savedVal === ''){
				$atts['checked'] = 'checked';
			}
			return we_getInputRadioField($fieldname, $savedVal, $value, $atts);

		case 'textinput':
		default:
			return we_getInputTextInputField($fieldname, $savedVal, $atts);
	}
}
