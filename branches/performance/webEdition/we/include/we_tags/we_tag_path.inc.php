<?php
function we_tag_path($attribs, $content){
	$db = new DB_WE();
	$field = we_getTagAttribute("field", $attribs);
	$dirfield = we_getTagAttribute("dirfield", $attribs, $field);
	$index = we_getTagAttribute("index", $attribs);
	$htmlspecialchars = we_getTagAttribute("htmlspecialchars", $attribs, "", true);

	$docAttr = we_getTagAttribute("doc", $attribs);
	$doc = we_getDocForTag($docAttr, true);

	$pID = $doc->ParentID;

	$indexArray = $index ? explode(",", $index) : array(
		"index.html", "index.htm", "index.php", "default.htm", "default.html", "default.php"
	);

	$sep = we_getTagAttribute("separator", $attribs, "/");
	$home = we_getTagAttribute("home", $attribs, "home");
	$hidehome = we_getTagAttribute("hidehome", $attribs, false, true);

	$class = we_getTagAttribute("class", $attribs);
	$style = we_getTagAttribute("style", $attribs);

	$class = $class ? ' class="' . $class . '"' : '';
	$style = $style ? ' style="' . $style . '"' : '';

	$path = "";
	$q = "";
	foreach ($indexArray as $i => $v) {
		$q .= " Text='$v' OR ";
	}
	$q = ereg_replace("(.*) OR ", '\1', $q);
	$id = $doc->ID;
	$show = $doc->getElement($field);
	if (!in_array($doc->Text, $indexArray)) {
		if (!$show)
			$show = $doc->Text;
		$path = $htmlspecialchars ? htmlspecialchars($sep . $show) : $sep . $show;
	}
	while ($pID) {
		$db->query(
				"SELECT ID,Path FROM " . FILE_TABLE . " WHERE ParentID='".abs($pID)."' AND IsFolder = 0 AND ($q) AND (Published > 0 AND IsSearchable = 1)");
		$db->next_record();
		$fileID = $db->f("ID");
		$filePath = $db->f("Path");
		if ($fileID) {
			$show = f(
					"SELECT " . CONTENT_TABLE . ".Dat as Dat FROM " . LINK_TABLE . "," . CONTENT_TABLE . " WHERE " . LINK_TABLE . ".DID='".abs($fileID)."' AND " . LINK_TABLE . ".Name='".mysql_real_escape_string($dirfield)." ' AND " . CONTENT_TABLE . ".ID = " . LINK_TABLE . ".CID",
					"Dat",
					$db);
			if (!$show)
				$show = f("SELECT Text FROM " . FILE_TABLE . " WHERE ID='".abs($pID)."'", "Text", $db);

			if ($fileID != $doc->ID) {
				$link_pre = '<a href="' . $filePath . '"' . $class . $style . '>';
				$link_post = '</a>';
			} else {
				$link_pre = '';
				$link_post = '';
			}
		} else {
			$link_pre = '';
			$link_post = '';
			$show = f("SELECT Text FROM " . FILE_TABLE . " WHERE ID='".abs($pID)."'", "Text", $db);
		}
		$pID = f("SELECT ParentID from " . FILE_TABLE . " WHERE ID='".abs($pID)."'", "ParentID", $db);
		if (!$pID && $hidehome) {
			$path = $link_pre . ($htmlspecialchars ? htmlspecialchars($show) : $show) . $link_post . $path;
		} else {
			$path = $sep . $link_pre . ($htmlspecialchars ? htmlspecialchars($show) : $show) . $link_post . $path;
		}
	}
	$show = "";
	$db->query(
			"SELECT ID,Path FROM " . FILE_TABLE . " WHERE ParentID='0' AND IsFolder = 0 AND ($q) AND (Published > 0 AND IsSearchable = 1)");
	$db->next_record();
	$fileID = $db->f("ID");
	$filePath = $db->f("Path");
	if ($fileID) {
		$show = f(
				"SELECT " . CONTENT_TABLE . ".Dat as Dat FROM " . LINK_TABLE . "," . CONTENT_TABLE . " WHERE " . LINK_TABLE . ".DID='".abs($fileID)."' AND " . LINK_TABLE . ".Name='".mysql_real_escape_string($field)."' AND " . CONTENT_TABLE . ".ID = " . LINK_TABLE . ".CID",
				"Dat",
				$db);
		if (!$show) {
			$show = $home;
		}
		$link_pre = '<a href="' . $filePath . '"' . $class . $style . '>';
		$link_post = '</a>';
	} else {
		$link_pre = '';
		$link_post = '';
		$show = $home;
	}
	if ($hidehome) {
		$show = "";
	} else {
		$show = $link_pre . ($htmlspecialchars ? htmlspecialchars($show) : $show) . $link_post;
	}
	return $show . $path;
}?>
