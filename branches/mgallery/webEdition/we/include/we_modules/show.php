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

$mod = we_base_request::_(we_base_request::STRING, 'mod');

if(we_base_moduleInfo::isActive($mod)){
	require_once(WE_MODULES_PATH . $mod . '/edit_' . $mod . '_frameset.php');
}
return;

if(!we_base_moduleInfo::isActive($mod)){
	return;
}

switch($mod){
	case 'banner':
		$protect = we_base_moduleInfo::isActive('banner') && we_users_util::canEditModule('banner') ? null : array(false);
		we_html_tools::protect($protect);

		$what = we_base_request::_(we_base_request::STRING, "pnt", "frameset");
		$mode = we_base_request::_(we_base_request::INT, "art", 0);

		$weFrame = new we_banner_frames(WE_MODULES_DIR . 'show.php?mod=' . $mod);
		break;
	default:
		echo 'no module';
		return;
}

//FIXME: process will generate js output without doctype
ob_start();
$weFrame->process();
$GLOBALS['extraJS'] = ob_get_clean();
echo $weFrame->getHTML($what, $mode);
