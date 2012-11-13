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
class weContentProvider{

	static function getInstance($we_ContentType, $ID = "", $table = ""){
		$we_doc = "";

		if($ID != ""){
			$we_ID = $ID;
		}
		switch($we_ContentType){
			case "doctype":
				$we_doc = new we_docTypes();
				if($ID != ""){
					$we_doc->initByID($ID, $we_doc->Table);
				}
				break;
			case "category":
				$we_doc = new we_category();
				$we_doc->load($ID);
				break;
			case "weNavigation":
				$we_doc = new weNavigation();
				$we_doc->we_load($ID);
				break;
			case "weNavigationRule":
				$we_doc = new weNavigationRule();
				$we_doc->we_load($ID);
				break;
			case "weThumbnail":
				$we_doc = new we_thumbnailEx();
				$we_doc->we_load($ID);
				break;
			case "weTable":
				$we_doc = new weTable($table);
				break;
			case "weTableItem":
				$we_doc = new weTableItem($table);
				if(!empty($ID))
					$we_doc->load($ID);
				break;

			case "weBinary":
				$we_doc = new weBinary();
				$we_doc->load($ID, false);
				break;
			case "weVersion":
				$we_doc = new weVersion();
				$we_doc->load($ID, false);
				break;
			// fix for classes
			case "object":
				if(defined("OBJECT_TABLE")){
					$we_doc = new we_objectEx();
					$we_doc->initByID($ID, OBJECT_TABLE);
				}
				break;
			// fix ends ------------------------------------------------
			default:
				if($we_ContentType == "folder" && !empty($table)){
					$we_Table = $table;
				} else if($we_ContentType == "text/weTmpl")
					$we_Table = TEMPLATES_TABLE;
				else if($we_ContentType == "object" && defined("OBJECT_TABLE"))
					$we_Table = OBJECT_TABLE;
				else if($we_ContentType == "objectFile" && defined("OBJECT_FILES_TABLE"))
					$we_Table = OBJECT_FILES_TABLE;
				else
					$we_Table = FILE_TABLE;

				if(($we_ContentType == "object" && !defined("OBJECT_TABLE")) || ($we_ContentType == "objectFile" && !defined("OBJECT_FILES_TABLE")))
					return $we_doc;

				include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');
		}

		return $we_doc;
	}

	static function populateInstance(&$object, $content){
		if(!isset($object)){
			return;
		}
		foreach($content as $k => $v){
			$object->$k = $v;
		}
		if(isset($object->persistent_slots) && empty($object->persistent_slots)){
			$object->persistent_slots = array_keys($content);
		}
	}

	static function getTagName($object){
		switch((isset($object->Pseudo) ? $object->Pseudo : $object->ClassName)){
			case "we_template":
				return "we:template";
			case "we_element":
				return "we:content";
			case "we_object":
				return "we:class";
			case "we_objectFile":
				return "we:object";
			case "we_docTypes":
				return "we:doctype";
			case "we_category":
				return "we:category";
			case "weTable":
				return "we:table";
			case "weTableAdv":
				return "we:tableadv";
			case "weTableItem":
				return "we:tableitem";
			case "weBinary":
				return "we:binary";
			case "weNavigation":
				return "we:navigation";
			case "weNavigationRule":
				return "we:navigationrule";
			case "we_thumbnailEx":
				return "we:thumbnail";
			default:
				return "we:document";
		}
	}

	static function needCoding($classname, $prop){
		if($prop == "schedArr"){
			return true;
		}
		$encoded = array(
			'we_element' => array('Dat', 'dat'),
			'weTableItem' => array('Dat', 'strFelder', 'strSerial', 'DocumentObject',
				'QASet', 'QASetAdditions', 'Catfields', 'RevoteUserAgent', 'agent',
				'LogData', 'strSerialOrder',
				'documentElements', 'documentScheduler', 'documentCustomFilter'//tblVersions
			),
			'we_object' => array('DefaultText', 'DefaultValues', 'SerializedArray'),
			'we_objectFile' => array('DefArray', 'schedArr'),
			'weBinary' => array('Data'),
			'weVersion' => array('Data',),
			'we_category' => array('Catfields'),
			'weNavigation' => array('Sort', 'Attributes')
		);

		return (isset($encoded[$classname]) ? in_array($prop, $encoded[$classname]) : false);
	}

