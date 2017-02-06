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

String.prototype.trim = function () {
	return this.replace(/^\s+|\s+$/g, "");
};

function addRow(rowNr) {

	var _table = document.getElementById("filterTable");
	if (_table) {
		var _numRows = _table.rows.length;

		if (rowNr === undefined) {
			rowNr = _numRows;
		}

		var _newRow = _table.insertRow(rowNr);

		_numRows++;

		var _cell = document.createElement("TD");
		_cell.style.paddingTop = "4px";
		_cell.style.width = "64px";

		if (rowNr > 0) {
		_cell.innerHTML = '<select onchange="wecf_logic_changed(this);" class="weSelect logicInput">' + filter.logic + '</select>';
		}
		_newRow.appendChild(_cell);

		_cell = document.createElement("TD");
		_cell.style.paddingTop = "4px";
		_cell.innerHTML = '<select onchange="wecf_hot();" class="weSelect leftInput">' + filter.args + '</select>';
		_newRow.appendChild(_cell);

		_cell = document.createElement("TD");
		_cell.style.paddingTop = "4px";
		_cell.innerHTML = '<select onchange="wecf_hot();" class="weSelect middleInput">' + filter.op + '</select>';
		_newRow.appendChild(_cell);

		_cell = document.createElement("TD");
		_cell.style.paddingTop = "4px";
		_cell.innerHTML = '<input onchange="wecf_hot();" type="text" class="defaultfont rightInput" />';
		_newRow.appendChild(_cell);

		_cell = document.createElement("TD");
		_cell.style.paddingTop = "4px";
		_newRow.appendChild(_cell);

		_cell = document.createElement("TD");
		_cell.style.paddingTop = "4px";
		_cell.style.paddingLeft = "5px";
		_newRow.appendChild(_cell);

		updateFilterTable();
		wecf_hot();
	}
}

function delRow(rowNr) {
	var _table = document.getElementById("filterTable");
	if (_table) {
		var _trows = _table.rows;
		var _rowID = "filterRow_" + rowNr;

		for (var i = 0; i < _trows.length; i++) {
			if (_rowID == _trows[i].id) {
				_table.deleteRow(i);
				break;
			}
		}

		updateFilterTable();

	}
	wecf_hot();
}

function updateFilterTable() {

	var _table = document.getElementById("filterTable");
	if (_table) {
		var _row;
		var _numRows = _table.rows.length;

		// now loop through all rows and set names and buttons
		for (var i = 0; i < _numRows; i++) {

			_row = _table.rows[i];

			_row.id = "filterRow_" + i;

			_cell = _row.cells[0];  // logic
			if (_cell.innerHTML.trim().toLowerCase().substring(0, 4) == "<img" && i > 0) {
				_cell.innerHTML = '<select onchange="wecf_logic_changed(this);" class="weSelect defaultfont" name="filterLogic_' + i + '">' + filter.logic + '</select>';
			}

			if (i > 0) {
				_cell.firstChild.name = "filterLogic_" + i;
			}

			_cell = _row.cells[1];  // field
			_cell.firstChild.name = "filterSelect_" + i;

			_cell = _row.cells[2];  // operator
			_cell.firstChild.name = "filterOperation_" + i;

			_cell = _row.cells[3];  // value
			_cell.firstChild.name = "filterValue_" + i;

			_cell = _row.cells[4];  // plus
			_cell.innerHTML = buttons.add.replace(/__CNT__/, i + 1);

			_cell = _row.cells[5];  // trash
			if (i === 0) {
				_cell.style.width = "25px";
			}
			_cell.innerHTML = (i === 0 ? '<span></span>' : buttons.trash.replace(/__CNT__/, i));


			if (i > 0) {
				_cell = _row.cells[0];
				var elem = _cell.firstChild;

				var _logic = elem.selectedIndex!==undefined?elem.options[elem.selectedIndex].value:"OR";
				var _prevRow = _table.rows[i - 1];

				for (var n = 0; n < _prevRow.cells.length; n++) {
					_prevRow.cells[n].style.paddingBottom = (_logic == "OR") ? "10px" : "0";
				}
				for (n = 0; n < _row.cells.length; n++) {
					_row.cells[n].style.paddingTop = (_logic == "OR") ? "10px" : "0";
					_row.cells[n].style.borderTop = (_logic == "OR") ? "1px solid grey" : "0";
				}
			}
		}
	}
}
