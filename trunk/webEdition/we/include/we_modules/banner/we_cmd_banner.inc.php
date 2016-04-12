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
	case 'banner_edit_ifthere':
	case 'banner_edit':
		$_REQUEST['mod'] = 'banner';
		$_REQUEST['pnt'] = 'show_frameset';
		return '../../we_showMod.php';
	case 'we_banner_dirSelector':
	case 'we_banner_selector':
		return 'selectors.inc.php';
	case 'banner_default':
		return 'we_modules/banner/we_defaultbanner.inc.php';
	case 'banner_code':
		return 'we_modules/banner/we_bannercode.inc.php';
}