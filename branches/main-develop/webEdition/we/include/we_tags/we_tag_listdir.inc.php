<?php
function we_tag_listdir($attribs, $content){
	global $we_editmode;

	$dirID = we_getTagAttribute("id", $attribs, $GLOBALS["we_doc"]->ParentID);

	$foo = we_getTagAttribute("index", $attribs, "index.html,index.htm,index.php,default.htm,default.html,default.php");
	$index = explode(",", $foo);

	$name = we_getTagAttribute("field", $attribs);

	$dirfield = we_getTagAttribute("dirfield", $attribs, $name);
	$sort = we_getTagAttribute("order", $attribs, $name);

	$desc = we_getTagAttribute("desc", $attribs, "", true);

	$q = "";
	foreach ($index as $i => $v) {
		$q .= " Text='$v' OR ";
	}
	$q = ereg_replace("(.*) OR ", '\1', $q);

	$files = array();

	$db = new DB_WE();
	$db2 = new DB_WE();
	$db3 = new DB_WE();

	$db->query(
			"SELECT ID,Text,IsFolder,Path FROM " . FILE_TABLE . " WHERE ((Published > 0 AND IsSearchable = 1) OR (IsFolder = 1)) AND ParentID='".abs($dirID)."'");

	while ($db->next_record()) {
		$sortfield = "";
		$namefield = "";

		if ($db->f("IsFolder")) {
			$db2->query(
					"SELECT ID FROM " . FILE_TABLE . " WHERE ParentID='" . abs($db->f("ID")) . "' AND IsFolder = 0 AND ($q) AND (Published > 0 AND IsSearchable = 1)");
			if ($db2->next_record()) {
				if ($sort) {
					$db3->query(
							"SELECT " . CONTENT_TABLE . ".Dat as Dat FROM " . LINK_TABLE . "," . CONTENT_TABLE . " WHERE " . LINK_TABLE . ".DID='" . abs($db2->f(
									"ID")) . "' AND " . LINK_TABLE . ".Name='".mysql_real_escape_string($sort)."' AND " . CONTENT_TABLE . ".ID = " . LINK_TABLE . ".CID");
					if ($db3->next_record()) {
						$sortfield = $db3->f("Dat");
					} else {
						$sortfield = $db->f("Text");
					}
				} else {
					$sortfield = $db->f("Text");
				}
				if ($dirfield) {
					$db3->query(
							"SELECT " . CONTENT_TABLE . ".Dat as Dat FROM " . LINK_TABLE . "," . CONTENT_TABLE . " WHERE " . LINK_TABLE . ".DID='" . abs($db2->f(
									"ID")) . "' AND " . LINK_TABLE . ".Name='".mysql_real_escape_string($dirfield)."' AND " . CONTENT_TABLE . ".ID = " . LINK_TABLE . ".CID");
					if ($db3->next_record()) {
						$namefield = $db3->f("Dat");
					} else {
						$namefield = $db->f("Text");
					}
				} else {
					$namefield = $db->f("Text");
				}
				array_push(
						$files,
						array(
							"properties" => $db->Record, "sort" => $sortfield, "name" => $namefield
						));
			}

		} else {
			if ($sort) {
				$db2->query(
						"SELECT " . CONTENT_TABLE . ".Dat as Dat FROM " . LINK_TABLE . "," . CONTENT_TABLE . " WHERE " . LINK_TABLE . ".DID='" . abs($db->f(
								"ID")) . "' AND " . LINK_TABLE . ".Name='".mysql_real_escape_string($sort)."' AND " . CONTENT_TABLE . ".ID = " . LINK_TABLE . ".CID");
				if ($db2->next_record()) {
					$sortfield = $db2->f("Dat");
				} else {
					$sortfield = $db->f("Text");
				}
			} else {
				$sortfield = $db->f("Text");
			}
			if ($name) {
				$db2->query(
						"SELECT " . CONTENT_TABLE . ".Dat as Dat FROM " . LINK_TABLE . "," . CONTENT_TABLE . " WHERE " . LINK_TABLE . ".DID='" . abs($db->f(
								"ID")) . "' AND " . LINK_TABLE . ".Name='".mysql_real_escape_string($name)."' AND " . CONTENT_TABLE . ".ID = " . LINK_TABLE . ".CID");
				if ($db2->next_record()) {
					$namefield = $db2->f("Dat");
				} else {
					$namefield = $db->f("Text");
				}
			} else {
				$namefield = $db->f("Text");
			}
			array_push(
					$files,
					array(
						"properties" => $db->Record, "sort" => $sortfield, "name" => $namefield
					));
		}
	}

	if ($sort) {
		if ($desc) {
			usort($files, "we_cmpFieldDesc");
		} else {
			usort($files, "we_cmpField");
		}
	} else {
		if ($desc) {
			usort($files, "we_cmpTextDesc");
		} else {
			usort($files, "we_cmpText");
		}
	}
	$out = "";

	foreach ($files as $i => $v) {

		$field = $v["name"];
		$id = $v["properties"]["ID"];
		$path = $v["properties"]["Path"];
		$foo = ereg_replace('<we:field([^>]*)>', $field, $content);
		$foo = ereg_replace('<we:id([^>]*)>', $id, $foo);
		$foo = ereg_replace('<we:path([^>]*)>', $path, $foo);
		$foo = ereg_replace('<we:a([^>]*)>', '<a href="' . $path . '"\1>', $foo);
		$foo = ereg_replace('</we:a[^>]*>', '</a>', $foo);
		$foo = ereg_replace(
				'<we:ifSelf[^>]*>',
				'<?php if("' . $GLOBALS["WE_MAIN_DOC"]->ID . '" == "' . $id . '"): ?>',
				$foo);
		$foo = ereg_replace('</we:ifSelf[^>]*>', '<?php endif ?>', $foo);
		$foo = ereg_replace(
				'<we:ifNotSelf[^>]*>',
				'<?php if("' . $GLOBALS["WE_MAIN_DOC"]->ID . '" != "' . $id . '"): ?>',
				$foo);
		$foo = ereg_replace('</we:ifNotSelf[^>]*>', '<?php endif ?>', $foo);
		$foo = ereg_replace('</we:else[^>]*>', '<?php else: ?>', $foo);
		$foo = ereg_replace('<we:else[^/]*/>', '<?php else: ?>', $foo);

		//	parse we:ifPosition
		if (strpos($foo, 'setVar') || strpos($foo, 'position') || strpos($foo, 'ifPosition') || strpos(
				$foo,
				'ifNotPosition')) {
			$tp = new we_tagParser();
			$tags = $tp->getAllTags($foo);

			$tp->parseTags($tags, $foo);
			$foo = '<?php $GLOBALS[\'we_position\'][\'listdir\'] = array(\'position\' => ' . ($i + 1) . ', \'size\' => ' . sizeof(
					$files) . ', \'field\' => \'' . $field . '\', \'id\' => \'' . $id . '\', \'path\' => \'' . $path . '\'); ?>' . $foo . '<?php unset($GLOBALS[\'we_position\'][\'listdir\']); ?>';
		}

		$out .= $foo . "\n";
	}
	return $out . "\n";
}?>
