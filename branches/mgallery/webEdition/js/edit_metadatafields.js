/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
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


function togglePropositionTable(sel, index) {
	var row = document.getElementById("proposalTable_" + index);
	row.style.display = sel.value === "none" ? "none" : "block";

	var fields = row.getElementsByTagName("INPUT");
	for (var i = 0; i < fields.length; i++) {
		fields[i].disabled = sel.value === "auto" ? true : false;
	}
}

function toggleType(sel, index) {
	var row = document.getElementById("proposalTable_" + index);
	var selMode = document.forms[0].elements["metadataMode[" + index + "]"];

	row.style.display = sel.value !== "textfield" ? "none" : (selMode.options[selMode.selectedIndex].value === "none" ? "none" : "block");
	selMode.disabled = sel.value === "textfield" ? false : true;
}


function delRow(id) {
	var elem = document.getElementById("metadataTable");
	if (elem) {
		var trows = elem.rows;
		var rowID0 = "metadataRow0_" + id;
		var rowID = "metadataRow_" + id;
		var rowID1 = "metadataRow1_" + id;
		var rowID2 = "metadataRow2_" + id;
		var rowID3 = "metadataRow3_" + id;
		var rowID4 = "metadataRow4_" + id;

		for (i = trows.length - 1; i >= 0; i--) {
			if (rowID == trows[i].id || rowID0 == trows[i].id || rowID1 == trows[i].id || rowID2 == trows[i].id || rowID3 == trows[i].id || rowID4 == trows[i].id) {
				elem.deleteRow(i);
			}
		}
	}
}

function addProposition(btn, index) {
	var plusRow = btn.parentNode.parentNode;
	var newProp = getPropositionRow(index, (plusRow.parentNode.rows.length - 1));
	plusRow.parentNode.insertBefore(newProp, plusRow);
}

function delProposition(btn) {
	var prop = btn.parentNode.parentNode;
	prop.parentNode.removeChild(prop);
}

function init() {
	self.focus();
}

function addFieldToInput(sel, inpNr) {
	if (sel && sel.selectedIndex >= 0 && sel.options[sel.selectedIndex].parentNode.nodeName.toLowerCase() == "optgroup") {
		var _inpElem = document.forms[0].elements["metadataImportFrom[" + inpNr + "]"];
		var _metaType = sel.options[sel.selectedIndex].parentNode.label.toLowerCase();
		var _str = _metaType + "/" + sel.options[sel.selectedIndex].value;
		_inpElem.value = _inpElem.value ? _inpElem.value + ("," + _str) : _str;
	}
	sel.selectedIndex = 0;
}


function addRow() {
	var elem = document.getElementById("metadataTable");
	var newID = (elem.rows.length) / 5;
	if (elem) {
		var newRow = document.createElement("TR");
		newRow.setAttribute("id", "metadataRow0_" + newID);
		cell = document.createElement("TD");
		cell.innerHTML = "<strong>" + g_l.tagname + "</strong>";
		cell.width = "210";
		cell.style.paddingTop = "12px";
		newRow.appendChild(cell);
		cell = document.createElement("TD");
		cell.innerHTML = "<strong>" + g_l.type + "</strong>";
		cell.width = "110";
		cell.style.paddingTop = "12px";
		cell.colspan = "2";
		newRow.appendChild(cell);
		elem.appendChild(newRow);

		newRow = document.createElement("TR");
		newRow.setAttribute("id", "metadataRow1_" + newID);
		cell = document.createElement("TD");
		cell.innerHTML = phpdata.tagInp.replace(/__we_new_id__/g, newID);
		cell.width = "210";
		newRow.appendChild(cell);
		cell = document.createElement("TD");
		cell.innerHTML = phpdata.typeSel.replace(/__we_new_id__/g, newID);
		cell.width = "200";
		newRow.appendChild(cell);
		cell = document.createElement("TD");
		cell.width = "30";
		cell.align = "right";
		cell.innerHTML = phpdata.trashButton.replace(/__we_new_id__/, newID);
		newRow.appendChild(cell);
		elem.appendChild(newRow);

		newRow = document.createElement("TR");
		newRow.setAttribute("id", "metadataRow2_" + newID);
		cell = document.createElement("TD");
		cell.style.paddingBottom = "6px";
		cell.innerHTML = '<div class="small">' + g_l.import_from + '</div>' + phpdata.importInp.replace(/__we_new_id__/, newID);
		newRow.appendChild(cell);
		cell = document.createElement("TD");
		cell.setAttribute("colspan", 2);
		cell.style.paddingBottom = "6px";
		cell.innerHTML = '<div class="small">' + g_l.fields + '</div>' + phpdata.fieldSel.replace(/__we_new_id__/g, newID);
		newRow.appendChild(cell);
		elem.appendChild(newRow);

		newRow = document.createElement("TR");
		newRow.setAttribute("id", "metadataRow3_" + newID);
		cell = document.createElement("TD");
		cell.style.paddingBottom = "1px";
		cell.innerHTML = '<div class="small">Vorschlagsliste</div>' + phpdata.modeSel.replace(/__we_new_id__/g, newID);
		newRow.appendChild(cell);
		cell = document.createElement("TD");
		cell.setAttribute("colspan", 2);
		cell.innerHTML = '<div class="small">&nbsp;</div>' + phpdata.csvCheck.replace(/__we_new_id__/g, newID) + '<div></div>';
		newRow.appendChild(cell);
		elem.appendChild(newRow);

		newRow = document.createElement("TR");
		newRow.setAttribute("id", "metadataRow4_" + newID);
		cell = document.createElement("TD");
		cell.colSpan = "3";
		cell.style.paddingBottom = "16px";
		cell.paddingRight = "5px";
		var nestedTable = document.createElement("TABLE");
		nestedTable.setAttribute("id", "proposalTable_" + newID);
		nestedTable.style.width = "100%";
		nestedTable.style.display = "none";
		//nestedTable.style.backgroundColor = "white";
		nestedTable.style.border = "1px solid gray";
		nestedTable.style.paddingTop = "8px";
		nestedTable.appendChild(getPropositionRow(newID, 0));
		nestedRow = document.createElement("TR");
		nestedCell = document.createElement("TD");
		nestedCell.width = "15%";
		nestedRow.appendChild(nestedCell);
		nestedCell = document.createElement("TD");
		nestedCell.innerHTML = phpdata.addPropositionBtn.replace(/__we_new_id__/, newID);
		nestedRow.appendChild(nestedCell);
		nestedCell = document.createElement("TD");
		nestedCell.width = "25";
		nestedRow.appendChild(nestedCell);
		nestedTable.appendChild(nestedRow);
		cell.appendChild(nestedTable);
		newRow.appendChild(cell);
		elem.appendChild(newRow);
	}
}

function getPropositionRow(indexMeta, indexProp) {
	var row = document.createElement("TR");
	var cell = document.createElement("TD");
	cell.width = "15%";
	row.appendChild(cell);

	cell = document.createElement("TD");
	cell.innerHTML = phpdata.proposalInp.replace(/__we_meta_id__/, indexMeta).replace(/__we_prop_id__/, indexProp);
	row.appendChild(cell);

	cell = document.createElement("TD");
	cell.width = "25";
	cell.innerHTML = phpdata.delPropositionBtn;
	row.appendChild(cell);

	return row;
}