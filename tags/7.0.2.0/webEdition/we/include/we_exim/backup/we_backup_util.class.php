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
abstract class we_backup_util{
	const COMPRESSION = 'gzip';
	const NO_COMPRESSION = 'none';
	const backupMarker = '<!-- webackup -->';
	const weXmlExImHead = '<webEdition';
	const weXmlExImFooter = '</webEdition>';
	const weXmlExImProtectCode = '<?php exit();?>';
	const logFile = 'data/lastlog.php';

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

	static function getRealTableName($table){
		$table = strtolower($table);
		$match = array();
		if(preg_match('|tblobject_([0-9]*)$|', $table, $match)){
			return (isset($_SESSION['weS']['weBackupVars']['tables']['tblobject_']) ?
					$_SESSION['weS']['weBackupVars']['tables']['tblobject_'] . $match[1] :
					false);
		}

		return (isset($_SESSION['weS']['weBackupVars']['tables'][$table]) ?
				$_SESSION['weS']['weBackupVars']['tables'][$table] :
				false);
	}

	static function getDefaultTableName($table){
		$match = array();
		if(defined('OBJECT_X_TABLE') && preg_match('|^' . OBJECT_X_TABLE . '([0-9]*)$|i', $table, $match)){
			if(isset($_SESSION['weS']['weBackupVars']['tables']['tblobject_'])){
				$max = f('SELECT MAX(ID) AS MaxTableID FROM ' . OBJECT_TABLE, 'MaxTableID', new DB_WE());
				if($match[1] <= $max){
					return 'tblobject_' . $match[1];
				}
			}

			return false;
		}

//$def_table = array_search($table,$_SESSION['weS']['weBackupVars']['tables']);
		foreach($_SESSION['weS']['weBackupVars']['tables'] as $key => $value){
			if(strtolower($table) == strtolower($value)){
				$def_table = $key;
			}
		}

// return false or default table name
		if(!empty($def_table)){
			return $def_table;
		}

		return false;
	}

	public static function getDescription($table, $prefix){
		switch($table){
			case CONTENT_TABLE:
				return g_l('backup', '[' . $prefix . '_content]');
			case FILE_TABLE:
				return g_l('backup', '[' . $prefix . '_files]');
			case LINK_TABLE:
				return g_l('backup', '[' . $prefix . '_links]');
			case TEMPLATES_TABLE:
				return g_l('backup', '[' . $prefix . '_templates]');
			case TEMPORARY_DOC_TABLE:
				return g_l('backup', '[' . $prefix . '][temporary_data]');
			case HISTORY_TABLE:
				return g_l('backup', '[' . $prefix . '][history_data]');
			case INDEX_TABLE:
				return g_l('backup', '[' . $prefix . '_indexes]');
			case DOC_TYPES_TABLE:
				return g_l('backup', '[' . $prefix . '_doctypes]');
			case USER_TABLE:
				return g_l('backup', '[' . $prefix . '_user_data]');
			case (defined('CUSTOMER_TABLE') ? CUSTOMER_TABLE : 'CUSTOMER_TABLE'):
				return g_l('backup', '[' . $prefix . '_customer_data]');
			case (defined('SHOP_TABLE') ? SHOP_TABLE : 'SHOP_TABLE'):
				return g_l('backup', '[' . $prefix . '_shop_data]');
			case (defined('PREFS_TABLE') ? PREFS_TABLE : 'PREFS_TABLE'):
				return g_l('backup', '[' . $prefix . '_prefs]');
			case (defined('BANNER_CLICKS_TABLE') ? BANNER_CLICKS_TABLE : 'BANNER_CLICKS_TABLE'):
				return g_l('backup', '[' . $prefix . '_banner_data]');
			default:
				return g_l('backup', '[working]');
		}
	}

