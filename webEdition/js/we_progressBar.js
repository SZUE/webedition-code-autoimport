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
var progressBar = WE().util.getDynamicVar(document, 'loadVarProgressBar', 'data-progress');

function setProgressText(name, text) {
	var div = document.getElementById(name);
	if (div) {
		div.innerHTML = text;
	}
}

function setProgress(name, progress) {
	var koef = (progressBar[name] / 100);
	document.getElementById("progress_image" + name).style.width = (koef * progress) + "px";
	document.getElementById("progress_image_bg" + name).style.width = (koef * 100) - (koef * progress) + "px";
	setProgressText("progress_text" + name, progress + "%");
}

//FIXME: remove; this is from export
function doProgress(progress) {
	var elem = document.getElementById("progress");
	if (elem.style.display === "none") {
		elem.style.display = "";
	}
	setProgress("", progress);
}

function hideProgress() {
	var elem = document.getElementById("progress");
	if (elem.style.display !== "none") {
		elem.style.display = "none";
	}
}
