<?php

/**
 * webEdition CMS
 *
 * $Rev: 8656 $
 * $Author: mokraemer $
 * $Date: 2014-11-28 18:22:19 +0100 (Fr, 28 Nov 2014) $
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

$dialog = new we_dialog_fullscreenEdit();
$dialog->initByHttp();
echo $dialog->getHTML();
