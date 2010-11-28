<?php
function we_tag_listviewEnd($attribs, $content){
	return $GLOBALS["lv"]->rows ? min(
			($GLOBALS["lv"]->start - abs($GLOBALS["lv"]->offset)) + $GLOBALS["lv"]->rows,
			($GLOBALS["lv"]->anz_all - abs($GLOBALS["lv"]->offset))) : ($GLOBALS["lv"]->anz_all - abs(
			$GLOBALS["lv"]->offset));
}
?>
