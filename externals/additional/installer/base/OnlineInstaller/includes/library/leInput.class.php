<?php
class leInput {

	static function get($name, $value, $attribs, $type = "text"){

		$_attribs = "";

		while(list($_key, $_val) = each($attribs)){
			$_attribs .= " $_key=\"$_val\"";
		}
		$_attribs .= " class=\"textinput\" onblur=\"this.className='textinput';\" onfocus=\"this.className='textinputselected'\"";

		return "<input type=\"$type\" name=\"$name\" value=\"" . stripslashes(htmlspecialchars($value)) . "\" $_attribs />";

	}

}