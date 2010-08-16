<?php

/**
 * webEdition CMS
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
 * @package    webEdition_language
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */


/**
 * Language file: sidebar.inc.php
 * Provides language strings for the sidebar.
 * Language: English
 */

$l_sidebar["headline"] = "Sidebar";
$l_sidebar["confirm_to_close_sidebar"] = "Would you really like to close the sidebar?";

// shown on the default sidebar page
$l_sidebar["default"] = array();

$l_sidebar["default"][0] = array();
$l_sidebar["default"][0]["headline"] = 'Welcome!';
$l_sidebar["default"][0]["text"] = 'webEdition is installed successfully, but contains no contents yet.';

$l_sidebar["default"][1] = array();
$l_sidebar["default"][1]["headline"] = 'Online documentation';
$l_sidebar["default"][1]["text"] = 'Here you find basic information about the operation and structure of webEdition';
$l_sidebar["default"][1]["link"] = 'http://documentation.webedition.org/wiki/en/start';
$l_sidebar["default"][1]["icon"] = 'documentation.gif';

$l_sidebar["default"][2] = array();
$l_sidebar["default"][2]["headline"] = 'Other help resources';
$l_sidebar["default"][2]["text"] = 'Overview of further instructions and references';
$l_sidebar["default"][2]["link"] = 'javascript:top.we_cmd(\'help\');';
$l_sidebar["default"][2]["icon"] = 'help.gif';
/*
$l_sidebar["default"][3] = array();
$l_sidebar["default"][3]["headline"] = 'How to proceed';
$l_sidebar["default"][3]["text"] = 'You can create your individual web site entirely from the scratch or access available elements and base layouts.';
*/

$l_sidebar["default"][3] = array();
$l_sidebar["default"][3]["headline"] = 'Tag reference';
$l_sidebar["default"][3]["text"] = 'Here you will find a list of all webEdition we:Tags with attributes and examples. ';
$l_sidebar["default"][3]["link"] = 'http://tags.webedition.org/wiki/en/';
$l_sidebar["default"][3]["icon"] = 'firststepswizard.gif';

$l_sidebar["default"][4] = array();
$l_sidebar["default"][4]["headline"] = 'Forum';
$l_sidebar["default"][4]["text"] = 'Official webEdition support forum with many Q&A concerning all kind of webEdition problems ';
$l_sidebar["default"][4]["link"] = 'http://forum.webedition.org/viewforum.php?f=36';
$l_sidebar["default"][4]["icon"] = 'tutorial.gif';

$l_sidebar["default"][5] = array();
$l_sidebar["default"][5]["headline"] = 'Version history';
$l_sidebar["default"][5]["text"] = 'A complete changelog of all webEdition bugfixes and improvements';
$l_sidebar["default"][5]["link"] = 'http://documentation.webedition.org/wiki/en/webedition/change-log/start';
$l_sidebar["default"][5]["icon"] = 'demopages.gif';
// Only shown on the default sidebar page if user has administrator perms
$l_sidebar["admin"] = array();

$l_sidebar["admin"][0] = array();
$l_sidebar["admin"][0]["headline"] = 'Preferences Sidebar';
$l_sidebar["admin"][0]["text"] = 'You find the settings for the Sidebar, like individual start documents, width or deactivation of the sidebar under extras> preferences > common ... on the "User interface" tab';
$l_sidebar["admin"][0]["link"] = 'javascript:top.we_cmd(\'openPreferences\');';

?>