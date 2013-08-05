<?php

/**
 * webEdition SDK
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_util
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * static class for various common filesystem operations
 * this is a merge of the old weFile class and the old we_live_tools.inc.php Script of webEdition 5.1.x and older
 *
 * @todo check if needed and if, then complete it and DON'T use old stuff like DB and other
 * */
abstract class we_util_File{

	public static function load($filename, $flags = "rb", $rsize = 8192){
		return weFile::load($filename, $flags, $rsize);
	}

	public static function loadLine($filename, $offset = 0, $rsize = 8192, $iscompressed = 0){
		return weFile::loadLine($filename, $offset, $rsize, $iscompressed);
	}

	public static function loadPart($filename, $offset = 0, $rsize = 8192, $iscompressed = 0){
		return weFile::loadPart($filename, $offset, $rsize, $iscompressed);
	}

	public static function save($filename, $content, $flags = "wb", $create_path = false){
		if(($create_path && !self::mkpath(dirname($filename))) || (!is_writable(dirname($filename)))){
			return false;
		}
		return weFile::save($filename, $content, $flags);
	}

	public static function saveTemp($content, $filename = "", $flags = "wb"){
		return weFile::saveTemp($content, $filename, $flags);
	}

	public static function delete($filename){
		return weFile::delete($filename);
	}

	public static function hasURL($filename){
		return ((strtolower(substr($filename, 0, 4)) == "http") || (strtolower(substr($filename, 0, 4)) == "ftp"));
	}

	public static function getUniqueId($md5 = true){
		return weFile::getUniqueId($md5);
	}

	/**
	 * split a file into various parts of a predefined size
	 */
	public static function splitFile($filename, $path, $pattern = '', $split_size = 0, $marker = ''){
		return weFile::splitFile($filename, $path, $pattern, $split_size, $marker);
	}

	public static function mkpath($path){
		return weFile::mkpath($path);
	}

	public static function hasGzip(){
		return weFile::hasGzip();
	}

	public static function hasZip(){
		return weFile::hasZip();
	}

	public static function hasBzip(){
		return weFile::hasBzip();
	}

	public static function hasCompression($comp){
		return weFile::hasCompression($comp);
	}

	public static function getComPrefix($compression){
		return weFile::getComPrefix($compression);
	}

	public static function getZExtension($compression){
		return weFile::getZExtension($compression);
	}

	public static function getCompression($filename){
		return weFile::getCompression($filename);
	}

	public static function compress($file, $compression = "gzip", $destination = "", $remove = true, $writemode = "wb"){

		if(!self::hasCompression($compression)){
			return false;
		}
		if($destination == ""){
			$destination = $file;
		}
		$prefix = weFile::getComPrefix($compression);
		$open = $prefix . "open";
		$write = $prefix . "write";
		$close = $prefix . "close";

		$fp = @fopen($file, "rb");
		if($fp){
			$zfile = $destination . ".gz";
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
			@unlink($file);
		}
		return $zfile;
	}

