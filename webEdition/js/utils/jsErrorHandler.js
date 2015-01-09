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

try {
	window.onerror = function (msg, file, line, col, errObj) {
		console.debug(msg);
		if (errObj) {
			console.debug(errObj);
		}
		try {//we don' want to raise errors inside
			postData = 'we_cmd[msg]=' + encodeURIComponent(msg);
			postData += '&we_cmd[file]=' + encodeURIComponent(file);
			postData += '&we_cmd[line]=' + encodeURIComponent(line);
			postData += '&we_cmd[url]=' + encodeURIComponent(this.location.pathname + this.location.search);
			postData += '&we_cmd[search]=' + encodeURIComponent(line);
			if (col) {
				postData += '&we_cmd[col]=' + encodeURIComponent(col);
			}
			if (errObj) {
				postData += '&we_cmd[errObj]=' + encodeURIComponent(errObj.stack);
			}
			lcaller = arguments.callee.caller;
			while (lcaller) {
				postData += '&we_cmd[]=' + encodeURIComponent(lcaller.name);
				lcaller = lcaller.caller;
			}
			postData += '&we_cmd[App]=' + encodeURIComponent(navigator.appName);
			postData += '&we_cmd[Ver]=' + encodeURIComponent(navigator.appVersion);
			postData += '&we_cmd[UA]=' + encodeURIComponent(navigator.userAgent);
			xmlhttp = new XMLHttpRequest();
			xmlhttp.open('POST', '/webEdition/rpc/rpc.php?cmd=TriggerJSError&cns=error', true);
			xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			xmlhttp.send(postData);
		} catch (e) {
			console.debug(e);
		}
	}
} catch (e) {
	console.debug(e);
}
