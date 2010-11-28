<?php
/**
 * @return boolean
 * @param array $attribs
 * @param string $content
 * @desc returns true if calendar date is same with current date
 */
function we_tag_ifCurrentDate($attribs, $content){
	if (isset($GLOBALS["lv"]->calendar_struct)) {
		switch ($GLOBALS["lv"]->calendar_struct["calendar"]) {
			case "day" :
				return (date("d-m-Y H", $GLOBALS["lv"]->calendar_struct["date"]) == date("d-m-Y H"));
				break;
			case "month" :
			case "month_table" :
				return (date("d-m-Y", $GLOBALS["lv"]->calendar_struct["date"]) == date("d-m-Y"));
				break;
			case "year" :
				return (date("m-Y", $GLOBALS["lv"]->calendar_struct["date"]) == date("m-Y"));
				break;
		}
	}
	return false;
}
?>
