/*
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

/* global top, WE */

var backup = WE().util.getDynamicVar(document, 'loadVarBackup_wizard', 'data-backup');

function doExport() {
	if ((!top.body.document.we_form.export_send.checked) && (!top.body.document.we_form.export_server.checked)) {
		WE().util.showMessage(WE().consts.g_l.backupWizard.save_not_checked, WE().consts.message.WE_MESSAGE_WARNING, window);
	} else {
		top.busy.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=" + backup.modeCmd + "&pnt=busy&operation_mode=busy&step=2";
		top.body.we_submitForm("cmd", WE().consts.dirs.WE_INCLUDES_DIR + "we_editors/we_backup_cmd.php");
	}
}

function pressYesStep1() {
	var _usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();
	var _unsavedChanges = false;
	for (var frameId in _usedEditors) {
		if (_usedEditors[frameId].getEditorIsHot()) {
			_unsavedChanges = true;
		}
	}

	if (_unsavedChanges) {
		WE().util.showMessage(WE().consts.g_l.backupWizard.recover_backup_unsaved_changes, WE().consts.message.WE_MESSAGE_WARNING, window);
	} else {
		top.body.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=" + backup.modeCmd + "&pnt=body&do_import_after_backup=1";
		top.busy.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=" + backup.modeCmd + "&pnt=busy";
		top.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=" + backup.modeCmd + "&pnt=cmd";
	}

}