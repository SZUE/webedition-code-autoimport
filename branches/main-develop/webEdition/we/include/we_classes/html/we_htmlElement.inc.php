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
 * Filename:    we_htmlElement.inc.php
 * Directory:   /webEdition/we/include/we_classes/html
 *
 * Function:    Class to create html tags
 *
 * Description: Provides functions for creating html tags
 */
abstract class we_htmlElement{

	/**
	 * Function generates html code for html form
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	static function htmlForm($attribs=array(), $content=''){

		if(!isset($attribs['name']))
			$attribs['name'] = 'we_form';
		return we_baseElement::getHtmlCode(new we_baseElement('form', true, $attribs, $content));
	}

	/**
	 * Function generates html code for html input element
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	static function htmlInput($attribs=array()){

		if(!isset($attribs['class']))
			$attribs['class'] = 'defaultfont';
		return we_baseElement::getHtmlCode(new we_baseElement('input', false, $attribs));
	}

	/**
	 * Function generates html code for html radio-checkbox input element
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	static function htmlRadioCheckbox($attribs=array()){
		$attribs['type'] = 'checkbox';

		$table = new we_htmlTable(array('cellpadding' => '0', 'cellspacing' => '0', 'border' => '0'), 1, 3);
		$table->setColContent(0, 0, we_htmlElement::htmlInput($attribs));
		$table->setColContent(0, 1, we_html_tools::getPixel(4, 2));
		$table->setColContent(0, 2, we_htmlElement::htmlLabel(array('for' => '$name', 'title' => sprintf(g_l('htmlForms', '[click_here]'), $attribs['title']), $attribs['title'])));

		return $table->getHtml();
	}

	/**
	 * Function generates css code
	 *
	 * @param		$content								string			(optional)
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	static function cssElement($content='', $attribs=array()){
		$attribs['type'] = 'text/css';
		return we_baseElement::getHtmlCode(new we_baseElement('style', true, $attribs, $content));
	}

	static function jsScript($name){
		$attribs = array(
			'src' => $name,
			'type' => 'text/javascript',
		);
		return we_baseElement::getHtmlCode(new we_baseElement('script', true, $attribs));
	}

	/**
	 * Function generates js code
	 *
	 * @param		$content								string			(optional)
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	static function jsElement($content='', $attribs=array()){
		$attribs['type'] = 'text/javascript';
		if(strpos($content, '<!--') === FALSE){
			$content = "<!--\n" . $content . "\n//-->\n";
		}
		return we_baseElement::getHtmlCode(new we_baseElement('script', true, $attribs, $content));
	}

	static function cssLink($url){
		return we_baseElement::getHtmlCode(new we_baseElement('link', false,
					array('href' => $url, 'rel' => 'styleSheet', 'type' => 'text/css')
			));
	}

	/**
	 * Function generates link code
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	static function linkElement($attribs=array()){
		return we_baseElement::getHtmlCode(new we_baseElement('link', false, $attribs));
	}

	/**
	 * Function generates html code for html font element
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	static function htmlFont($attribs=array(), $content=''){
		return we_baseElement::getHtmlCode(new we_baseElement('font', true, $attribs, $content));
	}

	/**
	 * Function generates html code for html div elements
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	static function htmlSpan($attribs=array(), $content=''){
		return we_baseElement::getHtmlCode(new we_baseElement('span', true, $attribs, $content));
	}

	/**
	 * Function generates html code for html div elements
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	static function htmlDiv($attribs=array(), $content=''){
		return we_baseElement::getHtmlCode(new we_baseElement('div', true, $attribs, $content));
	}

	/**
	 * Function generates html code for html b element
	 *
	 * @param		$content								string
	 *
	 * @return		string
	 */
	static function htmlB($content){
		return we_baseElement::getHtmlCode(new we_baseElement('b', true, array(), $content));
	}

	/**
	 * Function generates html code for html i element
	 *
	 * @param		$content								string
	 *
	 * @return		string
	 */
	static function htmlI($content){
		return we_baseElement::getHtmlCode(new we_baseElement('i', true, array(), $content));
	}

	/**
	 * Function generates html code for html u element
	 *
	 * @param		$content								string
	 *
	 * @return		string
	 */
	static function htmlU($content){
		return we_baseElement::getHtmlCode(new we_baseElement('u', true, array(), $content));
	}

