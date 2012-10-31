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

/**
 * Filename:    we_baseElement.inc.php
 * Directory:   /webEdition/we/include/we_classes/html
 *
 * Function:    Utility class that implements basic html elements operations
 *
 * Description: Provides functions for creating html tags
 */
class we_baseElement{

	var $uid;
	var $tag_name = '';
	var $need_end_tag = true;
	var $attribs = array('style' => array());
	var $content = '';
	private $rfc = 'html4';

	/**
	 * Constructor
	 * @param		$attribs								array
	 * @param		$content								string
	 *
	 * @return		we_baseElement
	 */
	function __construct($tagname = "", $need_end_tag = true, $attribs = '', $content = '', $rfc = 'html4'){
		$this->setTagName($tagname);
		$this->setNeedEndTag($need_end_tag);
		$this->setAttributes($attribs);
		$this->setContent($content);
		$this->rfc = $rfc;
	}

	/**
	 * Function fills uniquie id attribute with random value
	 *
	 * @return	void
	 */
	function setUniquieID($secure = true){
		$this->uid = ($secure ? md5(uniqid(__FILE__, true)) : str_replace('.', '', uniqid('', true)));
	}

	/**
	 * Function returns copy of object
	 *
	 * @return     we_baseElement
	 */
	function copy(){
		return unserialize(serialize($this));
	}

	/**
	 * Function sets tag name
	 *
	 * @param		$tagname								string
	 *
	 * @return		void
	 */
	function setTagName($tagname){
		$this->tag_name = $tagname;
	}

	/**
	 * Function sets need_end_tag element attribute. Attribute need_end_tag indicates when the element needs end tag.
	 *
	 * @param		$need_end_tag							bool
	 *
	 * @return		void
	 */
	function setNeedEndTag($need_end_tag){
		$this->need_end_tag = $need_end_tag;
	}

	/*
	 * Function sets element attributes
	 *
	 * @param		$attribs								array
	 *
	 * @return		void
	 */

	function setAttributes($attribs){
		if(is_array($attribs)){
			foreach($attribs as $k => $v){
				$this->setAttribute($k, $v);
			}
		}
	}

	/**
	 * Function sets element attribute
	 *
	 * @param		$attrib_name							string
	 * @param		$attrib_value							string
	 *
	 * @return		void
	 */
	function setAttribute($attrib_name, $attrib_value){
		if($attrib_name == 'style'){
			$attrib_value = rtrim($attrib_value, '; ');
			if(strpos($attrib_value, ';') !== FALSE){
				$vals = explode(';', $attrib_value);
				foreach($vals as $val){
					list($k, $v) = explode(':', $val);
					$this->setStyle($k, $v);
				}
			} else{
				if(trim($attrib_value)){
					list($k, $v) = explode(':', $attrib_value);
					$this->setStyle($k, $v);
				}
			}
		} else{
			switch($attrib_name){
				case 'valign':
					$this->setStyle('vertical-align', $attrib_value);
					break;
				case 'width':
					$this->setStyle('width', $attrib_value . (is_numeric($attrib_value) ? 'px' : ''));
					break;
				case 'height':
					$this->setStyle('height', $attrib_value . (is_numeric($attrib_value) ? 'px' : ''));
					break;
				case 'border':
					$this->setStyle('border-width', $attrib_value . (is_numeric($attrib_value) ? 'px' : ''));
					break;
				case 'bgcolor':
					$this->setStyle('background-color', $attrib_value);
				default:
					$this->attribs[$attrib_name] = $attrib_value;
			}
		}
	}

	function setStyle($type, $val){
		$this->attribs['style'][trim($type)] = trim($val);
	}

	/**
	 * Function gets element attribute
	 *
	 * @param		$attrib_name							string
	 *
	 * @return		string
	 */
	function getAttribute($attrib_name){
		return $this->attribs[$attrib_name];
	}

	/**
	 * Function sets element content
	 *
	 * @param		$content							string
	 *
	 * @return		void
	 */
	function setContent($content){
		$this->content = $content;
	}

	/**
	 * Function append content
	 *
	 * @param		$content							string
	 *
	 * @return		void
	 */
	function appendContent($content){
		$this->content.=$content;
	}

	/**
	 * The function generate HTML code for the tag
	 *
	 * @param		$object								we_baseElement
	 *
	 * @return		string
	 */
	static function getHtmlCode($object){
		return $object->getHTML();
	}

	function getHTML(){
		$out = '<' . $this->tag_name;
		foreach($this->attribs as $k => $v){
			if($k == 'style'){
				if(count($v)){
					$out.=' ' . $k . '="';
					foreach($v as $kk => $vv){
						$out.=$kk . ':' . $vv . ';';
					}
					$out.='"';
				}
			} else if($v !== '' || $k == 'value'){ //wg. Bug #5971 neu || $k == 'value'
				$out.=' ' . $k . '="' . $v . '"';
			} else{
				switch($k){
					case 'disabled':
					case 'multiple':
					case 'noshade':
					case 'nowrap':
					case 'readonly':
					case 'checked':
					case 'selected':
						$out.=$k . '="' . $k . '"';
						break;
					default:
						$out.=$k . '=""';
				}
			}
		}
		$out.=($this->need_end_tag === 'selfclose' ? '/' : '') . '>' .
			$this->content .
			($this->need_end_tag === true ? "</" . $this->tag_name . ">" : '');

		return $out;
	}

}
