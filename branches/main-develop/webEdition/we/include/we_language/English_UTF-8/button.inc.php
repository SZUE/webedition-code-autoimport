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
 * Language file: buttons.inc.php
 * Provides language strings.
 * Language: English
 */

$dir=dirname(__FILE__).'/buttons/';
include($dir."global.inc.php");
if (is_dir($dir."modules")) {

	// Include language files of buttons used in modules
	$d = dir($dir."modules");
	while (false !== ($entry = $d->read())) {
		if ($entry[0] != "." && substr($entry,(-1 * strlen(".php"))) == ".php") {
			include($dir."modules/".$entry);
		}
	}
	$d->close();
}
