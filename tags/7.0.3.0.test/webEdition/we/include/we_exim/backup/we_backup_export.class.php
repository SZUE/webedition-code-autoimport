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
abstract class we_backup_export{

	public static function export($fh, &$offset, &$row_count, $lines = 1, $export_binarys = 0, $log = 0, $export_version_binarys = 0){

		if(!$fh){
			return false;
		}
		static $db = 0;
		$db = $db ? : new DB_WE();

		if($offset == 0){

			$table = we_backup_util::getNextTable();

			// export table
			if($log){
				we_backup_util::addLog(sprintf('Exporting table %s', $table));
			}

			$object = new we_backup_tableAdv($table, true);

			$attributes = array(
				'name' => we_backup_util::getDefaultTableName($table),
				'type' => 'create'
			);

			we_exim_contentProvider::object2xml($object, $fh, $attributes, $_SESSION['weS']['weBackupVars']['write']);

			$_SESSION['weS']['weBackupVars']['write']($fh, we_backup_util::backupMarker . "\n");
		}


		$table = $_SESSION['weS']['weBackupVars']['current_table'];

		//sppedup for some tables
		if(isset($table)){
			switch($table){
				case LANGLINK_TABLE:
				case RECIPIENTS_TABLE:
				case HISTORY_TABLE:
				case LINK_TABLE:
				case CONTENT_TABLE:
				case PREFS_TABLE:
				case (defined('BANNER_CLICKS_TABLE') ? BANNER_CLICKS_TABLE : 'BANNER_CLICKS_TABLE'):
					$lines = intval($lines) * 5;
					break;
				case FILE_TABLE://since binary files can be large
					$lines = ($export_binarys ? 1 : $lines);
					break;
				case VERSIONS_TABLE:
					$lines = ($export_version_binarys ? 1 : $lines);
					break;
			}
		}
		if(!$table){
			return false;
		}

		// export table item

		$keys = we_backup_tableItem::getTableKey($table);
		$keys_str = '`' . implode('`,`', $keys) . '`';

		$db->query('SELECT ' . $db->escape($keys_str) . ' FROM  ' . $db->escape($table) . ' ORDER BY ' . $keys_str . ' LIMIT ' . intval($offset) . ' ,' . intval($lines), true);
		$def_table = we_backup_util::getDefaultTableName($table);
		$attributes = array(
			'table' => $def_table
		);

		while($db->next_record()){
			$keyvalue = array();
			foreach($keys as $key){
				$keyvalue[$key] = $db->f($key);
			}
			$ids = implode(',', $keyvalue);

			if($log){
				we_backup_util::addLog(sprintf('Exporting item %s:%s', $table, $ids));
			}

			$object = new we_backup_tableItem($table);
			$object->load($keyvalue);


			we_exim_contentProvider::object2xml($object, $fh, $attributes);
			fwrite($fh, we_backup_util::backupMarker . "\n");



			switch($def_table){
				case 'tblfile':
					if($export_binarys && $object->ClassName){
						$obname = $object->ClassName;
						$tmp = new $obname;
						if($tmp->isBinary()){
							$bin = we_exim_contentProvider::getInstance('we_backup_binary', $object->ID);
							if($log){
								we_backup_util::addLog(sprintf('Exporting binary data %s, %s', $bin->Path, we_base_file::getHumanFileSize($bin->getFilesize())));
								we_backup_util::writeLog();
							}

							we_exim_contentProvider::binary2file($bin, $fh, $_SESSION['weS']['weBackupVars']['write']);
							if($log){
								we_backup_util::addLog(sprintf('done'));
								we_backup_util::writeLog();
							}
						}
					}
					break;

				case 'tblversions':
					if($export_version_binarys){
						if($log){
							we_backup_util::addLog(sprintf('Exporting version data for item %s:%s', $table, $object->ID));
							we_backup_util::writeLog();
						}

						we_exim_contentProvider::version2file(we_exim_contentProvider::getInstance('weVersion', $object->ID), $fh, $_SESSION['weS']['weBackupVars']['write']);
					}
					break;
			}

			$offset++;
			$row_count++;
		}

		$table_end = f('SELECT COUNT(1) FROM ' . $db->escape($table), '', $db);
		if($offset >= $table_end){
			$offset = 0;
		}
		fflush($fh);

		return true;
	}

}