	static function noEncodingChange($classname, $prop, $wedocClass, $objectname){

		$nocoding = array(
			'we_object' => array('DefaultText', 'DefaultValues', 'SerializedArray'),
			'weBinary' => array('Data')
		);
		$nocoding2 = array(
			'we_element' => array('Dat', 'dat')
		);
		$nocodingDocClasses = array(
			'we_imageDocument',
			'we_flashDocument',
			'we_quicktimeDocument',
			'we_otherDocument'
		);
		if(isset($nocoding[$classname])){
			return in_array($prop, $nocoding[$classname]);
		}
		if(in_array($wedocClass[0], $nocodingDocClasses) && $objectname == "data" && isset($nocoding2[$classname])){
			return in_array($prop, $nocoding2[$classname]);
		}
		return false;
	}

	static function needCdata($classname, $prop, $content){
		/* 		$encoded = array(
		  "we_element" => array("Dat"),
		  "we_object" => array("DefaultText", "DefaultValues"),
		  "weTableItem" => array("Text", "BText", "answertext"),
		  "we_category" => array("Catfields")
		  ); */
		return strpos($content, '<') !== FALSE || strpos($content, '>') !== FALSE || strpos($content, '&') !== FALSE;
		/*
		  if($classname == "weTableItem"){
		  return !(is_numeric($content) || $content === '');
		  }
		  return (isset($encoded[$classname])) && in_array($prop, $encoded[$classname]); */
	}

	static function needSerialize(&$object, $classname, $prop){
		if($prop == "schedArr")
			return true;
		$serialize = array(
			"we_object" => array("SerializedArray"),
			"we_objectFile" => array("DefArray", "schedArr")
		);

		if($prop == 'Dat' && $classname == 'we_element' && defined('WE_SHOP_VARIANTS_ELEMENT_NAME') && $object->Name == WE_SHOP_VARIANTS_ELEMENT_NAME){
			// exception for shop - handling arrays in the content
			return true;
		} else if(isset($serialize[$classname])){
			return in_array($prop, $serialize[$classname]);
		} else{
			return false;
		}
	}

	static function isExportable(&$object, $prop){
		$classname = (isset($object->Pseudo) ? $object->Pseudo : $object->ClassName);

		if(isset($object->table) && $object->table == CONTENT_TABLE){
			return (!$this->IsBinary);
		}

		$noexport = array(); //future use
		return (isset($noexport[$classname]) ?
				!in_array($prop, $noexport[$classname]) :
				true);
	}

