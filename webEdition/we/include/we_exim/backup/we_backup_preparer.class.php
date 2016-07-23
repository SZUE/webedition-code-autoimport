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
abstract class we_backup_preparer{

	private static function checkFilePermission(){
		if(!is_writable(TEMP_PATH)){
			we_backup_util::addLog('Error: Can\'t write to ' . TEMP_PATH);
			t_e('Error: Can\'t write to ' . TEMP_PATH);
			return false;
		}
		return true;
	}

	private static function prepare(){

		if(!self::checkFilePermission()){
			return false;
		}
		$execTime = ini_get('max_execution_time');

		$_SESSION['weS']['weBackupVars'] = array(
			'options' => [],
			'handle_options' => [],
			'offset' => 0,
			'current_table' => '',
			'backup_steps' => 5,
			'backup_log' => we_base_request::_(we_base_request::BOOL, 'backup_log'),
			'backup_log_data' => '',
			'backup_log_file' => BACKUP_PATH . we_backup_util::logFile,
			'limits' => array(
				'mem' => min(60 * 1024 * 1024, we_convertIniSizes(ini_get('memory_limit'))),
				'exec' => min(30, ($execTime > 120 ? ($execTime > 2000 ? 5 : 15) : $execTime)),
				/* fix for really faulty php installations, e.g. 1&1
				 * 1&1 execution time >2000, no matter if the script gets killed after 15 seconds
				 * df execution time = 180,  no matter if the script gets killed after 20 seconds
				 */
				'requestTime' => 0,
				'lastMem' => 0,
			),
			'retry' => 0,
		);

		self::getOptions($_SESSION['weS']['weBackupVars']['options'], $_SESSION['weS']['weBackupVars']['handle_options']);
		$_SESSION['weS']['weBackupVars']['tables'] = self::getTables($_SESSION['weS']['weBackupVars']['handle_options']);

		if($_SESSION['weS']['weBackupVars']['backup_log']){
			we_base_file::save($_SESSION['weS']['weBackupVars']['backup_log_file'], "<?php exit();?>\r\n");
		}

		return true;
	}

	static function prepareExport(){
		if(!self::prepare()){
			return false;
		}
		we_updater::fixInconsistentTables();

		$_SESSION['weS']['weBackupVars']['protect'] = we_base_request::_(we_base_request::BOOL, 'protect');

		$_SESSION['weS']['weBackupVars']['options']['compress'] = (we_base_file::hasCompression(we_base_request::_(we_base_request::BOOL, 'compress'))) ? we_backup_util::COMPRESSION : we_backup_util::NO_COMPRESSION;
		$_SESSION['weS']['weBackupVars']['filename'] = we_base_request::_(we_base_request::FILE, 'filename') . ($_SESSION['weS']['weBackupVars']['options']['compress'] != we_backup_util::NO_COMPRESSION ? '.' . we_base_file::getZExtension(we_backup_util::COMPRESSION) : '');
		$_SESSION['weS']['weBackupVars']['backup_file'] = TEMP_PATH . $_SESSION['weS']['weBackupVars']['filename'];
		$prefix = we_base_file::getComPrefix($_SESSION['weS']['weBackupVars']['options']['compress']);
		$_SESSION['weS']['weBackupVars']['open'] = $prefix . 'open';
		$_SESSION['weS']['weBackupVars']['close'] = $prefix . 'close';
		$_SESSION['weS']['weBackupVars']['write'] = $prefix . 'write';

		$_SESSION['weS']['weBackupVars']['current_table_id'] = -1;

		if($_SESSION['weS']['weBackupVars']['options']['backup_extern']){
			$_SESSION['weS']['weBackupVars']['extern_files'] = [];
			self::getFileList($_SESSION['weS']['weBackupVars']['extern_files']);
			$_SESSION['weS']['weBackupVars']['extern_files_count'] = count($_SESSION['weS']['weBackupVars']['extern_files']);
		}

		$_SESSION['weS']['weBackupVars']['row_counter'] = 0;
		$_SESSION['weS']['weBackupVars']['row_count'] = 0;

		$db = new DB_WE();
		$db->query('SHOW TABLE STATUS');
		while($db->next_record()){
			// fix for object tables
			//if(in_array($db->f('Name'),$_SESSION['weS']['weBackupVars']['tables'])) {
			if(($name = we_backup_util::getDefaultTableName($db->f('Name'))) !== false){
				$_SESSION['weS']['weBackupVars']['row_count'] += $db->f('Rows');
				$_SESSION['weS']['weBackupVars']['avgLen'][$name] = $db->f('Avg_row_length');
			}
		}

		//always write protect code uncompressed
		we_base_file::save($_SESSION['weS']['weBackupVars']['backup_file'], ($_SESSION['weS']['weBackupVars']['protect'] ? we_backup_util::weXmlExImProtectCode : ''), 'wb');
		we_base_file::save($_SESSION['weS']['weBackupVars']['backup_file'], we_exim_XMLExIm::getHeader('', 'backup'), 'ab', $_SESSION['weS']['weBackupVars']['options']['compress']);

		return true;
	}

