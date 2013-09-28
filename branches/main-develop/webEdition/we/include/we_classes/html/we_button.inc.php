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
 * Class we_button
 *
 * Provides functions for creating webEdition buttons.
 */
//fIXME: this should be abstract - but liveupdate currently fails with this
class we_button{

	const HEIGHT = 22;
	const WIDTH = 100;
	const WE_IMAGE_BUTTON_IDENTIFY = 'image:';
	const WE_FORM_BUTTON_IDENTIFY = 'form:';
	const WE_SUBMIT_BUTTON_IDENTIFY = 'submit:';
	const WE_JS_BUTTON_IDENTIFY = 'javascript:';

	/**
	 * Gets the HTML Code for the button.
	 * @return string
	 * @param string $value
	 * @param string $id
	 * @param string $cmd
	 * @param integer $width
	 * @param string $title
	 * @param boolean $disabled
	 * @param string $margin
	 * @param string $padding
	 * @param string $key
	 * @param string $float
	 * @param string $display
	 * @param boolean $important
	 * @static
	 */
	static function getButton($value, $id, $cmd = '', $width = self::WIDTH, $title = '', $disabled = false, $margin = '', $padding = '', $key = '', $float = '', $display = '', $important = true, $isFormButton = false, $cssInline = false){
		return '<table  ' . ($title ? ' title="' . oldHtmlspecialchars($title) . '"' : '') .
			' id="' . $id . '" class="weBtn' . ($disabled ? 'Disabled' : '') .
			'"' . self::getInlineStyleByParam($width, '', $float, $margin, $padding, $display, '', $important) .
			' onmouseout="weButton.out(this);" onmousedown="weButton.down(this);" onmouseup="if(weButton.up(this)){' . oldHtmlspecialchars($cmd) . ';}">' .
			'<tr><td class="weBtnLeft' . ($disabled ? 'Disabled' : '') . '" ></td>' .
			'<td class="weBtnMiddle' . ($disabled ? 'Disabled' : '') . '">' . $value . '</td>' .
			'<td class="weBtnRight' . ($disabled ? 'Disabled' : '') . '">' . ($isFormButton ? we_html_tools::getPixel(1, 1) : '') . '</td>' .
			'</tr></table>';
	}

	/**
	 * Gets the style attribut for using in the elements HTML.
	 *
	 * @return string
	 * @param integer $width
	 * @param integer $height
	 * @param string $margin
	 * @param string $padding
	 * @param string $display
	 * @param string $extrastyle
	 * @param boolean $important
	 */
	static function getInlineStyleByParam($width = '', $height = '', $float = '', $margin = '', $padding = '', $display = '', $extrastyle = '', $important = true, $clear = ''){

		$_imp = $important ? ' ! important' : '';

		return ' style="border-style:none; padding:0px;border-spacing:0px;' . ($width ? 'width:' . $width . 'px' . $_imp . ';' : '') .
			($height ? 'height:' . $height . 'px' . $_imp . ';' : '') .
			($float ? 'float:' . $float . $_imp . ';' : '') .
			($clear ? 'clear:' . $clear . $_imp . ';' : '') .
			($margin ? 'margin:' . $margin . $_imp . ';' : '') .
			($display ? 'display:' . $display . $_imp . ';' : '') .
			($padding ? 'padding:' . $padding . $_imp . ';' : '') .
			$extrastyle . '"';
	}

	/*	 * ***********************************************************************
	 * FUNCTIONS
	 * *********************************************************************** */

	/**
	 * This function creates the JavaScript that switches the state of a button.
	 *
	 * @param      $standalone                             bool                (optional)
	 *
	 * @return     string
	 */
	static function create_state_changer($standalone = true){
		// Build the main preload function
		/**
		 * This functions switches the state of a text(!!!) button.
		 *
		 * @param      element                                 string
		 * @param      button                                  string
		 * @param      state                                   string
		 * @param      type                                    bool                (optional)
		 *
		 * @return     button                                  bool
		 */
		$_JavaScript_functions = '
function switch_button_state(element, button, state, type) {
	if (state == "enabled") {
		weButton.enable(element);
		return true;
	} else if (state == "disabled") {
		weButton.disable(element);
	}

	return false;
}';

		// Build string to be returned by the function

		return ($standalone ? we_html_element::jsElement($_JavaScript_functions) : $_JavaScript_functions);
	}

