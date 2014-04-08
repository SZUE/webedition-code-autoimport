<?php
/**
 * webEdition CMS
 *
 * $Rev: 6950 $
 * $Author: mokraemer $
 * $Date: 2013-11-14 22:33:01 +0100 (Do, 14 Nov 2013) $
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

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect(null, WEBEDITION_DIR . 'index.php');

if(!isset($_SESSION['weS']['we_mode']) || $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){ //	working in normal mode
	include_once(WE_INCLUDES_PATH . 'webEdition_normal.inc.php');
} else if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){ //	working in super-easy-edit-mode
	include_once(WE_INCLUDES_PATH . 'webEdition_seem.inc.php');
}

//do not load any WE-JS: we allready have it in the header of webEdition.php

?>
<html>

<body style="background-color:grey;margin: 0px;position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;" onbeforeunload="doUnload()">
	<?php
	flush();
	//	get the frameset for the actual mode.
	pWebEdition_Frameset();
	we_main_header::pJS();
	flush();
	?>
</body>
</html>