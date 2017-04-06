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
abstract class we_backup_XMLFileReader{
	const READ_SIZE = 32768;

	static $file = [];

	static function readLine($filename, &$offset, $lines = 1, $iscompressed = 0){
		$data = '';
		$prefix = $iscompressed == 0 ? 'f' : we_base_file::getComPrefix('gzip');
		$open = $prefix . 'open';
		$seek = $prefix . 'seek';
		$tell = $prefix . 'tell';
		$gets = $prefix . 'gets';
		$eof = $prefix . 'eof';

		if(empty(self::$file)){
			if(!$filename || !is_readable($filename)){
				return false;
			}
			if(!($fp = $open($filename, 'rb'))){
				return false;
			}
			self::$file = ['fp' => $fp,
				'offset' => $offset,
				];
			$seek($fp, $offset);
		}

		if((self::$file['offset'] != $offset) && ($seek(self::$file['fp'], $offset, SEEK_SET) != 0)){
			self::closeFile();
			return false;
		}

		$condition = false;

		do{
			$buffer = '';
			$count = 0;
			$start = microtime(true);
			do{

				$buffer .= $gets(self::$file['fp'], self::READ_SIZE);
				$first = substr($buffer, 0, 256);
				$end = substr($buffer, -20, 20);

				// chek if line is complete
				$iswestart = stripos($first, we_backup_util::weXmlExImHead) !== false;
				$isweend = stripos($end, we_backup_util::weXmlExImFooter) !== false;
				$isxml = preg_match('|<\?xml|i', $first);

				$isend = preg_match('|<!-- *webackup *-->|', $buffer) || empty($buffer);

				if($isend && we_backup_fileReader::preParse($first)){//preparse is true if table is not imported
					$buffer = '';
					$isend = $eof(self::$file['fp']) && we_backup_util::limitsReached('', max(0.1, microtime(true) - $start), 10);
					$count = 0;
					//keep time if we decided to end
					$start = $isend ? $start : microtime(true);
				}

				if($iswestart || $isweend || $isxml){
					$buffer = '';
					$isend = $eof(self::$file['fp']);
				}
				// -----------------------------------------------------
				// avoid endless loop

				if(++$count > 10000){
					t_e('line didn\'t end after 10000 iterations', strlen($buffer), $first, $end);
					break;
				}
			} while(!$isend);
			//  check condition
			$condition = --$lines > 0 && !$eof(self::$file['fp']) && we_backup_util::limitsReached('', max(0.1, microtime(true) - $start), 10);

			$data .= $buffer;
			$condition&=strlen($data) < (5 * 1024 * 1024);
		} while($condition);

		unset($buffer);

		self::$file['offset'] = $offset = $tell(self::$file['fp']);
		return $data;
	}

	public static function closeFile(){
		if(empty(self::$file)){
			return;
		}
		gzclose(self::$file['fp']);
		self::$file = [];
	}

}