	/**
	 * Function generates html code for html image element
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	static function htmlImg($attribs=array()){
		return we_baseElement::getHtmlCode(new we_baseElement('img', false, $attribs));
	}

	/**
	 * Function generates html code for html body element
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	static function htmlBody($attribs=array(), $content=''){
		if(!isset($attribs['style'])){
			$attribs['style'] = 'margin: 0px 0px 0px 0px;';
		} else if(strstr($attribs['style'], 'margin') === FALSE){
			$attribs['style'].=';margin: 0px 0px 0px 0px;';
		}

		return "\n" . we_baseElement::getHtmlCode(new we_baseElement('body', true, $attribs, "\n" . $content . "\n"));
	}

	/**
	 * Function generates html code for html label element
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	static function htmlLabel($attribs=array(), $content=''){
		return we_baseElement::getHtmlCode(new we_baseElement('label', true, $attribs, $content));
	}

	/**
	 * Function generates html code for html hidden element
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	static function htmlHidden($attribs=array()){
		$attribs['type'] = 'hidden';
		return we_baseElement::getHtmlCode(new we_baseElement('input', false, $attribs));
	}

	/**
	 * Function generates html code for html a element
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	static function htmlA($attribs=array(), $content=''){
		return we_baseElement::getHtmlCode(new we_baseElement('a', true, $attribs, $content));
	}

	/**
	 * Function generates html code for html br element
	 *
	 * @return		string
	 */
	static function htmlBr(){
		static $br = 0;
		$br = ($br ? $br : we_baseElement::getHtmlCode(new we_baseElement('br', false)));
		return $br;
	}

	/**
	 * Function generates html code for html nobr element
	 *
	 * @return		string
	 */
	static function htmlNobr($content=''){
		return we_baseElement::getHtmlCode(new we_baseElement('nobr', true, array(), $content));
	}

	/**
	 * Function generates html code for html br element
	 *
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	static function htmlComment($content){
		return we_baseElement::getHtmlCode(new we_baseElement('!-- ' . $content . ' --', false));
	}

	/**
	 *
	 */
	static function htmlDocType($version='4Trans'){
		switch($version){
			case 5:
			case '5':
				return '< !DOCTYPE html>';
			case '4Trans':
			default:
				return '<!DOCTYPE  HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">';
		}
	}

	/**
	 * Function generates html code for html document
	 *
	 * @return		string
	 */
	static function htmlHtml($content){
		return self::htmlDocType() . we_baseElement::getHtmlCode(new we_baseElement('html', true, array(), $content));
	}

	/**
	 * Function generates html code for document head
	 *
	 * @return		string
	 */
	static function htmlHead($content){
		return we_baseElement::getHtmlCode(new we_baseElement('head', true, array(), $content));
	}

	static function htmlMeta($attribs = array()){

		return we_baseElement::getHtmlCode(new we_baseElement('meta', false, $attribs));
	}

	static function htmlTitle($content){
		return we_baseElement::getHtmlCode(new we_baseElement('title', true, array(), $content));
	}

	/**
	 * Function generates html code for textarea tag
	 *
	 * @return		string
	 */
	static function htmlTextArea($attribs=array(), $content=''){
		return we_baseElement::getHtmlCode(new we_baseElement('textarea', true, $attribs, $content));
	}

	/**
	 * Function generates html code for p tag
	 *
	 * @return		string
	 */
	static function htmlP($attribs = array(), $content=''){
		return we_baseElement::getHtmlCode(new we_baseElement('p', true, $attribs, $content));
	}

	/**
	 * Function generates html code for center tag
	 *
	 * @return		string
	 */
	static function htmlCenter($content){
		return we_baseElement::getHtmlCode(new we_baseElement('center', true, array(), $content));
	}

	/**
	 * Function generates html code for center tag
	 *
	 * @return		string
	 */
	static function htmlApplet($attribs = array(), $content=''){
		return we_baseElement::getHtmlCode(new we_baseElement('applet', true, $attribs, $content));
	}

	/**
	 * Function generates html code for center tag
	 *
	 * @return		string
	 */
	static function htmlParam($attribs = array()){
		return we_baseElement::getHtmlCode(new we_baseElement('param', false, $attribs));
	}

}
