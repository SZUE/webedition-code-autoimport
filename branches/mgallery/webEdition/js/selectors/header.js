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

function disableRootDirButs() {
	switch_button_state("root_dir", "root_dir_enabled", "disabled");
	switch_button_state("btn_fs_back", "back_enabled", "disabled", "image");
	rootDirButsState = 0;
}
function enableRootDirButs() {
	switch_button_state("root_dir", "root_dir_enabled", "enabled");
	switch_button_state("btn_fs_back", "back_enabled", "enabled", "image");
	rootDirButsState = 1;
}
function disableNewFolderBut() {
	switch_button_state("btn_new_dir", "new_directory_enabled", "disabled", "image");
	makefolderState = 0;
}
function enableNewFolderBut() {
	switch_button_state("btn_new_dir", "new_directory_enabled", "enabled", "image");
	makefolderState = 1;
}
function disableNewBut() {
	switch_button_state("btn_new_dir", "new_directory_enabled", "disabled", "image");
	switch_button_state("btn_add_cat", "newCategorie_enabled", "disabled", "image");
}
function disableDelBut() {
	switch_button_state("btn_function_trash", "btn_function_trash_enabled", "disabled", "image");
	changeCatState = 0;
}

function enableNewBut() {
	if (top.options.userCanEditCat) {
		switch_button_state("btn_new_dir", "new_directory_enabled", "enabled", "image");
		switch_button_state("btn_add_cat", "newCategorie_enabled", "enabled", "image");
	}
}
function enableDelBut() {
	if (top.options.userCanEditCat) {
		switch_button_state("btn_function_trash", "btn_function_trash_enabled", "enabled", "image");
		changeCatState = 1;
	}
}

function clearOptions() {
	var a = document.we_form.elements.lookin;
	for (var i = a.options.length - 1; i >= 0; i--) {
		a.options[i] = null;
	}
}
function addOption(txt, id) {
	var a = document.we_form.elements.lookin;
	a.options[a.options.length] = new Option(txt, id);
	a.selectedIndex = (a.options.length > 0 ?
					a.options.length - 1 :
					0);

}
function selectIt() {
	var a = document.we_form.elements.lookin;
	a.selectedIndex = a.options.length - 1;
}

function setview(view) {
	top.options.view = view;
	var zoom = top.fsfooter.document.getElementsByName("zoom")[0];
	switch (view) {
		case 'list':
			zoom.value = 100;
			if (zoom.onchange) {
				zoom.onchange();
			}
			zoom.disabled = true;
			zoom.style.display = "none";
			break;
		case 'icons':
			zoom.disabled = false;
			zoom.style.display = "inline";
			break;
	}
	top.fsheader.document.getElementById('list').style.display = (view == 'list' ? "none" : "table-cell");
	top.fsheader.document.getElementById('icons').style.display = (view == 'icons' ? "none" : "table-cell");

	top.writeBody(top.fsbody.document.body);
}