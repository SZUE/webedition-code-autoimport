/* global WE, top */

/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 11867 $
 * $Author: mokraemer $
 * $Date: 2016-04-08 11:27:24 +0200 (Fr, 08. Apr 2016) $
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
function pressed_cancel() {
	window_closed();
	self.close();
}

function window_closed() {
	_EditorFrame.EditorExitDocQuestionDialog = false;
}

// functions for keyBoard Listener
function applyOnEnter() {
	pressed_yes();
}

// functions for keyBoard Listener
function closeOnEscape() {
	pressed_cancel();
}
