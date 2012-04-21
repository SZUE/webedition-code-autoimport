<?php
class leSelect {

	function get($name, $options, $selected, $attribs = array()){

		$_attribs = "";
		$_options = "";

		while(list($_key, $_val) = each($attribs)){
			$_attribs .= " $_key=\"$_val\"";
		}

		while(list($_key, $_val) = each($options)){
			$_options .= " $_key=\"$_val\"";
			$_options .=	'	<option value="' . $_key . '"'. ($selected==$_key?' selected="selected"':''). '>' . $_val . '</option>';
		}

		$_attribs .= " class=\"textselect\" onblur=\"this.className='textselect';\" onfocus=\"this.className='textselectselected'\"";

		return "<select name=\"$name\"$_attribs>" . $_options . "</select>";

	}

}