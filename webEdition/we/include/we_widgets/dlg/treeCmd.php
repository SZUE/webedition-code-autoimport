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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();

switch(we_base_request::_(we_base_request::STRING, "cmd")){
	case "load" :
		if(($pid = we_base_request::_(we_base_request::INT, "pid")) !== false){
			echo
			we_html_element::jsElement(
				"self.location='" . WE_EXPORT_MODULE_DIR . "exportLoadTree.php?we_cmd[1]=" . we_base_request::_(we_base_request::TABLE, "tab") . "&we_cmd[2]=" . $pid . "&we_cmd[3]=" . (we_base_request::_(we_base_request::STRING, 'openFolders') ? : "") . "&we_cmd[4]=top'");
		}
		break;
}
