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
class we_cache_file implements we_cache_base{
	/* private static function loadMeta(){
	  return we_unserialize(we_base_file::load(WE_CACHE_PATH . 'we_cache_meta'));
	  }

	  private static function saveMeta(array $data){
	  return we_base_file::save(WE_CACHE_PATH . 'we_cache_meta', we_serialize($data, SERIALIZE_JSON));
	  } */

	public static function load($entry){
		/* static $t = 0;
		  $t = $t ?: time();
		  static $meta = false;
		  $meta = $meta ?: self::loadMeta();
		  if(!$meta || !isset($meta[$entry]) || $meta[$entry] < $t){
		  $meta = false;
		  return false;
		  } */
		return !file_exists(WE_CACHE_PATH . 'we_cache_' . $entry) ? false : we_unserialize(we_base_file::load(WE_CACHE_PATH . 'we_cache_' . $entry));
	}

	public static function save($entry, array $data, $expiry = 3600){
		$ser = we_serialize($data, SERIALIZE_PHP); //keep this php, since encoding etc. works better & faster in all modes
		we_base_file::save(WE_CACHE_PATH . 'we_cache_' . $entry, (strlen($ser) > 1024 ? gzcompress($ser, 6) : $ser));
		we_base_file::insertIntoCleanUp(WE_CACHE_DIR . 'we_cache_' . $entry, $expiry);
	}

	public static function clean($pattern = ''){
		$files = [];
		foreach(glob(WE_CACHE_PATH . 'we_cache_' . $pattern . '*') as $file){
			unlink($file);
		}
		if($files){
			$GLOBALS['DB_WE']->query('DELETE FROM ' . CLEAN_UP_TABLE . ' WHERE Path IN ("' . implode('","', $files) . '")');
		}
	}

}
