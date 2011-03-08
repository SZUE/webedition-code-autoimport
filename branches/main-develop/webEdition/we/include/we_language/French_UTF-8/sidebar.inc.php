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
		'headline' => "Sidebar", // TRANSLATE
		'confirm_to_close_sidebar' => "Would you really like to close the sidebar?", // TRANSLATE
// shown on the default sidebar page
		'default' => array(
				array(
						'headline' => 'Welcome!',
						'text' => 'webEdition is installed successfully, but contains no contents yet.', // TRANSLATE
				),
				array(
						'headline' => 'Manuals',
						'text' => 'Here you find basic information about the operation and structure of webEdition', // TRANSLATE
						'link' => 'http://documentation.webedition.org/wiki/en/start',
						'icon' => 'documentation.gif', // TRANSLATE
				),
				array(
						'headline' => 'Other help resources',
						'text' => 'Overview of farther instructions and references', // TRANSLATE
						'link' => 'javascript:top.we_cmd(\'help\');', // TRANSLATE
						'icon' => 'help.gif', // TRANSLATE
				),
				array(
						'headline' => 'How to proceed',
						'text' => 'You can create your individual web site entirely from the scratch or access available elements and base layouts.', // TRANSLATE
				),
				array(
						'headline' => 'First-Steps-Wizard',
						'text' => 'Use this wizard to install a ready-to-use base layouts. With "webEdition Online" you can install templates for special purposes at any time.', // TRANSLATE
						'link' => 'javascript:top.we_cmd(\'openFirstStepsWizardMasterTemplate\');', // TRANSLATE
						'icon' => 'firststepswizard.gif', // TRANSLATE
				),
				array(
						'headline' => 'Demo web site',
						'text' => 'These are entire web sites including example contents. You can import and freely edit these to fit your needs.', // TRANSLATE
						'link' => 'http://demo.en.webedition.info/', // TRANSLATE
						'icon' => 'demopages.gif', // TRANSLATE
				),
				array(
						'headline' => 'Econda',
						'text' => '<a href="http://webedition.de/en/econda" target="_blank">econda</a> is the leading provider for web controlling solutions and webEdition technology partner.  The econda Shop Monitor makes online-shop analytics accessible, comprehensible and indispensable for optimally informed marketing and business decisions. <a href="http://webedition.de/en/econda-form" target="_blank">Register now</a> for a free 14-day trial! More information regarding the installation can be found in the <a href="http://documentation.webedition.de/200810241003219195" target="_blank">webEdition online documentation</a>.', // TRANSLATE
				),
		),
// Only shown on the default sidebar page if user has administrator perms
		'admin' => array(
				array(
						'headline' => 'Preferences Sidebar',
						'text' => 'You find the settings for the Sidebar, like individual start documents, width or deactivation of the sidebar under extras> preferences > common ... on the "User interface" tab', // TRANSLATE
						'link' => 'javascript:top.we_cmd(\'openPreferences\');', // TRANSLATE
				),
		),
);