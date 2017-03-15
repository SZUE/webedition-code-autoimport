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

function setProgress(progress) {
	var name = "";
	var elems = document.getElementsByClassName('progressbar');
	for (var i = 0; i < elems.length; i++) {
		elems[i].style.display = "";
	}
	var koef = progressBar[name].koef;
	document.getElementById("progress_image" + name).style.width = (koef * progress) + "px";
	document.getElementById("progress_image_bg" + name).style.width = (koef * 100) - (koef * progress) + "px";
	setProgressText("progress_text" + name, (Math.round(progress * 10) / 10) + "%");
	updateEst(name, progress);
}

function hideProgress() {
	var elems = document.getElementsByClassName('progressbar');
	for (var i = 0; i < elems.length; i++) {
		elems[i].style.display = "none";
	}
}

function updateEst(name, progress) {
	if (progress === 100) {
		progressBar[name].count = 1;
		progressBar[name].est = 0;
		return;
	}
	if (progress === 0) {
		progressBar[name].start = Date.now();
	}
	var diffTime = Date.now() - progressBar[name].start;
	progressBar[name].est = (progress > 0 ? ((diffTime * 100) / progress) - diffTime : -1);
	progressBar[name].count = 30;
	if (!progressBar[name].timer) {
		updateElapsed(name);
		progressBar[name].timer = window.setInterval(updateElapsed, 1000, name);
	}
}

function updateElapsed(name) {
	progressBar[name].count--;

	if (progressBar[name].count < 0) {
		window.clearInterval(progressBar[name].timer);
		progressBar[name].timer = 0;
	}
	var elapsed = new Date();
	elapsed.setTime(-3600000 + Date.now() - progressBar[name].start);
	progressBar[name].est -= 1000;
	var est = new Date();
	est.setTime(-3600000 + (progressBar[name].est > 0 ? progressBar[name].est : 0));
	setProgressText("elapsedTime" + name, elapsed.toLocaleTimeString() + " / " + (progressBar[name].est >= 0 ? est.toLocaleTimeString() : '<i class="fa fa-cog fa-spin"></i>')
		);
}

/*$(function () {
 var t = Date.now();
 for (var name in progressBar) {
 progressBar[name].start = t;
 updateEst(name, 0);
 updateElapsed(name);
 }
 });*/