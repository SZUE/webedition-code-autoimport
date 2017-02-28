/* global WE, top */

/**
 * webEdition CMS
 *
 * webEdition CMS
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
'use strict';

const TAB_ADMIN1 = 0;
const TAB_ADMIN2 = 1;
const TAB_ADMIN3 = 2;

var shp = WE().util.getDynamicVar(document, 'loadVarShop', 'data-shop');


function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "openOrder":
			//TODO: check this adress: mit oder ohne tree? Bisher: left
			if (top.content.doClick) {
				top.content.doClick(args[1], args[2], args[3]);//TODO: check this adress
			}
			break;
		default:
			// not needed yet
			top.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function setTab(tab) {
	switch (tab) {
		case TAB_ADMIN1:
			parent.edbody.document.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edit_shop_article_extend&typ=document";
			break;
		case TAB_ADMIN2:
			parent.edbody.document.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edit_shop_article_extend&typ=object&ViewClass=" + shp.classid;
			break;

		case TAB_ADMIN3:
			parent.edbody.document.location = WE().consts.dirs.WE_MODULES_DIR + "shop/edit_shop_revenueTop.php?ViewYear=" + shp.yearTrans;
			// treeData.yearshop
			break;

	}
}