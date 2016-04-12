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

	public static function export($_fh, &$offset, &$row_count, $lines = 1, $export_binarys = 0, $log = 0, $export_version_binarys = 0){

		if(!$_fh){
			return false;
		}
		static $_db = 0;
		$_db = $_db ? : new DB_WE();

		if($offset == 0){

			$_table = we_backup_util::getNextTable();

			// export table
			if($log){
				we_backup_util::addLog(sprintf('Exporting table %s', $_table));
			}

			$_object = new we_backup_tableAdv($_table, true);

			$_attributes = array(
				'name' => we_backup_util::getDefaultTableName($_table),
				'type' => 'create'
			);

			we_exim_contentProvider::object2xml($_object, $_fh, $_attributes, $_SESSION['weS']['weBackupVars']['write']);

			$_SESSION['weS']['weBackupVars']['write']($_fh, we_backup_util::backupMarker . "\n");
		}


		$_table = $_SESSION['weS']['weBackupVars']['current_table'];

		//sppedup for some tables
		if(isset($_table)){
			switch($_table){
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
		if(!$_table){
			return false;
		}

		// export table item

		$_keys = we_backup_tableItem::getTableKey($_table);
		$_keys_str = '`' . implode('`,`', $_keys) . '`';

		$_db->query('SELECT ' . $_db->escape($_keys_str) . ' FROM  ' . $_db->escape($_table) . ' ORDER BY ' . $_keys_str . ' LIMIT ' . intval($offset) . ' ,' . intval($lines), true);
		$_def_table = we_backup_util::getDefaultTableName($_table);
		$_attributes = array(
			'table' => $_def_table
		);

		while($_db->next_record()){
			$_keyvalue = array();
			foreach($_keys as $_key){
				$_keyvalue[$_key] = $_db->f($_key);
			}
			$_ids = implode(',', $_keyvalue);

			if($log){
				we_backup_util::addLog(sprintf('Exporting item %s:%s', $_table, $_ids));
			}

			$_object = new we_backup_tableItem($_table);
			$_object->load($_keyvalue);


			we_exim_contentProvider::object2xml($_object, $_fh, $_attributes);
			fwrite($_fh, we_backup_util::backupMarker . "\n");



			switch($_def_table){
				case 'tblfile':
					if($export_binarys && $_object->ClassName){
						$obname = $_object->ClassName;
						$tmp = new $obname;
						if($tmp->isBinary()){
							$bin = we_exim_contentProvider::getInstance('we_backup_binary', $_object->ID);
							if($log){
								we_backup_util::addLog(sprintf('Exporting binary data %s, %s', $bin->Path, we_base_file::getHumanFileSize($bin->getFilesize())));
								we_backup_util::writeLog();
							}

							we_exim_contentProvider::binary2file($bin, $_fh, $_SESSION['weS']['weBackupVars']['write']);
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
							we_backup_util::addLog(sprintf('Exporting version data for item %s:%s', $_table, $_object->ID));
							we_backup_util::writeLog();
						}

						we_exim_contentProvider::version2file(we_exim_contentProvider::getInstance('weVersion', $_object->ID), $_fh, $_SESSION['weS']['weBackupVars']['write']);
					}
					break;
			}

			$offset++;
			$row_count++;
		}

		$_table_end = f('SELECT COUNT(1) FROM ' . $_db->escape($_table), '', $_db);
		if($offset >= $_table_end){
			$offset = 0;
		}
		fflush($_fh);

		return true;
	}

}
