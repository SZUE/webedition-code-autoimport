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

if (typeof (window.top.pb_vars) === 'undefined') {
	var imgDir = "/webEdition/images";
	var colorCont = "#ffffff";
	var colorUnloaded = "#e6e8fa";
	var bgImgUnloaded = "balken_bg.gif";
	var colorLoaded = "000080";
	var bgImgLoaded = "balken.gif";
} else {
	window.pb_vars = window.top.pb_vars;
	document.write(pb_vars);
}

var pb_style = '<style type="text/css">' +
				'#divCont {' +
				'position:absolute; left:0px; top:0px;' +
				'width:150; height:98; clip:rect(0px 150 150 0px);' +
				'background-color:' + colorCont + ';' +
				'layer-background-color:' + colorCont + ';' +
				'}' +
				'#divLoaded {' +
				'position:absolute;' +
				'layer-background-color:' + colorUnloaded + ';' +
				'background-color:' + colorUnloaded + ';' +
				'background-image:url(' + imgDir + '/' + bgImgUnloaded + ');' +
				'layer-background-image:url(' + imgDir + '/' + bgImgUnloaded + ');' +
				'}' +
				'#divUnloaded {' +
				'position:absolute; left:0px; top:0px;' +
				'layer-background-color:' + colorLoaded + ';' +
				'background-color:' + colorLoaded + ';' +
				'background-image:url(' + imgDir + '/' + bgImgLoaded + ');' +
				'layer-background-image:url(' + imgDir + '/' + bgImgLoaded + ');' +
				'}' +
				'#divText {' +
				'position:absolute; background-color:transparent; font-family:Verdana;' +
				'color:#006699; font-size:9px; font-weight:bold;' +
				'}' +
				'</style>';

document.write(pb_style);

function bw_check() {
	this.ver = navigator.appVersion;
	this.agent = navigator.userAgent;
	this.dom = document.getElementById ? 1 : 0;
	return this;
}
bw = new bw_check();

var px ='px';

function pb_scale(maximum) {
	this.maximum = maximum;
	this.current = 0;
	this.loaderWidth = 100;
	this.loaderHeight = 10;
}

function pb_docsize() {
	this.x = 0;
	this.x2 = innerWidth || 0;
	this.y = 0;
	this.y2 = innerHeight || 0;
	if (!this.x2 || !this.y2)
		return;
	this.x50 = this.x2 / 2;
	this.y50 = this.y2 / 2;
	return this;
}

function pb_object(obj, nest) {
	nest = (!nest) ? '' : 'document.' + nest + '.';
	this.evnt = bw.dom ? document.getElementById(obj) : 0;
	this.css = bw.dom || this.evnt;
	this.ref = this.css;
	this.w = this.evnt.offsetWidth || this.css.clip.width ||
					this.ref.width || this.css.pixelWidth || 0;
	return this;
}

pb_object.prototype.pb_move = function (x, y) {
	this.x = x;
	this.y = y;
	this.css.left = x + px;
	this.css.top = y + px;
};

pb_object.prototype.pb_clip = function (t, r, b, l, setwidth) {
	this.ct = t;
	this.cr = r;
	this.cb = b;
	this.cl = l;

	if (t < 0)
		t = 0;
	if (r < 0)
		r = 0;
	if (b < 0)
		b = 0;
	if (b < 0)
		b = 0;
	this.css.clip = 'rect(' + t + 'px ' + r + 'px ' + b + 'px ' + l + 'px)';
	if (setwidth) {
		this.css.pixelWidth = r;
		this.css.pixelHeight = b;
		this.css.width = r + px;
		this.css.height = b + px;
	}

};

pb_object.prototype.pb_write = function (text, startHTML, endHTML) {
	this.evnt.innerHTML = text;
};

var oLoad2;

function pb_init(maximum, xPos, yPos) {
	scale = new pb_scale(maximum);
	oLoadCont = new pb_object('divCont');
	oLoad = new pb_object('divLoaded', 'divCont');
	oLoad2 = new pb_object('divUnloaded', 'divCont.document.divLoaded');
	oLoadText = new pb_object('divText');

	hsp = 7;
	vsp = 1;
	if (xPos != -1 && yPos != -1) {
		oLoad.pb_move(xPos, yPos + 2);
		oLoadText.pb_move(xPos + scale.loaderWidth + hsp, yPos);
	} else {
		page = new pb_docsize();
		oLoad.pb_move(page.x50 - scale.loaderWidth / 2, page.y50 - 20);
		oLoadText.pb_move(page.x50 - scale.loaderWidth / 2 + scale.loaderWidth + hsp, page.y50 - 20 - vsp);
	}
	oLoad.pb_clip(0, scale.loaderWidth, scale.loaderHeight, 0, 1);
	oLoad2.percent = scale.loaderWidth / scale.maximum;
	oLoadText.pb_write('0%');
}

function pb_increment() {
	scale.current++;
	if (oLoad2) {
		oLoad2.pb_clip(0, oLoad2.percent * scale.current, 40, 0, 1);
		oLoadText.pb_write(Math.floor(oLoad2.percent * scale.current) + '%');
	}
	if (scale.current >= scale.maximum)
		setTimeout(pb_destroy, 500);
}

function pb_destroy() {
	oLoadCont.css.visibility = 'hidden';
	oLoadCont = null;
	oLoad1 = null;
	oLoad2 = null;
	oLoadText = null;
	scale = null;
}

function pb_display() {
	scale.current++;
	if (oLoad2) {
		oLoad2.pb_clip(0, oLoad2.percent * scale.current, 40, 0, 1);
		oLoadText.pb_write(Math.floor(oLoad2.percent * scale.current) + '%');
	}
	if (scale.current <= scale.maximum) {
		setTimeout(pb_display, 200);
	} else {
		oLoadCont.css.visibility = 'hidden';
	}
}
