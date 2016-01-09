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
abstract class we_html_tools{

	const OPTGROUP = '<!--we_optgroup-->';
	const TYPE_NONE = 0;
	const TYPE_ALERT = 1;
	const TYPE_INFO = 2;
	const TYPE_QUESTION = 3;

	/** we_html_tools::protect()
	  protects a page. Guests can not see this page */
	static function protect(array $perms = null, $redirect = ''){
		$allow = false;
		if($perms && is_array($perms)){
			foreach($perms as $perm){
				$allow|=permissionhandler::hasPerm($perm);
			}
		} else {
			$allow = true;
		}
		if(!$allow || !isset($_SESSION['user']) || !isset($_SESSION['user']['Username']) || $_SESSION['user']['Username'] == ''){
			self::setHttpCode(401);
			echo self::getHtmlTop() .
			we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', '[perms_no_permissions]'), we_message_reporting::WE_MESSAGE_ERROR) . ($redirect ? 'document.location="' . $redirect . '"' : 'top.close();')) .
			'</head><body>' .
			str_replace('\n', '<br/>', g_l('alert', '[perms_no_permissions]')) .
			'</body></html>';
			exit();
		}
	}

	/**
	 * This function creates a table.
	 *
	 * @param          string                                  $element
	 * @param          string                                  $text
	 * @param          string                                  $textalign          (optional)
	 * @param          string                                  $textclass          (optional)
	 * @param          string                                  $col2               (optional)
	 * @param          string                                  $col3               (optional)
	 * @param          string                                  $col4               (optional)
	 * @param          string                                  $col5               (optional)
	 * @param          string                                  $col6               (optional)
	 * @param          int                                     $abstand            (optional)
	 *
	 * @return         string
	 */
	static function htmlFormElementTable($col1, $text, $textalign = 'left', $textclass = 'defaultfont', $col2 = '', $col3 = '', $col4 = '', $col5 = '', $col6 = '', $abstand = 1){
		$colspan = 0;
		$elemOut = '';

		for($i = 1; $i < 7; ++$i){
//FIXME:remove eval
			$var = ${'col' . $i};
			if($var){
				$tmp = '<td';
				if(is_array($var)){
					foreach($var as $key => $val){
						$key === 'text' ? $colText = $val : $tmp .= ' ' . $key . '=\'' . $val . '\'';
					}
				} else {
					$colText = $var;
				}
				$tmp .= '>' . $colText . '</td>';
				$elemOut.=$tmp;
				$colspan++;
			}
		}
		return '<table class="default">' .
				($text ? '<tr><td class="' . trim($textclass) . '" style="' . ($abstand ? 'margin-bottom:' . $abstand . 'px;' : '') . 'text-align:' . trim($textalign) . ';" colspan="' . $colspan . '">' . $text . '</td></tr>' : '') .
				'<tr>' . $elemOut . '</tr></table>';
	}

	static function targetBox($name, $size, $width = '', $id = '', $value = '', $onChange = '', $abstand = 8, $selectboxWidth = '', $disabled = false){
		$jsvarname = str_replace(array('[', ']'), '_', $name);
		$_inputs = array(
			'class' => 'weSelect',
			'name' => 'sel_' . $name,
			'onfocus' => 'change' . $jsvarname . '=1;',
			'onchange' => "if(this.selectedIndex){
			this.form.elements['" . $name . "'].value = this.options[this.selectedIndex].text;
}
change" . $jsvarname . '=1;
this.selectedIndex = 0;' .
			$onChange,
			'style' => (($selectboxWidth != '') ? ('width: ' . $selectboxWidth . 'px;') : ''),
			'class' => 'defaultfont'
		);

		if($disabled){
			$_inputs['disabled'] = 'true';
		}

		$_target_box = new we_html_select($_inputs, 0);
		$_target_box->addOptions(array(
			'' => '',
			'_top' => '_top',
			'_parent' => '_parent',
			'_self' => '_self',
			'_blank' => '_blank'
		));


		if($width){
			$_inputs['style'] = 'width: ' . $width . 'px;';
		}

		if($id){
			$_inputs['id'] = $id;
		}

		if($value){
			$_inputs['value'] = oldHtmlspecialchars($value);
		}

		if($onChange){
			$_inputs['onchange'] = $onChange;
		}