	static function prepareImport(){
		if(!self::prepare()){
			return false;
		}

		$_SESSION['weS']['weBackupVars']['backup_file'] = self::getBackupFile();
		if($_SESSION['weS']['weBackupVars']['backup_file'] === false){
			return false;
		}

		$offset = strlen(we_backup_util::weXmlExImProtectCode);
		$_SESSION['weS']['weBackupVars']['offset'] = (we_base_file::loadLine($_SESSION['weS']['weBackupVars']['backup_file'], 0, ($offset)) == we_backup_util::weXmlExImProtectCode) ? $offset : 0;
		$_SESSION['weS']['weBackupVars']['options']['compress'] = we_base_file::isCompressed($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['offset']) ? we_backup_util::COMPRESSION : we_backup_util::NO_COMPRESSION;

		$_SESSION['weS']['weBackupVars']['options']['format'] = we_backup_util::getFormat($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['options']['compress'] != we_backup_util::NO_COMPRESSION);

		$_SESSION['weS']['weBackupVars']['offset_end'] = we_backup_util::getEndOffset($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['options']['compress'] != we_backup_util::NO_COMPRESSION);

		if($_SESSION['weS']['weBackupVars']['options']['format'] === 'xml'){
			$_SESSION['weS']['weBackupVars']['options']['xmltype'] = we_backup_util::getXMLImportType($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['options']['compress'] != we_backup_util::NO_COMPRESSION, $_SESSION['weS']['weBackupVars']['offset_end']);
			if($_SESSION['weS']['weBackupVars']['options']['xmltype'] != 'backup'){
				return false;
			}
		}

		$_SESSION['weS']['weBackupVars']['encoding'] = self::getEncoding($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['options']['compress'] != we_backup_util::NO_COMPRESSION);
		$_SESSION['weS']['weBackupVars']['weVersion'] = self::getWeVersion($_SESSION['weS']['weBackupVars']['backup_file'], $_SESSION['weS']['weBackupVars']['options']['compress'] != we_backup_util::NO_COMPRESSION);

		if($_SESSION['weS']['weBackupVars']['handle_options']['core']){
			self::clearTemporaryData('tblFile');
			$_SESSION['weS']['weBackupVars']['files_to_delete'] = self::getFileLists();
			$_SESSION['weS']['weBackupVars']['files_to_delete_count'] = count($_SESSION['weS']['weBackupVars']['files_to_delete']);
		}

		if($_SESSION['weS']['weBackupVars']['handle_options']['versions'] || $_SESSION['weS']['weBackupVars']['handle_options']['core'] || $_SESSION['weS']['weBackupVars']['handle_options']['object'] || $_SESSION['weS']['weBackupVars']['handle_options']['versions_binarys']
		){
			self::clearVersionData();
		}

		if($_SESSION['weS']['weBackupVars']['handle_options']['object']){
			self::clearTemporaryData('tblObjectFiles');
		}

		return true;
	}

