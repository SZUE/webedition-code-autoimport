/* global WE */

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


function jsWindow(opener, url, ref, x, y, w, h, openAtStartup, scroll, hideMenue, resizable, noPopupErrorMsg, noPopupLocation) {
	var foo_w = w;
	var foo_h = h;

	if (window.screen) {
		var screen_height = ((screen.height - 50) > screen.availHeight) ? screen.height - 50 : screen.availHeight;
		screen_height = screen_height - 40;
		var screen_width = screen.availWidth - 10;
		w = Math.min(screen_width, w);
		h = Math.min(screen_height, h);
		x = (x === -1 ? Math.round((screen_width - w) / 2) : x);
		y = (y === -1 ? Math.round((screen_height - h) / 2) : y);
	}
	this.opener = opener;
	this.referer = opener.top;
	this.url = url;
	this.ref = ref;
	this.x = x;
	this.y = y;
	this.w = w;
	this.h = h;
	this.scroll = (foo_w !== w || foo_h !== h) ? true : scroll;
	this.hideMenue = hideMenue;
	this.resizable = resizable;
	this.wind = null;
	if (WE()) {
		WE().layout.windows.push(this);
	}
	if (openAtStartup) {
		this.open(noPopupErrorMsg, noPopupLocation);
	}
}

jsWindow.prototype = {
	open: function (noPopupErrorMsg, noPopupLocation) {
		var properties = (this.hideMenue ? "menubar=no," : "menubar=yes,") + (this.resizable ? "resizable=yes," : "resizable=no,") + ((this.scroll) ? "scrollbars=yes," : "scrollbars=no,") + "width=" + this.w + ",height=" + this.h + ",left=" + this.x + ",top=" + this.y;
		try {
			this.wind = this.opener.open(this.url, this.ref, properties);
//Bug mit chrome:
//		this.wind.moveTo(this.x,this.y);
			this.wind.focus();

		} catch (e) {
			if (noPopupErrorMsg !== undefined && noPopupErrorMsg.length) {
				if (!this.wind) {
					top.we_showMessage(noPopupErrorMsg, WE().consts.message.WE_MESSAGE_ERROR, this.opener);
					//  disabled See Bug#1335
					if (noPopupLocation !== undefined) {
						//document.location = noPopupLocation;
					}
				}
			}
		}

	},
	close: function () {
		var wind;
		for (var i = 0; i < WE().layout.windows.length; i++) {
			wind = WE().layout.windows[i].wind;
			if (wind === this.wind || wind.closed) {
				WE().layout.windows.splice(i, 1);
				i--;
			}
		}
		if (!this.wind.closed) {
			this.wind.close();
		}
	},
	closeByName: function (name) {
		var obj;
		while ((obj = this.find(name))) {
			//closes dependend windows
			this.closeAll(obj.wind);
		}
	},
//FIXME:this function should be called instead of a top.close
	closeAll: function (ref) {
		if (ref === undefined) {
			while (WE().layout.windows.length) {
				WE().layout.windows.pop().close();
			}
		} else {
			var refObj;
			for (var i = 0; i < WE().layout.windows.length; i++) {
				if (WE().layout.windows[i].wind === ref) {
					refObj = WE().layout.windows[i];
				}
				if (WE().layout.windows[i].referer === ref) {
					var obj = WE().layout.windows[i];
					//remove from window list
					WE().layout.windows.splice(i, 1);
					//close all windows from this window first
					this.closeAll(obj.wind);
					obj.close();
					//reset i
					i = -1;
				}
			}
			if (refObj) {
				refObj.close();
			} else if (!ref.closed) {
				//if we didn't find the window, just close it
				ref.close();
			}
		}
	},
	find: function (name) {
		for (var i = 0; i < WE().layout.windows.length; i++) {
			if (WE().layout.windows[i].ref === name) {
				return WE().layout.windows[i].wind;
			}
		}
		return undefined;
	},
	focus: function (name) {
		var wind = this.find(name);
		if (wind !== undefined) {
			wind.focus();
			return true;
		}
		return false;
	}
};
