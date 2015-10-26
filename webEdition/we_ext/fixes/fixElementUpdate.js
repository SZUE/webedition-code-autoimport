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
 * @package    webEdition_EXT
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

Ext.override(Ext.dom.Element, {
	update : function(html, loadScripts, callback) {
		
		var DOC				= document,
		scriptTagRe			= /(?:<script([^>]*)?>)((\n|\r|.)*?)(?:<\/script>)/ig,
		replaceScriptTagRe	= /(?:<script.*?>)((\n|\r|.)*?)(?:<\/script>)/ig,
		srcRe				= /\ssrc=([\'\"])(.*?)\1/i,
		typeRe				= /\stype=([\'\"])(.*?)\1/i;

		var me = this,
			id,
			dom,
			interval;

		if (!me.dom) {
			return me;
		}
		html = html || '';
		dom = me.dom;

		if (loadScripts !== true) {
			dom.innerHTML = html;
			Ext.callback(callback, me);
			return me;
		}

		id  = Ext.id();
		html += '<span id="' + id + '"></span>';

		interval = setInterval(function() {
			var hd,
				match,
				attrs,
				srcMatch,
				typeMatch,
				el,
				s;
			if (!(el = DOC.getElementById(id))) {
				return false;
			}
			clearInterval(interval);
			Ext.removeNode(el);
			hd = Ext.getHead().dom;
			while ((match = scriptTagRe.exec(html))) {
				attrs = match[1];
				srcMatch = attrs ? attrs.match(srcRe) : false;
				if (srcMatch && srcMatch[2]) {
					s = DOC.createElement("script");
					s.src = srcMatch[2];
					typeMatch = attrs.match(typeRe);
					if (typeMatch && typeMatch[2]) {
						s.type = typeMatch[2];
					}
					hd.appendChild(s);
				} else if (match[2] && match[2].length > 0) {
					if (window.execScript) {
						window.execScript(match[2]);
					} else {
						c = match[2].replace('<!--', '').replace('//-->', '');//FIX
						window.eval(c);
					}
				}
			}
			Ext.callback(callback, me);
		}, 20);
		dom.innerHTML = html.replace(replaceScriptTagRe, '');

		return me;
	}
});