	public static function getImportPercent(){
		if(isset($_SESSION['weS']['weBackupVars']['files_to_delete_count'])){
			$rest1 = intval($_SESSION['weS']['weBackupVars']['files_to_delete_count']) - count($_SESSION['weS']['weBackupVars']['files_to_delete']);
			$rest2 = intval($_SESSION['weS']['weBackupVars']['files_to_delete_count']);
		} else {
			$rest1 = 0;
			$rest2 = 0;
		}

		$percent = round(((float)
			(intval($_SESSION['weS']['weBackupVars']['offset'] + $rest1) /
			intval($_SESSION['weS']['weBackupVars']['offset_end'] + $rest2))) * 100, 2);

		return max(min($percent, 100), 0);
	}

	public static function getProgressJS($percent, $description, $return){
		$ret = 'if(top.busy && top.busy.setProgressText && top.busy.setProgress){
		top.busy.setProgressText("current_description", "' . $description . '");
		top.busy.setProgress(' . $percent . ');
}';
		if($return){
			return $ret;
		}

		echo we_html_element::jsElement($ret . '
			/*' . (time() - $_SESSION['weS']['weBackupVars']['limits']['requestTime']) . 's, ' . we_base_file::getHumanFileSize(memory_get_usage(true)) . '*/
			');
		flush();
	}

	public static function getExportPercent(){
		$all = intval($_SESSION['weS']['weBackupVars']['row_count']);
		$done = intval($_SESSION['weS']['weBackupVars']['row_counter']);

		if(isset($_SESSION['weS']['weBackupVars']['extern_files'])){
			$all += intval($_SESSION['weS']['weBackupVars']['extern_files_count']);
			$done += intval($_SESSION['weS']['weBackupVars']['extern_files_count']) - count($_SESSION['weS']['weBackupVars']['extern_files']);
		}

		$percent = round(($done / $all) * 100, ($all > 50000 ? 2 : 1));
		return max(min($percent, 100), 0);
	}

	public static function canImportBinary($id, $path){
		if($id){
			return $_SESSION['weS']['weBackupVars']['options']['backup_binary'];
		}
		static $settingsFiles = array();
		$settingsFiles = $settingsFiles? : we_backup_util::getSettingsFiles(true);
		$isSetting = in_array($path, $settingsFiles);

		if(($_SESSION['weS']['weBackupVars']['handle_options']['settings'] && $isSetting) ||
			($_SESSION['weS']['weBackupVars']['options']['backup_extern'] && !$isSetting) ||
			//($_SESSION['weS']['weBackupVars']['handle_options']['spellchecker'] && strpos($path, WE_MODULES_DIR . 'spellchecker') === 0 ) ||
			($_SESSION['weS']['weBackupVars']['handle_options']['hooks'] && strpos($path, WE_INCLUDES_PATH . 'we_hook/custom_hooks') === 0) ||
			($_SESSION['weS']['weBackupVars']['handle_options']['customTags'] && strpos($path, WE_INCLUDES_PATH . 'we_tags/custom_tags') === 0) ||
			($_SESSION['weS']['weBackupVars']['handle_options']['customTags'] && strpos($path, WE_INCLUDES_PATH . 'weTagWizard/we_tags/custom_tags') === 0)
		){
			return true;
		}

		return false;
	}

	public static function canImportVersion($id, $path){
		return (!empty($id) && stristr($path, VERSION_DIR) && $_SESSION['weS']['weBackupVars']['handle_options']['versions_binarys']);
	}

	static function exportFile($file, $fh, $fwrite = 'fwrite'){
		$bin = we_exim_contentProvider::getInstance('we_backup_binary', 0);
		$bin->Path = $file;
		we_exim_contentProvider::binary2file($bin, $fh, $fwrite);
	}

	static function exportFiles($to, array $files){
		if(($fh = $_SESSION['weS']['weBackupVars']['open']($to, 'ab'))){
			foreach($files as $file){
				self::exportFile($file, $fh, $_SESSION['weS']['weBackupVars']['write']);
			}
			$_SESSION['weS']['weBackupVars']['close']($fh);
		}
	}

