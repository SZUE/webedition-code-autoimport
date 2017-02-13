/* global tinymce */
'use strict';
(function () {
	tinymce.create('tinymce.plugins.WelangPlugin', {
		init: function (ed, f) {
			ed.addCommand('mceWelang', function () {
				var a = ed.selection;
				if (a.isCollapsed() && (!ed.dom.getParent(a.getNode(), 'SPAN'))){
					return;
				}
			ed.windowManager.open({
				file: '/webEdition/we_cmd_frontend.php?we_cmd[0]=open_dialog_lang&we_dialog_args[editor]=tinyMce&we_dialog_args[isFrontend]=' + ed.getParam('weIsFrontend'),
				popup_css: false,
				width: 460 + parseInt(ed.getLang('welang.delta_width', 0)),
				height: 160 + parseInt(ed.getLang('welang.delta_height', 0)),
				inline: 1
				}, {
				plugin_url: f,
				some_custom_arg: 'custom arg'
				});
			});
			ed.addButton('welang', {
				title: 'we.tt_welang',
				cmd: 'mceWelang'
			});

			// de.onNodeChange.add(function(ed, cm, n) {} => moved to weutil
		},

		createControl: function () {
			return null;
		},

		getInfo: function () {
			return {
				longname: 'Welang plugin',
				author: 'Some author',
				authorurl: 'http://tinymce.moxiecode.com',
				infourl: 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/welang',
				version: "1.0"
			};
		}
	});
	tinymce.PluginManager.add('welang', tinymce.plugins.WelangPlugin);
})();