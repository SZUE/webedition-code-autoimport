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
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

//FIXME: compare & unite all _tree.js files

function drawEintraege() {
	fr = top.content.tree;
	fr.innerHTML = '<div id="treetable" class="tree"><nobr>' +
					"<tr><td class=\"tree\"><nobr><a href=javascript:// onclick=\"doYearClick(" + top.yearshop + ");return true;\" title=\"" + g_l.treeYearClick + "\" >" + g_l.treeYear + ": <strong>" + top.yearshop + " </strong></a> <br/>" +
					zeichne(0, "") +
					"</nobr></div>" +
					"</body></html>";
}

function zeichne(startEntry, zweigEintrag) {
	var nf = search(startEntry);
	ret = "";
	for (var ai = 1; ai <= nf.len; ai++) {
		ret += zweigEintrag;
		if (nf[ai].typ === 'shop') {
			ret += '<span class="treeKreuz ' + (ai == nf.len ? "kreuzungend" : "kreuzung") + '"></span>';
			if (perm_EDIT_SHOP_ORDER) { // make  in tree clickable
				if (nf[ai].id !== -1) {
					ret += "<a href=\"javascript://\" onclick=\"doClick(" + nf[ai].id + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">";
				}
			}
			ret += WE().util.getTreeIcon('we/shop') +
							(perm_EDIT_SHOP_ORDER ?
											"</a>" :
											"") +
							(perm_EDIT_SHOP_ORDER ? // make orders in tree clickable
											"<a href=\"javascript://\" onclick=\"doClick(" + nf[ai].id + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">" :
											"") +
							//changed for #6786
							"<span style='" + nf[ai].st + "'>" + nf[ai].text + "</span>" +
							(perm_EDIT_SHOP_ORDER ?
											"</a>" : ""
											) +
							"<br/>";
		} else {
			var newAst = zweigEintrag;

			ret += "<a href=\"javascript:top.content.openClose('" + nf[ai].id + "',1)\"><span class='treeKreuz fa-stack " + (ai == nf.len ? "kreuzungend" : "kreuzung") + "'><i class='fa fa-square fa-stack-1x we-color'></i><i class='fa fa-" + (nf[ai].open ? "minus" : "plus") + "-square-o fa-stack-1x'></i></span></a>";
			ret += (perm_EDIT_SHOP_ORDER ?
							"<a href=\"javascript://\" onclick=\"doFolderClick(" + nf[ai].id + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">" :
							"") +
							WE().util.getTreeIcon('folder', nf[ai].open) +
							(perm_EDIT_SHOP_ORDER ?
											"</a>" +
											// make the month in tree clickable
											"<a href=\"javascript://\" onclick=\"doFolderClick(" + nf[ai].id + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">" :
											"") +
							(parseInt(nf[ai].published) ? " <b>" : "") + nf[ai].text + (parseInt(nf[ai].published) ? " </b>" : "") +
							(perm_EDIT_SHOP_ORDER ?
											"</a>" : "") +
							"<br/>";
			if (nf[ai].open) {
				newAst += (ai === nf.len ?
								'<span class="treeKreuz"></span>' :
								'<span class="strich treeKreuz "></span>');
				ret += zeichne(nf[ai].id, newAst);
			}
		}
	}
	return ret;
}

function openClose(id, status) {
	var eintragsIndex = indexOfEntry(id);
	treeData[eintragsIndex].open = status;
	drawEintraege();
}

function indexOfEntry(id) {
	for (var ai = 1; ai <= treeData.len; ai++) {
		if ((treeData[ai].typ === 'root') || (treeData[ai].typ === 'folder')) {
			if (treeData[ai].id == id) {
				return ai;
			}
		}
	}
	return -1;
}

function search(eintrag) {
	var nf = new container();
	for (var ai = 1; ai <= treeData.len; ai++) {
		if (treeData[ai].parentid == eintrag) {
			nf.add(treeData[ai]);
		}
	}
	return nf;
}

function rootEntry(id, text, rootstat) {
	return new node({
		id: id,
		text: text,
		loaded: true,
		typ: 'root',
		rootstat: rootstat,
	});
}

function start() {
	loadData();
	drawEintraege();
}

function doClick(id, ct, table) {
	top.content.editor.location = WE().consts.dirs.WE_MODULES_DIR + 'shop/edit_shop_frameset.php?pnt=editor&bid=' + id;
}
function doFolderClick(id, ct, table) {
	top.content.editor.location = WE().consts.dirs.WE_MODULES_DIR + 'shop/edit_shop_frameset.php?pnt=editor&mid=' + id;
}
function doYearClick(yearView) {
	top.content.editor.location = WE().consts.dirs.WE_MODULES_DIR + 'shop/edit_shop_frameset.php?pnt=editor&ViewYear=' + yearView;
}
