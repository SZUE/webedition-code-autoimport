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

/**
 * Filename:    we_html_element.inc.php
 *
 * Function:    Class to create html tags
 *
 * Description: Provides functions for creating html tags
 */
abstract class we_html_element{

	/**
	 * Function generates html code for html form
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	public static function htmlForm($attribs = array(), $content = ''){

		if(!isset($attribs['name'])){
			$attribs['name'] = 'we_form';
		}
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('form', true, $attribs, $content));
	}

	/**
	 * Function generates html code for html input element
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	public static function htmlInput(array $attribs = array()){

		if(!isset($attribs['class'])){
			$attribs['class'] = 'defaultfont';
		}
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('input', 'selfclose', $attribs));
	}

	/**
	 * Function generates html code for html radio-checkbox input element
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	public static function htmlRadioCheckbox(array $attribs = array()){
		$attribs['type'] = 'checkbox';

		$table = new we_html_table(array('class' => 'default'), 1, 2);
		$table->setCol(0, 0, array('style' => "padding-left:2px;"), self::htmlInput($attribs));
		$table->setColContent(0, 1, self::htmlLabel(array('for' => $name, 'title' => sprintf(g_l('htmlForms', '[click_here]'), $attribs['title']), $attribs['title'])));

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
	public static function cssElement($content = '', array $attribs = array()){
		$attribs['type'] = 'text/css';

		return we_html_baseElement::getHtmlCode(new we_html_baseElement('style', true, $attribs, $content));
	}

	public static function jsScript($name, $onload = '', $attribs = array()){
		$attribs['src'] = self::getUnCache($name);
		if($onload){
			$attribs['onload'] = $onload;
		}
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('script', true, $attribs));
	}

	/**
	 * Function generates js code
	 *
	 * @param		$content								string			(optional)
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	public static function jsElement($content = '', array $attribs = array()){
		if(strpos($content, '<!--') === FALSE){
			$content = "<!--\n" . trim($content, " \n") . "\n//-->\n";
		}
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('script', true, $attribs, $content));
	}

	public static function cssLink($url, array $attribs = array()){
		$attribs['href'] = self::getUnCache($url);
		$attribs['rel'] = 'styleSheet';
		$attribs['type'] = 'text/css';
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('link', false, $attribs));
	}

	/**
	 * Function generates link code
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	public static function linkElement(array $attribs = array()){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('link', 'selfclose', $attribs));
	}

	/**
	 * Function generates html code for html div elements
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	public static function htmlSpan(array $attribs = array(), $content = ''){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('span', true, $attribs, $content));
	}

	//FIMXE: remove for htmltools
	public static function htmlSelect(array $attribs = array(), $content = ''){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('select', true, $attribs, $content));
	}

	/**
	 * Function generates html code for html div elements
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	public static function htmlDiv(array $attribs = array(), $content = ''){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('div', true, $attribs, $content));
	}

	/**
	 * Function generates html code for html b element
	 *
	 * @param		$content								string
	 *
	 * @return		string
	 */
	public static function htmlB($content){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('span', true, array('style' => 'font-weight:bold'), $content));
	}

	/**
	 * Function generates html code for html i element
	 *
	 * @param		$content								string
	 *
	 * @return		string
	 */
	/* 	public static function htmlI($content){
	  return we_baseElement::getHtmlCode(new we_baseElement('i', true, array(), $content));
	  } */

	/**
	 * Function generates html code for html u element
	 *
	 * @param		$content								string
	 *
	 * @return		string
	 */
	/* public static function htmlU($content){
	  return we_baseElement::getHtmlCode(new we_baseElement('u', true, array(), $content));
	  } */

	/**
	 * Function generates html code for html image element
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	public static function htmlImg(array $attribs = array()){
		//if no alt is set, set dummy alt
		if(!isset($attribs['alt'])){
			$attribs['alt'] = '-';
		}
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('img', 'selfclose', $attribs));
	}

	/**
	 * Function generates html code for html body element
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	public static function htmlBody(array $attribs = array(), $content = ''){
		$body = new we_html_baseElement('body', true, $attribs, $content);
		return $body->getHTML();
	}

	/**
	 * Function generates html code for html label element
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	public static function htmlLabel(array $attribs = array(), $content = ''){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('label', true, $attribs, $content));
	}

	/**
	 * Function generates html code for html hidden element
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	public static function htmlHidden($name, $value, $id = ''){
		$attribs = array(
			'type' => 'hidden',
			'name' => $name,
			'value' => strpos($value, '"') !== false ? oldHtmlspecialchars($value) : $value
		);
		if($id){
			$attribs['id'] = $id;
		}
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('input', 'selfclose', $attribs));
	}

	public static function htmlHiddens(array $vals){
		$ret = '';
		foreach($vals as $key => $value){
			if($key){
				$ret.=we_html_baseElement::getHtmlCode(new we_html_baseElement('input', 'selfclose', array(
							'name' => $key,
							'value' => strpos($value, '"') !== false ? oldHtmlspecialchars($value) : $value,
							'type' => 'hidden'
				)));
			}
		}
		return $ret;
	}

	/**
	 * Function generates html code for html a element
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	public static function htmlA(array $attribs = array(), $content = ''){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('a', true, $attribs, $content));
	}

	/**
	 * Function generates html code for html br element
	 *
	 * @return		string
	 */
	public static function htmlBr(){
		static $br = 0;
		$br = ($br ? : we_html_baseElement::getHtmlCode(new we_html_baseElement('br', 'selfclose')));
		return $br;
	}

	/**
	 * Function generates html code for html nobr element
	 *
	 * @return		string
	 */
	public static function htmlNobr($content = ''){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('nobr', true, array(), $content));
	}

	/**
	 * Function generates html code for html br element
	 *
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	public static function htmlComment($content){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('!-- ' . $content . ' --', false));
	}

	/**
	 *
	 */
	public static function htmlDocType($version = '4Trans'){
		switch($version){
			default:
			case 5:
			case '5':
				return '<!DOCTYPE html>';
			case '4Trans':
				return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
		}
	}

	/**
	 * Function generates html code for html document
	 *
	 * @return		string
	 */
	public static function htmlHtml($content, $close = true){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('html', $close, array(), $content));
	}

	/**
	 * Function generates html code for document head
	 *
	 * @return		string
	 */
	public static function htmlHead($content, $close = true){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('head', $close, array(), $content));
	}

	public static function htmlMeta(array $attribs = array()){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('meta', 'selfclose', $attribs));
	}

	public static function htmlTitle($content){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('title', true, array(), $content));
	}

	/**
	 * Function generates html code for textarea tag
	 *
	 * @return		string
	 */
	public static function htmlTextArea(array $attribs = array(), $content = ''){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('textarea', true, $attribs, $content));
	}

	/**
	 * Function generates html code for p tag
	 *
	 * @return		string
	 */
	public static function htmlP(array $attribs = array(), $content = ''){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('p', true, $attribs, $content));
	}

	/**
	 * Function generates html code for center tag
	 *
	 * @return		string
	 */
	public static function htmlCenter($content){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('center', true, array(), $content));
	}

	/**
	 * Function generates html code for center tag
	 *
	 * @return		string
	 */
	public static function htmlApplet(array $attribs = array(), $content = '', array $params = array()){
		//$params['cache_archive'] = $attribs['archive']; // Applet seams not to like this param
		$params['cache_version'] = WE_VERSION;
		$params['type'] = 'application/x-java-applet;jpi-version=1.6.0';
		$params['scriptable'] = 'true';
		$params['mayscript'] = 'true';
		$tmp = '';
		foreach($params as $key => $value){
			$tmp.=we_html_element::htmlParam(array("name" => $key, "value" => $value));
		}
		$content = $tmp . $content;
		$attribs['MAYSCRIPT'] = '';
		$attribs['SCRIPTABLE'] = '';


		return we_html_baseElement::getHtmlCode(new we_html_baseElement('applet', true, $attribs, $content));
	}

	/**
	 * Function generates html code for center tag
	 *
	 * @return		string
	 */
	public static function htmlParam(array $attribs = array()){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('param', 'selfclose', $attribs));
	}

	/**
	 * this func is used to get a parameter variing in each version, to get the latest file (browser cache)
	 * but don't offer information about the current installed version!
	 * @staticvar string $cache saves current cached path
	 * @param string $url url to add the version-unique param
	 * @return string resulting url
	 */
	private static function getUnCache($url){
		static $cache = -1;
		if($cache == -1){
			$cache = md5(WE_VERSION . filemtime(WE_INCLUDES_PATH . 'we_version.php') . __FILE__);
		}
		return $url . (strstr($url, '?') ? '&amp;' : '?') . $cache;
	}

	public static function htmlIFrame($name, $src, $style = '', $iframestyle = '', $onload = '', $scroll = true, $class = ''){
		static $isApple = -1;
		$isApple = ($isApple !== -1 ? $isApple : we_base_browserDetect::inst()->getBrowser() == we_base_browserDetect::APPLE);
		$iframestyle = $iframestyle ? : 'border:0px;width:100%;height:100%;overflow: ' . (false && we_base_browserDetect::isFF() ? 'auto' : 'hidden') . ';';

		return self::htmlDiv(array('style' => $style, 'name' => $name . 'Div', 'id' => $name . 'Div', 'class' => $class)
						, we_html_baseElement::getHtmlCode(new we_html_baseElement('iframe', true, array('name' => $name, 'id' => $name, 'frameBorder' => 0, 'src' => $src, 'style' => $iframestyle, 'onload' => 'try{' . ($scroll ? 'this.contentDocument.body.style.overflow=\'' . ($isApple ? 'scroll !important' : 'auto') . '\';' . ($isApple ? 'this.contentDocument.body.style[\'-webkit-overflow-scrolling\']=\'touch !important\';' : '') : 'this.contentDocument.body.style.overflow=\'hidden\';') . '}catch(e){}' . $onload))
		));
	}

	public static function htmlExIFrame($__name, $__src, $__style = '', $class = ''){
		if(strpos($__src, $_SERVER['DOCUMENT_ROOT']) === 0){
			ob_start();
			include $__src;
			$tmp = ob_get_clean();
		} else {
			$tmp = $__src;
		}
		return self::htmlDiv(array('style' => $__style, 'name' => $__name . 'Div', 'id' => $__name . 'Div', 'class' => $class), $tmp);
	}

}