		return we_html_element::htmlSpan(array('class' => 'defaultfont', 'style' => 'margin-right:' . $abstand . 'px'), self::htmlTextInput($name, $size, $value, '', ($onChange ? 'onchange="' . $onChange . '"' : ''), 'text', $width, 0, '', $disabled)) . $_target_box->getHtml();
	}

	static function htmlTextInput($name, $size = 24, $value = '', $maxlength = '', $attribs = '', $type = 'text', $width = 0, $height = 0, $markHot = '', $disabled = false, $readonly = false){
		$style = ($width || $height) ? (' style="' . ($width ? ('width: ' . $width . (is_numeric($width) ? 'px' : '') . ';') : '') .
				($height ? ('height: ' . $height . (is_numeric($height) ? 'px' : '') . ';') : '') . '"') : '';
		return '<input' . ($markHot ? ' onchange="WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);' . $markHot . '.hot=1;"' : '') .
				(strstr($attribs, "class=") ? "" : ' class="wetextinput"') . ' type="' . trim($type) . '" name="' . trim($name) .
				'" value="' . oldHtmlspecialchars($value) . '"' . ($maxlength ? (' maxlength="' . intval($maxlength) . '"') : '') . ($attribs ? ' ' . $attribs : '') . $style . ($disabled ? (' disabled="true"') : '') . ($readonly ? (' readonly="true"') : '') . ' />';
	}

	static function htmlMessageBox($w, $h, $content, $headline = '', $buttons = ''){
		return '<div style="width:' . $w . 'px;height:' . $h . 'px;background-color:#F7F5F5;border: 2px solid #D7D7D7;padding:20px;">' .
				($headline ? '<h1 class="header">' . $headline . '</h1>' : '') .
				'<div>' . $content . '</div><div style="margin-top:20px;">' . $buttons . '</div></div>';
	}

	static function htmlDialogLayout($content, $headline, $buttons = '', $width = "100%", $marginLeft = 30, $height = "", $overflow = "auto"){
		return we_html_multiIconBox::getHTML('', array(
					array(
						"html" => $content,
						"headline" => ""
					)
						), $marginLeft, ($buttons ? '<div style="text-align:right;margin-left:10px;">' . $buttons . '</div>' : ''), -1, "", "", false, $headline, "", $height, $overflow);
	}

	static function htmlDialogBorder3($w, $h, $content, $headline, $class = "middlefont", $bgColor = "", $buttons = "", $id = "", $style = ""){ //content && headline are arrays
		$anz = count($headline);
		$out = '<table' . ($id ? ' id="' . $id . '"' : '') . ' style="width:' . $w . 'px;' . $style . '" class="default">
		<tr>';
		// HEADLINE
		for($f = 0; $f < $anz; $f++){
			$out .= '<td class="' . $class . ' boxHeader">' . $headline[$f]["dat"] . '</td>';
		}
		$out .= '</tr>';

		//CONTENT
		foreach($content as $c){
			$out .= '<tr>' . self::htmlDialogBorder4Row($c, $class, $bgColor) . '</tr>';
		}
		$out .= '</table>';

		if($buttons){
			$_table = new we_html_table(array('class' => 'default'), 3, 1, array(
				array(array('colspan' => 2), $out),
				array(array('style' => 'text-align:right;margin-top:5px;'), $buttons),
			));
			return $_table->getHtml();
		}
		return $out;
	}

	private static function htmlDialogBorder4Row($content, $class = 'middlefont', $bgColor = ''){
		$anz = count($content);
		$out = '';

		for($f = 0; $f < $anz; $f++){
			$bgcol = $bgColor ? : ((!empty($content[$f]["bgcolor"]) ) ? $content[$f]["bgcolor"] : "white");
			$out .= '<td class="' . $class . '" style="padding:2px 5px 2px 5px;' . (($f == 0) ? '' : "border-left:1px solid silver;" ) . 'border-bottom: 1px solid silver;background-color:' . $bgcol . '; ' .
					(isset($content[$f]["align"]) ? 'text-align:' . $content[$f]["align"] . ';' : '') . ' ' .
					(isset($content[$f]["height"]) ? 'height:' . $content[$f]["height"] . 'px;' : '') . '">' .
					(!empty($content[$f]["dat"]) ? $content[$f]["dat"] : "&nbsp;") .
					'</td>';
		}

		return $out;
	}

	static function htmlDialogBorder4($w, $h, $content, $headline, $class = "middlefont", $bgColor = "", $buttons = "", $id = "", $style = ""){ //content && headline are arrays
		$out = '<table' . ($id ? ' id="' . $id . '"' : '') . 'style="width:' . $w . 'px;' . $style . '" class="default">
		<tr>';
		// HEADLINE
		foreach($headline as $h){
			$out .= '<td class="' . $class . ' boxHeader">' . $h["dat"] . '</td>';
		}
		$out .= '</tr>';

		//CONTENT
		foreach($content as $c){
			$out .= '<tr>' . self::htmlDialogBorder4Row($c, $class, $bgColor) . '</tr>';
		}
		$out .= '</table>';

		if($buttons){
			$_table = new we_html_table(array("class" => 'default'), 3, 1, array(
				array(array("colspan" => 2), $out),
				array(array('style' => 'text-align:right;padding-top:5px;'), $buttons)
			));
			return $_table->getHtml();
		}
		return $out;
	}

	static function html_select($name, $size, $vals, $value = '', array $attribs = array()){
		return self::htmlSelect($name, $vals, $size, $value, false, $attribs, 'value');
	}

	static function htmlSelect($name, array $values, $size = 1, $selectedIndex = '', $multiple = false, array $attribs = array(), $compare = 'value', $width = 0, $cls = 'defaultfont', $oldHtmlspecialchars = true){
		$ret = '';
		$selIndex = is_array($selectedIndex) ? $selectedIndex : makeArrayFromCSV($selectedIndex);
		$optgroup = false;
		foreach($values as $value => $text){
			if($text === self::OPTGROUP || $value === self::OPTGROUP){
				if($optgroup){
					$ret .= '</optgroup>';
				}
				$optgroup = true;
				$ret .= '<optgroup label="' . ($oldHtmlspecialchars ? oldHtmlspecialchars($value) : $value) . '">';
				continue;
			}
			$ret .= '<option value="' . ($oldHtmlspecialchars ? oldHtmlspecialchars($value) : $value) . '"' . (in_array(
							(($compare === "value") ? $value : $text), $selIndex) ? ' selected="selected"' : '') . '>' . ($oldHtmlspecialchars ? oldHtmlspecialchars($text) : $text) . '</option>';
		}
		$ret .= ($optgroup ? '</optgroup>' : '');

		return ($name ? we_html_element::htmlSelect(array_merge(array(
							'class' => 'weSelect ' . $cls,
							'name' => trim($name),
							'size' => abs($size),
							($multiple ? 'multiple' : '') => 'multiple',
							($width ? 'width' : '') => ($width ? : '')
										), $attribs
								), $ret) : $ret);
	}

	//FIXME: make fn more concise and make base all country selects on it
	static function htmlSelectCountry($name = '', $id = '', $size = 1, $selected = array(), $multiple = false, array $attribs = array(), $width = 50, $cls = 'defaultfont', $oldHtmlspecialchars = true, $optsOnly = false){
		$langcode = array_search($GLOBALS['WE_LANGUAGE'], getWELangs());
		$countrycode = array_search($langcode, getWECountries());

		$attributes = array(
			'name' => $name,
			'id' => ($id ? : $name),
			'size' => $size,
			'width' => $width,
			'style' => (isset($attribs['style']) ? $attribs['style'] : ''),
			'class' => 'weSelect ' . $cls
		);
		if($multiple){
			$attributes['multiple'] = 'multiple';
		}
		$countryselect = new we_html_select($attributes);

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
		setlocale(LC_ALL, $langcode . '_' . $countrycode . '.UTF-8');
		asort($topCountries, SORT_LOCALE_STRING);
		asort($shownCountries, SORT_LOCALE_STRING);
		setlocale(LC_ALL, $oldLocale);

		if(WE_COUNTRIES_DEFAULT != ''){
			$countryselect->addOption('--', ($oldHtmlspecialchars ? oldHtmlspecialchars(CheckAndConvertISObackend(WE_COUNTRIES_DEFAULT)) : CheckAndConvertISObackend(WE_COUNTRIES_DEFAULT)));
		}
		foreach($topCountries as $countrykey => &$countryvalue){
			$countryselect->addOption($countrykey, ($oldHtmlspecialchars ? oldHtmlspecialchars(CheckAndConvertISObackend($countryvalue)) : CheckAndConvertISObackend($countryvalue)));
		}
		unset($countryvalue);
		if(!empty($topCountries) && !empty($shownCountries)){
			$countryselect->addOption('-', '----', array('disabled' => 'disabled'));
		}
		foreach($shownCountries as $countrykey => &$countryvalue){
			$countryselect->addOption($countrykey, ($oldHtmlspecialchars ? oldHtmlspecialchars(CheckAndConvertISObackend($countryvalue)) : CheckAndConvertISObackend($countryvalue)));
		}
		unset($countryvalue);

		foreach($selected as $val){
			$countryselect->selectOption($val);
			if(!$multiple){
				break;
			}
		}

		return $optsOnly ? $countryselect->getOptionsArray() : $countryselect->getHtml();
	}

	static function htmlInputChoiceField($name, $value, $values, $atts, $mode, $valuesIsHash = false){
		//  This function replaced we_getChoiceField
		//  we need input="text" and select-box
		//  First input='text'
		$textField = getHtmlTag('input', array_merge($atts, array('type' => 'text', 'name' => $name, 'value' => oldHtmlspecialchars($value))));

		$opts = getHtmlTag('option', array('value' => ''), '', true);
		$attsOpts = array();

		if($valuesIsHash){
			foreach($values as $_val => $_text){
				$attsOpts['value'] = oldHtmlspecialchars($_val);
				$opts .= getHtmlTag('option', $attsOpts, oldHtmlspecialchars($_text));
			}
		} else {
			// options of select Menu
			$options = makeArrayFromCSV($values);
			if(isset($atts['xml'])){
				$attsOpts['xml'] = $atts['xml'];
			}

			foreach($options as $option){
				$attsOpts['value'] = oldHtmlspecialchars($option);
				$opts .= getHtmlTag('option', $attsOpts, oldHtmlspecialchars($option));
			}
		}

		// select menu

		if(isset($atts['id'])){ //  use another ID!!!!
			$atts['id'] = 'tmp_' . $atts['id'];
		}
		$atts['onchange'] = 'this.form.elements[\'' . $name . '\'].value' . ($mode === 'add' ?
						' += ((this.form.elements[\'' . $name . '\'].value ? \' \' : \'\') + this.options[this.selectedIndex].value);' :
						'=this.options[this.selectedIndex].value;'
				) . 'this.selectedIndex=0;';
		$atts['name'] = 'tmp_' . $name;
		//$atts['size'] = isset($atts['size']) ? $atts['size'] : 1;
		$selectMenue = getHtmlTag('select', removeAttribs($atts, array('size')), $opts, true); //  remove size for choice
		return '<table class="default"><tr><td>' . $textField . '</td><td>' . $selectMenue . '</td></tr></table>';
	}

	static function getExtensionPopup($name, $selected, $extensions, $width = '', $attribs = '', $permission = true){
		if((isset($extensions)) && (count($extensions) > 1)){
			if(!$permission){
				$disabled = ' disabled="disabled "';
				$attribs .= $disabled;
			} else {
				$disabled = '';
			}
			$out = '<table class="default"><tr><td>' .
					self::htmlTextInput($name, 5, $selected, "", $attribs, "text", $width / 2, 0, "top") .
					'</td><td><select class="weSelect" name="wetmp_' . $name . '" size=1' . $disabled . ($width ? ' style="width: ' . ($width / 2) . 'px"' : '') . ' onchange="WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);if(this.options[this.selectedIndex].text){this.form.elements[\'' . $name . '\'].value=this.options[this.selectedIndex].text;};this.selectedIndex=0"><option>';
			foreach($extensions as $extension){
				$out .= '<option>' . $extension . '</option>';
			}
			$out .= '</select></td></tr></table>';
			return $out;
		}
		$_ext = $extensions[0];
		return self::hidden($name, $_ext) . '<b class="defaultfont">' . $_ext . '</b>';
	}

	static function pExtensionPopup($name, $selected, $extensions){
		echo self::getExtensionPopup($name, $selected, $extensions);
	}

	/**
	 *
	 * @param int $w
	 * @param int $h
	 * @param type $border
	 * @return type
	 * @deprecated since version 6.3.0
	 */
	static function getPixel($w, $h, $border = 0){
		//FIXME: remove this
		return '';
	}

	static function hidden($name, $value, $attribs = null){
		$attribs['name'] = $name;
		$attribs['type'] = 'hidden';
		$attribs['value'] = strpos($value, '"') !== false ? oldHtmlspecialchars($value) : $value;
		return getHtmlTag('input', $attribs);
	}

	static function we_getDayPos($format){
		return max(self::findChar($format, 'd'), self::findChar($format, 'D'), self::findChar($format, 'j'));
	}

	static function we_getMonthPos($format){
		return max(self::findChar($format, 'm'), self::findChar($format, 'M'), self::findChar($format, 'n'), self::findChar($format, 'F'));
	}

	static function we_getYearPos($format){
		return max(self::findChar($format, 'y'), self::findChar($format, 'Y'));
	}

	static function we_getHourPos($format){
		return max(self::findChar($format, 'g'), self::findChar($format, 'G'), self::findChar($format, 'h'), self::findChar($format, 'H'));
	}

	static function we_getMinutePos($format){
		return self::findChar($format, 'i');
	}

	static function findChar($in, $searchChar){
		$pos = 0;
		while(($pos = strpos($in, $searchChar, $pos)) !== FALSE){
			if(substr($in, $pos - 1, 1) != '\\'){
				return $pos;
			}
			++$pos;
		}
		return -1;
	}

	public static function getDateInput2($name, $time = 0, $setHot = false, $format = '', $onchange = '', $class = 'weSelect', $xml = false, $minyear = 0, $maxyear = 0, $style = ''){
		$_attsSelect = $_attsOption = $_attsHidden = $xml ? array('xml' => $xml) : array();

		if($class){
			$_attsSelect['class'] = $class;
		}
		if($style){
			$_attsSelect['style'] = $style;
		}
		$_attsSelect['size'] = 1;

		if($onchange || $setHot){
			$_attsSelect['onchange'] = (($setHot ? 'WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);' : '') . $onchange);
		}

		if(is_object($time)){
			$day = $time->format('j');
			$month = $time->format('n');
			$year = $time->format('Y');
			$hour = $time->format('G');
			$minute = $time->format('i');
		} else if($time){
			$time = intval($time);
			$day = intval(date('j', $time));
			$month = intval(date('n', $time));
			$year = intval(date('Y', $time));
			$hour = intval(date('G', $time));
			$minute = intval(date('i', $time));
		}

		$_dayPos = self::we_getDayPos($format);
		$_monthPos = self::we_getMonthPos($format);
		$_yearPos = self::we_getYearPos($format);
		$_hourPos = self::we_getHourPos($format);
		$_minutePos = self::we_getMinutePos($format);

		$_showDay = true;
		$_showMonth = true;
		$_showYear = true;
		$_showHour = true;
		$_showMinute = true;

		$name = preg_replace('/^(.+)]$/', '${1}%s]', $name);
		if(!$format || $_dayPos > -1){
			$days = getHtmlTag('option', array_merge($_attsOption, array('value' => 0)), '--');

			for($i = 1; $i <= 31; $i++){
				$_atts2 = ($time && $day == $i) ? array('selected' => 'selected') : array();
				$days .= getHtmlTag('option', array_merge($_attsOption, $_atts2), sprintf('%02d', $i));
			}
			$daySelect = getHtmlTag('select', array_merge($_attsSelect, array(
						'name' => sprintf($name, '_day'),
						'id' => sprintf($name, '_day')
							)), $days, true) . '&nbsp;';
		} else {
			$daySelect = getHtmlTag('input', array_merge($_attsHidden, array(
				'type' => 'hidden',
				'name' => sprintf($name, '_day'),
				'id' => sprintf($name, '_day'),
				'value' => $day
			)));
			$_showDay = false;
		}

		if(!$format || $_monthPos > -1){
			$months = getHtmlTag('option', array_merge($_attsOption, array('value' => 0)), '--');

			$monthType = (strpos($format, 'F') ? 'F' : (strpos($format, 'M') ? 'M' : 0));
			for($i = 1; $i <= 12; $i++){
				switch($monthType){//Bug #4095
					case 'F':
						$val = g_l('date', '[month][long][' . ($i - 1) . ']');
						break;
					case 'M':
						$val = g_l('date', '[month][short][' . ($i - 1) . ']');
						break;
					default:
						$val = sprintf('%02d', $i);
				}
				$_atts2 = ($time && $month == $i) ? array('selected' => 'selected', 'value' => $i) : array('value' => $i);
				$months .= getHtmlTag('option', array_merge($_attsOption, $_atts2), $val);
			}
			$monthSelect = getHtmlTag('select', array_merge($_attsSelect, array(
						'name' => sprintf($name, '_month'),
						'id' => sprintf($name, '_month')
							)), $months, true) . '&nbsp;';
		} else {
			$monthSelect = getHtmlTag('input', array_merge($_attsHidden, array(
				'type' => 'hidden',
				'name' => sprintf($name, '_month'),
				'id' => sprintf($name, '_month'),
				'value' => $month
			)));
			$_showMonth = false;
		}
		if(!$format || $_yearPos > -1){
			$years = getHtmlTag('option', array_merge($_attsOption, array('value' => 0)), '--');
			if(!$minyear){
				$minyear = 1970;
			}
			if(!$maxyear){
				$maxyear = abs(date('Y') + 100);
			}
			for($i = $minyear; $i <= $maxyear; $i++){
				$_atts2 = ($time && $year == $i) ? array('selected' => 'selected') : array();
				$years .= getHtmlTag('option', array_merge($_attsOption, $_atts2), sprintf('%02d', $i));
			}
			$yearSelect = getHtmlTag('select', array_merge($_attsSelect, array(
						'name' => sprintf($name, '_year'),
						'id' => sprintf($name, '_year')
							)), $years, true) . '&nbsp;';
		} else {
			$yearSelect = getHtmlTag('input', array_merge($_attsHidden, array(
				'type' => 'hidden',
				'name' => sprintf($name, '_year'),
				'id' => sprintf($name, '_year'),
				'value' => $year
			)));
			$_showYear = false;
		}

		if(!$format || $_hourPos > -1){
			$hours = '';
			for($i = 0; $i <= 23; $i++){
				$_atts2 = ($time && $hour == $i) ? array('selected' => 'selected') : array();
				$hours .= getHtmlTag('option', array_merge($_attsOption, $_atts2), sprintf('%02d', $i));
			}
			$hourSelect = getHtmlTag('select', array_merge($_attsSelect, array(
						'name' => sprintf($name, '_hour'),
						'id' => sprintf($name, '_hour')
							)), $hours, true) . '&nbsp;';
		} else {
			$hourSelect = getHtmlTag('input', array_merge($_attsHidden, array(
				'type' => 'hidden',
				'name' => sprintf($name, '_hour'),
				'id' => sprintf($name, '_hour'),
				'value' => isset($hour) ? $hour : 0
			)));
			$_showHour = false;
		}

		if(!$format || $_minutePos > -1){
			$minutes = '';
			for($i = 0; $i <= 59; $i++){
				$_atts2 = ($time && $minute == $i) ? array('selected' => 'selected') : array();
				$minutes .= getHtmlTag('option', array_merge($_attsOption, $_atts2), sprintf('%02d', $i));
			}
			$minSelect = getHtmlTag('select', array_merge($_attsSelect, array(
						'name' => sprintf($name, '_minute'),
						'id' => sprintf($name, '_minute')
							)), $minutes, true) . '&nbsp;';
		} else {
			$minSelect = getHtmlTag('input', array_merge($_attsHidden, array(
				'type' => 'hidden',
				'name' => sprintf($name, '_minute'),
				'id' => sprintf($name, '_minute'),
				'value' => isset($minute) ? $minute : 0
			)));
			$_showMinute = false;
		}

		$_datePosArray = array(
			($_dayPos == -1) ? 'd' : $_dayPos => $daySelect,
			($_monthPos == -1) ? 'm' : $_monthPos => $monthSelect,
			($_yearPos == -1) ? 'y' : $_yearPos => $yearSelect
		);

		$_timePosArray = array(
			($_hourPos == -1) ? 'h' : $_hourPos => $hourSelect, ($_minutePos == -1) ? 'i' : $_minutePos => $minSelect
		);

		ksort($_datePosArray);
		ksort($_timePosArray);

		return '<table class="default"><tr><td>' .
				implode('', $_datePosArray) .
				($_showHour || $_showMinute ? '</td></tr><tr><td>' : '') .
				implode('', $_timePosArray) .
				'</td></tr></table>';
	}

	//FIXME: remove deprecated
	public static function htmlTop($title = 'webEdition', $charset = '', $doctype = ''){
		t_e('deprecated', 'call of deprecated function');
		echo self::getHtmlTop($title, $charset, $doctype);
	}

	public static function getHtmlTop($title = 'webEdition', $charset = '', $doctype = '', $extraHead = '', $body = '', $skipErrorHandler = true){
		return we_html_element::htmlDocType($doctype) .
				we_html_element::htmlhtml(we_html_element::htmlHead(self::getHtmlInnerHead($title, $charset, $skipErrorHandler) . $extraHead, ($extraHead || $body ? true : false)) .
						$body, ($body ? true : false)
		);
	}

	public static function getJSErrorHandler($register = false){
		return we_html_element::jsScript(JS_DIR . 'utils/jsErrorHandler.js', ($register ? 'window.onerror=errorHandler;' : ''));
	}

	private static function getHtmlInnerHead($title, $charset, $skipErrorHandler){
		self::headerCtCharset('text/html', ($charset ? : $GLOBALS['WE_BACKENDCHARSET']));
		return
				//load this as early as possible
				($skipErrorHandler ?
						'' :
						self::getJSErrorHandler(true)
				) .
				we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
				self::htmlMetaCtCharset(($charset ? : $GLOBALS['WE_BACKENDCHARSET'])) .
				we_html_element::htmlTitle($_SERVER['SERVER_NAME'] . ' ' . $title) .
				we_html_element::htmlMeta(array('name' => 'viewport', 'content' => 'width=device-width; height=device-height; maximum-scale=1.0; initial-scale=1.0; user-scalable=yes')) .
				we_html_element::htmlMeta(array('http-equiv' => 'expires', 'content' => 0)) .
				we_html_element::htmlMeta(array('http-equiv' => 'Cache-Control', 'content' => 'no-cache')) .
				we_html_element::htmlMeta(array('http-equiv' => 'pragma', 'content' => 'no-cache')) .
				we_html_element::htmlMeta(array('http-equiv' => 'imagetoolbar', 'content' => 'no')) .
				we_html_element::htmlMeta(array('name' => 'generator', 'content' => 'webEdition')) .
				we_html_element::linkElement(array('rel' => 'SHORTCUT ICON', 'href' => IMAGE_DIR . 'webedition.ico'));
	}

	static function htmlMetaCtCharset($charset){
		$GLOBALS['we']['PageCharset'] = $charset;
		return we_html_element::htmlMeta(array('charset' => $charset));
	}

	static function headerCtCharset($content, $charset, $skipsent = false){
		$GLOBALS['we']['PageCharset'] = $charset;
		if(!$skipsent || ($skipsent && !headers_sent())){
			header('Content-Type: ' . $content . '; charset=' . $charset, true);
		}
	}

	/**
	 *
	 * @param string $text
	 * @param string $img
	 * @param bool $yes
	 * @param bool $no
	 * @param bool $cancel
	 * @param string $yesHandler
	 * @param string $noHandler
	 * @param string $cancelHandler
	 * @param string $script
	 * @return string
	 */
	static function htmlYesNoCancelDialog($text = '', $img = '', $yes = '', $no = '', $cancel = '', $yesHandler = '', $noHandler = '', $cancelHandler = '', $script = ''){
		$cancelButton = ($cancel ? we_html_button::create_button(we_html_button::CANCEL, 'javascript:' . $cancelHandler) : '');
		$noButton = ($no ? we_html_button::create_button(we_html_button::NO, 'javascript:' . $noHandler) : '');
		$yesButton = ($yes ? we_html_button::create_button(we_html_button::YES, 'javascript:' . $yesHandler) : '');


		$content = new we_html_table(array('class' => 'default'), 1, ($img ? 2 : 1));

		if($img){
			$content->setCol(0, 0, array('style' => 'vertical-align:top;padding:10px;'), $img);
		}

		$content->setCol(0, ($img ? 1 : 0), array('class' => 'defaultfont', 'style' => 'padding:10px;'), $text);

		return self::htmlDialogLayout(($script ? we_html_element::jsElement($script) : '') . $content->getHtml(), '', we_html_button::position_yes_no_cancel($yesButton, $noButton, $cancelButton), '99%', 0);
	}

	static function groupArray(array $arr, $sort = true, $len = 1){
		$tmp = array();
		if($sort){
			asort($arr, SORT_STRING);
		}
		$pre = '';
		foreach($arr as $key => $value){
			$newPre = strtoupper(substr($value, 0, $len));
			if($pre != $newPre){
				//we add an extra space so it never interferes with numeric keys
				$tmp[' ' . $newPre] = self::OPTGROUP;
				$pre = $newPre;
			}
			$tmp[$key] = $value;
		}
		return $tmp;
	}

	/* displays a grey box with text and an icon

	  $text: Text to display
	  $type: 0=no icon
	  1=Alert icon
	  2=Info icon
	  3=Question icon
	  $width: width of box
	  $useHtmlSpecialChars: true or false
	 */

	static function htmlAlertAttentionBox($text, $type = self::TYPE_NONE, $width = 0, $useHtmlSpecialChars = true, $clip = 0){
		if($width === false){
			$class = 'infobox';
			$title = '<span>' . $text . '</span>';
		} else {
			$title = $class = '';
		}
		switch($type){
			case self::TYPE_ALERT:
				$icon = '<span class="fa-stack fa-lg ' . $class . '" style="font-size: 14px;color:#F2F200;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i>' . $title . '</span>';
				break;
			case self::TYPE_INFO:
				$icon = '<span class="fa-stack fa-lg ' . $class . '" style="font-size: 14px;color:#007de3;"><i class="fa fa-circle fa-stack-2x" ></i><i class="fa fa-info fa-stack-1x fa-inverse"></i>' . $title . '</span>';
				break;
			case self::TYPE_QUESTION:
				$icon = '<span class="fa-stack fa-lg ' . $class . '" style="font-size: 14px;color:#F2F200;"><i class="fa fa-circle fa-stack-2x" ></i><i style="color:black" class="fa fa-question fa-stack-1x"></i>' . $title . '</span>';
				break;
			default :
				$icon = '';
		}
		if($width === false){
			return $icon;
		}

		$text = ($useHtmlSpecialChars) ? oldHtmlspecialchars($text, ENT_COMPAT, 'ISO-8859-1', false) : $text;

		if($clip){
			$unique = md5(uniqid(__FUNCTION__, true)); // #6590, changed from: uniqid(microtime())
			$smalltext = substr($text, 0, $clip) . ' ... ';
			$js = we_html_element::jsElement('
var state_' . $unique . '=0;
function clip_' . $unique . '(){
		var text = document.getElementById("td_' . $unique . '");
		var btn = document.getElementById("btn_' . $unique . '");

		if(state_' . $unique . '==0){
			text.innerHTML = "' . addslashes($text) . '";
			btn.innerHTML = \'<button class="weBtn" onclick="clip_' . $unique . '();"><i class="fa fa-lg fa-caret-down"></i></button>\';
			state_' . $unique . '=1;
		}else {
			text.innerHTML = "' . addslashes($smalltext) . '";
			btn.innerHTML = \'<button class="weBtn" onclick="clip_' . $unique . '();"><i class="fa fa-lg fa-caret-right"></i></button>\';
			state_' . $unique . '=0;
		}
}');
			$text = $smalltext;
		} else {
			$js = '';
		}

		if(strpos($width, '%') === false){
			$width = intval($width);
			$width -= ($width > 10 ? 10 : 0);
		}

		return $js . '<div style="background-color:#dddddd;padding:5px;white-space:normal;' . ($width ? ' width:' . $width . (is_numeric($width) ? 'px' : '') . ';' : '') . '"><table width="100%"><tr>' . ($icon ? '<td width="30" style="padding-right:10px;vertical-align:top">' . $icon . '</td>' : '') . '<td class="middlefont" ' . ($clip ? 'id="td_' . $unique . '"' : '') . '>' . $text . '</td>' . ($clip > 0 ? '<td style="vertical-align:top;text-align:right" id="btn_' . $unique . '"><button class="weBtn" onclick="clip_' . $unique . '();"><i class="fa fa-lg fa-caret-right"></i></button><td>' : '') . '</tr></table></div>';
	}

	public static function setHttpCode($status){
		switch($status){
			case 200:
				header('HTTP/1.0 200 OK', false, 200);
				header('Status: 200 OK', false, 200);
				break;
			case 303:
				header('HTTP/1.1 ' . $status . ' See Other', true, $status);
				header('Status: ' . $status . ' See Other', true, $status);
				break;
			case 304:
				header('HTTP/1.1 ' . $status . ' Not Modified', true, $status);
				header('Status: ' . $status . ' Not Modified', true, $status);
			case 307:
				header('HTTP/1.1 ' . $status . ' Temporary Redirect', true, $status);
				header('Status: ' . $status . ' Temporary Redirect', true, $status);
				break;
			case 400:
				header('HTTP/1.1 ' . $status . ' Bad Request', true, $status);
				header('Status: ' . $status . ' Bad Request', true, $status);
				break;
			case 401:
				header('HTTP/1.1 ' . $status . ' Unauthorized', true, $status);
				header('Status: ' . $status . ' Unauthorized', true, $status);
				break;
			case 403:
				header('HTTP/1.1 ' . $status . ' Forbidden', true, $status);
				header('Status: ' . $status . ' Forbidden', true, $status);
				break;
			case 404:
				header('HTTP/1.1 ' . $status . ' Not Found', true, $status);
				header('Status: ' . $status . ' Not Found', true, $status);
				break;
			case 408:
				header('HTTP/1.1 ' . $status . ' Request Time-out', true, $status);
				header('Status: ' . $status . ' Request Time-out', true, $status);
				break;
			case 503:
				header('HTTP/1.1 ' . $status . ' Service Unavailable', true, $status);
				header('Status: ' . $status . ' Service Unavailable', true, $status);
				break;
		}
	}

	/**
	 * @abstract get code for calendar
	 * @return html-code for calendar
	 */
	public static function getDateSelector($_name, $_btn, $value, $selWidth = 100, $btnClass = ''){
		$btnDatePicker = we_html_button::create_button(we_html_button::CALENDAR, "javascript:", false, 0, 0, '', '', false, false, $_btn, false, '', $btnClass);
		$oSelector = new we_html_table(array("class" => 'default', "id" => $_name . "_cell"), 1, 5);
		$oSelector->setCol(0, 2, null, we_html_tools::htmlTextInput($_name, 55, $value, 10, 'id="' . $_name . '" class="wetextinput" readonly="1"', "text", $selWidth));
		$oSelector->setCol(0, 3, null, '');
		$oSelector->setCol(0, 4, null, we_html_element::htmlA(array("href" => "#"), $btnDatePicker));

		return $oSelector->getHTML();
	}

	public static function getCalendarFiles(){
		return we_html_element::cssLink(LIB_DIR . 'additional/jscalendar/skins/aqua/theme.css') .
				we_html_element::jsScript(LIB_DIR . 'additional/jscalendar/calendar.js') .
				we_html_element::jsScript(WE_INCLUDES_DIR . 'we_language/' . $GLOBALS['WE_LANGUAGE'] . '/calendar.js') .
				we_html_element::jsScript(LIB_DIR . 'additional/jscalendar/calendar-setup.js');
	}

}
