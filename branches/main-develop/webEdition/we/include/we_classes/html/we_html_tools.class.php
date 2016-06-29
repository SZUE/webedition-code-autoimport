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
	const TYPE_HELP = 4;
	const TYPE_LINK = 5;

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
		$inputs = array(
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
			$inputs['disabled'] = 'true';
		}

		$target_box = new we_html_select($inputs, 0);
		$target_box->addOptions(array(
			'' => '',
			'_top' => '_top',
			'_parent' => '_parent',
			'_self' => '_self',
			'_blank' => '_blank'
		));


		if($width){
			$inputs['style'] = 'width: ' . $width . 'px;';
		}

		if($id){
			$inputs['id'] = $id;
		}

		if($value){
			$inputs['value'] = oldHtmlspecialchars($value);
		}

		if($onChange){
			$inputs['onchange'] = $onChange;
		}

		return we_html_element::htmlSpan(array('class' => 'defaultfont', 'style' => 'margin-right:' . $abstand . 'px'), self::htmlTextInput($name, $size, $value, '', ($onChange ? 'onchange="' . $onChange . '"' : ''), 'text', $width, 0, '', $disabled)) . $target_box->getHtml();
	}

	static function htmlTextInput($name, $size = 24, $value = '', $maxlength = '', $attribs = '', $type = 'text', $width = 0, $height = 0, $markHot = '', $disabled = false, $readonly = false){
		$style = ($width || $height) ? (' style="' . ($width ? ('width: ' . $width . (is_numeric($width) ? 'px' : '') . ';') : '') .
			($height ? ('height: ' . $height . (is_numeric($height) ? 'px' : '') . ';') : '') . '"') : '';
		return '<input' . ($markHot ? ' onchange="WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);' . $markHot . '.hot=1;"' : '') .
			(strstr($attribs, "class=") ? "" : ' class="wetextinput"') . ' type="' . trim($type) . '" name="' . trim($name) .
			'" value="' . oldHtmlspecialchars($value) . '"' . ($maxlength ? (' maxlength="' . intval($maxlength) . '"') : '') . ($attribs ? ' ' . $attribs : '') . $style . ($disabled ? (' disabled="true"') : '') . ($readonly ? (' readonly="true"') : '') . ' />';
	}

	static function htmlMessageBox($w, $h, $content, $headline = '', $buttons = ''){
		return '<div class="htmlMessageBox" style="width:' . $w . 'px;height:' . $h . 'px;">' .
			($headline ? '<h1 class="header">' . $headline . '</h1>' : '') .
			'<div>' . $content . '</div><div class="buttons">' . $buttons . '</div></div>';
	}

	static function htmlDialogLayout($content, $headline, $buttons = '', $width = "100%", $marginLeft = 30, $height = "", $overflow = "auto"){
		return we_html_multiIconBox::getHTML('', array(
				array(
					"html" => $content,
					"headline" => ""
				)
				), $marginLeft, ($buttons ? '<div class="htmlDialogLayoutButtons">' . $buttons . '</div>' : ''), -1, "", "", false, $headline, "", $height, $overflow);
	}

	static function htmlDialogBorder3($w, array $content, array $headline, $class = "middlefont", $id = ""){
		$anz = count($headline);
		$out = '<table' . ($id ? ' id="' . $id . '"' : '') . ' style="width:' . $w . 'px;" class="default">
		<tr class="boxHeader">';
		// HEADLINE
		for($f = 0; $f < $anz; $f++){
			$out .= '<td class="' . $class . '">' . $headline[$f]["dat"] . '</td>';
		}
		$out .= '</tr>';

		//CONTENT
		foreach($content as $c){
			$out .= '<tr class="htmlDialogBorder4Cell">' . self::htmlDialogBorder4Row($c, $class) . '</tr>';
		}
		$out .= '</table>';

		return $out;
	}

	private static function htmlDialogBorder4Row($content, $class){
		$anz = count($content);
		$out = '';

		for($f = 0; $f < $anz; $f++){
			$bgcol = (!empty($content[$f]["bgcolor"]) ) ? $content[$f]["bgcolor"] : '';
			$out .= '<td class="' . $class . '" style="' . ($bgcol ? 'background-color:' . $bgcol . '; ' : '') .
				(isset($content[$f]["align"]) ? 'text-align:' . $content[$f]["align"] . ';' : '') . ' ' .
				(isset($content[$f]["height"]) ? 'height:' . $content[$f]["height"] . 'px;' : '') . '">' .
				(!empty($content[$f]["dat"]) ? $content[$f]["dat"] : "&nbsp;") .
				'</td>';
		}

		return $out;
	}

	static function htmlDialogBorder4($w, $content, $headline, $class = "middlefont", $id = ""){ //content && headline are arrays
		$out = '<table' . ($id ? ' id="' . $id . '"' : '') . 'style="width:' . $w . 'px;" class="default">
		<tr class="boxHeader">';
		// HEADLINE
		foreach($headline as $h){
			$out .= '<td class="' . $class . '">' . $h["dat"] . '</td>';
		}
		$out .= '</tr>';

		//CONTENT
		foreach($content as $c){
			$out .= '<tr class="htmlDialogBorder4Cell">' . self::htmlDialogBorder4Row($c, $class, '') . '</tr>';
		}
		$out .= '</table>';

		return $out;
	}

	static function html_select($name, $size, $vals, $value = '', array $attribs = []){
		return self::htmlSelect($name, $vals, $size, $value, false, $attribs, 'value');
	}

	static function htmlSelect($name, array $values, $size = 1, $selectedIndex = '', $multiple = false, array $attribs = [], $compare = 'value', $width = 0, $cls = 'defaultfont', $oldHtmlspecialchars = true){
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
	static function htmlSelectCountry($name = '', $id = '', $size = 1, $selected = [], $multiple = false, array $attribs = [], $width = 50, $cls = 'defaultfont', $oldHtmlspecialchars = true, $optsOnly = false){
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
		$attsOpts = [];

		if($valuesIsHash){
			foreach($values as $val => $text){
				$attsOpts['value'] = oldHtmlspecialchars($val);
				$opts .= getHtmlTag('option', $attsOpts, oldHtmlspecialchars($text));
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
		$ext = $extensions[0];
		return self::hidden($name, $ext) . '<b class="defaultfont">' . $ext . '</b>';
	}

	private static function we_getDayPos($format){
		return max(array(self::findChar($format, 'd'), self::findChar($format, 'D'), self::findChar($format, 'j')));
	}

	private static function we_getMonthPos($format){
		return max(array(self::findChar($format, 'm'), self::findChar($format, 'M'), self::findChar($format, 'n'), self::findChar($format, 'F')));
	}

	private static function we_getYearPos($format){
		return max(array(self::findChar($format, 'y'), self::findChar($format, 'Y')));
	}

	private static function we_getHourPos($format){
		return max(array(self::findChar($format, 'g'), self::findChar($format, 'G'), self::findChar($format, 'h'), self::findChar($format, 'H')));
	}

	private static function we_getMinutePos($format){
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

	//note it is possible to set "no date"
	public static function getDateInput($name, $time = 0, $setHot = false, $format = '', $onchange = '', $class = 'weSelect', $xml = false, $minyear = 0, $maxyear = 0, $style = ''){
		$attsSelect = $attsOption = $attsHidden = $xml ? array('xml' => $xml) : [];

		if($class){
			$attsSelect['class'] = $class;
		}
		if($style){
			$attsSelect['style'] = $style;
		}
		$attsSelect['size'] = 1;

		if($onchange || $setHot){
			$attsSelect['onchange'] = (($setHot ? 'WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);' : '') . $onchange);
		}

		if(is_object($time)){
			$day = $time->format('j');
			$month = $time->format('n');
			$year = $time->format('Y');
			$hour = $time->format('G');
			$minute = $time->format('i');
		} else {
			$time = $time > 2 ? $time : 0;
			$day = intval(date('j', $time));
			$month = intval(date('n', $time));
			$year = intval(date('Y', $time));
			$hour = intval(date('G', $time));
			$minute = intval(date('i', $time));
		}

		$dayPos = self::we_getDayPos($format);
		$monthPos = self::we_getMonthPos($format);
		$yearPos = self::we_getYearPos($format);
		$hourPos = self::we_getHourPos($format);
		$minutePos = self::we_getMinutePos($format);

		$showHour = true;
		$showMinute = true;

		$name = preg_replace('/^(.+)]$/', '${1}%s]', $name);
		if(!$format || $dayPos > -1){
			$days = getHtmlTag('option', array_merge($attsOption, array('value' => 0)), '--');

			for($i = 1; $i <= 31; $i++){
				$atts2 = ($time && $day == $i) ? array('selected' => 'selected') : [];
				$days .= getHtmlTag('option', array_merge($attsOption, $atts2), sprintf('%02d', $i));
			}
			$daySelect = getHtmlTag('select', array_merge($attsSelect, array(
					'name' => sprintf($name, '_day'),
					'id' => sprintf($name, '_day')
					)), $days, true) . '&nbsp;';
		} else {
			$daySelect = getHtmlTag('input', array_merge($attsHidden, array(
				'type' => 'hidden',
				'name' => sprintf($name, '_day'),
				'id' => sprintf($name, '_day'),
				'value' => $time ? $day : 0
			)));
		}

		if(!$format || $monthPos > -1){
			$months = getHtmlTag('option', array_merge($attsOption, array('value' => 0)), '--');

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
				$atts2 = ($time && $month == $i) ? array('selected' => 'selected', 'value' => $i) : array('value' => $i);
				$months .= getHtmlTag('option', array_merge($attsOption, $atts2), $val);
			}
			$monthSelect = getHtmlTag('select', array_merge($attsSelect, array(
					'name' => sprintf($name, '_month'),
					'id' => sprintf($name, '_month')
					)), $months, true) . '&nbsp;';
		} else {
			$monthSelect = getHtmlTag('input', array_merge($attsHidden, array(
				'type' => 'hidden',
				'name' => sprintf($name, '_month'),
				'id' => sprintf($name, '_month'),
				'value' => $time ? $month : 0
			)));
		}
		if(!$format || $yearPos > -1){
			$years = getHtmlTag('option', array_merge($attsOption, array('value' => 0)), '--');
			if(!$minyear){
				$minyear = 1970;
			}
			if(!$maxyear){
				$maxyear = abs(date('Y') + 100);
			}
			for($i = $minyear; $i <= $maxyear; $i++){
				$atts2 = ($time && $year == $i) ? array('selected' => 'selected') : [];
				$years .= getHtmlTag('option', array_merge($attsOption, $atts2), sprintf('%02d', $i));
			}
			$yearSelect = getHtmlTag('select', array_merge($attsSelect, array(
					'name' => sprintf($name, '_year'),
					'id' => sprintf($name, '_year')
					)), $years, true) . '&nbsp;';
		} else {
			$yearSelect = getHtmlTag('input', array_merge($attsHidden, array(
				'type' => 'hidden',
				'name' => sprintf($name, '_year'),
				'id' => sprintf($name, '_year'),
				'value' => $time ? $year : 0
			)));
		}

		if(!$format || $hourPos > -1){
			$hours = '';
			for($i = 0; $i <= 23; $i++){
				$atts2 = ($time && $hour == $i) ? array('selected' => 'selected') : [];
				$hours .= getHtmlTag('option', array_merge($attsOption, $atts2), sprintf('%02d', $i));
			}
			$hourSelect = getHtmlTag('select', array_merge($attsSelect, array(
					'name' => sprintf($name, '_hour'),
					'id' => sprintf($name, '_hour')
					)), $hours, true) . '&nbsp;';
		} else {
			$hourSelect = getHtmlTag('input', array_merge($attsHidden, array(
				'type' => 'hidden',
				'name' => sprintf($name, '_hour'),
				'id' => sprintf($name, '_hour'),
				'value' => $time ? $hour : 0
			)));
			$showHour = false;
		}

		if(!$format || $minutePos > -1){
			$minutes = '';
			for($i = 0; $i <= 59; $i++){
				$atts2 = ($time && $minute == $i) ? array('selected' => 'selected') : [];
				$minutes .= getHtmlTag('option', array_merge($attsOption, $atts2), sprintf('%02d', $i));
			}
			$minSelect = getHtmlTag('select', array_merge($attsSelect, array(
					'name' => sprintf($name, '_minute'),
					'id' => sprintf($name, '_minute')
					)), $minutes, true) . '&nbsp;';
		} else {
			$minSelect = getHtmlTag('input', array_merge($attsHidden, array(
				'type' => 'hidden',
				'name' => sprintf($name, '_minute'),
				'id' => sprintf($name, '_minute'),
				'value' => $time ? $minute : 0,
			)));
			$showMinute = false;
		}

		$datePosArray = array(
			($dayPos == -1) ? 'd' : $dayPos => $daySelect,
			($monthPos == -1) ? 'm' : $monthPos => $monthSelect,
			($yearPos == -1) ? 'y' : $yearPos => $yearSelect
		);

		$timePosArray = array(
			($hourPos == -1) ? 'h' : $hourPos => $hourSelect,
			($minutePos == -1) ? 'i' : $minutePos => $minSelect
		);

		ksort($datePosArray);
		ksort($timePosArray);

		return '<table class="default"><tr><td>' .
			implode('', $datePosArray) .
			($showHour || $showMinute ? '</td></tr><tr><td>' : '') .
			implode('', $timePosArray) .
			'</td></tr></table>';
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
			we_html_element::htmlMeta(array('name' => 'viewport', 'content' => 'width=device-width, height=device-height, maximum-scale=1.0, initial-scale=1.0, user-scalable=yes')) .
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
		$tmp = [];
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
		switch($type){
			case self::TYPE_LINK:
				//we have link & text attached in an array
				list($link, $text) = $text;
			case self::TYPE_HELP:
				$width = false;
		}

		if($width === false){
			$class = 'infobox';
			$title = '<span>' . $text . '</span>';
		} else {
			$title = $class = '';
		}
		switch($type){
			case self::TYPE_ALERT:
				$icon = '<span class="fa-stack fa-lg alertIcon ' . $class . '"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i>' . $title . '</span>';
				break;
			case self::TYPE_INFO:
				$icon = '<span class="fa-stack fa-lg alertIcon ' . $class . '" style="color:#007de3;"><i class="fa fa-circle fa-stack-2x" ></i><i class="fa fa-info fa-stack-1x fa-inverse"></i>' . $title . '</span>';
				break;
			case self::TYPE_QUESTION:
				$icon = '<span class="fa-stack fa-lg alertIcon ' . $class . '"><i class="fa fa-circle fa-stack-2x" ></i><i style="color:black" class="fa fa-question fa-stack-1x"></i>' . $title . '</span>';
				break;
			case self::TYPE_HELP:
				return '<span class="fa-stack alertIcon ' . $class . '" style="color:inherit;"><i class="fa fa-question-circle" ></i>' . $title . '</span>';
			case self::TYPE_LINK:
				return '<span class="fa-stack alertIcon ' . $class . '" style="color:inherit;"><i class="fa fa-external-link-square" ></i>' . $title . '</span>';
			default :
				$icon = '';
		}
		if($width === false){
			return $icon;
		}

		$text = ($useHtmlSpecialChars) ? oldHtmlspecialchars($text, ENT_COMPAT, 'ISO-8859-1', false) : $text;

		if($clip){
			$unique = md5(uniqid(__FUNCTION__, true)); // #6590, changed from: uniqid(microtime())
		}

		return '<div class="alertAttentionBox' . ($icon ? ' alertIcon' : '') . ($clip ? ' alertCut' : '') . '" style="' . ($width ? ' width:' . $width . (is_numeric($width) ? 'px' : '') . ';' : '') . '">' .
			($icon ? '<div class="icon">' . $icon . '</div>' : '') .
			'<div class="middlefont ' . ($clip > 0 ? 'cutText" id="td_' . $unique . '" style="max-width:' . $clip . 'ex;"' : '"') . '>' . $text . '</div>' .
			($clip > 0 ? '<button type="button" class="weBtn clipbutton" id="btn_' . $unique . '" onclick="WE().util.clip(document,\'' . $unique . '\',' . $clip . ')"><i class="fa fa-lg fa-caret-right"></i></button>' : '') .
			'</div>';
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
	public static function getDateSelector($name, $btn, $value, $selWidth = 100, $btnClass = ''){
		$btnDatePicker = we_html_button::create_button(we_html_button::CALENDAR, "javascript:", false, 0, 0, '', '', false, false, $btn, false, '', $btnClass);
		$oSelector = new we_html_table(array("class" => 'default', "id" => $name . "_cell"), 1, 5);
		$oSelector->setCol(0, 2, null, we_html_tools::htmlTextInput($name, 55, $value, 10, 'id="' . $name . '" class="wetextinput" readonly="1"', "text", $selWidth));
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
