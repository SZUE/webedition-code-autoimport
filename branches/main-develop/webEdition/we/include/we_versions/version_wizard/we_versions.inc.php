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
we_html_tools::protect();

require_once (WE_INCLUDES_PATH . 'we_versions/version_wizard/we_versions_wizard.inc.php');

switch(weRequest('string', "fr", '')){
	case "body" :
		echo we_versions_wizard::getBody();
		break;
	case "busy" :
		echo we_versions_wizard::getBusy();
		break;
	case "cmd" :
		echo we_versions_wizard::getCmd();
		break;
	default :
		echo we_versions_wizard::getFrameset();
}
