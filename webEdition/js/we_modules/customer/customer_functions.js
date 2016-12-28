/* global WE */

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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
'use strict';

var fieldTypesByName = WE().util.getDynamicVar(document, 'loadVarCustomerFuntions', 'data-customerFunctions');
function showHideDatePickerIcon(fieldNr, show) {
	document.getElementsByName('value_' + fieldNr)[0].style.display = (show ? 'none' : '');
	document.getElementsByName('value_date_' + fieldNr)[0].style.display = (show ? '' : 'none');
	document.getElementById('date_picker_' + fieldNr).style.display = (show ? '' : 'none');
	document.getElementById('dpzell_' + fieldNr).style.display = (show ? '' : 'none');
}

function isDateField(fieldNr) {
	var selBranch = document.getElementsByName('branch_' + fieldNr)[0].value;
	var selField = document.getElementsByName('field_' + fieldNr)[0].value;
	selField = selField.substring(selBranch.length + 1, selField.length);
	if (fieldTypesByName[selField] === 'date' || fieldTypesByName[selField] === 'datetime') {
		showHideDatePickerIcon(fieldNr, true);
	} else {
		showHideDatePickerIcon(fieldNr, false);
	}
}

function lookForDateFields() {
	var selBranch, selField;
	for (var i = 0; i < document.getElementsByName('count')[0].value; i++) {
		selBranch = document.getElementsByName('branch_' + i)[0].value;
		selField = document.getElementsByName('field_' + i)[0].value;
		selField = selField.substring(selBranch.length + 1, selField.length);
		if (fieldTypesByName[selField] === 'date' || fieldTypesByName[selField] === 'datetime') {
			if (document.getElementsByName('value_' + i)[0].value !== '') {
				//document.getElementById('value_date_' + i).value = fieldDate.timestempToDate(document.getElementsByName('value_' + i)[0].value);

			}
			showHideDatePickerIcon(i, true);
		}
	}
}

function transferDateFields() {
	var selBranch,selField;
	for (var i = 0; i < document.getElementsByName('count')[0].value; i++) {
		selBranch = document.getElementsByName('branch_' + i)[0].value;
		selField = document.getElementsByName('field_' + i)[0].value;
		selField = selField.substring(selBranch.length + 1, selField.length);
		if (fieldTypesByName[selField] === 'date' && document.getElementById('value_date_' + i).value !== '') {
			//document.getElementsByName('value_' + i)[0].value = fieldDate.dateToTimestemp(document.getElementById('value_date_' + i).value);
		}
	}
}