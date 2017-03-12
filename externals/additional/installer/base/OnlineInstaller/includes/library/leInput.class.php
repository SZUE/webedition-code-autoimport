<?php
/**
 * $Header$
 */

class leInput{

	static function get($name, $value, $attribs, $type = "text"){

		$_attribs = "";

		foreach($attribs as $_key => $_val){
			$_attribs .= " $_key=\"$_val\"";
		}
		$_attribs .= " class=\"textinput\" onblur=\"this.className='textinput';\" onfocus=\"this.className='textinputselected'\"";

		return "<input type=\"$type\" name=\"$name\" value=\"" . stripslashes(htmlspecialchars($value)) . "\" $_attribs />";
	}

}
