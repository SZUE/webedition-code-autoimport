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

$l_sidebar["headline"] = "Sivupalkki";
$l_sidebar["confirm_to_close_sidebar"] = "Haluatko varmasti sulkea sivupalkin?";

// shown on the default sidebar page
$l_sidebar["default"] = array();

$l_sidebar["default"][0] = array();
$l_sidebar["default"][0]["headline"] = 'Tervetuloa!';
$l_sidebar["default"][0]["text"] = 'webEdition on nyt asennettu mutta toistaiseksi se on viel� ilman sis�lt��.';

$l_sidebar["default"][1] = array();
$l_sidebar["default"][1]["headline"] = 'Manuaalit';
$l_sidebar["default"][1]["text"] = 'T��lt� l�yd�t tietoa webEditionin toiminnasta ja rakenteesta';
$l_sidebar["default"][1]["link"] = 'http://www.webedition.de/en/Documentation/index.php';
$l_sidebar["default"][1]["icon"] = 'documentation.gif';

$l_sidebar["default"][2] = array();
$l_sidebar["default"][2]["headline"] = 'Muita tiedonl�hteit�';
$l_sidebar["default"][2]["text"] = 'Katsaus muista tiedonl�hteist�';
$l_sidebar["default"][2]["link"] = 'javascript:top.we_cmd(\'help\');';
$l_sidebar["default"][2]["icon"] = 'help.gif';

$l_sidebar["default"][3] = array();
$l_sidebar["default"][3]["headline"] = 'Kuinka jatkaa';
$l_sidebar["default"][3]["text"] = 'Voit luoda yksil�llisen web-sivuston alusta alkaen tai k�ytt�� tarjottuja asetteluelementtej�.';

$l_sidebar["default"][4] = array();
$l_sidebar["default"][4]["headline"] = 'Aloitusvelho';
$l_sidebar["default"][4]["text"] = 'K�yt� t�t� velhoa asentaaksesi k�ytt�valmiita perussivupohjia. "webEdition Onlinen" avulla voit asentaa sivupohjia erikoistarkoituksiin milloin vain.';
$l_sidebar["default"][4]["link"] = 'javascript:top.we_cmd(\'openFirstStepsWizardMasterTemplate\');';
$l_sidebar["default"][4]["icon"] = 'firststepswizard.gif';

$l_sidebar["default"][5] = array();
$l_sidebar["default"][5]["headline"] = 'Demosivut';
$l_sidebar["default"][5]["text"] = 'N�m� esimerkkisivut sis�lt�v�t t�ydellisen esimerkin perussivuista. Voi t�ysin vapaasti tuoda t��lt� osia omiin sivustoihisi ja muokata niit� haluamallasi tavalla.';
$l_sidebar["default"][5]["link"] = 'http://demo.en.webedition.info/';
$l_sidebar["default"][5]["icon"] = 'demopages.gif';

$l_sidebar["default"][6] = array();
$l_sidebar["default"][6]["headline"] = 'Econda';
$l_sidebar["default"][6]["text"] = '<a href="http://webedition.de/en/econda" target="_blank">econda</a> is the leading provider for web controlling solutions and webEdition technology partner.  The econda Shop Monitor makes online-shop analytics accessible, comprehensible and indispensable for optimally informed marketing and business decisions. <a href="http://webedition.de/en/econda-form" target="_blank">Register now</a> for a free 14-day trial! More information regarding the installation can be found in the <a href="http://documentation.webedition.de/200810241003219195" target="_blank">webEdition online documentation</a>.';

// Only shown on the default sidebar page if user has administrator perms
$l_sidebar["admin"] = array();

$l_sidebar["admin"][0] = array();
$l_sidebar["admin"][0]["headline"] = 'Sivupalkin asetukset';
$l_sidebar["admin"][0]["text"] = 'L�yd�t sivupalkin asetukset, kuten yksil�llisen aloitussivun ja mitta-asetukset valikosta extrat> asetukset > yleiset ... "K�ytt�liittym�" v�lilehdelt�';
$l_sidebar["admin"][0]["link"] = 'javascript:top.we_cmd(\'openPreferences\');';

?>