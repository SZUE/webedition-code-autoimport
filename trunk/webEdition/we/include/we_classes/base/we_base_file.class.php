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
abstract class we_base_file{
	const SZ_HUMAN = 0;
	const SZ_BYTE = 1;
	const SZ_KB = 2;
	const SZ_MB = 3;

	static function load($filename, $flags = 'rb', $rsize = 8192, $iscompressed = false){
		if($filename === ''){
			return false;
		}
		if(!self::hasURL($filename)){
			$filename = realpath($filename);
			/* if(strpos($filename, $_SERVER['DOCUMENT_ROOT']) === FALSE){
			  t_e('warning', 'Acess outside document_root forbidden!', $filename);
			  return;
			  } */

			if(!is_readable($filename)){
				return false;
			}
		}

		$prefix = $iscompressed ? self::getComPrefix('gzip') : 'f';
		$open = $prefix . 'open';
		$read = $prefix . 'read';
		$close = $prefix . 'close';

		$buffer = '';
		if(($fp = @$open($filename, $flags))){
			do{
				$data = $read($fp, $rsize);
				if(strlen($data) == 0){
					break;
				}
				$buffer .= $data;
			} while(true);
			$close($fp);
			return $buffer;
		}
		return false;
	}

	static function loadLine($filename, $offset = 0, $rsize = 8192, $iscompressed = false){
		if($filename === '' || self::hasURL($filename) || !is_readable($filename)){
			return false;
		}
		$filename = realpath($filename);
		/* if(strpos($filename, $_SERVER['DOCUMENT_ROOT']) === FALSE){
		  t_e('warning', 'Acess outside document_root forbidden!', $filenam);
		  return;
		  } */

		$prefix = $iscompressed ? self::getComPrefix('gzip') : 'f';
		$open = $prefix . 'open';
		$seek = $prefix . 'seek';
		$read = $prefix . 'read';
		$close = $prefix . 'close';

		$buffer = '';
		if(($fp = $open($filename, 'rb'))){
			if($seek($fp, $offset, SEEK_SET) == 0){
				$buffer = $read($fp, $rsize);
				$close($fp);
				return $buffer;
			} else {
				$close($fp);
			}
		}
		return false;
	}

	static function loadLines($filename, $from, $to, $iscompressed = false){
		if($filename === '' || self::hasURL($filename) || !is_readable($filename)){
			return false;
		}
		$filename = realpath($filename);
		/* if(strpos($filename, $_SERVER['DOCUMENT_ROOT']) === FALSE){
		  t_e('warning', 'Acess outside document_root forbidden!', $filenam);
		  return;
		  } */

		$prefix = $iscompressed ? self::getComPrefix('gzip') : 'f';
		$open = $prefix . 'open';
		$gets = $prefix . 'gets';
		$close = $prefix . 'close';

		$buffer = '';
		$lines = array();
		$line = 0;
		if(($fp = $open($filename, 'rb'))){
			while((($buffer = $gets($fp, 4096)) !== false) && ++$line < $to){
				if($line >= $from){
					$lines[$line] = $buffer;
				}
			}
			$close($fp);
		}
		return $lines;
	}

	static function loadPart($filename, $offset = 0, $rsize = 8192, $iscompressed = false){
		if($filename === '' || self::hasURL($filename) || !is_readable($filename)){
			return false;
		}
		$filename = realpath($filename);
		/* if(strpos($filename, $_SERVER['DOCUMENT_ROOT']) === FALSE){
		  t_e('warning', 'Acess outside document_root forbidden!', $filename);
		  return;
		  } */

		$prefix = $iscompressed ? self::getComPrefix('gzip') : 'f';
		$open = $prefix . 'open';
		$seek = $prefix . 'seek';
		$read = $prefix . 'read';
		$close = $prefix . 'close';

		$buffer = '';
		if(($fp = @$open($filename, 'rb'))){
			if($seek($fp, $offset, SEEK_SET) == 0){
				$buffer = $read($fp, $rsize);
				$close($fp);
				return $buffer;
			} else {
				$close($fp);
			}
		}
		return false;
	}

