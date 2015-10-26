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

switch(we_base_request::_(we_base_request::STRING, 'fr')){
	case "body":
		echo we_rebuild_wizard::getBody();
		break;
	case "busy":
		echo we_rebuild_wizard::getBusy();
		break;
	case "cmd":
		echo we_rebuild_wizard::getCmd();
		break;
	default:
		echo we_rebuild_wizard::getFrameset();
}