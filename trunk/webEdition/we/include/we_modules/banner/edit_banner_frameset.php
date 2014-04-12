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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
$protect = we_base_moduleInfo::isActive('banner') && we_users_util::canEditModule('banner') ? null : array(false);
we_html_tools::protect($protect);


$what = weRequest('string', "pnt", "frameset");
$mode = weRequest('int', "art", 0);

$weFrame = new we_banner_frames(WEBEDITION_DIR . 'we/include/we_modules/banner/edit_banner_frameset.php');
echo $weFrame->getHTMLDocumentHeader();
$weFrame->View->processVariables();
$weFrame->View->processCommands();
echo $weFrame->getHTML($what, $mode);
