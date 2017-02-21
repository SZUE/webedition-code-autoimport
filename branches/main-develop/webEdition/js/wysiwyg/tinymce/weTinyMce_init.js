/* global tinyMCEPopup, tinymce,top, WE, tinyMCE */

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
'use strict';

var initVars = WE().util.getDynamicVar(document, 'loadVarWeTinyMce_init', 'data-dynvars');
var confObject = null;
var tinyMceConfObjects = tinyMceConfObjects ? tinyMceConfObjects : {};

initVars.weWin = window;
tinyMceConfObjects[initVars.weFieldNameClean] = confObject = WE().layout.we_tinyMCE.getTinyConfObject(initVars);

tinyMCE.addI18n(WE().consts.g_l.tinyMceTranslationObject);
tinyMCE.PluginManager.load = tinyPluginManager;

WE().layout.we_tinyMCE.functions.tinyMceInitialize(window, confObject);

function tinyPluginManager(n, u, cb, s) {
	var t = this, url = u;top.console.log('t', t);
	function loadDependencies() {
		var dependencies = t.dependencies(n);
		tinymce.each(dependencies, function (dep) {
			var newUrl = t.createUrl(u, dep);
			t.load(newUrl.resource, newUrl, undefined, undefined);
		});
		if (cb) {
			if (s) {
				cb.call(s);
			} else {
				cb.call(tinymce.ScriptLoader);
			}
		}
	}
	if (t.urls[n]) {
		return;
	}
	if (typeof u === "object") {
		url = u.resource.indexOf("we") === 0 ? WE().consts.dirs.WE_JS_TINYMCE_DIR + "plugins/" + u.resource + u.suffix : u.prefix + u.resource + u.suffix;
	}
	if (url.indexOf("/") !== 0 && url.indexOf("://") === -1) {
		url = tinymce.baseURL + "/" + url;
	}
	t.urls[n] = url.substring(0, url.lastIndexOf("/"));
	if (t.lookup[n]) {
		loadDependencies();
	} else {
		tinymce.ScriptLoader.add(url, loadDependencies, s);
	}
};