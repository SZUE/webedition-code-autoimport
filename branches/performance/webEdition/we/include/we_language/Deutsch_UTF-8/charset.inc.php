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
 * @package    webEdition_language
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/* 	To complete the localisation kit, it is necceessary to have the possibility
 * 	to change the charset as well, and use different stylesheets as well
 * 	In this file all these language specific characteristica are set.
 */

$l_charset = array(
//	This is for the charset of webEdition itself.
		'charset' => 'UTF-8',
		'error' => array(
				'no_charset_tag' => "Kein we:charset-Tag in der Vorlage",
				'no_charset_available' => "--Ohne--",
		),
		'titles' => array(
//	These values are for templates used in webEdition
//	we/include/charset.inc.php
// :ATTENTION: this code is used independent from charset, therefore we use htmlentities

				'west_european' => "Westeuropäisch", // ISO-8859-1
				'central_european' => "Mitteleuropäisch", // ISO-8859-2
				'south_european' => "Südeuropäisch", // ISO-8859-3
				'north_european' => "Nordeuropäisch", // ISO-8859-4
				'cyrillic' => "Kyrillisch", // ISO-8859-5
				'arabic' => "Arabisch", // ISO-8859-6
				'greek' => "Griechisch", // ISO-8859-7
				'hebrew' => "Hebräisch", // ISO-8859-8
				'turkish' => "Türkisch", // ISO-8859-9
				'nordic' => "Nordisch", // ISO-8859-10
				'thai' => "Thailändisch", // ISO-8859-11
				'baltic' => "Baltisch", // ISO-8859-13
				'keltic' => "Keltisch", // ISO-8859-14
				'extended_european' => "Europäisch (erweitert)", // ISO-8859-15

				'unicode' => "Unicode", // UTF-8
				'windows_1251' => "Windows-1251", // Windows-1251
				'windows_1252' => "Windows-1252", // Windows-1251
		),
);
