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
class we_exim_contentProvider{
	const CODING_ENCODE = 'encode';
	const CODING_SERIALIZE = 'serial';
	const CODING_ATTRIBUTE = 'coding';
	const CODING_NONE = null;

	static function getInstance($we_ContentType, $ID = '', $table = ''){
		$we_doc = '';
		if($ID){
			//used in include!
			$we_ID = $ID;
		}
		switch($we_ContentType){
			case 'doctype':
				$we_doc = new we_docTypes();
				if($ID){
					$we_doc->initByID($ID, $we_doc->Table);
				}
				return $we_doc;
			case 'category':
				$we_doc = new we_category();
				$we_doc->load($ID);
				return $we_doc;
			case 'weNavigation':
				$we_doc = new we_navigation_navigation();
				$we_doc->we_load($ID);
				return $we_doc;
			case 'weNavigationRule':
				$we_doc = new we_navigation_rule();
				$we_doc->we_load($ID);
				return $we_doc;
			case 'weThumbnail':
				$we_doc = new we_exim_thumbnailExport();
				$we_doc->we_load($ID);
				return $we_doc;
			case 'we_backup_tableAdv':
			case 'we_backup_table':
				$we_doc = new we_backup_tableAdv($table);
				return $we_doc;
			case 'we_backup_tableItem':
				$we_doc = new we_backup_tableItem($table);
				if($ID){
					$we_doc->load($ID);
				}
				return $we_doc;

			case 'we_backup_binary':
			case 'weBinary'://FIXME: remove
				$we_doc = new we_backup_binary();
				$we_doc->load($ID, false);
				return $we_doc;
			case 'weVersion':
				$we_doc = new we_backup_version();
				$we_doc->load($ID, false);
				return $we_doc;
			// fix for classes
			case 'object':
				if(defined('OBJECT_TABLE')){
					$we_doc = new we_backup_object();
					$we_doc->initByID($ID, OBJECT_TABLE);
				}
				return $we_doc;
			// fix ends ------------------------------------------------
		}

		switch($we_ContentType){
			case we_base_ContentTypes::FOLDER:
				$we_Table = $table ? : FILE_TABLE;
				break;
			case we_base_ContentTypes::TEMPLATE:
				$we_Table = TEMPLATES_TABLE;
				break;
			case we_base_ContentTypes::OBJECT:
				if(!defined('OBJECT_TABLE')){
					return '';
				}
				$we_Table = OBJECT_TABLE;
				break;
			case we_base_ContentTypes::OBJECT_FILE:
				if(!defined('OBJECT_FILES_TABLE')){
					return '';
				}
				$we_Table = OBJECT_FILES_TABLE;
				break;
			default:
				$we_Table = FILE_TABLE;
		}
		$dontMakeGlobal = true;
		include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

		return $we_doc;
	}

	static function populateInstance(&$object, $content){
		if(!isset($object)){
			return;
		}
		$reflect = new ReflectionClass($object);
		$props = $reflect->getProperties(ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED);
		foreach($props as $prop){
			unset($content[$prop->getName()]);
		}

		foreach($content as $k => $v){
			$object->$k = $v;
		}
		if(isset($object->persistent_slots) && empty($object->persistent_slots)){
			$object->persistent_slots = array_keys($content);
		}
	}

	static function getTagName($object){
		switch(get_class($object)){
			case 'we_template':
				return 'we:template';
			case 'we_element':
				return 'we:content';
			case 'we_object':
				return 'we:class';
			case 'we_objectFile':
				return 'we:object';
			case 'we_docTypes':
				return 'we:doctype';
			case 'we_category':
				return 'we:category';

			case 'we_backup_tableAdv'://for backup
				return 'we:tableadv';
			case 'we_backup_tableItem'://for backup
				return 'we:tableitem';
			case 'we_backup_binary':
			case 'weBinary'://fixme: remove
				return 'we:binary';
			case 'we_navigation_navigation':
				return 'we:navigation';
			case 'we_navigation_rule':
				return 'we:navigationrule';
			case 'we_thumbnail':
			case 'we_thumbnailEx':
				return 'we:thumbnail';
			default:
				return 'we:document';
		}
	}

