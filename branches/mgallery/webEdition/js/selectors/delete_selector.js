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

function doClick(id, ct) {
	if (ct == 1) {
		if (wasdblclick) {
			setDir(id);
			setTimeout("wasdblclick=0;", 400);
		}
	} else if (fsbody.shiftpressed) {
		var oldid = currentID;
		var currendPos = getPositionByID(id);
		var firstSelected = getFirstSelected();

		if (currendPos > firstSelected) {
			selectFilesFrom(firstSelected, currendPos);
		} else if (currendPos < firstSelected) {
			selectFilesFrom(currendPos, firstSelected);
		} else {
			selectFile(id);
		}
		currentID = oldid;

	} else if (!fsbody.ctrlpressed) {
		selectFile(id);
	} else if (isFileSelected(id)) {
		unselectFile(id);
	} else {
		selectFile(id);
	}
	if (fsbody.ctrlpressed) {
		fsbody.ctrlpressed = 0;
	}
	if (fsbody.shiftpressed) {
		fsbody.shiftpressed = 0;
	}
}

function setDir(id) {
	e = getEntry(id);
	if (id === 0) {
		e.text = "";
	}
	currentID = id;
	currentDir = id;
	currentPath = e.path;
	top.fsfooter.document.we_form.fname.value = e.text;
	if (id) {
		top.fsfooter.enableDelBut();
	}
	top.fscmd.location.replace(top.queryString(queryType.CMD, id));
}

function selectFile(id) {
	if (id) {
		e = getEntry(id);

		if (top.fsfooter.document.we_form.fname.value != e.text &&
						top.fsfooter.document.we_form.fname.value.indexOf(e.text + ",") == -1 &&
						top.fsfooter.document.we_form.fname.value.indexOf("," + e.text + ",") == -1 &&
						top.fsfooter.document.we_form.fname.value.indexOf("," + e.text + ",") == -1) {

			top.fsfooter.document.we_form.fname.value = top.fsfooter.document.we_form.fname.value ?
							(top.fsfooter.document.we_form.fname.value + "," + e.text) :
							e.text;
		}
		if (top.fsbody.document.getElementById("line_" + id))
			top.fsbody.document.getElementById("line_" + id).style.backgroundColor = "#DFE9F5";
		currentPath = e.path;
		currentID = id;
		if (id)
			top.fsfooter.enableDelBut();
		we_editDelID = 0;
	} else {
		top.fsfooter.document.we_form.fname.value = "";
		currentPath = "";
		we_editDelID = 0;
	}
}

function unselectAllFiles(){
	for	(var i=0;i < entries.length; i++){
		top.fsbody.document.getElementById("line_"+entries[i].ID).style.backgroundColor="white";
	}
	top.fsfooter.document.we_form.fname.value = "";
	top.fsfooter.disableDelBut();
}