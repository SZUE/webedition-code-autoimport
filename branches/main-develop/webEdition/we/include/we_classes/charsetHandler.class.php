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
class charsetHandler{

	var $charsets = array();

	/**
	 * @return charsetHandler
	 * initialises with all available charsets
	 */
	function __construct(){
		//	First ISO-8859-charsets
		$_charsets["west_european"]["national"] = "West Europe";		//	Here is the name of the country in mother language
		$_charsets["west_european"]["charset"] = "ISO-8859-1";
		$_charsets["west_european"]["international"] = g_l('charset', "[titles][west_european]"); //	Name in selected language

		$_charsets["central_european"]["national"] = "Central Europe";
		$_charsets["central_european"]["charset"] = "ISO-8859-2";
		$_charsets["central_european"]["international"] = g_l('charset', "[titles][central_european]");

		$_charsets["south_european"]["national"] = "South Europe";
		$_charsets["south_european"]["charset"] = "ISO-8859-3";
		$_charsets["south_european"]["international"] = g_l('charset', "[titles][south_european]");

		$_charsets["north_european"]["national"] = "North Europe";
		$_charsets["north_european"]["charset"] = "ISO-8859-4";
		$_charsets["north_european"]["international"] = g_l('charset', "[titles][north_european]");

		$_charsets["cyrillic"]["national"] = ";&#1077;&#1086;&#1073;&#1077;&#1089;&#1087;&#1077;&#1095;";
		$_charsets["cyrillic"]["charset"] = "ISO-8859-5";
		$_charsets["cyrillic"]["international"] = g_l('charset', "[titles][cyrillic]");

		$_charsets["arabic"]["national"] = "&#1578;&#1587;&#1580;&#1617;&#1604; &#1575;&#1604;&#1570;&#1606;";
		$_charsets["arabic"]["charset"] = "ISO-8859-6";
		$_charsets["arabic"]["international"] = g_l('charset', "[titles][arabic]");

		$_charsets["greek"]["national"] = "Greek";
		$_charsets["greek"]["charset"] = "ISO-8859-7";
		$_charsets["greek"]["international"] = g_l('charset', "[titles][greek]");

		$_charsets["hebrew"]["national"] = "&#1488;&#1497;&#1512;&#1493;&#1508;&#1492;";
		$_charsets["hebrew"]["charset"] = "ISO-8859-8";
		$_charsets["hebrew"]["international"] = g_l('charset', "[titles][hebrew]");

		$_charsets["turkish"]["national"] = "Turkish";
		$_charsets["turkish"]["charset"] = "ISO-8859-9";
		$_charsets["turkish"]["international"] = g_l('charset', "[titles][turkish]");

		$_charsets["nordic"]["national"] = "Nordic";
		$_charsets["nordic"]["charset"] = "ISO-8859-10";
		$_charsets["nordic"]["international"] = g_l('charset', "[titles][nordic]");

		$_charsets["thai"]["national"] = "Thai";
		$_charsets["thai"]["charset"] = "ISO-8859-11";
		$_charsets["thai"]["international"] = g_l('charset', "[titles][thai]");

		$_charsets["baltic"]["national"] = "baltic";
		$_charsets["baltic"]["charset"] = "ISO-8859-13";
		$_charsets["baltic"]["international"] = g_l('charset', "[titles][baltic]");

		$_charsets["keltic"]["national"] = "keltic";
		$_charsets["keltic"]["charset"] = "ISO-8859-14";
		$_charsets["keltic"]["international"] = g_l('charset', "[titles][keltic]");

		$_charsets["extended_european"]["national"] = "ISO-8859-15";
		$_charsets["extended_european"]["charset"] = "ISO-8859-15";
		$_charsets["extended_european"]["international"] = g_l('charset', "[titles][extended_european]");

		$_charsets["unicode"]["national"] = "Unicode";
		$_charsets["unicode"]["charset"] = "UTF-8";
		$_charsets["unicode"]["international"] = g_l('charset', "[titles][unicode]");

		$_charsets["windows_1251"]["national"] = "Windows-1251";
		$_charsets["windows_1251"]["charset"] = "Windows-1251";
		$_charsets["windows_1251"]["international"] = g_l('charset', "[titles][windows_1251]");

		$_charsets["windows_1252"]["national"] = "Windows-1252";
		$_charsets["windows_1252"]["charset"] = "Windows-1252";
		$_charsets["windows_1252"]["international"] = g_l('charset', "[titles][windows_1252]");

		$this->charsets = $_charsets;
	}

	/**
	 * @return array
	 * @param $availableChars array
	 * @desc This function returns an array(key = charset / value = charset - name(international) (name(national)))
	 */
	function getCharsetsForTagWizzard(){

		$_charsets = $this->charsets;

		$retArr = array();
		$first = true;

		while(list($key, $val) = each($_charsets)) {

			$retArr[$val["charset"]] = $val["charset"] . " - " . $val["international"] . " (" . $val["national"] . ")";
		}
		reset($_charsets);
		return $retArr;
	}

	/**
	 * @return array
	 * @param string $charset
	 * @desc returns array (national, international, charset, when charset is known)
	 */
	function getCharsetArrByCharset($charset){

		$_charsets = $this->charsets;

		$_charsetArray = false;

		while(list($key, $val) = each($_charsets)) {

			if(strtolower($val["charset"]) == strtolower($charset)){
				return $_charsets[$key];
			}
		}
		return $_charsetArray;
	}

	/**
	 * @return array
	 * @param $availableChars array
	 * @desc This function returns an array for the property page of a webEdition document
	 */
	function getCharsetsByArray($availableChars){

		$_charsets = $this->charsets;

		$tmpCharArray = array();
		$retArray = array();

		for($i = 0; $i < sizeof($availableChars); $i++){

			if($charset = $this->getCharsetArrByCharset($availableChars[$i])){
				array_push($tmpCharArray, $charset);
			} else{
				array_push($tmpCharArray, array("charset" => $availableChars[$i]));
			}
		}
		reset($tmpCharArray);

		while(list($key, $val) = each($tmpCharArray)) {

			if(isset($val["international"])){
				$retArr[$val["charset"]] = $val["charset"] . " - " . $val["international"] . " (" . $val["national"] . ")";
			} else{
				$retArr[$val["charset"]] = $val["charset"];
			}
		}

		return $retArr;
	}


	//FIXME: use array obove; currently this seems to complecated
	static function getAvailCharsets(){
		return array(
			'UTF-8',
			'ISO-8859-1',
			'ISO-8859-2',
			'ISO-8859-3',
			'ISO-8859-4',
			'ISO-8859-5',
			'ISO-8859-6',
			'ISO-8859-7',
			'ISO-8859-8',
			'ISO-8859-9',
			'ISO-8859-10',
			'ISO-8859-11',
			'ISO-8859-12',
			'ISO-8859-13',
			'ISO-8859-14',
			'ISO-8859-15',
			'Windows-1251',
			'Windows-1252',
		);
	}

}
