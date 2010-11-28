<?php
function we_tag_search($attribs, $content){

	$name = we_getTagAttribute("name", $attribs, "0");
	$type = we_getTagAttribute("type", $attribs);
	$xml = we_getTagAttribute("xml", $attribs, "");
	$value = we_getTagAttribute("value", $attribs, "");

	$searchValue = htmlspecialchars(
			str_replace(
					"\"",
					"",
					str_replace(
							"\\\"",
							"",
							(isset($_REQUEST["we_lv_search_" . $name]) ? trim($_REQUEST["we_lv_search_" . $name]) : $value))));
	if ($type == "print") {
		return $searchValue;
	} else {

		$attsHidden = array(

				'type' => 'hidden',
				'xml' => $xml,
				'name' => 'we_from_search_' . $name,
				'value' => (isset($GLOBALS["we_editmode"]) && $GLOBALS["we_editmode"] ? 0 : 1)
		);

		$hidden = getHtmlTag('input', $attsHidden);
		if ($type == "textinput") {

			$atts = removeAttribs($attribs, array(
				'type', 'onchange', 'name', 'cols', 'rows'
			));
			$atts = array_merge(
					$atts,
					array(

					'name' => "we_lv_search_$name", 'type' => 'text', 'value' => $searchValue, 'xml' => $xml
					));
			return getHtmlTag('input', $atts) . $hidden;

		} else
			if ($type == "textarea") {

				$atts = removeAttribs(
						$attribs,
						array(
							'type', 'onchange', 'name', 'size', 'maxlength', 'value'
						));
				$atts = array_merge(
						$atts,
						array(
							'class' => 'defaultfont', 'name' => "we_lv_search_$name", 'xml' => $xml
						));

				return getHtmlTag('textarea', $atts, $searchValue, true) . $hidden;
			}
	}
}?>
