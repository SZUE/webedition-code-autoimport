<?php
function we_tag_registeredUser($attribs, $content){

	$id = we_getTagAttribute("id", $attribs);
	$show = we_getTagAttribute("show", $attribs);
	$docAttr = we_getTagAttribute("doc", $attribs);

	if (ereg("^field:(.+)$", $id, $regs)) {
		$doc = we_getDocForTag($docAttr);
		$field = $regs[1];
		if (strlen($field))
			$id = $doc->getElement($field);
	}
	if ($id) {
		$db = new DB_WE();
		$h = getHash("SELECT * FROM " . CUSTOMER_TABLE . " WHERE id='$id'", $db);
		if ($show) {
			preg_match_all("|%([^ ]+) ?|i", $show, $foo, PREG_SET_ORDER);
			for ($i = 0; $i < sizeof($foo); $i++) {
				$show = str_replace("%" . $foo[$i][1], $h[$foo[$i][1]], $show);
			}
			return $show;
		} else {
			return $h["Username"];
		}
	}
	return "";
}?>
