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
	//simple button with icon
	const WE_FA_BUTTON_IDENTIFY = 'fa';
	//button with an icon that is composed of more icons
	const WE_FASTACK_BUTTON_IDENTIFY = 'fas';
	//button with icon & text
	const WE_FATEXT_BUTTON_IDENTIFY = 'fat';
	const WE_FORM = 'form';
	const WE_JS_BUTTON_IDENTIFY = 'javascript';
	const ADD = 'fa:add,fa-lg fa-plus';
	const BACK = 'fat:back,fa-lg fa-step-backward';
	const CALENDAR = 'fa:date_picker,fa-lg fa-calendar';
	const CANCEL = 'fat:cancel,fa-lg fa-ban fa-cancel';
	const CLOSE = 'fat:close,fa-lg fa-close fa-cancel';
	const DELETE = 'fa:delete,fa-lg fa-trash-o';
	const DELETE_ALL = 'fa:delete_all,fa-lg fa-database,fa-lg fa-trash-o';
	const DELETE_EQUAL = 'fa:delete_equal,fa-lg fa-trash-o, fa-lg fa-exchange';
	const DIRDOWN = 'fa:btn_direction_down,fa-lg fa-caret-down';
	const DIRRIGHT = 'fa:btn_direction_right,fa-lg fa-caret-right';
	const DIRUP = 'fa:btn_direction_up,fa-lg fa-caret-up';
	const EDIT = 'fa:btn_edit,fa-lg fa-edit';
	const EXPORT = 'fat:export,fa-lg fa-download';
	const MAKE_PREVIEW = 'fa:make_preview,fa-lg fa-refresh, fa-lg fa-search';
	const NEXT = 'fat:next,fa-lg fa-step-forward';
	const NO = 'fat:no,fa-lg fa-close fa-cancel';
	const NOT_FOUND = '.fa-lg fa-bullseye';
	const OK = 'fat:ok,fa-lg fa-check fa-ok';
	const PLUS = 'fa:btn_function_plus,fa-lg fa-plus';
	const PREVIEW = 'fa:preview,fa-lg fa-eye';
	const PUBLISH = 'fat:publish,fa-lg fa-globe';
	const REFRESH = 'fat:refresh,fa-lg fa-refresh';
	const RELOAD = 'fa:btn_function_reload,fa-lg fa-refresh';
	const RESET_VERSION = 'fa:reset,fa-lg fa-history';
	const SAVE = 'fat:save,fa-lg fa-save';
	const SEARCH = 'fa:btn_function_search,fa-lg fa-search';
	const SELECT = 'fa:select,fa-lg fa-hand-o-right,fa-lg fa-file-o';
	const SEND = 'fat:send,fa-lg fa-send-o';
	const TOGGLE = 'fa:selectAll,fa-lg fa-check-square-o,fa-lg fa-square-o';
	const TRASH = 'fa:btn_function_trash,fa-lg fa-trash-o';
	const UNLOCK = 'fa:btn_function_unlock,fa-lg fa-unlock';
	const UPLOAD = 'fat:upload,fa-lg fa-upload';
	const VIEW = 'fa:btn_function_view,fa-lg fa-eye';
	const YES = 'fat:yes,fa-lg fa-check fa-ok';

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
	static function getButton($text, $id, $cmd = '', $title = '', $disabled = false, $isFormButton = false, $class = '', array $dimensions = [], $htmlName = ""){
		// $dimensions: to be used only when calling this function from app
		$style = isset($dimensions['width']) || isset($dimensions['height']) ? ' style="' . (isset($dimensions['width']) ? 'width:' . $dimensions['width'] . 'px;' : '') . (isset($dimensions['height']) ? 'height:' . $dimensions['height'] . 'px;' : '') . '"' : '';

		return '<button type="' . ($isFormButton ? 'submit' : 'button') . '" ' . ($title ? ' title="' . oldHtmlspecialchars($title) . '"' : '') .
			($disabled ? ' disabled="disabled"' : '') .
			$style .
			($htmlName ? ' name="' . $htmlName . '"' : ' id="' . $id . '"') .
			' class="weBtn' . ($class ? ' ' . $class : '') . '" ' .
			' onclick="' . oldHtmlspecialchars($cmd) . '"' .
			'>' . $text . '</button>';
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
	static function create_button($name, $href, $htmlName = '', $unused = 0, $unused2 = 0, $on_click = '', $target = '', $disabled = false, $uniqid = true, $suffix = '', $opensDialog = false, $title = '', $class = '', $id = '', $notTranslate = false, array $dimensions = [
	]){
		$cmd = '';

		if($htmlName === true){
			$htmlName = '';
			t_e("we_html_button::create_button: $htmlName = true => change to ''");
		}

		restart:
		$all = explode(':', $name, 2);
		list($type, $names) = count($all) == 1 ? ['', ''] : $all;
		// Check if the button is a text button or an image button
		$value = '';
		if($on_click){
			$on_click = ltrim($on_click, ';') . ';';
			if(!$href){
				$href = self::WE_JS_BUTTON_IDENTIFY . ':' . $on_click;
				$on_click = '';
			}
		}
		$hasIcon = false;
		switch($type){
			case self::WE_FASTACK_BUTTON_IDENTIFY://fixme: add stack class
				$hasIcon = true;
				//get name for title
				list($name, $names) = explode(',', $names, 2);
				$fas = explode(',', $names);
				$value = '<span class="fa-stack">';
				foreach($fas as $cnt => $fa){
					$value .= '<i class="fa ' . ($cnt == 0 ? 'fa-stack-2x ' : 'fa-stack-1x ') . $fa . '"></i>';
				}
				$value .= '</span>';
				$class .= ' weIconButton';
				break;
			case self::WE_FATEXT_BUTTON_IDENTIFY:
				$hasIcon = true;
				$class .= ' weIconTextButton';
			case self::WE_FA_BUTTON_IDENTIFY:
				$hasIcon = true;
				//get name for title
				list($name, $names) = explode(',', $names, 2);
				$fas = explode(',', $names);
				if(count($fas) > 1){
					$class .= ' multiicon';
				}
				$value = '';
				foreach($fas as $cnt => $fa){
					$value .= '<i class="fa ' . ($cnt > 0 ? 'fa-moreicon ' : 'fa-firsticon ') . $fa . '"></i>';
				}
				if($type == self::WE_FA_BUTTON_IDENTIFY){
					$class .= ' weIconButton';
					break;
				}
			//add text, no break;
			default:
				$text = $notTranslate ? $name : g_l('button', '[' . $name . '][value]') . ($opensDialog ? '&hellip;' : '');
				if($hasIcon){ // we need this, since text is stripped in mobile view
					$value = ($name == 'next' ? '<span class="text">' . $text . ' </span> ' . $value : $value . '<span class="text">' . $text . '</span>');
				} else {
					$value = $text;
				}
		}
		$hrefData = explode(':', $href, 2);

		switch($hrefData[0]){
			case self::WE_FORM:
				$form_name = $hrefData[1];
				$cmd = 'if (document.' . $form_name . '.onsubmit===undefined||document.' . $form_name . '.onsubmit===null||document.' . $form_name . '.onsubmit()) {' . $on_click . ' document.' . $form_name . '.submit(); } return false;';
				break;
			case self::WE_JS_BUTTON_IDENTIFY:
				// Get content of JavaScript
				$cmd = $on_click . $hrefData[1];
				break;
			default:
				// Check if the link has to be opened in a different frame or in a new window
				$cmd = $on_click . ($href ? "window.location.href='" . $href . "';" : ''); /* ); */
		}

		return self::getButton($value, ($id ?: ($uniqid ? 'we' . $name . '_' . md5(uniqid(__FUNCTION__, true)) : $name) . $suffix), $cmd, ($title ?: (g_l('button', '[' . $name . '][alt]', true) ?: '')), $disabled, $hrefData[0] === self::WE_FORM, $class, $dimensions, $htmlName);
	}

	static function formatButtons($buttons){
		return '<div style="float:right">' . $buttons . '</div>';
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
	static function position_yes_no_cancel($yes_button, $no_button = '', $cancel_button = '', $gap = 10, $align = '', $attribs = [], $aligngap = '0px'){
		//	Create default attributes for table
		$align = /* $align ? 'right' : */ 'right';
		$attr = ['style' => 'padding:0px ' . ($align === 'right' ? $aligngap : '0px') . ' 0px ' . ($align === 'left' ? $aligngap : '0px') . ';border-spacing:0px;float:' . $align . ';'
		];

		if(is_array($attribs) && !empty($attribs)){
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