	static function needCoding($prop, $data){
		if($prop === 'schedArr'){
			return true;
		}

		return preg_match('!(^[asO]:\d+:)|([\x0-\x08\x0e-\x19\x11\x12<>&\x7F-\xFF])!', $data); //exclude x9:\t,x10:\n,x13:\r,x20:space
	}

	static function needCdata($content){
		return preg_match('-[<>&]-', $content);
	}

	static function needSerialize(&$object, $classname, $prop){
		if(!isset($object->$prop)){
			return false;
		}
		if($prop === 'schedArr' || is_array($object->$prop)){
			return true;
		}
		$serialize = array(
			'we_object' => array('SerializedArray'),
			'we_objectFile' => array('DefArray', 'schedArr')
		);

		if($prop === 'Dat' && $classname === 'we_element' && $object->Name == we_base_constants::WE_VARIANTS_ELEMENT_NAME){
			// exception for shop - handling arrays in the content
			return true;
		}
		if(isset($serialize[$classname])){
			return in_array($prop, $serialize[$classname]);
		}
		return false;
	}

	static function binary2file(&$object, $file, $fwrite = 'fwrite', $tag = 'we:binary'){
		$attribs = '';
		foreach($object->persistent_slots as $v){
			switch($v){
				case 'Data':
				case 'SeqN':
					break;
				default:
					$coding = self::CODING_NONE;
					if(isset($object->$v)){
						$content = $object->$v;
					}
					if(self::needCoding($v, $content) || self::needCdata($content) || self::needSerialize($object->ClassName, $v, $content)){//fix for faulty parser
						$content = self::encode($content);
						$coding = array(self::CODING_ATTRIBUTE => self::CODING_ENCODE);
					}
					$attribs .= we_xml_composer::we_xmlElement($v, $content, $coding);
			}
		}

		if(isset($object->Path)){
			$offset = 0;
			$rsize = 1048576;
			do{
				//prefer doc_root over site.
				//FIXME: this must be changed, if parking of documents is implemented
				$path = $_SERVER['DOCUMENT_ROOT'] . $object->Path;
				if($object->Path == ''){
					break;
				}
				if(!file_exists($path)){
					$path = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $object->Path;
				}
				$data = we_base_file::loadPart($path, $offset, $rsize);
				if($data){
					$fwrite($file, '<' . $tag . '>' . $attribs .
						we_xml_composer::we_xmlElement('SeqN', $object->SeqN) .
						we_xml_composer::we_xmlElement('Data', self::encode($data), array(self::CODING_ATTRIBUTE => self::CODING_ENCODE)) .
						'</' . $tag . '>' . we_backup_util::backupMarker . "\n");
					$offset+=$rsize;
					$object->SeqN++;
				}
				// if offset g.t. filesize then exit
				/* if(filesize($path)<$offset){
				  $data = null;
				  } */
			} while($data);
		}
	}

	static function version2file(&$object, $file, $fwrite = 'fwrite'){
		self::binary2file($object, $file, $fwrite, 'we:version');
	}

	private static function objectMetadata($obj){
		static $hash = array();
		if(isset($hash[$obj])){
			return $hash[$obj];
		}
		$db = new DB_WE();
		$hash[$obj] = $db->metadata($obj);
		return $hash[$obj];
	}

