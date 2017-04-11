/* global tinymce */
'use strict';
(function () {
	tinymce.create('tinymce.plugins.WeimagePlugin', {
		init: function (ed, f) {
			ed.addCommand('mceWeimage', function (ui, dropID) {
				dropID = dropID || 0;

				ed.isWeDataInitialized = false;
				if (ed.dom.getAttrib(ed.selection.getNode(), 'class', '').indexOf('mceItem') !== -1) {
					return;
				}
				var wesrc = "";
				if (ed.selection.getNode().nodeName === 'IMG' && ed.dom.getAttrib(ed.selection.getNode(), 'src', '')) {
					wesrc = ed.dom.getAttrib(ed.selection.getNode(), 'src', '');
				}

				ed.windowManager.openLegacyPlugin({
					file: '/webEdition/we_cmd_frontend.php?we_cmd[0]=open_dialog_image&we_dialog_args[editor]=tinyMce&we_dialog_args[src]=' + encodeURIComponent(wesrc) + '&we_dialog_args[cssclasses]=' + ed.getParam('weClassNames_urlEncoded') + "&we_dialog_args[isFrontend]=" + ed.getParam('weIsFrontend') + "&we_dialog_args[selectorStartID]=" + ed.getParam('weImageStartID') + "&we_dialog_args[fileID]=" + dropID + "&we_dialog_args[isPresetFromDnD]=" + (dropID ? 1 : 0),
					popup_css: false,
					width: 600 + parseInt(ed.getLang('weimage.delta_width', 0)),
					height: 610 + parseInt(ed.getLang('weimage.delta_height', 0)),
					inline: false
				}, {
					plugin_url: f
				});
			});
			ed.addButton('weimage', {
				icon: 'fa fa-image',
				title: tinymce.i18n.data.de.we.tt_weimage,
				cmd: 'mceWeimage'
			});

			ed.addMenuItem('weimage', {
				text: tinymce.i18n.data.de.we.tt_weimage,
				icon: 'fa fa-image',
				context: 'insert',
				cmd: 'mceWeimage',
				onPostRender: function() {
					var ctrl = this;
					ed.on('NodeChange', function(e) {
						ctrl.active(e.element.nodeName.toLowerCase() === 'img');
						ctrl.disabled(false);
					});
				}
			});
		},
		getInfo: function () {
			return {
				longname: 'webEdition Image-Dialog',
				author: 'webEdition e.V',
				authorurl: 'http://www.webedition.org',
				infourl: 'http://www.webedition.org'
			};
		}
	});
	tinymce.PluginManager.add('weimage', tinymce.plugins.WeimagePlugin);
})();