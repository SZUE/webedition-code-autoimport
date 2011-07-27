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
/**
 * Language file: charset.inc.php
 * Provides language strings.
 * Language: English
 */
/* 	To complete the localisation kit, it is necceessary to have the possibility
 * 	to change the charset as well, and use different stylesheets as well
 * 	In this file all these language specific characteristica are set.
 */

$l_charset = array(
//	This is for the charset of webEdition itself.
		'charset' => 'UTF-8',
		'error' => array(
				'no_charset_tag' => "Ei we:charset-tagia sivupohjassa",
				'no_charset_available' => "--ei valittu--",
		),
		'titles' => array(
//	These values are for templates used in webEdition
//	we/include/charset.inc.php

				'west_european' => "Western European", // ISO-8859-1
				'central_european' => "Central European", // ISO-8859-2
				'south_european' => "Southern European", // ISO-8859-3
				'north_european' => "Northern European", // ISO-8859-4
				'cyrillic' => "Cyrillic", // ISO-8859-5
				'arabic' => "Arabia", // ISO-8859-6
				'greek' => "Greek", // ISO-8859-7
				'hebrew' => "Hebrew", // ISO-8859-8
				'turkish' => "Turkish", // ISO-8859-9
				'nordic' => "Nordic", // ISO-8859-10
				'thai' => "Thai", // ISO-8859-11
				'baltic' => "Baltic", // ISO-8859-13
				'keltic' => "Keltic", // ISO-8859-14
				'extended_european' => "European (extended)", // ISO-8859-15

				'unicode' => "Unicode", // UTF-8
				'windows_1251' => "Windows-1251", // Windows-1251
				'windows_1252' => "Windows-1252", // Windows-1251
		),
);
