/* global tinymce */
/**
 * webEdition CMS
 *
 * $Rev: 13357 $
 * $Author: lukasimhof $
 * $Date: 2017-02-13 22:29:45 +0100 (Mo, 13 Feb 2017) $
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
'use strict';

(function () {
	tinymce.create('tinymce.plugins.WeacronymPlugin', {
		init: function (ed, f) {
			ed.addCommand('mceWeacronym', function () {
				var a = ed.selection;
				if (a.isCollapsed() && (!ed.dom.getParent(a.getNode(), 'ACRONYM'))){
					return;
				}
				ed.windowManager.openLegacyPlugin({
					file: '/webEdition/we_cmd_frontend.php?we_cmd[0]=open_dialog_acronym&we_dialog_args[editor]=tinyMce&we_dialog_args[isFrontend]=' + ed.getParam('weIsFrontend'),
					popup_css: false,
					width: 460 + parseInt(ed.getLang('weacronym.delta_width', 0)),
					height: 200 + parseInt(ed.getLang('weacronym.delta_height', 0)),
					inline: false
					}, {
					plugin_url: f,
					some_custom_arg: 'custom arg'
				});
			});

			ed.addButton('weacronym', {
				text: 'AKR',
				title: tinymce.i18n.data.de.we.tt_weacronym,
				cmd: 'mceWeacronym'
			});

			ed.addMenuItem('weacronym', {
				text: tinymce.i18n.data.de.we.tt_weacronym,
				//icon: 'fa fa-image',
				context: 'xhtml',
				cmd: 'mceWeacronym',
				onPostRender: function() {
					var ctrl = this;
					ed.on('NodeChange', function(e) {
						ctrl.active(false);
						ctrl.disabled(ed.selection.isCollapsed());

						var n = e.element;
						if (n && n.nodeName) { // we check for lang spans recursive
							do {
								if(n.nodeName.toLowerCase() === 'acronym'){
									ctrl.active(true);
									ctrl.disabled(false);
									break;
								}
							} while ((n = n.parentNode));
						}
					});
				}
			});

			// ed.onNodeChange.add(function(ed, cm, n) {} => moved to weutil
		},

		createControl: function (n, a) {
			return null;
		},

		getInfo: function () {
			return {
				longname: 'Weacronym Plugin',
				author: 'webEdition e.V',
				authorurl: 'http://www.webedition.org',
				infourl: 'http://www.webedition.org',
				version: "1.0"
			};
		}
	});
	tinymce.PluginManager.add('weacronym', tinymce.plugins.WeacronymPlugin);
})();