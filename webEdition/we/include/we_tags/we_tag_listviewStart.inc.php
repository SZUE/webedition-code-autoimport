<?php

/**
 * webEdition CMS
 *
 * $Rev: 8665 $
 * $Author: mokraemer $
 * $Date: 2014-12-02 17:11:48 +0100 (Di, 02. Dez 2014) $
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
function we_tag_listviewStart(){
	return $GLOBALS['lv']->start + 1 - abs($GLOBALS['lv']->offset);
}
