/* global top, WE, doc */

/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 13374 $
 * $Author: mokraemer $
 * $Date: 2017-02-15 19:33:39 +0100 (Mi, 15. Feb 2017) $
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

$(function () {
	window.weCollectionEdit = WE().layout.getGUI_Collection(window, WE().util.getDynamicVar(document, 'loadVarCollection', 'data-dynamicVars'));
});