	public static function decompress($gzfile, $remove = true){
		$gzfp = @gzopen($gzfile, 'rb');
		if($gzfp){
			$file = str_replace('.gz', '', $gzfile);
			if($file == $gzfile){
				$file = $gzfile . 'xml';
			}
			if(($fp = @fopen($file, 'wb'))){
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
			@unlink($gzfile);
		}
		return $file;
	}

	public static function isCompressed($file, $offset = 0){
		return weFile::isCompressed($file, $offset);
	}

	public static function saveFile($file_name, $sourceCode = ''){
		if(!self::createLocalFolderByPath(str_replace('\\', '/', dirname($file_name)))){
			return false;
		}
		$fh = @fopen($file_name, 'wb');
		if(!$fh){
			return false;
		}
		$ret = ($sourceCode ? fwrite($fh, $sourceCode) : true);
		fclose($fh);
		return $ret;
	}

	public static function createLocalFolder($RootDir, $path = ''){
		return weFile::createLocalFolderByPath($RootDir . $path);
	}

	public static function createLocalFolderByPath($completeDirPath){
		return weFile::createLocalFolderByPath($completeDirPath);
	}

	public static function insertIntoCleanUp($path, $date){
		return weFile::insertIntoCleanUp($path, $date);
	}

	public static function checkAndMakeFolder($path, $recursive = false){
		return weFile::checkAndMakeFolder($path, $recursive);
	}

	/**
	 * checks permission to write in path $path and tries a chmod(0755)
	 */
	public static function checkWritePermissions($path, $mod = 0755){
		if(!is_file($path) && !is_dir($path)){
			t_e('warning', "target " . $path . " does not exist");
			return false;
		}
		if(is_writable($path)){
			return true;
		}
		if(!@chmod($path, $mod)){
			return false;
		}
		return (is_writable($path));
	}

	public static function insertIntoErrorLog($text){
		t_e('warning', $text);
	}

	/**
	 * @deprecated since - 05.06.2008
	 * please use moveFile() instead
	 */
	public static function renameFile($old, $new){
		return self::moveFile($old, $new);
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
	 * recursively moves a directory
	 * it will only move $dir if there is no directory in $target with the same name
	 */
	public static function moveDir($dir, $target){
		$dir = self::removeTrailingSlash($dir);
		$target = self::addTrailingSlash($target);
		$dirname = substr(strrchr($dir, "/"), 1);
		if(self::removeTrailingSlash($dir) == self::removeTrailingSlash($target)){
			t_e('notice', "source and destination are the same.");
			return true;
		}
		if(!@rename($dir, self::addTrailingSlash($target))){
			t_e('warning', "could not move directory " . $dir . " to " . self::addTrailingSlash($target) . ".");
			return false;
		}
		return true;
	}

	public static function deleteLocalFolder($filename, $delAll = false){
		if(!file_exists($filename)){
			return false;
		}
		if($delAll){
			$foo = (substr($filename, -1) == "/") ? $filename : ($filename . "/");
			$d = dir($filename);
			while(false !== ($entry = $d->read())){
				if($entry != ".." && $entry != "."){
					$path = $foo . $entry;
					if(is_dir($path)){
						self::deleteLocalFolder($path, 1);
					} else {
						self::deleteLocalFile($path);
					}
				}
			}
			$d->close();
		}
		return @rmdir($filename);
	}

	public static function deleteLocalFile($filename){
		return (file_exists($filename) ? unlink($filename) : false);
	}

	/**
	 * recursively deletes a directory with all its contents
	 *
	 * @param string $path path to the directory that has to be deleted
	 * @param bool $nofiles does not delete any files but only empty subdirectories
	 */
	public static function rmdirr($path, $nofiles = false){
//t_e("trying to recursively delete " . $path);
		if($nofiles && !is_dir($path)){
			t_e('warning', "ERROR: $path is no directory");
			return false;
		}
		if(!file_exists($path)){
			t_e('warning', "ERROR: could not find $path");
			return false;
		}
// check if it is a file or a symbolic link;
		if(is_file($path) || is_link($path)){
			if($nofiles === false){
				if(@unlink($path)){
					return true;
				} else {
					t_e('warning', " unable to delete file " . $path);
				}
			} else {
//t_e(" -- skipping file " . $path);
			}
		}
// loop through the folder
		$dir = dir($path);
		while(false !== $entry = $dir->read()){
			if($entry == '.' || $entry == '..'){
				continue;
			}
// Recurse
//t_e(" -- trying to delete folder " . $path);
			self::rmdirr($path . DIRECTORY_SEPARATOR . $entry);
		}
		$dir->close();
		return @rmdir($path);
	}

	public static function addTrailingSlash($value){
		return self::removeTrailingSlash($value) . '/';
	}

	public static function removeTrailingSlash($value){
		return rtrim($value, '/');
	}

	public static function compressDirectoy($directoy, $destinationfile){
		if(!is_dir($directoy)){
			return false;
		}
		$DirFileObjectsArray = array();
		$DirFileObjects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoy));
		foreach(array_keys($DirFileObjects) as $name){
			if(substr($name, -2) != '/.' && substr($name, -3) != '/..'){
				$DirFileObjectsArray[] = $name;
			}
		}
		sort($DirFileObjectsArray);
		if(class_exists('Archive_Tar', true)){
			$tar_object = new Archive_Tar($destinationfile, true);
			$tar_object->setErrorHandling(PEAR_ERROR_TRIGGER, E_USER_WARNING);
			$tar_object->createModify($DirFileObjectsArray, '', $directoy);
		} else {
//FIXME: remove include
			include($GLOBALS['__WE_LIB_PATH__'] . DIRECTORY_SEPARATOR . 'additional' . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR . 'altArchive_Tar.class.php');
			$tar_object = new altArchive_Tar($gzfile, true);
			$tar_object->createModify($DirFileObjectsArray, '', $directoy);
		}
		return true;
	}

	public static function decompressDirectoy($gzfile, $destination){
		if(!is_file($gzfile)){
			return false;
		}
		if(class_exists('Archive_Tar', true)){
			$tar_object = new Archive_Tar($gzfile, true);
			$tar_object->setErrorHandling(PEAR_ERROR_TRIGGER, E_USER_WARNING);
			$tar_object->extractModify($destination, '');
		} else {
//FIXME: remove include
			include($GLOBALS['__WE_LIB_PATH__'] . DIRECTORY_SEPARATOR . 'additional' . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR . 'altArchive_Tar.class.php');
			$tar_object = new altArchive_Tar($gzfile, true);
			$tar_object->extractModify($destination, '');
		}
		return true;
	}

}