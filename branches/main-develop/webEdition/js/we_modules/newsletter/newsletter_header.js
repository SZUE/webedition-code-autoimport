/* global WE, top */

/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 13200 $
 * $Author: mokraemer $
 * $Date: 2016-12-28 01:33:14 +0100 (Mi, 28 Dez 2016) $
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
'use strict';

function setTab(tab) {
	top.content.editor.edbody.we_cmd(tab);
}
