<?php

class leCheckbox{

	static function get($name, $value, $attribs, $text, $checked = false, $type = "checkbox"){
		$_attribs = "";
		foreach($attribs as $_key => $_val){
			$_attribs .= " $_key=\"$_val\"";
		}

		$_checked = ($checked ? " checked=\"checked\"" : '');
		$_id = (array_key_exists("id", $attribs) ? $attribs['id'] : $name);

		return "<table cellpadding=\"0\" border=\"0\" cellspacing=\"0\">
<tr><td>
	<input type=\"" . $type . "\" name=\"" . $name . "\" " . (array_key_exists("id", $attribs) ? "" : "id=\"" . $name . "\" ") . "value=\"" . $value . "\"" . $_attribs . $_checked . " />
</td><td class=\"defaultfont\" nowrap=\"nowrap\">" .
			($text != "" ? "<label for=\"" . $name . "\" style=\"cursor: pointer;\" hidefocus=\"hidefocus\" >&nbsp;" . $text . "</label>" : "") .
			"</td></tr></table>";
	}

}