	static function getNextTable(){
		if(!isset($_SESSION['weS']['weBackupVars']['allTables'])){
			$db = new DB_WE();
			$_SESSION['weS']['weBackupVars']['allTables'] = $db->table_names();
		}
// get all table names from database
		$tables = $_SESSION['weS']['weBackupVars']['allTables'];
		$do = true;

		do{
			if(++$_SESSION['weS']['weBackupVars']['current_table_id'] < count($tables)){
// get real table name from database
				$table = $tables[$_SESSION['weS']['weBackupVars']['current_table_id']]['table_name'];

				$def_table = self::getDefaultTableName($table);

				if($def_table !== false){

					$do = false;

					$_SESSION['weS']['weBackupVars']['current_table'] = $table;
				}
			} else {
				$_SESSION['weS']['weBackupVars']['current_table'] = false;
				$do = false;
			}
		} while($do);

		return $_SESSION['weS']['weBackupVars']['current_table'];
	}

	public static function addLog($log){
		if(isset($_SESSION['weS']['weBackupVars']['backup_log_data'])){
			$_SESSION['weS']['weBackupVars']['backup_log_data'] .= '[' . date('d-M-Y H:i:s', time()) . '] ' . $log . "\r\n";
		}
	}

	public static function writeLog(){
		if(empty($_SESSION['weS']['weBackupVars']['backup_log_data'])){
			return;
		}
		if($_SESSION['weS']['weBackupVars']['backup_log']){
			we_base_file::save($_SESSION['weS']['weBackupVars']['backup_log_file'], $_SESSION['weS']['weBackupVars']['backup_log_data'], 'ab');
		}
		$_SESSION['weS']['weBackupVars']['backup_log_data'] = '';
	}

	public static function getFormat($file, $iscompr = 0){
		$part = we_base_file::loadPart($file, 0, 512, $iscompr);

		return (preg_match('|<\?xml |i', $part) ?
				'xml' :
				'unknown');
	}

	public static function getXMLImportType($file, $iscompr = 0, $end_off = 0){
		$found = 'unknown';
		$try = 0;
		$count = 30;
		$part_len = 16384;
		$part_skip_len = 204800;

		if($end_off == 0){
			$end_off = self::getEndOffset($file, $iscompr);
		}

		$start = $end_off - $part_len;

		$part = we_base_file::loadPart($file, 0, $part_len, $iscompr);

		if($part === false){
			return 'unreadble';
		}

		if(stripos($part, we_backup_util::weXmlExImHead) === false){
			return 'unknown';
		}
		$hasbinary = false;
		while($found === 'unknown' && $try < $count){
			if(preg_match('/.*' . we_backup_util::weXmlExImHead . '.*type="backup".*>/', $part)){
				return 'backup';
			} elseif(preg_match('/<we:(document|template|class|object|info|navigation)/i', $part)){
				return 'weimport';
			} elseif(stripos($part, '<we:table') !== false){
				return 'backup';
			} elseif(stripos($part, '<we:binary') !== false){
				$hasbinary = true;
			} elseif(stripos($part, '<customer') !== false){
				return 'customer';
			}

			$part = we_base_file::loadPart($file, $start, $part_len, $iscompr);

			$start = $start - $part_skip_len;

			$try++;
		}

		if($found === 'unknown' && $hasbinary){
			return 'weimport';
		}

		return $found;
	}

	public static function getEndOffset($filename, $iscompressed){
		$end = 0;
		if($iscompressed == 0){
			$fh = fopen($filename, 'rb');
			if($fh){
				fseek($fh, 0, SEEK_END);
				$end = ftell($fh);
				fclose($fh);
			}
		} else {

			$fh = gzopen($filename, 'rb');
			while(!gzeof($fh)){
				gzread($fh, 16768);
			}
			$end = gztell($fh);
			gzclose($fh);
		}
		return $end;
	}

	public static function hasNextTable(){
		$current_id = $_SESSION['weS']['weBackupVars']['current_table_id'];
		$current_id++;

		if(!isset($_SESSION['weS']['weBackupVars']['allTables'])){
			$db = new DB_WE();
			$_SESSION['weS']['weBackupVars']['allTables'] = $db->table_names();
		}
// get all table names from database
		$tables = $_SESSION['weS']['weBackupVars']['allTables'];

		if($current_id < count($tables)){
			$table = $tables[$current_id]['table_name'];
			if(self::getDefaultTableName($table) === false){
				return false;
			}

			return true;
		}

		return false;
	}

}
