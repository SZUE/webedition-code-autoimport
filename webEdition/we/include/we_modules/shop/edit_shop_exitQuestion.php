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

$frame = 'opener.' . (we_base_request::_(we_base_request::RAW, 'frame') ? : 'top');
$yes = $frame . '.hot=0;' . $frame . '.we_cmd("' . (we_base_request::_(we_base_request::RAW, 'approveCmd') ? : 'save') . '");self.close()';
$no = $frame . '.hot=0;' . $frame . '.we_cmd("' . (we_base_request::_(we_base_request::RAW, 'declineCmd') ? : 'close') . '","' . we_base_request::_(we_base_request::INT, 'declineParam') . '");self.close();';
$cancel = 'self.close();';

echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', '', '
<body class="weEditorBody" onBlur="self.focus()" onload="self.focus()">' .
	we_html_tools::htmlYesNoCancelDialog(g_l('modules_shop', '[exit_question]'), '<span class="fa-stack fa-lg" style="color:#F2F200;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>', "ja", "nein", "abbrechen", $yes, $no, $cancel) . //GL
	'</body>');
