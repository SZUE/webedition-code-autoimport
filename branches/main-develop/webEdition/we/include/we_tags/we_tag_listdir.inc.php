<?php
/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

function we_tag_listdir($attribs, $content){
	$dirID = we_getTagAttribute('id', $attribs, $GLOBALS['we_doc']->ParentID);
	$index = explode(',', we_getTagAttribute('index', $attribs, 'index.html,index.htm,index.php,default.htm,default.html,default.php'));
	$name = we_getTagAttribute('field', $attribs);
	$dirfield = we_getTagAttribute('dirfield', $attribs, $name);
	$sort = we_getTagAttribute('order', $attribs, $name);
	$desc = we_getTagAttribute('desc', $attribs, '', true);

	$q = array();
	foreach ($index as $i => $v) {
		$q[] = " Text='$v'";
	}
	$q = implode(' OR ',$q);

	$files = array();

	$db = new DB_WE();
	$db2 = new DB_WE();
	$db3 = new DB_WE();

	$db->query(
			"SELECT ID,Text,IsFolder,Path FROM " . FILE_TABLE . " WHERE ((Published > 0 AND IsSearchable = 1) OR (IsFolder = 1)) AND ParentID=".abs($dirID));

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
									"ID")) . "' AND " . LINK_TABLE . ".Name='".$db3->escape($sort)."' AND " . CONTENT_TABLE . ".ID = " . LINK_TABLE . ".CID");
						$sortfield = ($db3->next_record()) ? $db3->f("Dat") : $db->f("Text");
				} else {
					$sortfield = $db->f("Text");
				}
				if ($dirfield) {
					$db3->query(
							"SELECT " . CONTENT_TABLE . ".Dat as Dat FROM " . LINK_TABLE . "," . CONTENT_TABLE . " WHERE " . LINK_TABLE . ".DID='" . abs($db2->f(
									"ID")) . "' AND " . LINK_TABLE . ".Name='".$db3->escape($dirfield)."' AND " . CONTENT_TABLE . ".ID = " . LINK_TABLE . ".CID");
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
								"ID")) . "' AND " . LINK_TABLE . ".Name='".$db2->escape($sort)."' AND " . CONTENT_TABLE . ".ID = " . LINK_TABLE . ".CID");
					$sortfield = ($db2->next_record()) ? $db2->f("Dat"):$db->f("Text");
			} else {
				$sortfield = $db->f("Text");
			}
			if ($name) {
				$db2->query(
						"SELECT " . CONTENT_TABLE . ".Dat as Dat FROM " . LINK_TABLE . "," . CONTENT_TABLE . " WHERE " . LINK_TABLE . ".DID='" . abs($db->f(
								"ID")) . "' AND " . LINK_TABLE . ".Name='".$db2->escape($name)."' AND " . CONTENT_TABLE . ".ID = " . LINK_TABLE . ".CID");
					$namefield = ($db2->next_record()) ? $db2->f("Dat") : $db->f("Text");
			} else {
				$namefield = $db->f("Text");
			}
			array_push($files,array("properties" => $db->Record, "sort" => $sortfield, "name" => $namefield));
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
	$out = '';

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
				'<?php if("' . $GLOBALS["WE_MAIN_DOC"]->ID . '" == "' . $id . '"){ ?>',
				$foo);
		$foo = ereg_replace('</we:ifSelf[^>]*>', '<?php } ?>', $foo);
		$foo = ereg_replace(
				'<we:ifNotSelf[^>]*>',
				'<?php if("' . $GLOBALS["WE_MAIN_DOC"]->ID . '" != "' . $id . '"){ ?>',
				$foo);
		$foo = ereg_replace('</we:ifNotSelf[^>]*>', '<?php } ?>', $foo);
		$foo = ereg_replace('</we:else[^>]*>', '<?php }else{ ?>', $foo);
		$foo = ereg_replace('<we:else[^/]*/>', '<?php }else{ ?>', $foo);

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
}
