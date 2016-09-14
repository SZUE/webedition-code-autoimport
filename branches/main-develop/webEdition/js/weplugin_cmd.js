/*
 * webEdition CMS
 *
 * $Rev: 12585 $
 * $Author: mokraemer $
 * $Date: 2016-08-01 22:17:06 +0200 (Mo, 01. Aug 2016) $
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

/* global WE */

var weplugin = WE().util.getDynamicVar(document, 'loadVarWeplugin_cmd', 'data-weplugin');
var _EditorFrame;

switch (weplugin.cmd) {
	case 'editSource':
		if (top.plugin.isLoaded && (typeof top.plugin.document.WePlugin.editSource == "function")) {
			top.plugin.document.WePlugin.editSource(weplugin.session, weplugin.sessionName, weplugin.transaction, weplugin.filename, weplugin.source, weplugin.ct, "true", weplugin.charset);
		}
		break;
	case 'editFile':
		top.plugin.document.WePlugin.editFile(weplugin.session, weplugin.sessionName, weplugin.ua, weplugin.lang, weplugin.enc, weplugin.transaction, weplugin.filename, weplugin.tmp, weplugin.ct);
		break;
	case 'setSource':
		_EditorFrame = WE().layout.weEditorFrameController.getEditorFrameByTransaction(weplugin.transaction);
		_EditorFrame.getContentFrame().reloadContent = true;
		break;
	case 'reloadContentFrame':
		_EditorFrame = WE().layout.weEditorFrameController.getEditorFrameByTransaction(weplugin.transaction);
		_EditorFrame.setEditorIsHot(true);
		switch (_EditorFrame.getEditorEditPageNr()) {
			case WE().consts.global.WE_EDITPAGE_CONTENT:
			case WE().consts.global.WE_EDITPAGE_PREVIEW:
			case WE().consts.global.WE_EDITPAGE_PREVIEW_TEMPLATE:
				if (_EditorFrame.getEditorIsActive()) { // reload active editor
					_EditorFrame.setEditorReloadNeeded(true);
					_EditorFrame.setEditorIsActive(true);
				} else {
					_EditorFrame.setEditorReloadNeeded(true);
				}
		}
		break;
}