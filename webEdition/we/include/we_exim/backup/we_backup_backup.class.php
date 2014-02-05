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

/**
 * Class weBackup
 *
 * Provides functions for exporting and importing backups. Extends we_backup.
 */
class we_backup_backup extends we_backup_base{

	const backupMarker = '<!-- webackup -->';
	const weXmlExImHead = '<webEdition';
	const weXmlExImFooter = '</webEdition>';
	const weXmlExImProtectCode = '<?php exit();?>';

	var $header;
	var $footer;
	var $nl = "\n";
	var $mode = "sql";
	var $filename;
	var $compress = self::NO_COMPRESSION;
	var $rebuild;
	var $file_list = array();
	var $file_counter = 0;
	var $file_end = 0;
	var $backup_dir;
	var $backup_dir_tmp;
	var $row_count = 0;
	var $file_list_count = 0;
	var $old_objects_deleted = 0;
	var $backup_binary = 1;

	function __construct($handle_options = array()){
		$this->header = '<?xml version="1.0" encoding="' . $GLOBALS['WE_BACKENDCHARSET'] . '" standalone="yes"?>' . $this->nl .
			self::weXmlExImHead . ' version="' . WE_VERSION . '" type="backup" xmlns:we="we-namespace">' . $this->nl;
		$this->footer = $this->nl . self::weXmlExImFooter;

		$this->properties[] = 'mode';
		$this->properties[] = 'filename';
		$this->properties[] = 'compress';
		$this->properties[] = 'backup_binary';
		$this->properties[] = 'rebuild';
		$this->properties[] = 'file_counter';
		$this->properties[] = 'file_end';
		$this->properties[] = 'row_count';
		$this->properties[] = 'file_list_count';
		$this->properties[] = 'old_objects_deleted';

		//FIXME: never call parent not in first place
		parent::__construct($handle_options);

		$this->tables['core'] = array('tblfile', 'tbllink', 'tbltemplates', 'tblindex', 'tblcontent', 'tblcategorys', 'tbldoctypes', 'tblthumbnails');
		$this->tables['object'] = array('tblobject', 'tblobjectfiles', 'tblobject_');

		$this->mode = 'xml';

		$this->backup_dir = $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR;
		$this->backup_dir_tmp = $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'tmp/';
	}

	function splitFile2(){
		if($this->filename == ''){
			return -1;
		}
		if($this->mode == 'sql'){
			return parent::splitFile($this->filename);
		}
		t_e('this should not happen');
	}

	/**
	 *
	 * @param type $table
	 * @param type $execTime
	 * @return boolean true, if still time left
	 */
	public static function limitsReached($table, $execTime, $memMulti = 2){
		if($table){
			//check if at least 10 avg rows
			$rowSz = $_SESSION['weS']['weBackupVars']['avgLen'][strtolower(stripTblPrefix($table))];
			if(memory_get_usage(true) + 10 * $rowSz > $_SESSION['weS']['weBackupVars']['limits']['mem']){
				return false;
			}
		} elseif($_SESSION['weS']['weBackupVars']['limits']['lastMem'] != 0){
			$cur = memory_get_usage(true);
			$diff = $cur - $_SESSION['weS']['weBackupVars']['limits']['lastMem'];
			if($cur + $diff * $memMulti > $_SESSION['weS']['weBackupVars']['limits']['mem']){
				return false;
			}
		}
		$_SESSION['weS']['weBackupVars']['limits']['lastMem'] = memory_get_usage(true);

		if($execTime == 0){
			t_e('execTime was 0 - this should never happen - assume microtime is not working correct', $execTime);
			$execTime = 1;
		}

		$maxTime = $_SESSION['weS']['weBackupVars']['limits']['exec'] > 33 ? 30 : $_SESSION['weS']['weBackupVars']['limits']['exec'] - 2;
		if(time() - intval($_SESSION['weS']['weBackupVars']['limits']['requestTime']) + 2 * $execTime > $maxTime){
			return false;
		}

		return true;
	}