	/**
	 * This functions creates the button.
	 *
	 * @param      $name                                   string
	 * @param      $href                                   string
	 * @param      $alt                                    bool                (optional)
	 * @param      $width                                  int                 (optional)
	 * @param      $height                                 int                 (optional)
	 * @param      $on_click                               string              (optional)
	 * @param      $target                                 string              (optional)
	 * @param      $disabled                               bool                (optional)
	 * @param      $uniqid                                 bool                (optional)
	 * @param      $suffix                                 string              (optional)
	 *
	 * @return     string
	 */
	static function create_button($name, $href, $alt = true, $width = self::WIDTH, $height = self::HEIGHT, $on_click = '', $target = '', $disabled = false, $uniqid = true, $suffix = '', $opensDialog = false){

		$cmd = '';
		// Initialize variable for Form:Submit behaviour
		$_add_form_submit_dummy = false;

		/**
		 * CHECK DEFAULTS
		 */
		// Check width
		$width = ($width == -1 ? self::WIDTH : $width);

		// Check height
		$height = ($height == -1 ? self::HEIGHT : $height);

		/**
		 * DEFINE THE NAME OF THE BUTTON
		 */
		// Check if the button is a text button or an image button
		if(strpos($name, self::WE_IMAGE_BUTTON_IDENTIFY) === false){ // Button is NOT an image
			$_button_name = ($uniqid ? 'we' . $name . '_' . md5(uniqid(__FUNCTION__, true)) : $name) . $suffix;
		} else { // Button is an image - create a unique name
			$_button_pure_name = substr($name, (strpos($name, self::WE_IMAGE_BUTTON_IDENTIFY) + strlen(self::WE_IMAGE_BUTTON_IDENTIFY)));
			$_button_name = ($uniqid ? 'we' . substr($name, (strpos($name, self::WE_IMAGE_BUTTON_IDENTIFY) + strlen(self::WE_IMAGE_BUTTON_IDENTIFY))) . '_' . md5(uniqid(__FUNCTION__, true)) : substr($name, (strpos($name, self::WE_IMAGE_BUTTON_IDENTIFY) + strlen(self::WE_IMAGE_BUTTON_IDENTIFY))) . $suffix);
		}
		/**
		 * CHECK IF THE LANGUAGE FILE DEFINES ANOTHER WIDTH FOR THE BUTTON
		 */
		// Check if the button will a text button or a image button
		if(strpos($name, self::WE_IMAGE_BUTTON_IDENTIFY) === false){ // Button will NOT be an image
			$tmp = g_l('button', '[' . $name . '][width]', true);
			if(!empty($tmp) && ($width == self::WIDTH)){
				$width = $tmp;
			}
		} else {
			//set width for image button if given width has not default value
			$width = ($width == self::WIDTH ? 0 : $width);
		}

		// Check if the button will be used in a form or not
		if(strpos($href, self::WE_FORM_BUTTON_IDENTIFY) === false){ // Button will NOT be used in a form
			// Check if the buttons target will be a JavaScript
			if(strpos($href, self::WE_JS_BUTTON_IDENTIFY) === false){ // Buttons target will NOT be a JavaScript
				// Check if the link has to be opened in a different frame or in a new window
				$_button_link = ($target ? // The link will be opened in a different frame or in a new window
						// Check if the link has to be opend in a frame or a window
						($target == '_blank' ? // The link will be opened in a new window
							"window.open('" . $href . "', '" . $target . "');" :
							// The link will be opened in a different frame
							"target_frame = eval('parent.' + " . $target . ");target_frame.location.href='" . $href . "';") :
						// The link will be opened in the current frame or window
						"window.location.href='" . $href . "';");

				// Now assign the link string
				$cmd .= $_button_link;
			} else { // Buttons target will be a JavaScript
				// Get content of JavaScript
				$_javascript_content = substr($href, (strpos($href, self::WE_JS_BUTTON_IDENTIFY) + strlen(self::WE_JS_BUTTON_IDENTIFY)));

				// Render link
				$cmd .= $_javascript_content;
			}
		} else { // Button will be used in a form
			// Check if the button shall call the onSubmit event
			if(strpos($href, self::WE_SUBMIT_BUTTON_IDENTIFY) === false){ // Button shall not call the onSubmit event
				// Get name of form
				$_form_name = substr($href, (strpos($href, self::WE_FORM_BUTTON_IDENTIFY) + strlen(self::WE_FORM_BUTTON_IDENTIFY)));

				// Render link
				$cmd .= 'document.' . $_form_name . '.submit();return false;';
			} else { // Button must call the onSubmit event
				// Set variable for Form:Submit behaviour
				$_add_form_submit_dummy = true;

				// Get name of form
				$_form_name = substr($href, (strpos($href, self::WE_SUBMIT_BUTTON_IDENTIFY) + strlen(self::WE_SUBMIT_BUTTON_IDENTIFY)));

				// Render link
				$cmd .= 'if (document.' . $_form_name . '.onsubmit()) { document.' . $_form_name . '.submit(); } return false;';
			}
		}

		$value = (strpos($name, self::WE_IMAGE_BUTTON_IDENTIFY) === false) ? g_l('button', '[' . $name . '][value]') . ($opensDialog ? '&hellip;' : '') :
			we_html_element::htmlImg(array('src' => BUTTONS_DIR . 'icons/' . str_replace('btn_', '', $_button_pure_name) . '.gif', 'class' => 'weBtnImage'));

		$title = '';
		// Check if the button will a text button or an image button
		if(strpos($name, self::WE_IMAGE_BUTTON_IDENTIFY) === false){ // Button will NOT be an image
			$tmp = g_l('button', '[' . $name . '][alt]');
			if(!empty($tmp) && $alt){
				$title = $tmp;
			}
		} else {
			$tmp = g_l('button', '[' . $_button_pure_name . '][alt]', true);
			//ignore missing alt attribute
			if(!empty($tmp) && $alt){
				$title = $tmp;
			}
		}
		return self::getButton($value, $_button_name, $cmd, $width, $title, $disabled, '', '', '', '', '', true, (strpos($href, self::WE_FORM_BUTTON_IDENTIFY) !== false));
	}

