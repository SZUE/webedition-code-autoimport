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

/**
 * Class weBackup
 *
 * Provides functions for exporting and importing backups. Extends we_backup.
 */
class we_backup_backup{
	const COMPRESSION = 'gzip';
	const NO_COMPRESSION = 'none';
	const backupMarker = '<!-- webackup -->';
	const weXmlExImHead = '<webEdition';
	const weXmlExImFooter = '</webEdition>';
	const weXmlExImProtectCode = '<?php exit();?>';
	const logFile = 'data/lastlog.php';

	var $backup_db;
	var $errors = array();
	var $warnings = array();
	var $extables = array();
	var $mysql_max_packet = 1048576;
	var $dumpfilename = '';
	var $tempfilename = '';
	var $handle_options = array();
	var $default_backup_steps = 30;
	var $default_backup_len = 150000;
	var $default_offset = 100000;
	var $default_split_size = 150000;
	var $backup_step;
	var $backup_steps;
	var $backup_phase = 0;
	var $backup_extern = 0;
	var $export2server = 0;
	var $export2send = 0;
	var $partial;
	var $current_insert = '';
	var $table_end = 0;
	var $description = array();
	var $current_description = '';
	var $offset = 0;
	var $dummy = array();
	var $table_map = array(
		'tblcategorys' => CATEGORY_TABLE,
		'tblcleanup' => CLEAN_UP_TABLE,
		'tblcontent' => CONTENT_TABLE,
		'tbldoctypes' => DOC_TYPES_TABLE,
		'tblerrorlog' => ERROR_LOG_TABLE,
		'tblfile' => FILE_TABLE,
		'tbllink' => LINK_TABLE,
		'tbltemplates' => TEMPLATES_TABLE,
		'tbltemporarydoc' => TEMPORARY_DOC_TABLE,
		'tblprefs' => PREFS_TABLE,
		'tblrecipients' => RECIPIENTS_TABLE,
		'tblupdatelog' => UPDATE_LOG_TABLE,
		'tbluser' => USER_TABLE,
		'tblfailedlogins' => FAILED_LOGINS_TABLE,
		'tblthumbnails' => THUMBNAILS_TABLE,
		'tblvalidationservices' => VALIDATION_SERVICES_TABLE,
		'tblsettings' => SETTINGS_TABLE,
	);
	var $fixedTable = array(
		'tblbackup', 'tblerrorlog', 'tblcleanup', 'tbllock',
		'tblfailedlogins', 'tblupdatelog');
	var $tables = array(
		'settings' => array('tblprefs', 'tblrecipients', 'tblvalidationservices', 'tblsettings'),
		'configuration' => array(),
		'users' => array('tbluser'),
		'customers' => array('tblwebuser', 'tblwebadmin'),
		'shop' => array('tblorders'),
		'workflow' => array(
			'tblworkflowdef', 'tblworkflowstep', 'tblworkflowtask',
			'tblworkflowdoc', 'tblworkflowdocstep', 'tblworkflowdoctask',
			'tblworkflowlog'
		),
		'todo' => array(
			'tbltodo', 'tbltodohistory', 'tblmessages', 'tblmsgaccounts',
			'tblmsgaddrbook', 'tblmsgfolders', 'tblmsgsettings'
		),
		'newsletter' => array(
			'tblnewsletter', 'tblnewslettergroup',
			'tblnewsletterblock', 'tblnewsletterlog',
			'tblnewsletterprefs', 'tblnewsletterconfirm',
		),
		'temporary' => array('tbltemporarydoc'),
		'banner' => array(
			'tblbanner', 'tblbannerclicks',
			'tblbannerprefs', 'tblbannerviews',
		),
		'schedule' => array(
			'tblschedule'
		),
		'export' => array(
			'tblexport'
		),
		'voting' => array(
			'tblvoting'
		),
	);
	var $properties = array(
		'default_backup_steps', 'backup_step', 'backup_steps', 'backup_phase', 'backup_extern',
		'export2server', 'export2send', 'partial', 'current_insert', 'table_end', 'current_description', 'offset'
	);
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

		$this->properties = array_push($this->properties, 'mode', 'filename', 'compress', 'backup_binary', 'rebuild', 'file_counter', 'file_end', 'row_count', 'file_list_count', 'old_objects_deleted');

