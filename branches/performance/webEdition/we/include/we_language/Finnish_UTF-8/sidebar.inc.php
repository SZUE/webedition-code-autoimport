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
		'headline' => "Sivupalkki",
		'confirm_to_close_sidebar' => "Haluatko varmasti sulkea sivupalkin?",
// shown on the default sidebar page
		'default' => array(
				array(
						'headline' => 'Tervetuloa!',
						'text' => 'webEdition on nyt asennettu mutta toistaiseksi se on viel� ilman sis�lt��.',
				),
				array(
						'headline' => 'Manuaalit',
						'text' => 'T��lt� l�yd�t tietoa webEditionin toiminnasta ja rakenteesta',
						'link' => 'http://documentation.webedition.org/wiki/en/start',
						'icon' => 'documentation.gif',
				),
				array(
						'headline' => 'Muita tiedonl�hteit�',
						'text' => 'Katsaus muista tiedonl�hteist�',
						'link' => 'javascript:top.we_cmd(\'help\');',
						'icon' => 'help.gif',
				),
				array(
						'headline' => 'Tagi hakemisto',
						'text' => 'Here you will find a list of all webEdition we:Tags with attributes and examples. ',
						'link' => 'http://tags.webedition.org/wiki/en/',
						'icon' => 'firststepswizard.gif',
				),
				array(
						'headline' => 'Keskustelufoorumi',
						'text' => 'Official webEdition support forum with many Q&A concerning all kind of webEdition problems ',
						'link' => 'http://forum.webedition.org/viewforum.php?f=36',
						'icon' => 'tutorial.gif',
				),
				array(
						'headline' => 'Versiohistoria',
						'text' => 'A complete changelog of all webEdition bugfixes and improvements',
						'link' => 'http://documentation.webedition.org/wiki/en/webedition/change-log/start',
						'icon' => 'demopages.gif',
				),
		),
// Only shown on the default sidebar page if user has administrator perms
		'admin' => array(
				array(
						'headline' => 'Sivupalkin asetukset',
						'text' => 'L�yd�t sivupalkin asetukset, kuten yksil�llisen aloitussivun ja mitta-asetukset valikosta extrat> asetukset > yleiset ... "K�ytt�liittym�" v�lilehdelt�',
						'link' => 'javascript:top.we_cmd(\'openPreferences\');',
				),
		),
);
