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
class weBackupImport{
private static $mem=0;
	static function import($filename, &$offset, $lines = 1, $iscompressed = 0, $encoding = 'ISO-8859-1', $log = 0){

		include_once(WE_INCLUDES_PATH . 'we_exim/weXMLExImConf.inc.php');
		if(isset($_SESSION['weS']['weBackupVars']['options']['convert_charset']) && $_SESSION['weS']['weBackupVars']['options']['convert_charset']){
			$data = '<?xml version="1.0" encoding="' . $_SESSION['weS']['weBackupVars']['encoding'] . '" standalone="yes"?>' . $GLOBALS['weXmlExImNewLine'] .
				'<webEdition version="' . WE_VERSION . '" xmlns:we="we-namespace">' . $GLOBALS['weXmlExImNewLine'];
		} else{
			$data = $GLOBALS['weXmlExImHeader'];
		}
		self::$mem=memory_get_usage();
		weBackupUtil::addLog(sprintf('Reading offset %s', $offset));

		if(!weBackupFileReader::readLine($filename, $data, $offset, $lines, 0, $iscompressed)){
			return false;
		}

		$data .= $GLOBALS['weXmlExImFooter'];
		t_e('read',memory_get_usage()-self::$mem);

		self::transfer($data, $encoding, $log);
		return true;
	}

	private static function transfer(&$data, $charset = 'ISO-8859-1', $log = 0){
		$nFactor = 5;

		if($log){
			weBackupUtil::addLog('Parsing data');
		}

		$parser = new weXMLParser();

		//if(isset($_SESSION['weS']['weBackupVars']['options']['convert_charset']) && $_SESSION['weS']['weBackupVars']['options']['convert_charset']){ vor 4092
		if(DEFAULT_CHARSET != ''){// Fix f�r 4092, in Verbindung mit alter Version f�r bug 3412 l�st das beide Situationen
			$parser->parse($data, DEFAULT_CHARSET);
		} else{
			$parser->parse($data);
		}
				t_e('+parser',memory_get_usage()-self::$mem,memory_get_usage());
		// free some memory
		unset($data);
				t_e('+parser-data',memory_get_usage()-self::$mem);

		if($parser === false){
			p_r($parser->parseError);
			if($log){
				weBackupUtil::addLog(print_r($parser->parseError, true));
			}
		}


		$parser->normalize();
				t_e('normalize',memory_get_usage()-self::$mem);

		// set parser on the first child node
		$parser->seek(1);

		do{

			$entity = $parser->getNodeName();
			$attributes = $parser->getNodeAttributes();

			$classname = '';
			$object = '';

			if(self::getObject($entity, $attributes, $object, $classname)){
				$parser->addMark('first');
				$parser->next();
				do{
					$name = $parser->getNodeName();

					//import elements
					if($name == 'we:content'){

						$parser->addMark('second');
						$parser->next();

						do{

							$element_value = $parser->getNodeName();
							if($element_value == 'Field'){
								$element_name = $parser->getNodeData();
							}
							if($element_name){
								$object->elements[$element_name][$element_value] = $parser->getNodeData();
							}
						} while($parser->nextSibling());

						unset($element_name);
						unset($element_value);

						$parser->gotoMark('second');
					} else{
						$attr = $parser->getNodeAttributes();
						if(version_compare($_SESSION['weS']['weBackupVars']['weVersion'], '6.3.3.0', '>')){
							switch(($attr && isset($attr[weContentProvider::CODING_ATTRIBUTE]) ? $attr[weContentProvider::CODING_ATTRIBUTE] : null)){
								case weContentProvider::CODING_ENCODE:
									$object->$name = weContentProvider::decode($parser->getNodeData());
									break;
								case weContentProvider::CODING_SERIALIZE:
									$object->$name = unserialize($parser->getNodeData());
									break;
								case 'enocde':
								case weContentProvider::CODING_NONE:
								default:
									$object->$name = $parser->getNodeData();
							}
						} else{
							// import field
							$object->$name = (weContentProvider::needCoding($classname, $name) ?
									weContentProvider::decode($parser->getNodeData()) :
									$parser->getNodeData()); //original mit Bug #3412 aber diese Version l�st 4092
						}

						if(isset($object->persistent_slots) && !in_array($name, $object->persistent_slots)){
							$object->persistent_slots[] = $name;
						}
					}

					//correct table name in tblversions
					if(isset($object->table) && $object->table == "tblversions" && isset($object->documentTable)){
						if(strtolower(substr($object->documentTable, -14)) == "tblobjectfiles"){
							$object->documentTable = defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'tblobjectfiles';
						}
						if(strtolower(substr($object->documentTable, -7)) == "tblfile"){
							$object->documentTable = defined('FILE_TABLE') ? FILE_TABLE : 'tblfile';
						}
					}
				} while($parser->nextSibling());

				if($log){
					$addtext = '';
					if(isset($_SESSION['weS']['weBackupVars']['options']['convert_charset']) && $_SESSION['weS']['weBackupVars']['options']['convert_charset']){
						$addtext = (method_exists($object, 'convertCharsetEncoding') ?
								" - Converting Charset: " . $_SESSION['weS']['weBackupVars']['encoding'] . " -> " . DEFAULT_CHARSET :
								" - Converting Charset: NO ");
					}
					$_prefix = 'Saving object ';
					switch($classname){
						case 'weTable':
						case 'weTableAdv':
							weBackupUtil::addLog($_prefix . $classname . ':' . $object->table . $addtext);
							break;
						case 'weTableItem':
							$_id_val = '';
							foreach($object->keys as $_key){
								$_id_val .= ':' . $object->$_key;
							}
							weBackupUtil::addLog($_prefix . $classname . ':' . $object->table . $_id_val . $addtext);
							break;
						case 'weBinary':
							weBackupUtil::addLog($_prefix . $classname . ':' . $object->ID . ':' . $object->Path . $addtext);
							break;
					}
				}
				if(isset($_SESSION['weS']['weBackupVars']['options']['convert_charset']) && $_SESSION['weS']['weBackupVars']['options']['convert_charset'] && method_exists($object, 'convertCharsetEncoding')){
					$object->convertCharsetEncoding($_SESSION['weS']['weBackupVars']['encoding'], DEFAULT_CHARSET);
				}
				if(isset($object->Path) && $object->Path == WE_INCLUDES_DIR . 'conf/we_conf_global.inc.php'){
					weBackupImport::handlePrefs($object);
				} else if(defined('SPELLCHECKER') && isset($object->Path) && (strpos($object->Path, WE_MODULES_DIR . 'spellchecker/') === 0) && !$_SESSION['weS']['weBackupVars']['handle_options']['spellchecker']){
					// do nothing
				} else{
					$object->save(true);
				}

				//speedup for some tables
				$_SESSION['weS']['weBackupVars']['backup_steps'] =
					(isset($object->table) && ($object->table == LINK_TABLE || $object->table == CONTENT_TABLE) ?
						BACKUP_STEPS * $nFactor :
						BACKUP_STEPS);

				$parser->gotoMark('first');
			}

			if(isset($object)){
				unset($object);
			}
		} while($parser->nextSibling());
	}

