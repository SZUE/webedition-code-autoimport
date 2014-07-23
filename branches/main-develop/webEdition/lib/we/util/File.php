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
 * @package none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * static class for various common filesystem operations
 * this is a merge of the old weFile class and the old we_live_tools.inc.php Script of webEdition 5.1.x and older
 *
 * @todo check if needed and if, then complete it and DON'T use old stuff like DB and other
 * */
abstract class we_util_File extends we_base_file{

	public static function save($filename, $content, $flags = "wb", $create_path = false){
		if(($create_path && !self::mkpath(dirname($filename))) || (!is_writable(dirname($filename)))){
			return false;
		}
		return parent::save($filename, $content, $flags);
	}

	public static function saveFile($file_name, $sourceCode = ''){
		if(!self::createLocalFolderByPath(str_replace('\\', '/', dirname($file_name)))){
			return false;
		}
		return parent::save($file_name, $sourceCode) !== false;
	}

	/**
	 * checks permission to write in path $path and tries a chmod(0755)
	 */
	public static function checkWritePermissions($path, $mod = 0755){
		if(!is_file($path) && !is_dir($path)){
			t_e('warning', 'target ' . $path . ' does not exist');
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
		if(self::removeTrailingSlash($dir) == self::removeTrailingSlash($target)){
			t_e('notice', "source and destination are the same.");
			return true;
		}
		if(!rename($dir, self::addTrailingSlash($target))){
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

	public static function compressDirectory($directoy, $destinationfile){
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
		$tar_object = new Archive_Tar($destinationfile, true);
		if(method_exists($tar_object, 'setErrorHandling')){
			//only if orignal pear was used
			$tar_object->setErrorHandling(PEAR_ERROR_TRIGGER, E_USER_WARNING);
		}
		$tar_object->createModify($DirFileObjectsArray, '', $directoy);
		return true;
	}

	public static function decompressDirectory($gzfile, $destination){
		if(!is_file($gzfile)){
			return false;
		}
		if(class_exists('Archive_Tar', true)){
			$tar_object = new Archive_Tar($gzfile, true);
			$tar_object->setErrorHandling(PEAR_ERROR_TRIGGER, E_USER_WARNING);
			$tar_object->extractModify($destination, '');
		} else {
//FIXME: remove include
			include(WE_LIB_PATH . 'additional/archive/altArchive_Tar.class.php');
			$tar_object = new altArchive_Tar($gzfile, true);
			$tar_object->extractModify($destination, '');
		}
		return true;
	}

}
