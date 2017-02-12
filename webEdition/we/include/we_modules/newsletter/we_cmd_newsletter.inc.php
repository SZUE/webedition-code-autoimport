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
	case 'newsletter_edit':
	case 'newsletter_edit_ifthere':
		$_REQUEST['mod'] = 'newsletter';
		$_REQUEST['pnt'] = 'show_frameset';
		return '../../we_showMod.php';
	case 'we_newsletter_dirSelector':
		we_selector_file::getSelectorFromRequest();
		return true;
}
