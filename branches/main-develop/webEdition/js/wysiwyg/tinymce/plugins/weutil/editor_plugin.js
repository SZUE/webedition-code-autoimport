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
 * @package    webEdition_tinymce
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * This source is based on tinyMCE-plugin "contextmenu":
 * Moxiecode Systems AB, http://tinymce.moxiecode.com/license.
 */

(function () {
	tinymce.create('tinymce.plugins.WeUtilPlugin', {
		init: function (ed, url) {
			this.editor = ed;

			ed.onNodeChange.add(function (ed, cm, n, co) {
				var mapTable = {
					strong: 'weadaptbold',
					b: 'weadaptbold',
					em: 'weadaptitalic',
					i: 'weadaptitalic',
					acronym: 'weacronym',
					abbr: 'weabbr',
					img: 'weimage'
				};

				n = ed.dom.getParent(n, 'STRONG,B,EM,I,ACRONYM,ABBR,A,IMG,SPAN');

				cm.setDisabled('weacronym', co);
				cm.setDisabled('weabbr', co);
				cm.setDisabled('welink', co);
				cm.setDisabled('weadaptunlink', true);
				cm.setDisabled('welang', co);

				cm.setActive('weadaptbold', 0);
				cm.setActive('weadaptitalic', 0);
				cm.setActive('weacronym', 0);
				cm.setActive('weabbr', 0);
				cm.setActive('welang', 0);
				cm.setActive('welink', 0);
				cm.setActive('weunlink', 0);
				cm.setActive('weimage', 0);

				if (n && n.nodeName) {
					do {
						switch (n.nodeName.toLowerCase()) {
							case 'a':
								if (!n.getAttribute('name')) {
									cm.setActive('welink', 1);
									cm.setDisabled('welink', false);
									cm.setDisabled('weadaptunlink', false);
								}
								break;
							case 'span':
								if (n.getAttribute('lang') && n.getAttribute('style') !== '') {
									cm.setActive('welang', 1);
									cm.setDisabled('welang', false);
								}
								/* falls through */
							default:
								if (mapTable[n.nodeName.toLowerCase()] !== undefined) {
									cm.setActive(mapTable[n.nodeName.toLowerCase()], 1);
									cm.setDisabled(mapTable[n.nodeName.toLowerCase()], false);
								}
						}
					} while ((n = n.parentNode));
				}

			});

		},
		getInfo: function () {
			return {
				longname: 'webEdition e.V.',
				author: 'webEdition e.V.',
				authorurl: 'http://webedition.org',
				infourl: 'http://webedition.org',
				version: tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('weutil', tinymce.plugins.WeUtilPlugin);
})();