	function recoverTable($nodeset, &$xmlBrowser){
		$attributes = $xmlBrowser->getAttributes($nodeset);

		$tablename = $attributes["name"];
		if(!$this->isFixed($tablename) && $tablename != ""){
			$tablename = $this->fixTableName($tablename);
			$this->current_description = (isset($this->description["import"][strtolower($tablename)]) && $this->description["import"][strtolower($tablename)] ?
					$this->description["import"][strtolower($tablename)] :
					g_l('backup', "[working]"));

			$object = weContentProvider::getInstance("we_backup_table", 0, $tablename);
			$node_set2 = $xmlBrowser->getSet($nodeset);
			foreach($node_set2 as $set2){
				$node_set3 = $xmlBrowser->getSet($set2);
				foreach($node_set3 as $nsv){
					$tmp = $xmlBrowser->nodeName($nsv);
					if($tmp == "Field"){
						$name = $xmlBrowser->getData($nsv);
					}
					$object->elements[$name][$tmp] = $xmlBrowser->getData($nsv);
				}
			}

			if(
				((defined("OBJECT_TABLE") && $object->table == OBJECT_TABLE) ||
				(defined("OBJECT_FILES_TABLE") && $object->table == OBJECT_FILES_TABLE)) && $this->old_objects_deleted == 0){
				$this->delOldTables();
				$this->old_objects_deleted = 1;
			}
			$object->save();
		}
	}

	function recoverTableItem($nodeset, &$xmlBrowser){
		$content = array();
		$node_set2 = $xmlBrowser->getSet($nodeset);
		$classname = "we_backup_tableItem";

		foreach($node_set2 as $nsv){
			$index = $xmlBrowser->nodeName($nsv);
			$content[$index] = (weContentProvider::needCoding($classname, $index, $nsv) ?
					weContentProvider::decode($xmlBrowser->getData($nsv)) :
					$xmlBrowser->getData($nsv));
		}
		$attributes = $xmlBrowser->getAttributes($nodeset);

		$tablename = $attributes["table"];
		if(!$this->isFixed($tablename) && $tablename != ""){
			$tablename = $this->fixTableName($tablename);

			$object = weContentProvider::getInstance($classname, 0, $tablename);
			weContentProvider::populateInstance($object, $content);

			$object->save(true);
		}
	}

	function recoverBinary($nodeset, &$xmlBrowser){
		$content = array();
		$node_set2 = $xmlBrowser->getSet($nodeset);
		$classname = weContentProvider::getContentTypeHandler("weBinary");
		foreach($node_set2 as $nsv){
			$index = $xmlBrowser->nodeName($nsv);
			$content[$index] = (weContentProvider::needCoding($classname, $index, $nsv) ?
					weContentProvider::decode($xmlBrowser->getData($nsv)) :
					$xmlBrowser->getData($nsv));
		}
		$object = weContentProvider::getInstance($classname, 0);
		weContentProvider::populateInstance($object, $content);

		if($object->ID && $this->backup_binary){
			$object->save(true);
		} else if($this->handle_options["settings"] && $object->Path == WE_INCLUDES_DIR . "conf/we_conf_global.inc.php"){
			we_backup_backup::recoverPrefs($object);
		} else if(!$object->ID && $this->backup_extern){
			$object->save(true);
		}
	}

	function recoverPrefs(&$object){
		$file = TEMP_DIR . 'we_conf_global.inc.php';
		$object->Path = $file;
		$object->save(true);
		we_base_preferences::check_global_config(true, $_SERVER['DOCUMENT_ROOT'] . $file, array('DB_SET_CHARSET'));
		we_base_file::delete($_SERVER['DOCUMENT_ROOT'] . $file);
	}

	function recover($chunk_file){
		if(!is_readable($chunk_file)){
			return false;
		}

		$xmlBrowser = new we_xml_browser($chunk_file);
		$xmlBrowser->mode = "backup";

		foreach($xmlBrowser->nodes as $key => $val){
			$name = $xmlBrowser->nodeName($key);
			switch($name){
				case "we:table":
					we_backup_backup::recoverTable($key, $xmlBrowser);
					break;
				case "we:tableitem":
					we_backup_backup::recoverTableItem($key, $xmlBrowser);
					break;
				case "we:binary":
					we_backup_backup::recoverBinary($key, $xmlBrowser);
					break;
			}
		}
		return true;
	}

	function backup($id){

	}