	static function getOptions(&$options, &$handle_options){
		$options = array(
			'backup_extern' => we_base_request::_(we_base_request::BOOL, 'handle_extern'),
			'convert_charset' => we_base_request::_(we_base_request::BOOL, "convert_charset"),
			'compress' => we_base_request::_(we_base_request::BOOL, 'compress') ? we_backup_util::COMPRESSION : we_backup_util::NO_COMPRESSION,
			'backup_binary' => we_base_request::_(we_base_request::BOOL, 'handle_binary'),
			'rebuild' => we_base_request::_(we_base_request::BOOL, 'rebuild'),
			'export2server' => we_base_request::_(we_base_request::BOOL, 'export_server'),
			'export2send' => we_base_request::_(we_base_request::BOOL, 'export_send'),
			'do_import_after_backup' => we_base_request::_(we_base_request::BOOL, 'do_import_after_backup'),
		);

		$handle_options = array(
			'user' => we_base_request::_(we_base_request::BOOL, 'handle_user'),
			'customer' => we_base_request::_(we_base_request::BOOL, 'handle_customer'),
			'shop' => we_base_request::_(we_base_request::BOOL, 'handle_shop'),
			'workflow' => we_base_request::_(we_base_request::BOOL, 'handle_workflow'),
			'todo' => we_base_request::_(we_base_request::BOOL, 'handle_todo'),
			'newsletter' => we_base_request::_(we_base_request::BOOL, 'handle_newsletter'),
			'temporary' => we_base_request::_(we_base_request::BOOL, 'handle_temporary'),
			'history' => we_base_request::_(we_base_request::BOOL, 'handle_history'),
			'banner' => we_base_request::_(we_base_request::BOOL, 'handle_banner'),
			'core' => we_base_request::_(we_base_request::BOOL, 'handle_core'),
			'object' => we_base_request::_(we_base_request::BOOL, 'handle_object'),
			'schedule' => we_base_request::_(we_base_request::BOOL, 'handle_schedule'),
			'settings' => we_base_request::_(we_base_request::BOOL, 'handle_settings'),
			'configuration' => we_base_request::_(we_base_request::BOOL, 'handle_configuration'),
			'export' => we_base_request::_(we_base_request::BOOL, 'handle_export'),
			'voting' => we_base_request::_(we_base_request::BOOL, 'handle_voting'),
			'versions' => we_base_request::_(we_base_request::BOOL, 'handle_versions'),
			'versions_binarys' => we_base_request::_(we_base_request::BOOL, 'handle_versions_binarys'),
			'tools' => [],
			//'spellchecker' => we_base_request::_(we_base_request::BOOL, 'handle_spellchecker'),
			'glossary' => we_base_request::_(we_base_request::BOOL, 'handle_glossary'),
			'hooks' => we_base_request::_(we_base_request::BOOL, "handle_hooks"),
			'customTags' => we_base_request::_(we_base_request::BOOL, "handle_customtags"),
			'backup' => $options['backup_extern'],
		);

		if(($tools = we_base_request::_(we_base_request::BOOL, 'handle_tool'))){
			foreach(array_keys($tools) as $tool){
				if(we_tool_lookup::isTool($tool)){
					$handle_options['tools'][] = $tool;
				}
			}
		}

		if($options['convert_charset']){
			$handle_options['settings'] = 0;
			$handle_options['spellchecker'] = 0;
		}
	}

	static function getTables($options){
		include(WE_INCLUDES_PATH . 'we_exim/backup/weTableMap.inc.php');

		$tables = [];
		foreach($options as $group => $enabled){
			if($enabled && isset($tableMap[$group])){
				$tables = array_merge($tables, $tableMap[$group]);
			}
		}

		if($options['tools']){
			foreach($options['tools'] as $tool){
				$tables = array_merge($tables, we_tool_lookup::getBackupTables($tool));
			}
		}

		return $tables;
	}

