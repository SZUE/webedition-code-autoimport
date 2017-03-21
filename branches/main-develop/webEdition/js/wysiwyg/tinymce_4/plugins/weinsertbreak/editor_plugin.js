/* global tinymce */
'use strict';
(function () {
	tinymce.create('tinymce.plugins.WeinsertbreakPlugin', {
		init: function (c, d) {
			c.addCommand('mceWeinsertbreak', function () {
				c.selection.setContent(c.dom.createHTML('br'));
			});
			c.addButton('weinsertbreak', {
				icon: 'fa fa-level-down',
				title: tinymce.i18n.data.de.we.tt_weinsertbreak,
				cmd: 'mceWeinsertbreak'
			});

			c.addMenuItem('weinsertbreak', {
				text: tinymce.i18n.data.de.we.tt_weinsertbreak,
				icon: 'fa fa-level-down',
				context: 'insert',
				cmd: 'mceWeinsertbreak'
			});
		},
		createControl: function (n, a) {
			return null;
		},
		getInfo: function () {
			return{
				longname: 'Weinsertbreak plugin',
				author: 'webEdition e.V',
				authorurl: 'http://www.webedition.org',
				infourl: 'http://www.webedition.org'
			};
		}});
	tinymce.PluginManager.add('weinsertbreak', tinymce.plugins.WeinsertbreakPlugin);
})();
