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
				'no_charset_tag' => "Aucun we:charset-Tag dans le modèle",
				'no_charset_available' => "--Sans--",
		),
		'titles' => array(
//	These values are for templates used in webEdition
//	we/include/charset.inc.php

				'west_european' => "Western European", // TRANSLATE		// ISO-8859-1
				'central_european' => "Central European", // TRANSLATE		// ISO-8859-2
				'south_european' => "Southern European", // TRANSLATE		// ISO-8859-3
				'north_european' => "Northern European", // TRANSLATE		// ISO-8859-4
				'cyrillic' => "Cyrillic", // TRANSLATE				// ISO-8859-5
				'arabic' => "Arabia", // TRANSLATE					// ISO-8859-6
				'greek' => "Greek", // TRANSLATE					// ISO-8859-7
				'hebrew' => "Hebrew", // TRANSLATE					// ISO-8859-8
				'turkish' => "Turkish", // TRANSLATE				// ISO-8859-9
				'nordic' => "Nordic", // TRANSLATE					// ISO-8859-10
				'thai' => "Thai", // TRANSLATE					// ISO-8859-11
				'baltic' => "Baltic", // TRANSLATE					// ISO-8859-13
				'keltic' => "Keltic", // TRANSLATE					// ISO-8859-14
				'extended_european' => "Européenne (élargi)", // ISO-8859-15

				'unicode' => "Unicode", // TRANSLATE				// UTF-8
				'windows_1251' => "Windows-1251", // TRANSLATE		// Windows-1251
				'windows_1252' => "Windows-1252", // TRANSLATE		// Windows-1251
		),
);