	private static function getBackupFile(){
		if(($backup_select = we_base_request::_(we_base_request::FILE, 'backup_select'))){
			return BACKUP_PATH . $backup_select;
		}

		$fileUploader = new we_fileupload_resp_base();
		//$fileUploader->setTypeCondition('accepted', array(we_base_ContentTypes::XML), array('.gz', '.tgz'));
		if(($commitedFile = $fileUploader->commitUploadedFile(true))){
			we_base_file::insertIntoCleanUp($commitedFile, 0);
			return $commitedFile;
		} else {
			t_e('we_fileupload failure', $this->fileUploader->getError());
		}

		return false;
	}

	static function getExternalFiles(){
		$list = [];
		self::getFileList($list, TEMPLATES_PATH, true, false);
		return $list;
	}

	static function getFileLists(){
		$list = [];
		self::getFileList($list, TEMPLATES_PATH, true, false);
		self::getFileList($list, WE_CACHE_PATH, true, false);
		self::getSiteFiles($list);
		return $list;
	}

	public static function getFileList(array &$list, $dir = '', $with_dirs = false, $rem_doc_root = true){
		$dir = ($dir ? : $_SERVER['DOCUMENT_ROOT']);
		if(!is_readable($dir) || !is_dir($dir)){
			return false;
		}
		$thumbDir = trim(WE_THUMBNAIL_DIRECTORY, '/');

		$d = dir($dir);
		while(false !== ($entry = $d->read())){
			switch($entry){
				case '.':
				case '..':
				case 'webEdition':
				case 'sql_dumps':
				case '.project':
				case '.trustudio.dbg.php':
				case $thumbDir:
					continue;
				default:
					$file = $dir . '/' . $entry;
					if(!self::isPathExist(str_replace($_SERVER['DOCUMENT_ROOT'], '', $file))){
						if(is_dir($file)){
							if($with_dirs){
								self::addToFileList($list, $file, $rem_doc_root);
							}
							self::getFileList($list, $file, $with_dirs, $rem_doc_root);
						} else {
							self::addToFileList($list, $file, $rem_doc_root);
						}
					} elseif(is_dir($file)){
						self::getFileList($list, $file, $with_dirs, $rem_doc_root);
					}
			}
		}
		$d->close();
	}

	static function addToFileList(array &$list, $file, $rem_doc_root = true){
		$list[] = ($rem_doc_root ? str_replace($_SERVER['DOCUMENT_ROOT'], '', $file) : $file);
	}