	static function save($filename, $content, $flags = 'wb', $compression = ''){
		if(empty($filename) || self::hasURL($filename) || (file_exists($filename) && !is_writable($filename))){
			t_e('error writing file', $filename);
			return false;
		}
		$prefix = $compression ? self::getComPrefix($compression) : 'f';
		$open = $prefix . 'open';
		$write = $prefix . 'write';
		$close = $prefix . 'close';
		$len = strlen($content);

		if(($fp = $open($filename, $flags))){
			$written = $write($fp, $content, $len);
			@$close($fp);
			//if we write a php file, invalidate cache if used.
			if(substr($filename, -4) === '.php' && function_exists('opcache_invalidate')){
				opcache_invalidate($filename, true);
			}

			return $written == $len;
		}
		t_e('error writing file, unable to open file', $filename);
		return false;
	}

	static function saveTemp($content, $filename = '', $flags = 'wb'){
		if($filename === ''){
			$filename = self::getUniqueId();
		}
		$filename = TEMP_PATH . $filename;
		return (self::save($filename, $content, $flags) ? $filename : false);
	}

	static function delete($filename){
		if(!$filename){
			return false;
		}
		if(!self::hasURL($filename) && is_writable($filename)){
			return (is_dir($filename) ? rmdir($filename) : unlink($filename));
		}
		return false;
	}

	static function hasURL($filename){
		return ((strtolower(substr($filename, 0, 4)) === 'http') || (strtolower(substr($filename, 0, 3)) === 'ftp'));
	}

	static function getUniqueId($md5 = true){
		// md5 encrypted hash with the start value microtime(). The function
		// uniqid() prevents from simultanious access, within a microsecond.
		return ($md5 ? md5(uniqid(__FILE__, true)) : str_replace('.', '', uniqid('', true)));
	}

	/**
	 * Function: splitFile
	 *
	 * Description: This function splits a file.
	 */
	static function splitFile($filename, $path, $pattern = '', $split_size = 0, $marker = ''){

		if(empty($pattern)){
			$pattern = basename($filename) . '%s';
		}
		$buff = '';
		$filename_tmp = '';
		$fh = fopen($filename, 'rb');
		$num = -1;
		$open_new = true;
		$fsize = 0;

		$marker_size = strlen($marker);

		if($fh){
			while(!@feof($fh)){
				update_time_limit(60);
				$line = '';
				$findline = false;

				while($findline == false && !@feof($fh)){
					$line .= @fgets($fh, 4096);
					if(substr($line, -1) == "\n"){
						$findline = true;
					}
				}

				if($open_new){
					$num++;
					$filename_tmp = sprintf($path . $pattern, $num);
					$fh_temp = fopen($filename_tmp, 'wb');
					$open_new = false;
				}

				if($fh_temp){
					$buff.=$line;
					$write = false;

					//print substr($buff,(0-($marker_size+1)))."<br/>\n";

					if($marker_size){
						$write = ((substr($buff, (0 - ($marker_size + 1))) == $marker . "\n") || (substr($buff, (0 - ($marker_size + 2))) == $marker . "\r\n"));
					} else {
						$write = true;
					}

					if($write){
						$fsize+=strlen($buff);
						fwrite($fh_temp, $buff);
						if(($split_size && $fsize > $split_size) || ($marker_size)){
							$open_new = true;
							@fclose($fh_temp);
							$fsize = 0;
						}
						$buff = '';
					}
				} else {
					return -1;
				}
			}
		} else {
			return -1;
		}
		if($fh_temp){
			if($buff){
				fwrite($fh_temp, $buff);
			}
			@fclose($fh_temp);
		}
		@fclose($fh);

		return $num + 1;
	}

	static function mkpath($path){
		$path = str_replace('\\', '/', $path);
		return (self::hasURL($path) ?
				false :
				($path ? self::createLocalFolderByPath($path) : false));
	}

	public static function insertIntoCleanUp($path, $date = 300){
		$DB_WE = new DB_WE();
		$date = max($date,300); //make each entry last at least 300 seconds
		$DB_WE->query('INSERT INTO ' . CLEAN_UP_TABLE . ' SET ' . we_database_base::arraySetter(array(
				'Path' => $DB_WE->escape($path),
				'Date' => sql_function('(NOW()+ INTERVAL ' . intval($date) . ' SECOND)'),
			)) . ' ON DUPLICATE KEY UPDATE Date=(NOW()+ INTERVAL ' . intval($date) . ' SECOND)');
	}

	public static function deleteLocalFile($filename){
		return (file_exists($filename) ? unlink($filename) : false);
	}

