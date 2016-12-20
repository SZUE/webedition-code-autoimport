/* global WE */

/**
 * webEdition CMS
 *
 * $Rev: 12156 $
 * $Author: mokraemer $
 * $Date: 2016-05-24 12:38:58 +0200 (Di, 24. Mai 2016) $
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

var preview = WE().util.getDynamicVar(document, 'loadVarPreview', 'data-preview');

function init() {
	parent.rpcHandleResponse(preview.type, preview.id, document.getElementById(preview.type), preview.tb);
	if (preview.iconClass) {
		WE().util.setIconOfDocClass(document, preview.iconClass);
	}
}