	static function getSiteFiles(array &$out){
		global $DB_WE;

		$list = [];
		self::getFileList($list, $_SERVER['DOCUMENT_ROOT'] . SITE_DIR, true, false);
		foreach($list as $file){
			//don't use f/getHash since RAM usage
			$DB_WE->query('SELECT ContentType FROM ' . FILE_TABLE . ' WHERE Path="' . $DB_WE->escape(str_replace($_SERVER['DOCUMENT_ROOT'] . rtrim(SITE_DIR, '/'), '', $file)) . '"', false, true);
			$DB_WE->next_record();
			switch($DB_WE->f('ContentType')){
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
	}

	private static function clearTemporaryData($docTable){
		global $DB_WE;
		$DB_WE->query('DELETE FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocTable="' . stripTblPrefix($docTable) . '"');
		$DB_WE->query('TRUNCATE TABLE ' . NAVIGATION_TABLE);
		$DB_WE->query('TRUNCATE TABLE ' . NAVIGATION_RULE_TABLE);
		$DB_WE->query('TRUNCATE TABLE ' . HISTORY_TABLE);
		$DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE ClassID' . ($docTable === FILE_TABLE ? '=0' : '>0'));
	}

	static function clearVersionData(){
		global $DB_WE;
		$DB_WE->query('TRUNCATE TABLE ' . VERSIONS_TABLE . ';');
		$path = $_SERVER['DOCUMENT_ROOT'] . VERSION_DIR;
		if(($dir = opendir($path))){
			while(($file = readdir($dir))){
				switch($file){
					case '.':
					case '..':
						break;
					default:
						if(!is_dir($file)){
							unlink($path . $file);
						}
				}
			}
			closedir($dir);
		}
	}

	static function isPathExist($path){
		global $DB_WE;

		return ((f('SELECT 1 FROM ' . FILE_TABLE . ' WHERE Path="' . $DB_WE->escape($path) . '" LIMIT 1', '', $DB_WE) == 1) ||
			(f('SELECT 1 FROM ' . TEMPLATES_TABLE . ' WHERE Path="' . $DB_WE->escape($path) . '" LIMIT 1', '', $DB_WE) == 1));
	}

	static function getEncoding($file, $iscompressed){
		if(!empty($file)){
			$data = we_base_file::loadPart($file, 0, 256, $iscompressed);
			$match = [];
			$pattern = "%(encoding\s*=\s*[\"\'\\\\]\s*)([^\'\"> ? \\\]*)%";

			if(preg_match($pattern, $data, $match)){
				if(strtoupper($match[2]) != 'ISO-8859-1'){
					return 'UTF-8';
				}
			}
		}

		return 'ISO-8859-1';
	}

	static function getWeVersion($file, $iscompressed){
		if(!empty($file)){
			$data = we_base_file::loadPart($file, 0, 256, $iscompressed);
			$match = [];
			$pattern = "%webEdition\s*version\s*=\s*[\"\'\\\\]\s*([^\'\"> ? \\\]*)%";

			if(preg_match($pattern, $data, $match)){
				return $match[1];
			}
		}

		return -1;
	}

	static function isOtherXMLImport($format){

		switch($format){
			case 'weimport':
				if(permissionhandler::hasPerm('WXML_IMPORT')){
					return we_html_element::jsElement('
							if(confirm("' . str_replace('"', '\'', g_l('backup', '[import_file_found]') . ' \n\n' . g_l('backup', '[import_file_found_question]')) . '")){
								top.opener.top.we_cmd("import");
								top.close();
							} else {
								top.body.location = "' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=recover_backup&pnt=body&step=2";
							}');
				} else {
					return we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('backup', '[import_file_found]'), we_message_reporting::WE_MESSAGE_WARNING) .
							'top.body.location = "' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=recover_backup&pnt=body&step=2";');
				}
			case 'customer':
				return we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('backup', '[customer_import_file_found]'), we_message_reporting::WE_MESSAGE_WARNING) .
						'top.body.location = "' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=recover_backup&pnt=body&step=2";');
			default:
				return we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('backup', '[format_unknown]'), we_message_reporting::WE_MESSAGE_WARNING) .
						'top.body.location = "' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=recover_backup&pnt=body&step=2";');
		}
	}

	static function getErrorMessage(){
		$mess = '';

		if(!($_SESSION['weS']['weBackupVars']['backup_file'])){
			if(isset($_SESSION['weS']['weBackupVars']['options']['upload'])){
				$mess = sprintf(g_l('backup', '[upload_failed]'), we_base_file::getHumanFileSize(getUploadMaxFilesize(), we_base_file::SZ_MB));
			} else {
				$mess = g_l('backup', '[file_missing]');
			}
		} else if(!is_readable($_SESSION['weS']['weBackupVars']['backup_file'])){

			$mess = g_l('backup', '[file_not_readable]');
		} else if($_SESSION['weS']['weBackupVars']['options']['format'] != 'xml' && $_SESSION['weS']['weBackupVars']['options']['format'] != 'sql'){

			$mess = g_l('backup', '[format_unknown]');
		} else if($_SESSION['weS']['weBackupVars']['options']['xmltype'] != 'backup'){

			return self::isOtherXMLImport($_SESSION['weS']['weBackupVars']['options']['xmltype']);
		} else if($_SESSION['weS']['weBackupVars']['options']['compress'] != we_backup_util::NO_COMPRESSION && !we_base_file::hasGzip()){

			$mess = g_l('backup', '[cannot_split_file_ziped]');
		} else {
			$mess = g_l('backup', '[unspecified_error]');
		}

		if(!empty($_SESSION['weS']['weBackupVars']['backup_log'])){
			we_backup_util::addLog('Error: ' . $mess);
		}

		return we_html_element::jsElement(we_message_reporting::getShowMessageCall($mess, we_message_reporting::WE_MESSAGE_ERROR) .
				'top.body.location = "' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=recover_backup&pnt=body&step=2";');
	}

}
