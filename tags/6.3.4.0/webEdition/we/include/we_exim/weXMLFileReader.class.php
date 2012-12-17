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
abstract class weXMLFileReader{

	static $file = array();

	static function readLine($filename, &$data, &$offset, $lines = 1, $size = 0, $iscompressed = 0){
		$prefix = $iscompressed == 0 ? 'f' : weFile::getComPrefix('gzip');
		$open = $prefix . 'open';
		$seek = $prefix . 'seek';
		$tell = $prefix . 'tell';
		$gets = $prefix . 'gets';
		$eof = $prefix . 'eof';

		if(empty(self::$file)){
			if($filename == '' || !is_readable($filename)){
				return false;
			}
			if(!($_fp = $open($filename, 'rb'))){
				return false;
			}
			$file = array(
				'fp' => $_fp,
				'offset' => 0);
		}


		if(($file['offset'] != $offset) && ($seek($file['fp'], $offset, SEEK_SET) != 0)){
			self::closeFile();
			return false;
		}

		$i = 0;
		$_condition = false;

		do{
			$_buffer = '';
			$_count = 0;
			$_rsize = 8192; // read 8KB
			do{

				$_buffer .= $gets($file['fp'], $_rsize);

				$_first = substr($_buffer, 0, 256);
				$_end = substr($_buffer, -20, 20);

				// chek if line is complite
				$_iswestart = stripos($_first, '<webEdition') !== false;
				$_isweend = stripos($_end, '</webEdition>') !== false;
				$_isxml = preg_match('|<\?xml|i', $_first);

				$_isend = preg_match("|<!-- *webackup *-->|", $_buffer) || empty($_buffer);

				if($_isend && self::preParse($_first)){
					$_buffer = '';
					$_isend = $eof($file['fp']);
				}

				if($_iswestart || $_isweend || $_isxml){
					$_buffer = '';
					$_isend = $eof($file['fp']);
				}
				// -----------------------------------------------------
				// avoid endless loop
				$_count++;
				if($_count > 100000){
					break;
				}
			} while(!$_isend);

			//  check condition
			if($size > 0){
				if(empty($_buffer)){
					$_condition = false && !$eof($file['fp']);
				} else{
					$i = strlen($_buffer);
					$_condition = ($i < $size ?
							true && !$eof($file['fp']) :
							false && !$eof($file['fp'])
						);
				}
			} else if($lines > 0){
				$_condition = ($i < $lines ?
						true && !$eof($file['fp']) :
						false
					);
				$i++;
			}

			$data .= $_buffer;
		} while($_condition);

		unset($_buffer);

		$offset = $tell($file['fp']);
		$file['offset'] = $offset;

		return (!empty($data));
	}

	public static function closeFile(){
		if(empty(self::$file)){
			return;
		}
		gzclose(self::$file['fp']);
		self::$file = array();
	}

	static function preParse(&$content){
		return false;
	}

}
