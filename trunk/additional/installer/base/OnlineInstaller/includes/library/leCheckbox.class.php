<?php


class leCheckbox {

	static function get($name, $value, $attribs, $text, $checked = false, $type = "checkbox"){

		$_attribs = "";

		while(list($_key, $_val) = each($attribs)){
			$_attribs .= " $_key=\"$_val\"";
		}

		$_checked = "";
		if($checked) {
			$_checked .= " checked=\"checked\"";
		}
		
		$BROWSER = "";
		if(stristr($_SERVER['HTTP_USER_AGENT'], 'safari')) {
			$BROWSER = "SAFARI";
		}
		
		$_id = (array_key_exists("id", $attribs) ? $attribs['id'] : $name);
		
		return 		"<table cellpadding=\"0\" border=\"0\" cellspacing=\"0\">"
				.	"<tr>"
				.	"<td>"
				.	"<input type=\"" . $type .  "\" name=\"" . $name . "\" "
				.	(array_key_exists("id", $attribs) ? "" : "id=\"" . $name . "\" ")
				.	"value=\"" . $value . "\"" . $_attribs . $_checked . " />"
				.	"</td>"
				.	"<td class=\"defaultfont\" nowrap=\"nowrap\">"
				.	($text != "" ? "<label" . (($BROWSER == "SAFARI") ? ' style=\"cursor:normal!important\"' : '')." for=\"" . $name . "\" style=\"cursor: pointer;-moz-user-select: none;-moz-outline: none;\" hidefocus=\"hidefocus\" >&nbsp;" . $text . "</label>" : "")
				.	"</td>"
				.	"</tr>"
				.	"</table>";

	}

}