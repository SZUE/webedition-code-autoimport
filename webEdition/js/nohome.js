/* global WE, top */

/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 12699 $
 * $Author: mokraemer $
 * $Date: 2016-08-31 15:32:00 +0200 (Mi, 31. Aug 2016) $
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
function isHot(){
	return false;
}

function closeAllModalWindows(){
}

var _EditorFrame = WE().layout.weEditorFrameController.getEditorFrame(window.name);
_EditorFrame.initEditorFrameData({
	"EditorType":"cockpit",
	"EditorDocumentText":"Cockpit",
	"EditorDocumentPath":"Cockpit",
	"EditorContentType":"cockpit",
	"EditorEditCmd":"open_cockpit"
});