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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
abstract class we_glossary_treeLoader{

	public static function getItems($ParentId, $Offset = 0, $Segment = 500){
		$Types = array(
			we_glossary_glossary::TYPE_ABBREVATION,
			we_glossary_glossary::TYPE_ACRONYM,
			we_glossary_glossary::TYPE_FOREIGNWORD,
			we_glossary_glossary::TYPE_LINK,
			we_glossary_glossary::TYPE_TEXTREPLACE,
		);

		$Temp = explode('_', $ParentId);

		if(in_array($Temp[(count($Temp) - 1)], $Types)){
			$Type = array_pop($Temp);
			$Language = implode('_', $Temp);
			return self::getItemsFromDB($Language, $Type, $Offset, $Segment);
		}
		if(in_array($ParentId, $GLOBALS['weFrontendLanguages'])){
			return self::getTypes($ParentId);
		}
		return self::getLanguages();
	}

	private static function getLanguages(){
		$Items = array();

		foreach(getWeFrontendLanguagesForBackend() as $Key => $Val){
			$Items[] = array(
				'id' => $Key,
				'parentid' => 0,
				'text' => $Val,
				'typ' => 'group',
				'open' => 0,
				'disabled' => 0,
				'tooltip' => $Val,
				'offset' => 0,
				'published' => 1,
				'cmd' => "glossary_view_folder",
			);
		}

		return $Items;
	}

	private static function getTypes($Language){

		$Items = array();

		$Types = array(
			we_glossary_glossary::TYPE_ABBREVATION => g_l('modules_glossary', '[abbreviation]'),
			we_glossary_glossary::TYPE_ACRONYM => g_l('modules_glossary', '[acronym]'),
			we_glossary_glossary::TYPE_FOREIGNWORD => g_l('modules_glossary', '[foreignword]'),
			we_glossary_glossary::TYPE_LINK => g_l('modules_glossary', '[link]'),
			we_glossary_glossary::TYPE_TEXTREPLACE => g_l('modules_glossary', '[textreplacement]'),
		);

		foreach($Types as $Key => $Val){
			$Items[] = array(
				'id' => $Language . "_" . $Key,
				'parentid' => $Language,
				'text' => $Val,
				'typ' => 'group',
				'open' => 0,
				'disabled' => 0,
				'tooltip' => $Val,
				'offset' => 0,
				'published' => 1,
				'cmd' => 'glossary_view_type',
			);
		}

		if(permissionhandler::hasPerm("EDIT_GLOSSARY_DICTIONARY")){
			$Items[] = array(
				'id' => $Language . "_exception",
				'parentid' => $Language,
				'text' => g_l('modules_glossary', '[exception]'),
				'typ' => 'item',
				'open' => 0,
				'disabled' => 0,
				'tooltip' => g_l('modules_glossary', '[exception]'),
				'offset' => 0,
				'published' => 1,
				'cmd' => 'glossary_view_exception',
				'Icon' => 'prog.gif'
			);
		}

		return $Items;
	}

	private static function getItemsFromDB($Language, $Type, $Offset = 0, $Segment = 500){

		$Db = new DB_WE();

		$Items = array();

		$Where = " WHERE Language='" . $Db->escape($Language) . "' AND Type='" . $Db->escape($Type) . "'";

		$PrevOffset = max(0,$Offset - $Segment);

		if($Offset && $Segment){
			$Item = array(
				"id" => "prev_" . $Language . "_" . $Type,
				"parentid" => $Language . "_" . $Type,
				"text" => "display (" . $PrevOffset . "-" . $Offset . ")",
				"contenttype" => "arrowup",
				"table" => GLOSSARY_TABLE,
				"typ" => "threedots",
				"icon" => "arrowup.gif",
				"open" => 0,
				"disabled" => 0,
				"tooltip" => "",
				"offset" => $PrevOffset,
			);
			$Items[] = $Item;
		}

		$Db->query('SELECT ID, Type, Language, Text, Icon, abs(Text) AS Nr, (Text REGEXP "^[0-9]") AS isNr, Published FROM ' . GLOSSARY_TABLE . ' ' .
			$Where . ' ORDER BY isNr DESC, Nr, Text ' .
			($Segment ? 'LIMIT ' . intval($Offset) . ',' . intval($Segment) : ''));

		while($Db->next_record(MYSQL_ASSOC)){

			$Item = array(
				'id' => $Db->f('ID'),
				'parentid' => $Language . "_" . $Type,
				'text' => $Db->f('Text'),
				'typ' => 'item',
				'open' => 0,
				'disabled' => 0,
				'tooltip' => $Db->f('ID'),
				'offset' => $Offset,
				'published' => ($Db->f('Published') > 0 ? true : false),
				'icon' => $Db->f('Icon'),
			);

			switch($Type){

				case we_glossary_glossary::TYPE_ABBREVATION:
					$Item['cmd'] = "glossary_edit_abbreviation";
					break;
				case we_glossary_glossary::TYPE_ACRONYM:
					$Item['cmd'] = "glossary_edit_acronym";
					break;
				case we_glossary_glossary::TYPE_FOREIGNWORD:
					$Item['cmd'] = "glossary_edit_foreignword";
					break;
				case we_glossary_glossary::TYPE_LINK:
					$Item['cmd'] = "glossary_edit_link";
					break;
				case we_glossary_glossary::TYPE_TEXTREPLACE:
					$Item['cmd'] = "glossary_edit_textreplacement";
					break;
			}

			foreach($Db->Record as $Key => $Val){
				$Item[strtolower($Key)] = (strtolower($Key) === 'text' ? oldHtmlspecialchars($Val) : $Val);
			}

			$Items[] = $Item;
		}

		$Total = f('SELECT COUNT(1) FROM ' . $Db->escape(GLOSSARY_TABLE) . ' ' . $Where, '', $Db);

		$NextOffset = $Offset + $Segment;
		if($Segment && ($Total > $NextOffset)){
			$Items[] = array(
				"id" => 'next_' . $Language . "_" . $Type,
				"parentid" => $Language . "_" . $Type,
				"text" => "display (" . $NextOffset . "-" . ($NextOffset + $Segment) . ")",
				"contenttype" => "arrowdown",
				"table" => GLOSSARY_TABLE,
				"typ" => "threedots",
				"icon" => "arrowdown.gif",
				"open" => 0,
				"disabled" => 0,
				"tooltip" => "",
				"offset" => $NextOffset,
			);
		}

		return $Items;
	}

}
