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
include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_exim/backup/weBackupUtil.class.php');

class weBackupImport{

	function import($filename, &$offset, $lines=1, $iscompressed=0, $encoding='ISO-8859-1', $log=0){

		include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_exim/weXMLExImConf.inc.php');
		include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_exim/backup/weBackupFileReader.class.php');
		if(isset($_SESSION['weBackupVars']['options']['convert_charset']) && $_SESSION['weBackupVars']['options']['convert_charset']){
			$data = '<?xml version="1.0" encoding="' . $_SESSION['weBackupVars']['encoding'] . '" standalone="yes"?>' . $GLOBALS['weXmlExImNewLine'] .
				'<webEdition version="' . WE_VERSION . '" xmlns:we="we-namespace">' . $GLOBALS['weXmlExImNewLine'];
		} else{
			$data = $GLOBALS['weXmlExImHeader'];
		}
		if($log){
			weBackupUtil::addLog(sprintf('Reading offset %s', $offset));
		}

		$_fileReader = new weBackupFileReader();
		$_fileReader->readLine($filename, $data, $offset, $lines, 0, $iscompressed);

		$data .= $GLOBALS['weXmlExImFooter'];

		weBackupImport::transfer($data, $encoding, $log);
	}

	function transfer(&$data, $charset='ISO-8859-1', $log=0){

		include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_exim/weXMLParser.class.php');
		include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_exim/weContentProvider.class.php');

		$nFactor = 5;

		if($log){
			weBackupUtil::addLog('Parsing data');
		}

		$parser = new weXMLParser();
		//if(isset($_SESSION['weBackupVars']['options']['convert_charset']) && $_SESSION['weBackupVars']['options']['convert_charset']){ vor 4092
		if(defined('DEFAULT_CHARSET') && DEFAULT_CHARSET != ''){// Fix f�r 4092, in Verbindung mit alter Version f�r bug 3412 l�st das beide Situationen
			$parser->parse($data, DEFAULT_CHARSET);
		} else{
			$parser->parse($data);
		}
		if($parser === false){
			p_r($parser->parseError);
		}

		// free some memory
		unset($parser->Indexes);
		unset($data);

		$parser->normalize();
		// set parser on the first child node
		$parser->seek(1);

		do{

			$entity = $parser->getNodeName();
			$attributes = $parser->getNodeAttributes();

			$classname = '';
			$object = '';

			if(weBackupImport::getObject($entity, $attributes, $object, $classname)){


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
						// import field
						if(weContentProvider::needCoding($classname, $name)){
							$object->$name = weContentProvider::decode($parser->getNodeData());
						} else{
							$object->$name = $parser->getNodeData(); //original mit Bug #3412 aber diese Version l�st 4092
							// ehemaliger Fix Bug #3412, nicht mehr notwendig dank fix oben f�r 4092
							/*
							  if($charset=="UTF-8"){
							  $object->$name = utf8_encode($parser->getNodeData());
							  } else {
							  $object->$name = $parser->getNodeData();
							  }
							 */
						}
						if(isset($object->persistent_slots) && !in_array($name, $object->persistent_slots)){
							$object->persistent_slots[] = $name;
						}
					}

					//correct table name in tblversions
					if(isset($object->table) && $object->table == "tblversions"){
						if(isset($object->documentTable)){
							if(strtolower(substr($object->documentTable, -14)) == "tblobjectfiles"){
								$object->documentTable = defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'tblobjectfiles';
							}
							if(strtolower(substr($object->documentTable, -7)) == "tblfile"){
								$object->documentTable = defined('FILE_TABLE') ? FILE_TABLE : 'tblfile';
							}
						}
					}
				} while($parser->nextSibling());


				if($log){
					$addtext = '';
					if(isset($_SESSION['weBackupVars']['options']['convert_charset']) && $_SESSION['weBackupVars']['options']['convert_charset']){
						if(method_exists($object, 'convertCharsetEncoding')){
							$addtext = " - Converting Charset: " . $_SESSION['weBackupVars']['encoding'] . " -> " . DEFAULT_CHARSET;
						} else{
							$addtext = " - Converting Charset: NO ";
						}
					}
					$_prefix = 'Saving object ';
					if($classname == 'weTable' || $classname == 'weTableAdv'){
						weBackupUtil::addLog($_prefix . $classname . ':' . $object->table . $addtext);
					} else if($classname == 'weTableItem'){
						$_id_val = '';
						foreach($object->keys as $_key){
							$_id_val .= ':' . $object->$_key;
						}
						weBackupUtil::addLog($_prefix . $classname . ':' . $object->table . $_id_val . $addtext);
					} else if($classname == 'weBinary'){
						weBackupUtil::addLog($_prefix . $classname . ':' . $object->ID . ':' . $object->Path . $addtext);
					}
				}
				if(isset($_SESSION['weBackupVars']['options']['convert_charset']) && $_SESSION['weBackupVars']['options']['convert_charset']){
					if(method_exists($object, 'convertCharsetEncoding'))
						$object->convertCharsetEncoding($_SESSION['weBackupVars']['encoding'], DEFAULT_CHARSET);
				}
				if(isset($object->Path) && $object->Path == '/webEdition/we/include/conf/we_conf_global.inc.php'){
					weBackupImport::handlePrefs($object);
				} else if(defined('SPELLCHECKER') && isset($object->Path) && (strpos($object->Path, '/webEdition/we/include/we_modules/spellchecker/') === 0) && !$_SESSION['weBackupVars']['handle_options']['spellchecker']){
					// do nothing
				} else{
					$object->save();
				}

				//speedup for some tables
				if(isset($object->table) && ($object->table == LINK_TABLE || $object->table == CONTENT_TABLE)){
					$_SESSION['weBackupVars']['backup_steps'] = BACKUP_STEPS * $nFactor;
				} else{
					$_SESSION['weBackupVars']['backup_steps'] = BACKUP_STEPS;
				}

				$parser->gotoMark('first');
			}



