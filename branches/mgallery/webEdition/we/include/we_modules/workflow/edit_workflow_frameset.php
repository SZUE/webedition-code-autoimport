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
$what = we_base_request::_(we_base_request::STRING, 'pnt', 'frameset');
//protect, but not, if call on document
$protect = we_base_moduleInfo::isActive('workflow') && ($what === 'log' || we_users_util::canEditModule('workflow')) ? null : array(false);
we_html_tools::protect($protect);

$mode = we_base_request::_(we_base_request::INT, 'art', 0);
$type = we_base_request::_(we_base_request::INTLIST, 'type', 0);

$weFrame = new we_workflow_frames();
$weFrame->process();
echo $weFrame->getHTML($what, $mode, $type);
