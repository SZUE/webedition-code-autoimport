<?php

/**
 * webEdition CMS
 *
 * $Rev: 8106 $
 * $Author: mokraemer $
 * $Date: 2014-08-23 23:04:44 +0200 (Sa, 23. Aug 2014) $
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
/* the parent class of storagable webEdition classes */


interface we_modules_viewIF{//FIXME is this really a base class, or is it an interface???
	//----------- Utility functions ------------------

	function htmlHidden($name, $value = '');

	function processCommands();

	function processVariables();
}
