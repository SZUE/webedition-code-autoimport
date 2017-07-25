/* global WE, top */

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
'use strict';


WE().util.jsWindow = function (opener, url, ref, w, h, openAtStartup, scroll, hideMenue, resizable, noPopupErrorMsg) {
	var foo_w = w;
	var foo_h = h;

	var screen_height = (((screen.height - 50) > screen.availHeight) ? screen.height - 50 : screen.availHeight) - 40;
	var screen_width = screen.availWidth - 10;

	w = Math.min(screen_width, w);
	h = Math.min(screen_height, h);

	this.opener = opener;
	this.referer = opener.top;
	this.url = url;
	this.ref = ref;
	this.x = Math.round((screen_width - w) / 2);
	this.y = Math.round((screen_height - h) / 2);
	this.w = w;
	this.h = h;
	this.scroll = (foo_w !== w || foo_h !== h) ? true : scroll;
	this.hideMenue = hideMenue;
	this.resizable = resizable;
	this.wind = null;
	if (WE(true)) {
		WE().layout.windows.push(this);
	}
	if (openAtStartup) {
		this.open(noPopupErrorMsg);
	}
};

WE().util.jsWindow.prototype = {
	open: function (noPopupErrorMsg) {
		var properties = (this.hideMenue ? "menubar=no," : "menubar=yes,") + (this.resizable ? "resizable=yes," : "resizable=no,") + ((this.scroll) ? "scrollbars=yes," : "scrollbars=no,") + "width=" + this.w + ",height=" + this.h + ",left=" + this.x + ",top=" + this.y;
		try {
			this.wind = this.opener.open(this.url, this.ref, properties);
//Bug mit chrome:
//		this.wind.moveTo(this.x,this.y);
			this.wind.focus();

		} catch (e) {
			if (noPopupErrorMsg !== undefined && noPopupErrorMsg.length) {
				if (!this.wind) {
					WE().util.showMessage(noPopupErrorMsg, WE().consts.message.WE_MESSAGE_ERROR, this.opener);
					//  disabled See Bug#1335
				}
			}
		}

	},
	close: function () {
		var wind;
		for (var i = 0; i < WE().layout.windows.length; i++) {
			wind = WE().layout.windows[i].wind;
			if (!wind || wind === this.wind || wind === undefined || wind.closed) {
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
		if (ref === undefined || ref === null) {
			while (WE().layout.windows.length) {
				WE().layout.windows.pop().close();
			}
		} else {
			var refObj;
			for (var i = 0; i < WE().layout.windows.length; i++) {
				if (!WE().layout.windows[i] || WE().layout.windows[i] === undefined || WE().layout.windows[i].wind === undefined) {
					//remove from window list
					WE().layout.windows.splice(i, 1);
					//reset i
					i = -1;
					continue;
				}
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

//used for we:userInput
function open_wysiwyg_win() {
	var url = "/webEdition/we_cmd_frontend.php?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}

	/*if (window.screen) {
	 h = ((screen.height - 100) > screen.availHeight) ? screen.height - 100 : screen.availHeight;
	 w = screen.availWidth;
	 }*/
	var wyw = Math.max(arguments[2], arguments[9]);
	wyw = wyw ? wyw : 800;
	var wyh = parseInt(arguments[3]) + parseInt(arguments[10]);
	wyh = wyh ? wyh : 600;
	if (window.screen) {
		var screen_height = ((screen.height - 50) > screen.availHeight) ? screen.height - 50 : screen.availHeight;
		screen_height = screen_height - 40;
		var screen_width = screen.availWidth - 10;
		wyw = Math.min(screen_width, wyw);
		wyh = Math.min(screen_height, wyh);
	}
// set new width & height;

	url = url.replace(/we_cmd\[2\]=[^&]+/, "we_cmd[2]=" + wyw).replace(/we_cmd\[3\]=[^&]+/, "we_cmd[3]=" + (wyh - arguments[10]));
	new (WE().util.jsWindow)(window, url, "we_wysiwygWin", Math.max(220, wyw), Math.max(100, wyh + 60), true, false, true);
}