	/**
	 * Function: makeBackup
	 *
	 * Description: This function initializes the creation of a backup.
	 */
	function makeBackup(){
		if(!$this->tempfilename){
			$this->tempfilename = $this->filename;
			$this->dumpfilename = $this->backup_dir_tmp . $this->tempfilename;
			$this->backup_step = 0;

			if(!we_base_file::save($this->dumpfilename, $this->header)){
				$this->setError(sprintf(g_l('backup', "[can_not_open_file]"), $this->dumpfilename));
				return -1;
			}
		}

		return ($this->backup_extern == 1 && $this->backup_phase == 0 ?
				$this->exportExtern() :
				$this->exportTables());
	}

	/**
	 * Function: exportTables
	 *
	 * Description: This function saves the files in the previously builded
	 * table if the users chose to backup external files.
	 */
	function exportTables(){
		$tabtmp = array();
		if(!isset($_SESSION['weS']['weBackupVars']['allTables'])){
			$_SESSION['weS']['weBackupVars']['allTables'] = $this->backup_db->table_names();
		}
		$tab = $_SESSION['weS']['weBackupVars']['allTables'];

		$xmlExport = new weXMLExIm();
		$xmlExport->setBackupProfile();

		foreach($tab as $v){
			$noprefix = $this->getDefaultTableName($v["table_name"]);
			if($noprefix && $this->isWeTable($noprefix)){
				$tabtmp[] = $v["table_name"];
			}
		}

		$tables = $this->arraydiff($tabtmp, $this->extables);
		$num_tables = count($tables);
		if($num_tables){
			$i = 0;
			while($i < $num_tables){
				$table = $tables[$i];
				$noprefix = $this->getDefaultTableName($table);

				if(!$this->isFixed($noprefix)){

					//$metadata = $this->backup_db->metadata($table);

					if(!$this->partial){
						$xmlExport->exportChunk(0, "we_backup_table", $this->dumpfilename, $table, $this->backup_binary);

						$this->backup_step = 0;
						$this->table_end = 0;

						$this->table_end = f('SELECT COUNT(1) AS Count FROM ' . $this->backup_db->escape($table), 'Count', $this->backup_db);
					}

					$this->current_description = (isset($this->description["export"][strtolower($table)]) ?
							$this->description["export"][strtolower($table)] :
							g_l('backup', "[working]"));

					$keys = we_backup_tableItem::getTableKey($table);
					$this->partial = false;

					do{
						$start = microtime(true);
						$this->backup_db->query($this->getBackupQuery($table, $keys));

						while($this->backup_db->next_record()){
							$keyvalue = array();
							foreach($keys as $key){
								$keyvalue[] = $this->backup_db->f($key);
							}
							++$this->row_count;

							$xmlExport->exportChunk(implode(",", $keyvalue), "we_backup_tableItem", $this->dumpfilename, $table, $this->backup_binary);
							++$this->backup_step;
						}
					} while(self::limitsReached($table, microtime(true) - $start));
				}
				$i++;
				if($this->backup_step < $this->table_end && $this->backup_db->num_rows() != 0){
					$this->partial = true;
					break;
				} else {
					$this->partial = false;
				}
				if(!$this->partial && !in_array($table, $this->extables)){
					$this->extables[] = $table;
				}
			}
		}
		if($this->partial){
			return 1;
		}
		//$res=array();
		//$res=$this->arraydiff($tab,$this->extables);
		unset($xmlExport);
		return 0;
	}

	/**
	 * Function: exportMapper
	 *
	 * Description: This function exports the fields from table
	 */
	function exportInfo($filename, $table, $fields){
		if(!is_array($fields)){
			return false;
		}
		$out = '<we:info>';
		$this->backup_db->query('SELECT ' . implode(',', $fields) . ' FROM ' . $this->backup_db->escape($table));
		while($this->backup_db->next_record()){
			$out.='<we:map table="' . $this->getDefaultTableName($table) . '"';
			foreach($fields as $field){
				$out.=' ' . $field . '="' . $this->backup_db->f($field) . '"';
			}
			$out.='>';
		}
		$out.='</we:info>' . we_backup_backup::backupMarker . "\n";
		we_base_file::save($filename, $out, "ab");
	}

	/**
	 * Function: printDump2BackupDir
	 *
	 * Description: This function saves the dump to the backup directory.
	 */
	function printDump2BackupDir(){
		$backupfilename = $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . $this->filename;
		if($this->compress != self::NO_COMPRESSION && $this->compress != ""){
			$this->dumpfilename = we_base_file::compress($this->dumpfilename, $this->compress);
			$this->filename = $this->filename . '.' . we_base_file::getZExtension($this->compress);
		}

		if($this->export2server == 1){
			$backupfilename = $this->backup_dir . $this->filename;
			return @copy($this->dumpfilename, $backupfilename);
		}

		return true;
	}

