/* global tinymce */
'use strict';
(function () {
	tinymce.create('tinymce.plugins.WeimagePlugin', {
		init: function (e, f) {
			e.addCommand('mceWeimage', function (ui, dropID) {
				dropID = dropID || 0;

				e.isWeDataInitialized = false;
				if (e.dom.getAttrib(e.selection.getNode(), 'class', '').indexOf('mceItem') != -1) {
					return;
				}
				wesrc = "";
				if (e.selection.getNode().nodeName == 'IMG' && e.dom.getAttrib(e.selection.getNode(), 'src', '')) {
					wesrc = e.dom.getAttrib(e.selection.getNode(), 'src', '');
				}

				e.windowManager.open({
					file: '/webEdition/dynamic/wysiwyg/imageDialog.php?we_dialog_args[editor]=tinyMce&we_dialog_args[src]=' + encodeURIComponent(wesrc) + '&we_dialog_args[cssclasses]=' + e.getParam('weClassNames_urlEncoded') + "&we_dialog_args[isFrontend]=" + e.getParam('weIsFrontend') + "&we_dialog_args[selectorStartID]=" + e.getParam('weImageStartID') + "&we_dialog_args[fileID]=" + dropID + "&we_dialog_args[isPresetFromDnD]=" + (dropID ? 1 : 0),
					popup_css: false,
					width: 600 + parseInt(e.getLang('weimage.delta_width', 0)),
					height: 610 + parseInt(e.getLang('weimage.delta_height', 0)),
					inline: 1,
				}, {
					plugin_url: f
				});
			});
			e.addButton('weimage', {
				title: 'we.tt_weimage',
				cmd: 'mceWeimage'
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