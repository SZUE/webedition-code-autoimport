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
abstract class we_backup_import{

	static function import($filename, &$offset, $lines = 1, $iscompressed = 0, $encoding = 'ISO-8859-1'){
		we_backup_util::addLog(sprintf('Reading offset %s, %s lines, Mem: %s', $offset, $lines, memory_get_usage(true)));
		we_backup_util::writeLog();
		$header = (!empty($_SESSION['weS']['weBackupVars']['options']['convert_charset']) ?
				we_exim_XMLExIm::getHeader($_SESSION['weS']['weBackupVars']['encoding'], 'backup') :
				we_exim_XMLExIm::getHeader('', 'backup'));
		$data = $header . we_backup_fileReader::readLine($filename, $offset, $lines, $iscompressed);

		if(strlen($data) == strlen($header)){
			return false;
		}

		we_backup_util::addLog(sprintf('Read %s bytes, Mem: %s', strlen($data), memory_get_usage(true)));
		we_backup_util::writeLog();

		$data .=we_backup_backup::weXmlExImFooter;

		self::transfer($data, $encoding);
		return true;
	}

	private static function transfer(&$data, $charset = 'ISO-8859-1'){
		we_backup_util::addLog('Parsing data');

		$parser = new we_backup_XMLParser();

		$parser->parse($data, (DEFAULT_CHARSET ? : 'ISO-8859-1')); // Fix f�r 4092, in Verbindung mit alter Version f�r bug 3412 l�st das beide Situationen
		if($parser->parseError){
			t_e('encountered parse error during import', $parser->parseError);
		}
		// free some memory
		unset($data);

		if($parser === false){
			p_r($parser->parseError);
			we_backup_util::addLog(print_r($parser->parseError, true));
		}

		$parser->normalize();
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
					if($name === 'we:content'){

						$parser->addMark('second');
						$parser->next();

						do{
							$element_value = $parser->getNodeName();
							if($element_value === 'Field'){
								$element_name = $parser->getNodeData();
							}
							if(!empty($element_name)){
								$object->elements[$element_name][$element_value] = $parser->getNodeData();
							}
						} while($parser->nextSibling());

						unset($element_name);
						unset($element_value);

						$parser->gotoMark('second');
					} else {
						$attr = $parser->getNodeAttributes();
						if(version_compare($_SESSION['weS']['weBackupVars']['weVersion'], '6.3.3.1', '>')){
							$object->$name = we_exim_contentProvider::getDecodedData(($attr && isset($attr[we_exim_contentProvider::CODING_ATTRIBUTE]) ? $attr[we_exim_contentProvider::CODING_ATTRIBUTE] : we_exim_contentProvider::CODING_NONE), $parser->getNodeData());
						} else {
							// import field
							$object->$name = (we_exim_contentProvider::needCoding($classname, $name, we_exim_contentProvider::CODING_OLD) ?
									we_exim_contentProvider::decode($parser->getNodeData()) :
									$parser->getNodeData()); //original mit Bug #3412 aber diese Version l�st 4092
						}

						if(isset($object->persistent_slots) && !in_array($name, $object->persistent_slots)){
							$object->persistent_slots[] = $name;
						}
					}

					//correct table name in tblversions
					if(isset($object->table) && $object->table === "tblversions" && isset($object->documentTable)){
						if(strtolower(substr($object->documentTable, -14)) === "tblobjectfiles"){
							$object->documentTable = defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'tblobjectfiles';
						}
						if(strtolower(substr($object->documentTable, -7)) === "tblfile"){
							$object->documentTable = defined('FILE_TABLE') ? FILE_TABLE : 'tblfile';
						}
					}
				} while($parser->nextSibling());

				$addtext = '';
				if(!empty($_SESSION['weS']['weBackupVars']['options']['convert_charset'])){
					$addtext = (method_exists($object, 'convertCharsetEncoding') ?
							" - Converting Charset: " . $_SESSION['weS']['weBackupVars']['encoding'] . " -> " . DEFAULT_CHARSET :
							" - Converting Charset: NO ");
				}
				$_prefix = 'Saving object ';
				switch($classname){
					case 'we_backup_table':
					case 'we_backup_tableAdv':
					case 'we_backup_tableItem':
					case 'weBinary':
						we_backup_util::addLog($object->getLogString($_prefix . $classname . ':') . $addtext);
						break;
				}
				if(!empty($_SESSION['weS']['weBackupVars']['options']['convert_charset']) && method_exists($object, 'convertCharsetEncoding')){
					$object->convertCharsetEncoding($_SESSION['weS']['weBackupVars']['encoding'], DEFAULT_CHARSET);
				}
				if(isset($object->Path) && $object->Path == WE_INCLUDES_DIR . 'conf/we_conf_global.inc.php'){
					self::handlePrefs($object);
				} else if(defined('SPELLCHECKER') && isset($object->Path) && (strpos($object->Path, WE_MODULES_DIR . 'spellchecker/') === 0) && !$_SESSION['weS']['weBackupVars']['handle_options']['spellchecker']){
					// do nothing
				} else {
					$object->save(true);
				}

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
				$table = we_backup_util::getRealTableName($attribs['name']);
				if($table !== false){
					we_backup_util::setBackupVar('current_table', $table);
					$object = new we_backup_table($table);
					$classname = get_class($object);
					return true;
				}
				return false;

			case 'we:tableadv':
				$table = we_backup_util::getRealTableName($attribs['name']);
				if($table !== false){
					we_backup_util::setBackupVar('current_table', $table);
					$object = new we_backup_tableAdv($table);
					$classname = get_class($object);
					return true;
				}
				return false;

			case 'we:tableitem':
				$table = we_backup_util::getRealTableName($attribs['table']);
				if($table !== false){
					we_backup_util::setBackupVar('current_table', $table);
					$object = new we_backup_tableItem($table);
					$classname = get_class($object);
					return true;
				}
				return false;

			case 'we:binary':
				$object = new weBinary();
				$classname = get_class($object);
				return true;

			case 'we:version':
				$object = new we_backup_version();
				$classname = 'weVersion';
				return true;

			default:
				return false;
		}
	}

	private static function handlePrefs(&$object){
		$file = TEMP_DIR . 'we_conf_global.inc.php';
		$object->Path = $file;
		$object->save(true);
		we_base_preferences::check_global_config(true, $_SERVER['DOCUMENT_ROOT'] . $file, array('DB_SET_CHARSET'));
		we_base_file::delete($_SERVER['DOCUMENT_ROOT'] . $file);
	}

}
