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

if(we_base_moduleInfo::isActive($mod) && file_exists(WE_MODULES_PATH . $mod . '/edit_' . $mod . '_frameset.php')){
	require_once(WE_MODULES_PATH . $mod . '/edit_' . $mod . '_frameset.php');
}

if(strpos($mod, '?')){
	//compatibility code for ?mod=xxx?pnt=yy
	list($mod, $other) = explode('?', $mod);
	$_REQUEST['mod'] = $mod;
	list($k, $v) = explode('=', $other);
	$_REQUEST[$k] = $v;
}

if(!we_base_moduleInfo::isActive($mod)){
	return;
}
$what = we_base_request::_(we_base_request::STRING, "pnt", "frameset");
$mode = we_base_request::_(we_base_request::INT, "art", 0);
$step = we_base_request::_(we_base_request::INT, 'step', 0);

switch($mod){
	case 'banner':
		$protect = we_base_moduleInfo::isActive('banner') && we_users_util::canEditModule('banner') ? null : array(false);
		we_html_tools::protect($protect);


		$weFrame = new we_banner_frames(WE_MODULES_DIR . 'show.php?mod=' . $mod);
		ob_start();
		$weFrame->process();
		$GLOBALS['extraJS'] = ob_get_clean();
		break;
	case 'shop':
		$protect = we_base_moduleInfo::isActive('shop') && we_users_util::canEditModule('shop') ? null : array(false);
		we_html_tools::protect($protect);

		$weFrame = new we_shop_frames(WE_MODULES_DIR . 'show.php?mod=shop');
		$weFrame->View->processCommands();
		break;
	case 'customer':
		$protect = we_base_moduleInfo::isActive('customer') && we_users_util::canEditModule('customer') ? null : array(false);
		we_html_tools::protect($protect);

		switch($what){
			case 'export':
			case 'eibody':
			case 'eifooter':
			case 'eiload':
			case 'import':
			case 'eiupload':
				$weFrame = new we_customer_EIWizard();
				break;
			default:
				$weFrame = new we_customer_frames();
				$weFrame->process();
		}
		break;
	case 'users':
		$protect = we_base_moduleInfo::isActive('users') && we_users_util::canEditModule('users') ? null : array(false);
		we_html_tools::protect($protect);

		$weFrame = new we_users_frames(WE_MODULES_DIR . 'show.php?mod=users');

		ob_start();
		$weFrame->process();
		$GLOBALS['extraJS'] = ob_get_clean();
		break;
	default:
		echo 'no module';
		return;
}

//FIXME: process will generate js output without doctype
echo $weFrame->getHTML($what, $mode, $step);
