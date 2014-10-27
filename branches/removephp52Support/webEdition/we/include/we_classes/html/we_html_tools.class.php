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
		return '<table style="border-spacing: 0px;border-style:none" cellpadding="0">' .
				($text ? '<tr><td class="' . trim($textclass) . '" align="' . trim($textalign) . '" colspan="' . $colspan . '">' . $text . '</td></tr>' : '') .
				($abstand ? ('<tr style="height:' . $abstand . 'px"><td colspan="' . $colspan . '"></td></tr>') : '') .
				'<tr>' . $elemOut . '</tr></table>';
	}

	static function targetBox($name, $size, $width = '', $id = '', $value = '', $onChange = '', $abstand = 8, $selectboxWidth = '', $disabled = false){
		$jsvarname = str_replace(array('[', ']'), '_', $name);
		$_inputs = array(
			'class' => 'weSelect',
			'name' => 'sel_' . $name,
			'onfocus' => "change$jsvarname=1;",
			'onchange' => "if(change$jsvarname) this.form.elements['" . $name . "'].value = this.options[this.selectedIndex].text; change$jsvarname=0; this.selectedIndex = 0;" . $onChange,
			'style' => (($selectboxWidth != '') ? ('width: ' . $selectboxWidth . 'px;') : '')
		);

		if($disabled){
			$_inputs['disabled'] = 'true';
		}

		$_target_box = new we_html_select($_inputs, 0);
		$_target_box->addOptions(5, array(
			'', '_top', '_parent', '_self', '_blank'
				), array(
			'', '_top', '_parent', '_self', '_blank'
		));

		$_table = new we_html_table(array(
			'cellpadding' => 0, 'cellspacing' => 0, 'border' => 0
				), 1, 3);

		$_inputs = array(
			'name' => $name,
			'class' => 'defaultfont'
		);

		if($width){
			$_inputs ['style'] = 'width: ' . $width . 'px;';
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

		$_table->setCol(0, 0, array(
			'class' => 'defaultfont'
				), self::htmlTextInput($name, $size, $value, '', ($onChange ? 'onchange="' . $onChange . '"' : ''), 'text', $width, 0, '', $disabled));

		$_table->setCol(0, 1, null, self::getPixel($abstand, 1));

		$_table->setCol(0, 2, array(
			'class' => 'defaultfont'
				), $_target_box->getHtml());

		return $_table->getHtml();
	}

	static function htmlTextInput($name, $size = 24, $value = '', $maxlength = '', $attribs = '', $type = 'text', $width = 0, $height = 0, $markHot = '', $disabled = false){
		$style = ($width || $height) ? (' style="' . ($width ? ('width: ' . $width . (is_numeric($width) ? 'px' : '') . ';') : '') .
				($height ? ('height: ' . $height . (is_numeric($height) ? 'px' : '') . ';') : '') . '"') : '';
		return '<input' . ($markHot ? ' onchange="if(typeof(_EditorFrame) != \'undefined\'){_EditorFrame.setEditorIsHot(true);}' . $markHot . '.hot=1;"' : '') .
				(strstr($attribs, "class=") ? "" : ' class="wetextinput"') . ' type="' . trim($type) . '" name="' . trim($name) .
				'" size="' . intval($size) . '" value="' . oldHtmlspecialchars($value) . '"' . ($maxlength ? (' maxlength="' . intval($maxlength) . '"') : '') . ($attribs ? ' ' . $attribs : '') . $style . ($disabled ? (' disabled="true"') : '') . ' />';
	}

	static function htmlMessageBox($w, $h, $content, $headline = '', $buttons = ''){
		return '<div style="width:' . $w . 'px;height:' . $h . 'px;background-color:#F7F5F5;border: 2px solid #D7D7D7;padding:20px;">' .
				($headline ? '<h1 class="header">' . $headline . '</h1>' : '') .
				'<div>' . $content . '</div><div style="margin-top:20px;">' . $buttons . '</div></div>';
	}

	static function htmlDialogLayout($content, $headline, $buttons = '', $width = "100%", $marginLeft = 30, $height = "", $overflow = "auto"){
		return we_html_multiIconBox::getHTML('', $width, array(
					array(
						"html" => $content, "headline" => "", "space" => 0
					)
						), $marginLeft, ($buttons ? '<div align="right" style="margin-left:10px;">' . $buttons . '</div>' : ''), -1, "", "", false, $headline, "", $height, $overflow);
	}

	static function htmlDialogBorder3($w, $h, $content, $headline, $class = "middlefont", $bgColor = "", $buttons = "", $id = "", $style = ""){ //content && headline are arrays
		$anz = count($headline);
		$out = '<table' . ($id ? ' id="' . $id . '"' : '') . ' style="border-spacing: 0px;border-style:none;width:' . $w . 'px;' . $style . '" cellpadding="0">
		<tr>
		<td width="8" style="background-image:url(' . IMAGE_DIR . 'box/box_header_ol2.gif);">' . self::getPixel(8, 21) . '</td>';
		// HEADLINE
		for($f = 0; $f < $anz; $f++){
			$out .= '<td class="' . $class . '" style="padding:1px 5px 1px 5px;background-image:url(' . IMAGE_DIR . 'box/box_header_bg2.gif);">' . $headline[$f]["dat"] . '</td>';
		}
		$out .= '<td width="8" style="background-image:url(' . IMAGE_DIR . 'box/box_header_or2.gif);">' . self::getPixel(8, 21) . '</td>
				</tr>';

		//CONTENT
		foreach($content as $c){
			$out .= '<tr>' . self::htmlDialogBorder4Row($c, $class, $bgColor) . '</tr>';
		}

		$out .= '</table>';

		if($buttons){
			$attribs = array(
				'border' => 0, 'cellpadding' => 0, 'cellspacing' => 0
			);
			$_table = new we_html_table($attribs, 3, 1);
			$_table->setCol(0, 0, array(
				'colspan' => 2
					), $out);
			$_table->setCol(1, 0, null, self::getPixel($w, 5)); // row for gap between buttons and dialogborder
			$_table->setCol(2, 0, array(
				'align' => 'right'
					), $buttons);
			return $_table->getHtml();
		} else {
			return $out;
		}
	}

	static function htmlDialogBorder4Row($content, $class = 'middlefont', $bgColor = ''){
		$anz = count($content);
		$out = '<td style="border-bottom: 1px solid silver;background-image:url(' . IMAGE_DIR . 'box/shaddowBox3_l.gif);">' .
				self::getPixel(8, isset($content[0]["height"]) ? $content[0]["height"] : 1) . '</td>';

		for($f = 0; $f < $anz; $f++){
			$bgcol = $bgColor ? : ((isset($content[$f]["bgcolor"]) && $content[$f]["bgcolor"]) ? $content[$f]["bgcolor"] : "white");
			$out .= '<td class="' . $class . '" style="padding:2px 5px 2px 5px;' . (($f != 0) ? "border-left:1px solid silver;" : "") . 'border-bottom: 1px solid silver;background-color:' . $bgcol . ';" ' .
					((isset($content[$f]["align"])) ? 'align="' . $content[$f]["align"] . '"' : "") . ' ' .
					((isset($content[$f]["height"])) ? 'height="' . $content[$f]["height"] . '"' : "") . '>' .
					((isset($content[$f]["dat"]) && $content[$f]["dat"]) ? $content[$f]["dat"] : "&nbsp;") .
					'</td>';
		}
		$out .= '<td style="border-bottom: 1px solid silver;background-image:url(' . IMAGE_DIR . 'box/shaddowBox3_r.gif);">' .
				self::getPixel(8, isset($content[0]["height"]) ? $content[0]["height"] : 1) . '</td>';
		return $out;
	}

	static function htmlDialogBorder4($w, $h, $content, $headline, $class = "middlefont", $bgColor = "", $buttons = "", $id = "", $style = ""){ //content && headline are arrays
		$out = '<table' . ($id ? ' id="' . $id . '"' : '') . 'style="border-spacing: 0px;border-style:none;width:' . $w . 'px;' . $style . '" cellpadding="0">
		<tr><td width="8" style="background-image:url(' . IMAGE_DIR . 'box/box_header_ol2.gif);">' . self::getPixel(8, 21) . '</td>';
		// HEADLINE
		foreach($headline as $h){
			$out .= '<td class="' . $class . '" style="padding:1px 5px 1px 5px;background-image:url(' . IMAGE_DIR . 'box/box_header_bg2.gif);">' . $h["dat"] . '</td>';
		}
		$out .= '<td width="8" style="background-image:url(' . IMAGE_DIR . 'box/box_header_or2.gif);">' . self::getPixel(8, 21) . '</td></tr>';

		//CONTENT
		foreach($content as $c){
			$out .= '<tr>' . self::htmlDialogBorder4Row($c, $class, $bgColor) . '</tr>';
		}

		$out .= '</table>';

		if($buttons){
			$attribs = array(
				"border" => 0, "cellpadding" => 0, "cellspacing" => 0
			);
			$_table = new we_html_table($attribs, 3, 1);
			$_table->setCol(0, 0, array("colspan" => 2), $out);
			$_table->setCol(1, 0, null, self::getPixel($w, 5)); // row for gap between buttons and dialogborder
			$_table->setCol(2, 0, array("align" => "right"), $buttons);
			return $_table->getHtml();
		} else {
			return $out;
		}
	}

	static function html_select($name, $size, $vals, $value = '', $onchange = '', array $attribs = array()){
		return self::htmlSelect($name, $vals, $size, $value, false, array_merge($attribs, array('onchange' => ($onchange ? : ''))), 'key');
	}

	static function htmlSelect($name, $values, $size = 1, $selectedIndex = '', $multiple = false, array $attribs = array(), $compare = 'value', $width = 0, $cls = 'defaultfont', $oldHtmlspecialchars = true){
		$ret = '';
		$selIndex = makeArrayFromCSV($selectedIndex);
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

		if(!is_array($attribs)){
			$attribs = self::parseAttribs($attribs);
		}

		return ($name ? we_html_element::htmlSelect(array_merge(array(
							'class' => 'weSelect ' . $cls,
							'name' => trim($name),
							'size' => abs($size),
							($multiple ? 'multiple' : '') => 'multiple',
							($width ? 'width' : '') => ($width ? : '')
										), $attribs
								), $ret) : $ret);
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
		$atts = removeAttribs($atts, array('size')); //  remove size for choice
		$selectMenue = getHtmlTag('select', $atts, $opts, true);
		return '<table style="border-spacing: 0px;border-style:none;" cellpadding="0"><tr><td>' . $textField . '</td><td>' . $selectMenue . '</td></tr></table>';
	}

	static function gifButton($name, $href, $language = "Deutsch", $alt = "", $width = "", $height = "", $onClick = "", $bname = "", $target = "", $disabled = false){
		$img = we_html_element::htmlImg(array(
					'src' => IMAGE_DIR . 'buttons/' . $name . ($disabled ? '_d' : "") . ($language ? '_' : '') . $language . '.gif',
					'style' => ($width ? ' width:' . $width . 'px;' : '') . ($height ? ' height:' . $height . 'px' : ''),
					'alt' => $alt,
					'border' => 0,
					'name' => ($bname ? : '')
		));

		return ($disabled ?
						$img : ($href ?
								'<a href="' . $href . '" onmouseover="window.status=\'' . $alt . '\';return true;" onmouseout="window.status=\'\';return true;"' . ($onClick ? ' onclick="' . $onClick . '"' : '') . ($target ? (' target="' . $target . '"') : '') . '>' . $img . '</a>' :
								'<input type="image" src="' . IMAGE_DIR . 'buttons/' . $name . ($language ? '_' : '') . $language . '.gif"' . ($width ? ' width="' . $width . '"' : '') . ($height ? ' height="' . $height . '"' : '') . ' border="0" alt="' . $alt . '"' . ($onClick ? ' onclick="' . $onClick . '"' : '') . ($bname ? ' name="' . $bname . '"' : '') . ' />'
						));
	}

	static function getExtensionPopup($name, $selected, $extensions, $width = '', $attribs = '', $permission = true){
		if((isset($extensions)) && (count($extensions) > 1)){
			if(!$permission){
				$disabled = ' disabled="disabled "';
				$attribs .= $disabled;
			} else {
				$disabled = '';
			}
			$out = '<table style="border-spacing: 0px;border-style:none;" cellpadding="0"><tr><td>' .
					self::htmlTextInput($name, 5, $selected, "", $attribs, "text", $width / 2, 0, "top") .
					'</td><td><select class="weSelect" name="wetmp_' . $name . '" size=1' . $disabled . ($width ? ' style="width: ' . ($width / 2) . 'px"' : '') . ' onchange="if(typeof(_EditorFrame) != \'undefined\'){_EditorFrame.setEditorIsHot(true);}if(this.options[this.selectedIndex].text){this.form.elements[\'' . $name . '\'].value=this.options[this.selectedIndex].text;};this.selectedIndex=0"><option>';
			foreach($extensions as $extension){
				$out .= '<option>' . $extension . '</option>';
			}
			$out .= '</select></td></tr></table>';
			return $out;
		} else {
			$_ext = $extensions[0];
			return self::hidden($name, $_ext) . '<b class="defaultfont">' . $_ext . '</b>';
		}
	}

	static function pExtensionPopup($name, $selected, $extensions){
		print self::getExtensionPopup($name, $selected, $extensions);
	}

	static function getPixel($w, $h, $border = 0){
		if($w == ''){
			$w = 0;
		}
		if($h == ''){
			$h = 0;
		}
		return '<span style="display:inline-block;width:' . $w . (is_numeric($w) ? 'px' : '') . ';height:' . $h . (is_numeric($h) ? 'px' : '') . ';' . ($border ? 'border:' . $border . 'px solid black;' : '') . '"></span>';
	}

	static function pPixel($w, $h){
		print self::getPixel($w, $h);
	}

	static function hidden($name, $value, $attribs = null){
		$attribute = '';
		if(isset($attribs) && is_array($attribs)){
			foreach($attribs as $key => $val){
				$attribute .= $key . '="' . $val . '" ';
			}
		} if(XHTML_DEFAULT){
			$tagende = '/>';
		} else {
			$tagende = '>';
		}
		return '<input type="hidden" value="' . $value . '" name="' . $name . '" ' . $attribute . $tagende;
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
			$_attsSelect['onchange'] = (($setHot ? '_EditorFrame.setEditorIsHot(true);' : '') . $onchange);
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

		$name = preg_replace('/^(.+)]$/', '\1%s]', $name);
		if(!$format || $_dayPos > -1){
			$days = getHtmlTag('option', array_merge($_attsOption, array('value' => 0)), '--');
			;
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
						'name' => sprintf($name, '_hour'), 'id' => sprintf($name, '_hour')
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

		return '<table style="border-spacing: 0px;border-style:none;" cellpadding="0"><tr><td>' .
				implode('', $_datePosArray) .
				($_showHour || $_showMinute ? '</td></tr><tr><td>' : '') .
				implode('', $_timePosArray) .
				'</td></tr></table>';
	}

	//FIXME: remove deprecated
	public static function htmlTop($title = 'webEdition', $charset = '', $doctype = ''){
		t_e('deprecated');
		print self::getHtmlTop($title, $charset, $doctype);
	}

	public static function getHtmlTop($title = 'webEdition', $charset = '', $doctype = '', $expand = false, $closeHtml = false, $closeHead = false){
		return we_html_element::htmlDocType($doctype) .
				we_html_element::htmlhtml(we_html_element::htmlHead(
								self::getHtmlInnerHead($title, $charset, $expand), $closeHead)
						, $closeHtml);
	}

	public static function getJSErrorHandler($plain = false){
		$ret = 'try{' .
				'window.onerror=function(msg, file, line, col, errObj){' .
				(defined('WE_VERSION_SUPP') && WE_VERSION_SUPP ?
						'
	postData=\'we_cmd[msg]=\'+encodeURIComponent(msg);
	postData+=\'&we_cmd[file]=\'+encodeURIComponent(file);
	postData+=\'&we_cmd[line]=\'+encodeURIComponent(line);
	if(col){
		postData+=\'&we_cmd[col]=\'+encodeURIComponent(col);
	}
	if(errObj){
		postData+=\'&we_cmd[errObj]=\'+encodeURIComponent(errObj.stack);
	}
	lcaller=arguments.callee.caller;
	while(lcaller){
		postData+=\'&we_cmd[]=\'+encodeURIComponent(lcaller.name);
		lcaller=lcaller.caller;
	}
	postData+=\'&we_cmd[App]=\'+encodeURIComponent(navigator.appName);
	postData+=\'&we_cmd[Ver]=\'+encodeURIComponent(navigator.appVersion);
	postData+=\'&we_cmd[UA]=\'+encodeURIComponent(navigator.userAgent);
	xmlhttp=new XMLHttpRequest();
	xmlhttp.open(\'POST\',\'' . WEBEDITION_DIR . 'rpc/rpc.php?cmd=TriggerJSError&cns=error\',true);
	xmlhttp.setRequestHeader(\'Content-type\',\'application/x-www-form-urlencoded\');
	xmlhttp.send(postData);
	return true;
' :
						'return true;'//prevent JS errors to have influence
				) . '}}catch(e){}';

		return ($plain ? str_replace("\n", '', $ret) : we_html_element::jsElement($ret));
	}

	public static function getHtmlInnerHead($title = 'webEdition', $charset = '', $expand = false){
		if(!$expand){
			self::headerCtCharset('text/html', ($charset ? : $GLOBALS['WE_BACKENDCHARSET']));
		}
		return we_html_element::htmlTitle($_SERVER['SERVER_NAME'] . ' ' . $title) .
				we_html_element::htmlMeta(array('http-equiv' => 'expires', 'content' => 0)) .
				we_html_element::htmlMeta(array('http-equiv' => 'Cache-Control', 'content' => 'no-cache')) .
				we_html_element::htmlMeta(array('http-equiv' => 'pragma', 'content' => 'no-cache')) .
				self::htmlMetaCtCharset('text/html', ($charset ? : $GLOBALS['WE_BACKENDCHARSET'])) .
				we_html_element::htmlMeta(array('http-equiv' => 'imagetoolbar', 'content' => 'no')) .
				we_html_element::htmlMeta(array('name' => 'generator', 'content' => 'webEdition')) .
				we_html_element::linkElement(array('rel' => 'SHORTCUT ICON', 'href' => IMAGE_DIR . 'webedition.ico')) .
				($expand ?
						we_html_element::jsElement(we_base_file::load(JS_PATH . 'we_showMessage.js')) .
						we_html_element::jsElement(we_base_file::load(JS_PATH . 'attachKeyListener.js')) :
						we_html_element::jsScript(JS_DIR . 'we_showMessage.js') .
						we_html_element::jsScript(JS_DIR . 'attachKeyListener.js')

				) . self::getJSErrorHandler();
	}

	static function htmlMetaCtCharset($content, $charset){
		$GLOBALS['we']['PageCharset'] = $charset;
		return we_html_element::htmlMeta(array(
					'http-equiv' => 'content-type',
					'content' => $content . '; charset=' . $charset
		));
	}

	static function headerCtCharset($content, $charset, $skipsent = false){
		$GLOBALS['we']['PageCharset'] = $charset;
		if(!$skipsent || ($skipsent && !headers_sent())){
			header('Content-Type: ' . $content . '; charset=' . $charset);
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
		$cancelButton = (empty($cancel) ? '' : we_html_button::create_button('cancel', 'javascript:' . $cancelHandler));
		$noButton = (empty($no) ? '' : we_html_button::create_button('no', 'javascript:' . $noHandler));
		$yesButton = (empty($yes) ? '' : we_html_button::create_button('yes', 'javascript:' . $yesHandler) );


		$content = new we_html_table(array(
			'cellpadding' => 10, 'cellspacing' => 0, 'border' => 0
				), 1, (empty($img) ? 1 : 2));

		if(!empty($img) && file_exists($_SERVER['DOCUMENT_ROOT'] . $img)){
			$size = getimagesize($_SERVER['DOCUMENT_ROOT'] . $img);
			$content->setCol(
					0, 0, array(
				'valign' => 'top'
					), we_html_element::htmlImg(
							array(
								'src' => $img, 'border' => 0, 'width' => $size[0], 'height' => $size[1]
			)));
		}

		$content->setCol(0, (empty($img) ? 0 : 1), array(
			'class' => 'defaultfont'
				), $text);

		return self::htmlDialogLayout(
						($script ? we_html_element::jsElement($script) : '') . $content->getHtml()
						, '', we_html_button::position_yes_no_cancel($yesButton, $noButton, $cancelButton), '99%', 0);
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
				$tmp[$newPre] = self::OPTGROUP;
				$pre = $newPre;
			}
			$tmp[$key] = $value;
		}
		return $tmp;
	}

	private static function parseAttribs($attribs){
		$attr = $matches = array();
		preg_match_all('|(\w+)\s*=\s*(["\'])([^\2]*)\2|U', $attribs, $matches, PREG_SET_ORDER);
		foreach($matches as $match){
			$attr[$match[1]] = ($match[2] === '\'' ? str_replace('"', '\"', $match[3]) : $match[3]);
		}
		return $attr;
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
			case self::TYPE_ALERT:
				$icon = 'alert';
				break;
			case self::TYPE_INFO:
				$icon = 'info';
				break;
			case self::TYPE_QUESTION:
				$icon = 'question';
				break;
			default :
				$icon = '';
		}

		$text = ($useHtmlSpecialChars) ? oldHtmlspecialchars($text, ENT_COMPAT, 'ISO-8859-1', false) : $text;
		$js = '';

		if($clip > 0){
			$unique = md5(uniqid(__FUNCTION__, true)); // #6590, changed from: uniqid(microtime())
			$smalltext = substr($text, 0, $clip) . ' ... ';
			$js = we_html_element::jsElement('
		var state_' . $unique . '=0;
			function clip_' . $unique . '(){
					var text = document.getElementById("td_' . $unique . '");
					var btn = document.getElementById("btn_' . $unique . '");

					if(state_' . $unique . '==0){
						text.innerHTML = "' . addslashes($text) . '";
						btn.innerHTML = "<a href=\'javascript:clip_' . $unique . '();\'><img src=\'' . BUTTONS_DIR . 'btn_direction_down.gif\' alt=\'down\' border=\'0\'></a>";
						state_' . $unique . '=1;
					}else {
						text.innerHTML = "' . addslashes($smalltext) . '";
						btn.innerHTML = "<a href=\'javascript:clip_' . $unique . '();\'><img src=\'' . BUTTONS_DIR . 'btn_direction_right.gif\' alt=\'right\' border=\'0\'></a>";
						state_' . $unique . '=0;
					}
			}');
			$text = $smalltext;
		}

		if(strpos($width, '%') === false){
			$width = intval($width);
			if($width > 10 && (!we_base_browserDetect::isIE() || (we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() >= 10))){
				$width -= 10;
			}
		}

		return $js . '<div style="background-color:#dddddd;padding:5px;white-space:normal;' . ($width ? ' width:' . $width . (is_numeric($width) ? 'px' : '') . ';' : '') . '"><table border="0" cellpadding="2" width="100%"><tr>' . ($icon ? '<td width="30" style="padding-right:10px;" valign="top"><img src="' . IMAGE_DIR . $icon . '_small.gif" width="20" height="22" /></td>' : '') . '<td class="middlefont" ' . ($clip > 0 ? 'id="td_' . $unique . '"' : '') . '>' . $text . '</td>' . ($clip > 0 ? '<td valign="top" align="right" id="btn_' . $unique . '"><a href="javascript:clip_' . $unique . '();"><img src="' . BUTTONS_DIR . 'btn_direction_right.gif" alt="right" border="0" /></a><td>' : '') . '</tr></table></div>';
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
	public static function getDateSelector($_name, $_btn, $value){
		$btnDatePicker = we_html_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, $_btn);
		$oSelector = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0, "id" => $_name . "_cell"), 1, 5);
		$oSelector->setCol(0, 2, null, we_html_tools::htmlTextInput($_name, 55, $value, 10, 'id="' . $_name . '" class="wetextinput" readonly="1"', "text", 100));
		$oSelector->setCol(0, 3, null, "&nbsp;");
		$oSelector->setCol(0, 4, null, we_html_element::htmlA(array("href" => "#"), $btnDatePicker));

		return $oSelector->getHTML();
	}

}
