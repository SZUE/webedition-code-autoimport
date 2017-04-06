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
abstract class we_import_functions{
	const TYPE_CSV = 'CSV';
	const TYPE_XML = 'XML';
	const TYPE_WE = 'WE';
	const TYPE_LOCAL_FILES = 'FileImport';
	const TYPE_SITE = 'siteImport';

	/**
	 * @return boolean
	 * @param integer $parentID
	 * @param integer $templateID
	 * @param array $fields
	 * @param integer $doctypeID
	 * @param string $categories
	 * @param string $filename
	 * @param boolean $isDynamic
	 * @param string $extension
	 * @param boolean $publish
	 * @param boolean $IsSearchable
	 * @desc imports a document into webedition
	 */
	public static function importDocument($parentID, $templateID, $fields, $doctypeID, $categories, $filename, $isDynamic, $extension, $publish, $IsSearchable, $charset, $conflict = 'rename'){

		// erzeugen eines neuen webEdition-Dokument-Objekts
		$GLOBALS['we_doc'] = new we_webEditionDocument();

		$GLOBALS['we_doc']->we_new();

		$GLOBALS['we_doc']->Extension = $extension;
		if($filename){
			$filename = self::correctFilename($filename);
			$GLOBALS['we_doc']->Filename = $filename;
		}
		$GLOBALS['we_doc']->Text = $GLOBALS['we_doc']->Filename . $GLOBALS['we_doc']->Extension;

		$GLOBALS['we_doc']->setParentID($parentID);
		if($charset){
			$GLOBALS['we_doc']->setElement('Charset', $charset, 'dat');
		}
		$GLOBALS['we_doc']->Path = $GLOBALS['we_doc']->getParentPath() . (($GLOBALS['we_doc']->getParentPath() != "/") ? "/" : "") . $GLOBALS['we_doc']->Text;
		// IF NAME OF OBJECT EXISTS, WE HAVE TO CREATE A NEW NAME
		if(($file_id = f('SELECT ID FROM ' . FILE_TABLE . ' WHERE Path="' . $GLOBALS['DB_WE']->escape($GLOBALS['we_doc']->Path) . '"'))){
			if($conflict === 'rename'){
				$z = 0;
				$footext = $GLOBALS['we_doc']->Filename . "_" . $z . $GLOBALS['we_doc']->Extension;
				while(f('SELECT ID FROM ' . FILE_TABLE . ' WHERE Text="' . $GLOBALS['DB_WE']->escape($footext) . '" AND ParentID=' . intval($GLOBALS['we_doc']->ParentID))){
					$z++;
					$footext = $GLOBALS['we_doc']->Filename . "_" . $z . $GLOBALS['we_doc']->Extension;
				}
				$GLOBALS['we_doc']->Filename = $GLOBALS['we_doc']->Filename . "_" . $z;

				$GLOBALS['we_doc']->Text = $footext;
				$GLOBALS['we_doc']->Path = $GLOBALS['we_doc']->getParentPath() . (($GLOBALS['we_doc']->getParentPath() != "/") ? "/" : "") . $GLOBALS['we_doc']->Text;
			} else if($conflict === 'replace'){
				$GLOBALS['we_doc']->initById($file_id);
			} else {
				return true;
			}
		}

		$GLOBALS['we_doc']->DocType = $doctypeID;
		$GLOBALS['we_doc']->setTemplateID($templateID);
		$GLOBALS['we_doc']->Category = $categories;

		$GLOBALS['we_doc']->ContentType = we_base_ContentTypes::WEDOCUMENT;

		$GLOBALS['we_doc']->IsDynamic = $isDynamic;
		$GLOBALS['we_doc']->IsSearchable = $IsSearchable;
		foreach($fields as $fieldName => $fieldValue){
			$GLOBALS['we_doc']->setElement($fieldName, $fieldValue);
		}

		// SAVE DOCUMENT
		if(!$GLOBALS['we_doc']->we_save()){
			return false;
		}
		// PUBLISH OR EXIT
		return ($publish ?
				$GLOBALS['we_doc']->we_publish() :
				true
			);
	}

