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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/*
 * This file is opened by js-function dounload() which is only triggered
 * when webEdition.php is not closed regularily (by using menu -> quit): window.onbeforeunload()
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect(null, WEBEDITION_DIR . 'index.php');

$GLOBALS['isIncluded'] = true;
include(WE_INCLUDES_PATH . 'we_logout.inc.php');

if(we_base_request::_(we_base_request::BOOL, 'isopener')){
	header('location: ' . WEBEDITION_DIR . 'index.php');
}

echo we_html_tools::getHtmlTop('', '', '', '', '
	<body onload="self.setTimeout(function(){
		self.close();
	}, 1000);" style="background-color:#386AAB;color:white">
		' . g_l('global', '[irregular_logout]') . '
	</body>');
