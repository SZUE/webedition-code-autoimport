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

function encode64(inp) {
	var key = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	var chr1, chr2, chr3, enc3, enc4, i = 0, out = "";
	while (i < inp.length) {
		chr1 = inp.charCodeAt(i++);
		if (chr1 > 127)
			chr1 = 88;
		chr2 = inp.charCodeAt(i++);
		if (chr2 > 127)
			chr2 = 88;
		chr3 = inp.charCodeAt(i++);
		if (chr3 > 127)
			chr3 = 88;
		if (isNaN(chr3)) {
			enc4 = 64;
			chr3 = 0;
		} else {
			enc4 = chr3 & 63;
		}
		if (isNaN(chr2)) {
			enc3 = 64;
			chr2 = 0;
		} else {
			enc3 = ((chr2 << 2) | (chr3 >> 6)) & 63;
		}
		out += key.charAt((chr1 >> 2) & 63) + key.charAt(((chr1 << 4) | (chr2 >> 4)) & 63) + key.charAt(enc3) + key.charAt(enc4);
	}
	return encodeURIComponent(out);
}
function weCmdEnc(inp) {
	return 'WECMDENC_' + encode64(inp);
}
