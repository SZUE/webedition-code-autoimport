<?php

/**
 * webEdition CMS
 *
 * $Rev: 8143 $
 * $Author: mokraemer $
 * $Date: 2014-08-27 19:26:54 +0200 (Mi, 27. Aug 2014) $
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
class rpcTriggerJSErrorCmd extends rpcCmd{

	function execute(){
		t_e('JavaScript error found', $_REQUEST['we_cmd']);
	}

}