		$this->backup_db = new DB_WE();
		$this->backup_steps = $this->default_backup_steps;
		$this->partial = false;

		$this->handle_options = $handle_options;

		$this->mysql_max_packet = f('SHOW VARIABLES LIKE "max_allowed_packet"', 'Value', $this->backup_db);


		if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
			$this->table_map['tblschedule'] = SCHEDULE_TABLE;
		}

		if(defined('CUSTOMER_TABLE')){
			$this->table_map = array_merge($this->table_map, array(
				'tblwebuser' => CUSTOMER_TABLE
			));
		}

		if(defined('OBJECT_TABLE')){
			$this->table_map = array_merge($this->table_map, array(
				'tblobject' => OBJECT_TABLE,
				'tblobjectfiles' => OBJECT_FILES_TABLE,
				'tblobjectlink' => OBJECTLINK_TABLE,
				'tblobject_' => OBJECT_X_TABLE));
		}

		if(defined('SHOP_TABLE')){
			$this->table_map = array_merge($this->table_map, array(
				'tblorders' => SHOP_TABLE));
		}

		if(defined('WORKFLOW_TABLE')){
			$this->table_map = array_merge($this->table_map, array(
				'tblworkflowdef' => WORKFLOW_TABLE,
				'tblworkflowstep' => WORKFLOW_STEP_TABLE,
				'tblworkflowtask' => WORKFLOW_TASK_TABLE,
				'tblworkflowdoc' => WORKFLOW_DOC_TABLE,
				'tblworkflowdocstep' => WORKFLOW_DOC_STEP_TABLE,
				'tblworkflowdoctask' => WORKFLOW_DOC_TASK_TABLE,
				'tblworkflowlog' => WORKFLOW_LOG_TABLE
				)
			);
		}

		if(defined('MSG_TODO_TABLE')){
			$this->table_map = array_merge($this->table_map, array(
				'tbltodo' => MSG_TODO_TABLE,
				'tbltodohistory' => MSG_TODOHISTORY_TABLE,
				'tblmessages' => MESSAGES_TABLE,
				'tblmsgaccounts' => MSG_ACCOUNTS_TABLE,
				'tblmsgaddrbook' => MSG_ADDRBOOK_TABLE,
				'tblmsgfolders' => MSG_FOLDERS_TABLE,
				)
			);
		}
		if(defined('NEWSLETTER_TABLE')){
			$this->table_map = array_merge($this->table_map, array(
				'tblnewsletter' => NEWSLETTER_TABLE,
				'tblnewslettergroup' => NEWSLETTER_GROUP_TABLE,
				'tblnewsletterblock' => NEWSLETTER_BLOCK_TABLE,
				'tblnewsletterlog' => NEWSLETTER_LOG_TABLE,
				'tblnewsletterconfirm' => NEWSLETTER_CONFIRM_TABLE
				)
			);
		}

		if(defined('BANNER_TABLE')){
			$this->table_map = array_merge($this->table_map, array(
				'tblbanner' => BANNER_TABLE,
				'tblbannerclicks' => BANNER_CLICKS_TABLE,
				'tblbannerviews' => BANNER_VIEWS_TABLE
				)
			);
		}
		if(we_base_moduleInfo::isActive(we_base_moduleInfo::EXPORT)){
			$this->table_map = array_merge($this->table_map, array(
				'tblexport' => EXPORT_TABLE
				)
			);
		}
		if(defined('VOTING_TABLE')){
			$this->table_map['tblvoting'] = VOTING_TABLE;
		}


		$this->setDescriptions();


		$this->clearOldTmp();

		$this->tables['core'] = array(
			'tblfile',
			'tbllink',
			'tbltemplates',
			'tblindex',
			'tblcontent',
			'tblcategorys',
			'tbldoctypes',
			'tblthumbnails',
		);
		$this->tables['object'] = array(
			'tblobject',
			'tblobjectfiles',
			'tblobject_'
		);

		$this->mode = 'xml';

		$this->backup_dir = BACKUP_PATH;
		$this->backup_dir_tmp = BACKUP_PATH . 'tmp/';
	}

	function splitFile2(){
		if(!$this->filename){
			return -1;
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

		$maxTime = $_SESSION['weS']['weBackupVars']['limits']['exec'] - 2;
		if(time() - intval($_SESSION['weS']['weBackupVars']['limits']['requestTime']) + 2 * $execTime > $maxTime){
			return false;
		}

		return true;
	}

	private static function recoverTable($nodeset, &$xmlBrowser){
		$attributes = $xmlBrowser->getAttributes($nodeset);

		$tablename = $attributes["name"];
		if($tablename == "" || $this->isFixed($tablename)){
			return;
		}
		$tablename = $this->fixTableName($tablename);
		$this->current_description = (!empty($this->description["import"][strtolower($tablename)]) ?
				$this->description["import"][strtolower($tablename)] :
				g_l('backup', '[working]'));

		$object = we_exim_contentProvider::getInstance("we_backup_table", 0, $tablename);
		$node_set2 = $xmlBrowser->getSet($nodeset);
		foreach($node_set2 as $set2){
			$node_set3 = $xmlBrowser->getSet($set2);
			foreach($node_set3 as $nsv){
				$tmp = $xmlBrowser->nodeName($nsv);
				if($tmp === "Field"){
					$name = $xmlBrowser->getData($nsv);
				}
				$object->elements[$name][$tmp] = $xmlBrowser->getData($nsv);
			}
		}

		if(
			((defined('OBJECT_TABLE') && $object->table == OBJECT_TABLE) ||
			(defined('OBJECT_FILES_TABLE') && $object->table == OBJECT_FILES_TABLE)) && $this->old_objects_deleted == 0){
			$this->delOldTables();
			$this->old_objects_deleted = 1;
		}
		$object->save();
	}

	private static function recoverTableItem($nodeset, &$xmlBrowser){
		$content = array();
		$node_set2 = $xmlBrowser->getSet($nodeset);
		$classname = "we_backup_tableItem";

		foreach($node_set2 as $nsv){
			$index = $xmlBrowser->nodeName($nsv);
			$content[$index] = (we_exim_contentProvider::needCoding($classname, $index, $nsv) ?
					we_exim_contentProvider::decode($xmlBrowser->getData($nsv)) :
					$xmlBrowser->getData($nsv));
		}
		$attributes = $xmlBrowser->getAttributes($nodeset);

		$tablename = $attributes["table"];
		if($this->isFixed($tablename) || $tablename == ""){
			return;
		}
		$tablename = $this->fixTableName($tablename);

		$object = we_exim_contentProvider::getInstance($classname, 0, $tablename);
		we_exim_contentProvider::populateInstance($object, $content);

		$object->save(true);
	}

	private static function recoverBinary($nodeset, &$xmlBrowser){
		$content = array();
		$node_set2 = $xmlBrowser->getSet($nodeset);
		$classname = we_exim_contentProvider::getContentTypeHandler("we_backup_binary");
		foreach($node_set2 as $nsv){
			$index = $xmlBrowser->nodeName($nsv);
			$content[$index] = (we_exim_contentProvider::needCoding($classname, $index, $nsv) ?
					we_exim_contentProvider::decode($xmlBrowser->getData($nsv)) :
					$xmlBrowser->getData($nsv));
		}
		$object = we_exim_contentProvider::getInstance($classname, 0);
		we_exim_contentProvider::populateInstance($object, $content);

		if($object->ID && $this->backup_binary){
			$object->save(true);
		} else if($this->handle_options["settings"] && $object->Path == WE_INCLUDES_DIR . 'conf/we_conf_global.inc.php'){
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

	private function recover($chunk_file){
		if(!is_readable($chunk_file)){
			return false;
		}

		$xmlBrowser = new we_xml_browser($chunk_file);
		$xmlBrowser->mode = 'backup';

		foreach($xmlBrowser->nodes as $key => $val){
			$name = $xmlBrowser->nodeName($key);
			switch($name){
				case 'we:table':
					self::recoverTable($key, $xmlBrowser);
					break;
				case 'we:tableitem':
					self::recoverTableItem($key, $xmlBrowser);
					break;
				case 'we:binary':
					self::recoverBinary($key, $xmlBrowser);
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
				$this->setError(sprintf(g_l('backup', '[can_not_open_file]'), $this->dumpfilename));
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

		$xmlExport = new we_exim_XMLExIm();
		$xmlExport->setBackupProfile();

		foreach($tab as $v){
			$noprefix = $this->getDefaultTableName($v["table_name"]);
			if($noprefix && $this->isWeTable($noprefix)){
				$tabtmp[] = $v["table_name"];
			}
		}

		$tables = $this->arraydiff($tabtmp, $this->extables);
		if($tables){
			foreach($tables as $table){
				$noprefix = $this->getDefaultTableName($table);

				if(!$this->isFixed($noprefix)){

					if(!$this->partial){
						$xmlExport->exportChunk(0, "we_backup_table", $this->dumpfilename, $table, $this->backup_binary);

						$this->backup_step = 0;
						$this->table_end = 0;

						$this->table_end = f('SELECT COUNT(1) FROM ' . $this->backup_db->escape($table), '', $this->backup_db);
					}

					$this->current_description = (isset($this->description["export"][strtolower($table)]) ?
							$this->description["export"][strtolower($table)] :
							g_l('backup', '[working]'));

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
				if($this->backup_step < $this->table_end && $this->backup_db->num_rows() != 0){
					$this->partial = true;
					break;
				}
				$this->partial = false;

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
	static function exportInfo($filename, $table, $fields){
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
		$backupfilename = BACKUP_PATH . $this->filename;
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
	 * Function: restoreFromBackup
	 *
	 * Description: This function restores a backup.
	 */
	public function restoreChunk($filename){
		if(!is_readable($filename)){
			$this->setError(sprintf(g_l('backup', '[can_not_open_file]'), $filename));
			return false;
		}

		return ($this->mode === 'sql' ?
				t_e('unsupported backup') :
				$this->recover($filename));
	}

	function getVersion($file){
		$this->mode = ($this->isOldVersion($file) ? "sql" : "xml");
	}

	private function isOldVersion($file){
		$part = we_base_file::loadPart($file, 0, 512);
		return (stripos($part, "# webEdition version:") !== false && stripos($part, "DROP TABLE") !== false && stripos($part, "CREATE TABLE") !== false);
	}

	/**
	 * Function: isFixed
	 *
	 * Description: This function checks if a table name has its correct value.
	 */
	function isFixed($tab){
		if(defined('OBJECT_X_TABLE') && stripos($tab, OBJECT_X_TABLE) !== false){
			return (empty($this->handle_options["object"]));
		}
		if(stripos($tab, "tblobject") !== false){
			return true;
		}
		$table = strtolower($tab);
		$fixTable = $this->fixedTable;

		foreach($this->handle_options as $hok => $hov){
			if(!$hov){
				$fixTable = array_merge($fixTable, $this->tables[$hok]);
			}
		}

		return (in_array($table, $fixTable));
	}

	function getFileList($dir = '', $with_dirs = false, $rem_doc_root = true){
		$dir = ($dir ? : $_SERVER['DOCUMENT_ROOT']);
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
				case 'sql_dumps':
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
			$ct = f('SELECT ContentType FROM ' . FILE_TABLE . ' WHERE Path="' . $this->backup_db->escape(str_replace($_SERVER['DOCUMENT_ROOT'] . rtrim(SITE_DIR, '/'), '', $file)) . '"', '', $this->backup_db);
			switch($ct){
				case we_base_ContentTypes::IMAGE:
				case we_base_ContentTypes::APPLICATION:
				case we_base_ContentTypes::FLASH:
				case we_base_ContentTypes::VIDEO:
				case we_base_ContentTypes::AUDIO:
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
	private function exportExtern(){
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

			$bin = we_exim_contentProvider::getInstance('we_backup_binary', 0);
			$bin->Path = $file;

			we_exim_contentProvider::binary2file($bin, $fh);
			fclose($fh);
		}
	}

	function saveState($of = ""){
//FIXME: use __sleep/__wakeup
		$save = $this->_saveState() . '
$this->file_list=' . var_export($this->file_list, true) . ';';

		$of = ($of ? : we_base_file::getUniqueId());
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
		return max(min($percent, 100), 0);
	}

	function getImportPercent(){
		$file_list_count = (int) ($this->file_list_count - count($this->file_list)) / 100;
		$percent = (int) ((($this->file_counter + $file_list_count) / (($this->file_list_count / 100) + $this->file_end)) * 100);
		return max(min($percent, 100), 0);
	}

	private function exportGlobalPrefs(){
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

	private function getBackupQuery($table, $keys){
		return 'SELECT `' . implode('`,`', $keys) . '` FROM ' . escape_sql_query($table) . ' LIMIT ' . intval($this->backup_step) . ',' . intval($this->backup_steps);
	}

	private function delOldTables(){
		if(!defined('OBJECT_X_TABLE') || !isset($this->handle_options["object"]) || !$this->handle_options["object"]){
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
		we_updater::doUpdate();
	}

	function clearTemporaryData($docTable){
		$this->backup_db->query('DELETE FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocTable="' . $this->backup_db->escape(stripTblPrefix($docTable)) . '"');
	}

	public static function getSettingsFiles($import){
		return array_filter(array(
			WE_INCLUDES_DIR . 'conf/we_conf_global.inc.php',
			WE_INCLUDES_DIR . 'conf/we_conf_language.inc.php',
			WE_INCLUDES_DIR . 'conf/we_active_integrated_modules.inc.php',
			WE_INCLUDES_DIR . 'conf/we_conf_language.inc.php',
			($import || file_exists(WEBEDITION_PATH . 'agency.php') ?
				WEBEDITION_DIR . 'agency.php' :
				'')
		));
	}

	private function setDescriptions(){
		$this->description = array(
			'import' => array(
				strtolower(CONTENT_TABLE) => g_l('backup', '[import_content]'),
				strtolower(FILE_TABLE) => g_l('backup', '[import_files]'),
				strtolower(DOC_TYPES_TABLE) => g_l('backup', '[import_doctypes]'),
				strtolower(USER_TABLE) => g_l('backup', '[import_user_data]'),
				defined('CUSTOMER_TABLE') ? strtolower(CUSTOMER_TABLE) : 'CUSTOMER_TABLE' => g_l('backup', '[import_customer_data]'),
				defined('SHOP_TABLE') ? strtolower(SHOP_TABLE) : 'SHOP_TABLE' => g_l('backup', '[import_shop_data]'),
//				defined('WE_SHOP_PREFS_TABLE') ? strtolower(WE_SHOP_PREFS_TABLE) : 'WE_SHOP_PREFS_TABLE' => g_l('backup', '[import_prefs]'),
				strtolower(SETTINGS_TABLE) => g_l('backup', '[import_prefs]'),
				strtolower(TEMPLATES_TABLE) => g_l('backup', '[import_templates]'),
				strtolower(TEMPORARY_DOC_TABLE) => g_l('backup', '[import][temporary_data]'),
				strtolower(LINK_TABLE) => g_l('backup', '[import_links]'),
				strtolower(INDEX_TABLE) => g_l('backup', '[import_indexes]'),
			),
			'export' => array(
				strtolower(CONTENT_TABLE) => g_l('backup', '[export_content]'),
				strtolower(FILE_TABLE) => g_l('backup', '[export_files]'),
				strtolower(DOC_TYPES_TABLE) => g_l('backup', '[export_doctypes]'),
				strtolower(USER_TABLE) => g_l('backup', '[export_user_data]'),
				defined('CUSTOMER_TABLE') ? strtolower(CUSTOMER_TABLE) : 'CUSTOMER_TABLE' => g_l('backup', '[export_customer_data]'),
				defined('SHOP_TABLE') ? strtolower(SHOP_TABLE) : 'SHOP_TABLE' => g_l('backup', '[export_shop_data]'),
				//defined('WE_SHOP_PREFS_TABLE') ? strtolower(WE_SHOP_PREFS_TABLE) : 'WE_SHOP_PREFS_TABLE' => g_l('backup', '[export_prefs]'),
				strtolower(SETTINGS_TABLE) => g_l('backup', '[export_prefs]'),
				strtolower(TEMPLATES_TABLE) => g_l('backup', '[export_templates]'),
				strtolower(TEMPORARY_DOC_TABLE) => g_l('backup', '[export][temporary_data]'),
				strtolower(LINK_TABLE) => g_l('backup', '[export_links]'),
				strtolower(INDEX_TABLE) => g_l('backup', '[export_indexes]'),
			)
		);
	}

	/*	 * ***********************************************************************
	 * FUNCTIONS
	 * *********************************************************************** */

	/**
	 * This function checks if a given path exists in the database.
	 *
	 * @param      $path                                   string
	 *
	 * @see        putFileInDB()
	 * @see        putDirInDB()
	 *
	 * @return     bool
	 */
	protected function isPathExist($path){
		return
			f('SELECT 1  FROM ' . FILE_TABLE . ' WHERE Path="' . $this->backup_db->escape($path) . '" LIMIT 1', '', $this->backup_db) == '1' ||
			f('SELECT 1 FROM ' . TEMPLATES_TABLE . ' WHERE Path="' . $this->backup_db->escape($path) . '" LIMIT 1', '', $this->backup_db) == '1';
	}

	/**
	 * Function: printDump
	 *
	 * Description: This function saves a given file into the dump.
	 */
	function printDump(){
		$fh = @fopen($this->dumpfilename, 'rb');
		if($fh){
			while(!@feof($fh)){
				echo @fread($fh, 52428);
				update_time_limit(80);
			}
			@fclose($fh);
		} else {
			$this->setError(sprintf(g_l('backup', '[can_not_open_file]'), $this->dumpfilename));
			return false;
		}
		return true;
	}

	/**
	 * Function: getTmpFilename
	 *
	 * Description: This function returns the filename of a file located in the
	 * temporary directory used for backups.
	 */
	function getTmpFilename(){
		return $this->tempfilename;
	}

	/**
	 * Function: getDiff
	 *
	 * Description: This function checks for differences between the table
	 * structure of the current database and the table structure of the
	 * backup file.
	 */
	function getDiff(&$q, $tab, &$fupdate){
		$fnames = array();
		$fields = '';
		$sub_parts = array();
		$len = strlen($q);
		$br = 0;
		$run = 0;
		for($i = 0; $i < $len; $i++){
			if($q[$i] === "("){
				$run = 1;
				$br++;
			} else if($q[$i] === ")"){
				$br--;
			} else if($br > 0){
				$fields.=$q[$i];
			}
			if($br == 0 && $run){
				break;
			}
		}
		$parts = explode(',', $fields);
		foreach($parts as $v){
			$sub_parts = explode(' ', trim($v));
			switch($sub_parts[0]){
				case '':
				case 'PRIMARY':
				case 'UNIQUE':
				case 'KEY':
					break;
				default:
					$fnames[] = strtolower($sub_parts[0]);
			}
		}

		$this->backup_db->query('SHOW TABLES LIKE "' . $this->backup_db->escape($tab) . '"');
		if($this->backup_db->next_record()){
			$this->backup_db->query('SHOW COLUMNS FROM ' . $this->backup_db->escape($tab));
			while($this->backup_db->next_record()){
				if(!in_array(strtolower($this->backup_db->f("Field")), $fnames)){
					$fupdate[] = "ALTER TABLE " . $this->backup_db->escape($tab) . ' ADD ' . $this->backup_db->f("Field") . ' ' . $this->backup_db->f("Type") . " DEFAULT '" . $this->backup_db->f("Default") . "'" . ($this->backup_db->f("Null") === "YES" ? " NOT NULL" : '');
				}
			}
		}
		return true;
	}

	/**
	 * Function: fixTableNames
	 *
	 * Description: The function convert default table names to
	 * real table names
	 */
	function fixTableNames(&$arr){
		foreach($arr as $key => $val){
			$name = $this->fixTableName($val);
			$arr[$key] = $name;
		}
		array_unique($arr);
	}

	/**
	 * Function: fixTableName
	 *
	 * Description: This function checks and returns the real name of a
	 * given default table name.
	 */
	function fixTableName($tabname){
		$tabname = strtolower($tabname);

		if(substr($tabname, 0, 10) === "tblobject_" && defined('OBJECT_X_TABLE')){
			return str_ireplace("tblobject_", OBJECT_X_TABLE, $tabname);
		}
		return (isset($this->table_map[$tabname]) ?
				$this->table_map[$tabname] :
				$tabname);
	}

	/**
	 * Function: getDefaultTableName
	 *
	 * Description: The function returns default name for given
	 * real table name
	 */
	function getDefaultTableName($tabname){
		$tabname = strtolower($tabname);
		if(defined('OBJECT_X_TABLE') && stripos($tabname, OBJECT_X_TABLE) !== false){
			return str_ireplace(OBJECT_X_TABLE, "tblobject_", $tabname);
		}

		foreach($this->table_map as $k => $v){
			if($tabname == strtolower($v)){
				return $k;
			}
		}

		return $tabname;
	}

	/**
	 * Function: isWeTable
	 *
	 * Description: The function checks if given  name
	 * is webEdition table name
	 */
	function isWeTable($tabname){
		if(in_array(strtolower($tabname), array_keys($this->table_map))){
			return true;
		}
		if(defined('OBJECT_X_TABLE')){
			$object_x_table = stripTblPrefix(OBJECT_X_TABLE);

			return stripos($tabname, $object_x_table) !== false;
		}
		return false;
	}

	/**
	 * Function: setError
	 *
	 * Description: This function sets a value for an error.
	 */
	function setError($errtxt){
		$this->errors[] = $errtxt;
	}

	/**
	 * Function: setWarning
	 *
	 * Description: This function sets a value for a warning.
	 */
	function setWarning($wartxt){
		$this->warnings[] = $wartxt;
	}

	/**
	 * Function: getErrors
	 *
	 * Description: This function returns errors if any were set.
	 */
	function getErrors(){
		return $this->errors;
	}

	/**
	 * Function: getWarnings
	 *
	 * Description: This function returns warnings if any were set.
	 */
	function getWarnings(){
		return $this->warnings;
	}

	//FIMXE: remove this function
	/**
	 * Function: arrayintersect
	 *
	 * Description:
	 */
	function arrayintersect($array1, $array2){
		$ret = array();
		foreach($array1 as $v){
			if(!is_array($v) && in_array($v, $array2)){
				$ret[] = $v;
			}
		}
		return $ret;
	}

	//FIMXE: remove this function
	/**
	 * Function: arraydiff
	 * Description:
	 */
	function arraydiff($array1, $array2){
		$ret = array();
		foreach($array1 as $v){
			if(!is_array($v) && !in_array($v, $ret) && !in_array($v, $array2)){
				$ret[] = $v;
			}
		}
		return $ret;
	}

	protected function _saveState(){
		//FIXME: use __sleep/__wakeup
		//		// Initialize variable
		return '
$this->errors=' . var_export($this->errors, true) . ';
$this->warnings=' . var_export($this->warnings, true) . ';
$this->extables=' . var_export($this->extables, true) . ';
$this->dumpfilename=' . var_export($this->dumpfilename, true) . ';
$this->tempfilename=' . var_export($this->tempfilename, true) . ';
$this->handle_options=' . var_export($this->handle_options, true) . ';
$this->properties=' . var_export($this->properties, true) . ';
$this->dummy=' . var_export($this->dummy, true) . ';
';
	}


	/**
	 * Function: restoreState
	 *
	 * Description:
	 */
	function restoreState($temp_filename){
		//FIXME: use __sleep/__wakeup
		if(file_exists(BACKUP_PATH . "tmp/" . $temp_filename)){
			include(BACKUP_PATH . "tmp/" . $temp_filename);
			return $temp_filename;
		}
		return 0;
	}

	private function clearOldTmp(){
		if(!is_writable(BACKUP_PATH . "tmp")){
			$this->setError(sprintf(g_l('backup', '[cannot_save_tmpfile]'), BACKUP_DIR));
			return -1;
		}

		$d = dir(BACKUP_PATH . "tmp");
		$co = -1;
		$limit = time() - 86400;
		while(false !== ($entry = $d->read())){
			if($entry != "." && $entry != ".." && !@is_dir($entry)){
				if(filemtime(BACKUP_PATH . '/tmp/' . $entry) < $limit){
					unlink(BACKUP_PATH . '/tmp/' . $entry);
				}
			}
		}
		$d->close();
	}

}
