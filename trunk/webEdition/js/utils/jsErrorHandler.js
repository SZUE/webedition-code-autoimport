/**
 * webEdition CMS
 *
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

function errorHandler(msg, file, line, col, errObj) {
	deb = console.debug !== undefined;
	if (deb) {
		console.debug(msg);
	} else {
		console.log(msg);
	}
	if (errObj) {
		if (deb) {
			console.debug(errObj);
		} else {
			console.log(errObj);
		}
	}
	try {//we don' want to raise errors inside
		var loc = (this.location ? this.location : document.location);
		postData = 'we_cmd[msg]=' + encodeURIComponent(msg) +
						'&we_cmd[file]=' + encodeURIComponent(file) +
						'&we_cmd[line]=' + encodeURIComponent(line) +
						'&we_cmd[url]=' + encodeURIComponent(loc.pathname + loc.search) +
						'&we_cmd[App]=' + encodeURIComponent(navigator.appName) +
						'&we_cmd[Ver]=' + encodeURIComponent(navigator.appVersion) +
						'&we_cmd[UA]=' + encodeURIComponent(navigator.userAgent) +
						(col ? '&we_cmd[col]=' + encodeURIComponent(col) : '') +
						(errObj ? '&we_cmd[errObj]=' + encodeURIComponent(errObj.stack) : '');
		lcaller = arguments.callee.caller;
		while (lcaller) {
			postData += '&we_cmd[]=' + encodeURIComponent(lcaller.name);
			lcaller = lcaller.caller;
		}
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open('POST', WE().consts.dirs.WEBEDITION_DIR + 'rpc.php?cmd=TriggerJSError&cns=error', true);
		xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xmlhttp.send(postData);
	} catch (e) {
		if (deb) {
			console.debug(e);
		} else {
			console.log(e);
		}
	}
}
