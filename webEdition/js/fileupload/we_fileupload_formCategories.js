/* global top, WE */

/**
 * webEdition CMS
 *
 * $Rev: 13200 $
 * $Author: mokraemer $
 * $Date: 2016-12-28 01:33:14 +0100 (Mi, 28 Dez 2016) $
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

var preview = WE().util.getDynamicVar(document, 'loadVarFileupload_ui_preview', 'data-preview');
var categories_edit = new (WE().util.multi_edit)("categoriesDiv", window, 0, preview.delButton, preview.categoriesDivSize, false);
categories_edit.addVariant();

function removeAllCats() {
	if (categories_edit.itemCount > 0) {
		while (categories_edit.itemCount > 0) {
			categories_edit.delItem(categories_edit.itemCount);
		}
		categories_edit.showVariant(0);
		selectCategories();
	}
}

function addCat(paths) {
	for (var i = 0; i < paths.length; i++) {
		if (paths[i] !== "") {
			categories_edit.addItem();
			categories_edit.setItem(0, (categories_edit.itemCount - 1), paths[i]);
		}
	}
	categories_edit.showVariant(0);
	//selectCategories();
}

function selectCategories() {
	var cats = [];
	for (var i = 0; i < categories_edit.itemCount; i++) {
		cats.push(categories_edit.form.elements[categories_edit.name + "_variant0_" + categories_edit.name + "_item" + i].value);
	}
	categories_edit.form.fu_doc_categories.value = cats.join(",");
}

function init() {
	for (var cat in preview.variantCats) {
		categories_edit.addItem();
		categories_edit.setItem(0, (categories_edit.itemCount - 1), preview.variantCats[cat]);
	}
	categories_edit.showVariant(0);
}