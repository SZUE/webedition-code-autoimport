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
class weBackupExport{

	function export($filename, &$offset, &$row_count, $lines=1, $export_binarys=0, $log=0, $export_version_binarys=0){

		$_fh = fopen($filename, 'ab');

		if($_fh){

			if($offset == 0){

				$_table = weBackupUtil::getNextTable();

				// export table
				if($log){
					weBackupUtil::addLog(sprintf('Exporting table %s', $_table));
				}

				$_object = new weTableAdv($_table, true);

				$_attributes = array(
					'name' => weBackupUtil::getDefaultTableName($_table),
					'type' => 'create'
				);

				weContentProvider::object2xml($_object, $_fh, $_attributes);

				fwrite($_fh, '<!-- webackup -->' . "\n");
			}

			$_table = weBackupUtil::getCurrentTable();

			//sppedup for some tables
			if(isset($_table) && ($_table == LINK_TABLE || $_table == CONTENT_TABLE)){
				$lines = ((integer) $lines) * 5;
			} else if(isset($_table) && defined('BANNER_CLICKS_TABLE') && $_table == BANNER_CLICKS_TABLE){
				$lines = ((integer) $lines) * 5;
			}

			if(empty($_table)){
				return false;
			}

			// export table item

			$_keys = weTableItem::getTableKey($_table);
			$_keys_str = implode(',', $_keys);
			$_db = new DB_WE();

			$_query = 'SELECT ' . $_db->escape($_keys_str) . " FROM  " . $_db->escape($_table) . " ORDER BY $_keys_str LIMIT " . abs($offset) . " ," . abs($lines) . ";";

			$_db->query($_query);

			$_def_table = weBackupUtil::getDefaultTableName($_table);

			$_attributes = array(
				'table' => $_def_table
			);

			while($_db->next_record()) {

				$_keyvalue = array();
				foreach($_keys as $_key){
					$_keyvalue[$_key] = $_db->f($_key);
				}
				$_ids = implode(",", $_keyvalue);

				if($log){
					weBackupUtil::addLog(sprintf('Exporting item %s:%s', $_table, $_ids));
				}

				$_object = new weTableItem($_table);
				$_object->load($_keyvalue);


				weContentProvider::object2xml($_object, $_fh, $_attributes);
				fwrite($_fh, we_html_element::htmlComment("webackup") . "\n");


				if(($_def_table == 'tblfile' && $export_binarys) &&
					($_object->ContentType == "image/*" || stripos($_object->ContentType, "application/") !== false)
				){
					if($log){
						weBackupUtil::addLog(sprintf('Exporting binary data for item %s:%s', $_table, $_object->ID));
					}

					$bin = weContentProvider::getInstance('weBinary', $_object->ID);

					weContentProvider::binary2file($bin, $_fh);
				}

				if(($_def_table == 'tblversions' && $export_version_binarys)
				//&& ($_object->ContentType=="image/*" || stripos($_object->ContentType,"application/")!==false)
				){
					if($log){
						weBackupUtil::addLog(sprintf('Exporting version data for item %s:%s', $_table, $_object->ID));
					}

					$bin = weContentProvider::getInstance('weVersion', $_object->ID);

					weContentProvider::version2file($bin, $_fh);
				}

				$offset++;
				$row_count++;
			}

			$_table_end = f("SELECT COUNT(1) AS Count FROM " . $_db->escape($_table), 'Count', $_db);
			if($offset >= $_table_end){
				$offset = 0;
			}

			fclose($_fh);

			return true;
		}

		return false;
	}

}
