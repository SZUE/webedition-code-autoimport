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
 * @package    webEdition_javamenu
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
$we_menu_banner["000100"]["text"] = g_l('modules_banner', '[banner]');
$we_menu_banner["000100"]["parent"] = "000000";
$we_menu_banner["000100"]["enabled"] = "1";

$we_menu_banner["000200"]["text"] = g_l('modules_banner', '[new]');
$we_menu_banner["000200"]["parent"] = "000100";
$we_menu_banner["000200"]["enabled"] = "1";

$we_menu_banner["000250"]["text"] = g_l('modules_banner', '[banner]');
$we_menu_banner["000250"]["cmd"] = "new_banner";
$we_menu_banner["000250"]["perm"] = "NEW_BANNER || ADMINISTRATOR";
$we_menu_banner["000250"]["parent"] = "000200";
$we_menu_banner["000250"]["enabled"] = "0";

$we_menu_banner["000251"]["text"] = g_l('modules_banner', '[bannergroup]');
$we_menu_banner["000251"]["cmd"] = "new_bannergroup";
$we_menu_banner["000251"]["perm"] = "NEW_BANNER || ADMINISTRATOR";
$we_menu_banner["000251"]["parent"] = "000200";
$we_menu_banner["000251"]["enabled"] = "0";

$we_menu_banner["000300"]["text"] = g_l('modules_banner', '[save]');
$we_menu_banner["000300"]["parent"] = "000100";
$we_menu_banner["000300"]["cmd"] = "save_banner";
$we_menu_banner["000300"]["perm"] = "EDIT_BANNER || ADMINISTRATOR";
$we_menu_banner["000300"]["enabled"] = "0";

$we_menu_banner["000400"]["text"] = g_l('modules_banner', '[delete]');
$we_menu_banner["000400"]["parent"] = "000100";
$we_menu_banner["000400"]["cmd"] = "delete_banner";
$we_menu_banner["000400"]["perm"] = "DELETE_BANNER || ADMINISTRATOR";
$we_menu_banner["000400"]["enabled"] = "0";

$we_menu_banner["000500"]["parent"] = "000100"; // separator
$we_menu_banner["000800"]["text"] = g_l('modules_banner', '[quit]');
$we_menu_banner["000800"]["parent"] = "000100";
$we_menu_banner["000800"]["cmd"] = "exit_banner";
$we_menu_banner["000800"]["enabled"] = "1";

$we_menu_banner["002000"]["text"] = g_l('modules_banner', '[options]');
$we_menu_banner["002000"]["parent"] = "000000";
$we_menu_banner["002000"]["enabled"] = "1";

$we_menu_banner["002900"]["text"] = g_l('modules_banner', '[defaultbanner]') . "...";
$we_menu_banner["002900"]["parent"] = "002000";
$we_menu_banner["002900"]["cmd"] = "default_banner";
$we_menu_banner["002900"]["perm"] = "EDIT_BANNER || ADMINISTRATOR";
$we_menu_banner["002900"]["enabled"] = "0";

$we_menu_banner["003000"]["text"] = g_l('modules_banner', '[bannercode]') . "...";
$we_menu_banner["003000"]["parent"] = "002000";
$we_menu_banner["003000"]["cmd"] = "banner_code";
$we_menu_banner["003000"]["perm"] = "EDIT_BANNER || ADMINISTRATOR";
$we_menu_banner["003000"]["enabled"] = "0";


$we_menu_banner["004000"]["text"] = g_l('modules_banner', '[help]');
$we_menu_banner["004000"]["parent"] = "000000";
$we_menu_banner["004000"]["enabled"] = "1";

$we_menu_banner["004100"]["text"] = g_l('modules_banner', '[help]') . "...";
;
$we_menu_banner["004100"]["parent"] = "004000";
$we_menu_banner["004100"]["cmd"] = "help_modules";
$we_menu_banner["004100"]["enabled"] = "1";

$we_menu_banner["004200"]["text"] = g_l('modules_banner', '[info]') . "...";
;
$we_menu_banner["004200"]["parent"] = "004000";
$we_menu_banner["004200"]["cmd"] = "info_modules";
$we_menu_banner["004200"]["enabled"] = "1";