	public static function checkAndMakeFolder($path, $recursive = false){
		/* if the directory exists, we have nothing to do and then we return true  */
		if((file_exists($path) && is_dir($path)) || (strtolower(rtrim($_SERVER['DOCUMENT_ROOT'], '/')) == strtolower(rtrim($path, '/')))){
			return true;
		}

// if instead of the directory a file exists, we delete the file and create the directory
		if(file_exists($path) && (!is_dir($path))){
			if(!self::deleteLocalFile($path)){
				t_e('Warning', "Could not delete File '" . $path . "'");
			}
		}

		$mod = octdec(intval(WE_NEW_FOLDER_MOD));
		$umask = umask(0);

// check for directories: create it if we could no write into it:
		if(!mkdir($path, $mod, $recursive)){
			t_e('warning', "Could not create local Folder at '" . __FUNCTION__ . "': '" . $path . "'");
			umask($umask);
			return false;
		}
		umask($umask);
		return true;
	}
	/**
	 * @deprecated since version 7.0
	 * @param type $RootDir
	 * @param type $path
	 * @return type
	 */
	public static function createLocalFolder($RootDir, $path = ''){
		return self::createLocalFolderByPath($RootDir . $path);
	}

	public static function createLocalFolderByPath($completeDirPath){
		$returnValue = true;

		if(self::checkAndMakeFolder($completeDirPath, true)){
			return $returnValue;
		}

		$cf = array($completeDirPath);

		$parent = str_replace('\\', '/', dirname($completeDirPath));

		while(!self::checkAndMakeFolder($parent)){
			$cf[] = $parent;
			$parent = str_replace('\\', '/', dirname($parent));
		}
		$mod = octdec(intval(WE_NEW_FOLDER_MOD));
		$umask = umask(0);

		foreach(array_reverse($cf) as $dir){
			if(!mkdir($dir, $mod, true)){
				t_e('Warning', "Could not create local Folder at File.php/createLocalFolderByPath(): '" . $dir . "'");
				$returnValue = false;
			}
		}
		umask($umask);

		return $returnValue;
	}

	static function hasGzip(){
		return function_exists('gzopen');
	}

	static function hasZip(){
		return function_exists('zip_open');
	}

	static function hasBzip(){
		return function_exists('bzopen');
	}

	static function hasCompression($comp){
		switch($comp){
			case 'gzip':
				return self::hasGzip();
			case 'zip':
				return self::hasZip();
			case 'bzip':
				return self::hasBzip();
			default:
				return false;
		}
	}

	static function getComPrefix($compression){
		switch($compression){
			case 'gzip':
				return 'gz';
			case 'zip':
				return 'zip_';
			case 'bzip':
				return 'bz';
			default://leave here since 0 is equivalent to first switch
				return 'f';
		}
	}

	static function getZExtension($compression){
		switch($compression){
			case 'gzip':
				return 'gz';
			case 'zip':
				return 'zip';
			case 'bzip':
				return 'bz';
			default:
				return '';
		}
	}

	static function getCompression($filename){
		$compressions = array('gzip', 'zip', 'bzip');
		foreach($compressions as $val){
			if(stripos(basename($filename), '.' . self::getZExtension($val)) !== false){
				return $val;
			}
		}
		return 'none';
	}

	static function compress($file, $compression = 'gzip', $destination = '', $remove = true, $writemode = 'wb'){
		if(!self::hasCompression($compression)){
			t_e('compression not available', $compression);
			return false;
		}

		$zfile = ($destination ? : $file) . '.' . self::getZExtension($compression);

		if(self::isCompressed($file)){
			if($remove){
				rename($file, $zfile);
			} else {
				copy($file, $zfile);
			}
			return $zfile;
		}
		$prefix = self::getComPrefix($compression);
		$open = $prefix . 'open';
		$write = $prefix . 'write';
		$close = $prefix . 'close';

		$fp = @fopen($file, 'rb');
		if($fp){
			$gzfp = $open($zfile, $writemode);
			if($gzfp){
				do{
					$data = fread($fp, 8192);
					$_data_size = strlen($data);
					if($_data_size == 0){
						break;
					}
					$_written = $write($gzfp, $data, $_data_size);
					if($_data_size != $_written){
						return false;
					}
				} while(true);
				$close($gzfp);
			} else {
				fclose($fp);
				return false;
			}
			fclose($fp);
		} else {
			return false;
		}
		if($remove){
			self::delete($file);
		}
		return $zfile;
	}

