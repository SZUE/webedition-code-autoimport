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
switch($cmd){
	case 'shop_edit_ifthere':
	case 'shop_edit':
		$GLOBALS['mod'] = 'shop';
		return 'we_modules/show_frameset.php';
	case 'shop_insert_variant':
	case 'shop_move_variant_up':
	case 'shop_move_variant_down':
	case 'shop_remove_variant':
	case 'shop_preview_variant':
		return 'we_editors/we_editor.inc.php';
}