	/**
	 * This function creates a table with a bunch of buttons.
	 *
	 * @param      $buttons                                array
	 * @param      $gap                                    int                 (optional)
	 * @param      $attribs                                array               (optional)
	 *
	 * @see        create_button()
	 * @see        we_html_table::we_html_table()
	 * @see        we_html_table::setCol()
	 * @see        we_html_table::getHtml()
	 *
	 * @return     string
	 */
	static function create_button_table($buttons, $gap = 10, $attribs = ''){
		// Get number of buttons
		$_count_button = count($buttons);

		// Create array for table attributes
		$attr = array('style' => 'border-style:none; padding:0px;border-spacing:0px;');

		// Check for attribute parameters
		if($attribs && is_array($attribs)){
			foreach($attribs as $k => $v){
				$attr[$k] = $v;
			}
		}

		// Create table
		$_button_table = new we_html_table($attr, 1, $_count_button);

		// Build cols for every button
		foreach($buttons as $i => $button){
			$_button_table->setCol(0, $i, array('class' => 'weEditmodeStyle', 'style' => ( $i < $_count_button - 1 ? 'padding-right:' . $gap . 'px' : '')), $button);
		}

		// Get created HTML
		return $_button_table->getHtml();
	}

	/**
	 * This function displays ok, no, cancel - buttons matching to the OS
	 * and places them at the right ($align) side
	 *
	 * For Mac OS         : NO, CANCEL, YES
	 * For Windows & Linux: OK, NO, CANVCEL
	 *
	 * @param      $yes_button                             string
	 * @param      $no_button                              string              (optional)
	 * @param      $cancel_button                          string              (optional)
	 * @param      $gap                                    int                 (optional)
	 * @param      $align                                  string              (optional)
	 * @param      $attribs                                array               (optional)
	 * @param	   $aligngap                               int                 (optional)
	 *
	 * @see        we_html_table::we_html_table()
	 * @see        we_html_table::setCol()
	 * @see        we_html_table::getHtml()
	 *
	 * @return     string
	 */
	static function position_yes_no_cancel($yes_button, $no_button = null, $cancel_button = null, $gap = 10, $align = '', $attribs = array(), $aligngap = 0){
		//	Create default attributes for table
		$align = empty($align) ? 'right' : $align;
		$attr = array(
			'style' => 'border-style:none; padding-top:0px;padding-bottom:0px;padding-left:' . ($align == 'left' ? $aligngap : 0) . 'px;padding-right:' . ($align == 'right' ? $aligngap : 0) . 'px;border-spacing:0px;',
			'align' => $align,
		);

		// Check for attribute parameters
		if($attribs && is_array($attribs)){
			foreach($attribs as $k => $v){
				$attr[$k] = $v;
			}
		}

		//	Create button array
		$_buttons = array();
		//	button order depends on OS
		$_order = (we_base_browserDetect::isMAC() ? array('no_button', 'cancel_button', 'yes_button') : array('yes_button', 'no_button', 'cancel_button'));

		//	Existing buttons are added to array
		for($_i = 0; $_i < count($_order); $_i++){
			if(isset($$_order[$_i]) && $$_order[$_i] != ''){
				$_buttons[] = $$_order[$_i];
			}
		}

		$_count_button = count($_buttons);

		//	Create_table
		$_button_table = new we_html_table($attr, 1, count($_buttons));

		//	Write buttons
		foreach($_buttons as $i => $button){
			$_button_table->setCol(0, $i, array('class' => 'weEditmodeStyle', 'style' => ( $i < $_count_button - 1 ? 'padding-right:' . $gap . 'px' : '')), $button);
		}

		// Return created HTML
		return $_button_table->getHtml();
	}

}