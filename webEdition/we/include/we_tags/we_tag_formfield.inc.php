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
function we_tag_formfield(array $attribs){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}
	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);

	$types = weTag_getAttribute('type', $attribs, 'textinput', we_base_request::STRING_LIST);
	$attrs = weTag_getAttribute('attribs', $attribs, '', we_base_request::STRING_LIST);
	$ffname = $GLOBALS['we_doc']->getElement($name, 'ffname');

	$type_sel = $GLOBALS['we_doc']->getElement($name, 'fftype') ? : ($types ? $types[0] : 'textinput');

	$nameprefix = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . '#';

	$xml = weTag_getAttribute('xml', $attribs, XHTML_DEFAULT, we_base_request::BOOL);
	$ff = array();

	// here add some mandatory fields
	$mandatoryFields = array();
	if($xml){
		$mandatoryFields = array(
			'textarea_cols', 'textarea_rows'
		);
	}
	$m = array();
	foreach($attribs as $k => $v){
		if(preg_match('/^([^_]+)_([^_]+)$/', $k, $m) && ($m[1] == $type_sel)){
			if(in_array($k, $attrs)){
				if(isset($GLOBALS['we_doc']->elements[$name]['ff_' . $type_sel . '_' . $m[2]])){
					$ff[$m[2]]['value'] = $GLOBALS['we_doc']->getElement($name, 'ff_' . $type_sel . '_' . $m[2]);
				}
				$ff[$m[2]]['default'] = $attribs[$k];
			} else {
				/* $ff[$m[2]] = array('change' => 0, 'default' => $attribs[$k]); */
				$ff[$m[2]]['change'] = 0;
				$ff[$m[2]]['default'] = $attribs[$k];
			}
			if(in_array($m[0], $mandatoryFields)){
				for($i = (count($mandatoryFields) - 1); $i >= 0; $i--){
					if($mandatoryFields[$i] == $m[0]){
						unset($mandatoryFields[$i]);
					}
				}
			}
		}
	}

	$attrs = array_merge($attrs, $mandatoryFields);

	foreach($attrs as $a){
		if(preg_match('/^([^_]+)_([^_]+)$/', $a, $m) && ($m[1] == $type_sel)){
			$ff[$m[2]]['change'] = 1;

			if(isset($GLOBALS['we_doc']->elements[$name]['ff_' . $type_sel . '_' . $m[2]])){
				$t = $GLOBALS['we_doc']->getElement($name, 'ff_' . $type_sel . '_' . $m[2]);
				if(!empty($t)){
					$ff[$m[2]]['value'] = $t;
				}
			}
		}
	}

	if(!empty($GLOBALS['we_editmode'])){
		$tmp_select = '<select name="' . $nameprefix . 'fftype]" onchange="setScrollTo();we_cmd(\'reload_editpage\');">' . "\n";
		foreach($types as $k){
			$tmp_select .= '<option value="' . $k . '"' . (($k == $type_sel) ? ' selected="selected"' : '') . '>' . $k . '</option>';
		}
		$tmp_select .= '</select>';
		$tbl = '<table style="padding:4px;border:0px; color: black;" class="weEditTable weEditmodeStyle">
<colgroup>
<col style="width:8em;color: black;padding-right:1ex;"/>
<col style="width:15em;"/>
</colgroup>
	<tr>
		<td>' . g_l('global', '[name]') . ':</td>
		<td><input type="text" name="' . $nameprefix . 'ffname]" value="' . $ffname . '" required="required"/></td>
	</tr>
	<tr>
		<td>' . g_l('global', '[type]') . ':</td>
		<td>' . $tmp_select . '</td>
	</tr>';

		if($ff){
			$tbl .= '	<tr>
		<td>' . g_l('global', '[attributes]') . ':</td>
		<td>
			<table class="weEditTable spacing0 border0">
				<tr>';

			foreach($ff as $f => $m){
				$tbl .= '<td style="color: black; margin-left:5px;"><nobr><span class="small bold">' . $f . ':</span>&nbsp;';
				$val = isset($m['value']) ? $m['value'] : '';

				$default = isset($m['default']) ? makeArrayFromCSV($m['default']) : array();

				if($m['change'] == 1){
					if(count($default) > 1){
						$valselect = '<select name="' . $name . 'tmp" onchange="this.form.elements[\'' . $nameprefix . 'ff_' . $type_sel . '_' . $f . ']\'].value=this.options[this.selectedIndex].value;">' .
							'<option value=""></option>';
						foreach($default as $v){
							$valselect .= '<option value="' . $v . '">' . $v . '</option>';
						}
						$valselect .= '</select>';
					} else {
						$valselect = '';
					}
					if((!isset($m['value'])) && count($default) == 1){
						$val = $default[0];
					}
					$tbl .= '<input type="text" name="' . $nameprefix . 'ff_' . $type_sel . '_' . $f . ']" size="7"' . ($val ? ' value="' . $val . '"' : '') . ' />' . $valselect;
				} else {
					if(count($default) > 1){
						$val = $GLOBALS['we_doc']->getElement($name, 'ff_' . $type_sel . '_' . $f);
						if(count($default) > 1){
							$valselect = '<select name="' . $nameprefix . 'ff_' . $type_sel . '_' . $f . ']">';
							foreach($default as $v){
								$valselect .= '<option value="' . $v . '"' . (($v == $val) ? " selected" : "") . '>' . $v . '</option>';
							}
							$valselect .= '</select>';
							$tbl .= $valselect;
						}
					} else {
						$foo = empty($default) ? '' : $default[0];
						$tbl .= $foo . '<input type="hidden" name="' . $nameprefix . 'ff_' . $type_sel . '_' . $f . ']" value="' . $foo . '" />';
					}
				}
				$tbl .= '</span></nobr></td>';
			}
			$tbl .= '</tr>
			</table>
		</td>
	</tr>';
		}
		switch($type_sel){
			case 'select':
				$tbl .= '	<tr>
		<td>' . g_l('global', '[values]') . ':</td>
		<td><textarea name="' . $nameprefix . 'ffvalues]" cols="30" rows="5">' . $GLOBALS['we_doc']->getElement($name, 'ffvalues') . '</textarea></td>
	</tr>
	<tr>
		<td>' . g_l('global', '[default]') . ':</td>
		<td><input type="text" name="' . $nameprefix . 'ffdefault]" value="' . $GLOBALS['we_doc']->getElement($name, 'ffdefault') . '" /></td>
	</tr>';
				break;
			case 'file':
				$tbl .= '	<tr>
		<td>' . g_l('global', '[max_file_size]') . ':</td>
		<td><input type="text" name="' . $nameprefix . 'ffmaxfilesize]" value="' . $GLOBALS['we_doc']->getElement($name, 'ffmaxfilesize') . '" /></td>
	</tr>';
				break;
			case 'radio':
			case 'checkbox':
				$tbl .= '	<tr>
		<td>' . g_l('global', '[checked]') . ':</td>
		<td><select name="' . $nameprefix . 'ffchecked]"><option value="0"' . ($GLOBALS['we_doc']->getElement($name, 'ffchecked') ? "" : " selected") . '>' . g_l('global', '[no]') . '</option><option value="1"' . ($GLOBALS['we_doc']->getElement($name, 'ffchecked') ? " selected" : "") . '>' . g_l('global', '[yes]') . '</option></select></td>
	</tr>';
				break;
		}
		$tbl .= '</table>';

		return $tbl;
	}

	$tagEndTag = false;

	$tagAtts = removeAttribs($attribs, array('doc', 'type', 'attribs'));
	$tagAtts['name'] = oldHtmlspecialchars($GLOBALS['we_doc']->getElement($attribs['name'], 'ffname'));

	$tagContent = '';

	switch($type_sel){
		case 'textarea' :
		case 'select' :
			$tagName = $type_sel;
			break;
		default :
			$tagName = 'input';
			$tagAtts['type'] = $type_sel;
	}

	foreach(array_keys($ff) as $f){
		if(!((($f === 'value') && ($type_sel === 'textarea')) || $f === 'type')){
			$val = $GLOBALS['we_doc']->getElement($name, 'ff_' . $type_sel . '_' . $f);
			if($val){
				$tagAtts[$f] = oldHtmlspecialchars($val);
			}
		}
	}

	$ret = '';
	switch($type_sel){
		case 'checkbox':
		case 'radio':
			if($GLOBALS['we_doc']->getElement($name, 'ffchecked')){
				$tagAtts['checked'] = 'checked';
			}
			break;
		case 'textinput': // correct input type="text"
			$tagAtts['type'] = 'text';
			break;
		case 'textarea':
			$tagEndTag = true;
			if(isset($ff['value'])){
				$tagContent = oldHtmlspecialchars($ff['value']['value']);
			}
			if(!array_key_exists('cols', $tagAtts)){
				$tagAtts['cols'] = 20;
			}
			if(!array_key_exists('rows', $tagAtts)){
				$tagAtts['rows'] = 5;
			}
			break;
		case 'select':
			$selected = $GLOBALS['we_doc']->getElement($name, 'ffdefault');
			$foo = explode("<_BR_>", str_replace(array("\r\n", "\r", "\n",), '<_BR_>', $GLOBALS['we_doc']->getElement($name, 'ffvalues')));
			foreach($foo as $v){
				$atts = array(
					'value' => oldHtmlspecialchars(trim($v))
				);
				if($selected == $v){
					$atts['selected'] = 'selected';
				}
				$tagContent .= getHtmlTag('option', $atts, oldHtmlspecialchars($v));
			}
			break;
		case 'country':
			$orgVal = $GLOBALS['we_doc']->getElement($name, 'ffdefault');
			$docAttr = weTag_getAttribute("doc", $attribs, "self", we_base_request::STRING);
			$doc = we_getDocForTag($docAttr);
			$lang = $doc->Language;
			$langcode = ($lang ? substr($lang, 0, 2) : array_search($GLOBALS['WE_LANGUAGE'], getWELangs()) );

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

			$tagContent = '';
			if(WE_COUNTRIES_DEFAULT != ''){
				$tagContent.='<option value="--" ' . ($orgVal === '--' ? ' selected="selected">' : '>') . WE_COUNTRIES_DEFAULT . '</option>';
			}
			foreach($topCountries as $countrykey => &$countryvalue){
				$tagContent.='<option value="' . $countrykey . '" ' . ($orgVal == $countrykey ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($countryvalue) . '</option>';
			}
			unset($countryvalue);
			$tagContent.='<option value="-" disabled="disabled">----</option>';
			foreach($shownCountries as $countrykey2 => &$countryvalue2){
				$tagContent.='<option value="' . $countrykey2 . '" ' . ($orgVal == $countrykey2 ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($countryvalue2) . '</option>';
			}
			unset($countryvalue);
			$tagAtts['size'] = (isset($attrs['size']) ? $attrs['size'] : 1);
			//  $newAtts['name'] = $name;
			$tagName = 'select';
			break;
		case 'language':
			$orgVal = $GLOBALS['we_doc']->getElement($name, 'ffdefault');
			$docAttr = weTag_getAttribute('doc', $attribs, 'self', we_base_request::STRING);
			$doc = we_getDocForTag($docAttr);
			$lang = $doc->Language;
			$langcode = ($lang ? substr($lang, 0, 2) : array_search($GLOBALS['WE_LANGUAGE'], getWELangs()));
			$frontendL = $GLOBALS['weFrontendLanguages'];
			foreach($frontendL as &$lcvalue){
				$lccode = explode('_', $lcvalue);
				$lcvalue = $lccode[0];
			}
			unset($lcvalue);
			foreach($frontendL as &$lcvalue){
				$frontendLL[$lcvalue] = we_base_country::getTranslation($lcvalue, we_base_country::LANGUAGE, $langcode);
			}

			$oldLocale = setlocale(LC_ALL, NULL);
			setlocale(LC_ALL, $lang . '.UTF-8');
			asort($frontendLL, SORT_LOCALE_STRING);
			setlocale(LC_ALL, $oldLocale);

			$tagContent = '';
			foreach($frontendLL as $langkey => &$langvalue){
				$tagContent.='<option value="' . $langkey . '" ' . ($orgVal == $langkey ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($langvalue) . '</option>';
			}
			unset($langvalue);
			$tagAtts['size'] = (isset($attrs['size']) ? $attrs['size'] : 1);

			$tagName = 'select';
			break;
		case 'file':
			$ret = getHtmlTag('input', array(
				'type' => 'hidden',
				'name' => 'MAX_FILE_SIZE',
				'value' => oldHtmlspecialchars($GLOBALS['we_doc']->getElement($name, 'ffmaxfilesize')),
				'xml' => $xml
			));
			break;
	}
	return getHtmlTag($tagName, $tagAtts, $tagContent, $tagEndTag) . $ret;
}