	static function object2xml($object, $file, array $attribs = array(), $fwrite = 'fwrite'){
		$classname = get_class($object);

		switch($classname){
			case 'we_navigation_navigation':
			case 'weNavigation':
				$object->persistent_slots['ClassName'] = we_base_request::STRING;
				break;
			case 'we_navigation_rule':
			case 'weNavigationRule':
			case 'we_category':
			case 'we_thumbnailEx':
			case 'we_thumbnail':
				$object->persistent_slots = array_merge(array('ClassName'), $object->persistent_slots);
				break;
			default:
				break;
		}

		$tagName = self::getTagName($object);

		//write tag name
		$write = '<' . $tagName . ($attribs ? we_xml_composer::buildAttributesFromArray($attribs) : '') . '>';

		// fix for classes; insert missing field length into default values ---
		switch($classname){
			case 'we_object':
				$tableInfo = self::objectMetadata(OBJECT_X_TABLE . $object->ID);
				$defvalues = we_unserialize($object->DefaultValues);
				foreach($tableInfo as $cur){
					$fieldname = $cur['name'];
					if(isset($defvalues[$fieldname])){
						$defvalues[$fieldname]['length'] = ($cur['len'] > 255) ? 255 : $cur['len'];
					}
				}
				$object->DefaultValues = we_serialize($defvalues, 'json');
				break;
			// fix ends -----------------------------------------------------------

			case 'we_webEditionDocument':
				$object->TemplatePath = we_base_file::clearPath('/' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $object->TemplatePath));
				$object->DocTypeName = f('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' dt WHERE dt.ID=' . intval($object->DocType));
				$object->persistent_slots[] = 'DocTypeName';
				break;
		}

		if(isset($object->Table)){
			$object->Table = strtolower(stripTblPrefix($object->Table));
		}


		foreach($object->persistent_slots as $k => $v){
			if(!is_numeric($k)){
				//new persistents have the form name=>type
				$v = $k;
			}
			if($v === 'elements' || $v === 'usedElementNames' || $v === 'nHash'){
				continue;
			}
			if(self::needSerialize($object, $classname, $v)){
				$content = self::encode(we_serialize($content, 'json'));
				$coding = array(self::CODING_ATTRIBUTE => self::CODING_SERIALIZE);
			} else {
				$content = (isset($object->$v) ? $object->$v : '');
				$coding = self::CODING_NONE;
				if(self::needCoding($v, $content) || self::needCdata($content)){//fix for faulty parser
					$content = self::encode($content);
					$coding = array(self::CODING_ATTRIBUTE => self::CODING_ENCODE);
				}
			}
			$write.= we_xml_composer::we_xmlElement($v, $content, $coding);
		}
		$fwrite($file, $write);

		if(isset($object->elements) && $object->ClassName != 'we_object'){
			$elements_ids = array_keys($object->elements);

			foreach($elements_ids as $ck){
				switch($object->ClassName){
					case 'we_backup_tableAdv':
						$contentObj = new we_element(false, $object->elements[$ck]);
						foreach($object->elements[$ck] as $okey => $ov){
							$contentObj->$okey = trim($ov);
						}
						break;

					default:
						$options = array(
							'ClassName' => 'we_element',
							'Name' => $ck,
						);

						if(isset($object->elements[$ck]['dat'])){
							$options['Dat'] = $object->elements[$ck]['dat'];
						}

						if(isset($object->elements[$ck]['type'])){
							$options['Type'] = $object->elements[$ck]['type'];
						}
						if(isset($object->elements[$ck]['len'])){
							$options['Len'] = $object->elements[$ck]['len'];
						}
						if(isset($object->elements[$ck]['bdid'])){
							$options['BDID'] = $object->elements[$ck]['bdid'];
						}

						$contentObj = new we_element(false, $options);
				}

				self::object2xml($contentObj, $file);
			}
			unset($elements_ids, $contentObj);
			//$out.=$elements_out;
		}

		//return $out;
		$fwrite($file, '</' . $tagName . '>');
	}

	static function file2xml($file, $fh){//FIXME: unused?
		$bin = self::getInstance('weBinary', 0);
		$bin->Path = $file;

		self::binary2file($bin, $fh, false);
	}

	static function isBinary($id){
		return f('SELECT 1 FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id) . ' AND ContentType="' . we_base_ContentTypes::IMAGE . '" OR ContentType LIKE "application/%"  LIMIT 1', '', new DB_WE()) == 1;
	}

	static function getCDATA($data){
		return sprintf('<![CDATA[%s]]>', $data);
	}

	static function encode($data){
		return base64_encode($data);
	}

	static function decode($data){
		return base64_decode($data);
	}

	static function getContentTypeHandler($contenttype){
		switch($contenttype){
			case 'category':
				return 'we_base_model';
			case we_base_ContentTypes::TEMPLATE:
				return 'we_template';
			case 'doctype':
				return 'we_docTypes';
			default:
				return $contenttype;
		}
	}

	public static function getDecodedData($type, $data){
		switch($type){
			case self::CODING_ENCODE:
				return self::decode($data);
			case self::CODING_SERIALIZE:
				return self::decode(we_unserialize($data));
			case self::CODING_NONE:
			default:
				return $data;
		}
	}

}
