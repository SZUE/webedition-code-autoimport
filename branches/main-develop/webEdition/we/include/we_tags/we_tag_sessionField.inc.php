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
include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/utils/rndGenPass.inc.php');

function we_tag_sessionField($attribs, $content) {
	$foo = attributFehltError($attribs, 'name', 'sessionField');
	if ($foo){
		return $foo;
	}

	$name = we_getTagAttribute('name', $attribs);
	$xml = we_getTagAttribute('xml', $attribs, '', true);
	$removeFirstParagraph = we_getTagAttribute("removefirstparagraph", $attribs, 0, true, defined("REMOVEFIRSTPARAGRAPH_DEFAULT") ? REMOVEFIRSTPARAGRAPH_DEFAULT : true);
	$autobrAttr = we_getTagAttribute('autobr', $attribs, '', true);
	$checked = we_getTagAttribute('checked', $attribs, '', true);
	$values = we_getTagAttribute('values', $attribs);
	$type = we_getTagAttribute('type', $attribs);
	$size = we_getTagAttribute('size', $attribs);
	$dateformat = we_getTagAttribute('dateformat', $attribs);
	$value = we_getTagAttribute('value', $attribs);
	$orgVal = (isset($_SESSION['webuser'][$name]) && (strlen($_SESSION['webuser'][$name]) > 0)) ? $_SESSION['webuser'][$name] : (($type == 'radio') ? '' : $value);


	$autofill = we_getTagAttribute('autofill', $attribs, false);
	if ($autofill) {
		if ($name == 'Username') {
			$condition = array('caps' => 4, 'small' => 4, 'nums' => 4, 'specs' => 0);
		} else {
			$condition = array('caps' => 3, 'small' => 4, 'nums' => 3, 'specs' => 2);
		}
		$pass = new rndConditionPass(7, $condition);
		$orgVal = $pass->PassGen();
		//echo $tmppass;
	}

	switch ($type) {
		case "date" :
			$currentdate = we_getTagAttribute("currentdate", $attribs, "", true);
			$minyear = we_getTagAttribute("minyear", $attribs, "");
			$maxyear = we_getTagAttribute("maxyear", $attribs, "");
			$format = we_getTagAttribute("dateformat", $attribs, "");
			if ($currentdate) {
				$orgVal = time();
			}

			return getDateInput2(
					"s[we_date_" . $name . "]",
					($orgVal ? $orgVal : time()),
					false,
					$format,
					'',
					'',
					$xml,
					$minyear,
					$maxyear);
			break;
		case 'country':
			$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'maxlength', 'wysiwyg'));
			$newAtts['name'] = 's[' . $name . ']';
			$docAttr = we_getTagAttribute('doc', $attribs, 'self');
			$doc = we_getDocForTag($docAttr);
			$lang = $doc->Language;
			if ($lang!=''){
				$langcode= substr($lang,0,2);
			} else {
				$langcode = we_core_Local::weLangToLocale($GLOBALS["WE_LANGUAGE"]);
			}

			$zendsupported = Zend_Locale::getTranslationList('territory', $langcode, 2);
			$topCountries = (defined('WE_COUNTRIES_TOP') ? explode(',', WE_COUNTRIES_TOP) : explode(',', 'DE,AT,CH'));
			$topCountries = array_flip($topCountries);
			foreach ($topCountries as $countrykey => &$countryvalue) {
				$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
			}

			$shownCountries = (defined('WE_COUNTRIES_SHOWN') ? explode(',', WE_COUNTRIES_SHOWN) : explode(',', 'BE,DK,FI,FR,GR,IE,IT,LU,NL,PT,SE,ES,GB,EE,LT,MT,PL,SK,SI,CZ,HU,CY'));
			$shownCountries = array_flip($shownCountries);
			foreach ($shownCountries as $countrykey => &$countryvalue) {
				$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
			}
			$oldLocale = setlocale(LC_ALL, NULL);
			setlocale(LC_ALL, $lang . '.UTF-8');
			asort($topCountries, SORT_LOCALE_STRING);
			asort($shownCountries, SORT_LOCALE_STRING);
			setlocale(LC_ALL, $oldLocale);

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

		case 'language':
			$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'maxlength', 'wysiwyg'));
			$newAtts['name'] = 's[' . $name . ']';
			$docAttr = we_getTagAttribute('doc', $attribs, 'self');
			$doc = we_getDocForTag($docAttr);
			$lang = $doc->Language;
			if ($lang!=''){
				$langcode= substr($lang,0,2);
			} else {
				$langcode = we_core_Local::weLangToLocale($GLOBALS["WE_LANGUAGE"]);
			}
			$frontendL = $GLOBALS['weFrontendLanguages'];
			foreach ($frontendL as $lc => &$lcvalue) {
				$lccode = explode('_', $lcvalue);
				$lcvalue = $lccode[0];
			}
			foreach ($frontendL as &$lcvalue) {
				$frontendLL[$lcvalue] = Zend_Locale::getTranslation($lcvalue, 'language', $langcode);
			}

			$oldLocale = setlocale(LC_ALL, NULL);
			setlocale(LC_ALL, $lang . '.UTF-8');
			asort($frontendLL, SORT_LOCALE_STRING);
			setlocale(LC_ALL, $oldLocale);
			$content = '';
			foreach ($frontendLL as $langkey => &$langvalue) {
				$content.='<option value="' . $langkey . '" ' . ($orgVal == $langkey ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($langvalue) . '</option>' . "\n";
			}
			return getHtmlTag('select', $newAtts, $content, true);

		case 'select':
			$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'maxlength', 'wysiwyg'));
			return we_getSelectField('s[' . $name . ']', $orgVal, $values, $newAtts, true);

		case 'choice':
			$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'maxlength', 'rows', 'cols', 'wysiwyg'));
			$mode = we_getTagAttribute('mode', $attribs);
			return we_getInputChoiceField('s[' . $name . ']', $orgVal, $values, $newAtts, $mode);

		case 'textinput':
			$choice = we_getTagAttribute('choice', $attribs, '', true);
			$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'wysiwyg', 'rows', 'cols'));
			//FIXME: can't this be done by calling the 'choice' switch?
			if ($choice) { // because of backwards compatibility
				$newAtts = removeAttribs($newAtts, array('maxlength'));
				$newAtts['name'] = 's[' . $name . ']';

				$optionsAr = makeArrayFromCSV(we_getTagAttribute('options', $attribs));
				$isin = 0;
				$options = '';
				for ($i = 0; $i < sizeof($optionsAr); $i++) {
					if ($optionsAr[$i] == $orgVal) {
						$options .= getHtmlTag('option', array('value' => htmlspecialchars($optionsAr[$i]), 'selected' => 'selected'), $optionsAr[$i]) . "\n";
						$isin = 1;
					} else {
						$options .= getHtmlTag('option', array('value' => htmlspecialchars($optionsAr[$i])), $optionsAr[$i]) . "\n";
					}
				}
				if (!$isin) {
					$options .= getHtmlTag('option', array('value' => htmlspecialchars($orgVal), 'selected' => 'selected'), htmlspecialchars($orgVal)) . "\n";
				}
				return getHtmlTag('select', $newAtts, $options, true);
			} else {
				return we_getInputTextInputField('s[' . $name . ']', $orgVal, $newAtts);
			}
		case 'textarea':
			$pure = we_getTagAttribute('pure', $attribs, '', true);
			if ($pure) {
				$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'size', 'wysiwyg'));
				return we_getTextareaField('s[' . $name . ']', $orgVal, $newAtts);
			} else {
				include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/html/we_forms.inc.php');
				include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/js/we_textarea_include.inc.php');
				$pure = we_getTagAttribute('pure', $attribs, '', true);
				$autobr = $autobrAttr ? 'on' : 'off';
				$showAutobr = isset($attribs['autobr']);
				return we_forms::weTextarea('s[' . $name . ']', $orgVal, $attribs, $autobr, 'autobr', $showAutobr, $GLOBALS['we_doc']->getHttpPath(), false, false, $xml, $removeFirstParagraph, '');
			}
		case 'radio':
			if ((!isset($_SESSION['webuser'][$name])) && $checked) {
				$orgVal = $value;
			}
			$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'wysiwyg'));

			return we_getInputRadioField('s[' . $name . ']', $orgVal, $value, $newAtts);
		case 'checkbox':
			$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'wysiwyg'));

			if ((!isset($_SESSION['webuser'][$name])) && $checked) {
				$orgVal = 1;
			}
			return we_getInputCheckboxField('s[' . $name . ']', $orgVal, $newAtts);
		case 'password':
			$newAtts = removeAttribs($attribs, array('checked', 'options', 'selected', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'wysiwyg'));
			$newAtts['name'] = 's[' . $name . ']';
			$newAtts['value'] = htmlspecialchars($orgVal);
			return getHtmlTag('input', $newAtts);
		case 'print':
			$ascountry = we_getTagAttribute('ascountry', $attribs, 'false', true);
			$aslanguage = we_getTagAttribute('aslanguage', $attribs, 'false', true);
			if (!$ascountry && !$aslanguage) {
				if (is_numeric($orgVal) && !empty($dateformat)) {
					return date($dateformat, $orgVal);
				} elseif (!empty($dateformat) && $weTimestemp = new DateTime($orgVal)) {
					return $weTimestemp->format($dateformat);
				}
			} else {
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
				if ($ascountry) {
					if ($orgVal=='--') {
						return '';
					} else {
						return CheckAndConvertISOfrontend(Zend_Locale::getTranslation($orgVal, 'territory', $langcode));
					}
				}
				if ($aslanguage) {
					return CheckAndConvertISOfrontend(Zend_Locale::getTranslation($orgVal, 'language', $langcode));
				}
			}
			return $orgVal;
		case 'hidden':
			$usevalue = we_getTagAttribute('usevalue',$attribs,'false',true);
			$languageautofill = we_getTagAttribute('languageautofill', $attribs, 'false', true);
			$_hidden['type'] = 'hidden';
			$_hidden['name'] = 's[' . $name . ']';
			$_hidden['value'] = ($usevalue?$value:$orgVal);
			$_hidden['xml'] = $xml;
			if ($languageautofill) {
				$docAttr = we_getTagAttribute('doc', $attribs, 'self');
				$doc = we_getDocForTag($docAttr);
				$lang = $doc->Language;
				$langcode = substr($lang, 0, 2);
				$_hidden['value'] = $langcode;
			}
			return getHtmlTag('input', $_hidden);
		case 'img':
			if (!isset($_SESSION['webuser']['imgtmp'])) {
				$_SESSION['webuser']['imgtmp'] = array();
			}
			if (!isset($_SESSION['webuser']['imgtmp'][$name])) {
				$_SESSION['webuser']['imgtmp'][$name] = array();
			}

			$_SESSION['webuser']['imgtmp'][$name]['parentid'] = we_getTagAttribute('parentid', $attribs, '0');
			$_SESSION['webuser']['imgtmp'][$name]['width'] = we_getTagAttribute('width', $attribs, 0);
			$_SESSION['webuser']['imgtmp'][$name]['height'] = we_getTagAttribute('height', $attribs, 0);
			$_SESSION['webuser']['imgtmp'][$name]['quality'] = we_getTagAttribute('quality', $attribs, '8');
			$_SESSION['webuser']['imgtmp'][$name]['keepratio'] = we_getTagAttribute('keepratio', $attribs, '', true, true);
			$_SESSION['webuser']['imgtmp'][$name]['maximize'] = we_getTagAttribute('maximize', $attribs, '', true);
			$_SESSION['webuser']['imgtmp'][$name]['id'] = $orgVal ? $orgVal : '';

			$_foo = id_to_path($_SESSION['webuser']['imgtmp'][$name]['id']);
			if (!$_foo) {
				$_SESSION['webuser']['imgtmp'][$name]['id'] = 0;
			}

			$bordercolor = we_getTagAttribute('bordercolor', $attribs, '#006DB8');
			$checkboxstyle = we_getTagAttribute('checkboxstyle', $attribs);
			$inputstyle = we_getTagAttribute('inputstyle', $attribs);
			$checkboxclass = we_getTagAttribute('checkboxclass', $attribs);
			$inputclass = we_getTagAttribute('inputclass', $attribs);
			$checkboxtext = we_getTagAttribute('checkboxtext', $attribs, g_l('parser','[delete]'));

			if ($_SESSION['webuser']['imgtmp'][$name]['id']) {
				$attribs['id'] = $_SESSION['webuser']['imgtmp'][$name];
			}

			unset($attribs['width']);
			unset($attribs['height']);

			$showcontrol = we_getTagAttribute('showcontrol', $attribs, '', true, true);
			if ($showcontrol) {
				$we_button = new we_button();

				$foo = attributFehltError($attribs, 'parentid', 'sessionField');
				if ($foo)
					return $foo;
			}

			$imgId = $_SESSION['webuser']['imgtmp'][$name]['id'];

			include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/we_document.inc.php');

			$thumbnail = we_getTagAttribute('thumbnail', $attribs, '');
			if ($thumbnail != '') {
				$attr['thumbnail'] = $thumbnail;
				$imgTag = we_document::getFieldByVal($imgId, 'img', $attr);
			} else {
				$imgTag = we_document::getFieldByVal($imgId, 'img');
			}

			if ($showcontrol) {
				$checked = '';

				return '<table border="0" cellpadding="2" cellspacing="2" style="border: solid ' . $bordercolor . ' 1px;">
					<tr>
						<td class="weEditmodeStyle" colspan="2" align="center">' .
				$imgTag . '
							<input type="hidden" name="s[' . $name . ']" value="' . $_SESSION['webuser']['imgtmp'][$name]["id"] . '" /></td>
					</tr>
					<tr>
						<td class="weEditmodeStyle" colspan="2" align="left">
							<input' . ($size ? ' size="' . $size . '"' : '') . ' name="WE_SF_IMG_DATA['.$name.']" type="file" accept="' . IMAGE_CONTENT_TYPES . '"' . ($inputstyle ? (' style="' . $inputstyle . '"') : '') . ($inputclass ? (' class="' . $inputclass . '"') : '') . ' />
						</td>
					</tr>
					<tr>
						<td class="weEditmodeStyle" colspan="2" align="left">
							<table border="0" cellpadding="0" cellspacing="0">
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
