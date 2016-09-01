/* global top,WE */

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

var msg = WE().util.getDynamicVar(document, 'loadVarMsg', 'data-msg');
if (msg) {
	var type = document.getElementById('loadVarMsg').getAttribute('data-msgType');
	top.we_showMessage(msg, type ? parseInt(type) : WE().consts.message.WE_MESSAGE_NOTICE, window);
}