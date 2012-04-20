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



	$we_menu_workflow["000100"]["text"] = g_l('javaMenu_workflow','[workflow]');
    $we_menu_workflow["000100"]["parent"] = "000000";
    $we_menu_workflow["000100"]["enabled"]  = "1";

    $we_menu_workflow["000150"]["text"]= g_l('javaMenu_workflow','[new]');
    $we_menu_workflow["000150"]["cmd"] = "new_workflow";
    $we_menu_workflow["000150"]["perm"] = "NEW_WORKFLOW || ADMINISTRATOR";
    $we_menu_workflow["000150"]["parent"] = "000100";
    $we_menu_workflow["000150"]["enabled"] = "0";

    $we_menu_workflow["000600"]["text"]= g_l('javaMenu_workflow','[save]');
    $we_menu_workflow["000600"]["parent"] = "000100";
    $we_menu_workflow["000600"]["cmd"] = "save_workflow";
    $we_menu_workflow["000600"]["perm"] = "EDIT_WORKFLOW || ADMINISTRATOR";
    $we_menu_workflow["000600"]["enabled"] = "0";

    $we_menu_workflow["000700"]["text"] = g_l('javaMenu_workflow','[delete]');
    $we_menu_workflow["000700"]["parent"] = "000100";
    $we_menu_workflow["000700"]["cmd"] = "delete_workflow";
    $we_menu_workflow["000700"]["perm"] = "DELETE_WORKFLOW || ADMINISTRATOR";
    $we_menu_workflow["000700"]["enabled"] = "0";

    $we_menu_workflow["000800"]["parent"] = "000100"; // separator
/*
	$we_menu_workflow["000850"]["text"] = g_l('javaMenu_workflow','[reload]');
    $we_menu_workflow["000850"]["parent"] = "000100";
    $we_menu_workflow["000850"]["cmd"] = "reload_workflow";
    $we_menu_workflow["000850"]["enabled"] = "0";

	$we_menu_workflow["000880"]["parent"] = "000100"; // separator
*/
    $we_menu_workflow["000900"]["text"]= g_l('javaMenu_workflow','[empty_log]')."...";
    $we_menu_workflow["000900"]["parent"] = "000100";
    $we_menu_workflow["000900"]["cmd"] = "empty_log";
    $we_menu_workflow["000900"]["perm"] = "EMPTY_LOG || ADMINISTRATOR";
    $we_menu_workflow["000900"]["enabled"] = "0";

    $we_menu_workflow["001000"]["parent"] = "000100"; // separator

    $we_menu_workflow["001100"]["text"]= g_l('javaMenu_workflow','[quit]');
    $we_menu_workflow["001100"]["parent"] = "000100";
    $we_menu_workflow["001100"]["cmd"] = "exit_workflow";
    $we_menu_workflow["001100"]["enabled"] = "1";

    $we_menu_workflow["001500"]["text"]= g_l('javaMenu_workflow','[help]');
    $we_menu_workflow["001500"]["parent"] = "000000";
    $we_menu_workflow["001500"]["enabled"] = "1";

    $we_menu_workflow["001600"]["text"]= g_l('javaMenu_workflow','[help]')."...";
    $we_menu_workflow["001600"]["parent"] = "001500";
    $we_menu_workflow["001600"]["cmd"] = "help_modules";
    $we_menu_workflow["001600"]["enabled"] = "1";

    $we_menu_workflow["001700"]["text"]= g_l('javaMenu_workflow','[info]')."...";
    $we_menu_workflow["001700"]["parent"] = "001500";
    $we_menu_workflow["001700"]["cmd"] = "info_modules";
    $we_menu_workflow["001700"]["enabled"] = "1";