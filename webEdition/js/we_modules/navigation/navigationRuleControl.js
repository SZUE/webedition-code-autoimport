/* global WE, weSelect, weInput, categories_edit */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev: 13200 $
 * $Author: mokraemer $
 * $Date: 2016-12-28 01:33:14 +0100 (Mi, 28. Dez 2016) $
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
'use strict';
function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
//	var url = WE().util.getWe_cmdArgsUrl(args);
	var i;
	var doc = top.frames.content;
	switch (args[0]) {
		case "setWorkspaces":
			doc.weSelect.setOptions("WorkspaceID", args[1]);
			break;
		case 'selectWorkspace':
			doc.weSelect.selectOption("WorkspaceID", args[1]);
			break;
		case 'setFormData':
			doc.clearNavigationForm();

			doc.weInput.setValue('ID', args[1].ID);
			doc.weInput.setValue('NavigationName', args[1].NavigationName);

			doc.weInput.setValue('NavigationID', args[1].NavigationID);
			doc.weInput.setValue('NavigationIDPath', args[1].NavigationIDPath);

			doc.weInput.setValue('FolderID', args[1].FolderID);
			doc.weInput.setValue('FolderIDPath', args[1].FolderIDPath);

			doc.weSelect.selectOption('SelectionType', args[1].SelectionType);
			doc.switchType(args[1].SelectionType);

			doc.weInput.setValue('DoctypeID', args[1].DoctypeID);

			doc.weInput.setValue('ClassID', args[1].ClassID);
			doc.weInput.setValue('ClassIDPath', args[1].ClassIDPath);
			break;
		case 'setCategories':
			doc.removeAllCats();
			for (i = 0; i < args[1].length; i++) {
				doc.categories_edit.addItem();
				doc.categories_edit.setItem(0, (doc.categories_edit.itemCount - 1), args[1][i]);
			}

			doc.categories_edit.showVariant(0);
			doc.weInput.setValue('CategoriesCount', doc.categories_edit.itemCount);
			break;
		case 'delRule':
			doc.weSelect.removeOption('navigationRules', args[1], args[2]);
			doc.weInput.setValue('ID', 0);
			break;
		case 'setSavedRule':
			if (args[3]) {
				doc.weSelect.addOption('navigationRules', args[1], args[2]);
			} else {
				doc.weSelect.updateOption('navigationRules', args[1], args[2]);
			}

			doc.weSelect.selectOption('navigationRules', args[1]);
			doc.weInput.setValue('ID', args[1]);
			break;
		default:
			top.opener.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}