	private static function getObject($tagname, $attribs, &$object, &$classname){
		switch($tagname){
			case 'we:table':
				$table = weBackupUtil::getRealTableName($attribs['name']);
				if($table !== false){
					weBackupUtil::setBackupVar('current_table', $table);
					$object = new weTable($table);
					$classname = 'weTable';
					return true;
				}
				return false;

			case 'we:tableadv':
				$table = weBackupUtil::getRealTableName($attribs['name']);
				if($table !== false){
					weBackupUtil::setBackupVar('current_table', $table);
					$object = new weTableAdv($table);
					$classname = 'weTableAdv';
					return true;
				}
				return false;

			case 'we:tableitem':
				$table = weBackupUtil::getRealTableName($attribs['table']);
				if($table !== false){
					weBackupUtil::setBackupVar('current_table', $table);
					$object = new weTableItem($table);
					$classname = 'weTableItem';
					return true;
				}
				return false;

			case 'we:binary':
				$object = new weBinary();
				$classname = 'weBinary';
				return true;

			case 'we:version':
				$object = new weVersion();
				$classname = 'weVersion';
				return true;

			default:
				return false;
		}
	}

	function handlePrefs(&$object){
		$file = TEMP_DIR . 'we_conf_global.inc.php';
		$object->Path = $file;
		$object->save(true);
		we_base_preferences::check_global_config(true, $_SERVER['DOCUMENT_ROOT'] . $file, array('BACKUP_STEPS', 'DB_SET_CHARSET'));
		weFile::delete($_SERVER['DOCUMENT_ROOT'] . $file);
	}

}
