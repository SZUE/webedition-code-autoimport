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
 * Language: Deutsch
 */
$l_sidebar = array(
		'headline' => "Sidebar",
		'confirm_to_close_sidebar' => "Möchten Sie die Sidebar wirklich schließen?",
// shown on the default sidebar page
		'default' => array(
				array(
						'headline' => 'Herzlich Willkommen!',
						'text' => 'webEdition ist nun erfolgreich installiert, enthält zunächst jedoch noch keine Inhalte.',
				),
				array(
						'headline' => 'Dokumentation',
						'text' => 'Hier finden Sie grundlegende Informationen zu Bedienung und Aufbau von webEdition.',
						'link' => 'http://documentation.webedition.org/wiki/de/start',
						'icon' => 'documentation.gif',
				),
				array(
						'headline' => 'Weitere Hilfe-Ressourcen',
						'text' => 'Übersicht weiterer Anleitungen und Referenzen.',
						'link' => 'javascript:top.we_cmd(\'help\');',
						'icon' => 'help.gif',
				),
				/*
				  array(
				  'headline' => 'So geht\'s weiter',
				  'text' => 'Ihre individuelle Website können Sie von Grund auf neu erstellen oder auf vorhandene Elemente und Basislayouts zugreifen.',
				  ),
				 */
				array(
						'headline' => 'Tagreferenz',
						'text' => 'Hier finden Sie eine Auflistung aller in webEdition verwendeten we:Tags mit Attributen und Anwendungsbeispielen.',
						'link' => 'http://tags.webedition.org/wiki/de/',
						'icon' => 'firststepswizard.gif',
				),
				array(
						'headline' => 'Forum',
						'text' => 'Das offizielle webEdition Support Forum mit vielen Fragen und Antworten zu allen Themen rund um webEdition',
						'link' => 'http://forum.webedition.org/viewforum.php?f=35',
						'icon' => 'tutorial.gif',
				),
				array(
						'headline' => 'Versionshistorie',
						'text' => 'Alle Fehlerbehebungen und Neuerungen in webEdition mit Versionsangaben ',
						'link' => 'http://documentation.webedition.org/wiki/de/webedition/change-log/start',
						'icon' => 'demopages.gif',
				),
		/*
		  array(
		  'headline' => 'Econda',
		  'text' => '<a href="http://webedition.de/de/econda" target="_blank">econda</a> ist der führende Anbieter  für erfolgreiches Web Controlling und Technologiepartner von webEdition. econda Lösungen sind auf die Bedürfnisse und Ziele von Online-Versandhändlern zugeschnitten und liefern in Echtzeit Entscheidungsgrundlagen für dauerhafte Umsatzsteigerung. Dabei steht das econda Team seinen Kunden mit Web-Analytics-Expertise aus hunderten Projekten beratend zur Seite. Testen Sie econda Monitor <a href="http://webedition.de/de/econda-formular" target="_blank">14 Tage kostenlos</a> und unverbindlich! Weitere informationen zur Installation finden Sie in der <a href="http://documentation.webedition.de/200810150921145860" target="_blank">webEdition Online-Dokumentation</a>.',
		  ),
		 */
// Only shown on the default sidebar page if user has administrator perms
		),
		'admin' => array(
				array(
						'headline' => 'Einstellungen Sidebar',
						'text' => 'Einstellungen zur Sidebar, wie individuelle Startdokumente, Breite oder Deaktivierung der Sidebar, finden Sie unter Extras > Einstellungen > Allgemein... auf dem Karteireiter "Oberfläche".',
						'link' => 'javascript:top.we_cmd(\'openPreferences\');',
				),
		),
);