	/**
	 * Function: removeDumpFile
	 *
	 * Description: This function deletes a database dump.
	 */
	function removeDumpFile(){
		if($this->export2send && !$this->export2server){
			we_util_File::insertIntoCleanUp($this->dumpfilename, time());
		} else if(is_file($this->dumpfilename)){
			@unlink($this->dumpfilename);
			$this->dumpfilename = "";
			$this->tempfilename = "";
		}
	}

	/**
	 * Function: restoreFromBackup
	 *
	 * Description: This function restores a backup.
	 */
	function restoreChunk($filename){
		if(!is_readable($filename)){
			$this->setError(sprintf(g_l('backup', "[can_not_open_file]"), $filename));
			return false;
		}

		return ($this->mode == 'sql' ?
				parent::restoreFromBackup($filename, $this->backup_extern) :
				$this->recover($filename));
	}

	function getVersion($file){
		$this->mode = ($this->isOldVersion($file) ? "sql" : "xml");
	}

	function isOldVersion($file){
		$part = we_base_file::loadPart($file, 0, 512);
		return (stripos($part, "# webEdition version:") !== false && stripos($part, "DROP TABLE") !== false && stripos($part, "CREATE TABLE") !== false);
	}

	function isCompressed($file){
		$part = we_base_file::loadPart($file, 0, 512);
		return stripos($part, "<?xml version=") === false;
	}

	function getDownloadFile(){
		return ($this->export2server ? $this->backup_dir . $this->filename : $this->dumpfilename);
	}

	/**
	 * Function: isFixed
	 *
	 * Description: This function checks if a table name has its correct value.
	 */
	function isFixed($tab){
		if(defined("OBJECT_X_TABLE")){
			if(stripos($tab, OBJECT_X_TABLE) !== false){
				return !(isset($this->handle_options["object"]) && $this->handle_options["object"]);
			}
		} else if(stripos($tab, "tblobject") !== false){
			return true;
		}
		return parent::isFixed($tab) || !$this->isWeTable($tab);
	}

	function getFileList($dir = '', $with_dirs = false, $rem_doc_root = true){
		$dir = ($dir ? $dir : $_SERVER['DOCUMENT_ROOT']);
		if(!is_readable($dir) || !is_dir($dir)){
			$this->file_list_count = 0;
			return false;
		}
		$thumbDir = trim(WE_THUMBNAIL_DIRECTORY, '/');
		$d = dir($dir);
		while(false !== ($entry = $d->read())){
			switch($entry){
				case '.':
				case '..':
				case 'CVS':
				case 'sql_dumps':
				case '.project':
				case '.trustudio.dbg.php':
				case 'LanguageChanges.csv':
					continue;
				case 'webEdition':
				case $thumbDir:
					//FIXME: check if dir==doc_root
					continue;
				default:
					$file = $dir . '/' . $entry;
					if(!$this->isPathExist(str_replace($_SERVER['DOCUMENT_ROOT'], '', $file))){
						if(is_dir($file)){
							if($with_dirs){
								$this->addToFileList($file, $rem_doc_root);
							}
							$this->getFileList($file, $with_dirs, $rem_doc_root);
						} else {
							$this->addToFileList($file, $rem_doc_root);
						}
					} elseif(is_dir($file)){
						$this->getFileList($file, $with_dirs, $rem_doc_root);
					}
			}
		}
		$d->close();
		$this->file_list_count = count($this->file_list);
	}

	function addToFileList($file, $rem_doc_root = true){
		$this->file_list[] = ($rem_doc_root ?
				str_replace($_SERVER['DOCUMENT_ROOT'], '', $file) :
				$file);
	}

	function getSiteFiles(){
		$this->getFileList($_SERVER['DOCUMENT_ROOT'] . SITE_DIR, true, false);
		$out = array();
		foreach($this->file_list as $file){
			$ct = f('SELECT ContentType FROM ' . FILE_TABLE . ' WHERE Path="' . $this->backup_db->escape(str_replace($_SERVER['DOCUMENT_ROOT'] . rtrim(SITE_DIR, '/'), '', $file)) . '"', 'ContentType', $this->backup_db);
			switch($ct){
				case'image/*':
				case 'application/*':
				case 'application/x-shockwave-flash':
					continue;
				default:
					$out[] = $file;
			}
		}
		$this->file_list = $out;
	}