	static function decompress($gzfile, $remove = true){
		$gzfp = @gzopen($gzfile, 'rb');
		if($gzfp){
			$file = str_replace('.gz', '', $gzfile);
			if($file == $gzfile){
				$file = $gzfile . 'xml';
			}
			$fp = @fopen($file, 'wb');
			if($fp){
				do{
					$data = gzread($gzfp, 8192);
					if(strlen($data) == 0){
						break;
					}
					fwrite($fp, $data);
				} while(true);
				fclose($fp);
			} else {
				gzclose($gzfp);
				return false;
			}
			gzclose($gzfp);
		} else {
			return false;
		}
		if($remove){
			self::delete($gzfile);
		}
		return $file;
	}

	static function isCompressed($file, $offset = 0){
		if(($fh = @fopen($file, 'rb'))){
			if(fseek($fh, $offset, SEEK_SET) == 0){
				// according to rfc1952 the first two bytes identify the format
				$_id1 = fgets($fh, 2);
				$_id2 = fgets($fh, 2);
				fclose($fh);
				return ((ord($_id1) == 31) && (ord($_id2) == 139));
			}
			fclose($fh);
		}
		return false;
	}

	/**
	 * @destination string where the link should point to (fullqualified)
	 * @link string	fullqualified linkname
	 */
	public static function makeSymbolicLink($destination, $link){
		//basename+dirname
		$destinationPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', rtrim($destination, '/'));
		$linktarget = realpath(is_link($link) ? dirname($link) . '/' . readlink($link) : $destination);

		if(($linktarget == false || $linktarget != realpath($destination))){
			@unlink($link);
		}
		if(!is_link($link)){
			$cnt = substr_count(str_replace($_SERVER['DOCUMENT_ROOT'], '', $link), '/') - 1;
			$destination = str_repeat('../', $cnt) . basename($destinationPath);
			if(!symlink($destination, $link)){
				t_e('error in making symlink from ' . $link . ' to ' . $destination);
			}
		}
		return false;
	}

	/**
	 * creates a hard link to the target - if not possible, make a copy
	 * @param type $destination
	 * @param type $link
	 */
	public static function makeHardLink($destination, $link){
		if(is_file($link)){
			unlink($link);
		}
		if(!is_file($destination)){
			t_e('destination not naming a file', $destination, $link);
			return false;
		}
		if(@link($destination, $link)){
			return true;
		}
		return self::copyFile($destination, $link);
	}

	static function lock($id){
		$path = TEMP_PATH . $id . '.lck';
		$fp = fopen($path, 'c');

		//we can't cleanup file, since another instance might already access this lockfile
		if(flock($fp, LOCK_EX)){
			return $fp;
		}
		return false;
	}

	static function unlock($fp){
		if($fp){
			flock($fp, LOCK_UN);
			fclose($fp);
		}
	}

	static function getHumanFileSize($filesize, $type = self::SZ_HUMAN){
		switch($type){
			case self::SZ_BYTE:
				return $filesize . ' Byte';
			case self::SZ_KB:
				return round($filesize / 1024, 1) . ' kB';
			case self::SZ_MB:
				return round($filesize / (1024 * 1024), 1) . ' MB';
			default:
			case self::SZ_HUMAN:
				if($filesize >= (1024 * 1024)){
					return round($filesize / (1024 * 1024), 1) . ' MB';
				}
				if($filesize >= 1024){
					return round($filesize / 1024, 1) . ' kB';
				}
				return $filesize . ' Byte';
		}
	}

	public static function isWeFile($id, $table = FILE_TABLE, we_database_base $db = NULL){
		$id = intval($id);
		if($id == 0){
			return true;
		}
		return (f('SELECT 1 FROM ' . $table . ' WHERE ID=' . $id, '', ($db ? : new DB_WE())) === '1');
	}

