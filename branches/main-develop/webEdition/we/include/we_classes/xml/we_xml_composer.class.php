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
abstract class we_xml_composer{

	static function we_xmlElement($name, $content = "", $attributes = null){
		$element = new we_html_baseElement($name, true, (isset($attributes) && is_array($attributes) ? $attributes : null), $content);
		return $element->getHTML();
	}

	/* Function creates new xml element.
	 *
	 * element - [name] - element name
	 * 				 [attributes] - atributes array in form arry["attribute_name"]=attribute_value
	 * 				 [content] - if array childs otherwise some content
	 *
	 */

	static function buildXMLElements($elements){
		$out = "";
		foreach($elements as $element){
			$element = new we_html_baseElement($element['name'], true, $element["attributes"], (is_array($element["content"]) ? we_xml_composer::buildXMLElements($element["content"]) : $element["content"]));
			$out.=$element->getHTML();
		}
		return $out;
	}

	static function buildAttributesFromArray($attribs){

		if(!is_array($attribs)){
			return '';
		}
		$out = '';
		foreach($attribs as $k => $v){
			if($v == null && $v != ""){
				$out.=' ' . $k;
			} else {
				$out.=' ' . $k . '="' . $v . '"';
			}
		}

		return $out;
	}

}
