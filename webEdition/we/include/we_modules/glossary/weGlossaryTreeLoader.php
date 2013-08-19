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
class weGlossaryTreeLoader{

	function getItems($ParentId, $Offset = 0, $Segment = 500, $Sort = ""){

		$Types = array(
			weGlossary::TYPE_ABBREVATION,
			weGlossary::TYPE_ACRONYM,
			weGlossary::TYPE_FOREIGNWORD,
			weGlossary::TYPE_LINK,
			weGlossary::TYPE_TEXTREPLACE,
		);

		$Temp = explode("_", $ParentId);

		if(in_array($Temp[(count($Temp) - 1)], $Types)){
			$Type = array_pop($Temp);
			$Language = implode("_", $Temp);
			return weGlossaryTreeLoader::getItemsFromDB($Language, $Type, $Offset, $Segment);
		} else if(in_array($ParentId, $GLOBALS['weFrontendLanguages'])){
			return weGlossaryTreeLoader::getTypes($ParentId);
		} else {
			return weGlossaryTreeLoader::getLanguages();
		}
	}

	function getLanguages(){

		$Items = array();

		foreach(getWeFrontendLanguagesForBackend() as $Key => $Val){

			$Item = array(
				'id' => $Key,
				'parentid' => 0,
				'text' => $Val,
				'typ' => 'group',
				'open' => 0,
				'disabled' => 0,
				'tooltip' => $Val,
				'offset' => 0,
				'published' => 1,
				'cmd' => "view_folder",
			);

			$Items[] = $Item;
		}

		return $Items;
	}

	function getTypes($Language){

		$Items = array();

		$Types = array(
			weGlossary::TYPE_ABBREVATION => g_l('modules_glossary', '[abbreviation]'),
			weGlossary::TYPE_ACRONYM => g_l('modules_glossary', '[acronym]'),
			weGlossary::TYPE_FOREIGNWORD => g_l('modules_glossary', '[foreignword]'),
			weGlossary::TYPE_LINK => g_l('modules_glossary', '[link]'),
			weGlossary::TYPE_TEXTREPLACE => g_l('modules_glossary', '[textreplacement]'),
		);

		foreach($Types as $Key => $Val){

			$Item = array(
				'id' => $Language . "_" . $Key,
				'parentid' => $Language,
				'text' => $Val,
				'typ' => 'group',
				'open' => 0,
				'disabled' => 0,
				'tooltip' => $Val,
				'offset' => 0,
				'published' => 1,
				'cmd' => 'view_type',
			);

			$Items[] = $Item;
		}

		if(we_hasPerm("EDIT_GLOSSARY_DICTIONARY")){
			$Item = array(
				'id' => $Language . "_exception",
				'parentid' => $Language,
				'text' => g_l('modules_glossary', '[exception]'),
				'typ' => 'item',
				'open' => 0,
				'disabled' => 0,
				'tooltip' => g_l('modules_glossary', '[exception]'),
				'offset' => 0,
				'published' => 1,
				'cmd' => 'view_exception',
				'Icon' => 'prog.gif'
			);

			$Items[] = $Item;
		}

		return $Items;
	}

	function getItemsFromDB($Language, $Type, $Offset = 0, $Segment = 500){

		$Db = new DB_WE();

		$Items = array();

		$Where = " WHERE Language = '" . $Db->escape($Language) . "' AND Type = '" . $Db->escape($Type) . "'";

		$PrevOffset = $Offset - $Segment;
		$PrevOffset = ($PrevOffset < 0) ? 0 : $PrevOffset;

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

		$Query = 'SELECT ID, Type, Language, Text, Icon, abs(Text) AS Nr, (Text REGEXP "^[0-9]") AS isNr, Published FROM ' . GLOSSARY_TABLE . ' ' .
			$Where . ' ORDER BY isNr DESC, Nr, Text ' .
			($Segment ? 'LIMIT ' . intval($Offset) . ',' . intval($Segment) : '');

		$Db->query($Query);
		while($Db->next_record()){

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
				"icon" => $Db->f('Icon'),
			);

			switch($Type){

				case weGlossary::TYPE_ABBREVATION:
					$Item['cmd'] = "edit_glossary_abbreviation";
					break;
				case weGlossary::TYPE_ACRONYM:
					$Item['cmd'] = "edit_glossary_acronym";
					break;
				case weGlossary::TYPE_FOREIGNWORD:
					$Item['cmd'] = "edit_glossary_foreignword";
					break;
				case weGlossary::TYPE_LINK:
					$Item['cmd'] = "edit_glossary_link";
					break;
				case weGlossary::TYPE_TEXTREPLACE:
					$Item['cmd'] = "edit_glossary_textreplacement";
					break;
			}

			foreach($Db->Record as $Key => $Val){
				if(!is_numeric($Key)){
					$Item[strtolower($Key)] = (strtolower($Key) == "text" ? oldHtmlspecialchars($Val) : $Val);
				}
			}

			$Items[] = $Item;
		}

		$Total = f('SELECT COUNT(1) as total FROM ' . $Db->escape(GLOSSARY_TABLE) . ' ' . $Where, 'total', $Db);

		$NextOffset = $Offset + $Segment;
		if($Segment && ($Total > $NextOffset)){
			$Item = array(
				"id" => "next_" . $Language . "_" . $Type,
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

			$Items[] = $Item;
		}

		return $Items;
	}

}
