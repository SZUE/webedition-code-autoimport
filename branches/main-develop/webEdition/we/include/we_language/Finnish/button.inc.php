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
 *
 * Provides language strings.
 *
 * Language: Deutsch
 */

$l_button=array();
$dir=dirname(__FILE__).'/buttons/';
include($dir."global.inc.php");
$l_button=array_merge($l_button,$l_global);
unset($l_global);
if (is_dir($dir."modules")) {

	// Include language files of buttons used in modules
	$d = dir($dir."modules");
	while (false !== ($entry = $d->read())) {
		$var=substr($entry,0,-8);
		if ($entry[0] != "." && substr($entry,-8 ) == ".inc..php") {
			include($dir."modules/".$entry);
			$l_button=array_merge($l_button,${"l_$var"});
			unset(${"l_$var"});
		}
	}
	$d->close();
}
unset($dir);
