/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev: 9450 $
 * $Author: mokraemer $
 * $Date: 2015-03-02 00:54:31 +0100 (Mo, 02. Mär 2015) $
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
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

function IsDigitPercent(e) {
	var key;
	if (e.charCode === undefined) {
		key = event.keyCode;
	} else {
		key = e.charCode;
	}

	return (((key >= 48) && (key <= 57)) || (key === 37) || (key === 0) || (key === 46) || (key === 101) || (key === 109) || (key === 13) || (key === 8) || (key <= 63235 && key >= 63232) || (key === 63272));
}

function doUnload() {
	if (jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function IsDigit(e) {
	var key = (e.charCode === undefined ?
					event.keyCode :
					e.charCode);

	return (((key >= 48) && (key <= 57)) || (key == 0) || (key == 13) || (key == 8) || (key <= 63235 && key >= 63232) || (key == 63272));
}


function weSaveToGlossaryFn() {
	document.we_form.elements.weSaveToGlossary.value = 1;
	document.we_form.submit();
}