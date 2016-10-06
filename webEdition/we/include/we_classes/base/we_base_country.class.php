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
abstract class we_base_country{
	const TERRITORY = 'territory';
	const REGION = 'region';
	const LANGUAGE = 'language';
	const SCRIPT = 'script';
	const MONTH = 'months';
	const DAY = 'days';

	static $last = [];

	private static function loadLang($langcode){
		$file = WE_INCLUDES_PATH . 'country/' . $langcode . '.inc.php';
		if(!file_exists($file)){
				echo 'no file' . $file;
			return false;
		}

		self::$last = [$langcode => include($file)
			];
		return true;
	}

	public static function getTranslation($countrykey, $type, $langcode){
		list($langcode) = explode('_', strtolower($langcode), 2);
		if(!isset(self::$last[$langcode]) && !self::loadLang($langcode)){
			return '';
		}
		return empty(self::$last[$langcode][$type][$countrykey]) ? '' : self::$last[$langcode][$type][$countrykey];
	}

	public static function getTranslationList($type, $langcode){
		list($langcode) = explode('_', strtolower($langcode), 2);
		if(!isset(self::$last[$langcode]) && !self::loadLang($langcode)){
			return [];
		}
		return empty(self::$last[$langcode][$type]) ? '' : self::$last[$langcode][$type];
	}

	public static function dateformat($langcode, DateTime $date, $format){
		list($langcode) = explode('_', strtolower($langcode), 2);
		if(!isset(self::$last[$langcode]) && !self::loadLang($langcode)){
			return '';
		}

		$months = self::getTranslationList(self::MONTH, $langcode);
		$days = self::getTranslationList(self::DAY, $langcode);

		$dat = $date->format(strtr($format, [
			'D' => "\dD\d", //Mon bis Sun
			'l' => "\lD\l", //Sunday bis Saturday
			'F' => "\\fn\\f", //January bis December
			'M' => '\mn\m', //Jan bis Dec
			]));

		$wd = $date->format('D');
		$mon = $date->format('n');

		return strtr($dat, [
			'd' . $wd . 'd' => $days['abbreviated'][strtolower($wd)], //Mon bis Sun
			'l' . $wd . 'l' => $days['wide'][strtolower($wd)], //Sunday bis Saturday
			'f' . $mon . 'f' => $months['wide'][$mon], //January bis December
			'm' . $mon . 'm' => $months['abbreviated'][$mon], //Jan bis Dec
			]
		);
	}

}
