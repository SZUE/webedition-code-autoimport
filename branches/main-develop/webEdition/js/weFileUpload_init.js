/* global WE, top */

/**
 webEdition CMS
 *
 * $Rev: 12935 $
 * $Author: lukasimhof $
 * $Date: 2016-10-06 16:15:28 +0200 (Do, 06 Okt 2016) $
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

window.initObj = WE().util.getDynamicVar(document, 'loadVarWeFileUpload_init', 'data-initObject');
window.weFileUpload_instance = new WE().layout.weFileUpload(window.initObj.uiType, window);
window.weFileUpload_instance.init(window.initObj);
window.initObj = {};