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
function we_tag_shopField($attribs){
	if(($foo = attributFehltError($attribs, "name", __FUNCTION__))){
		return $foo;
	}
	if(($foo = attributFehltError($attribs, "reference", __FUNCTION__))){
		return $foo;
	}
	if(($foo = attributFehltError($attribs, "shopname", __FUNCTION__))){
		return $foo;
	}


	$name = weTag_getAttribute("name", $attribs);
	$reference = weTag_getAttribute("reference", $attribs);
	$shopname = weTag_getAttribute("shopname", $attribs);

	$type = weTag_getAttribute("type", $attribs);

	$values = weTag_getAttribute("values", $attribs); // select, choice
	$value = weTag_getAttribute("value", $attribs); // checkbox
	$checked = weTag_getAttribute("checked", $attribs, false, true); // checkbox
	$mode = weTag_getAttribute("mode", $attribs);

	$xml = weTag_getAttribute("xml", $attribs);

	$fieldname = ($reference == 'article' ? WE_SHOP_ARTICLE_CUSTOM_FIELD : WE_SHOP_CART_CUSTOM_FIELD) . '[' . $name . ']';
	$savedVal = '';
	$isFieldForCheckBox = false;

	if($reference == 'article'){ // name depends on value
		$savedVal = (!$shopname) && isset($_REQUEST[WE_SHOP_ARTICLE_CUSTOM_FIELD][$name]) ? $_REQUEST[WE_SHOP_ARTICLE_CUSTOM_FIELD][$name] : '';
		// does not exist here - we are only in article - custom fields are not stored on documents

		if(isset($GLOBALS['lv']) && ($tmpVal = we_tag('field', array('name' => $name)))){
			$savedVal = $tmpVal;
			unset($tmpVal);
		}
	} else{
		$savedVal = isset($GLOBALS[$shopname]) ? $GLOBALS[$shopname]->getCartField($name) : '';
		$isFieldForCheckBox = $GLOBALS[$shopname]->hasCartField($name);
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
			$tmpname = md5(uniqid(time()));
			$atts['name'] = $tmpname;
			$atts['type'] = 'checkbox';
			$atts['value'] = $value;
			$atts['onclick'] = 'this.form.elements[\''.$fieldname.'\'].value=(this.checked) ? \''.htmlspecialchars($value).'\' : \'\''; //#6544
			if(($savedVal == $value) || (!$isFieldForCheckBox) && $checked){
				$atts['checked'] = 'checked';
			}
            
            // added we_html_tools::hidden #6544
			return getHtmlTag('input', $atts).we_html_tools::hidden($fieldname, $savedVal);
			break;

		case 'choice':
			$reference = weTag_getAttribute("mode", $attribs);

			return we_getInputChoiceField($fieldname, $savedVal, $values, $atts, $mode);

			break;

		case 'hidden':
			return we_html_tools::hidden($fieldname, $savedVal);
			break;

		case 'print':
			return $savedVal;
			break;

		case 'select':
			return we_getSelectField($fieldname, $savedVal, $values, $atts, false);
			break;

		case 'textarea':
			return we_getTextareaField($fieldname, $savedVal, $atts);
			break;

		case 'radio':
			if($checked && $savedVal == ''){
				$atts['checked'] = 'checked';
			}
			return we_getInputRadioField($fieldname, $savedVal, $value, $atts);
			break;

		case 'textinput':
		default:
			return we_getInputTextInputField($fieldname, $savedVal, $atts);
			break;
	}
}