	static function binary2file(&$object, $file, $isWe = true){
		$attribs = '';
		foreach($object->persistent_slots as $k => $v){
			if($v != "Data" && $v != "SeqN"){
				if(isset($object->$v)){
					$content = $object->$v;
				}
				if(self::needCoding($object->ClassName, $v)){
					$content = self::getCDATA(self::encode($content));
				} else if(self::needCdata($object->ClassName, $v, $content)){
					$content = self::getCDATA($content);
				}
				$attribs .= weXMLComposer::we_xmlElement($v, $content);
			}
		}

		if(isset($object->Data)){
			$offset = 0;
			$rsize = 1048576;
			do{
				//prefer doc_root over site.
				//FIXME: this must be changed, if parking of documents is implemented
				$path = $_SERVER['DOCUMENT_ROOT'] . $object->Path;
				if(!file_exists($path)){
					$path = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $object->Path;
				}
				$data = weFile::loadPart($path, $offset, $rsize);
				if(!empty($data)){
					fwrite($file, '<we:binary>' . $attribs .
						weXMLComposer::we_xmlElement('SeqN', $object->SeqN) .
						weXMLComposer::we_xmlElement('Data', self::encode($data)) .
						'</we:binary><!-- webackup -->' . "\n");
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

	static function version2file(&$object, $file, $isWe = true){
		$attribs = '';
		foreach($object->persistent_slots as $k => $v){
			if($v != "Data" && $v != "SeqN"){
				if(isset($object->$v))
					$content = $object->$v;
				if(self::needCoding($object->ClassName, $v)){
					$content = self::getCDATA(self::encode($content));
				} else if(self::needCdata($object->ClassName, $v, $content)){
					$content = self::getCDATA($content);
				}
				$attribs .= weXMLComposer::we_xmlElement($v, $content);
			}
		}

		if(isset($object->Data)){
			$offset = 0;
			$rsize = 1048576;
			do{

				$path = $_SERVER['DOCUMENT_ROOT'] . $object->Path;
				if($object->Path == ""){
					break;
				}
				$data = weFile::loadPart($path, $offset, $rsize);

				if(!empty($data)){
					fwrite($file, '<we:version>' . $attribs .
						weXMLComposer::we_xmlElement('SeqN', $object->SeqN) .
						weXMLComposer::we_xmlElement('Data', self::encode($data)) .
						'</we:version><!-- webackup -->' . "\n");
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

	private static function objectMetadata($obj){
		static $hash = array();
		if(isset($hash[$obj])){
			return $hash[$obj];
		}
		$db = new DB_WE();
		$hash[$obj] = $db->metadata($obj);
		return $hash[$obj];
	}

	static function object2xml(&$object, $file, $attribs = array()){
		$classname = (isset($object->Pseudo) ? $object->Pseudo : $object->ClassName);

		switch($classname){
			case "we_category":
			case "weNavigation":
			case "weNavigationRule":
			case "we_thumbnailEx":
				$object->persistent_slots = array_merge(array("ClassName"), $object->persistent_slots);
				break;
			default:
				break;
		}

		//write tag name
		$write = '<' . self::getTagName($object) . (!empty($attribs) ? weXMLComposer::buildAttributesFromArray($attribs) : '') . '>';

		// fix for classes; insert missing field length into default values ---
		switch($classname){
			case 'we_object':
				$tableInfo = self::objectMetadata(OBJECT_X_TABLE . $object->ID);
				$defvalues = unserialize($object->DefaultValues);
				for($i = 0; $i < count($tableInfo); $i++){
					$fieldname = $tableInfo[$i]['name'];
					if(isset($defvalues[$fieldname])){
						$defvalues[$fieldname]['length'] = ($tableInfo[$i]['len'] > 255) ? 255 : $tableInfo[$i]['len'];
					}
				}
				$object->DefaultValues = serialize($defvalues);
				break;
			// fix ends -----------------------------------------------------------

			case 'we_webEditionDocument':
				$object->TemplatePath = clearPath('/' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $object->TemplatePath));
				break;
		}

		if(isset($object->Table)){
			$object->Table = strtolower(stripTblPrefix($object->Table));
		}


		foreach($object->persistent_slots as $k => $v){
			if($v != "elements"){
				$content = (isset($object->$v) ? $object->$v : '');

				if(self::needSerialize($object, $classname, $v)){
					$content = serialize($content);
				}


				if(self::needCoding($classname, $v)){
					if(!is_array($content)){
						$content = self::encode($content);
					}
				} else if(self::needCdata($classname, $v, $content)){
					$content = self::getCDATA($content);
				}
				$write.=weXMLComposer::we_xmlElement($v, $content);
			}
		}
		fwrite($file, $write);

		if(isset($object->elements) && $object->ClassName != "we_object"){
			$elements_ids = array_keys($object->elements);

			foreach($elements_ids as $ck){
				if($object->ClassName == "weTable" || $object->ClassName == "weTableAdv"){
					if($object->ClassName == "weTablea"){
						$contentObj = new we_element(false, $object->elements[$ck]);
					} else{
						array_unshift($object->elements[$ck], ' ');
						$contentObj = new we_element(false, $object->elements[$ck]);
						foreach($object->elements[$ck] as $okey => $ov){
							$contentObj->$okey = trim($ov);
						};
					}
				} else{
					$options = array(
						"ClassName" => "we_element",
						"Name" => $ck,
						"Dat" => isset($object->elements[$ck]["dat"]) ? $object->elements[$ck]["dat"] : ""
					);

					if(isset($object->elements[$ck]["type"])){
						$options["Type"] = $object->elements[$ck]["type"];
					}
					if(isset($object->elements[$ck]["len"])){
						$options["Len"] = $object->elements[$ck]["len"];
					}
					if(isset($object->elements[$ck]["bdid"])){
						$options["BDID"] = $object->elements[$ck]["bdid"];
					}

					$contentObj = new we_element(false, $options);
				}

				self::object2xml($contentObj, $file);
			}
			unset($elements_ids);
			unset($contentObj);
			//$out.=$elements_out;
		}

		//return $out;
		fwrite($file, '</' . self::getTagName($object) . '>');
	}

	static function file2xml($file, $fh){

		$bin = self::getInstance('weBinary', 0);
		$bin->Path = $file;

		self::binary2file($bin, $fh, false);
	}

	static function xml2object(&$object){
		switch($object->ClassName){
			case "we_template":
				break;
			case "we_objectFile":
				break;
			case "we_object":
				break;
			default:
		}
	}

	static function isBinary($id){
		return f("SELECT 1 AS a FROM " . FILE_TABLE . " WHERE ID=" . intval($id) . " AND ContentType='image/*' OR ContentType LIKE 'application/%';", "a", new DB_WE());
	}

	static function getCDATA($data){
		return sprintf("<![CDATA[%s]]>", $data);
	}

	static function encode($data){
		return base64_encode($data);
	}

	static function decode($data){
		return base64_decode($data);
	}

	static function getContentTypeHandler($contenttype){
		switch($contenttype){
			case "category":
				return "weModelBase";
			case "text/weTmpl":
				return "we_template";
			case "doctype":
				return "we_docTypes";
			default:
				return $contenttype;
		}
	}

}
