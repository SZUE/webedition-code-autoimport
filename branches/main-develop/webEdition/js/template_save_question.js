/*
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 12732 $
 * $Author: mokraemer $
 * $Date: 2016-09-06 15:05:35 +0200 (Di, 06. Sep 2016) $
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

/* global WE */

var editorSave = WE().util.getDynamicVar(document, 'loadVarTemplate_save_question', 'data-editorSave');

// functions for keyBoard Listener
function applyOnEnter() {
	pressed_yes_button();
}

// functions for keyBoard Listener
function closeOnEscape() {
	pressed_cancel_button();
}

function pressed_cancel_button() {
	self.close();
}

function pressed_yes_button() {
	opener.top.we_cmd('save_document', editorSave.we_transaction, 0, 1, 1, WE().util.Base64.encode(JSON.stringify(editorSave.we_responseJS)), WE().util.Base64.encode(JSON.stringify(editorSave.we_cmd6)));
	self.close();

}

function pressed_no_button() {
	opener.top.we_cmd('save_document', editorSave.we_transaction, 0, 1, 0, WE().util.Base64.encode(JSON.stringify(editorSave.we_responseJS)), WE().util.Base64.encode(JSON.stringify(editorSave.we_cmd6)));
	self.close();
}