	/**
	 * Function: exportExtern
	 *
	 * Description: This function backup external files.
	 *
	 */
	function exportExtern(){
		$this->current_description = g_l('backup', '[external_backup]');

		if(isset($this->file_list[0])){
			if(is_readable($_SERVER['DOCUMENT_ROOT'] . $this->file_list[0])){
				$this->exportFile($this->file_list[0]);
			}
			array_shift($this->file_list);
		}

		if(empty($this->file_list)){
			$this->backup_phase = 1;
		}
		return true;
	}

	/**
	 * Function: exportExtern
	 *
	 * Description: This function backup  given file to backup.
	 *
	 */
	function exportFile($file){
		$fh = fopen($this->dumpfilename, 'ab');
		if($fh){

			$bin = weContentProvider::getInstance('weBinary', 0);
			$bin->Path = $file;

			weContentProvider::binary2file($bin, $fh);
			fclose($fh);
		}
	}

	function saveState($of = ""){
		//FIXME: use __sleep/__wakeup + serialize/unserialize
		$save = $this->_saveState() . '
$this->file_list=' . var_export($this->file_list, true) . ';';

		$of = ($of ? $of : we_base_file::getUniqueId());
		we_base_file::save($this->backup_dir_tmp . $of, $save);
		return $of;
	}

	function getExportPercent(){
		$all = 0;
		$db = new DB_WE();
		$db->query('SHOW TABLE STATUS');
		while($db->next_record()){
			$noprefix = $this->getDefaultTableName($db->f("Name"));
			if(!$this->isFixed($noprefix)){
				$all += $db->f("Rows");
			}
		}

		$ex_files = ((int) $this->file_list_count) - ((int) count($this->file_list));
		$all+=(int) $this->file_list_count;
		$done = ((int) $this->row_count) + ((int) $ex_files);
		$percent = (int) (($done / $all) * 100);
		return ($percent < 0 ? 0 : ($percent > 100 ? 100 : $percent));
	}

	function getImportPercent(){
		$file_list_count = (int) ($this->file_list_count - count($this->file_list)) / 100;
		$percent = (int) ((($this->file_counter + $file_list_count) / (($this->file_list_count / 100) + $this->file_end)) * 100);
		return ($percent > 100 ? 100 : ($percent < 0 ? 0 : $percent));
	}

	function exportGlobalPrefs(){
		$file = WE_INCLUDES_DIR . 'conf/we_conf_global.inc.php';
		if(is_readable($_SERVER['DOCUMENT_ROOT'] . $file)){
			$this->exportFile($file);
		}
	}

	function writeFooter(){
		if($this->handle_options["settings"]){
			$this->exportGlobalPrefs();
		}
		we_base_file::save($this->dumpfilename, $this->footer, 'ab');
	}

	function getBackupQuery($table, $keys){
		return 'SELECT `' . implode('`,`', $keys) . '` FROM ' . escape_sql_query($table) . ' LIMIT ' . intval($this->backup_step) . ',' . intval($this->backup_steps);
	}

	function delOldTables(){
		if(!defined("OBJECT_X_TABLE") || !isset($this->handle_options["object"]) || !$this->handle_options["object"]){
			return;
		}
		$this->backup_db->query("SHOW TABLE STATUS");
		while($this->backup_db->next_record()){
			$table = $this->backup_db->f("Name");
			$name = stripTblPrefix($this->backup_db->f("Name"));
			if(substr(strtolower($name), 0, 10) == strtolower(stripTblPrefix(OBJECT_X_TABLE)) && is_numeric(str_replace(strtolower(OBJECT_X_TABLE), '', strtolower($table)))){
				$GLOBALS['DB_WE']->delTable($table);
			}
		}
	}

	function doUpdate(){
		$updater = new we_updater();
		$updater->doUpdate();
	}

	function clearTemporaryData($docTable){
		$this->backup_db->query('DELETE FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocTable="' . $this->backup_db->escape(stripTblPrefix($docTable)) . '"');
	}

}
