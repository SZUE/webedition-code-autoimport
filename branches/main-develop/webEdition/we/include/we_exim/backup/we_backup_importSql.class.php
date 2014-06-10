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
define('BACKUP_TABLE', TBL_PREFIX . 'tblbackup');

class we_backup_importSql{

	function import($filename, &$offset, $lines = 1, $iscompressed = 0, $encoding = 'ISO-8859-1'){

		we_backup_util::addLog(sprintf('Reading offset %s', $offset));

		for($i = 0; $i < $lines; $i++){

			$_data = '';
			$_create = '';
			$_insert = '';

			$_fileReader = new we_backup_sqlFileReader();
			if($_fileReader->readLine($filename, $_data, $offset, 1, 0, $iscompressed, $_create, $_insert)){

				self::transfer($_data, $encoding, $_create, $_insert);

				if($_insert == BACKUP_TABLE){
					we_backup_importSql::flushBackupTable();
				}
			} else {

				return;
			}
		}
		//exit();
	}

	private static function transfer(&$data, $charset = 'ISO-8859-1', &$create, &$insert){
		if($create != ''){

			$_table = we_backup_util::getRealTableName($create);
			if($_table !== false){
				we_backup_util::setBackupVar('current_table', $_table);

				we_backup_util::addLog('Creating table ' . $_table);

				if($_table == BACKUP_TABLE){ // make exception for backup table
					$_start = substr($data, 0, 64); // take the chunk

					$_start = str_replace($create, $_table, $_start); // replace with real table name

					$data = $_start . substr($data, 64);

					$GLOBALS['DB_WE']->query('DROP TABLE IF EXISTS ' . $GLOBALS['DB_WE']->escape($_table));
					if(!$GLOBALS['DB_WE']->query("$data")){

						we_backup_util::addLog('DB Error: ' . $GLOBALS['DB_WE']->Error);
					}
				} else {

					$_object = new we_backup_table($_table, (defined('CUSTOMER_TABLE') && $_table == CUSTOMER_TABLE));
					$_object->save();
				}
				$create = $_table;
			}
		} else if($insert != ''){

			$_table = we_backup_util::getRealTableName($insert);

			if($_table !== false){

				$_start = substr($data, 0, 64); // take the chunk

				$_start = str_replace($insert, $_table, $_start); // replace with real table name

				$data = $_start . substr($data, 64);

				we_backup_util::addLog('Inserting into table ' . $_table);

				if(!$GLOBALS['DB_WE']->query("$data")){
					we_backup_util::addLog('DB Error: ' . $GLOBALS['DB_WE']->Error);
				}

				$insert = $_table;
			}
		}
	}

	function flushBackupTable(){
		$_file = '';

		$GLOBALS['DB_WE']->query('SELECT * FROM ' . BACKUP_TABLE . ' WHERE IsFolder=0 ORDER BY Path ASC;');

		while($GLOBALS['DB_WE']->next_record()){
			$_file = new weBinary();
			$_file->ID = 0;
			$_file->Path = $GLOBALS['DB_WE']->f('Path');
			$_file->Data = $GLOBALS['DB_WE']->f('Data');
			$_file->save(true);
		}

		$GLOBALS['DB_WE']->query('TRUNCATE ' . BACKUP_TABLE);
		unset($_file);
	}

	function delBackupTable(){
		$GLOBALS['DB_WE']->query('DROP TABLE IF EXISTS ' . BACKUP_TABLE);
	}

}