	/**
	 * @return boolean
	 * @param integer $classID
	 * @param array $fields
	 * @param string $categories
	 * @param string $filename
	 * @param boolean $publish
	 * @desc imports an object into webEdition
	 */
	public static function importObject($classID, $fields, $categories, $filename, $publish, $issearchable, $parentID = 0, $charset = '', $conflict = 'rename'){

		// INIT OBJECT
		$object = new we_objectFile();
		$object->we_new();
		$object->TableID = $classID;
		$object->setRootDirID(true);
		$object->resetParentID();
		$object->restoreDefaults();
		if($categories){
			$object->Category = $categories;
		}
		$object->IsSearchable = $issearchable;
		if($charset){
			$object->Charset = $charset;
		}
		if($parentID){
			$object->setParentID($parentID);
		}

		// IF WE HAVE TO GIVE THE OBJECT A NAME
		if($filename || $filename == 0){
			$name_exists = false;
			$filename = we_import_functions::correctFilename($filename);
			$object->Text = $filename;
			$object->Path = $object->getParentPath() . (($object->getParentPath() != '/') ? '/' : '') . $object->Text;
			// IF NAME OF OBJECT EXISTS, WE HAVE TO CREATE A NEW NAME
			if(($file_id = f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path="' . $GLOBALS['DB_WE']->escape($object->Path) . '"'))){
				$name_exists = true;
				switch($conflict){
					case 'replace':
						$object->initByID($file_id, OBJECT_FILES_TABLE);
						break;
					case 'rename':
						$z = 0;
						$footext = $object->Text . '_' . $z;
						while(f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Text="' . $GLOBALS['DB_WE']->escape($footext) . '" AND ParentID=' . intval($object->ParentID))){
							$z++;
							$footext = $object->Text . '_' . $z;
						}
						$object->Text = $footext;
						$object->Path = $object->getParentPath() . (($object->getParentPath() != '/') ? '/' : '') . $object->Text;
						break;
					default:
						return true;
				}
			}
		}

		// FILL FIELDS OF OBJECT
		foreach($fields as $fieldName => $fieldValue){
			$object->setElement($fieldName, $fieldValue);
		}
		// SAVE OBJECT
		if(!$object->we_save()){
			return false;
		}
		// PUBLISH OR EXIT
		return ($publish ?
				$object->we_publish() :
				true);
	}

	/**
	 * @return string
	 * @param string $filename
	 * @desc corrects the filename if it contains invalid chars
	 */
	static function correctFilename($filename, $allowPath = false){
		$filename = preg_replace('%[^a-z0-9\._+\-@' . ($allowPath ? '/' : '') . ']%i', '', trim(correctUml($filename), '/'));

		if(!$allowPath && strlen($filename) > 100){
			$pos = strrpos($filename, '.');
			$ext = substr($filename, $pos + 1, 16);
			$name = substr($filename, 0, min($pos, 100 - strlen($ext)));
			$filename = $name . '.' . $ext;
		}
		return $filename ? : 'newfile';
	}

	/**
	 * @return int
	 * @param string $datestring
	 * @param string $format
	 * @desc converts a $datestring which represent a date to an unix timestamp with the given $format. If $format is empty, $datestring has to be a valid English date format
	 */
	static function date2Timestamp($datestring, $format = ""){
		if(!$format){
			return strtotime($datestring);
		}

		$replaceorder = [];

		$formatchars = ['Y', 'y', 'm', 'n', 'd', 'j', 'H', 'G', 'i', 's'];

		$eregchars = implode('', $formatchars);

		foreach($formatchars as $char){
			$format = str_replace("\\" . $char, "###we###" . ord($char) . "###we###", $format);
		}
		$matches = [];
		if(preg_match_all('/[' . $eregchars . ']/', $format, $matches, PREG_SET_ORDER)){
			foreach($matches as $match){
				if(is_array($match) && isset($match[0])){
					$replaceorder[] = $match[0];
				}
			}
		}

		$eregformat = preg_replace("/([$eregchars])/", "([0-9]+)", str_replace("/", "\\/", preg_quote($format)));

		foreach($formatchars as $char){
			$eregformat = str_replace("###we###" . ord($char) . "###we###", "\\" . $char, $eregformat);
		}

		$outarray = ["hour" => 1,
			"minute" => 0,
			"second" => 0,
			"month" => 1,
			"day" => 1,
			"year" => 1970
			];

		if(preg_match_all('/' . $eregformat . '/', $datestring, $matches, PREG_SET_ORDER)){

			if(isset($matches[0]) && is_array($matches[0])){
				for($i = 1; $i < count($matches[0]); $i++){
					if(isset($replaceorder[$i - 1])){
						switch($replaceorder[$i - 1]){
							case "y":
							case "Y":
								$outarray["year"] = $matches[0][$i];
								break;
							case "m":
							case "n":
								$outarray["month"] = $matches[0][$i];
								break;
							case "d":
							case "j":
								$outarray["day"] = $matches[0][$i];
								break;
							case "H":
							case "G":
								$outarray["hour"] = $matches[0][$i];
								break;
							case "i":
								$outarray["minute"] = $matches[0][$i];
								break;
							case "s":
								$outarray["second"] = $matches[0][$i];
								break;
						}
					}
				}
			}

			return mktime($outarray["hour"], $outarray["minute"], $outarray["second"], $outarray["month"], $outarray["day"], $outarray["year"]);
		}
		return 0;
	}

}
