/**
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
 *
 *  * This source is part of webEdition CMS. webEdition CMS is
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

windows = [];

function jsWindow(url, ref, x, y, w, h, openAtStartup, scroll, hideMenue, resizable, noPopupErrorMsg, noPopupLocation) {
	var foo_w = w;
	var foo_h = h;

	if (window.screen) {
		var screen_height = ((screen.height - 50) > screen.availHeight) ? screen.height - 50 : screen.availHeight;
		screen_height = screen_height - 40;
		var screen_width = screen.availWidth - 10;
		w = Math.min(screen_width, w);
		h = Math.min(screen_height, h);
		x = (x == -1 ? Math.round((screen_width - w) / 2) : x);
		y = (y == -1 ? Math.round((screen_height - h) / 2) : y);
	}

	this.url = url;
	this.ref = ref;
	this.x = x;
	this.y = y;
	this.w = w;
	this.h = h;
	this.scroll = (foo_w != w || foo_h != h) ? true : scroll;
	this.hideMenue = hideMenue;
	this.resizable = resizable;
	this.wind = null;
	windows.push(this);
	WE().layout.windows.push(this);
	if (openAtStartup) {
		this.open(noPopupErrorMsg, noPopupLocation);
	}
}

jsWindow.prototype.open = function (noPopupErrorMsg, noPopupLocation) {
	var properties = (this.hideMenue ? "menubar=no," : "menubar=yes,") + (this.resizable ? "resizable=yes," : "resizable=no,") + ((this.scroll) ? "scrollbars=yes," : "scrollbars=no,") + "width=" + this.w + ",height=" + this.h + ",left=" + this.x + ",top=" + this.y;
	try {
		this.wind = window.open(this.url, this.ref, properties);
//Bug mit chrome:
//		this.wind.moveTo(this.x,this.y);
		this.wind.focus();

	} catch (e) {
		if (noPopupErrorMsg !== undefined && noPopupErrorMsg.length) {
			if (!this.wind) {
				top.we_showMessage(noPopupErrorMsg, WE().consts.message.WE_MESSAGE_ERROR, window);
				//  disabled See Bug#1335
				if (noPopupLocation !== undefined) {
					//document.location = noPopupLocation;
				}
			}
		}
	}

}

jsWindow.prototype.close = function () {
	if (!this.wind.closed) {
		this.wind.close();
	}
};

jsWindow.prototype.closeByName = function (name) {
	for (var i = 0; i < windows.length; i++) {
		if (windows[i].ref == name) {
			windows[i].close();
			windows.splice(i, 1);
		}
	}
}

//FIXME: since we need one function to close all dependent windows, we can't currently use only WE().layout.windows global

jsWindow.prototype.closeAll = function (all) {
	if (all) {
		while (WE().layout.windows.length) {
			WE().layout.windows.pop().close();
		}
	} else {
		while (windows.length) {
			windows.pop().close();
		}
	}
}

jsWindow.prototype.find = function (name) {
	for (var i = 0; i < windows.length; i++) {
		if (windows[i].ref == name) {
			return windows[i].wind;
		}
	}
	return undefined;
}

jsWindow.prototype.focus = function (name) {
	var wind = jsWindow.prototype.find(name);
	if (wind !== undefined) {
		wind.focus();
		return true;
	}
	return false;
}
