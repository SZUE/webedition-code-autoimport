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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
var isRegisterDialogHere = true;
var tinyMceDialog = null;
var tinyMceSecondaryDialog = null;
var tinyMceFullscreenDialog = null;
var blocked = false;

function weRegisterTinyMcePopup(win, action) {
	win = win !== undefined ? win : null;
	switch (action) {
		case "registerDialog":
			if (!blocked) {
				if (tinyMceDialog) {
					try {
						tinyMceDialog.close();
					} catch (err) {
					}
				}
				tinyMceDialog = win;
			} else {
				blocked = false;
			}
			if (tinyMceSecondaryDialog) {
				try {
					tinyMceSecondaryDialog.close();
				} catch (err) {
				}
			}
			break;
		case "registerSecondaryDialog":
			if (tinyMceSecondaryDialog) {
				try {
					tinyMceSecondaryDialog.close();
				} catch (err) {
				}
			}
			tinyMceSecondaryDialog = win;
			break;
		case "registerFullscreenDialog":
			if (tinyMceDialog) {
				try {
					tinyMceDialog.close();
				} catch (err) {
				}
			}
			if (tinyMceSecondaryDialog) {
				try {
					tinyMceSecondaryDialog.close();
				} catch (err) {
				}
			}
			if (tinyMceFullscreenDialog) {
				try {
					tinyMceFullscreenDialog.close();
				} catch (err) {
				}
			}
			tinyMceFullscreenDialog = win;
			break;
		case "block":
			blocked = true;
			break;
		case "skip":
			// do nothing!
			break;
		case "unregisterDialog":
			if (tinyMceDialog) {
				try {
					tinyMceDialog.close();
				} catch (err) {
				}
			}
			/* falls through */
		case "unregisterSecondaryDialog":
			if (tinyMceSecondaryDialog) {
				try {
					tinyMceSecondaryDialog.close();
				} catch (err) {
				}
			}
			break;
	}
}