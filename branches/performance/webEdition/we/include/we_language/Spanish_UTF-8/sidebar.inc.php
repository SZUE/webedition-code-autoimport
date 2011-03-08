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
 * @package    webEdition_language
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/**
 * Language file: sidebar.inc.php
 * Provides language strings for the sidebar.
 * Language: English
 */
$l_sidebar = array(
		'headline' => "Sidebar",
		'confirm_to_close_sidebar' => "Would you really like to close the sidebar?",
// shown on the default sidebar page
		'default' => array(
				array(
						'headline' => 'Welcome!',
						'text' => 'webEdition is installed successfully, but contains no contents yet.',
				),
				array(
						'headline' => 'Online documentation',
						'text' => 'Here you find basic information about the operation and structure of webEdition',
						'link' => 'http://documentation.webedition.org/wiki/en/start',
						'icon' => 'documentation.gif',
				),
				array(
						'headline' => 'Other help resources',
						'text' => 'Overview of further instructions and references',
						'link' => 'javascript:top.we_cmd(\'help\');',
						'icon' => 'help.gif',
				),
				/*
				  array(
				  'headline' => 'How to proceed',
				  'text' => 'You can create your individual web site entirely from the scratch or access available elements and base layouts.',
				  ),
				 */

				array(
						'headline' => 'Tag reference',
						'text' => 'Here you will find a list of all webEdition we:Tags with attributes and examples. ',
						'link' => 'http://tags.webedition.org/wiki/en/',
						'icon' => 'firststepswizard.gif',
				),
				array(
						'headline' => 'Forum',
						'text' => 'Official webEdition support forum with many Q&A concerning all kind of webEdition problems ',
						'link' => 'http://forum.webedition.org/viewforum.php?f=36',
						'icon' => 'tutorial.gif',
				),
				array(
						'headline' => 'Version history',
						'text' => 'A complete changelog of all webEdition bugfixes and improvements',
						'link' => 'http://documentation.webedition.org/wiki/en/webedition/change-log/start',
						'icon' => 'demopages.gif',
				),
		),
// Only shown on the default sidebar page if user has administrator perms
		'admin' => array(
				array(
						'headline' => 'Preferences Sidebar',
						'text' => 'You find the settings for the Sidebar, like individual start documents, width or deactivation of the sidebar under extras> preferences > common ... on the "User interface" tab',
						'link' => 'javascript:top.we_cmd(\'openPreferences\');',
				),
		),
);