	public static function cleanTempFiles($cleanSessFiles = false){
		$db = $GLOBALS['DB_WE'];
		$files = $db->getAllq('SELECT Path FROM ' . CLEAN_UP_TABLE . ' WHERE Date<=NOW()', true);
		foreach($files as $file){
			if(file_exists($file)){
				self::deleteLocalFile($file);
			}
			$db->query('DELETE FROM ' . CLEAN_UP_TABLE . ' WHERE Path="' . $file . '"');
		}
		if($cleanSessFiles){
			$seesID = session_id();
			$files = $db->getAllq('SELECT Path FROM ' . CLEAN_UP_TABLE . ' WHERE Path LIKE "%' . $GLOBALS['DB_WE']->escape($seesID) . '%"', true);
			foreach($files as $file){
				if(file_exists($file)){
					self::deleteLocalFile($file);
				}
			}
			$db->query('DELETE FROM ' . CLEAN_UP_TABLE . ' WHERE Path LIKE "%' . $GLOBALS['DB_WE']->escape($seesID) . '%"');
		}
		$d = dir(TEMP_PATH);
		while(false !== ($entry = $d->read())){
			switch($entry){
				case '.':
				case '..':
				case '.htaccess':
				case 'fragments':
					break;
				default:
					$foo = TEMP_PATH . $entry;
					if(filemtime($foo) <= (time() - 300)){
						if(is_dir($foo)){
							self::deleteLocalFolder($foo, 1);
						} elseif(file_exists($foo)){
							self::deleteLocalFile($foo);
						}
					}
			}
		}
		$d->close();

// when a fragment task was stopped by the user, the tmp file will not be deleted! So we have to clean up
		$d = dir(rtrim(WE_FRAGMENT_PATH, '/'));
		while(false !== ($entry = $d->read())){
			switch($entry){
				case '.':
				case '..':
				case '.htaccess':
					break;
				default:
					$foo = WE_FRAGMENT_PATH . $entry;
					if(filemtime($foo) <= (time() - 3600 * 24)){
						if(is_dir($foo)){
							self::deleteLocalFolder($foo, true);
						} elseif(file_exists($foo)){
							self::deleteLocalFile($foo);
						}
					}
			}
		}
		$d->close();
	}

	public static function we_filenameNotValid($filename, $isIso = false){
		return (substr($filename, 0, 2) === '..') || preg_match('![\'<>?":|\\/*' . ($isIso ? '\x00-\x20\x7F-\xFF' : '') . ']!', $filename);
	}

	public static function clearPath($path){
		return preg_replace('#/+#', '/', str_replace('\\', '/', $path));
	}

	public static function replaceInFile($string1, $string2, $file){
		self::save($file, preg_replace('/' . preg_quote($string1, '/') . '/i', $string2, self::load($file, 'r')), 'w');
	}

	public static function deleteLocalFolder($filename, $delAll = false, $withFolder = true){
		if(!file_exists($filename)){
			return false;
		}
		if($delAll){
			$foo = (substr($filename, -1) === '/') ? $filename : ($filename . '/');
			$d = dir($filename);
			while(false !== ($entry = $d->read())){
				switch($entry){
					case '.':
					case '..':
						break;
					case '.htaccess':
						if(!$withFolder){
							break;
						}
					default:
						$path = $foo . $entry;
						if(is_dir($path)){
							self::deleteLocalFolder($path, $delAll);
						} else {
							self::deleteLocalFile($path);
						}
				}
			}
			$d->close();
		}
		return $withFolder ? rmdir($filename) : true;
	}

	/**
	 * copy a file
	 * due to windows limitations, the file has to be copied and the old file deleted afterwards.
	 * if $new exists already, windows will not rename the file $old
	 */
	public static function copyFile($old, $new){
		return (@copy($old, $new));
	}

	/**
	 * move/rename a file
	 * due to windows limitations, the file has to be copied and the old file deleted afterwards.
	 * if $new exists already, windows will not rename the file $old
	 */
	public static function moveFile($old, $new){
		if(!@rename($old, $new)){
			if(!copy($old, $new)){
				return false;
			}
			unlink($old);
		}
		return true;
	}

	/**
	 * returns array of directory IDs of all directories which are located inside $folderID (recursive)
	 *
	 * @return array
	 * @param int $folderID
	 */
	static function getFoldersInFolder($folderID, $table = FILE_TABLE, we_database_base $db = null){
		$outArray = array(
			$folderID
		);
		$db = ($db ? : new DB_WE());
		$db->query('SELECT ID FROM ' . $table . ' WHERE ParentID=' . intval($folderID) . ' AND IsFolder=1');
		$new = array();
		while($db->next_record()){
			$new[] = $db->f('ID');
		}
		foreach($new as $cur){
			$tmpArray = self::getFoldersInFolder($cur, $table, $db);
			$outArray = array_merge($outArray, $tmpArray);
		}
		return $outArray;
	}

}
