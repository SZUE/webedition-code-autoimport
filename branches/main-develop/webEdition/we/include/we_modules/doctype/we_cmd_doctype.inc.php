<?php
/**
 * webEdition CMS
 *
 * $Rev: 13684 $
 * $Author: mokraemer $
 * $Date: 2017-04-04 23:48:16 +0200 (Di, 04. Apr 2017) $
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
switch($cmd){
	case 'doctype_edit':
		$_REQUEST['mod'] = 'doctype';
		$_REQUEST['pnt'] = 'show_frameset';
		return '../../we_showMod.php';
}