			if(isset($object)){
				unset($object);
			}
		} while($parser->nextSibling());
	}

	function getObject($tagname, $attribs, &$object, &$classname){

		switch($tagname){

			case 'we:table':
				$table = weBackupUtil::getRealTableName($attribs['name']);
				if($table !== false){
					weBackupUtil::setBackupVar('current_table', $table);
					$object = new weTable($table);
					$classname = 'weTable';
					return true;
				}
				break;

			case 'we:tableadv':
				$table = weBackupUtil::getRealTableName($attribs['name']);
				if($table !== false){
					weBackupUtil::setBackupVar('current_table', $table);
					$object = new weTableAdv($table);
					$classname = 'weTableAdv';
					return true;
				}
				break;

			case 'we:tableitem':
				$table = weBackupUtil::getRealTableName($attribs['table']);
				if($table !== false){
					weBackupUtil::setBackupVar('current_table', $table);
					$object = new weTableItem($table);
					$classname = 'weTableItem';
					return true;
				}
				break;

			case 'we:binary':
				$object = new weBinary();
				$classname = 'weBinary';
				return true;
				break;

			case 'we:version':
				$object = new weVersion();
				$classname = 'weVersion';
				return true;
				break;
		}

		return false;
	}

	function handlePrefs(&$object){
		$file = "/webEdition/we/tmp/we_conf_global.inc.php";
		$object->Path = $file;
		$object->save(true);
		$parser = weConfParser::getConfParserByFile($_SERVER['DOCUMENT_ROOT'] . $file);

		$newglobals = $parser->getData();

		foreach($newglobals as $k => $v){
			if($k != 'BACKUP_STEPS' && $v != ''){
				if($k != 'DB_SET_CHARSET'){
					weConfParser::setGlobalPref($k, $v);
				}
			}
		}
		@unlink($_SERVER['DOCUMENT_ROOT'] . $file);
	}

}
