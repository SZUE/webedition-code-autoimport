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
	tinymce.create('tinymce.plugins.WegalleryPlugin', {
		init: function (ed, f) {
			var templates = ed.getParam('weGalleryTemplates');
			ed.addButton('wegallery', {
				icon: 'fa fa-archive',
				title: tinymce.i18n.data.de.we.tt_wegallery,
				cmd: 'mceWegallery',
				onPostRender: function() {
					var ctrl = this;
					ed.on('NodeChange', function(e) {
						if(templates){
							if(!ed.selection.isCollapsed()){
								ctrl.disabled(e.element.nodeName !== 'WE-GALLERY');
								ctrl.active(e.element.nodeName === 'WE-GALLERY');
							} else {
								ctrl.disabled(false);
								ctrl.active(e.element.nodeName === 'WE-GALLERY');
							}
						} else {
							ctrl.disabled(true);
						}
					});
				}
			});

			ed.addMenuItem('wegallery', {
				text: tinymce.i18n.data.de.we.tt_wegallery,
				icon: 'fa fa-archive',
				context: 'insert',
				cmd: 'mceWegallery',
				onPostRender: function() {
					var ctrl = this;
					ed.on('NodeChange', function(e) {
						if(templates){
							if(!ed.selection.isCollapsed()){
								ctrl.disabled(e.element.nodeName !== 'WE-GALLERY');
								ctrl.active(e.element.nodeName === 'WE-GALLERY');
							} else {
								ctrl.disabled(false);
								ctrl.active(e.element.nodeName === 'WE-GALLERY');
							}
						} else {
							ctrl.disabled(true);
						}
					});
				}
			});

			ed.addCommand('mceWegallery', function () {
				ed.isWeGalleryInitialized = false;
				var c = ed.selection, weid = 0, wetmpl = '';
				if(!templates){
					return;
				}
				if (ed.dom.getParent(c.getNode(), 'WE-GALLERY') !== null) {
					weid = ed.dom.getParent(c.getNode(), 'WE-GALLERY').id;
					wetmpl = ed.dom.getParent(c.getNode(), 'WE-GALLERY').getAttribute('tmpl');
				}

				ed.windowManager.openLegacyPlugin({
					file: '/webEdition/we_cmd_frontend.php?we_cmd[0]=open_dialog_gallery&we_dialog_args[editor]=tinyMce&we_dialog_args[isFrontend]=' + ed.getParam('weIsFrontend') + '&we_dialog_args[collid]=' + weid + '&we_dialog_args[tmpl]=' + wetmpl + '&we_dialog_args[templateIDs]=' + templates,
					popup_css: false,
					width: 500 + parseInt(ed.getLang('weabbr.delta_width', 0)),
					height: 250 + parseInt(ed.getLang('weabbr.delta_height', 0)),
					inline: false
					}, {
					plugin_url: f,
					some_custom_arg: 'custom arg'
				});
			});
		},

		createControl: function () {
			return null;
		},

		getInfo: function () {
			return {
				longname: 'Wegallery plugin',
				author: 'webedtition.irg',
				authorurl: 'http://webedtition.org',
				infourl: 'http://webedtition.org',
				version: "1.0"
			};
		}
	});
	tinymce.PluginManager.add('wegallery', tinymce.plugins.WegalleryPlugin);
})();