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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$_frame = 'opener.' . (we_base_request::_(we_base_request::RAW, 'frame') ? : 'top');
$_yes = $_frame . '.hot=0;' . $_frame . '.we_cmd("' . (we_base_request::_(we_base_request::RAW, 'approveCmd') ? : 'save') . '");self.close()';
$_no = $_frame . '.hot=0;' . $_frame . '.we_cmd("' . (we_base_request::_(we_base_request::RAW, 'declineCmd') ? : 'close') . '","' . we_base_request::_(we_base_request::INT, 'declineParam') . '");self.close();';
$_cancel = 'self.close();';

echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET, '
<body class="weEditorBody" onBlur="self.focus()" onload="self.focus()">' .
	we_html_tools::htmlYesNoCancelDialog(g_l('modules_shop', '[exit_question]'), IMAGE_DIR . "alert.gif", "ja", "nein", "abbrechen", $_yes, $_no, $_cancel) . //GL
	'</body>');
