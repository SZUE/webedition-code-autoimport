<?php

class leSelect{

	static function get($name, $options, $selected, $attribs = array()){

		$_attribs = "";
		$_options = "";

		foreach($attribs as $_key => $_val){
			$_attribs .= " $_key=\"$_val\"";
		}

		foreach($options as $_key => $_val){
			$_options .= " $_key=\"$_val\"" .
				'	<option value="' . $_key . '"' . ($selected == $_key ? ' selected="selected"' : '') . '>' . $_val . '</option>';
		}

		$_attribs .= " class=\"textselect\" onblur=\"this.className='textselect';\" onfocus=\"this.className='textselectselected'\"";

		return "<select name=\"$name\"$_attribs>" . $_options . "</select>";
	}

}
