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

var TAB_DISABLED = 0;
var TAB_NORMAL = 1;
var TAB_ACTIVE = 2;
var we_name_z = 0;

function we_tab_write() {
	if (this.svg) {
		document.write(
						'<div >' +
						(this.state != TAB_DISABLED ?
										'<a style="text-decoration:none;" href="' + this.href + '" onclick="we_tabs[' + this.z + '].setState(TAB_ACTIVE,false,we_tabs);' + this.js + ';this.blur();">' : '') +
						'<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" style="display:none;" id="' + this.nameNorm + '" width="' + this.width + '" height="' + this.height + '" externalResourcesRequired="false">' +
						this.normSrc +
						'</svg>' +
						'<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" style="display:none;" id="' + this.nameActive + '" width="' + this.width + '" height="' + this.height + '" externalResourcesRequired="false">' +
						this.activeSrc +
						'</svg>' +
						'<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" style="display:none;" id="' + this.nameDisabled + '" width="' + this.width + '" height="' + this.height + '" externalResourcesRequired="false">' +
						this.disabledSrc +
						'</svg>' +
						(this.state != TAB_DISABLED ? '</a>' : '') + '</div>');
	} else {
		document.write(((this.state != TAB_DISABLED) ? '<a href="' + this.href + '" onclick="we_tabs[' + this.z + '].setState(TAB_ACTIVE,false,we_tabs);' + this.js + ';this.blur();">' : '') + '<img src="' + this.src + '" width="' + this.width + '" height="' + this.height + '" border="0" name="' + this.name + '">' + ((this.state != TAB_DISABLED) ? '</a>' : ''));
	}
}

function we_getTabStateImg() {
	switch (this.state) {
		case TAB_DISABLED:
			return this.nameDisabled;
			break;
		case TAB_ACTIVE:
			return this.nameActive;
		default:
			return this.nameNorm;
	}
}

function we_tab_setState(state, init, objects) {
	this.state = state;

	if (objects) {
		for (var i = 0; i < objects.length; i++) {
			if (objects[i] !== null) {
				if (this.svg) {
					if (this != objects[i] && objects[i].state != TAB_DISABLED) {
						objects[i].state = TAB_NORMAL;
					}
					document.getElementById(objects[i].nameNorm).style.display = "none";
					document.getElementById(objects[i].nameActive).style.display = "none";
					document.getElementById(objects[i].nameDisabled).style.display = "none";
					document.getElementById(objects[i].getTabStateImg()).style.display = "block";
				} else {
					if (objects[i].state != TAB_DISABLED && this.z != i) {
						objects[i].state = TAB_NORMAL;
						objects[i].src = objects[i].normSrc;
						changeImage(null, objects[i].name, objects[i].nameNorm);
					}
				}
			}
		}
	}

	if (!init) {
		if (this.svg) {
			this.state = state;
			document.getElementById(this.getTabStateImg()).style.display = "block";
		} else {
			switch (this.state) {
				case TAB_DISABLED:
					this.src = this.disabledSrc;
					imgObj = this.nameDisabled;
					break;
				case TAB_ACTIVE:
					this.src = this.activeSrc;
					imgObj = this.nameActive;
					break;
				default:
					this.src = this.normSrc;
					imgObj = this.nameNorm;
					break;
			}
			changeImage(null, this.name, imgObj);
		}
	}
}


function we_tab(href, normSrc, activeSrc, disabledSrc, width, height, state, js, svg, no) {
	this.src = "";
	this.href = href;
	this.normSrc = normSrc;
	this.activeSrc = activeSrc;
	this.disabledSrc = disabledSrc;
	this.width = width;
	this.height = height;
	this.name = "tab_" + we_name_z;
	this.nameNorm = "tabNorm_" + we_name_z;
	this.nameActive = "tabActive_" + we_name_z;
	this.nameDisabled = "tabDisabled_" + we_name_z;
	this.js = js;
	this.svg = (svg === undefined ? false : svg);


	this.z = (no === undefined ? we_name_z : no);
	we_name_z++;
	this.setState = we_tab_setState;
	this.setState(state, true);

	if (!this.svg) {
		preload(this.nameNorm, this.normSrc);
		preload(this.nameActive, this.activeSrc);
		preload(this.nameDisabled, this.disabledSrc);
	}

	this.write = we_tab_write;
	this.getTabStateImg = we_getTabStateImg;
}