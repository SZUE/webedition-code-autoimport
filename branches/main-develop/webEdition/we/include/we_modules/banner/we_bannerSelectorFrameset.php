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

include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_global.inc.php");

we_html_tools::protect();
		$id = $_REQUEST['we_cmd'][1];

		$JSIDName = we_cmd_dec(2);
		$JSTextName = we_cmd_dec(3);
		$JSCommand = we_cmd_dec(4);

		include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_modules/banner/we_bannerSelect.php");
