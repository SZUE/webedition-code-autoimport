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

function drawTree() {
	top.content.document.getElementById("treetable").innerHTML = "<a href=javascript:// onclick=\"doYearClick(" + top.yearshop + ");return true;\" title=\"" + WE().consts.g_l.shop.tree.treeYearClick + "\" >" + WE().consts.g_l.shop.tree.treeYear + ": <strong>" + top.yearshop + " </strong></a><br/>" + treeData.draw(0, "");
}

container.prototype.drawShop=function (nf, ai, zweigEintrag){
	var perm=WE().util.hasPerm("EDIT_SHOP_ORDER");
			var ret= '<span class="treeKreuz ' + (ai == nf.len ? "kreuzungend" : "kreuzung") + '"></span>';
			if (perm) { // make  in tree clickable
				if (nf[ai].id !== -1) {
					ret += "<a href=\"javascript://\" onclick=\"doClick(" + nf[ai].id + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">";
				}
			}
			ret += WE().util.getTreeIcon('we/shop') +
							(perm ?
											"</a>" :
											"") +
							(perm ? // make orders in tree clickable
											"<a href=\"javascript://\" onclick=\"doClick(" + nf[ai].id + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">" :
											"") +
							//changed for #6786
							"<span style='" + nf[ai].st + "'>" + nf[ai].text + "</span>" +
							(perm ?
											"</a>" : ""
											) +
							"<br/>";
	return  ret;
};

container.prototype.drawFolder=function (nf, ai, zweigEintrag){
	var newAst = zweigEintrag;
	var perm = WE().util.hasPerm("EDIT_SHOP_ORDER");
			var ret= "<a href=\"javascript:top.content.treeData.openClose('" + nf[ai].id + "',1)\"><span class='treeKreuz fa-stack " + (ai == nf.len ? "kreuzungend" : "kreuzung") + "'><i class='fa fa-square fa-stack-1x we-color'></i><i class='fa fa-" + (nf[ai].open ? "minus" : "plus") + "-square-o fa-stack-1x'></i></span></a>"+
							(perm ?
							"<a href=\"javascript://\" onclick=\"doFolderClick(" + nf[ai].id + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">" :
							"") +
							WE().util.getTreeIcon('folder', nf[ai].open) +
							(perm ?
											"</a>" +
											// make the month in tree clickable
											"<a href=\"javascript://\" onclick=\"doFolderClick(" + nf[ai].id + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">" :
											"") +
							(parseInt(nf[ai].published) ? " <b>" : "") + nf[ai].text + (parseInt(nf[ai].published) ? " </b>" : "") +
							(perm ?
											"</a>" : "") +
							"<br/>";
			if (nf[ai].open) {
				newAst += (ai === nf.len ?
								'<span class="treeKreuz"></span>' :
								'<span class="strich treeKreuz "></span>');
				ret += this.draw(nf[ai].id, newAst);
			}
			return ret;
};

container.prototype.openClose = function(id, status) {
	var eintragsIndex = treeData.indexOfEntry(id);
	treeData[eintragsIndex].open = status;
	drawTree();
};

container.prototype.indexOfEntry=function (id) {
	for (var ai = 1; ai <= treeData.len; ai++) {
		switch(this[ai].typ){
			case 'root':
			case 'folder':
			if (this[ai].id == id) {
				return ai;
			}
		}
	}
	return -1;
};

container.prototype.search = function (eintrag) {
	var nf = new container();
	for (var ai = 1; ai <= this.len; ai++) {
		if (this[ai].parentid == eintrag) {
			nf.add(this[ai]);
		}
	}
	return nf;
};

function startTree() {
	loadData();
	drawTree();
}

function doClick(id, ct, table) {
	top.content.editor.location = WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=shop&pnt=editor&bid=' + id;
}
function doFolderClick(id, ct, table) {
	top.content.editor.location = WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=shop&pnt=editor&mid=' + id;
}
function doYearClick(yearView) {
	top.content.editor.location = WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=shop&pnt=editor&ViewYear=' + yearView;
}
