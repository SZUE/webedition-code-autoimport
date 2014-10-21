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
abstract class we_backup_fileReader extends we_backup_XMLFileReader{

	static function preParse(&$content){
		$match = array();

		if(preg_match('|<we:table(item)?([^>]*)|i', $content, $match)){

			$attributes = explode('=', $match[2]);
			$attributes[0] = trim($attributes[0]);

			if($attributes[0] === 'name' || $attributes[0] === 'table'){
				$attributes[1] = trim(str_replace(array('"', '\''), '', $attributes[1]));

				// if the table should't be imported
				if(we_backup_util::getRealTableName($attributes[1]) === false){
					return true;
				}
			}
		}

		if((preg_match('|<we:binary><ID>([^<]*)</ID>(.*)<Path>([^<]*)</Path>|i', $content, $match) && !we_backup_util::canImportBinary($match[1], $match[3])) ||
			(preg_match('|<we:version><ID>([^<]*)</ID>(.*)<Path>([^<]*)</Path>|i', $content, $match) && !we_backup_util::canImportVersion($match[1], $match[3]))){
			return true;
		}

		return false;
	}

}
