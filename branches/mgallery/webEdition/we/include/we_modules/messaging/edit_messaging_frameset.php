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
$protect = we_base_moduleInfo::isActive('messaging') && we_users_util::canEditModule('messaging') ? null : array(false);
we_html_tools::protect($protect);


$what = we_base_request::_(we_base_request::STRING, "pnt", "frameset");

if(!isset($we_transaction)){//FIXME: can this ever be set except register globals???
	$we_transaction = 0;
}
$transaction = $what === 'frameset' ? $we_transaction : we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 'no_request');//FIXME: is $transaction used anywhere?

$weFrame = new we_messaging_frames(we_base_request::_(we_base_request::STRING, 'viewclass', 'message'), we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 'no_request'), $we_transaction);
$weFrame->process();
echo $weFrame->getHTML($what);
