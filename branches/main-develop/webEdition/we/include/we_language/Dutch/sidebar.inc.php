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
		'headline' => "Zijbalk",
		'confirm_to_close_sidebar' => "Wilt u de zijbalk echt sluiten?",
// shown on the default sidebar page
		'default' => array(
				array(
						'headline' => 'Welcome!',
						'text' => 'webEdition is succesvol ge�nstalleerd, maar bevat nog geen content.',
				),
				array(
						'headline' => 'Manuals',
						'text' => 'Hier vind u algemene informatie over de werking en structuur van webEdition',
						'link' => 'http://documentation.webedition.org/wiki/en/start', // CHECK

						'icon' => 'documentation.gif', // TRANSLATE
				),
				array(
						'headline' => 'Other help resources',
						'text' => 'Overzicht van verdere instructies en referenties',
						'link' => 'javascript:top.we_cmd(\'help\');', // TRANSLATE
						'icon' => 'help.gif', // TRANSLATE
				),
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
						'text' => 'U vind de instellingen voor de zijbalk, zoals individuele start documenten, breedte of deactivatie van de zijbalk onder extras> voorkeuren > algemeen ... onder de "Gebruikers interface" tab',
						'link' => 'javascript:top.we_cmd(\'openPreferences\');', // TRANSLATE
				),
		),
);
