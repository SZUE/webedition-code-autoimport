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
 * Class we_button
 *
 * Provides functions for creating webEdition buttons.
 */
abstract class we_html_button{
	const HEIGHT = 22;
	const WIDTH = 100;
	const AUTO_WIDTH = -1;
	const WE_IMAGE_BUTTON_IDENTIFY = 'image';
	//simple button with icon
	const WE_FA_BUTTON_IDENTIFY = 'fa';
	//button with an icon that is composed of more icons
	const WE_FASTACK_BUTTON_IDENTIFY = 'fas';
	//button with icon & text
	const WE_FATEXT_BUTTON_IDENTIFY = 'fat';
	const WE_FORM_BUTTON_IDENTIFY = 'form:';
	const WE_SUBMIT_BUTTON_IDENTIFY = 'submit:';
	const WE_JS_BUTTON_IDENTIFY = 'javascript:';
	const DELETE_ALL = 'fa:delete_all,fa-lg fa-database,fa-lg fa-trash-o';
	const DELETE = 'fa:delete,fa-lg fa-trash-o';
	const ADD = 'fa:add,fa-lg fa-plus';
	const EDIT = 'fa:btn_edit,fa-lg fa-edit';
	const TRASH = 'fa:btn_function_trash,fa-lg fa-trash-o';
	const PLUS = 'fa:btn_function_plus,fa-lg fa-plus';
	const DIRUP = 'fa:btn_direction_up,fa-lg fa-caret-up';
	const DIRDOWN = 'fa:btn_direction_down,fa-lg fa-caret-down';
	const DIRRIGHT = 'fa:btn_direction_right,fa-lg fa-caret-right';
	const VIEW = 'fa:btn_function_view,fa-lg fa-eye';
	const RELOAD = 'fa:btn_function_reload,fa-lg fa-refresh';
	const SELECT = 'fa:select,fa-lg fa-hand-o-right,fa-lg fa-file-o';
	const SAVE = 'fat:save,fa-lg fa-save';
	const NEXT = 'fat:next,fa-lg fa-step-forward';
	const BACK = 'fat:back,fa-lg fa-step-backward';
	const REFRESH = 'fat:refresh,fa-lg fa-refresh';
	const SEARCH = 'fa:btn_function_search,fa-lg fa-search';
	const CLOSE = 'fat:close,fa-lg fa-close fa-cancel';
	const CANCEL = 'fat:cancel,fa-lg fa-ban fa-cancel';
	const NO = 'fat:no,fa-lg fa-close fa-cancel';
	const YES = 'fat:yes,fa-lg fa-check fa-ok';
	const OK = 'fat:ok,fa-lg fa-check fa-ok';
	const UPLOAD = 'fat:upload,fa-lg fa-upload';
	const PREVIEW = 'fat:preview,fa-lg fa-eye';
	const CALENDAR = 'fa:date_picker,fa-lg fa-calendar';
	const PUBLISH = 'fat:publish,fa-lg fa-sun-o';
	const NOT_FOUND = '.fa-lg fa-bullseye';

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
	static function getButton($value, $id, $cmd = '', $width = self::WIDTH, $title = '', $disabled = false, $margin = '', $padding = '', $key = '', $float = '', $display = '', $important = true, $isFormButton = false, $class = ''){
		return '<button type="' . ($isFormButton ? 'submit' : 'button') . '" ' . ($title ? ' title="' . oldHtmlspecialchars($title) . '"' : '') .
			($disabled ? ' disabled="disabled"' : '') .
			' id="' . $id . '" class="weBtn' . ($class ? ' ' . $class : '') . '" ' . self::getInlineStyleByParam(($width ? : ($width == self::AUTO_WIDTH ? 0 : self::WIDTH)), '', $float, $margin, $padding, $display, '', $important) .
			' onclick="' . oldHtmlspecialchars($cmd) . '"' .
			'>' . $value . '</button>';
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

		return ' style="' . /* border-style:none; padding:0px;border-spacing:0px;' . */ ($width > 0 ? 'width:' . $width . 'px' . $_imp . ';' : '') .
			($height ? 'height:' . $height . 'px' . $_imp . ';' : '') .
			($float ? 'float:' . $float . $_imp . ';' : '') .
			($clear ? 'clear:' . $clear . $_imp . ';' : '') .
			($margin ? 'margin:' . $margin . $_imp . ';' : '') .
			($display ? 'display:' . $display . $_imp . ';' : '') .
			($padding ? 'padding:' . $padding . $_imp . ';' : '') .
			$extrastyle . '"';
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
	static function create_button($name, $href, $alt = true, $width = self::WIDTH, $height = self::HEIGHT, $on_click = '', $target = '', $disabled = false, $uniqid = true, $suffix = '', $opensDialog = false, $title = '', $class = ''){
		$cmd = '';

		// Check width
		$width = ($width ? : self::WIDTH);

		// Check height
		$height = ($height ? : self::HEIGHT);

		restart:
		$all = explode(':', $name, 2);
		list($type, $names) = count($all) == 1 ? array('', '') : $all;
		/**
		 * DEFINE THE NAME OF THE BUTTON
		 */
		// Check if the button is a text button or an image button
		$value = '';
		if($on_click){
			if($href){
				t_e('onclick on button is not supported');
			} else {
				$href = self::WE_JS_BUTTON_IDENTIFY . $on_click;
			}
		}

		switch($type){
			//FIXME: remove image buttons - we don't have any
			case self::WE_IMAGE_BUTTON_IDENTIFY:// Button is an image
				//fixes for Update
				switch($name){
					case 'image:btn_function_reload':
						$name = self::REFRESH;
						goto restart;
					default:
						t_e('old image button', $name);
						$name = str_replace(self::WE_IMAGE_BUTTON_IDENTIFY, 'fa', $name) . self::NOT_FOUND;
				}
			case self::WE_FASTACK_BUTTON_IDENTIFY://fixme: add stack class
				//set width for image button if given width has not default value
				$width = ($width == self::WIDTH ? self::AUTO_WIDTH : $width);
				//get name for title
				list($name, $names) = explode(',', $names, 2);
				$fas = explode(',', $names);
				$value = '<span class="fa-stack">';
				foreach($fas as $cnt => $fa){
					$value.='<i class="fa ' . ($cnt == 0 ? 'fa-stack-2x ' : 'fa-stack-1x ') . $fa . '"></i>';
				}
				$value.='</span>';
				break;
			case self::WE_FATEXT_BUTTON_IDENTIFY:
			case self::WE_FA_BUTTON_IDENTIFY:
				//set width for image button if given width has not default value
				if($type == self::WE_FA_BUTTON_IDENTIFY){
					$width = ($width == self::WIDTH ? self::AUTO_WIDTH : $width);
				}
				//get name for title
				list($name, $names) = explode(',', $names, 2);
				$fas = explode(',', $names);
				if(count($fas) > 1){
					$class.=' multiicon';
				}
				$value = '';
				foreach($fas as $cnt => $fa){
					$value.='<i class="fa ' . ($cnt > 0 ? 'fa-moreicon ' : 'fa-firsticon ') . $fa . '"></i>';
				}
				if($type == self::WE_FA_BUTTON_IDENTIFY){
					break;
				}
				//add text, no break;
				$value.=' ';
			default:
				if(($width == self::WIDTH) && ($tmp = g_l('button', '[' . $name . '][width]', true))){
					$width = $tmp;
				}
				$text = g_l('button', '[' . $name . '][value]') . ($opensDialog ? '&hellip;' : '');
				$value = ($name == 'next' ? $text . ' ' . $value : $value . $text);
		}

		// Check if the button will be used in a form or not
		if(strpos($href, self::WE_FORM_BUTTON_IDENTIFY) !== false){ // Button will be used in a form
			// Check if the button shall call the onSubmit event
			if(strpos($href, self::WE_SUBMIT_BUTTON_IDENTIFY) !== false){ // Button must call the onSubmit event
				$_form_name = substr($href, strlen(self::WE_SUBMIT_BUTTON_IDENTIFY));
				// Render link
				$cmd .= 'if (document.' . $_form_name . '.onsubmit()) { document.' . $_form_name . '.submit(); } return false;';
			} else {
				// Render link
				$cmd .= 'document.' . substr($href, strlen(self::WE_FORM_BUTTON_IDENTIFY)) . '.submit();return false;';
			}
		} elseif(strpos($href, self::WE_JS_BUTTON_IDENTIFY) !== false){ // Buttons target will be a JavaScript
			// Get content of JavaScript
			$_javascript_content = substr($href, strlen(self::WE_JS_BUTTON_IDENTIFY));

			// Render link
			$cmd .= $_javascript_content;
		} else {
			// Check if the link has to be opened in a different frame or in a new window
			$_button_link = ($target ? // The link will be opened in a different frame or in a new window
					// Check if the link has to be opend in a frame or a window
					($target === '_blank' ? // The link will be opened in a new window
						"window.open('" . $href . "', '" . $target . "');" :
						// The link will be opened in a different frame
						"target_frame = eval('parent.' + " . $target . ");target_frame.location.href='" . $href . "';") :
					// The link will be opened in the current frame or window
					"window.location.href='" . $href . "';");

			// Now assign the link string
			$cmd .= $_button_link;
		}

		return self::getButton($value, ($uniqid ? 'we' . $name . '_' . md5(uniqid(__FUNCTION__, true)) : $name) . $suffix, $cmd, $width, ($alt ? ($title ? : (($tmp = g_l('button', '[' . $name . '][alt]', true)) ? $tmp : '')) : ''), $disabled, '', '', '', '', '', true, (strpos($href, self::WE_FORM_BUTTON_IDENTIFY) !== false), $class);
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
	static function create_button_table($buttons, $attribs = ''){
		if(is_array($attribs)){
			$attr = '';
			foreach($attribs as $k => $v){
				$attr .= ' ' . $k . '="' . $v . '"';
			}
		} else {
			$attr = $attribs;
		}

		//FIMXE: change all calls to this function => remove
		return ($attribs ? '<span ' . $attr . '>' : '') . implode('', $buttons) . ($attribs ? '</span>' : '');
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
	//FIXME: this function is used at many places where yes buttons contains more than one button!!
	static function position_yes_no_cancel($yes_button, $no_button = '', $cancel_button = '', $gap = 10, $align = '', $attribs = array(), $aligngap = 0){
		//	Create default attributes for table
		$align = /* $align ? 'right' : */ 'right';
		$attr = array(
			'style' => 'border-style:none; padding:0 ' . ($align === 'right' ? $aligngap : 0) . ' 0 ' . ($align === 'left' ? $aligngap : 0) . ';border-spacing:0px;float:' . $align . ';'
		);

		if(is_array($attribs) && count($attribs) > 0){
			array_merge($attr, $attribs);
		}


		//	Create button array
		//	button order depends on OS
		$buttons = (we_base_browserDetect::isMAC() ?
				$no_button . $cancel_button . $yes_button :
				$yes_button . $no_button . $cancel_button);


		return we_html_element::htmlDiv($attr, $buttons);
	}

}
